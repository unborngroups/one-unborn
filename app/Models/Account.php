<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'account_group_id',
        'account_name',
        'account_code',
        'opening_balance',
        'balance_type',
        'is_active',
        'status',
        'is_locked',
        'maker_id',
        'checker_id',
        'maker_submitted_at',
        'checker_approved_at',
        'locked_at',
    ];

    public function group()
    {
        return $this->belongsTo(AccountGroup::class,'account_group_id');
    }

    public function maker()
    {
        return $this->belongsTo(User::class, 'maker_id');
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checker_id');
    }
}
