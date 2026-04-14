<?php

namespace App\Http\Middleware;

use App\Models\ReferralAgent;
use App\Models\ReferralClick;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReferralTrackingMiddleware
{
    /**
     * Cookie name for storing referral code
     */
    const REFERRAL_COOKIE_NAME = 'gotrips_referral';

    /**
     * Cookie expiry in days
     */
    const COOKIE_EXPIRY_DAYS = 30;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Check if referral code is in the URL
        $referralCode = $request->query('ref');

        if ($referralCode) {
            // Validate referral code
            $agent = ReferralAgent::where('referral_code', $referralCode)
                ->where('status', 'active')
                ->first();

            if ($agent) {
                // Track the click
                $this->trackClick($request, $agent, $referralCode);

                // Update agent click count
                $agent->increment('total_clicks');

                // Set cookie with referral code (Last click wins)
                $minutes = self::COOKIE_EXPIRY_DAYS * 24 * 60;
                $response->withCookie(cookie(
                    self::REFERRAL_COOKIE_NAME,
                    $referralCode,
                    $minutes,
                    '/',
                    null,
                    true, // Secure
                    true  // HttpOnly
                ));
            }
        }

        return $response;
    }

    /**
     * Track the referral click
     */
    protected function trackClick(Request $request, ReferralAgent $agent, string $referralCode): void
    {
        $userAgent = $request->userAgent() ?? '';
        $parsedUA = ReferralClick::parseUserAgent($userAgent);

        ReferralClick::create([
            'referral_agent_id' => $agent->id,
            'referral_code' => $referralCode,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'referer_url' => $request->header('referer'),
            'landing_page' => $request->fullUrl(),
            'device_type' => $parsedUA['device_type'],
            'browser' => $parsedUA['browser'],
            'os' => $parsedUA['os'],
        ]);
    }

    /**
     * Get referral code from cookie
     */
    public static function getReferralCode(Request $request): ?string
    {
        return $request->cookie(self::REFERRAL_COOKIE_NAME);
    }

    /**
     * Get referral agent from cookie
     */
    public static function getReferralAgent(Request $request): ?ReferralAgent
    {
        $referralCode = self::getReferralCode($request);

        if (!$referralCode) {
            return null;
        }

        return ReferralAgent::where('referral_code', $referralCode)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Clear referral cookie
     */
    public static function clearReferralCookie(): \Symfony\Component\HttpFoundation\Cookie
    {
        return cookie()->forget(self::REFERRAL_COOKIE_NAME);
    }

    /**
     * Check if referral code is valid
     */
    public static function isValidReferralCode(string $code): bool
    {
        return ReferralAgent::where('referral_code', $code)
            ->where('status', 'active')
            ->exists();
    }
}
