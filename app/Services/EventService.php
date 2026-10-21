<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Organization;
use App\Models\Submission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Creating, updating and listing an organisation's initiatives.
 */
class EventService
{
    /** Columns an NPO is allowed to fill from the form. */
    private const FIELDS = [
        'category_id', 'title', 'description', 'requirements',
        'location', 'state', 'event_date', 'start_time', 'end_time',
        'application_deadline', 'capacity', 'is_featured', 'status',
    ];

    private const IMAGE_FOLDER = 'events';

    public function __construct(private EventEmbeddingService $embeddings)
    {
    }

    /* ---------------- Listing ---------------- */

    public function paginateFor(Organization $organization, ?string $status): LengthAwarePaginator
    {
        return $organization->events()
            ->with('category')
            ->withCount([
                'submissions',
                'approvedSubmissions',
                'submissions as pending_count' => fn ($q) => $q->where('review_status', Submission::STATUS_PENDING),
            ])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('event_date')
            ->paginate(10)
            ->withQueryString();
    }

    /** e.g. ['published' => 6, 'draft' => 2] */
    public function statusCounts(Organization $organization): array
    {
        return Event::where('org_id', $organization->org_id)
            ->selectRaw('status, COUNT(*) AS total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }

    /* ---------------- Saving ---------------- */

    public function create(Organization $organization, array $data, ?UploadedFile $image): Event
    {
        $event = DB::transaction(function () use ($organization, $data, $image) {
            $event = $organization->events()->create([
                ...Arr::only($data, self::FIELDS),
                'image_path' => $image?->store(self::IMAGE_FOLDER, 'public'),
            ]);

            $event->skills()->sync($data['skills'] ?? []);

            return $event;
        });

        // AI profile for Smart Match (never blocks saving if Gemini is down).
        $this->embeddings->embedSafely($event->fresh(['category', 'skills']));

        return $event;
    }

    public function update(Event $event, array $data, ?UploadedFile $image, bool $removeImage): Event
    {
        DB::transaction(function () use ($event, $data, $image, $removeImage) {
            $event->fill(Arr::only($data, self::FIELDS));

            if ($image || $removeImage) {
                $this->deleteImage($event);
                $event->image_path = $image?->store(self::IMAGE_FOLDER, 'public');
            }

            $event->save();
            $event->skills()->sync($data['skills'] ?? []);
        });

        // Title, description, skills or date may have changed → refresh the AI profile.
        $this->embeddings->embedSafely($event->fresh(['category', 'skills']));

        return $event;
    }

    /**
     * Only initiatives without applications may be deleted,
     * so volunteer records are never lost.
     */
    public function canDelete(Event $event): bool
    {
        return ! $event->submissions()->exists();
    }

    public function delete(Event $event): void
    {
        $this->deleteImage($event);
        $event->delete();   // event_skill rows are removed by the database (cascade)
    }

    private function deleteImage(Event $event): void
    {
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }
    }
}