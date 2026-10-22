<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Full data set:
     *  - lookup tables (categories, skills)
     *  - demo NPO (demo@bantunet.local / password123) with upcoming initiatives
     *  - 10 sample NPOs with a year of initiatives and applications
     *
     * Afterwards run: php artisan events:embed
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SkillSeeder::class,
            DemoSeeder::class,
            DemoSubmissionSeeder::class,
            SampleDataSeeder::class,
        ]);
    }
}