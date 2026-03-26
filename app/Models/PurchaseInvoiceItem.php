<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceItem extends Model
{
    protected $fillable = [
        'purchase_invoice_id',
        'item_id',
        'quantity',
        'price',
        'total',
    ];

    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function item()
    {
        return $this->belongsTo(Items::class, 'item_id');
    }
}
