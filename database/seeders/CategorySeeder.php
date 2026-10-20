<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Environment'         => 'Tree planting, river and beach cleanups, recycling drives.',
            'Education'           => 'Tutoring, literacy programmes, mentoring students.',
            'Community Care'      => 'Neighbourhood programmes, community kitchens, gotong-royong.',
            'Elderly Care'        => 'Visits and activities for senior citizens and care homes.',
            'Children & Youth'    => 'Programmes for orphans, refugees and underprivileged youth.',
            'Health & Medical'    => 'Health screenings, blood donation drives, medical camps.',
            'Disaster Relief'     => 'Flood relief, emergency supply distribution, evacuation support.',
            'Animal Welfare'      => 'Shelter care, adoption drives, stray feeding programmes.',
            'Food Security'       => 'Food banks, meal distribution, surplus food rescue.',
            'Arts & Culture'      => 'Heritage, festivals, community art and music programmes.',
            'Sports & Events'     => 'Marathons, charity runs and large public events.',
            'Technology & Digital'=> 'Digital literacy, IT support and tech for NGOs.',
        ];

        $now = now();
        $rows = [];
        foreach ($categories as $name => $description) {
            $rows[] = [
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // upsert = safe to run many times without duplicates
        DB::table('categories')->upsert($rows, ['slug'], ['name', 'description', 'updated_at']);
    }
}