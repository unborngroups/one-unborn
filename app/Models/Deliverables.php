<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deliverables extends Model
{
    protected $fillable = [
        'feasibility_id',
        'purchase_order_id',   // IMPORTANT FIX
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
        'plans_name',
        'speed_in_mbps_plan',
        'no_of_months_renewal',
        'date_of_activation',
        'date_of_expiry',
        'sla',
        'mode_of_delivery',
        'pppoe_username_1', 'pppoe_password_1', 'pppoe_vlan_1',
'pppoe_username_2', 'pppoe_password_2', 'pppoe_vlan_2',
'pppoe_username_3', 'pppoe_password_3', 'pppoe_vlan_3',
'pppoe_username_4', 'pppoe_password_4', 'pppoe_vlan_4',

        // dhcp details
        'dhcp_ip_address',
        'dhcp_vlan',
        
        //    static details
        'static_ip_address',
        'static_vlan',
        'static_subnet_mask',
        'static_gateway',
        'static_vlan_tag',
       //payment details    
        'payment_login_url',
        'payment_quick_url',
        'payment_account_or_username',
        'payment_password',

        'status_of_link',
        'mtu',
        'wifi_username',
        'wifi_password',
        'lan_ip_1',
        'lan_ip_2',
        'lan_ip_3',
        'lan_ip_4',
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
        'account_id',
        'static_ip_cost',
        'export_file',
        'asset_id',
        'asset_serial_no',
        'po_number',
        'po_date'
    ];

    protected $casts = [
        'date_of_activation' => 'date',
        'delivered_at' => 'datetime',
        'otc_extra_charges' => 'decimal:2'
    ];

    public function feasibility(): BelongsTo
    {
        return $this->belongsTo(Feasibility::class);
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
}
