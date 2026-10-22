<?php

namespace App\Services;

use App\Mail\ApplicationReceived;
use App\Mail\ApplicationReviewed;
use App\Mail\NewApplicationAlert;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Submission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Everything about volunteer applications:
 * submitting, tracking, withdrawing (volunteers) and listing, reviewing (NPOs).
 */
class ApplicationService
{
    /** Fields a volunteer fills in on the application form. */
    private const APPLY_FIELDS = [
        'volunteer_name', 'volunteer_email', 'volunteer_phone', 'age', 'gender',
        'tshirt_size', 'availability', 'emergency_name', 'emergency_phone', 'skills_summary',
    ];

    private const RESUME_FOLDER = 'resumes';   // storage/app/private/resumes (not public)

    public function __construct(private SubmissionEmbeddingService $embeddings)
    {
    }

    /* ==================================================================
       VOLUNTEER SIDE
       ================================================================== */

    /**
     * Save a new application. Refused if the initiative closed or filled up
     * while the volunteer was filling in the form.
     */
    public function submit(Event $event, array $data, ?UploadedFile $resume): Submission
    {
        $submission = DB::transaction(function () use ($event, $data, $resume) {
            $event = Event::whereKey($event->getKey())->lockForUpdate()->firstOrFail();
            $event->loadCount('approvedSubmissions');

            if (! $event->isOpen()) {
                throw ValidationException::withMessages([
                    'event' => 'Sorry, this initiative is no longer accepting applications.',
                ]);
            }

            $fromSmartMatch = ($data['source'] ?? null) === Submission::SOURCE_SMART_MATCH;

            return $event->submissions()->create([
                ...Arr::only($data, self::APPLY_FIELDS),
                'source'               => $fromSmartMatch ? Submission::SOURCE_SMART_MATCH : Submission::SOURCE_BROWSE,
                'match_score'          => $fromSmartMatch ? ($data['match_score'] ?? null) : null,
                'resume_path'          => $resume?->store(self::RESUME_FOLDER, 'local'),
                'resume_original_name' => $resume ? Str::limit($resume->getClientOriginalName(), 200, '') : null,
            ]);
        });

        $submission->load('event.organization');

        $this->send($submission->volunteer_email, new ApplicationReceived($submission));
        $this->send($submission->event->organization->org_email, new NewApplicationAlert($submission));

        // AI fit for the NPO's "Best fit first" ranking (never blocks the application).
        $this->embeddings->embedSafely($submission);

        return $submission;
    }

    /** Tracking code + email must both match. */
    public function findForTracking(string $trackingCode, string $email): ?Submission
    {
        return Submission::where('tracking_code', $trackingCode)
            ->where('volunteer_email', $email)
            ->first();
    }

    /** Volunteers may withdraw while pending/approved and before the event day. */
    public function canWithdraw(Submission $submission): bool
    {
        return in_array($submission->review_status, [Submission::STATUS_PENDING, Submission::STATUS_APPROVED], true)
            && ! $submission->event->event_date->lt(today());
    }

    public function withdraw(Submission $submission): void
    {
        if (! $this->canWithdraw($submission)) {
            throw ValidationException::withMessages([
                'withdraw' => 'This application can no longer be withdrawn.',
            ]);
        }

        $submission->forceFill(['review_status' => Submission::STATUS_WITHDRAWN])->save();
    }

    /* ==================================================================
       NPO SIDE
       ================================================================== */

    /**
     * Applications with an AI fit score (cosine similarity between the applicant's
     * vector and the initiative's vector). $sort: "newest" or "fit".
     */
    public function paginateFor(Organization $organization, ?string $status, ?int $eventId, string $sort = 'newest'): LengthAwarePaginator
    {
        $query = $this->queryFor($organization, $eventId)
            ->join('events', 'events.event_id', '=', 'submissions.event_id')
            ->select('submissions.*')
            ->selectRaw('CASE WHEN submissions.embedding IS NULL OR events.embedding IS NULL
                              THEN NULL
                              ELSE 1 - (submissions.embedding <=> events.embedding)
                         END AS fit_score')
            ->with('event')
            ->when($status, fn (Builder $q) => $q->where('submissions.review_status', $status));

        if ($sort === 'fit') {
            $query->orderByRaw('fit_score DESC NULLS LAST');
        }

        return $query
            ->orderByDesc('submissions.created_at')
            ->paginate(15)
            ->withQueryString();
    }

    /** e.g. ['Pending' => 4, 'Approved' => 7] */
    public function statusCounts(Organization $organization, ?int $eventId): array
    {
        return $this->queryFor($organization, $eventId)
            ->selectRaw('submissions.review_status, COUNT(*) AS total')
            ->groupBy('submissions.review_status')
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

        $previousStatus = $submission->review_status;

        DB::transaction(function () use ($submission, $decision, $note) {
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
        });

        // Only email the volunteer when the decision actually changed.
        if ($previousStatus !== $decision) {
            $this->send($submission->volunteer_email, new ApplicationReviewed($submission));
        }

        return $submission;
    }

    /* ================================================================== */

    /** Applications that belong to this organisation's events only. */
    private function queryFor(Organization $organization, ?int $eventId): Builder
    {
        return Submission::query()
            ->whereIn('submissions.event_id', $organization->events()->select('event_id'))
            ->when($eventId, fn (Builder $q) => $q->where('submissions.event_id', $eventId));
    }

    /** An email problem must never stop an application from being saved. */
    private function send(string $to, Mailable $mail): void
    {
        rescue(fn () => Mail::to($to)->send($mail));
    }
}