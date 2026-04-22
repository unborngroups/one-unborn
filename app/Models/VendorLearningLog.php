<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorLearningLog extends Model
{
    protected $table = 'vendor_learning_logs';

    protected $fillable = [
        'vendor_name_raw',
        'gstin',
        'matched_vendor_id',
        'confidence',
        'is_verified'
    ];

    protected $casts = [
        'confidence' => 'float',
        'is_verified' => 'boolean',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'matched_vendor_id');
    }
}
