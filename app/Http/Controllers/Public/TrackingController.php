<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\TrackRequest;
use App\Models\Submission;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Volunteers check an application with tracking code + email.
 * After a successful check, the application is remembered in the session,
 * so nobody can open someone else's application just by guessing a URL.
 */
class TrackingController extends Controller
{
    public const SESSION_KEY = 'tracked_submissions';

    public function __construct(private ApplicationService $applications)
    {
    }

    public function index(Request $request): View
    {
        return view('public.track.index', [
            'code' => strtoupper((string) $request->query('code')),
        ]);
    }

    public function lookup(TrackRequest $request): RedirectResponse
    {
        $submission = $this->applications->findForTracking(
            $request->validated('tracking_code'),
            $request->validated('volunteer_email'),
        );

        if (! $submission) {
            return back()
                ->withInput()
                ->withErrors(['tracking_code' => 'We could not find an application with that tracking code and email.']);
        }

        $request->session()->push(self::SESSION_KEY, $submission->submission_id);

        return redirect()->route('track.show', $submission);
    }

    public function show(Request $request, Submission $submission): View|RedirectResponse
    {
        if (! $this->hasAccess($request, $submission)) {
            return $this->askForEmail($submission);
        }

        $submission->load('event.organization', 'event.category');

        return view('public.track.show', [
            'submission'  => $submission,
            'canWithdraw' => $this->applications->canWithdraw($submission),
        ]);
    }

    public function withdraw(Request $request, Submission $submission): RedirectResponse
    {
        if (! $this->hasAccess($request, $submission)) {
            return $this->askForEmail($submission);
        }

        $this->applications->withdraw($submission);

        return redirect()
            ->route('track.show', $submission)
            ->with('success', 'Your application has been withdrawn.');
    }

    /* ------------------------------------------------------------------ */

    private function hasAccess(Request $request, Submission $submission): bool
    {
        return in_array($submission->submission_id, $request->session()->get(self::SESSION_KEY, []));
    }

    private function askForEmail(Submission $submission): RedirectResponse
    {
        return redirect()
            ->route('track.index', ['code' => $submission->tracking_code])
            ->with('error', 'Please confirm your email to view this application.');
    }
}