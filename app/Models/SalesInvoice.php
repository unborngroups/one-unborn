<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    protected $table = 'sales_invoices';

    protected $fillable = [
        'company_id',
        'invoice_no',
        'invoice_date',
        'due_date',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_gstin',
        'sub_total',
        'cgst_total',
        'sgst_total',
        'grand_total',
        'status',
        'notes',
        'terms',
    ];
     public function deliverable()
    {
        return $this->belongsTo(\App\Models\Deliverables::class, 'deliverable_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    
}
