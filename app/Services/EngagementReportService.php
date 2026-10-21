<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Submission;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

/**
 * Volunteer Engagement Report: every number on the Reports page and its exports.
 * Covers initiatives whose event date falls inside the chosen period.
 */
class EngagementReportService
{
    private const AGE_GROUPS = [
        '13–17' => [13, 17],
        '18–24' => [18, 24],
        '25–34' => [25, 34],
        '35–44' => [35, 44],
        '45+'   => [45, 200],
    ];

    public function build(Organization $organization, CarbonImmutable $from, CarbonImmutable $to, ?int $categoryId): array
    {
        $events = $organization->events()
            ->where('status', '!=', Event::STATUS_DRAFT)
            ->whereBetween('event_date', [$from->toDateString(), $to->toDateString()])
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->with('category')
            ->orderBy('event_date')
            ->get();

        $submissions = Submission::whereIn('event_id', $events->pluck('event_id'))->get();
        $byEvent = $submissions->groupBy('event_id');

        $approved = $submissions->where('review_status', Submission::STATUS_APPROVED);
        $reviewed = $submissions->whereIn('review_status', [Submission::STATUS_APPROVED, Submission::STATUS_REJECTED]);
        $attended = $approved->where('attended', true);

        // Attendance rate only counts events that already happened.
        $pastEventIds = $events->filter(fn (Event $e) => $e->event_date->lt(today()))->pluck('event_id');
        $approvedPast = $approved->whereIn('event_id', $pastEventIds);

        $smart = $submissions->where('source', Submission::SOURCE_SMART_MATCH);
        $browse = $submissions->where('source', Submission::SOURCE_BROWSE);

        return [
            'from'     => $from,
            'to'       => $to,
            'category' => $categoryId ? Category::find($categoryId) : null,

            'summary' => [
                'initiatives'       => $events->count(),
                'applications'      => $submissions->count(),
                'volunteers'        => $approved->pluck('volunteer_email')->unique()->count(),
                'approval_rate'     => $this->rate($approved->count(), $reviewed->count()),
                'attendance_rate'   => $this->rate($approvedPast->where('attended', true)->count(), $approvedPast->count()),
                'hours'             => round((float) $attended->sum('hours_volunteered'), 1),
                'smart_match_share' => $this->rate($smart->count(), $submissions->count()),
                'avg_match_score'   => $smart->isEmpty() ? null : round((float) $smart->avg('match_score'), 1),
            ],

            // Is Smart Match bringing in better-suited volunteers? (evaluation evidence)
            'sources' => [
                ['label' => 'Smart Match', 'applications' => $smart->count(),  'approval_rate' => $this->approvalRate($smart)],
                ['label' => 'Browse',      'applications' => $browse->count(), 'approval_rate' => $this->approvalRate($browse)],
            ],

            'monthly'     => $this->monthly($events, $byEvent, $from, $to),
            'categories'  => $this->byCategory($events, $byEvent),
            'genders'     => $this->countEach($approved, 'gender', config('bantunet.genders')),
            'age_groups'  => $this->ageGroups($approved),
            'initiatives' => $events->map(fn (Event $event) => $this->eventRow($event, $byEvent->get($event->event_id, collect())))->all(),
        ];
    }

    /** Quick links for the period filter. */
    public function presets(): array
    {
        $now = CarbonImmutable::now();

        return [
            'This year'      => [$now->startOfYear(), $now->endOfYear()],
            'Last year'      => [$now->subYear()->startOfYear(), $now->subYear()->endOfYear()],
            'Last 12 months' => [$now->subMonths(11)->startOfMonth(), $now->endOfMonth()],
        ];
    }

