<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MakeType extends Model
{
    protected $fillable = ['company_id', 'make_name'];

    public function company() { return $this->belongsTo(Company::class); }
}
