<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CCAvenueTransaction extends Model
{
    protected $table = 'ccavenue_transactions';

    protected $fillable = [
        'order_id',
        'tracking_id',
        'bank_ref_no',
        'order_status',
        'failure_message',
        'amount',
        'payment_mode',
        'booking_type',    // Updated from visa_application_id to booking_type
        'response_data',
    ];

    /**
     * (Optional) Define relationship based on booking_type if applicable.
     * 
     * Since booking_type is numeric and not a foreign key, traditionally you would not define
     * a belongsTo relation here unless you want to link to multiple models by booking_type.
     * 
     * If you still want to relate to visa application when booking_type = 1:
     */

    public function visaApplication()
    {
        // Note: This assumes booking_type 1 corresponds to visa applications and 
        // order_id contains the numerical ID to match.
        // Adjust logic based on your actual DB design.

        // If you keep visa_application_id reference elsewhere, update accordingly.
        return $this->belongsTo(UAEVApplication::class, 'visa_application_id');
    }
}