    /** The report as CSV rows (summary first, then one row per initiative). */
    public function csvRows(Organization $organization, array $report): array
    {
        $s = $report['summary'];
        $pct = fn (?float $v) => $v === null ? '-' : $v.'%';

        $rows = [
            ['BantuNet Volunteer Engagement Report'],
            ['Organisation', $organization->org_name],
            ['ROS number', $organization->ros_number],
            ['Period', $report['from']->format('j M Y').' - '.$report['to']->format('j M Y')],
            ['Category', $report['category']?->name ?? 'All categories'],
            ['Generated', now()->format('j M Y, g:i A')],
            [],
            ['SUMMARY'],
            ['Initiatives held', $s['initiatives']],
            ['Applications received', $s['applications']],
            ['Unique volunteers approved', $s['volunteers']],
            ['Approval rate', $pct($s['approval_rate'])],
            ['Attendance rate', $pct($s['attendance_rate'])],
            ['Volunteer hours', $s['hours']],
            ['Applications via Smart Match', $pct($s['smart_match_share'])],
            ['Average Smart Match score', $s['avg_match_score'] === null ? '-' : $s['avg_match_score'].'%'],
            [],
            ['INITIATIVES'],
            ['Initiative', 'Date', 'Category', 'State', 'Applications', 'Approved', 'Attended', 'Hours'],
        ];

        foreach ($report['initiatives'] as $row) {
            $rows[] = [
                $row['event']->title,
                $row['event']->event_date->format('Y-m-d'),
                $row['event']->category->name,
                $row['event']->state,
                $row['applications'],
                $row['approved'],
                $row['attended'],
                $row['hours'],
            ];
        }

        return $rows;
    }

    /* ------------------------------------------------------------------ */

    private function eventRow(Event $event, Collection $submissions): array
    {
        $attended = $submissions->where('review_status', Submission::STATUS_APPROVED)->where('attended', true);

        return [
            'event'        => $event,
            'applications' => $submissions->count(),
            'approved'     => $submissions->where('review_status', Submission::STATUS_APPROVED)->count(),
            'attended'     => $attended->count(),
            'hours'        => round((float) $attended->sum('hours_volunteered'), 1),
        ];
    }

    /** Approved volunteers and hours for every month in the period (by event date). */
    private function monthly(Collection $events, Collection $byEvent, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $months = [];

        foreach (CarbonPeriod::create($from->startOfMonth(), '1 month', $to->startOfMonth()) as $month) {
            $months[$month->format('Y-m')] = ['label' => $month->format('M y'), 'approved' => 0, 'hours' => 0.0];
        }

        foreach ($events as $event) {
            $key = $event->event_date->format('Y-m');

            if (! isset($months[$key])) {
                continue;
            }

            $row = $this->eventRow($event, $byEvent->get($event->event_id, collect()));
            $months[$key]['approved'] += $row['approved'];
            $months[$key]['hours'] += $row['hours'];
        }

        return array_values($months);
    }

    private function byCategory(Collection $events, Collection $byEvent): array
    {
        return $events
            ->groupBy(fn (Event $event) => $event->category->name)
            ->map(function (Collection $group, string $name) use ($byEvent) {
                $rows = $group->map(fn (Event $e) => $this->eventRow($e, $byEvent->get($e->event_id, collect())));

                return [
                    'label'       => $name,
                    'initiatives' => $group->count(),
                    'approved'    => $rows->sum('approved'),
                    'hours'       => round($rows->sum('hours'), 1),
                ];
            })
            ->sortByDesc('approved')
            ->values()
            ->all();
    }

    private function countEach(Collection $submissions, string $field, array $values): array
    {
        return collect($values)
            ->map(fn (string $value) => ['label' => $value, 'value' => $submissions->where($field, $value)->count()])
            ->all();
    }

    private function ageGroups(Collection $submissions): array
    {
        return collect(self::AGE_GROUPS)
            ->map(fn (array $range, string $label) => [
                'label' => $label,
                'value' => $submissions->whereBetween('age', $range)->count(),
            ])
            ->values()
            ->all();
    }

    private function approvalRate(Collection $submissions): ?float
    {
        return $this->rate(
            $submissions->where('review_status', Submission::STATUS_APPROVED)->count(),
            $submissions->whereIn('review_status', [Submission::STATUS_APPROVED, Submission::STATUS_REJECTED])->count(),
        );
    }

    /** Percentage with 1 decimal, or null when there is nothing to divide by. */
    private function rate(int $part, int $total): ?float
    {
        return $total > 0 ? round($part / $total * 100, 1) : null;
    }
}