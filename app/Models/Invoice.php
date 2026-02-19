<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\Client;
use App\Models\InvoiceItem;
use App\Models\Deliverables;

class Invoice extends Model
{

    protected $fillable = [
        'company_id',
        'invoice_no',
        'invoice_date',
        'due_date',

        // Customer details (snapshot)
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_gstin',

        // Totals
        'sub_total',
        'cgst_total',
        'sgst_total',
        'grand_total',

        'status',
        'notes',
        'terms'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

 
    public function deliverable()
{
    return $this->belongsTo(Deliverables::class,'deliverable_id');
}

}


