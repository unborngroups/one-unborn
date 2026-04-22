<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'event_key', // e.g. feasibility_created, password_reset
        'subject',
        'body',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
