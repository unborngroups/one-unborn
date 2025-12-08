<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Feasibility extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($feasibility) {
            if (empty($feasibility->feasibility_request_id)) {
                $feasibility->feasibility_request_id = self::generateRequestId($feasibility->client_id);
            }
        });
    }

    public static function generateRequestId($clientId = null)
    {
        return \App\Services\PrefixGenerator::generateFeasibilityId($clientId);
    }

    protected $fillable = [
        'feasibility_request_id',
        'type_of_service',
        'company_id',
        'client_id',
        'pincode',
        'state',
        'district',
        'area',
        'address',
        'spoc_name',
        'spoc_contact1',
        'spoc_contact2',
        'spoc_email',
        'no_of_links',
        'vendor_type',
        'speed',
        'static_ip',
        'static_ip_subnet',
        'expected_delivery',
        'expected_activation',
        'hardware_required',
         'hardware_details',
        'status',
        'created_by',
    ];

    protected $casts = [
        'expected_delivery' => 'date',
        'expected_activation' => 'date',
    'hardware_details' => 'array',

    ];

     // 🧩 Relationship — Each Feasibility has one Feasibility Status
    public function feasibilityStatus()
    {
        return $this->hasOne(FeasibilityStatus::class);
    }

    // 🧩 Relationship — Each Feasibility belongs to one Client
    public function client()
{
    return $this->belongsTo(Client::class, 'client_id');
}

public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by');
}

    // Provide alias for existing controllers referencing createdByUser
    public function createdByUser()
    {
        return $this->createdBy();
    }

// 🧩 Relationship — Each Feasibility can have multiple Purchase Orders
public function purchaseOrders()
{
    return $this->hasMany(PurchaseOrder::class);
}
public function company() {
    return $this->belongsTo(Company::class);
}

  
}
