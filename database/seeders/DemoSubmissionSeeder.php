<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Submission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * 3 sample applications for each demo initiative (Pending / Approved / Rejected mix).
 * Uses a fixed Faker seed, so running it again does not create duplicates.
 */
class DemoSubmissionSeeder extends Seeder
{
    private const SUMMARIES = [
        'Active in my university volunteer club for two years, mostly outdoor and community events.',
        'I tutor primary school students on weekends and enjoy working with children.',
        'Comfortable with heavy lifting and long hours outdoors. I have joined several gotong-royong programmes.',
        'Trained in basic first aid (St John Ambulance) and calm in busy situations.',
        'I take photos for my campus club and manage its Instagram page.',
        'Good with elderly people — I help care for my grandparents at home.',
        'Experienced in event logistics: registration desks, crowd control and setting up booths.',
        'I can speak English, Malay and Mandarin, and I am happy to help with translation.',
    ];

    public function run(): void
    {
        $organization = Organization::where('org_email', 'demo@bantunet.local')->first();

        if (! $organization) {
            $this->command?->warn('Run DemoSeeder first.');
            return;
        }

        $faker = fake();

        foreach ($organization->events()->get() as $event) {
            for ($i = 0; $i < 3; $i++) {
                $faker->seed($event->event_id * 10 + $i);   // same data every run

                $name = $faker->name();
                $email = Str::slug($name, '.').'.'.$event->event_id.'@example.com';
                $status = $faker->randomElement(['Pending', 'Pending', 'Approved', 'Approved', 'Rejected']);
                $fromAi = $faker->boolean(40);

                $submission = Submission::firstOrNew([
                    'event_id'        => $event->event_id,
                    'volunteer_email' => $email,
                ]);

                $submission->fill([
                    'volunteer_name'  => $name,
                    'volunteer_phone' => $faker->numerify('01#-### ####'),
                    'age'             => $faker->numberBetween(18, 45),
                    'gender'          => $faker->randomElement(config('bantunet.genders')),
                    'tshirt_size'     => $faker->randomElement(config('bantunet.tshirt_sizes')),
                    'availability'    => $faker->randomElement(array_keys(config('bantunet.availability'))),
                    'emergency_name'  => $faker->name(),
                    'emergency_phone' => $faker->numerify('01#-### ####'),
                    'skills_summary'  => $faker->randomElement(self::SUMMARIES),
                    'source'          => $fromAi ? Submission::SOURCE_SMART_MATCH : Submission::SOURCE_BROWSE,
                    'match_score'     => $fromAi ? $faker->randomFloat(2, 62, 94) : null,
                ]);

                $submission->forceFill([
                    'review_status' => $status,
                    'reviewer_note' => $status === 'Rejected' ? 'Thank you for applying. We have filled this role for now.' : null,
                    'reviewed_at'   => $status === 'Pending' ? null : now()->subDays($faker->numberBetween(0, 3)),
                ])->save();
            }
        }
    }
}