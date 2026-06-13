<?php

namespace App\Http\Controllers;

use App\Models\FifaMatch;
use App\Models\FifaSetting;
use App\Models\FifaTicket;
use App\Models\FifaTicketRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FifaTicketsController extends Controller
{
    /** Public FIFA World Cup 2026 tickets page. */
    public function index()
    {
        $markup   = FifaSetting::markupPercent();
        $currency = FifaSetting::currency();

        $matches = FifaMatch::where('is_active', 1)
            ->whereHas('activeTickets')
            ->with(['activeTickets'])
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
            $toEmail   = current_company()?->email ?: $fromEmail;
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
}
