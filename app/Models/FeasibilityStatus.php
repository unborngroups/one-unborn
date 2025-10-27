<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeasibilityStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'feasibility_id',
        'vendor_name',
        'arc',
        'otc',
        'static_ip_cost',
        'delivery_timeline',
        'status', // Open, In Progress, Closed
    ];

    public function feasibility()
    {
        return $this->belongsTo(Feasibility::class);
    }
}
