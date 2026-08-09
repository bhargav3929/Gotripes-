<?php

namespace App\Http\Controllers;

use App\Mail\BookingNotificationMail;
use App\Models\FluxirVisaApplication;
use App\Services\FluxirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Post-payment half of the Fluxir e-visa flow. Applications are created by
 * FluxirEvisaController::apply(); Stripe (or the credit-flow gateway) then
 * returns the customer here.
 *
 *   success(): finalizeCheckout -> mark paid/submitted -> notify business + customer
 *   cancel() : mark cancelled
 *   status() : poll current Fluxir state
 */
class FluxirVisaController extends Controller
{
    public function __construct(protected FluxirService $fluxir)
    {
    }

    /**
     * GET /visa/fluxir/success — Stripe returns here after payment.
     */
    public function success(Request $request)
    {
        // Order ids are globally unique; look up without the tenant scope so a
        // payment that returns on the apex domain still finds an application
        // created on a tenant subdomain (CompanyScope would otherwise hide it).
        $record = FluxirVisaApplication::withoutCompanyScope()
            ->where('order_id', $request->query('order_id'))->first();

        // Credit/invoicing submissions are already 'submitted' (no Stripe step),
        // so skip finalize-checkout for them — only the pay-now flow finalizes.
        $isCredit = $record && $record->status === 'submitted';

        if ($record && !$isCredit && $record->fluxir_trip_id) {
            $final = $this->fluxir->finalizeCheckout($record->fluxir_trip_id);
            $record->update([
                'is_paid'       => (bool) ($final['success'] ?? false),
                'status'        => ($final['success'] ?? false) ? 'submitted' : $record->status,
                'state'         => $final['data']['serviceApplications'][0]['state'] ?? $record->state,
                'last_response' => $final['data'] ?? $record->last_response,
            ]);
        }

        // Notify the business team once, when the application is submitted
        // (credit flow) or payment finalized (pay-now). notified_at guards against
        // re-sending if the customer refreshes this Stripe return page.
        if ($record && $record->status === 'submitted' && !$record->notified_at) {
            $this->sendVisaNotification($record);
        }

        // The customer gets their own confirmation, guarded separately so a
        // failure of either email can be retried without duplicating the other.
        if ($record && $record->status === 'submitted' && !$record->customer_notified_at) {
            $this->sendCustomerConfirmation($record);
        }

        return redirect('/e-visa')->with('status', $isCredit
            ? 'Your visa application has been submitted for processing. We will email you updates.'
            : 'Your visa application and payment were received. We will email you updates.');
    }

    /**
     * GET /visa/fluxir/cancel — Stripe returns here on cancel.
     */
    public function cancel(Request $request)
    {
        FluxirVisaApplication::withoutCompanyScope()
            ->where('order_id', $request->query('order_id'))
            ->update(['status' => 'cancelled']);

        return redirect('/e-visa')->with('status', 'Payment was cancelled. You can resume your application any time.');
    }

    /**
     * Email the business team that a visa application was submitted/paid.
     * Recipients come from Manager → Booking Notifications (service 'visa'),
     * resolved against the application's tenant. Failures are logged, not thrown.
     */
    private function sendVisaNotification(FluxirVisaApplication $record): void
    {
        try {
            $recipients = booking_recipients(
                service_notification_emails('visa', $record->company)
            );
            if (empty($recipients)) {
                return;
            }

            Mail::to($recipients)->send(new BookingNotificationMail(
                heading: 'New e-Visa application',
                intro: 'A visa application has been submitted for processing.',
                rows: [
                    'Applicant'   => trim(($record->first_name ?? '') . ' ' . ($record->last_name ?? '')),
                    'Email'       => $record->email,
                    'Phone'       => $record->phone,
                    'Nationality' => $record->nationality,
                    'Passport'    => $record->passport_number,
                    'Destination' => $record->destination_code,
                    'Arrival'     => optional($record->arrival_date)->format('d M Y'),
                    'Amount'      => trim(($record->currency ?? '') . ' ' . $record->amount),
                    'Paid'        => $record->is_paid ? 'Yes' : 'On credit / pending',
                ],
                reference: $record->order_id,
                replyToAddress: $record->email,
            ));

            $record->forceFill(['notified_at' => now()])->save();
        } catch (\Throwable $e) {
            Log::error('Visa booking notification failed', [
                'order_id' => $record->order_id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send the traveller their confirmation email. Failures are logged, never
     * thrown — a mail outage must not break the Stripe return page.
     */
    private function sendCustomerConfirmation(FluxirVisaApplication $record): void
    {
        if (!$record->email) {
            return;
        }
        try {
            Mail::to($record->email)->send(new \App\Mail\EvisaCustomerMail($record, 'submitted'));
            $record->forceFill(['customer_notified_at' => now()])->save();
        } catch (\Throwable $e) {
            Log::error('e-Visa customer confirmation failed', [
                'order_id' => $record->order_id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    /**
     * GET /visa/fluxir/status/{orderId} — poll current state.
     */
    public function status(string $orderId)
    {
        $record = FluxirVisaApplication::withoutCompanyScope()
            ->where('order_id', $orderId)->firstOrFail();

        $live = ($record->fluxir_trip_id && $record->fluxir_service_application_id)
            ? $this->fluxir->getServiceApplication($record->fluxir_trip_id, $record->fluxir_service_application_id)
            : ['success' => true, 'data' => null];

        if (($live['success'] ?? false) && isset($live['data']['state'])) {
            $record->update([
                'state'   => $live['data']['state'],
                'is_paid' => $this->fluxir->applicationIsPaid($live['data']) ?: $record->is_paid,
            ]);
        }

        return response()->json([
            'success' => true,
            'order_id' => $orderId,
            'status'  => $record->status,
            'state'   => $record->state,
            'is_paid' => $record->is_paid,
        ]);
    }
}
