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
        'vendor_id',
        'deliverable_id',
        'vendor_name',
        'vendor_email',
        'vendor_phone',
        'vendor_address',
        'vendor_gstin',
        'po_invoice_file',
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

    public function items()
{
    return $this->hasMany(PurchaseInvoiceItem::class);
}

}
