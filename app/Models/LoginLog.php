<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LoginLog extends Model
{
    protected $fillable = [
        'user_id', 'login_time', 'logout_time', 'total_minutes', 'status'
    ];

    protected $dates = ['login_time', 'logout_time'];
    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
