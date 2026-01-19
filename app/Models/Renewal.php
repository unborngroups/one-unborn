<?php

namespace App\Models;
use App\Models\Deliverables;

use Illuminate\Database\Eloquent\Model;

class Renewal extends Model
{
    protected $fillable = [
        'deliverable_id',
        'circuit_id',
        'date_of_renewal',
        'renewal_months',
        'new_expiry_date',
        'alert_date'
    ];

    public function deliverable()
    {
        return $this->belongsTo(Deliverables::class);
    }
}
