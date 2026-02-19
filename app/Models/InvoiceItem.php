<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'description',
        'hsn_sac',
        'qty',
        'rate',

        'taxable_amount',

        'cgst_percent',
        'cgst_amount',

        'sgst_percent',
        'sgst_amount',

        'total_amount'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
