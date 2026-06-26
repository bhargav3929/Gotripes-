<?php

/**
 * Booking-notification helpers.
 *
 * Loaded via require_once in App\Providers\TenantServiceProvider (same pattern
 * as Helpers/tenant.php) so it works on FTP deploys without composer dump-autoload.
 */

if (!function_exists('parse_emails')) {
    /**
     * Turn a free-text recipient list (comma / newline / semicolon separated)
     * into a clean array of unique, valid, lowercased email addresses.
     *
     * @param  string|null  $raw
     * @param  int          $max  hard cap to avoid abuse
     * @return array<int, string>
     */
    function parse_emails(?string $raw, int $max = 10): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        $tokens = preg_split('/[\s,;]+/', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $emails = [];
        foreach ($tokens as $token) {
            $email = strtolower(trim($token));
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[$email] = true; // dedupe via keys
            }
        }

        return array_slice(array_keys($emails), 0, $max);
    }
}

if (!function_exists('booking_recipients')) {
    /**
     * Resolve the final list of business-side recipients for a booking notification.
     *
     * Priority:
     *   1. The entity's configured notification emails (per-entity).
     *   2. If none configured, fall back to the supplied fallback, else the
     *      current tenant's company email — so a notification is never lost.
     * The customer's own email is appended when provided (they get a copy too).
     *
     * @param  array<int, string>  $entityEmails  already-parsed per-entity recipients
     * @param  string|null         $customerEmail
     * @param  string|null         $fallback
     * @return array<int, string>  unique, validated, lowercased
     */
    function booking_recipients(array $entityEmails = [], ?string $customerEmail = null, ?string $fallback = null): array
    {
        $business = array_values(array_filter($entityEmails, function ($e) {
            return is_string($e) && filter_var($e, FILTER_VALIDATE_EMAIL);
        }));

        if (empty($business)) {
            $fallback = $fallback
                ?: (function_exists('current_company') ? current_company()?->email : null)
                ?: config('mail.from.address');
            if ($fallback && filter_var($fallback, FILTER_VALIDATE_EMAIL)) {
                $business[] = strtolower($fallback);
            }
        }

        if ($customerEmail && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $business[] = strtolower($customerEmail);
        }

        // Final dedupe while preserving order.
        return array_values(array_unique($business));
    }
}

if (!function_exists('service_notification_emails')) {
    /**
     * Per-service notification recipients configured by a tenant for products
     * that have no per-item catalog row (eSIM, e-Visa, FIFA). Stored in the
     * company's `settings` JSON under `booking_notification_emails.{service}`.
     *
     * Pass an explicit $company when resolving outside a tenant-bound request
     * (e.g. a payment webhook) so we read the ORDER's tenant, not the apex host.
     *
     * @return array<int, string>
     */
    function service_notification_emails(string $service, $company = null): array
    {
        $company = $company ?: (function_exists('current_company') ? current_company() : null);
        if (!$company) {
            return [];
        }

        $map = $company->getSetting('booking_notification_emails', []);
        $raw = is_array($map) ? ($map[$service] ?? '') : '';

        return parse_emails(is_string($raw) ? $raw : '');
    }
}
