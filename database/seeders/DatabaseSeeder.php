<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * The full sample data set is added in Phase 9.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SkillSeeder::class,
            DemoSeeder::class,
            DemoSubmissionSeeder::class,
        ]);
    }
}