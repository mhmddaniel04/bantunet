<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ApplyRequest;
use App\Models\Event;
use App\Models\Submission;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function create(Request $request, Event $event): View|RedirectResponse
    {
        abort_if($event->status === Event::STATUS_DRAFT, 404);

        $event->load(['category', 'organization'])->loadCount('approvedSubmissions');

        if (! $event->isOpen()) {
            return redirect()
                ->route('events.show', $event)
                ->with('error', 'Sorry, this initiative is no longer accepting applications.');
        }

        // Smart Match (Phase 7) links here with ?via=smart_match&score=87.5
        $fromSmartMatch = $request->query('via') === Submission::SOURCE_SMART_MATCH;

        return view('public.applications.create', [
            'event'        => $event,
            'source'       => $fromSmartMatch ? Submission::SOURCE_SMART_MATCH : Submission::SOURCE_BROWSE,
            'matchScore'   => $fromSmartMatch ? min(100, max(0, (float) $request->query('score'))) : null,
            'genders'      => config('bantunet.genders'),
            'tshirtSizes'  => config('bantunet.tshirt_sizes'),
            'availability' => config('bantunet.availability'),
        ]);
    }

    public function store(ApplyRequest $request, Event $event, ApplicationService $applications): RedirectResponse
    {
        abort_if($event->status === Event::STATUS_DRAFT, 404);

        $submission = $applications->submit($event, $request->validated(), $request->file('resume'));

        // Let this browser view the tracking page straight away.
        $request->session()->push(TrackingController::SESSION_KEY, $submission->submission_id);

        return redirect()
            ->route('track.show', $submission)
            ->with('just_applied', true);
    }
}