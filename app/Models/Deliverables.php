<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\DeliverablePlan;

class Deliverables extends Model
{
    public function deliverablePlans()
    {
        return $this->hasMany(DeliverablePlan::class, 'deliverable_id', 'id')
            ->from('deliverable_plans');
    }
    protected $fillable = [
        'feasibility_id',
        'purchase_order_id',
        'status',
        'delivery_id',
        'site_address',
        'local_contact',
        'state',
        'gst_number',
        'link_type',
        'speed_in_mbps',
        'no_of_links',
        'vendor',
        'circuit_id',
        'client_circuit_id',
        'client_feasibility',
        'vendor_code',
        
        'mode_of_delivery',
        'pppoe_username', 
        'pppoe_password', 'pppoe_vlan',

        // dhcp details
        'dhcp_ip_address',
        'dhcp_vlan',
    
        //    static details
        'static_ip_address',
        'static_vlan',
        'network_ip',
        'static_subnet_mask',
        'static_gateway',
        'usable_ips',
        'static_vlan_tag',
       //payment details    
        'payment_login_url',
        'payment_quick_url',
        'payment_account',
        'payment_username',
        'payment_password',
        'info_ip_address',

        'status_of_link',
        'mtu',
        'wifi_username',
        'wifi_password',
        'lan_ip_1',
        'lan_ip_2',
        'lan_ip_3',
        'lan_ip_4',
        'router_username',
        'router_password',
        'ipsec',
        'phase_1',
        'phase_2',
        'ipsec_interface',
        'otc_extra_charges',
        'otc_bill_file',
        'delivered_at',
        'delivered_by',
        'delivery_notes',
        'arc_cost',
        'otc_cost',
        'static_ip_cost',
        'export_file',
        'asset_id',
        'asset_serial_no',
        'asset_mac_no',
        'po_number',
        'po_date'
    ];

    protected $casts = [
        'date_of_activation' => 'date',
        'date_of_activation_1' => 'date',
        'date_of_activation_2' => 'date',
        'date_of_activation_3' => 'date',
        'date_of_activation_4' => 'date',
        'date_of_expiry_1' => 'date',
        'date_of_expiry_2' => 'date',
        'date_of_expiry_3' => 'date',
        'date_of_expiry_4' => 'date',
        'delivered_at' => 'datetime',
        'otc_extra_charges' => 'decimal:2'
    ];

    public function feasibility(): BelongsTo
    {
        return $this->belongsTo(Feasibility::class, 'feasibility_id');
    }
   
    public static function generateDeliveryId()
    {
        $prefix = 'DEL';
        $year = date('Y');
        $lastRecord = self::whereYear('created_at', $year)
                         ->orderBy('id', 'desc')
                         ->first();
        
        $sequence = $lastRecord ? (int)substr($lastRecord->delivery_id, -4) + 1 : 1;
        
        return $prefix . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($deliverable) {
            if (!$deliverable->delivery_id) {
                $deliverable->delivery_id = self::generateDeliveryId();
            }
        });
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'InProgress');
    }

    public function scopeDelivery($query)
    {
        return $query->where('status', 'Delivery');
    }
    public function purchaseOrder()
{
    return $this->belongsTo(PurchaseOrder::class);
}

}
