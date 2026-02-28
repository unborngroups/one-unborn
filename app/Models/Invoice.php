<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\Client;
use App\Models\Deliverables;
use App\Models\Items;

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

   

        public function deliverable()
{
    return $this->belongsTo(Deliverables::class,'deliverable_id');
}

    public function item()
{
    return $this->belongsTo(Items::class,'item_id');
}

/**
 * Scope: Filter invoices by state and month
 */
public function scopeStateMonth(
    $query, $state, $month, $year
) {
    return $query->whereHas('shipToCompanies', function ($q) use ($state) {
        $q->where('state', $state);
    })
    ->whereMonth('activation_date', $month)
    ->whereYear('activation_date', $year);
}

/**
 * Scope: Filter invoices by state and quarter
 */
public function scopeStateQuarter(
    $query, $state, $quarter, $year
) {
    $months = [
        1 => [4,5,6],    // Q1: Apr-Jun
        2 => [7,8,9],    // Q2: Jul-Sep
        3 => [10,11,12], // Q3: Oct-Dec
        4 => [1,2,3],    // Q4: Jan-Mar
    ];
    return $query->whereHas('shipToCompanies', function ($q) use ($state) {
        $q->where('state', $state);
    })
    ->whereIn('activation_month', $months[$quarter])
    ->whereYear('activation_date', $year);
}

/**
     * Relationship: Ship To Companies (assumes pivot table invoice_company)
     */
    public function shipToCompanies()
    {
        return $this->belongsToMany(Company::class, 'invoice_company', 'invoice_id', 'company_id');
    }

    /**
     * Accessor: activation_month (returns month from activation_date)
     */
    public function getActivationMonthAttribute()
    {
        return $this->activation_date ? date('n', strtotime($this->activation_date)) : null;
    }
}
