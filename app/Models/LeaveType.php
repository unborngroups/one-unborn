<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'leavetype',
        'shortcode',
        'status',
        'description',
    ];

    protected $casts = [
        'status' => 'string',
    ];
}