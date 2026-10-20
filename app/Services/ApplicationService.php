<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Organization;
use App\Models\Submission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Listing and reviewing volunteer applications for an organisation.
 */
class ApplicationService
{
    public function paginateFor(Organization $organization, ?string $status, ?int $eventId): LengthAwarePaginator
    {
        return $this->queryFor($organization, $eventId)
            ->with('event')
            ->when($status, fn (Builder $q) => $q->where('review_status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    /** e.g. ['Pending' => 4, 'Approved' => 7] */
    public function statusCounts(Organization $organization, ?int $eventId): array
    {
        return $this->queryFor($organization, $eventId)
            ->selectRaw('review_status, COUNT(*) AS total')
            ->groupBy('review_status')
            ->pluck('total', 'review_status')
            ->all();
    }

    /**
     * Approve or reject. Approving is refused when the initiative is full.
     * The row lock stops two reviewers approving the last slot at the same time.
     */
    public function review(Submission $submission, string $decision, ?string $note): Submission
    {
        if ($submission->review_status === Submission::STATUS_WITHDRAWN) {
            throw ValidationException::withMessages([
                'decision' => 'This volunteer has withdrawn the application.',
            ]);
        }

        return DB::transaction(function () use ($submission, $decision, $note) {
            $event = Event::whereKey($submission->event_id)->lockForUpdate()->firstOrFail();

            $isNewApproval = $decision === Submission::STATUS_APPROVED
                && $submission->review_status !== Submission::STATUS_APPROVED;

            if ($isNewApproval && $event->capacity !== null
                && $event->approvedSubmissions()->count() >= $event->capacity) {
                throw ValidationException::withMessages([
                    'decision' => "All {$event->capacity} volunteer slots are already filled. Increase the capacity or reject another volunteer first.",
                ]);
            }

            $submission->forceFill([
                'review_status' => $decision,
                'reviewer_note' => $note,
                'reviewed_at'   => now(),
            ])->save();

            // Phase 6: email the volunteer about the decision here.

            return $submission;
        });
    }

    /** Applications that belong to this organisation's events only. */
    private function queryFor(Organization $organization, ?int $eventId): Builder
    {
        return Submission::query()
            ->whereIn('event_id', $organization->events()->select('event_id'))
            ->when($eventId, fn (Builder $q) => $q->where('event_id', $eventId));
    }
}