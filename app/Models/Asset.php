<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_id','company_id','asset_type_id','make_type_id','model','brand',
        'serial_no','mac_no','procured_from','purchase_date','warranty',
        'po_no','mrp','purchase_cost'
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function assetType() { return $this->belongsTo(AssetType::class); }
    public function makeType() { return $this->belongsTo(MakeType::class); }
}
