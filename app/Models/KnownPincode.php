<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnownPincode extends Model
{
       protected $fillable = ['pincode', 'state', 'district', 'post_office'];
}
