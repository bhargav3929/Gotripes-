<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $paymentStatus;
    public $bookingType;

    public function __construct($data, $paymentStatus, $bookingType)
    {
        $this->data = $data;
        $this->paymentStatus = $paymentStatus; // Success, Failed, Cancelled
        $this->bookingType = $bookingType; // visa, activity, etc.
    }

    public function build()
    {
        $subject = $this->getSubject();
        
        return $this->subject($subject)
                    ->view('emails.payment-status')
                    ->with([
                        'data' => $this->data,
                        'paymentStatus' => $this->paymentStatus,
                        'bookingType' => $this->bookingType,
                        'isSuccess' => $this->paymentStatus === 'Success',
                        'isFailed' => $this->paymentStatus === 'Failed',
                        'isCancelled' => $this->paymentStatus === 'Cancelled'
                    ]);
    }

    private function getSubject()
    {
        $type = ucfirst($this->bookingType);
        
        switch($this->paymentStatus) {
            case 'Success':
                return "{$type} Booking - Payment Completed Successfully";
            case 'Failed':
                return "{$type} Booking - Payment Failed";
            case 'Cancelled':
                return "{$type} Booking - Payment Cancelled";
            default:
                return "{$type} Booking - Payment Status Update";
        }
    }
}
