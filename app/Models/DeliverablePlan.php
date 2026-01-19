<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliverablePlan extends Model
{
    protected $fillable = [
        'deliverable_id',
        'link_number',
        'vendor_name',
        'vendor_email',
        'vendor_contact',
        'circuit_id',
        'plans_name',
        'speed_in_mbps_plan',
        'no_of_months_renewal',
        'date_of_activation',
        'date_of_expiry',
        'sla',
        // New fields
        'status_of_link',
        'mode_of_delivery',
        'client_circuit_id',
        'client_feasibility',
        'vendor_code',
        'mtu',
        'wifi_username',
        'wifi_password',    
        'router_username',  
        'router_password',
        'payment_login_url',
        'payment_quick_url',
        'payment_account',
        'payment_username',
        'payment_password',
        'pppoe_username',
        'pppoe_password',
        'pppoe_vlan',
        'dhcp_ip_address',  
        'dhcp_vlan',
        'static_ip_address',
        'static_vlan',
        'network_ip',
        'static_subnet_mask',
        'static_gateway',
        'usable_ips',
        'remarks',
        
    ];

    protected $casts = [
        'date_of_activation' => 'date',
        'date_of_expiry' => 'date',
    ];

    public function deliverable()
    {
        return $this->belongsTo(Deliverables::class);
    }
}
