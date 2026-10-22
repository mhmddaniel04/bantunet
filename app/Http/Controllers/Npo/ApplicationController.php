<?php

namespace App\Http\Controllers\Npo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Npo\ReviewApplicationRequest;
use App\Models\Submission;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationController extends Controller
{
    private const SORT_OPTIONS = [
        'newest' => 'Newest first',
        'fit'    => '🧠 Best AI fit first',
    ];

    public function __construct(private ApplicationService $applications)
    {
    }

    public function index(Request $request): View
    {
        $organization = $request->user();

        $status = in_array($request->query('status'), config('bantunet.review_statuses'), true)
            ? $request->query('status')
            : null;

        $sort = array_key_exists((string) $request->query('sort'), self::SORT_OPTIONS)
            ? $request->query('sort')
            : 'newest';

        // Only accept an event filter that belongs to this NPO.
        $eventId = $organization->events()->whereKey($request->integer('event'))->value('event_id');

        return view('npo.applications.index', [
            'applications' => $this->applications->paginateFor($organization, $status, $eventId, $sort),
            'counts'       => $this->applications->statusCounts($organization, $eventId),
            'eventOptions' => $organization->events()->orderByDesc('event_date')->pluck('title', 'event_id')->all(),
            'sortOptions'  => self::SORT_OPTIONS,
            'status'       => $status,
            'eventId'      => $eventId,
            'sort'         => $sort,
        ]);
    }

    public function show(Submission $submission): View
    {
        Gate::authorize('view', $submission);

        $submission->load('event.category')->event->loadCount('approvedSubmissions');

        return view('npo.applications.show', ['submission' => $submission]);
    }

    public function review(ReviewApplicationRequest $request, Submission $submission): RedirectResponse
    {
        $this->applications->review(
            $submission,
            $request->validated('decision'),
            $request->validated('reviewer_note'),
        );

        return redirect()
            ->route('npo.applications.show', $submission)
            ->with('success', "Application marked as {$submission->review_status}.");
    }

    public function resume(Submission $submission): StreamedResponse
    {
        Gate::authorize('downloadResume', $submission);

        abort_unless(
            $submission->resume_path && Storage::disk('local')->exists($submission->resume_path),
            404
        );

        return Storage::disk('local')->download(
            $submission->resume_path,
            $submission->resume_original_name ?? 'resume'
        );
    }
}