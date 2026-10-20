<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'Physical Labour',
            'Teaching & Tutoring',
            'Social Care & Empathy',
            'Logistics & Coordination',
            'First Aid & Medical',
            'Photography & Videography',
            'Graphic Design',
            'Social Media & Marketing',
            'IT & Technical Support',
            'Cooking & Food Handling',
            'Event Management',
            'Translation & Languages',
            'Counselling',
            'Driving',
            'Fundraising',
            'Administration & Data Entry',
        ];

        $now = now();
        $rows = array_map(fn (string $name) => [
            'name' => $name,
            'slug' => Str::slug($name),
            'created_at' => $now,
            'updated_at' => $now,
        ], $skills);

        DB::table('skills')->upsert($rows, ['slug'], ['name', 'updated_at']);
    }
}