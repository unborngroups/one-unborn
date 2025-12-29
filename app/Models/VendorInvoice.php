<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor;

class VendorInvoice extends Model
{
    protected $fillable = [
        'vendor_id','invoice_no','invoice_date',
        'subtotal','gst_amount','total_amount','status'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}

