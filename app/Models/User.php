<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\User
 *
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
 */
class User extends Authenticatable
{
    // Relationship to login_logs
    public function loginLogs()
    {
        return $this->hasMany(\App\Models\LoginLog::class, 'user_id');
    }
      use HasFactory, Notifiable;

      protected $table = 'users'; 

    protected $fillable = [
        'name',
        'user_type_id',
        'email',
        'official_email',
    'personal_email',
        'mobile',
        'Date_of_Birth',
        'Date_of_Joining',
        'status',
        'email_template',
        'password',
    'profile_created'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'profile_created' => 'boolean',
    ];

    public function userType()
{
    return $this->belongsTo(UserType::class, 'user_type_id', 'id');
}

// User.php
public function companies()
{
    return $this->belongsToMany(Company::class, 'company_user', 'user_id', 'company_id');
}
//profile.php
public function profile()
{
    return $this->hasOne(\App\Models\Profile::class);
}
public function menuPrivileges()
{
    return $this->hasMany(UserMenuPrivilege::class);
}


}

