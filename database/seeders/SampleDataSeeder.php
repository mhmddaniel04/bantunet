<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Skill;
use App\Models\Submission;
use Database\Factories\SubmissionFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Realistic data set: 10 NPOs, ~60 initiatives (past year + next 2 months)
 * and ~400 applications with approvals, attendance and hours.
 *
 * Uses a fixed random seed, so everyone gets the same data.
 * After seeding, run:  php artisan events:embed
 */
class SampleDataSeeder extends Seeder
{
    private const PASSWORD = 'password123';

    private Collection $categories;
    private Collection $skills;

    public function run(): void
    {
        if (Organization::where('org_email', 'like', '%@sample.bantunet.local')->exists()) {
            $this->command?->warn('Sample data already exists. Use "php artisan migrate:fresh --seed" to start over.');

            return;
        }

        fake()->seed(2026);

        $organizations = require database_path('data/sample-organizations.php');
        $templates = require database_path('data/sample-initiatives.php');

        $this->categories = Category::pluck('category_id', 'slug');
        $this->skills = Skill::pluck('skill_id', 'slug');

        DB::transaction(function () use ($organizations, $templates) {
            foreach ($organizations as $data) {
                $organization = $this->createOrganization($data);

                foreach ($data['focus'] as $categorySlug) {
                    foreach ($templates[$categorySlug] as $template) {
                        // Every template ran once in the past year…
                        $this->createInitiative($organization, $data, $categorySlug, $template, past: true);

                        // …and most are running again soon.
                        if (fake()->boolean(65)) {
                            $this->createInitiative($organization, $data, $categorySlug, $template, past: false);
                        }
                    }
                }
            }
        });

        $this->command?->info(sprintf(
            'Sample data ready: %d NPOs, %d initiatives, %d applications. Next: php artisan events:embed',
            count($organizations),
            Event::count(),
            Submission::count(),
        ));
    }

    /* ------------------------------------------------------------------ */

    private function createOrganization(array $data): Organization
    {
        $organization = Organization::create([
            'org_name'        => $data['name'],
            'ros_number'      => $data['ros'],
            'org_email'       => $data['email'],
            'org_phone'       => $data['phone'],
            'org_hq_location' => $data['address'],
            'state'           => $data['state'],
            'description'     => $data['description'],
            'password'        => self::PASSWORD,
        ]);

        $organization->forceFill(['is_verified' => true])->save();

        return $organization;
    }

    private function createInitiative(Organization $organization, array $data, string $categorySlug, array $template, bool $past): void
    {
        $city = fake()->randomElement($data['cities']);

        $date = $past
            ? today()->subDays(fake()->numberBetween(5, 330))
            : today()->addDays(fake()->numberBetween(5, 60));

        $event = $organization->events()->create([
            'category_id'          => $this->categories[$categorySlug],
            'title'                => $template['title'].' — '.$city,
            'description'          => $template['description']."\n\nThis session takes place in {$city}, {$data['state']}.",
            'requirements'         => $template['requirements'],
            'location'             => str_replace('{city}', $city, $template['venue']),
            'state'                => $data['state'],
            'event_date'           => $date,
            'start_time'           => $template['start'],
            'end_time'             => $template['end'],
            'application_deadline' => $date->copy()->subDays(2),
            'capacity'             => $template['capacity'],
            'is_featured'          => ! $past && fake()->boolean(20),
            'status'               => $past ? Event::STATUS_COMPLETED : Event::STATUS_PUBLISHED,
        ]);

        $event->skills()->sync($this->skills->only($template['skills'])->values()->all());

        $this->createApplications($event, $past, $this->hours($template));
    }

    private function createApplications(Event $event, bool $past, float $eventHours): void
    {
        $count = $past ? fake()->numberBetween(4, 14) : fake()->numberBetween(0, 8);
        $approved = 0;

        for ($i = 0; $i < $count; $i++) {
            $status = $past
                ? fake()->randomElement(['Approved', 'Approved', 'Approved', 'Approved', 'Approved', 'Approved', 'Rejected', 'Rejected', 'Withdrawn'])
                : fake()->randomElement(['Pending', 'Pending', 'Pending', 'Approved', 'Approved', 'Rejected']);

            // Never approve more volunteers than the initiative has slots for.
            if ($status === 'Approved' && $approved >= $event->capacity) {
                $status = 'Rejected';
            }
            $approved += $status === 'Approved' ? 1 : 0;

            $appliedAt = $event->event_date->copy()
                ->subDays(fake()->numberBetween(3, 30))
                ->setTime(fake()->numberBetween(8, 22), fake()->numberBetween(0, 59));

            if ($appliedAt->isFuture()) {
                $appliedAt = now()->subHours(fake()->numberBetween(1, 48));
            }

            $isReviewed = in_array($status, ['Approved', 'Rejected'], true);
            $attended = $past && $status === 'Approved' && fake()->boolean(85);

            SubmissionFactory::new()->create([
                'event_id'          => $event->event_id,
                'review_status'     => $status,
                'reviewer_note'     => $status === 'Rejected' ? 'Thank you for applying. All places have been filled this time.' : null,
                'reviewed_at'       => $isReviewed ? $this->min($appliedAt->copy()->addDays(fake()->numberBetween(1, 3)), now()) : null,
                'attended'          => $attended,
                'hours_volunteered' => $attended ? max(1, $eventHours - fake()->randomElement([0, 0, 0, 0.5, 1])) : null,
                'created_at'        => $appliedAt,
                'updated_at'        => $appliedAt,
            ]);
        }
    }

    private function hours(array $template): float
    {
        $minutes = Carbon::parse($template['start'])->diffInMinutes(Carbon::parse($template['end']));

        return round(abs($minutes) / 60, 1);
    }

    private function min(Carbon $a, Carbon $b): Carbon
    {
        return $a->lessThan($b) ? $a : $b;
    }
}