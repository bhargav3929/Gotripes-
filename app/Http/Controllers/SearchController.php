<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $services = []; // Initialize services
        
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
        
        // Define searchable pages
        $pages = $this->searchPages($query);
        
        // Search activities from database
        $activities = $this->searchActivities($query);
        
        // Search visas
        $visas = $this->searchVisas($query);

        // Search Emirates
        $emirates = $this->searchEmirates($query);

        // Search Countries (Hardcoded Popular)
        $countries = $this->searchCountries($query);

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

    private function searchEmirates($query)
    {
        $emirates = DB::table('tbl_emirates')
            ->where(DB::raw('LOWER(emirateName)'), 'like', '%' . $query . '%')
            ->limit(3)
            ->get();
        
        $results = [];
        foreach ($emirates as $emirate) {
            $results[] = [
                'title' => $emirate->emirateName . ' - Explore',
                'url' => '/Activities?emirate=' . urlencode($emirate->emirateName),
                'description' => 'Explore popular activities and attractions in ' . $emirate->emirateName
            ];
        }
        return $results;
    }
    
    // ... searchPages and other methods ...
    private function searchPages($query)
    {
        $allPages = [
            ['title' => 'Home', 'url' => '/', 'description' => 'Explore our premium travel services and packages', 'keywords' => 'home main landing'],
            ['title' => 'Activities', 'url' => '/Activities', 'description' => 'Browse exciting activities and experiences in Dubai and UAE', 'keywords' => 'activities things to do experiences adventures'],
            ['title' => 'UAE Visa Services', 'url' => '/uaevisa', 'description' => 'Apply for UAE tourist visa, transit visa, and visa services', 'keywords' => 'visa uae dubai tourist transit visa services'],
            ['title' => 'Tour Packages', 'url' => '/countriestour', 'description' => 'World-class tour packages and international trips', 'keywords' => 'tour packages trips travel international vacation holidays egypt bahrain oman saudi arabia south africa'],
            ['title' => 'Hajj & Umrah Services', 'url' => '/hajj-umrah', 'description' => 'Hajj and Umrah pilgrimage packages and services', 'keywords' => 'hajj umrah pilgrimage mecca medina islamic religious'],
            ['title' => 'Our Services', 'url' => '/our-services', 'description' => 'Complete list of all our travel and business services', 'keywords' => 'services offerings solutions'],
            ['title' => 'Shop Online', 'url' => '/shopnow', 'description' => 'Book activities, tours, and services online', 'keywords' => 'shop online booking ecommerce buy'],
            ['title' => 'Pay Online', 'url' => '/payonline', 'description' => 'Secure online payment portal for bookings', 'keywords' => 'pay payment online transaction secure'],
            ['title' => 'Careers', 'url' => '/lookingforajob', 'description' => 'Job openings and career opportunities', 'keywords' => 'careers jobs employment opportunities work hiring recruitment'],
            ['title' => 'Contact Us', 'url' => '/contact-us', 'description' => 'Get in touch with our team', 'keywords' => 'contact support help email phone address'],
            ['title' => 'Our Story', 'url' => '/ourstory', 'description' => 'Learn about our journey and company history', 'keywords' => 'about us story history company'],
        ];
        
        $results = [];
        foreach ($allPages as $page) {
            $searchText = strtolower($page['title'] . ' ' . $page['description'] . ' ' . $page['keywords']);
            if (str_contains($searchText, $query)) {
                $results[] = [
                    'title' => $page['title'],
                    'url' => $page['url'],
                    'description' => $page['description']
                ];
            }
        }
        return array_slice($results, 0, 5);
    }
    
    private function searchActivities($query)
    {
        $activities = DB::table('tbl_uaeactivities') // Corrected table name
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
                'description' => $activity->activityLocation
            ];
        }
        return $results;
    }
    
    private function searchVisas($query)
    {
        $visas = DB::table('uae_visa_master') // Check if this matches your DB exactly
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
                'description' => 'Premium UAE visa processing - AED ' . number_format($visa->UAEVPrice, 2)
            ];
        }
        return $results;
    }

    private function searchCountries($query)
    {
        $popularDestinations = [
            ['title' => 'Egypt', 'keywords' => 'egypt cairo pyramids nile'],
            ['title' => 'Bahrain', 'keywords' => 'bahrain manama gulf'],
            ['title' => 'Oman', 'keywords' => 'oman muscat salalah'],
            ['title' => 'Saudi Arabia', 'keywords' => 'saudi arabia riyadh jeddah makkah medina'],
            ['title' => 'South Africa', 'keywords' => 'south africa cape town johannesburg'],
            ['title' => 'Thailand', 'keywords' => 'thailand bangkok phuket'],
            ['title' => 'Singapore', 'keywords' => 'singapore city'],
            ['title' => 'Malaysia', 'keywords' => 'malaysia kuala lumpur'],
            ['title' => 'Georgia', 'keywords' => 'georgia tbilisi'],
            ['title' => 'Azerbaijan', 'keywords' => 'azerbaijan baku'],
            ['title' => 'Germany', 'keywords' => 'germany berlin munich'],
            ['title' => 'United Kingdom', 'keywords' => 'uk london britain england'],
            ['title' => 'France', 'keywords' => 'france paris'],
            ['title' => 'Switzerland', 'keywords' => 'switzerland zurich geneva'],
            ['title' => 'Jordan', 'keywords' => 'jordan amman petra'],
        ];

        $results = [];
        foreach ($popularDestinations as $dest) {
            if (str_contains(strtolower($dest['title'] . ' ' . $dest['keywords']), $query)) {
                $results[] = [
                    'title' => $dest['title'] . ' - Tour Packages',
                    'url' => '/countriestour?search=' . urlencode($dest['title']),
                    'description' => 'Explore world-class tour packages to ' . $dest['title']
                ];
            }
        }
        return array_slice($results, 0, 3);
    }
}
