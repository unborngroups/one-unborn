<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DeliverablePlan;

class Termination extends Model
{
    protected $fillable = [
        'circuit_id',
        'company_name',
        'address',
        'bandwidth',
        'asset_make',
        'asset_mac',
        'asset_serial',
        'date_of_activation',
        'date_of_delivered',
        'date_of_last_renewal',
        'date_of_expiry',
        'termination_request_date',
        'termination_requested_by',
        'termination_request_document',
        'termination_date',
        'status',
    ];

    public function deliverable_plans()
    {
        return $this->belongsTo(DeliverablePlan::class);
    }
}
