<?php

namespace App\Services;

use App\Mail\SupportTicketMail;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SupportTicketNotifier
{
    public function notifyCreated(SupportTicket $ticket): void
    {
        $ticket->loadMissing('company');
        $emailErrors = [];
        $sentAny = false;

        try {
            Mail::to($ticket->customer_email)->send(new SupportTicketMail($ticket, 'customer_created'));
            $sentAny = true;
        } catch (\Throwable $e) {
            $emailErrors[] = 'Customer: ' . $e->getMessage();
            Log::error('Support ticket customer acknowledgement failed', [
                'ticket' => $ticket->ticket_number,
                'error' => $e->getMessage(),
            ]);
        }

        $recipient = $this->supportEmail($ticket);
        if ($recipient) {
            try {
                Mail::to($recipient)->send(new SupportTicketMail($ticket, 'staff_created'));
                $sentAny = true;
            } catch (\Throwable $e) {
                $emailErrors[] = 'Support team: ' . $e->getMessage();
                Log::error('Support ticket staff notification failed', [
                    'ticket' => $ticket->ticket_number,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            $emailErrors[] = 'No support recipient configured';
        }

        $ticket->forceFill([
            'email_delivery_status' => empty($emailErrors) ? 'sent' : ($sentAny ? 'partial' : 'failed'),
            'email_delivery_error' => $emailErrors ? implode(' | ', $emailErrors) : null,
        ])->save();

        $this->sendWhatsApp($ticket, $this->newTicketWhatsAppText($ticket));
    }

    public function notifyCustomerFollowup(SupportTicket $ticket, string $message): void
    {
        $recipient = $this->supportEmail($ticket);
        if ($recipient) {
            try {
                Mail::to($recipient)->send(new SupportTicketMail($ticket, 'staff_followup', $message));
            } catch (\Throwable $e) {
                Log::error('Support follow-up email failed', [
                    'ticket' => $ticket->ticket_number,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->sendWhatsApp($ticket, "Customer follow-up on {$ticket->ticket_number}:\n{$message}");
    }

    public function notifyStaffReply(SupportTicket $ticket, string $message): void
    {
        try {
            Mail::to($ticket->customer_email)->send(new SupportTicketMail($ticket, 'customer_reply', $message));
        } catch (\Throwable $e) {
            Log::error('Support reply email failed', [
                'ticket' => $ticket->ticket_number,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * A ticket was handed to a staff member: leave an internal note on the
     * thread (visibility 'internal' never reaches the customer view, which
     * filters on 'public') and email the new assignee. Callers skip this when
     * the actor assigned the ticket to themselves.
     */
    public function notifyAssigned(SupportTicket $ticket, User $assignee, User $actor): void
    {
        $ticket->loadMissing('company');

        try {
            $ticket->messages()->create([
                'company_id' => $ticket->company_id,
                'user_id' => $actor->id,
                'sender_type' => 'system',
                'sender_name' => 'System',
                'visibility' => 'internal',
                'message' => "Assigned to {$assignee->name} by {$actor->name}.",
            ]);
        } catch (\Throwable $e) {
            Log::error('Support assignment note failed', [
                'ticket' => $ticket->ticket_number,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            $ticket->setRelation('assignee', $assignee);
            Mail::to($assignee->email, $assignee->name)
                ->send(new SupportTicketMail($ticket, 'staff_assigned', null));
        } catch (\Throwable $e) {
            Log::error('Support assignment email failed', [
                'ticket' => $ticket->ticket_number,
                'assignee' => $assignee->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function supportEmail(SupportTicket $ticket): ?string
    {
        return $ticket->company?->getSetting('support_email')
            ?: $ticket->company?->email
            ?: config('support.default_recipient')
            ?: config('mail.from.address');
    }

    private function sendWhatsApp(SupportTicket $ticket, string $text): void
    {
        $endpoint = config('support.whatsapp.endpoint');
        $token = config('support.whatsapp.token');
        $recipient = $ticket->company?->getSetting('support_whatsapp_recipient')
            ?: config('support.whatsapp.recipient');

        if (!config('support.whatsapp.enabled') || !$endpoint || !$token || !$recipient) {
            $ticket->forceFill([
                'whatsapp_delivery_status' => 'not_configured',
                'whatsapp_delivery_error' => null,
            ])->save();
            return;
        }

        try {
            $response = Http::withToken($token)->timeout(12)->post($endpoint, [
                'messaging_product' => 'whatsapp',
                'to' => preg_replace('/\D+/', '', (string) $recipient),
                'type' => 'text',
                'text' => ['preview_url' => false, 'body' => $text],
            ]);

            if (!$response->successful()) {
                throw new \RuntimeException("HTTP {$response->status()}: " . $response->body());
            }

            $ticket->forceFill([
                'whatsapp_delivery_status' => 'sent',
                'whatsapp_delivery_error' => null,
            ])->save();
        } catch (\Throwable $e) {
            $ticket->forceFill([
                'whatsapp_delivery_status' => 'failed',
                'whatsapp_delivery_error' => $e->getMessage(),
            ])->save();
            Log::error('Support ticket WhatsApp notification failed', [
                'ticket' => $ticket->ticket_number,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function newTicketWhatsAppText(SupportTicket $ticket): string
    {
        return "New support ticket {$ticket->ticket_number}\n"
            . "Customer: {$ticket->customer_name}\n"
            . "Phone: {$ticket->customer_phone}\n"
            . "Issue: {$ticket->subject}";
    }
}
