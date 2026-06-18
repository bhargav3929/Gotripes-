<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * A customer enquiry submitted from a tour package page. Stored so the team has
 * a record, and used to notify the package's configured recipient emails.
 */
class PackageEnquiry extends Model
{
    use BelongsToCompany;

    protected $table = 'package_enquiries';

    protected $fillable = [
        'company_id',
        'package_id',
        'package_title',
        'country',
        'name',
        'email',
        'phone',
        'travel_date',
        'travellers',
        'message',
        'status',
    ];
}
