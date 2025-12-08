<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SlaReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_link_id',
        'month',
        'year',
        'uptime_percentage',
        'downtime_hours',
        'avg_latency_ms',
        'avg_packet_loss',
        'breached',
    ];

    protected $casts = [
        'breached' => 'boolean',
    ];

    public function link()
    {
        return $this->belongsTo(ClientLink::class, 'client_link_id');
    }
}
