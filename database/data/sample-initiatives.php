<?php

/*
|--------------------------------------------------------------------------
| Initiative templates (2 per category)
|--------------------------------------------------------------------------
|
| SampleDataSeeder turns each template into real initiatives for the NPOs
| whose "focus" includes that category. {city} is replaced automatically.
| Descriptions are written carefully because they drive the AI matching.
|
*/

return [

    'environment' => [
        [
            'title'        => 'Mangrove Replanting Day',
            'venue'        => 'Mangrove conservation site, {city}',
            'description'  => 'Plant mangrove saplings along the coastline to protect it from erosion and give fish and crabs a safe nursery. Volunteers work in the mud at low tide, guided by our conservation officers.',
            'requirements' => 'Wear old clothes and booties — you will get muddy. Bring a hat, water bottle and sunscreen.',
            'skills'       => ['physical-labour', 'logistics-coordination'],
            'start' => '08:00', 'end' => '12:00', 'capacity' => 40,
        ],
        [
            'title'        => 'River Cleanup and Waste Audit',
            'venue'        => 'Riverside park, {city}',
            'description'  => 'Remove plastic and rubbish from the riverbank, then sort and count it so we can report the main pollution sources to the local council. Great for nature lovers who enjoy outdoor work.',
            'requirements' => 'Closed shoes and long pants. Gloves, grabbers and bags are provided.',
            'skills'       => ['physical-labour', 'administration-data-entry'],
            'start' => '08:00', 'end' => '11:30', 'capacity' => 30,
        ],
    ],

    'education' => [
        [
            'title'        => 'Weekend Reading Buddies for Primary Pupils',
            'venue'        => 'Community learning centre, {city}',
            'description'  => 'Read storybooks one-to-one with children aged 7 to 10 who are behind in reading. A short training session and all reading materials are provided. Patience and a love of books are all you need.',
            'requirements' => 'Commit to at least two sessions. Child-safety briefing required.',
            'skills'       => ['teaching-tutoring', 'translation-languages'],
            'start' => '09:00', 'end' => '11:00', 'capacity' => 15,
        ],
        [
            'title'        => 'SPM Maths & Science Revision Class',
            'venue'        => 'Public library meeting room, {city}',
            'description'  => 'Help Form 5 students from low-income families revise Mathematics, Physics and Chemistry before their SPM exams through small-group tutoring and past-year questions.',
            'requirements' => 'Good results in SPM Maths/Science or a related degree.',
            'skills'       => ['teaching-tutoring'],
            'start' => '14:00', 'end' => '17:00', 'capacity' => 10,
        ],
    ],

    'community-care' => [
        [
            'title'        => 'Gotong-Royong Kampung Clean & Repair',
            'venue'        => 'Dewan orang ramai, {city}',
            'description'  => 'Join residents to clean drains, repaint the community hall and repair a playground in a low-income neighbourhood. A friendly day of teamwork with breakfast and lunch provided.',
            'requirements' => 'Wear clothes suitable for painting and outdoor work.',
            'skills'       => ['physical-labour', 'event-management'],
            'start' => '08:00', 'end' => '13:00', 'capacity' => 50,
        ],
        [
            'title'        => 'Home Repair for Single Mothers',
            'venue'        => 'Taman perumahan rakyat, {city}',
            'description'  => 'Fix leaking roofs, broken doors, lights and taps in the homes of single mothers who cannot afford repairs. Volunteers with handyman or electrical skills are especially welcome.',
            'requirements' => 'Bring your own basic tools if you have them.',
            'skills'       => ['physical-labour', 'logistics-coordination'],
            'start' => '09:00', 'end' => '16:00', 'capacity' => 12,
        ],
    ],

    'elderly-care' => [
        [
            'title'        => 'Tea Time & Games with Senior Citizens',
            'venue'        => 'Rumah orang tua, {city}',
            'description'  => 'Spend an afternoon with residents of an old folks\' home: serve tea, play carrom and dam, sing old songs and listen to their stories. Many residents rarely receive visitors.',
            'requirements' => 'Dress modestly. Please stay home if you have flu symptoms.',
            'skills'       => ['social-care-empathy', 'event-management'],
            'start' => '15:00', 'end' => '17:30', 'capacity' => 15,
        ],
        [
            'title'        => 'Health Check Helpers for Seniors',
            'venue'        => 'Klinik komuniti, {city}',
            'description'  => 'Assist nurses at a free health screening for senior citizens: registration, guiding elderly visitors between stations and measuring blood pressure under supervision.',
            'requirements' => 'Medical or nursing students preferred, but not required.',
            'skills'       => ['first-aid-medical', 'administration-data-entry', 'social-care-empathy'],
            'start' => '08:30', 'end' => '13:00', 'capacity' => 12,
        ],
    ],

    'children-youth' => [
        [
            'title'        => 'Fun Learning Camp for Orphans',
            'venue'        => 'Children\'s home, {city}',
            'description'  => 'Run games, arts and crafts, and teamwork activities for children from an orphanage during a one-day camp. Energetic volunteers who enjoy working with kids will love this.',
            'requirements' => 'Child-safety briefing on the morning of the camp.',
            'skills'       => ['event-management', 'teaching-tutoring'],
            'start' => '09:00', 'end' => '17:00', 'capacity' => 20,
        ],
        [
            'title'        => 'Youth Mentoring: Career Talk Day',
            'venue'        => 'Secondary school hall, {city}',
            'description'  => 'Share your career journey and mentor teenagers from B40 families in small groups about further studies, scholarships and choosing a job.',
            'requirements' => 'Working adults or final-year university students.',
            'skills'       => ['teaching-tutoring', 'counselling'],
            'start' => '09:00', 'end' => '13:00', 'capacity' => 10,
        ],
    ],

    'health-medical' => [
        [
            'title'        => 'Blood Donation Drive Crew',
            'venue'        => 'Shopping mall concourse, {city}',
            'description'  => 'Help organise a blood donation campaign with the hospital blood bank: welcome donors, manage queues, register donors and serve refreshments after donation.',
            'requirements' => 'Friendly and comfortable talking to the public.',
            'skills'       => ['event-management', 'administration-data-entry', 'first-aid-medical'],
            'start' => '09:00', 'end' => '15:00', 'capacity' => 15,
        ],
        [
            'title'        => 'Mental Health Awareness Booth',
            'venue'        => 'Community centre, {city}',
            'description'  => 'Staff an awareness booth, share information about stress, anxiety and where to get help, and invite visitors to free counselling sessions run by professionals.',
            'requirements' => 'A short briefing on mental health first aid is provided.',
            'skills'       => ['counselling', 'social-media-marketing'],
            'start' => '10:00', 'end' => '18:00', 'capacity' => 8,
        ],
    ],

    'disaster-relief' => [
        [
            'title'        => 'Flood Relief Kit Packing',
            'venue'        => 'Relief warehouse, {city}',
            'description'  => 'Pack rice, canned food, hygiene items and blankets into relief kits for families evacuated during the monsoon floods. Fast, organised volunteers help us reach more families.',
            'requirements' => 'Able to lift boxes up to 10 kg.',
            'skills'       => ['logistics-coordination', 'physical-labour'],
            'start' => '08:00', 'end' => '17:00', 'capacity' => 60,
        ],
        [
            'title'        => 'Post-Flood House Cleanup',
            'venue'        => 'Flood-affected villages around {city}',
            'description'  => 'Help families clean mud out of their houses after the flood water recedes: shovelling mud, washing floors and moving damaged furniture. Drivers with 4x4 vehicles are needed to transport supplies.',
            'requirements' => 'Boots and gloves required. Tetanus vaccination recommended.',
            'skills'       => ['physical-labour', 'driving'],
            'start' => '08:00', 'end' => '16:00', 'capacity' => 40,
        ],
    ],

    'animal-welfare' => [
        [
            'title'        => 'Shelter Dog Walking & Care',
            'venue'        => 'Animal shelter, {city}',
            'description'  => 'Walk, bathe and play with rescued dogs, and help clean the kennels at our shelter. Perfect for animal lovers who don\'t mind getting a little dirty.',
            'requirements' => 'Not suitable for people afraid of dogs. Wear old clothes.',
            'skills'       => ['social-care-empathy', 'physical-labour'],
            'start' => '08:00', 'end' => '11:00', 'capacity' => 10,
        ],
        [
            'title'        => 'Adoption Day Photography',
            'venue'        => 'Pet adoption event, {city}',
            'description'  => 'Take attractive photos and short videos of cats and dogs for our adoption campaign, then help design posts and share them on social media so more animals find homes.',
            'requirements' => 'Bring your own camera or a good smartphone.',
            'skills'       => ['photography-videography', 'social-media-marketing', 'graphic-design'],
            'start' => '10:00', 'end' => '14:00', 'capacity' => 6,
        ],
    ],

    'food-security' => [
        [
            'title'        => 'Community Kitchen Meal Prep',
            'venue'        => 'Community kitchen, {city}',
            'description'  => 'Cook, portion and pack 500 hot meals for homeless people and low-income families. Experienced home cooks and kitchen helpers are both welcome.',
            'requirements' => 'Hairnets, aprons and gloves provided. Follow food-hygiene rules.',
            'skills'       => ['cooking-food-handling'],
            'start' => '07:00', 'end' => '12:00', 'capacity' => 25,
        ],
        [
            'title'        => 'Food Bank Delivery Drivers',
            'venue'        => 'Food bank hub, {city}',
            'description'  => 'Use your own car to deliver monthly food boxes to elderly and disabled people who cannot collect them. Routes are planned for you; petrol allowance is provided.',
            'requirements' => 'Valid driving licence and your own car.',
            'skills'       => ['driving', 'logistics-coordination'],
            'start' => '09:00', 'end' => '14:00', 'capacity' => 15,
        ],
    ],

    'arts-culture' => [
        [
            'title'        => 'Heritage Walk Guides',
            'venue'        => 'Old town heritage trail, {city}',
            'description'  => 'Guide visitors through the historic old town, sharing stories of its buildings, food and communities. A script and training session are provided. Speaking extra languages is a plus.',
            'requirements' => 'Confident speaking to groups of 10–15 people.',
            'skills'       => ['translation-languages', 'event-management'],
            'start' => '09:00', 'end' => '12:00', 'capacity' => 10,
        ],
        [
            'title'        => 'Community Mural Painting',
            'venue'        => 'Neighbourhood wall, {city}',
            'description'  => 'Paint a large mural with local children celebrating Malaysia\'s multicultural heritage. Artists, designers and complete beginners are all welcome.',
            'requirements' => 'Wear clothes you don\'t mind getting paint on.',
            'skills'       => ['graphic-design', 'event-management'],
            'start' => '08:30', 'end' => '16:00', 'capacity' => 25,
        ],
    ],

    'sports-events' => [
        [
            'title'        => 'Charity Run Marshals',
            'venue'        => 'City park start line, {city}',
            'description'  => 'Guide runners along the route, staff water stations and cheer participants at a charity run raising funds for children\'s healthcare. First-aid trained volunteers are needed at medical points.',
            'requirements' => 'Arrive 30 minutes early for briefing. Volunteer T-shirt provided.',
            'skills'       => ['event-management', 'first-aid-medical'],
            'start' => '05:30', 'end' => '10:30', 'capacity' => 60,
        ],
        [
            'title'        => 'Special Olympics Sports Day Helpers',
            'venue'        => 'Stadium mini, {city}',
            'description'  => 'Assist athletes with intellectual disabilities during a sports day: set up games, keep scores and encourage every participant. A patient, cheerful attitude matters most.',
            'requirements' => 'Short inclusion briefing before the event.',
            'skills'       => ['social-care-empathy', 'event-management'],
            'start' => '08:00', 'end' => '13:00', 'capacity' => 30,
        ],
    ],

    'technology-digital' => [
        [
            'title'        => 'Smartphone Classes for Seniors',
            'venue'        => 'Community library, {city}',
            'description'  => 'Teach senior citizens one-to-one how to make WhatsApp video calls, use e-wallets safely and spot online scam messages.',
            'requirements' => 'Comfortable with both Android and iPhone.',
            'skills'       => ['it-technical-support', 'teaching-tutoring'],
            'start' => '10:00', 'end' => '12:30', 'capacity' => 10,
        ],
        [
            'title'        => 'Website Makeover for Small Charities',
            'venue'        => 'Co-working space, {city}',
            'description'  => 'Help small charities refresh their websites, design social media templates and set up online donation pages in a one-day digital sprint.',
            'requirements' => 'Bring your own laptop.',
            'skills'       => ['it-technical-support', 'graphic-design', 'social-media-marketing'],
            'start' => '09:00', 'end' => '17:00', 'capacity' => 8,
        ],
    ],

];