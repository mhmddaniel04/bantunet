<?php

/*
|--------------------------------------------------------------------------
| Sample NPOs for testing and demonstration
|--------------------------------------------------------------------------
|
| Fictional organisations with ROS-style numbers. You may replace name,
| ros, state, address and description with the public ROS (MySociety)
| data you collected. Keep the emails on @sample.bantunet.local so no
| real organisation receives test emails.
|
| focus  = category slugs this NPO runs initiatives for (2 each)
| cities = towns used for its initiatives (must be in its state)
|
| Every account's password is: password123
|
*/

return [
    [
        'name'        => 'Persatuan Hijau Lestari Selangor',
        'ros'         => 'PPM-011-10-12032016',
        'email'       => 'hijau.lestari@sample.bantunet.local',
        'phone'       => '03-5512 3401',
        'state'       => 'Selangor',
        'address'     => 'No. 12, Jalan Plumbum 7/102, Seksyen 7, 40000 Shah Alam',
        'description' => 'An environmental group restoring mangroves, rivers and urban green spaces across the Klang Valley with community volunteers.',
        'focus'       => ['environment', 'animal-welfare'],
        'cities'      => ['Shah Alam', 'Klang', 'Kuala Selangor', 'Petaling Jaya'],
    ],
    [
        'name'        => 'Pertubuhan Pendidikan Harapan Kita Pulau Pinang',
        'ros'         => 'PPM-024-07-08112018',
        'email'       => 'harapan.kita@sample.bantunet.local',
        'phone'       => '04-226 8812',
        'state'       => 'Pulau Pinang',
        'address'     => '45, Lebuh Carnarvon, 10100 George Town',
        'description' => 'Free tuition, reading programmes and mentoring for children from low-income families in Penang.',
        'focus'       => ['education', 'children-youth'],
        'cities'      => ['George Town', 'Butterworth', 'Bayan Lepas'],
    ],
    [
        'name'        => 'Persatuan Kebajikan Warga Emas Perak',
        'ros'         => 'PPM-008-08-21052015',
        'email'       => 'warga.emas@sample.bantunet.local',
        'phone'       => '05-254 9031',
        'state'       => 'Perak',
        'address'     => '18, Jalan Raja Musa Aziz, 30300 Ipoh',
        'description' => 'Caring for senior citizens through home visits, day activities and free health screenings in Perak.',
        'focus'       => ['elderly-care', 'health-medical'],
        'cities'      => ['Ipoh', 'Taiping', 'Teluk Intan'],
    ],
    [
        'name'        => 'Yayasan Bantuan Bencana Pantai Timur',
        'ros'         => 'PPM-031-03-15012021',
        'email'       => 'bantuan.bencana@sample.bantunet.local',
        'phone'       => '09-747 2210',
        'state'       => 'Kelantan',
        'address'     => 'Lot 221, Jalan Sultan Yahya Petra, 15200 Kota Bharu',
        'description' => 'Emergency flood relief, food aid and recovery support for families on the east coast.',
        'focus'       => ['disaster-relief', 'food-security'],
        'cities'      => ['Kota Bharu', 'Pasir Mas', 'Tumpat'],
    ],
    [
        'name'        => 'Kelab Sukarelawan Belia Johor',
        'ros'         => 'PPM-017-01-02092019',
        'email'       => 'belia.johor@sample.bantunet.local',
        'phone'       => '07-223 5567',
        'state'       => 'Johor',
        'address'     => '3, Jalan Wong Ah Fook, 80000 Johor Bahru',
        'description' => 'A youth volunteer club running sports events, gotong-royong and neighbourhood programmes in Johor.',
        'focus'       => ['sports-events', 'community-care'],
        'cities'      => ['Johor Bahru', 'Batu Pahat', 'Muar'],
    ],
    [
        'name'        => 'Persatuan Penyayang Haiwan Kuching',
        'ros'         => 'PPM-042-13-30062020',
        'email'       => 'haiwan.kuching@sample.bantunet.local',
        'phone'       => '082-245 118',
        'state'       => 'Sarawak',
        'address'     => '27, Jalan Padungan, 93100 Kuching',
        'description' => 'Rescuing, treating and rehoming stray cats and dogs, and protecting local wildlife habitats in Sarawak.',
        'focus'       => ['animal-welfare', 'environment'],
        'cities'      => ['Kuching', 'Miri', 'Sibu'],
    ],
    [
        'name'        => 'Dapur Komuniti Kuala Lumpur',
        'ros'         => 'PPM-005-14-11042017',
        'email'       => 'dapur.komuniti@sample.bantunet.local',
        'phone'       => '03-2698 4410',
        'state'       => 'W.P. Kuala Lumpur',
        'address'     => '9, Jalan Tun H S Lee, 50000 Kuala Lumpur',
        'description' => 'Community kitchens and food banks feeding homeless people and low-income families in Kuala Lumpur.',
        'focus'       => ['food-security', 'community-care'],
        'cities'      => ['Kuala Lumpur', 'Chow Kit', 'Cheras'],
    ],
    [
        'name'        => 'Pertubuhan Celik Digital Malaysia',
        'ros'         => 'PPM-050-16-19082022',
        'email'       => 'celik.digital@sample.bantunet.local',
        'phone'       => '03-8888 2301',
        'state'       => 'W.P. Putrajaya',
        'address'     => 'Presint 9, 62250 Putrajaya',
        'description' => 'Closing the digital divide through digital literacy classes and tech help for charities.',
        'focus'       => ['technology-digital', 'education'],
        'cities'      => ['Putrajaya'],
    ],
    [
        'name'        => 'Persatuan Seni dan Warisan Melaka',
        'ros'         => 'PPM-019-04-25022018',
        'email'       => 'seni.warisan@sample.bantunet.local',
        'phone'       => '06-282 7716',
        'state'       => 'Melaka',
        'address'     => '58, Jalan Hang Jebat, 75200 Melaka',
        'description' => 'Preserving Melaka\'s heritage through guided walks, community art and cultural programmes for young people.',
        'focus'       => ['arts-culture', 'children-youth'],
        'cities'      => ['Melaka City', 'Alor Gajah'],
    ],
    [
        'name'        => 'Persatuan Kesihatan Komuniti Sabah',
        'ros'         => 'PPM-037-12-07102019',
        'email'       => 'kesihatan.sabah@sample.bantunet.local',
        'phone'       => '088-233 904',
        'state'       => 'Sabah',
        'address'     => 'Lot 6, Jalan Gaya, 88000 Kota Kinabalu',
        'description' => 'Health awareness, blood donation drives and community support for rural families in Sabah.',
        'focus'       => ['health-medical', 'community-care'],
        'cities'      => ['Kota Kinabalu', 'Sandakan', 'Tawau'],
    ],
];