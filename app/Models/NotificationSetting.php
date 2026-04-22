<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'notify_sla_breach',
        'notify_link_down',
        'notify_high_latency',
        'notify_high_packet_loss',
        'latency_threshold',
        'packet_loss_threshold',
        'cooldown_minutes',
        'extra_recipients',
    ];

    protected $casts = [
        'notify_sla_breach' => 'boolean',
        'notify_link_down' => 'boolean',
        'notify_high_latency' => 'boolean',
        'notify_high_packet_loss' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
