<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $table = 'purchase_invoices';

    protected $fillable = [
        'company_id',
        'invoice_no',
        'invoice_date',
        'due_date',
        'vendor_name',
        'vendor_email',
        'vendor_phone',
        'vendor_address',
        'vendor_gstin',
        'sub_total',
        'cgst_total',
        'sgst_total',
        'grand_total',
        'status',
        'notes',
        'terms',
    ];
}
