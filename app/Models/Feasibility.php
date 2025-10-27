<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Feasibility extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_of_service',
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
        'expected_delivery',
        'expected_activation',
        'hardware_required',
        'hardware_model_name',
        'status',
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

  
}
