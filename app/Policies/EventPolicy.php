<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\Organization;

/**
 * An organisation may only manage its own events.
 */
class EventPolicy
{
    public function update(Organization $organization, Event $event): bool
    {
        return (int) $organization->org_id === (int) $event->org_id;
    }

    public function delete(Organization $organization, Event $event): bool
    {
        return $this->update($organization, $event);
    }

    public function viewSubmissions(Organization $organization, Event $event): bool
    {
        return $this->update($organization, $event);
    }
}