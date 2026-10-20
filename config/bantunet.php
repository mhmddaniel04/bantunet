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

];