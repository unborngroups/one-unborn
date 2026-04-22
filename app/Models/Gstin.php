<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gstin extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'gstin',
        'trade_name',
        'legal_name',
        'principal_business_address',
        'building_name',
        'building_number',
        'floor_number',
        'street',
        'location',
        'district',
        'city',
        'state',
        'state_code',
        'pincode',
        'status',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the entity (Client or Vendor) that owns the GSTIN
     */
    public function entity()
    {
        return $this->morphTo();
    }
}
