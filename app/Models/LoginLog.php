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

    public static function markStaleOffline(int $minutes = self::STALE_TIMEOUT_MINUTES): void
    {
        $cutoff = Carbon::now()->subMinutes($minutes);
        $logoutTime = Carbon::now();

        static::where('status', 'Online')
            ->where('updated_at', '<', $cutoff)
            ->chunkById(100, function ($logs) use ($logoutTime) {
                foreach ($logs as $log) {
                    $totalMinutes = $log->login_time->diffInMinutes($logoutTime);
                    $log->update([
                        'logout_time' => $logoutTime,
                        'total_minutes' => $totalMinutes,
                        'status' => 'Offline',
                    ]);
                }
            });
    }
}
