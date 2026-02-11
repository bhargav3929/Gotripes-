<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'total' => 0,
                'pages' => [],
                'activities' => [],
                'services' => [],
                'visas' => [],
                'emirates' => [],
                'countries' => []
            ]);
        }

        $query = strtolower($query);

        $pages = $this->searchPages($query);
        $activities = $this->searchActivities($query);
        $visas = $this->searchVisas($query);
        $emirates = $this->searchEmirates($query);
        $countries = $this->searchCountries($query);
        $services = $this->searchServices($query);

        $total = count($pages) + count($activities) + count($services) + count($visas) + count($emirates) + count($countries);

        return response()->json([
            'total' => $total,
            'pages' => $pages,
            'activities' => $activities,
            'services' => $services,
            'visas' => $visas,
            'emirates' => $emirates,
            'countries' => $countries
        ]);
    }

    private function searchPages($query)
    {
        $allPages = [
            ['title' => 'Home', 'url' => '/', 'description' => 'Explore our premium travel services and packages', 'keywords' => 'home main landing gotrips'],
            ['title' => 'Activities', 'url' => '/activities', 'description' => 'Browse exciting activities and experiences in Dubai and UAE', 'keywords' => 'activities things to do experiences adventures desert safari burj khalifa water sports'],
            ['title' => 'UAE Visa Services', 'url' => '/uaevisa', 'description' => 'Apply for UAE tourist visa, transit visa, and visa services', 'keywords' => 'visa uae dubai tourist transit visa services apply passport'],
            ['title' => 'Tour Packages', 'url' => '/countriestour', 'description' => 'Explore 195+ countries with world-class tour packages', 'keywords' => 'tour packages trips travel international vacation holidays countries world'],
            ['title' => 'Hajj & Umrah Services', 'url' => '/hajj-umrah', 'description' => 'Sacred pilgrimage packages with luxury accommodation', 'keywords' => 'hajj umrah pilgrimage mecca medina islamic religious saudi'],
            ['title' => 'Our Services', 'url' => '/our-services', 'description' => 'Complete list of all our travel and business services', 'keywords' => 'services offerings solutions travel business'],
            ['title' => 'Shop Online', 'url' => '/shopnow', 'description' => 'Book activities, tours, and services online', 'keywords' => 'shop online booking ecommerce buy store'],
            ['title' => 'Pay Online', 'url' => '/payonline', 'description' => 'Secure online payment portal for bookings', 'keywords' => 'pay payment online transaction secure ccavenue'],
            ['title' => 'Careers', 'url' => '/lookingforajob', 'description' => 'Job openings and career opportunities in UAE', 'keywords' => 'careers jobs employment opportunities work hiring recruitment uae'],
            ['title' => 'Contact Us', 'url' => '/contact-us', 'description' => 'Get in touch with our team for support', 'keywords' => 'contact support help email phone address location office'],
            ['title' => 'Our Story', 'url' => '/ourstory', 'description' => 'Learn about Ayn Al Amir Tourism and our journey', 'keywords' => 'about us story history company ayn al amir'],
            ['title' => 'Dubai Global Village', 'url' => '/dubai-global-village', 'description' => 'Book tickets for Dubai Global Village', 'keywords' => 'global village dubai entertainment festival culture tickets booking'],
            ['title' => 'Lotus Cruise Dubai', 'url' => '/lotus-cruise-dubai', 'description' => 'Premium dinner cruise experience on Dubai Creek', 'keywords' => 'lotus cruise dubai creek dinner boat yacht buffet'],
            ['title' => 'Privacy Policy', 'url' => '/privacypolicy', 'description' => 'Our privacy policy and data protection', 'keywords' => 'privacy policy data protection'],
            ['title' => 'Terms & Conditions', 'url' => '/termsandconditions', 'description' => 'Terms and conditions of service', 'keywords' => 'terms conditions legal policy rules'],
        ];

        $results = [];
        foreach ($allPages as $page) {
            $searchText = strtolower($page['title'] . ' ' . $page['description'] . ' ' . $page['keywords']);
            if (str_contains($searchText, $query)) {
                $results[] = [
                    'title' => $page['title'],
                    'url' => $page['url'],
                    'description' => $page['description'],
                    'type' => 'page',
                    'icon' => 'bi-globe'
                ];
            }
        }
        return array_slice($results, 0, 5);
    }

    private function searchActivities($query)
    {
        $activities = DB::table('tbl_uaeactivities')
            ->where('isActive', true)
            ->where(function($q) use ($query) {
                $q->where(DB::raw('LOWER(activityName)'), 'like', '%' . $query . '%')
                  ->orWhere(DB::raw('LOWER(activityLocation)'), 'like', '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        $results = [];
        foreach ($activities as $activity) {
            $results[] = [
                'title' => $activity->activityName,
                'url' => '/activities?id=' . $activity->activityID,
                'description' => $activity->activityLocation,
                'type' => 'activity',
                'icon' => 'bi-geo-alt'
            ];
        }
        return $results;
    }

    private function searchEmirates($query)
    {
        $emirates = DB::table('tbl_emirates')
            ->where(DB::raw('LOWER(emiratesName)'), 'like', '%' . $query . '%')
            ->limit(3)
            ->get();

        $results = [];
        foreach ($emirates as $emirate) {
            $results[] = [
                'title' => $emirate->emiratesName . ' - Explore',
                'url' => '/activities?emiratesID=' . $emirate->emiratesID,
                'description' => 'Explore popular activities and attractions in ' . $emirate->emiratesName,
                'type' => 'emirate',
                'icon' => 'bi-building'
            ];
        }
        return $results;
    }

    private function searchVisas($query)
    {
        $visas = DB::table('uae_visa_master')
            ->where('isActive', true)
            ->where(function($q) use ($query) {
                $q->where(DB::raw('LOWER(UAEVisaDuration)'), 'like', '%' . $query . '%')
                  ->orWhere('UAEVisaDuration', 'like', '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        $results = [];
        foreach ($visas as $visa) {
            $results[] = [
                'title' => $visa->UAEVisaDuration . ' UAE Visa',
                'url' => '/uaevisa',
                'description' => 'Premium UAE visa processing - AED ' . number_format($visa->UAEVPrice, 2),
                'type' => 'visa',
                'icon' => 'bi-passport'
            ];
        }
        return $results;
    }

    private function searchCountries($query)
    {
        $countries = [
            ['title' => 'Egypt Tour Package', 'description' => 'Explore the pyramids, Nile cruises, and ancient wonders', 'keywords' => 'egypt cairo pyramid nile alexandria'],
            ['title' => 'Bahrain Tour Package', 'description' => 'Discover the pearl of the Gulf', 'keywords' => 'bahrain manama gulf pearl'],
            ['title' => 'Oman Tour Package', 'description' => 'Mountains, wadis, and pristine beaches', 'keywords' => 'oman muscat nizwa wadi salalah'],
            ['title' => 'Saudi Arabia Tour Package', 'description' => 'Heritage sites, modern cities, and holy lands', 'keywords' => 'saudi arabia riyadh jeddah ksa neom'],
            ['title' => 'South Africa Tour Package', 'description' => 'Safari adventures and Table Mountain', 'keywords' => 'south africa cape town safari johannesburg durban'],
            ['title' => 'Turkey Tour Package', 'description' => 'Istanbul, Cappadocia, and Mediterranean coast', 'keywords' => 'turkey istanbul cappadocia antalya turkish'],
            ['title' => 'Georgia Tour Package', 'description' => 'Tbilisi, wine country, and Caucasus mountains', 'keywords' => 'georgia tbilisi caucasus batumi'],
            ['title' => 'Azerbaijan Tour Package', 'description' => 'Baku, fire temple, and Caspian shores', 'keywords' => 'azerbaijan baku caspian flame towers'],
            ['title' => 'Thailand Tour Package', 'description' => 'Bangkok temples, Phuket beaches, and street food', 'keywords' => 'thailand bangkok phuket pattaya chiang mai'],
            ['title' => 'Malaysia Tour Package', 'description' => 'Kuala Lumpur, Langkawi, and tropical adventures', 'keywords' => 'malaysia kuala lumpur langkawi petronas'],
        ];

        $results = [];
        foreach ($countries as $country) {
            $searchText = strtolower($country['title'] . ' ' . $country['description'] . ' ' . $country['keywords']);
            if (str_contains($searchText, $query)) {
                $results[] = [
                    'title' => $country['title'],
                    'url' => '/countriestour',
                    'description' => $country['description'],
                    'type' => 'country',
                    'icon' => 'bi-airplane'
                ];
            }
        }
        return array_slice($results, 0, 3);
    }

    private function searchServices($query)
    {
        $services = [
            ['title' => 'Activities', 'url' => '/activities', 'keywords' => 'activities things to do experiences adventures'],
            ['title' => 'City Tour', 'url' => '/our-services', 'keywords' => 'city tour sightseeing guided'],
            ['title' => 'Pick & Drop Guests', 'url' => '/our-services', 'keywords' => 'pick drop airport transfer transportation shuttle'],
            ['title' => 'Liwa Guests Assistance', 'url' => '/our-services', 'keywords' => 'liwa guests assistance western region al dhafra'],
            ['title' => 'World Travel eSIM', 'url' => '/our-services', 'keywords' => 'esim sim card travel data international roaming mobile'],
            ['title' => 'Trips Organising', 'url' => '/our-services', 'keywords' => 'trips organising planning group corporate events'],
            ['title' => 'Hotel Bookings', 'url' => '/our-services', 'keywords' => 'hotel bookings accommodation resort stay booking'],
            ['title' => 'Business WhatsApp Integration', 'url' => '/our-services', 'keywords' => 'whatsapp business integration messaging communication'],
            ['title' => 'Website Development', 'url' => '/our-services', 'keywords' => 'website development web design digital online'],
            ['title' => 'Visa Services', 'url' => '/uaevisa', 'keywords' => 'visa services processing uae tourist transit'],
            ['title' => 'Hajj & Umrah Services', 'url' => '/hajj-umrah', 'keywords' => 'hajj umrah pilgrimage mecca medina'],
            ['title' => 'Car Rentals', 'url' => '/our-services', 'keywords' => 'car rental hire vehicle drive rent'],
            ['title' => 'Recruitment Services', 'url' => '/our-services', 'keywords' => 'recruitment hiring staffing manpower jobs'],
            ['title' => 'Internships', 'url' => '/our-services', 'keywords' => 'internship training student placement'],
            ['title' => 'World Class Tour Packages', 'url' => '/countriestour', 'keywords' => 'tour packages international travel world class'],
            ['title' => 'Travel Agency Workflow Setup', 'url' => '/our-services', 'keywords' => 'travel agency workflow setup automation system'],
            ['title' => 'New Business Setup Assistance', 'url' => '/our-services', 'keywords' => 'business setup company formation trade license'],
            ['title' => 'GDS Support', 'url' => '/our-services', 'keywords' => 'gds amadeus sabre galileo booking system'],
            ['title' => 'Business Consultants', 'url' => '/our-services', 'keywords' => 'business consultant advisory strategy'],
            ['title' => 'AI, AR, VR Integration', 'url' => '/our-services', 'keywords' => 'ai artificial intelligence ar vr virtual reality augmented'],
            ['title' => 'Study Abroad', 'url' => '/our-services', 'keywords' => 'study abroad education overseas university college'],
        ];

        $results = [];
        foreach ($services as $service) {
            $searchText = strtolower($service['title'] . ' ' . $service['keywords']);
            if (str_contains($searchText, $query)) {
                $results[] = [
                    'title' => $service['title'],
                    'url' => $service['url'],
                    'description' => 'Professional ' . strtolower($service['title']) . ' services',
                    'type' => 'service',
                    'icon' => 'bi-briefcase'
                ];
            }
        }
        return array_slice($results, 0, 4);
    }
}
