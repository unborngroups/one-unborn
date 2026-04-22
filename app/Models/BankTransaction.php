<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    protected $fillable = [
        'bank_account_id','transaction_date',
        'type','amount','reference','narration','is_reconciled'
    ];

    public function bank()
    {
        return $this->belongsTo(BankAccount::class,'bank_account_id');
    }
}
