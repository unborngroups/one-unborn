<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
    protected $fillable = ['type_name'];

    public function company() { return $this->belongsTo(Company::class); }
}
