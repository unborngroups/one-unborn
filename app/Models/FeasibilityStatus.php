<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeasibilityStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'feasibility_id',
        // ✅ Vendor 1
        'vendor1_name',
        'vendor1_arc',
        'vendor1_otc',
        'vendor1_static_ip_cost',
        'vendor1_delivery_timeline',
        'vendor1_remarks',

        // ✅ Vendor 2
        'vendor2_name',
        'vendor2_arc',
        'vendor2_otc',
        'vendor2_static_ip_cost',
        'vendor2_delivery_timeline',
        'vendor2_remarks',

        // ✅ Vendor 3
        'vendor3_name',
        'vendor3_arc',
        'vendor3_otc',
        'vendor3_static_ip_cost',
        'vendor3_delivery_timeline',
        'vendor3_remarks',

        // ✅ Vendor 4
        'vendor4_name',
        'vendor4_arc',
        'vendor4_otc',
        'vendor4_static_ip_cost',
        'vendor4_delivery_timeline',
        'vendor4_remarks',

        'created_by',
        'updated_by',
        'deleted_by',
        'status', // Open, InProgress, Closed, Not-Feasible
    ];

    // 🧩 Relationship — Each FeasibilityStatus belongs to one Feasibility
    public function feasibility()
    {
        return $this->belongsTo(Feasibility::class);
    }

    public function createdUser()
{
    return $this->belongsTo(User::class,'created_by');
}

public function updatedUser()
{
    return $this->belongsTo(User::class,'updated_by');
}

public function deletedUser()
{
    return $this->belongsTo(User::class,'deleted_by');
}

}