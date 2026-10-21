<?php

/*
|--------------------------------------------------------------------------
| BantuNet application settings
|--------------------------------------------------------------------------
|
| Shared option lists used by forms, validation rules and reports.
| Change them here and every page updates automatically.
|
*/

return [

    'states' => [
        'Johor',
        'Kedah',
        'Kelantan',
        'Melaka',
        'Negeri Sembilan',
        'Pahang',
        'Perak',
        'Perlis',
        'Pulau Pinang',
        'Sabah',
        'Sarawak',
        'Selangor',
        'Terengganu',
        'W.P. Kuala Lumpur',
        'W.P. Labuan',
        'W.P. Putrajaya',
    ],

    // value => label
    'availability' => [
        'Weekdays' => 'Weekdays only',
        'Weekends' => 'Weekends only',
        'Any'      => 'Flexible / any day',
    ],

    'genders' => ['Male', 'Female', 'Prefer not to say'],

    'tshirt_sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],

    'review_statuses' => ['Pending', 'Approved', 'Rejected', 'Withdrawn'],

    // value => label
    'event_statuses' => [
        'draft'     => 'Draft',
        'published' => 'Published',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    'resume' => [
        'max_kb' => 2048,
        'mimes'  => ['pdf', 'doc', 'docx'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Public browse page
    |--------------------------------------------------------------------------
    */

    'per_page' => 9,

    // value => label  (used by the "When" filter)
    'date_ranges' => [
        'this_week'  => 'This week',
        'weekend'    => 'Weekends only',
        'this_month' => 'This month',
        'next_30'    => 'Next 30 days',
    ],

    // value => label
    'sort_options' => [
        'soonest' => 'Happening soonest',
        'newest'  => 'Newly posted',
    ],

    /*
    |--------------------------------------------------------------------------
    | Smart AI Matching
    |--------------------------------------------------------------------------
    |
    | Final score = weighted average of the parts the volunteer answered.
    | "semantic" is the cosine similarity between Gemini embeddings.
    | The weights should add up to 1.
    |
    */

    'matching' => [
        'weights' => [
            'semantic'     => 0.70,
            'skills'       => 0.15,
            'category'     => 0.05,
            'availability' => 0.05,
            'state'        => 0.05,
        ],

        // How many recommendations to show
        'limit' => 6,

        // Cosine similarity needed for the "matches what you described" reason
        'reasons' => [
            'strong'  => 0.70,
            'related' => 0.60,
        ],
    ],

];