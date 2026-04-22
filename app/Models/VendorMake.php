<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorMake extends Model
{
     protected $fillable = [
        'make_name',
        'company_name',
        'contact_no',
        'email_id',
    ];
}
