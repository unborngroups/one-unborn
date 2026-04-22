<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MikrotikRouter extends Model
{
    use HasFactory;

    protected $fillable = [
        'router_name',
        'management_ip',
        'api_port',
        'username',
        'password',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function links()
    {
        return $this->hasMany(ClientLink::class, 'router_id');
    }
}
