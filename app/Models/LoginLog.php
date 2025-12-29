<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LoginLog extends Model
{
    protected $fillable = [
        'user_id', 'login_time', 'logout_time', 'total_minutes', 'status', 'last_activity'
    ];

    protected $dates = ['login_time', 'logout_time', 'last_activity'];
    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
        'last_activity' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public const STALE_TIMEOUT_MINUTES = 15;

    public static function markStaleOffline()
    {
        $deadline = now()->subMinutes(self::STALE_TIMEOUT_MINUTES);

        $staleLogs = self::where('status', 'Online')
            ->whereNotNull('last_activity')
            ->where('last_activity', '<', $deadline)
            ->get();

        foreach ($staleLogs as $log) {
            $logoutTime = $log->last_activity ?? now();
            $minutes = $log->login_time->diffInMinutes($logoutTime);

            $log->update([
                'status' => 'Offline',
                'logout_time' => $logoutTime,
                'total_minutes' => $minutes,
            ]);
        }
    }
    /**
     * Get dynamic status attribute (Online/Offline) based on last activity and timeout.
     */
    /**
     * Get dynamic status attribute (Online/Offline) based on last activity and timeout.
     */
    public function getDynamicStatusAttribute()
    {
        $timeout = self::STALE_TIMEOUT_MINUTES;
        $lastActivity = $this->last_activity ?? $this->login_time;
        // If logout_time is set, always Offline
        if ($this->logout_time !== null) {
            return 'Offline';
        }
        // If last activity within timeout, Online
        if (now()->diffInMinutes($lastActivity) < $timeout) {
            return 'Online';
        }
        return 'Offline';
    }
}
   