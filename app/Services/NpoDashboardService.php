<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\Submission;

/**
 * Numbers and progress shown on the NPO dashboard overview.
 */
class NpoDashboardService
{
    public function stats(Organization $organization): array
    {
        return [
            'open_events'  => $organization->events()->openForApplications()->count(),
            'total_events' => $organization->events()->count(),
            'applications' => $organization->submissions()->count(),
            'pending'      => $organization->submissions()
                                ->where('review_status', Submission::STATUS_PENDING)
                                ->count(),
        ];
    }

    public function checklist(array $stats): array
    {
        return [
            ['label' => 'Create your NPO account',                 'done' => true],
            ['label' => 'Publish your first volunteer initiative', 'done' => $stats['total_events'] > 0],
            ['label' => 'Review your first volunteer application', 'done' => $stats['applications'] > 0],
        ];
    }
}