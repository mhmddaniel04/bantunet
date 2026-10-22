<?php

namespace Database\Factories;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * One random volunteer application (Malaysian names via APP_FAKER_LOCALE=ms_MY).
 * The seeder decides event, status, dates and attendance.
 *
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    private const SUMMARIES = [
        'Active in my university volunteer club, mostly outdoor and community events.',
        'I tutor primary school students on weekends and enjoy working with children.',
        'Comfortable with physical work and long hours outdoors. Joined many gotong-royong programmes.',
        'Trained in basic first aid and calm in busy situations.',
        'I take photos for my campus club and manage its Instagram page.',
        'Good with elderly people — I help care for my grandparents at home.',
        'Experienced in event logistics: registration desks, crowd control and setting up booths.',
        'I speak English, Malay and Mandarin and am happy to help with translation.',
        'I work in IT and like helping people with their phones and computers.',
        'I love cooking and have helped at my mosque\'s kitchen during Ramadan.',
        'First time volunteering — eager to learn and help wherever needed.',
        null,
    ];

    public function definition(): array
    {
        $name = fake()->name();
        $fromSmartMatch = fake()->boolean(35);

        return [
            'volunteer_name'  => $name,
            'volunteer_email' => Str::slug($name, '.').'.'.fake()->unique()->numberBetween(100, 999999).'@example.com',
            'volunteer_phone' => fake()->numerify('01#-### ####'),
            'age'             => fake()->numberBetween(17, 58),
            'gender'          => fake()->randomElement(['Male', 'Female', 'Female', 'Male', 'Prefer not to say']),
            'tshirt_size'     => fake()->randomElement(['S', 'M', 'M', 'L', 'L', 'XL', 'XS', 'XXL']),
            'availability'    => fake()->randomElement(['Weekends', 'Weekends', 'Any', 'Weekdays']),
            'emergency_name'  => fake()->name().' ('.fake()->randomElement(['Mother', 'Father', 'Spouse', 'Sibling']).')',
            'emergency_phone' => fake()->numerify('01#-### ####'),
            'skills_summary'  => fake()->randomElement(self::SUMMARIES),
            'source'          => $fromSmartMatch ? Submission::SOURCE_SMART_MATCH : Submission::SOURCE_BROWSE,
            'match_score'     => $fromSmartMatch ? fake()->randomFloat(2, 58, 92) : null,
            'review_status'   => Submission::STATUS_PENDING,
        ];
    }
}