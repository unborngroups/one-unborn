<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_link_id',
        'alert_type',
        'message',
        'sent_to_email',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function link()
    {
        return $this->belongsTo(ClientLink::class, 'client_link_id');
    }
}
