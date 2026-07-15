<?php

namespace App\Http\Controllers;

use App\Models\FifaMatch;
use App\Models\FifaSetting;
use App\Models\FifaTicket;
use App\Models\FifaTicketRequest;
use App\Models\NomodTransaction;
use App\Services\NomodService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FifaTicketsController extends Controller
{
    /** Public FIFA World Cup 2026 tickets page. */
    public function index()
    {
        $markup   = FifaSetting::markupPercent();
        $currency = FifaSetting::currency();

        // Only show UPCOMING matches — hide fixtures whose date has already passed
        // so finished games drop off automatically each day. Matches with no date
        // set (e.g. knockout placeholders that are still "to be decided") are kept.
        $today = now()->startOfDay();

        $matches = FifaMatch::where('is_active', 1)
            ->whereHas('activeTickets')
            ->where(function ($q) use ($today) {
                $q->whereNull('match_date')
                  ->orWhere('match_date', '>=', $today);
            })
            ->with(['activeTickets'])
            ->orderByRaw('match_date IS NULL')  // dated (soonest) first, undated last
            ->orderBy('match_date')
            ->orderBy('sort_order')->orderBy('id')
            ->get()
            ->groupBy('stage');

        return view('fifa-tickets', compact('matches', 'markup', 'currency'));
    }

    /** Handle a customer ticket-interest submission. */
    public function submitRequest(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:160',
            'email'    => 'required|email|max:160',
            'phone'    => 'nullable|string|max:40',
            'country'  => 'nullable|string|max:120',
            'ticket_id'=> 'nullable|exists:fifa_tickets,id',
            'quantity' => 'required|integer|min:1|max:50',
            'message'  => 'nullable|string|max:2000',
        ]);

        $ticket = !empty($data['ticket_id']) ? FifaTicket::with('match')->find($data['ticket_id']) : null;

        $ticketRequest = FifaTicketRequest::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'phone'        => $data['phone'] ?? null,
            'country'      => $data['country'] ?? null,
            'match_id'     => $ticket?->match_id,
            'ticket_id'    => $ticket?->id,
            'match_label'  => $ticket?->match?->title,
            'category'     => $ticket?->category,
            'quoted_price' => $ticket?->customer_price,
            'quantity'     => $data['quantity'],
            'message'      => $data['message'] ?? null,
            'status'       => 'new',
        ]);

        // Email a copy of the enquiry to the business inbox. Route to the
        // tenant's address if set, otherwise the platform "from" address
        // (info@aynalamirtourism.com). A mail failure must not break the
        // customer's confirmation, so it is logged, not thrown.
        try {
            $fromEmail = config('mail.from.address');
            // Per-service recipients configured in Manager → Booking Notifications,
            // falling back to the tenant's account email when none are set.
            $toEmail   = booking_recipients(service_notification_emails('fifa'));
            $currency  = FifaSetting::currency();
            $html      = $this->buildEnquiryEmailHtml($ticketRequest, $currency);

            Mail::html($html, function ($message) use ($toEmail, $fromEmail, $ticketRequest) {
                $message->to($toEmail)
                        ->from($fromEmail, config('mail.from.name'))
                        ->replyTo($ticketRequest->email, $ticketRequest->name)
                        ->subject(
                            'FIFA WC 2026 Ticket Enquiry — ' . $ticketRequest->name
                            . ($ticketRequest->match_label ? ' (' . $ticketRequest->match_label . ')' : '')
                        );
            });
        } catch (\Throwable $e) {
            Log::error('FIFA ticket enquiry email failed to send.', [
                'request_id' => $ticketRequest->id,
                'error'      => $e->getMessage(),
            ]);
        }

        return back()->with('fifa_success', 'Thank you! Your ticket request has been received — our concierge team will contact you shortly.');
    }

    /**
     * Start an online payment (Nomod) for a FIFA ticket. Creates a paid booking
     * record + Nomod checkout and returns the checkout URL. On payment success
     * the Nomod callback (NomodController) confirms it, emails the customer, and
     * notifies the configured FIFA recipients.
     */
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:160',
            'email'     => 'required|email|max:160',
            'phone'     => 'nullable|string|max:40',
            'country'   => 'nullable|string|max:120',
            'ticket_id' => 'required|exists:fifa_tickets,id',
            'quantity'  => 'required|integer|min:1|max:50',
            'message'   => 'nullable|string|max:2000',
        ]);

        $ticket = FifaTicket::with('match')->findOrFail($data['ticket_id']);
        $unit   = (float) $ticket->customer_price;
        $qty    = (int) $data['quantity'];
        $amount = round($unit * $qty, 2);
        $currency = FifaSetting::currency();

        if ($amount <= 0) {
            return response()->json(['success' => false, 'error' => 'This ticket is not available for online payment.'], 422);
        }

        $matchLabel = $ticket->match
            ? trim(($ticket->match->team_a ?? '') . ' vs ' . ($ticket->match->team_b ?? ''), ' vs ')
            : null;

        $booking = FifaTicketRequest::create([
            'company_id'     => current_company_id(),
            'name'           => $data['name'],
            'email'          => $data['email'],
            'phone'          => $data['phone'] ?? null,
            'country'        => $data['country'] ?? null,
            'match_id'       => $ticket->match_id,
            'ticket_id'      => $ticket->id,
            'match_label'    => $matchLabel,
            'category'       => $ticket->category,
            'quoted_price'   => $unit,
            'unit_price'     => $unit,
            'amount'         => $amount,
            'currency'       => $currency,
            'quantity'       => $qty,
            'message'        => $data['message'] ?? null,
            'status'         => 'new',
            'payment_status' => 'awaiting_payment',
        ]);

        $orderId = 'ORDFIFA' . $booking->id;
        $booking->update(['order_id' => $orderId]);

        $label = ($matchLabel ?: 'FIFA World Cup 2026') . ' — ' . $ticket->category;

        $nomod = new NomodService();
        $checkout = $nomod->createCheckout([
            'amount'      => $amount,
            'currency'    => $currency,
            'order_id'    => $orderId,
            'description' => "FIFA WC 2026: {$label} x{$qty}",
            'customer'    => array_filter([
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ]),
            'items' => [[
                'item_id'     => (string) $ticket->id,
                'name'        => $label,
                'quantity'    => $qty,
                'unit_amount' => number_format($unit, 2, '.', ''),
            ]],
            'metadata' => [
                'type'            => 'fifa',
                'fifa_request_id' => (string) $booking->id,
                'match'           => $matchLabel,
            ],
        ]);

        if (!($checkout['success'] ?? false)) {
            Log::error('FIFA Nomod checkout failed', ['request_id' => $booking->id, 'error' => $checkout['error'] ?? 'Unknown']);
            $booking->update(['payment_status' => 'failed']);
            return response()->json(['success' => false, 'error' => 'Payment could not be started. Please try again.'], 500);
        }

        NomodTransaction::create([
            'checkout_id'  => $checkout['checkout_id'],
            'order_id'     => $orderId,
            'status'       => 'created',
            'amount'       => $amount,
            'currency'     => $currency,
            'booking_type' => 'fifa',
            'checkout_url' => $checkout['checkout_url'],
            'items'        => [[
                'item_id' => $ticket->id, 'name' => $label, 'quantity' => $qty, 'unit_amount' => $unit,
            ]],
            'customer'     => ['name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'] ?? null],
            'metadata'     => ['type' => 'fifa', 'fifa_request_id' => $booking->id],
        ]);

        return response()->json([
            'success'      => true,
            'checkout_url' => $checkout['checkout_url'],
            'order_id'     => $orderId,
        ]);
    }

    /** Build the HTML body for the enquiry-copy email sent to the business inbox. */
    private function buildEnquiryEmailHtml(FifaTicketRequest $r, string $currency): string
    {
        $rows = [
            'Name'         => $r->name,
            'Email'        => $r->email,
            'Phone'        => $r->phone,
            'Country'      => $r->country,
            'Match'        => $r->match_label,
            'Category'     => $r->category,
            'Quoted Price' => $r->quoted_price ? ($currency . ' ' . number_format((float) $r->quoted_price, 2)) : null,
            'Quantity'     => $r->quantity,
        ];

        $rowsHtml = '';
        foreach ($rows as $label => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $rowsHtml .= '<tr>'
                . '<td style="padding:9px 0;width:140px;color:#6b7280;vertical-align:top;border-bottom:1px solid #f0f0f0;">' . e($label) . '</td>'
                . '<td style="padding:9px 0;color:#111827;font-weight:600;border-bottom:1px solid #f0f0f0;">' . e($value) . '</td>'
                . '</tr>';
        }

        $messageHtml = '';
        if (!empty($r->message)) {
            $messageHtml = '<div style="margin-top:20px;">'
                . '<div style="color:#6b7280;font-size:13px;margin-bottom:6px;">Message</div>'
                . '<div style="background:#f9fafb;border:1px solid #f0f0f0;border-radius:8px;padding:14px 16px;font-size:14px;line-height:1.6;color:#374151;white-space:pre-wrap;">'
                . nl2br(e($r->message)) . '</div></div>';
        }

        $ref      = $r->id ? ('Reference #' . $r->id . ' · ') : '';
        $received = (optional($r->created_at)->format('d M Y, H:i')) ?? now()->format('d M Y, H:i');

        return '<!DOCTYPE html><html><body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:24px 0;"><tr><td align="center">'
            . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.06);">'
            . '<tr><td style="background:#111114;padding:24px 32px;">'
            . '<div style="color:#FFD23F;font-size:13px;letter-spacing:1px;text-transform:uppercase;font-weight:700;">New Ticket Enquiry</div>'
            . '<div style="color:#fff;font-size:22px;font-weight:800;margin-top:4px;">FIFA World Cup 2026</div></td></tr>'
            . '<tr><td style="padding:28px 32px 8px;">'
            . '<p style="margin:0 0 18px;font-size:15px;line-height:1.6;color:#374151;">A new ticket enquiry has been submitted on the website. Reply directly to this email to reach the customer.</p>'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.5;">' . $rowsHtml . '</table>'
            . $messageHtml
            . '</td></tr>'
            . '<tr><td style="padding:20px 32px 28px;color:#9ca3af;font-size:12px;">' . $ref . 'Received ' . e($received) . '</td></tr>'
            . '</table></td></tr></table></body></html>';
    }

    /**
     * Live FIFA World Cup scores (JSON), polled by the FIFA page.
     * Uses API-Football (api-sports.io) free tier; results are cached so we
     * stay well within the free 100-requests/day cap. Degrades gracefully when
     * no API key is configured or the provider is unreachable.
     */
    public function liveScores(Request $request)
    {
        // ?demo=1 returns sample fixtures so the UI can be previewed before a
        // real API key is added. Never used for real visitors.
        if ($request->boolean('demo')) {
            return response()->json($this->demoScores());
        }

        $key = config('services.football.key');

        if (empty($key)) {
            return response()->json([
                'configured' => false,
                'live'       => false,
                'matches'    => [],
            ]);
        }

        // Cache 30s for live data — frequent enough to feel live, light on the quota.
        $data = Cache::remember('fifa_live_scores', 30, fn () => $this->fetchScores($key));

        return response()->json($data);
    }

    /** Fetch live World Cup fixtures, falling back to the next upcoming ones. */
    private function fetchScores(string $key): array
    {
        $base    = rtrim(config('services.football.base'), '/');
        $league  = config('services.football.wc_league');
        $headers = ['x-apisports-key' => $key];

        try {
            // 1) Live World Cup matches right now (not season-gated, works on free tier).
            $wc = Http::withHeaders($headers)->timeout(12)
                ->get("$base/fixtures", ['live' => 'all', 'league' => $league]);
            $matches = $this->mapFixtures($wc->json('response') ?? []);

            if (count($matches) > 0) {
                return [
                    'configured' => true,
                    'live'       => true,
                    'scope'      => 'world_cup',
                    'matches'    => $matches,
                    'updated_at' => now()->toIso8601String(),
                ];
            }

            // 2) No World Cup match live → show other live football so the board isn't
            //    empty. (The free plan can't list the 2026 WC schedule, so we can't show
            //    upcoming fixtures.) During WC match hours, step 1 takes over automatically.
            $all = Http::withHeaders($headers)->timeout(12)->get("$base/fixtures", ['live' => 'all']);
            $matches = array_slice($this->mapFixtures($all->json('response') ?? []), 0, 12);

            return [
                'configured' => true,
                'live'       => count($matches) > 0,
                'scope'      => 'all_live',
                'matches'    => $matches,
                'updated_at' => now()->toIso8601String(),
            ];
        } catch (\Throwable $e) {
            Log::warning('FIFA live scores fetch failed.', ['error' => $e->getMessage()]);
            return ['configured' => true, 'live' => false, 'matches' => [], 'error' => true];
        }
    }

    /** Sample fixtures for previewing the live-scores UI (?demo=1). */
    private function demoScores(): array
    {
        $flag = fn ($code) => "https://flagcdn.com/w80/{$code}.png";

        return [
            'configured' => true,
            'live'       => true,
            'demo'       => true,
            'scope'      => 'world_cup',
            'updated_at' => now()->toIso8601String(),
            'matches'    => [
                ['id' => 1, 'status' => '2H', 'status_long' => 'Second Half', 'elapsed' => 67, 'round' => 'Group A',
                 'home' => 'Argentina', 'home_logo' => $flag('ar'), 'away' => 'Brazil',   'away_logo' => $flag('br'), 'home_goals' => 2, 'away_goals' => 1],
                ['id' => 2, 'status' => '1H', 'status_long' => 'First Half', 'elapsed' => 23, 'round' => 'Group C',
                 'home' => 'France',    'home_logo' => $flag('fr'), 'away' => 'Spain',    'away_logo' => $flag('es'), 'home_goals' => 0, 'away_goals' => 0],
                ['id' => 3, 'status' => 'HT', 'status_long' => 'Halftime', 'elapsed' => 45, 'round' => 'Group D',
                 'home' => 'England',   'home_logo' => $flag('gb-eng'), 'away' => 'Portugal', 'away_logo' => $flag('pt'), 'home_goals' => 1, 'away_goals' => 1],
            ],
        ];
    }

    /** Normalise API-Football fixture objects into a compact shape for the UI. */
    private function mapFixtures(array $response): array
    {
        return collect($response)->map(fn ($f) => [
            'id'          => data_get($f, 'fixture.id'),
            'status'      => data_get($f, 'fixture.status.short'),
            'status_long' => data_get($f, 'fixture.status.long'),
            'elapsed'     => data_get($f, 'fixture.status.elapsed'),
            'date'        => data_get($f, 'fixture.date'),
            'league'      => data_get($f, 'league.name'),
            'round'       => data_get($f, 'league.round'),
            'home'        => data_get($f, 'teams.home.name'),
            'home_logo'   => data_get($f, 'teams.home.logo'),
            'away'        => data_get($f, 'teams.away.name'),
            'away_logo'   => data_get($f, 'teams.away.logo'),
            'home_goals'  => data_get($f, 'goals.home'),
            'away_goals'  => data_get($f, 'goals.away'),
        ])->values()->all();
    }
}
