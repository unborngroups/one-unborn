<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number',
        'po_date',
        'feasibility_id',
        'arc_per_link',
        'otc_per_link',
        'static_ip_cost_per_link',
        'no_of_links',
        'contract_period',
        'status',
        // Individual link pricing fields
        'arc_link_1', 'arc_link_2', 'arc_link_3', 'arc_link_4',
        'otc_link_1', 'otc_link_2', 'otc_link_3', 'otc_link_4',
        'static_ip_link_1', 'static_ip_link_2', 'static_ip_link_3', 'static_ip_link_4',
    ];

    protected $casts = [
        'po_date' => 'date',
        'arc_per_link' => 'decimal:2',
        'otc_per_link' => 'decimal:2',
        'static_ip_cost_per_link' => 'decimal:2',
        // Individual link pricing fields
        'arc_link_1' => 'decimal:2', 'arc_link_2' => 'decimal:2', 'arc_link_3' => 'decimal:2', 'arc_link_4' => 'decimal:2',
        'otc_link_1' => 'decimal:2', 'otc_link_2' => 'decimal:2', 'otc_link_3' => 'decimal:2', 'otc_link_4' => 'decimal:2',
        'static_ip_link_1' => 'decimal:2', 'static_ip_link_2' => 'decimal:2', 'static_ip_link_3' => 'decimal:2', 'static_ip_link_4' => 'decimal:2',
    ];

    // Relationship with Feasibility
    public function feasibility()
    {
        return $this->belongsTo(Feasibility::class);
    }

    // Generate PO Number using new prefix system
    public static function generatePONumber($vendorId = null)
    {
        return \App\Services\PrefixGenerator::generatePONumber($vendorId);
    }
}
