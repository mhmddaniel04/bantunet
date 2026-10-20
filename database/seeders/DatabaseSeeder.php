<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Organisation and event sample data are added in Phase 9.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SkillSeeder::class,
        ]);
    }
}