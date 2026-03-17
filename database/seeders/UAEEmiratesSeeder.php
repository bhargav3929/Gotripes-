<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Emirates;
use App\Models\UAEActivity;
use App\Models\UAEActivityDetail;
use Illuminate\Support\Facades\DB;

class UAEEmiratesSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data to avoid duplicates
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }
        
        UAEActivityDetail::truncate();
        UAEActivity::truncate();
        Emirates::truncate();
        
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        $emiratesData = [
            [
                'name' => 'Dubai',
                'description' => 'Dubai is a city and emirate in the United Arab Emirates luxury shopping, ultramodern architecture and a lively nightlife scene.',
                'image' => 'assets/emirates/dubai.jpg',
                'activities' => [
                    [
                        'name' => 'Burj Khalifa Top View',
                        'location' => 'Downtown Dubai',
                        'price' => 179.00,
                        'image' => 'assets/activities/1762343053_QAbWrA1hQD.webp',
                        'overview' => 'Experience the world\'s tallest building with breathtaking views from the 124th and 125th floors.',
                    ],
                    [
                        'name' => 'Desert Safari with BBQ Dinner',
                        'location' => 'Dubai Desert',
                        'price' => 150.00,
                        'image' => 'assets/activities/1762343123_zx77m9stOi.webp',
                        'overview' => 'An adventurous journey through the golden dunes of Dubai followed by a traditional Arabic dinner.',
                    ],
                    [
                        'name' => 'Dubai Marina Dinner Cruise',
                        'location' => 'Dubai Marina',
                        'price' => 120.00,
                        'image' => 'assets/activities/1762344006_UCCWfivdmW.png',
                        'overview' => 'Enjoy a romantic dinner on a traditional wooden dhow while cruising through the stunning Dubai Marina.',
                    ]
                ]
            ],
            [
                'name' => 'Abu Dhabi',
                'description' => 'The capital of the UAE, Abu Dhabi is known for its rich culture, stunning mosques, and high-end island resorts.',
                'image' => 'assets/emirates/abu-dhabi.jpg',
                'activities' => [
                    [
                        'name' => 'Sheikh Zayed Grand Mosque Tour',
                        'location' => 'Abu Dhabi City',
                        'price' => 50.00,
                        'image' => 'assets/activities/1762344279_O1CARAFXLX.jpg',
                        'overview' => 'Visit one of the world\'s largest and most beautiful mosques, a masterpiece of Islamic architecture.',
                    ],
                    [
                        'name' => 'Ferrari World Abu Dhabi',
                        'location' => 'Yas Island',
                        'price' => 310.00,
                        'image' => 'assets/activities/1762344407_QvRm9GPEFY.jpg',
                        'overview' => 'The first Ferrari-branded theme park in the world, featuring the fastest rollercoaster.',
                    ]
                ]
            ],
            [
                'name' => 'Sharjah',
                'description' => 'Known as the Cultural Capital of the UAE, Sharjah is home to numerous museums, heritage sites, and traditional souks.',
                'image' => 'assets/emirates/sharjah.jpg',
                'activities' => [
                    [
                        'name' => 'Sharjah Art Museum Visit',
                        'location' => 'Arts Area, Sharjah',
                        'price' => 30.00,
                        'image' => 'assets/activities/1762344766_iZy8U3t1vX.jpg',
                        'overview' => 'Explore the rich artistic heritage of the region with an extensive collection of Arabic art.',
                    ]
                ]
            ],
            [
                'name' => 'Ajman',
                'description' => 'The smallest emirate, Ajman offers beautiful beaches, a rich maritime history, and a peaceful atmosphere.',
                'image' => 'assets/emirates/ajman.jpg',
                'activities' => [
                    [
                        'name' => 'Ajman Museum Experience',
                        'location' => 'Ajman City',
                        'price' => 20.00,
                        'image' => 'assets/activities/1762344876_oQtHQfzyi1.jpg',
                        'overview' => 'Discover the history of Ajman in this beautifully restored fort that served as the ruler\'s palace.',
                    ]
                ]
            ],
            [
                'name' => 'Fujairah',
                'description' => 'The only emirate on the Gulf of Oman, Fujairah is famous for its mountains, diving spots, and historic forts.',
                'image' => 'assets/emirates/fujairah.jpg',
                'activities' => [
                    [
                        'name' => 'Snoopy Island Snorkeling',
                        'location' => 'Al Aqah, Fujairah',
                        'price' => 150.00,
                        'image' => 'assets/activities/1762345119_IFUAGZDkvz.jpg',
                        'overview' => 'Dive into crystal clear waters and explore the vibrant marine life around the famous Snoopy Island.',
                    ]
                ]
            ],
            [
                'name' => 'Ras Al Khaimah',
                'description' => 'RAK is known for its diverse landscapes, from desert dunes to the highest mountains in the UAE.',
                'image' => 'assets/emirates/ras-al-khaimah.jpg',
                'activities' => [
                    [
                        'name' => 'Jebel Jais Flight Zipline',
                        'location' => 'Jebel Jais',
                        'price' => 350.00,
                        'image' => 'assets/activities/1762345169_8FjnOiOQiw.jpg',
                        'overview' => 'Soar through the air on the world\'s longest zipline, located at the highest point in the UAE.',
                    ]
                ]
            ],
            [
                'name' => 'Umm Al Quwain',
                'description' => 'A quiet emirate offering traditional experiences, mangrove forests, and coastal adventures.',
                'image' => 'assets/emirates/umm-al-quwain.jpg',
                'activities' => [
                    [
                        'name' => 'Dreamland Aqua Park',
                        'location' => 'Umm Al Quwain',
                        'price' => 160.00,
                        'image' => 'assets/activities/1762345268_PAbISv6fds.jpg',
                        'overview' => 'One of the largest water parks in the region, offering a wide range of water slides and attractions.',
                    ]
                ]
            ],
        ];

        foreach ($emiratesData as $data) {
            $emirate = Emirates::create([
                'emiratesName' => $data['name'],
                'emiratesDescription' => $data['description'],
                'emiratesImage' => $data['image'],
                'isActive' => 1,
                'createdBy' => 'Antigravity',
                'createdDate' => now(),
            ]);

            foreach ($data['activities'] as $actData) {
                $activity = UAEActivity::create([
                    'activityName' => $actData['name'],
                    'activityLocation' => $actData['location'],
                    'activityPrice' => $actData['price'],
                    'activityImage' => $actData['image'],
                    'activityCurrency' => '$',
                    'emiratesID' => $emirate->emiratesID,
                    'isActive' => 1,
                    'createdBy' => 'Antigravity',
                    'createdDate' => now(),
                ]);

                UAEActivityDetail::create([
                    'activityID' => $activity->activityID,
                    'detailsOverview' => $actData['overview'],
                    'detailsIminfo' => 'Standard important information for ' . $actData['name'],
                    'detailsHighlights' => 'Experience the best of ' . $actData['name'],
                    'isActive' => 1,
                    'createdBy' => 'Antigravity',
                    'createdDate' => now(),
                ]);
            }
        }
    }
}
