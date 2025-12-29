<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'router_id',
        'interface_name',
        'deliverable_id',
        'link_type',
        'bandwidth',
        'service_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function router()
    {
        return $this->belongsTo(MikrotikRouter::class, 'router_id');
    }

    public function monitoring()
    {
        return $this->hasMany(LinkMonitoringData::class, 'client_link_id');
    }

    public function slaReports()
    {
        return $this->hasMany(SlaReport::class, 'client_link_id');
    }

    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class, 'client_link_id');
    }
}
