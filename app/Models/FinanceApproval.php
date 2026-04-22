<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FinanceApproval extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'action',
        'maker_id',
        'checker_id',
        'status',
        'remarks',
    ];

    public function maker()
    {
        return $this->belongsTo(User::class, 'maker_id');
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checker_id');
    }
}
