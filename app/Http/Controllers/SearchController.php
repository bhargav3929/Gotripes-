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

        $query = strtolower(trim($query));
        // Split query into individual words (min 2 chars each)
        $words = array_filter(explode(' ', $query), function($w) {
            return strlen($w) >= 2;
        });
        $words = array_values($words);

        if (empty($words)) {
            $words = [$query];
        }

        $pages = $this->searchPages($query, $words);
        $activities = $this->searchActivities($query, $words);
        $visas = $this->searchVisas($query, $words);
        $emirates = $this->searchEmirates($query, $words);
        $countries = $this->searchCountries($query, $words);
        $services = $this->searchServices($query, $words);

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

    /**
     * Score how well a search text matches the query words.
     * Higher score = more relevant.
     */
    private function scoreMatch($title, $description, $keywords, $fullQuery, $words)
    {
        $title = strtolower($title);
        $description = strtolower($description);
        $keywords = strtolower($keywords);
        $allText = $title . ' ' . $description . ' ' . $keywords;
        $score = 0;

        // Exact full phrase match bonuses
        if (str_contains($title, $fullQuery)) {
            $score += 15;
        } elseif (str_contains($description, $fullQuery)) {
            $score += 8;
        } elseif (str_contains($keywords, $fullQuery)) {
            $score += 6;
        }

        // Per-word matching
        foreach ($words as $word) {
            // Exact word in title (highest value)
            if (str_contains($title, $word)) {
                $score += 5;
            }
            // Exact word in description
            if (str_contains($description, $word)) {
                $score += 3;
            }
            // Exact word in keywords
            if (str_contains($keywords, $word)) {
                $score += 2;
            }

            // Partial match: check if any word in text STARTS with the query word
            // e.g., "vis" matches "visa", "tour" matches "tourism"
            $textWords = preg_split('/[\s,\-\/&]+/', $allText);
            foreach ($textWords as $tw) {
                if ($tw !== $word && str_starts_with($tw, $word)) {
                    $score += 1;
                    break; // only count partial bonus once per query word
                }
            }
        }

        return $score;
    }

    /**
     * Score and sort a list of hardcoded items.
     * Returns items with score > 0, sorted by relevance.
     */
    private function scoredSearch($items, $fullQuery, $words, $limit)
    {
        $scored = [];
        foreach ($items as $item) {
            $title = $item['title'] ?? '';
            $description = $item['description'] ?? '';
            $keywords = $item['keywords'] ?? '';

            $score = $this->scoreMatch($title, $description, $keywords, $fullQuery, $words);

            if ($score > 0) {
                $item['_score'] = $score;
                $scored[] = $item;
            }
        }

        // Sort by score descending
        usort($scored, function($a, $b) {
            return $b['_score'] - $a['_score'];
        });

        return array_slice($scored, 0, $limit);
    }

    private function searchPages($query, $words)
    {
        $allPages = [
            ['title' => 'Home', 'url' => '/', 'description' => 'Explore our premium travel services and packages', 'keywords' => 'home main landing gotrips homepage'],
            ['title' => 'Activities', 'url' => '/activities', 'description' => 'Browse exciting activities and experiences in Dubai and UAE', 'keywords' => 'activities things to do experiences adventures desert safari burj khalifa water sports fun entertainment ticket booking'],
            ['title' => 'UAE Visa Services', 'url' => '/uaevisa', 'description' => 'Apply for UAE tourist visa, transit visa, and visa services', 'keywords' => 'visa uae dubai tourist transit visa services apply passport document entry permit travel visa processing status check renewal extend'],
            ['title' => 'Tour Packages', 'url' => '/countriestour', 'description' => 'Explore 195+ countries with world-class tour packages', 'keywords' => 'tour packages trips travel international vacation holidays countries world explore destination trip holiday package deal offer'],
            ['title' => 'Hajj & Umrah Services', 'url' => '/hajj-umrah', 'description' => 'Sacred pilgrimage packages with luxury accommodation', 'keywords' => 'hajj umrah pilgrimage mecca medina islamic religious saudi holy trip spiritual journey package'],
            ['title' => 'Our Services', 'url' => '/our-services', 'description' => 'Complete list of all our travel and business services', 'keywords' => 'services offerings solutions travel business help what we do all services list'],
            ['title' => 'Shop Online', 'url' => '/shopnow', 'description' => 'Book activities, tours, and services online', 'keywords' => 'shop online booking ecommerce buy store purchase order'],
            ['title' => 'Pay Online', 'url' => '/payonline', 'description' => 'Secure online payment portal for bookings', 'keywords' => 'pay payment online transaction secure ccavenue credit card debit transfer money send'],
            ['title' => 'Careers', 'url' => '/lookingforajob', 'description' => 'Job openings and career opportunities in UAE', 'keywords' => 'careers jobs employment opportunities work hiring recruitment uae job vacancy opening position apply resume cv'],
            ['title' => 'Contact Us', 'url' => '/contact-us', 'description' => 'Get in touch with our team for support and inquiries', 'keywords' => 'contact support help email phone address location office reach call whatsapp message inquiry question'],
            ['title' => 'Our Story', 'url' => '/ourstory', 'description' => 'Learn about Ayn Al Amir Tourism and our journey', 'keywords' => 'about us story history company ayn al amir who we are team founder'],
            ['title' => 'Dubai Global Village', 'url' => '/dubai-global-village', 'description' => 'Book tickets for Dubai Global Village entertainment park', 'keywords' => 'global village dubai entertainment festival culture tickets booking park show event family fun'],
            ['title' => 'Lotus Cruise Dubai', 'url' => '/lotus-cruise-dubai', 'description' => 'Premium dinner cruise experience on Dubai Creek', 'keywords' => 'lotus cruise dubai creek dinner boat yacht buffet sea water ride ship luxury'],
            ['title' => 'Privacy Policy', 'url' => '/privacypolicy', 'description' => 'Our privacy policy and data protection information', 'keywords' => 'privacy policy data protection information legal'],
            ['title' => 'Terms & Conditions', 'url' => '/termsandconditions', 'description' => 'Terms and conditions of service', 'keywords' => 'terms conditions legal policy rules agreement'],
        ];

        $scored = $this->scoredSearch($allPages, $query, $words, 5);

        return array_map(function($item) {
            return [
                'title' => $item['title'],
                'url' => $item['url'],
                'description' => $item['description'],
                'type' => 'page',
                'icon' => 'bi-globe'
            ];
        }, $scored);
    }

    private function searchActivities($query, $words)
    {
        $activities = DB::table('tbl_UAEActivities')
            ->where('isActive', true)
            ->where(function($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere(DB::raw('LOWER(activityName)'), 'like', '%' . $word . '%')
                      ->orWhere(DB::raw('LOWER(activityLocation)'), 'like', '%' . $word . '%');
                }
            })
            ->limit(8)
            ->get();

        // Score and sort results
        $scored = [];
        foreach ($activities as $activity) {
            $name = strtolower($activity->activityName ?? '');
            $location = strtolower($activity->activityLocation ?? '');
            $score = $this->scoreMatch($activity->activityName ?? '', $activity->activityLocation ?? '', '', $query, $words);

            if ($score > 0) {
                $scored[] = [
                    'title' => $activity->activityName,
                    'url' => '/activities?id=' . $activity->activityID,
                    'description' => $activity->activityLocation,
                    'type' => 'activity',
                    'icon' => 'bi-geo-alt',
                    '_score' => $score
                ];
            }
        }

        usort($scored, function($a, $b) { return $b['_score'] - $a['_score']; });

        return array_map(function($item) {
            unset($item['_score']);
            return $item;
        }, array_slice($scored, 0, 5));
    }

    private function searchEmirates($query, $words)
    {
        $emirates = DB::table('tbl_emirates')
            ->where(function($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere(DB::raw('LOWER(emiratesName)'), 'like', '%' . $word . '%');
                }
            })
            ->limit(7)
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
        return array_slice($results, 0, 5);
    }

    private function searchVisas($query, $words)
    {
        // Always show visa results if any word relates to visa/travel docs
        $visaRelatedWords = ['visa', 'vis', 'passport', 'entry', 'permit', 'transit', 'tourist', 'travel', 'document', 'apply', 'status', 'check', 'renew', 'extend', 'uae', 'dubai', 'processing', 'day', 'days', 'month'];
        $isVisaRelated = false;

        foreach ($words as $word) {
            foreach ($visaRelatedWords as $vw) {
                if (str_contains($vw, $word) || str_contains($word, $vw)) {
                    $isVisaRelated = true;
                    break 2;
                }
            }
        }

        if (!$isVisaRelated) {
            return [];
        }

        $visas = DB::table('uae_visa_master')
            ->where('isActive', true)
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

    private function searchCountries($query, $words)
    {
        $countries = [
            ['title' => 'Egypt Tour Package', 'description' => 'Explore the pyramids, Nile cruises, and ancient wonders', 'keywords' => 'egypt cairo pyramid nile alexandria sharm el sheikh red sea africa travel trip holiday'],
            ['title' => 'Bahrain Tour Package', 'description' => 'Discover the pearl of the Gulf', 'keywords' => 'bahrain manama gulf pearl island kingdom middle east gcc travel trip'],
            ['title' => 'Oman Tour Package', 'description' => 'Mountains, wadis, and pristine beaches', 'keywords' => 'oman muscat nizwa wadi salalah beach mountain nature gcc middle east travel trip'],
            ['title' => 'Saudi Arabia Tour Package', 'description' => 'Heritage sites, modern cities, and holy lands', 'keywords' => 'saudi arabia riyadh jeddah ksa neom mecca medina gcc kingdom travel trip hajj umrah'],
            ['title' => 'South Africa Tour Package', 'description' => 'Safari adventures and Table Mountain', 'keywords' => 'south africa cape town safari johannesburg durban wildlife nature travel trip adventure'],
            ['title' => 'Turkey Tour Package', 'description' => 'Istanbul, Cappadocia, and Mediterranean coast', 'keywords' => 'turkey istanbul cappadocia antalya turkish europe asia travel trip holiday beach culture history'],
            ['title' => 'Georgia Tour Package', 'description' => 'Tbilisi, wine country, and Caucasus mountains', 'keywords' => 'georgia tbilisi caucasus batumi wine mountain nature europe travel trip holiday'],
            ['title' => 'Azerbaijan Tour Package', 'description' => 'Baku, fire temple, and Caspian shores', 'keywords' => 'azerbaijan baku caspian flame towers asia europe travel trip holiday'],
            ['title' => 'Thailand Tour Package', 'description' => 'Bangkok temples, Phuket beaches, and street food', 'keywords' => 'thailand bangkok phuket pattaya chiang mai beach island asia southeast travel trip holiday food temple'],
            ['title' => 'Malaysia Tour Package', 'description' => 'Kuala Lumpur, Langkawi, and tropical adventures', 'keywords' => 'malaysia kuala lumpur langkawi petronas twin towers asia southeast travel trip holiday tropical island'],
        ];

        $scored = $this->scoredSearch($countries, $query, $words, 5);

        return array_map(function($item) {
            return [
                'title' => $item['title'],
                'url' => '/countriestour',
                'description' => $item['description'],
                'type' => 'country',
                'icon' => 'bi-airplane'
            ];
        }, $scored);
    }

    private function searchServices($query, $words)
    {
        $services = [
            ['title' => 'Activities', 'url' => '/activities', 'description' => 'Exciting activities and experiences in UAE', 'keywords' => 'activities things to do experiences adventures fun entertainment dubai uae'],
            ['title' => 'City Tour', 'url' => '/our-services', 'description' => 'Guided city tour and sightseeing experiences', 'keywords' => 'city tour sightseeing guided explore visit see places attraction landmark'],
            ['title' => 'Pick & Drop Guests', 'url' => '/our-services', 'description' => 'Airport transfer and guest transportation', 'keywords' => 'pick drop airport transfer transportation shuttle ride car drive guest welcome receive'],
            ['title' => 'Liwa Guests Assistance', 'url' => '/our-services', 'description' => 'Guest assistance in Al Dhafra Western Region', 'keywords' => 'liwa guests assistance western region al dhafra abu dhabi desert help guide'],
            ['title' => 'World Travel eSIM', 'url' => '/our-services', 'description' => 'International travel eSIM and data plans', 'keywords' => 'esim sim card travel data international roaming mobile phone internet connection abroad'],
            ['title' => 'Trips Organising', 'url' => '/our-services', 'description' => 'Group and corporate trip planning', 'keywords' => 'trips organising planning group corporate events team building party gathering organize arrange'],
            ['title' => 'Hotel Bookings', 'url' => '/our-services', 'description' => 'Hotel and resort accommodation booking', 'keywords' => 'hotel bookings accommodation resort stay booking room lodge reserve apartment villa luxury'],
            ['title' => 'Business WhatsApp Integration', 'url' => '/our-services', 'description' => 'WhatsApp business automation and messaging', 'keywords' => 'whatsapp business integration messaging communication chat bot automation api marketing'],
            ['title' => 'Website Development', 'url' => '/our-services', 'description' => 'Professional website design and development', 'keywords' => 'website development web design digital online app software technology programming coding'],
            ['title' => 'Visa Services', 'url' => '/uaevisa', 'description' => 'UAE visa processing and application services', 'keywords' => 'visa services processing uae tourist transit apply application document passport entry permit travel'],
            ['title' => 'Hajj & Umrah Services', 'url' => '/hajj-umrah', 'description' => 'Sacred pilgrimage packages to Mecca and Medina', 'keywords' => 'hajj umrah pilgrimage mecca medina holy trip islamic religious spiritual package saudi'],
            ['title' => 'Car Rentals', 'url' => '/our-services', 'description' => 'Vehicle rental and car hire services', 'keywords' => 'car rental hire vehicle drive rent automobile suv sedan luxury economy transport'],
            ['title' => 'Recruitment Services', 'url' => '/our-services', 'description' => 'Staffing and manpower recruitment solutions', 'keywords' => 'recruitment hiring staffing manpower jobs employment placement hr human resource talent'],
            ['title' => 'Internships', 'url' => '/our-services', 'description' => 'Student internship and training programs', 'keywords' => 'internship training student placement learn experience program graduate'],
            ['title' => 'World Class Tour Packages', 'url' => '/countriestour', 'description' => 'International tour packages worldwide', 'keywords' => 'tour packages international travel world class vacation holiday trip abroad foreign country destination'],
            ['title' => 'Travel Agency Workflow Setup', 'url' => '/our-services', 'description' => 'Travel agency automation and system setup', 'keywords' => 'travel agency workflow setup automation system software manage solution technology'],
            ['title' => 'New Business Setup Assistance', 'url' => '/our-services', 'description' => 'Company formation and trade license services', 'keywords' => 'business setup company formation trade license new start register startup uae dubai'],
            ['title' => 'GDS Support', 'url' => '/our-services', 'description' => 'Global Distribution System support and training', 'keywords' => 'gds amadeus sabre galileo booking system travelport airline flight reservation'],
            ['title' => 'Business Consultants', 'url' => '/our-services', 'description' => 'Business advisory and strategy consulting', 'keywords' => 'business consultant advisory strategy consulting plan growth help expert advice'],
            ['title' => 'AI, AR, VR Integration', 'url' => '/our-services', 'description' => 'Artificial intelligence and virtual reality solutions', 'keywords' => 'ai artificial intelligence ar vr virtual reality augmented technology innovation smart digital'],
            ['title' => 'Study Abroad', 'url' => '/our-services', 'description' => 'Overseas education and university programs', 'keywords' => 'study abroad education overseas university college learn program degree school admission scholarship'],
        ];

        $scored = $this->scoredSearch($services, $query, $words, 6);

        return array_map(function($item) {
            return [
                'title' => $item['title'],
                'url' => $item['url'],
                'description' => $item['description'],
                'type' => 'service',
                'icon' => 'bi-briefcase'
            ];
        }, $scored);
    }
}
