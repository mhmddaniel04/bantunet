<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\Submission;

/**
 * An organisation may only review applications to its own events.
 */
class SubmissionPolicy
{
    public function view(Organization $organization, Submission $submission): bool
    {
        return (int) $organization->org_id === (int) $submission->event->org_id;
    }

    public function review(Organization $organization, Submission $submission): bool
    {
        return $this->view($organization, $submission);
    }

    public function downloadResume(Organization $organization, Submission $submission): bool
    {
        return $this->view($organization, $submission);
    }
}