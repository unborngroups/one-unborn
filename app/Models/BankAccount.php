<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'bank_name','account_name','account_number',
        'ifsc_code','opening_balance','is_active'
    ];

    public function transactions()
    {
        return $this->hasMany(BankTransaction::class);
    }
}
