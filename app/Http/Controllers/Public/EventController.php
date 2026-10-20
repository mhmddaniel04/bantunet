<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\EventFilterRequest;
use App\Models\Event;
use App\Services\EventCatalogService;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(EventFilterRequest $request, EventCatalogService $catalog): View
    {
        $filters = $request->filters();

        return view('public.events.index', [
            'events'  => $catalog->search($filters),
            'filters' => $filters,
            'options' => $catalog->filterOptions(),
        ]);
    }

    public function show(Event $event, EventCatalogService $catalog): View
    {
        // Drafts are private to the NPO.
        abort_if($event->status === Event::STATUS_DRAFT, 404);

        $event->load(['category', 'organization', 'skills'])
              ->loadCount('approvedSubmissions');

        return view('public.events.show', [
            'event'      => $event,
            'moreEvents' => $catalog->moreFromOrganization($event),
        ]);
    }
}