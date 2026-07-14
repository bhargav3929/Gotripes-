<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SaudiVisaType;
use App\Models\UmrahPackage;
use App\Models\UmrahDeparture;

class UmrahModuleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Saudi Visa Types
        $visaTypes = [
            ['name' => 'Tourist Visa', 'price' => 450.00],
            ['name' => 'Umrah Visa', 'price' => 380.00],
            ['name' => '1-Year Multiple Entry', 'price' => 750.00],
        ];

        foreach ($visaTypes as $vt) {
            SaudiVisaType::updateOrCreate(['name' => $vt['name']], [
                'company_id' => 1, // Default tenant company ID
                'price' => $vt['price'],
                'isActive' => true,
            ]);
        }

        // 2. Seed Umrah Packages
        $packages = [
            [
                'title' => 'Economy Bus Umrah Package',
                'category' => 'economy',
                'price' => 899.00,
                'currency' => 'AED',
                'duration' => '10 Days Stay',
                'transport' => 'Standard Coach Service',
                'hotels' => '3-Star Makkah & Medina Hotels',
                'inclusions' => ['Umrah Visa processing', 'Round-trip bus tickets', 'Hotel accommodation', 'Group assistance'],
                'exclusions' => ['Food & daily meals', 'Personal shopping and expenses', 'Excess luggage charges'],
                'itinerary' => [
                    'Day 1: Departure from Dubai to Medina via AC Coach.',
                    'Day 2: Arrival in Medina, check-in, rest and offer prayers at Al-Masjid an-Nabawi.',
                    'Day 3: Group visits (Ziyarat) to historic Medina sites.',
                    'Day 4: Rest day in Medina for individual prayers.',
                    'Day 5: Check-out Medina, wear Ihram, proceed to Makkah. Perform Umrah.',
                    'Day 6: Stay in Makkah for individual worship.',
                    'Day 7: Rest and prayers in Makkah.',
                    'Day 8: Ziyarat tour to historical sites in Makkah.',
                    'Day 9: Stay in Makkah, Farewell Tawaf, check-out and board coach back.',
                    'Day 10: Arrival back in Dubai, UAE.'
                ],
                'features' => ['Super affordable pricing', 'AC coach roundtrip', '3-Star accommodations', 'Basic guidance included'],
                'image' => 'assets/index_files/umrah_1.png',
                'isFeatured' => false,
                'sortOrder' => 1,
            ],
            [
                'title' => 'Standard Bus Umrah Package',
                'category' => 'standard',
                'price' => 1299.00,
                'currency' => 'AED',
                'duration' => '10 Days Stay',
                'transport' => 'Comfort AC Coach',
                'hotels' => '4-Star Hotels in Makkah & Medina',
                'inclusions' => ['Umrah Visa included', 'Comfort Bus transport', '4-Star Hotel stay', 'Breakfast included', 'Full Ziyarat trips'],
                'exclusions' => ['Lunch & Dinner meals', 'Personal shopping'],
                'itinerary' => [
                    'Day 1: Departure from Dubai to Medina.',
                    'Day 2: Check-in Medina 4-star hotel, rest.',
                    'Day 3: Guided Medina Ziyarat.',
                    'Day 4: Daily worship at Prophet\'s Mosque.',
                    'Day 5: Leave for Makkah in Ihram. Perform Umrah.',
                    'Day 6-8: Worship at Masjid al-Haram, guided Makkah Ziyarat.',
                    'Day 9: Farewell Tawaf, return boarding.',
                    'Day 10: Arrival in UAE.'
                ],
                'features' => ['4-Star comfortable rooms', 'Breakfast included', 'Medina & Makkah Ziyarat tours', 'Comfortable AC coach'],
                'image' => 'assets/index_files/umrah_2.png',
                'isFeatured' => true,
                'sortOrder' => 2,
            ],
            [
                'title' => 'Premium Bus Umrah Package',
                'category' => 'premium',
                'price' => 1799.00,
                'currency' => 'AED',
                'duration' => '10 Days Stay',
                'transport' => 'Luxury Pullman Coach',
                'hotels' => '5-Star Hotel in Medina, 4-Star in Makkah (Closer to Haram)',
                'inclusions' => ['Premium Visa handling', 'Luxury Pullman seating', '5-Star Medina stay', 'Half-board meals (Breakfast & Dinner)'],
                'exclusions' => ['Personal laundry', 'Extra room service charges'],
                'itinerary' => [
                    'Day 1: Board luxury Pullman coach to Medina.',
                    'Day 2: Check-in 5-star hotel in Medina near Haram.',
                    'Day 3: Extensive guided historical tour in Medina.',
                    'Day 4: Rest and prayers in Medina.',
                    'Day 5: Ihram dressing, travel to Makkah. Umrah performance.',
                    'Day 6-8: Stay in premium Makkah hotel, prayers at Haram.',
                    'Day 9: Check-out and return journey.',
                    'Day 10: Return in Dubai.'
                ],
                'features' => ['5-Star luxury hotel in Medina', 'Premium seating coach', 'Half-board delicious buffet meals', 'Proximity to Haram'],
                'image' => 'assets/index_files/umrah_3.png',
                'isFeatured' => true,
                'sortOrder' => 3,
            ],
            [
                'title' => 'VIP Bus Umrah Package',
                'category' => 'vip',
                'price' => 2499.00,
                'currency' => 'AED',
                'duration' => '10 Days Stay',
                'transport' => 'VIP Sleeper Coach',
                'hotels' => '5-Star Luxury Makkah & Medina Hotels directly facing Haram',
                'inclusions' => ['Priority VIP Visa processing', 'VIP Sleeper coach with personal screen', '5-Star luxury Haram-view hotel stay', 'Full board gourmet catering', 'Private guide & assistance'],
                'exclusions' => ['Private room service orders'],
                'itinerary' => [
                    'Day 1: Travel in VIP sleeper coach.',
                    'Day 2: VIP check-in 5-Star Haram-view hotel Medina.',
                    'Day 3: Private Medina historical tour with VIP guide.',
                    'Day 4: Spiritual day in Medina.',
                    'Day 5: Travel to Makkah, perform VIP Umrah.',
                    'Day 6-8: 5-Star luxury stay Makkah directly on Haram courtyard, VIP Ziyarat.',
                    'Day 9: Farewell Tawaf, VIP transfer back.',
                    'Day 10: Arrival in Dubai.'
                ],
                'features' => ['VIP sleeper seats coach', '5-Star Haram front courtyard hotels', 'Full board gourmet buffet meals', 'Private dedicated guide'],
                'image' => 'assets/index_files/umrah_4.png',
                'isFeatured' => false,
                'sortOrder' => 4,
            ]
        ];

        // Ensure directories exist
        $publicAssetsDir = public_path('assets/index_files');
        if (!\Illuminate\Support\Facades\File::exists($publicAssetsDir)) {
            \Illuminate\Support\Facades\File::makeDirectory($publicAssetsDir, 0755, true, true);
        }

        // Copy default images if needed, or make sure paths exist
        foreach ($packages as $pkg) {
            $createdPkg = UmrahPackage::updateOrCreate(['title' => $pkg['title']], [
                'company_id' => 1,
                'category' => $pkg['category'],
                'price' => $pkg['price'],
                'currency' => $pkg['currency'],
                'duration' => $pkg['duration'],
                'transport' => $pkg['transport'],
                'hotels' => $pkg['hotels'],
                'inclusions' => $pkg['inclusions'],
                'exclusions' => $pkg['exclusions'],
                'itinerary' => $pkg['itinerary'],
                'features' => $pkg['features'],
                'image' => $pkg['image'],
                'isFeatured' => $pkg['isFeatured'],
                'sortOrder' => $pkg['sortOrder'],
                'isActive' => true,
                'createdBy' => 'Seeder',
                'createdDate' => now(),
            ]);

            // Add departure dates (Wednesday departures only, upcoming weeks)
            // Let's generate 4 upcoming Wednesdays (starting from week + 1 to avoid 5-day lock)
            for ($w = 1; $w <= 5; $w++) {
                $daysToNextWed = (3 - date('w') + 7) % 7;
                if ($daysToNextWed == 0) {
                    $daysToNextWed = 7;
                }
                $offsetDays = $daysToNextWed + ($w * 7);
                $wednesdayDate = date('Y-m-d', strtotime("+{$offsetDays} days"));

                UmrahDeparture::updateOrCreate([
                    'umrah_package_id' => $createdPkg->id,
                    'departure_date' => $wednesdayDate,
                ], [
                    'seats_available' => 45,
                    'seats_booked' => rand(0, 10),
                    'status' => 'available',
                ]);
            }
        }
    }
}
