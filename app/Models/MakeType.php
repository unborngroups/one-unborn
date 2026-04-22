<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MakeType extends Model
{
    protected $fillable = ['make_name'];

    public function company() { return $this->belongsTo(Company::class); }
}
