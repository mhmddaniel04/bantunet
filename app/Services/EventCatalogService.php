<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Skill;
use App\Models\Submission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Everything the public website needs to find and list initiatives.
 */
class EventCatalogService
{
    /**
     * Browse page: open initiatives filtered by the volunteer's choices.
     *
     * @param  array{q?: string, category?: string, state?: string, skill?: string, when?: string, sort?: string}  $filters
     */
    public function search(array $filters): LengthAwarePaginator
    {
        $query = $this->baseQuery()->openForApplications();

        $query->when($filters['q'] ?? null, function (Builder $q, string $term) {
            $like = '%'.addcslashes($term, '%_\\').'%';

            $q->where(function (Builder $w) use ($like) {
                $w->where('title', 'ilike', $like)
                  ->orWhere('description', 'ilike', $like)
                  ->orWhere('location', 'ilike', $like)
                  ->orWhereHas('organization', fn (Builder $o) => $o->where('org_name', 'ilike', $like));
            });
        });

        $query->when($filters['category'] ?? null, fn (Builder $q, string $slug) =>
            $q->whereHas('category', fn (Builder $c) => $c->where('slug', $slug)));

        $query->when($filters['state'] ?? null, fn (Builder $q, string $state) =>
            $q->where('state', $state));

        $query->when($filters['skill'] ?? null, fn (Builder $q, string $slug) =>
            $q->whereHas('skills', fn (Builder $s) => $s->where('slug', $slug)));

        $query->when($filters['when'] ?? null, fn (Builder $q, string $range) =>
            $this->applyDateRange($q, $range));

        if (($filters['sort'] ?? 'soonest') === 'newest') {
            $query->latest();
        } else {
            $query->orderBy('event_date')->orderBy('start_time');
        }

        return $query->paginate(config('bantunet.per_page'))->withQueryString();
    }

    /** Home page: featured first, then the soonest. */
    public function featured(int $limit = 3): Collection
    {
        return $this->baseQuery()
            ->openForApplications()
            ->orderByDesc('is_featured')
            ->orderBy('event_date')
            ->limit($limit)
            ->get();
    }

    /** Other open initiatives by the same organisation (detail page). */
    public function moreFromOrganization(Event $event, int $limit = 3): Collection
    {
        return $this->baseQuery()
            ->openForApplications()
            ->where('org_id', $event->org_id)
            ->whereKeyNot($event->getKey())
            ->orderBy('event_date')
            ->limit($limit)
            ->get();
    }

    /** All categories with how many open initiatives each has. */
    public function categoriesWithOpenCounts(): Collection
    {
        return Category::query()
            ->withCount(['events as open_events_count' => fn (Builder $q) => $q->openForApplications()])
            ->orderBy('name')
            ->get();
    }

    /** Numbers shown in the home page hero. */
    public function platformStats(): array
    {
        return [
            'open_events'       => Event::openForApplications()->count(),
            'organizations'     => Organization::count(),
            'volunteers_placed' => Submission::where('review_status', Submission::STATUS_APPROVED)->count(),
        ];
    }

    /** Dropdown options for the browse filters. */
    public function filterOptions(): array
    {
        return [
            'categories' => Category::orderBy('name')->pluck('name', 'slug')->all(),
            'skills'     => Skill::orderBy('name')->pluck('name', 'slug')->all(),
            'states'     => config('bantunet.states'),
            'dates'      => config('bantunet.date_ranges'),
            'sort'       => config('bantunet.sort_options'),
        ];
    }

    /* ------------------------------------------------------------------ */

    /** Loads everything an event card needs in as few queries as possible. */
    private function baseQuery(): Builder
    {
        return Event::query()
            ->with(['category', 'organization', 'skills'])
            ->withCount('approvedSubmissions');
    }

    private function applyDateRange(Builder $query, string $range): void
    {
        $today = today();

        match ($range) {
            'this_week'  => $query->whereBetween('event_date', [$today, $today->copy()->endOfWeek()]),
            'this_month' => $query->whereBetween('event_date', [$today, $today->copy()->endOfMonth()]),
            'next_30'    => $query->whereBetween('event_date', [$today, $today->copy()->addDays(30)]),
            'weekend'    => $query->whereRaw('EXTRACT(DOW FROM event_date) IN (0, 6)'),
            default      => null,
        };
    }
}