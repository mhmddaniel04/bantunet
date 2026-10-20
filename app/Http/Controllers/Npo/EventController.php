<?php

namespace App\Http\Controllers\Npo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Npo\EventRequest;
use App\Models\Category;
use App\Models\Event;
use App\Models\Skill;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(private EventService $events)
    {
    }

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $status = array_key_exists((string) $status, config('bantunet.event_statuses')) ? $status : null;

        return view('npo.events.index', [
            'events'       => $this->events->paginateFor($request->user(), $status),
            'statusCounts' => $this->events->statusCounts($request->user()),
            'status'       => $status,
        ]);
    }

    public function create(): View
    {
        return view('npo.events.create', [
            'event' => new Event(['status' => Event::STATUS_PUBLISHED, 'start_time' => '09:00']),
            ...$this->formOptions(),
        ]);
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $event = $this->events->create($request->user(), $request->validated(), $request->file('image'));

        return redirect()
            ->route('npo.events.index')
            ->with('success', "“{$event->title}” has been saved.");
    }

    public function edit(Event $event): View
    {
        Gate::authorize('update', $event);

        return view('npo.events.edit', [
            'event' => $event->load('skills'),
            ...$this->formOptions(),
        ]);
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $this->events->update(
            $event,
            $request->validated(),
            $request->file('image'),
            $request->boolean('remove_image'),
        );

        return redirect()
            ->route('npo.events.index')
            ->with('success', "“{$event->title}” has been updated.");
    }

    public function destroy(Event $event): RedirectResponse
    {
        Gate::authorize('delete', $event);

        if (! $this->events->canDelete($event)) {
            return back()->with('error', 'This initiative already has applications, so it cannot be deleted. Set its status to Cancelled instead.');
        }

        $this->events->delete($event);

        return redirect()
            ->route('npo.events.index')
            ->with('success', 'The initiative has been deleted.');
    }

    /** Dropdown and checkbox options for the initiative form. */
    private function formOptions(): array
    {
        return [
            'categories' => Category::orderBy('name')->pluck('name', 'category_id')->all(),
            'skills'     => Skill::orderBy('name')->pluck('name', 'skill_id')->all(),
            'states'     => config('bantunet.states'),
            'statuses'   => config('bantunet.event_statuses'),
        ];
    }
}