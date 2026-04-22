<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Deliverables;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;  // <-- Add the SoftDeletes trait

    protected $fillable = [
        'po_number',
        'po_date',
        'vendor_id',
        'feasibility_id',
        'company_id',
        'feasibility_request_id', // <-- Added for full formatted ID
        'reused_from_purchase_order_id',
        'duration',
        'arc_per_link',
        'otc_per_link',
        'static_ip_cost_per_link',
        'no_of_links',
        'contract_period',
        'total_cost',
        'import_file',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
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
        'reused_from_purchase_order_id' => 'integer',
    ];

    // Relationship with Feasibility

    public function feasibility()
{
    return $this->belongsTo(Feasibility::class, 'feasibility_id');
}

    // Generate PO Number using new prefix system
    public static function generatePONumber($vendorId = null)
    {
        return \App\Services\PrefixGenerator::generatePONumber($vendorId);
    }

    public function deliverables()
{
    return $this->hasMany(Deliverables::class);
}

public function createdUser()
{
    return $this->belongsTo(User::class,'created_by');
}

public function updatedUser()
{
    return $this->belongsTo(User::class,'updated_by');
}

public function deletedUser()
{
    return $this->belongsTo(User::class,'deleted_by');
}

 public function company()
{
    return $this->belongsTo(Company::class, 'company_id');
}

}