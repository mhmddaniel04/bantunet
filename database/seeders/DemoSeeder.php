<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Skill;
use Illuminate\Database\Seeder;

/**
 * One verified demo NPO with 8 upcoming initiatives, for testing the public pages.
 * Login: demo@bantunet.local / password123
 * Safe to run many times (updates instead of duplicating).
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::firstOrCreate(
            ['org_email' => 'demo@bantunet.local'],
            [
                'org_name'        => 'Persatuan Sukarelawan Komuniti Malaysia',
                'ros_number'      => 'PPM-001-14-01012020',
                'org_phone'       => '03-2345 6789',
                'org_hq_location' => 'Jalan Tun Razak, 50400 Kuala Lumpur',
                'state'           => 'W.P. Kuala Lumpur',
                'website'         => 'https://example.org',
                'description'     => 'A community volunteer network running environmental, education and welfare programmes across Malaysia since 2020.',
                'password'        => 'password123',
            ]
        );
        $organization->forceFill(['is_verified' => true])->save();

        $categories = Category::pluck('category_id', 'slug');
        $skills = Skill::pluck('skill_id', 'slug');

        foreach ($this->initiatives() as $data) {
            $eventDate = today()->addDays($data['in_days']);

            $event = Event::updateOrCreate(
                ['org_id' => $organization->org_id, 'title' => $data['title']],
                [
                    'category_id'          => $categories[$data['category']],
                    'description'          => $data['description'],
                    'requirements'         => $data['requirements'],
                    'location'             => $data['location'],
                    'state'                => $data['state'],
                    'event_date'           => $eventDate,
                    'start_time'           => $data['start'],
                    'end_time'             => $data['end'],
                    'application_deadline' => $eventDate->copy()->subDays(2),
                    'capacity'             => $data['capacity'],
                    'is_featured'          => $data['featured'],
                    'status'               => Event::STATUS_PUBLISHED,
                ]
            );

            $event->skills()->sync($skills->only($data['skills'])->values()->all());
        }
    }

    private function initiatives(): array
    {
        return [
            [
                'title'        => 'Urban Forest Restoration at Taman Rimba Kiara',
                'category'     => 'environment',
                'skills'       => ['physical-labour', 'logistics-coordination'],
                'location'     => 'Taman Rimba Kiara, Taman Tun Dr Ismail',
                'state'        => 'W.P. Kuala Lumpur',
                'in_days'      => 9,
                'start'        => '07:30',
                'end'          => '11:30',
                'capacity'     => 40,
                'featured'     => true,
                'description'  => "Help us plant 300 native saplings to restore Kuala Lumpur's green belt. Volunteers will dig, plant, mulch and water young trees alongside our forestry team.\n\nPerfect for outdoor lovers who don't mind getting their hands dirty. No experience needed — a short safety and planting briefing is given on site.",
                'requirements' => 'Wear covered shoes and long pants. Bring a water bottle, hat and sunscreen. Gloves and tools are provided.',
            ],
            [
                'title'        => 'Weekend English & Maths Tutoring for Refugee Children',
                'category'     => 'education',
                'skills'       => ['teaching-tutoring', 'translation-languages'],
                'location'     => 'Community Learning Centre, Petaling Jaya',
                'state'        => 'Selangor',
                'in_days'      => 12,
                'start'        => '09:00',
                'end'          => '12:00',
                'capacity'     => 12,
                'featured'     => true,
                'description'  => "Teach basic English reading and primary-level Mathematics to refugee children aged 7–12 in small groups. Lesson plans and materials are prepared for you.\n\nIdeal for university students, teachers and anyone patient who enjoys working with kids. Speaking Malay, Burmese or Arabic is a bonus.",
                'requirements' => 'Commit to at least 3 Saturdays. A short online child-safety briefing is required before your first session.',
            ],
            [
                'title'        => 'Afternoon Visit to Rumah Seri Kenangan',
                'category'     => 'elderly-care',
                'skills'       => ['social-care-empathy', 'event-management'],
                'location'     => 'Rumah Seri Kenangan, George Town',
                'state'        => 'Pulau Pinang',
                'in_days'      => 6,
                'start'        => '14:00',
                'end'          => '17:00',
                'capacity'     => 15,
                'featured'     => true,
                'description'  => "Spend a warm afternoon with senior residents: chatting, singing old songs, playing board games and serving tea.\n\nMany residents rarely receive visitors, so a friendly conversation means a lot. Great for people who are good listeners and enjoy meaningful company.",
                'requirements' => 'Dress modestly. Please do not visit if you have flu symptoms.',
            ],
            [
                'title'        => 'Flood Relief Supply Packing',
                'category'     => 'disaster-relief',
                'skills'       => ['logistics-coordination', 'physical-labour', 'driving'],
                'location'     => 'Dewan Serbaguna, Kota Bharu',
                'state'        => 'Kelantan',
                'in_days'      => 4,
                'start'        => '08:00',
                'end'          => '16:00',
                'capacity'     => 50,
                'featured'     => false,
                'description'  => "Pack and load emergency kits — rice, canned food, hygiene items and blankets — for families affected by monsoon floods.\n\nVolunteers with a valid driving licence are also needed to deliver supplies to relief centres nearby.",
                'requirements' => 'Able to lift boxes up to 10 kg. Drivers must bring their licence.',
            ],
            [
                'title'        => 'Community Food Bank Distribution Day',
                'category'     => 'food-security',
                'skills'       => ['cooking-food-handling', 'logistics-coordination', 'administration-data-entry'],
                'location'     => 'Food Bank Hub, Seksyen 7, Shah Alam',
                'state'        => 'Selangor',
                'in_days'      => 15,
                'start'        => '10:00',
                'end'          => '15:00',
                'capacity'     => 25,
                'featured'     => false,
                'description'  => "Sort donated groceries, prepare food packs and help register families collecting their monthly supplies.\n\nRoles include food sorting, registration desk (data entry) and queue management.",
                'requirements' => 'Food handlers will receive gloves and hairnets on site.',
            ],
            [
                'title'        => 'Shelter Care Weekend for Rescued Cats',
                'category'     => 'animal-welfare',
                'skills'       => ['social-care-empathy', 'photography-videography', 'social-media-marketing'],
                'location'     => 'Paws Haven Shelter, Johor Bahru',
                'state'        => 'Johor',
                'in_days'      => 10,
                'start'        => '09:00',
                'end'          => '13:00',
                'capacity'     => 8,
                'featured'     => false,
                'description'  => "Clean enclosures, feed and socialise rescued cats so they are ready for adoption.\n\nWe also need photographers to take adoption profile photos and help post them on social media.",
                'requirements' => 'Not suitable for people with cat allergies. Wear clothes you don\'t mind getting dirty.',
            ],
            [
                'title'        => 'Digital Literacy Class for Senior Citizens',
                'category'     => 'technology-digital',
                'skills'       => ['it-technical-support', 'teaching-tutoring'],
                'location'     => 'Perpustakaan Awam, Ipoh',
                'state'        => 'Perak',
                'in_days'      => 18,
                'start'        => '10:00',
                'end'          => '12:30',
                'capacity'     => 6,
                'featured'     => false,
                'description'  => "Guide senior citizens one-to-one in using smartphones safely: WhatsApp video calls, online banking basics and spotting scam messages.\n\nPatience matters more than technical skill.",
                'requirements' => 'Basic smartphone knowledge (Android and iPhone).',
            ],
            [
                'title'        => 'Marshals for Putrajaya Charity Fun Run',
                'category'     => 'sports-events',
                'skills'       => ['event-management', 'first-aid-medical', 'logistics-coordination'],
                'location'     => 'Dataran Putra, Presint 1',
                'state'        => 'W.P. Putrajaya',
                'in_days'      => 21,
                'start'        => '05:30',
                'end'          => '10:30',
                'capacity'     => 60,
                'featured'     => false,
                'description'  => "Be a route marshal, water-station helper or finish-line crew for a 5 km charity run raising funds for children's cancer treatment.\n\nVolunteers with first-aid certification are especially welcome at medical points.",
                'requirements' => 'Arrive by 5:15 AM for briefing. Volunteer T-shirt and breakfast provided.',
            ],
        ];
    }
}