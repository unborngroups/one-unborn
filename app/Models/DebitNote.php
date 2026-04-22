<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\VendorInvoice;

class DebitNote extends Model
{
    protected $fillable = [
        'vendor_invoice_id','debit_note_no','date','amount','reason'
    ];

    public function vendorInvoice()
    {
        return $this->belongsTo(VendorInvoice::class);
    }
}
