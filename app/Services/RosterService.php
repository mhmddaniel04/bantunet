<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Submission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * The list of approved volunteers for one initiative, and their attendance.
 */
class RosterService
{
    public function volunteers(Event $event): Collection
    {
        return $event->approvedSubmissions()->orderBy('volunteer_name')->get();
    }

    /** e.g. ['S' => 3, 'M' => 5, 'L' => 2] in size order (helps with ordering T-shirts). */
    public function tshirtSummary(Event $event): array
    {
        $counts = $event->approvedSubmissions()
            ->selectRaw('tshirt_size, COUNT(*) AS total')
            ->groupBy('tshirt_size')
            ->pluck('total', 'tshirt_size');

        return collect(config('bantunet.tshirt_sizes'))
            ->mapWithKeys(fn (string $size) => [$size => (int) ($counts[$size] ?? 0)])
            ->filter()
            ->all();
    }

    /** Event length in hours (used to pre-fill the Hours boxes), or null without an end time. */
    public function defaultHours(Event $event): ?float
    {
        if (! $event->end_time) {
            return null;
        }

        $minutes = Carbon::parse($event->start_time)->diffInMinutes(Carbon::parse($event->end_time));

        return round(abs($minutes) / 60, 2);
    }

    /** Attendance can be recorded from the event day onwards. */
    public function canMarkAttendance(Event $event): bool
    {
        return ! $event->event_date->isFuture();
    }

    /**
     * @param  array<int|string, array{attended: bool|string, hours?: ?string}>  $rows  keyed by submission_id
     * @return int  how many volunteers were updated
     */
    public function saveAttendance(Event $event, array $rows): int
    {
        if (! $this->canMarkAttendance($event)) {
            throw ValidationException::withMessages([
                'attendance' => 'Attendance can be recorded on or after the event date.',
            ]);
        }

        return DB::transaction(function () use ($event, $rows) {
            $volunteers = $event->approvedSubmissions()
                ->whereIn('submission_id', array_keys($rows))
                ->get();

            foreach ($volunteers as $volunteer) {
                $row = $rows[$volunteer->submission_id];
                $attended = (bool) $row['attended'];

                $volunteer->forceFill([
                    'attended'          => $attended,
                    'hours_volunteered' => $attended ? ($row['hours'] ?? null) : null,
                ])->save();
            }

            return $volunteers->count();
        });
    }

    public function filename(Event $event, string $extension = 'csv'): string
    {
        return 'volunteers-'.Str::slug(Str::limit($event->title, 40, '')).'-'.$event->event_date->format('Y-m-d').'.'.$extension;
    }

    /** Header + one row per approved volunteer. */
    public function csvRows(Event $event): array
    {
        $availability = config('bantunet.availability');

        $rows = [[
            'Name', 'Email', 'Phone', 'Age', 'Gender', 'T-shirt', 'Availability',
            'Emergency contact', 'Emergency phone', 'Attended', 'Hours', 'Applied on', 'Tracking code',
        ]];

        foreach ($this->volunteers($event) as $v) {
            /** @var Submission $v */
            $rows[] = [
                $v->volunteer_name,
                $v->volunteer_email,
                $v->volunteer_phone,
                $v->age,
                $v->gender,
                $v->tshirt_size,
                $availability[$v->availability] ?? $v->availability,
                $v->emergency_name,
                $v->emergency_phone,
                $v->attended ? 'Yes' : 'No',
                $v->hours_volunteered,
                $v->created_at->format('Y-m-d'),
                $v->tracking_code,
            ];
        }

        return $rows;
    }
}