<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
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

    public const STALE_TIMEOUT_MINUTES = 10;

    public static function markStaleOffline()
{
    $timeout = config('session.lifetime'); // 30 mins

    self::where('status', 'Online')
        ->whereNotNull('last_activity')
        ->where('last_activity', '<', now()->subMinutes($timeout))
        ->update([
            'status' => 'Offline',
            'logout_time' => now()
        ]);
}
}
