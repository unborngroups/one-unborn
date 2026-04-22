<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    protected $fillable = [
        'item_name',
        'item_description',
        'item_rate',
        'hsn_sac_code',
        'usage_unit',
        'status',
        
    ];

    
}
