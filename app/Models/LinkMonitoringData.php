<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LinkMonitoringData extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_link_id',
        'latency_ms',
        'packet_loss',
        'upload_mbps',
        'download_mbps',
        'is_link_up',
        'collected_at',
    ];

    protected $casts = [
        'is_link_up' => 'boolean',
        'collected_at' => 'datetime',
    ];

    public function link()
    {
        return $this->belongsTo(ClientLink::class, 'client_link_id');
    }
}
