<?php

namespace App\Http\Controllers\Npo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Npo\AttendanceRequest;
use App\Models\Event;
use App\Services\RosterService;
use App\Support\Csv;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RosterController extends Controller
{
    public function __construct(private RosterService $roster)
    {
    }

    public function show(Event $event): View
    {
        Gate::authorize('viewSubmissions', $event);

        return view('npo.roster.show', [
            'event'        => $event->loadCount('approvedSubmissions'),
            'volunteers'   => $this->roster->volunteers($event),
            'sizes'        => $this->roster->tshirtSummary($event),
            'defaultHours' => $this->roster->defaultHours($event),
            'canMark'      => $this->roster->canMarkAttendance($event),
        ]);
    }

    public function attendance(AttendanceRequest $request, Event $event): RedirectResponse
    {
        $count = $this->roster->saveAttendance($event, $request->validated('attendance'));

        return redirect()
            ->route('npo.events.roster', $event)
            ->with('success', "Attendance saved for {$count} ".str('volunteer')->plural($count).'.');
    }

    public function export(Event $event): StreamedResponse
    {
        Gate::authorize('viewSubmissions', $event);

        return Csv::download($this->roster->filename($event), $this->roster->csvRows($event));
    }

    public function print(Event $event): View
    {
        Gate::authorize('viewSubmissions', $event);

        return view('npo.roster.print', [
            'event'      => $event->load('organization'),
            'volunteers' => $this->roster->volunteers($event),
            'sizes'      => $this->roster->tshirtSummary($event),
        ]);
    }
}