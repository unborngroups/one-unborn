<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxInvoiceSetting extends Model
{
     protected $fillable = [
        'invoice_prefix', 'invoice_start_no', 'currency_symbol',
        'currency_code', 'tax_percentage', 'billing_terms'
    ];
}
