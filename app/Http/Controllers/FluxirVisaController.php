<?php

namespace App\Http\Controllers;

use App\Models\FluxirVisaApplication;
use App\Services\FluxirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrates an e-visa application through the Fluxir API.
 *
 * Flow (each step aborts cleanly with a user-safe message on failure):
 *   apply()  : createPerson -> createTrip -> createServiceApplication
 *              -> uploadIdentityDocument(s) -> updateServiceApplication(ReadyForPayment)
 *              -> getCheckout  => returns Stripe checkout URL
 *   success(): finalizeCheckout -> mark paid/submitted
 *   cancel() : mark cancelled
 *   status() : poll current Fluxir state
 */
class FluxirVisaController extends Controller
{
    public function __construct(protected FluxirService $fluxir)
    {
    }

    /**
     * POST /visa/fluxir/apply
     * Receives the e-visa form (matches the traveller form fields) + documents.
     */
    public function apply(Request $request)
    {
        if (!$this->fluxir->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Visa service is not configured yet. Please contact support.',
            ], 503);
        }

        $data = $request->validate([
            'title'               => 'nullable|string|max:10',
            'first_name'          => 'required|string|max:100',
            'last_name'           => 'required|string|max:100',
            'gender'              => 'nullable|in:male,female,Male,Female,M,F',
            'passport_number'     => 'nullable|string|max:30',
            'passport_expiry'     => 'nullable|date',
            'country_of_issuance' => 'nullable|string|max:3',
            'date_of_birth'       => 'nullable|date',
            'nationality'         => 'nullable|string|max:3',
            'email'               => 'required|email',
            'phone'               => 'nullable|string|max:30',
            'destination_code'    => 'nullable|string|max:3',
            'origination_code'    => 'nullable|string|max:3',
            'arrival_date'        => 'required|date',
            'departure_date'      => 'required|date|after_or_equal:arrival_date',
            // Required documents for UAE eVisa (scheme item nameIds):
            'passport_file'       => 'required|file|mimes:pdf,jpg,jpeg,png|max:8192', // -> passportFile
            'personal_photo'      => 'required|image|mimes:jpg,jpeg,png|max:8192',    // -> traveler.personalPhoto
        ]);

        $orderId = 'ORDVISA-' . strtoupper(uniqid());

        // Persist an early draft so a mid-flow failure is still recoverable.
        $record = FluxirVisaApplication::create([
            'order_id'            => $orderId,
            'status'              => 'draft',
            'title'               => $data['title'] ?? null,
            'first_name'          => $data['first_name'],
            'last_name'           => $data['last_name'],
            'gender'              => $data['gender'] ?? null,
            'passport_number'     => $data['passport_number'] ?? null,
            'passport_expiry'     => $data['passport_expiry'] ?? null,
            'country_of_issuance' => $data['country_of_issuance'] ? strtoupper($data['country_of_issuance']) : null,
            'date_of_birth'       => $data['date_of_birth'] ?? null,
            'nationality'         => $data['nationality'] ? strtoupper($data['nationality']) : null,
            'email'               => $data['email'],
            'phone'               => $data['phone'] ?? null,
            'destination_code'    => strtoupper($data['destination_code'] ?? config('fluxir.default_destination')),
            'origination_code'    => strtoupper($data['origination_code'] ?? (string) config('fluxir.default_origin')),
            'arrival_date'        => $data['arrival_date'],
            'departure_date'      => $data['departure_date'],
        ]);

        try {
            // --- Step 1: person ---
            $person = $this->fluxir->createPerson([
                'firstName' => $data['first_name'],
                'lastName'  => $data['last_name'],
                'email'     => $data['email'],
            ]);
            if (!$person['success']) {
                return $this->fail($record, 'createPerson', $person);
            }
            $personId = $person['data']['id'] ?? null;

            // --- Step 2: trip ---
            $tripPayload = [
                'originationCode' => $record->origination_code ?: null,
                'destinationCode' => $record->destination_code,
                'from'            => $data['arrival_date'],
                'to'              => $data['departure_date'],
                'description'     => 'GoTrips e-visa ' . $orderId,
            ];
            $trip = $this->fluxir->createTrip($tripPayload);
            if (!$trip['success']) {
                return $this->fail($record, 'createTrip', $trip);
            }
            $tripId = $trip['data']['id'] ?? null;

            // --- Step 3: resolve the service intent (key + provider + scheme version) ---
            $intent = $this->fluxir->resolveServiceIntent($tripPayload);
            if (!$intent) {
                return $this->fail($record, 'resolveServiceIntent', ['error' => 'No visa service intent for destination']);
            }
            $serviceIntentKey  = $intent['serviceIntentKey'] ?? null;
            $providerName      = $intent['provider'] ?? config('fluxir.provider_name');
            $documentVersionId = $intent['documentSchemeVersionId'] ?? null;

            // --- Step 4: service (visa) application ---
            $app = $this->fluxir->createServiceApplication([
                'serviceType'       => 'Visa',
                'providerName'      => $providerName,
                'serviceIntentKey'  => $serviceIntentKey,
                'personId'          => $personId,
                'tripId'            => $tripId,
                'documentVersionId' => $documentVersionId,
                'dueDate'           => $data['departure_date'],
            ]);
            if (!$app['success']) {
                return $this->fail($record, 'createServiceApplication', $app);
            }
            $serviceAppId = $app['data']['id'] ?? null;

            $record->update([
                'fluxir_person_id'              => $personId,
                'fluxir_trip_id'                => $tripId,
                'fluxir_service_application_id' => $serviceAppId,
                'state'                         => $app['data']['state'] ?? 'Draft',
                'amount'                        => $intent['fee'] ?? null,
                'currency'                      => 'USD',
                'status'                        => 'created',
            ]);

            // --- Step 5: upload the required document items (scheme nameIds) ---
            $uploads = [
                'passportFile'            => $request->file('passport_file'),
                'traveler.personalPhoto'  => $request->file('personal_photo'),
            ];
            $attachments = [];
            foreach ($uploads as $itemNameId => $file) {
                $up = $this->fluxir->uploadDocumentItem(
                    $serviceAppId,
                    $itemNameId,
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
                if (!$up['success']) {
                    return $this->fail($record, "uploadDocumentItem:{$itemNameId}", $up);
                }
                $attachments[$itemNameId] = $up['data']['id'] ?? null;
            }
            $record->update(['attachments' => $attachments]);

            // --- Step 6: advance to ReadyForPayment (files already attached) ---
            $update = $this->fluxir->updateServiceApplication($serviceAppId, [], 'ReadyForPayment');
            if (!$update['success']) {
                return $this->fail($record, 'updateServiceApplication', $update);
            }
            $record->update(['state' => 'ReadyForPayment', 'status' => 'awaiting_payment']);

            // --- Step 7: checkout ---
            $checkout = $this->fluxir->getCheckout(
                $tripId,
                [$serviceAppId],
                config('fluxir.success_url') . '?order_id=' . $orderId,
                config('fluxir.cancel_url') . '?order_id=' . $orderId
            );
            if (!$checkout['success']) {
                return $this->fail($record, 'getCheckout', $checkout);
            }

            $sessionId = $checkout['session_id'] ?? null;

            $record->update([
                'checkout_session_id' => $sessionId,
                'last_response'       => $checkout['data'],
            ]);

            return response()->json([
                'success'            => true,
                'order_id'           => $orderId,
                'checkout_session_id' => $sessionId,
                'amount'             => $record->amount,
                'currency'           => $record->currency,
            ]);
        } catch (\Throwable $e) {
            Log::error('Fluxir apply exception', ['order_id' => $orderId, 'message' => $e->getMessage()]);
            $record->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => 'We could not start your visa application. Please try again.',
            ], 500);
        }
    }

    /**
     * GET /visa/fluxir/success — Stripe returns here after payment.
     */
    public function success(Request $request)
    {
        $record = FluxirVisaApplication::where('order_id', $request->query('order_id'))->first();

        if ($record && $record->fluxir_trip_id) {
            $final = $this->fluxir->finalizeCheckout($record->fluxir_trip_id);
            $record->update([
                'is_paid'       => (bool) ($final['success'] ?? false),
                'status'        => ($final['success'] ?? false) ? 'submitted' : $record->status,
                'state'         => $final['data']['serviceApplications'][0]['state'] ?? $record->state,
                'last_response' => $final['data'] ?? $record->last_response,
            ]);
        }

        return redirect('/uaevisa')->with('status', 'Your visa application and payment were received. We will email you updates.');
    }

    /**
     * GET /visa/fluxir/cancel — Stripe returns here on cancel.
     */
    public function cancel(Request $request)
    {
        FluxirVisaApplication::where('order_id', $request->query('order_id'))
            ->update(['status' => 'cancelled']);

        return redirect('/uaevisa')->with('status', 'Payment was cancelled. You can resume your application any time.');
    }

    /**
     * GET /visa/fluxir/status/{orderId} — poll current state.
     */
    public function status(string $orderId)
    {
        $record = FluxirVisaApplication::where('order_id', $orderId)->firstOrFail();

        $live = $record->fluxir_service_application_id
            ? $this->fluxir->getServiceApplication($record->fluxir_service_application_id)
            : ['success' => true, 'data' => null];

        if (($live['success'] ?? false) && isset($live['data']['state'])) {
            $record->update(['state' => $live['data']['state'], 'is_paid' => (bool) ($live['data']['isPaid'] ?? $record->is_paid)]);
        }

        return response()->json([
            'success' => true,
            'order_id' => $orderId,
            'status'  => $record->status,
            'state'   => $record->state,
            'is_paid' => $record->is_paid,
        ]);
    }

    /* ---------------------------------------------------------------- helpers */

    /** Map a form gender value to Fluxir's enum (gender.male / gender.female). */
    protected function mapGender(?string $g): ?string
    {
        if (!$g) {
            return null;
        }
        return str_starts_with(strtolower($g), 'f') ? 'gender.female' : 'gender.male';
    }

    /** Persist a failed step and return a clean JSON error. */
    protected function fail(FluxirVisaApplication $record, string $step, array $result)
    {
        Log::error("Fluxir step failed: {$step}", [
            'order_id' => $record->order_id,
            'error'    => $result['error'] ?? null,
            'status'   => $result['status'] ?? null,
        ]);

        $record->update(['status' => 'failed', 'last_response' => $result['data'] ?? null]);

        return response()->json([
            'success' => false,
            'step'    => $step,
            'message' => 'We could not complete your visa application. Please try again or contact support.',
        ], 502);
    }
}
