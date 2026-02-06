<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DeliverablePlan;
use App\Models\CompanySetting;
use App\Helpers\EmailHelper;
use App\Models\Company;     
use App\Models\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

use App\Models\{
    Deliverables,
    PurchaseOrder,
    FeasibilityStatus,
    ClientLink,
    Asset,
    Vendor
};
use App\Helpers\TemplateHelper;
use Carbon\Carbon;


class DeliverablesController extends Controller
{
    /**
     * AJAX: Calculate subnet/network details for a given IP and subnet mask.
     * Route: /calculate-subnet (GET)
     * Params: ip, subnet (e.g. /28)
     * Returns: JSON { network_ip, gateway, subnet_mask, usable_ips }
     */
    public function calculateSubnet(Request $request)
    {
        $ip = $request->input('ip');
        $subnet = $request->input('subnet');
        if (!$ip || !$subnet) {
            return response()->json(['error' => 'Missing IP or subnet'], 400);
        }

        // Convert subnet (e.g. /28) to mask
        $prefix = (int)str_replace('/', '', $subnet);
        if ($prefix < 1 || $prefix > 32) {
            return response()->json(['error' => 'Invalid subnet'], 400);
        }
        $mask = long2ip(-1 << (32 - $prefix));

        // Calculate network address
        $ip_long = ip2long($ip);
        $mask_long = ip2long($mask);
        if ($ip_long === false || $mask_long === false) {
            return response()->json(['error' => 'Invalid IP'], 400);
        }
        $network_long = $ip_long & $mask_long;
        $network_ip = long2ip($network_long);

        // Calculate broadcast address
        $broadcast_long = $network_long | (~$mask_long & 0xFFFFFFFF);
        $broadcast_ip = long2ip($broadcast_long);

        // Gateway: usually first usable IP (network + 1)
        $gateway_ip = long2ip($network_long + 1);

        // Usable IP range
        // - For /32: only the single IP is considered usable
        // - For /31: both addresses are typically usable in point-to-point links
        // - For all others: hosts from network+1 to broadcast-1
        if ($prefix === 32) {
            $first_usable = $network_long;
            $last_usable = $network_long;
        } elseif ($prefix === 31) {
            $first_usable = $network_long;
            $last_usable = $broadcast_long;
        } else {
            $first_usable = $network_long + 1;
            $last_usable = $broadcast_long - 1;
        }
        $usable_ips = [];
        if ($last_usable >= $first_usable) {
            for ($i = $first_usable; $i <= $last_usable; $i++) {
                $usable_ips[] = long2ip($i);
            }
        }

        // Usable IPs as string (show range or single IP)
        $usable_ips_str = count($usable_ips) > 1
            ? $usable_ips[0] . ' - ' . $usable_ips[count($usable_ips)-1]
            : (count($usable_ips) === 1 ? $usable_ips[0] : '');

        return response()->json([
            'network_ip' => $network_ip,
            'gateway' => $gateway_ip,
            'subnet_mask' => $mask,
            'usable_ips' => $usable_ips_str,
            'broadcast_ip' => $broadcast_ip,
        ]);
    }

    // Helper to get sequence for circuit_id
    public function getCircuitSequence($companyName, $clientShortName, $state, $year)
    {
        return Deliverables::whereYear('created_at', $year)
            ->whereHas('feasibility.company', function($q) use ($companyName) {
                $q->where('company_name', $companyName);
            })
            ->whereHas('feasibility.client', function($q) use ($clientShortName) {
                $q->where('short_name', $clientShortName);
            })
            ->where('state', $state)
            ->count() + 1;
    }


    // Operations Deliverables Views
    public function operationsOpen()
        {
             return $this->page('open', 'Open', 'operations Deliverables'); 
             
        }
    public function operationsInProgress() { return $this->page('inprogress', 'InProgress', 'operations Deliverables'); }
    public function operationsDelivery()   { return $this->page('delivery', 'Delivery', 'operations Deliverables'); }

    // S&M Deliverables Views
    public function smOpen()       { return $this->page('open', 'Open', 'Sm Deliverables'); }
    public function smInProgress() { return $this->page('inprogress', 'InProgress', 'Sm Deliverables'); }
    public function smDelivery()   { 
        
    return $this->page('delivery', 'Delivery', 'Sm Deliverables'); }

    private function page(string $view, string $status, $menuName = 'operations Deliverables')
    {
        $perPage = request()->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $search = request()->input('search');

        $records = Deliverables::with([
            'feasibility.client',
            'feasibility.company',
            'feasibility.feasibilityStatus',
            'deliverablePlans',
        ])
        ->where('status', $status)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%$search%")
                  ->orWhere('feasibility_id', 'like', "%$search%")
                  ->orWhere('circuit_id', 'like', "%$search%") // Search main deliverables.circuit_id
                  ->orWhereHas('feasibility', function ($fq) use ($search) {
                      $fq->where('feasibility_request_id', 'like', "%$search%")
                         ->orWhere('address', 'like', "%$search%")
                         ->orWhereHas('client', function ($cq) use ($search) {
                             $cq->where('client_name', 'like', "%$search%")
                                 //  ->orWhere('mobile', 'like', "%$search%")
                                 //  ->orWhere('email', 'like', "%$search%")
                                 ->orWhere('gstin', 'like', "%$search%")
                                 ->orWhere('location_id', 'like', "%$search%")
                                 ->orWhere('status_of_link', 'like', "%$search%")
                                 //  ->orWhere('mode_of_delivery', 'like', "%$search%")
                                 ->orWhere('circuit_id', 'like', "%$search%")
                                 ->orWhere('client_circuit_id', 'like', "%$search%")
                                 ->orWhere('client_feasibility', 'like', "%$search%")
                                 ->orWhere('vendor_code', 'like', "%$search%")
                                 ->orWhere('asset_serial_no', 'like', "%$search%")
                                 ->orWhere('asset_mac_no', 'like', "%$search%")
                                 ->orWhere('otc_extra_charges', 'like', "%$search%")
                                 ->orWhere('ipsec', 'like', "%$search%")
                                 ;
                         })
                         ->orWhereHas('company', function ($cq) use ($search) {
                             $cq->where('company_name', 'like', "%$search%")
                                 ;
                         });
                  })
                  // Add search for deliverable_plans (plan name, etc)
                  ->orWhereHas('deliverablePlans', function ($dpq) use ($search) {
                      $dpq->where('plans_name', 'like', "%$search%")
                          ->orWhere('circuit_id', 'like', "%$search%")
                          ->orWhere('speed_in_mbps_plan', 'like', "%$search%")
                          ->orWhere('sla', 'like', "%$search%")
                          ->orWhere('status_of_link', 'like', "%$search%")
                          ->orWhere('mode_of_delivery', 'like', "%$search%")
                          ->orWhere('client_circuit_id', 'like', "%$search%")
                          ->orWhere('client_feasibility', 'like', "%$search%")
                          ->orWhere('vendor_code', 'like', "%$search%")
                          ->orWhere('mtu', 'like', "%$search%")
                          ->orWhere('wifi_username', 'like', "%$search%")
                          ->orWhere('wifi_password', 'like', "%$search%")
                          ->orWhere('router_username', 'like', "%$search%")
                          ->orWhere('router_password', 'like', "%$search%")
                          ->orWhere('payment_login_url', 'like', "%$search%")
                          ->orWhere('payment_quick_url', 'like', "%$search%")
                          ->orWhere('payment_account', 'like', "%$search%")
                          ->orWhere('payment_username', 'like', "%$search%")
                          ->orWhere('payment_password', 'like', "%$search%")
                          ->orWhere('pppoe_username', 'like', "%$search%")
                          ->orWhere('pppoe_password', 'like', "%$search%")
                          ->orWhere('pppoe_vlan', 'like', "%$search%")
                          ->orWhere('dhcp_ip_address', 'like', "%$search%")
                          ->orWhere('dhcp_vlan', 'like', "%$search%")
                          ->orWhere('static_ip_address', 'like', "%$search%")
                          ->orWhere('static_vlan', 'like', "%$search%")
                          ->orWhere('network_ip', 'like', "%$search%")
                          ->orWhere('static_subnet_mask', 'like', "%$search%")
                          ->orWhere('static_gateway', 'like', "%$search%")
                          ->orWhere('usable_ips', 'like', "%$search%")
                          ->orWhere('remarks', 'like', "%$search%")
                          ;
                  });
            });
        })
        ->orderBy('id','desc')
        ->paginate($perPage)
        ->appends(request()->except('page'));

        return view("operations.deliverables.$view", [
            'records' => $records,
            'permissions' => TemplateHelper::getUserMenuPermissions($menuName),
            'search' => $search,
        ]);
    }

    /* ===================== VIEW / EDIT ===================== */

    public function operationsView($id)
{
    return view('operations.deliverables.view', [
        'record' => Deliverables::with([
            'feasibility.client',
            'feasibility.company',
            'deliverablePlans'
        ])->findOrFail($id)
    ]);
}

    public function operationsEdit($id)
    {
        $record = Deliverables::with('feasibility.client')->findOrFail($id);

        // if ($record->status === 'Delivery') {
        //     return back()->with('error', 'Delivered records cannot be edited');
        // }

        // Map per-link vendor details from the closed feasibility to deliverable plans
        $linkVendors = [];
        $feasibilityStatus = FeasibilityStatus::where('feasibility_id', $record->feasibility_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($feasibilityStatus) {
            $linkCount = $record->feasibility->no_of_links ?? 1;

            for ($i = 1; $i <= $linkCount; $i++) {
                $nameField = "vendor{$i}_name";
                $vendorName = trim($feasibilityStatus->{$nameField} ?? '');

                // Skip if no vendor or self-vendor placeholder
                if ($vendorName === '' || strcasecmp($vendorName, 'self') === 0) {
                    $linkVendors[$i] = null;
                    continue;
                }

                $linkVendors[$i] = Vendor::where('vendor_name', $vendorName)->first();
            }
        }

        $hardwareDetails = $record->feasibility->hardware_details ?? [];
        if (is_array($hardwareDetails)) {
            // Already casted by Eloquent $casts
        } else {
            $hardwareDetails = json_decode($hardwareDetails ?: '[]', true);
        }
        return view('operations.deliverables.edit', [
            'record' => $record,
            'assetOptions' => Asset::whereNotNull('asset_id')
                ->whereNotNull('serial_no')
                ->select('asset_id', 'serial_no', 'mac_no', DB::raw("'Inventory' as vendor_name"))
                ->orderBy('asset_id')->get(),
            'hardwareDetails' => $hardwareDetails,
            'linkVendors' => $linkVendors,
        ]);
    }

    /* ===================== SAVE ===================== */

    public function operationsSave(Request $request, $id)
    {
        $deliverable = Deliverables::findOrFail($id);
        $linkCount = $deliverable->feasibility->no_of_links ?? 1;
        $rules = [
            'lan_ip_1' => 'required'
        ];
        for ($i = 1; $i <= $linkCount; $i++) {
            $rules["mode_of_delivery_$i"] = 'required|string';
            $rules["mtu_$i"] = 'required';
        }
        $request->validate($rules);

        // Only update the main deliverable record once
        $data = $request->only([
            'status_of_link','mode_of_delivery','circuit_id',
            'client_circuit_id','client_feasibility','vendor_code',
            'mtu','wifi_username','wifi_password',
            'router_username','router_password',
            'lan_ip_1','lan_ip_2','lan_ip_3','lan_ip_4',
            'asset_id','asset_serial_no','asset_mac_no','otc_extra_charges','payment_login_url',
            'payment_quick_url','payment_account','payment_username','payment_password','ipsec',
            'phase_1','phase_2','ipsec_interface'
        ]);

        /* ===================== FILE UPLOADS ===================== */

$uploadMap = [
    'speed_test_file' => 'images/deliverable_speed_test',
    'ping_report_dns_file' => 'images/deliverable_pingreportdns',
    'ping_report_gateway_file' => 'images/deliverable_pingreportgateway',
    'onu_ont_device_file' => 'images/deliverable_onu_ontdevice',
    'static_ip_file' => 'images/deliverable_staticip',
    'export_file' => 'images/deliverable_export',
    'otc_bill_file' => 'images/deliverable_otcbill',
];

foreach ($uploadMap as $field => $folder) {
    if ($request->hasFile($field)) {
        $file = $request->file($field);
        $filename = time() . '_' . $file->getClientOriginalName();
        $destination = public_path($folder);

        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);

        // ✅ store RELATIVE path
        $data[$field] = $folder . '/' . $filename;
    }
}


        $data['ipsec'] = $request->input('ipsec', 'No');
        $data['status'] = $request->action === 'submit' ? 'Delivery' : 'InProgress';
        $deliverable->update($data);
            // Save per-link plan info in deliverable_plans table
            // Fetch existing plans before delete to preserve circuit_id
            $existingPlansMap = $deliverable->deliverablePlans->keyBy('link_number');
            DeliverablePlan::where('deliverable_id', $deliverable->id)->delete();

            // Define variables needed for circuit_id generation
            $feasibility = $deliverable->feasibility; // or fetch as needed
            $companyName = $feasibility->company->company_name ?? '';
            $clientShortName = $feasibility->client->short_name ?? $feasibility->client->client_name ?? '';
            $state = $feasibility->state ?? '';
            $year = date('Y');
            $stateAbbr = [
                'Andhra Pradesh' => 'AP', 'Arunachal Pradesh' => 'AR', 'Assam' => 'AS', 'Bihar' => 'BR',
                'Chhattisgarh' => 'CG', 'Goa' => 'GA', 'Gujarat' => 'GJ', 'Haryana' => 'HR', 'Himachal Pradesh' => 'HP',
                'Jammu and Kashmir' => 'JK', 'Jharkhand' => 'JH', 'Karnataka' => 'KA', 'Kerala' => 'KL', 'Madhya Pradesh' => 'MP',
                'Maharashtra' => 'MH', 'Manipur' => 'MN', 'Meghalaya' => 'ML', 'Mizoram' => 'MZ', 'Nagaland' => 'NL',
                'Orissa' => 'OR', 'Punjab' => 'PB', 'Rajasthan' => 'RJ', 'Sikkim' => 'SK', 'Tamil Nadu' => 'TN', 'Tripura' => 'TR',
                'Uttarakhand' => 'UK', 'Uttar Pradesh' => 'UP', 'West Bengal' => 'WB', 'Telangana' => 'TS',
                'Andaman and Nicobar Islands' => 'AN', 'Chandigarh' => 'CH', 'Dadra and Nagar Haveli' => 'DH', 'Daman and Diu' => 'DD',
                'Delhi' => 'DL', 'Lakshadweep' => 'LD', 'Pondicherry' => 'PY',
            ];
            $maxSeq = DeliverablePlan::whereYear('created_at', $year)
                ->where('circuit_id', 'like', substr($year, -2).'%')
                ->selectRaw("MAX(CAST(RIGHT(circuit_id, 4) AS UNSIGNED)) as max_seq")
                ->value('max_seq');
            $serial = $maxSeq ? intval($maxSeq) : 0;

            // Build a map of per-link vendor snapshots from feasibility status
            $linkVendorsForSave = [];
            $feasibilityStatusForSave = FeasibilityStatus::where('feasibility_id', $deliverable->feasibility_id)
                ->orderBy('id', 'desc')
                ->first();

            if ($feasibilityStatusForSave) {
                for ($i = 1; $i <= $linkCount; $i++) {
                    $nameField = "vendor{$i}_name";
                    $vendorName = trim($feasibilityStatusForSave->{$nameField} ?? '');

                    if ($vendorName === '' || strcasecmp($vendorName, 'self') === 0) {
                        $linkVendorsForSave[$i] = null;
                        continue;
                    }
                    $linkVendorsForSave[$i] = Vendor::where('vendor_name', $vendorName)->first();
                }
            }

            for ($i = 1; $i <= $linkCount; $i++) {
                $serial++;
                $vendorSnapshot = $linkVendorsForSave[$i] ?? null;
                // Preserve circuit_id if editing, only generate if missing
                $existingPlan = $existingPlansMap->get($i);
                $planData = [
                    'deliverable_id' => $deliverable->id,
                    'link_number' => $i,
                    'vendor_name' => $vendorSnapshot->vendor_name ?? null,
                    'vendor_email' => $vendorSnapshot->contact_person_email ?? null,
                    'vendor_contact' => $vendorSnapshot->contact_person_mobile ?? null,
                    'circuit_id' => $existingPlan ? $existingPlan->circuit_id : $this->generateCircuitId($companyName, $clientShortName, $state, $year, $serial),
                    'plans_name' => $request->input('plans_name_' . $i),
                    'speed_in_mbps_plan' => $request->input('speed_in_mbps_plan_' . $i),
                    'no_of_months_renewal' => $request->input('no_of_months_renewal_' . $i),
                    'date_of_activation' => $this->date($request->input('date_of_activation_' . $i)),
                    'date_of_expiry' => $this->date($request->input('date_of_expiry_' . $i)),
                    'sla' => $request->input('sla_' . $i),

                    // New fields
                    'status_of_link' => $request->input('status_of_link_' . $i, 'Active'),
                    'mode_of_delivery' => $request->input('mode_of_delivery_' . $i),
                    'client_circuit_id' => $request->input('client_circuit_id_' .  $i),
                    'client_feasibility' => $request->input('client_feasibility_' . $i),
                    'vendor_code' => $request->input('vendor_code_' . $i),
                    'mtu' => $request->input('mtu_' . $i),
                    'wifi_username' => $request->input('wifi_username_' . $i),
                    'wifi_password' => $request->input('wifi_password_' . $i),
                    'router_username' => $request->input('router_username_' . $i),  
                    'router_password' => $request->input('router_password_' . $i),
                    'payment_login_url' => $request->input('payment_login_url_' . $i),
                    'payment_quick_url' => $request->input('payment_quick_url_' . $i),
                    'payment_account' => $request->input('payment_account_' . $i),
                    'payment_username' => $request->input('payment_username_' . $i),
                    'payment_password' => $request->input('payment_password_' . $i),
                    'pppoe_username' => $request->input('pppoe_username_' . $i),
                    'pppoe_password' => $request->input('pppoe_password_' . $i),
                    'pppoe_vlan' => $request->input('pppoe_vlan_' . $i),
                    'dhcp_ip_address' => $request->input('dhcp_ip_address_' . $i),
                    'dhcp_vlan' => $request->input('dhcp_vlan_' . $i),
                    'static_ip_address' => $request->input('static_ip_address_' . $i),
                    'static_vlan_tag' => $request->input('static_vlan_tag_' . $i),
                    'network_ip' => $request->input('network_ip_' . $i),
                    'static_subnet_mask' => $request->input('static_subnet_mask_' . $i),
                    'static_gateway' => $request->input('gateway_' . $i),
                    'usable_ips' => $request->input('usable_ips_' . $i),
                    'remarks' => $request->input('remarks_' . $i),
                ];
                DeliverablePlan::create($planData);
            }

        /* ---- CLIENT LINK ---- */

        $serviceType = $feasibility->type_of_service ?? null;

        if ($data['status'] === 'Delivery') {

        $companySetting = CompanySetting::first();

if (!$companySetting) {
    Log::error('Company settings not found');
}


            // Use first plan's speed as bandwidth summary for ClientLink
            $firstPlan = $deliverable->deliverablePlans()->orderBy('link_number')->first();
            $bandwidth = $firstPlan->speed_in_mbps_plan ?? null;

            ClientLink::updateOrCreate(
                [
                    'deliverable_id' => $deliverable->id,
                ],
                [
                    'deliverable_id' => $deliverable->id,
                    'service_id'     => $deliverable->circuit_id,
                    'client_id'      => $deliverable->feasibility->client->id,
                    'link_type'      => $deliverable->link_type ?? 'Unknown',
                    'bandwidth'      => $bandwidth,
                    'interface_name' => $request->input('mode_of_delivery_1'),
                    'router_id'      => $deliverable->router_id,
                    'status'         => 'active',
                ]
            );


            $feasibility = $deliverable->feasibility ?? null;
            $client = $feasibility ? $feasibility->client : null;
            if (!$client) {
                Log::error('No client found for deliverable ID: ' . $deliverable->id);
                return back()->with('error', 'Client not found for this deliverable.');
            }
            $to = $client->delivered_email;
            $cc = $client->delivered_cc;
       // $templateKey = 'deliverable_delivered'; // Use your event_key from Template Master
            // $client = $deliverable->feasibility->client_i;
            $company = $deliverable->feasibility->company_name;

            $to = is_string($to)
    ? array_map('trim', preg_split('/[;,]/', $to))
    : $to;

$cc = is_string($cc)
    ? array_map('trim', preg_split('/[;,]/', $cc))
    : $cc;

    $templateKey = 'deliverable_delivered';

            // $from = $companySetting->delivery_mail_from_address ?? config('mail.from.address');
            // Log::info('DEBUG: ping_report_gateway_file value', ['value' => $firstPlan->ping_report_gateway_file]);
            $mediaFields = [
                'speed_test_file',
                'ping_report_gateway_file',
                'ping_report_dns_file',
                'onu_ont_device_file',
                'static_ip_file',
            ];
            $inlineAttachments = [];
            $mediaUrls = [];
            foreach ($mediaFields as $fieldName) {
                $media = $this->preparePublicAsset($deliverable->{$fieldName});
                $mediaUrls[$fieldName] = $media['asset'];
                if ($media['path'] && file_exists($media['path'])) {
                    $inlineAttachments[] = [
                        'field' => $fieldName,
                        'path' => $media['path'],
                    ];
                }
            }

            $templateData = [
                'client_id' => $deliverable->feasibility->client->client_name ?? '',
                'company_id' => $deliverable->feasibility->company->company_name ?? '',
                'circuit_id' => $firstPlan->circuit_id ?? '',
                'location_id' => $deliverable->feasibility->location_id ?? '',
                'address' => $deliverable->feasibility->address ?? '',
                'speed' => $firstPlan->speed_in_mbps_plan ?? '',
                // Add more fields as needed for your template

                'pppoe_username'     => $firstPlan->pppoe_username ?? '',
                'pppoe_password'     => $firstPlan->pppoe_password ?? '',
                'static_ip_address'  => $firstPlan->static_ip_address ?? '',
                'speed_test_file' => $mediaUrls['speed_test_file'] ?? '',
                'ping_report_gateway_file' => $mediaUrls['ping_report_gateway_file'] ?? '',
                'ping_report_dns_file' => $mediaUrls['ping_report_dns_file'] ?? '',
                'onu_ont_device_file' => $mediaUrls['onu_ont_device_file'] ?? '',
                'static_ip_file' => $mediaUrls['static_ip_file'] ?? '',
            ];

            if (!empty($inlineAttachments)) {
                $templateData['_inline_attachments'] = $inlineAttachments;
            }

            // Log::info('Email template data', $templateData);
            $emailSent = false;

if ($companySetting && $companySetting->delivery_email_check) {

    Log::info('Delivery email enabled, sending mail');
            $emailSent = EmailHelper::sendDynamicEmail(
    $to,
    $templateKey,
    $templateData,
    $cc,
    null,
    'delivery'
);
} else {
    Log::info('Delivery email disabled via settings');
}
            // Auto-create renewal entry if not exists
            foreach ($deliverable->deliverablePlans as $plan) {
                $activation = $plan->date_of_activation;
                $months = $plan->no_of_months_renewal;
                $circuitId = $plan->circuit_id;
                $existingRenewal = \App\Models\Renewal::where('deliverable_id', $deliverable->id)
                    ->where('circuit_id', $circuitId)
                    ->first();
                if (!$existingRenewal && $activation && $months && $circuitId) {
                    $renewalDate = \Carbon\Carbon::parse($activation);
                    $expiry = $renewalDate->copy()->addMonths((int)$months)->subDay();
                    $alertDate = $expiry->copy()->subDay();
                    \App\Models\Renewal::create([
                        'deliverable_id' => $deliverable->id,
                        'circuit_id' => $circuitId,
                        'date_of_renewal' => $renewalDate->format('Y-m-d'),
                        'renewal_months' => $months,
                        'new_expiry_date' => $expiry->format('Y-m-d'),
                        'alert_date' => $alertDate->format('Y-m-d'),
                    ]);
                }
            }
            // Redirect logic for Delivery
            if ($serviceType === 'ILL') {
                $msg = $emailSent
                    ? 'Deliverable saved successfully and email is sent to the client successfully'
                    : 'Deliverable saved successfully but email is not sent to the client successfully';
                return redirect()
                    ->route('operations.deliverables.acceptance.show', $deliverable->id)
                    ->with('success', $msg);
            }

            $msg = $emailSent
                ? 'Deliverable saved successfully and email is sent to the client successfully'
                : 'Deliverable saved successfully but email is not sent to the client successfully';
            return redirect()->route('operations.deliverables.delivery')
                ->with('success', $msg);
        }
         else {
            // For InProgress, no email, simple message
            return redirect()->route('operations.deliverables.inprogress')
                ->with('success', 'Deliverable saved successfully');
        }
    }

    // Acceptance list page (show all ILL deliverables that reached Delivery)
    public function operationsAcceptance()
    {
        $perPage = request()->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $records = Deliverables::with([
                'feasibility.client',
                'feasibility.company',
                'feasibility.feasibilityStatus',
            ])
            ->where('status', 'Delivery')
            ->whereHas('feasibility', function ($q) {
                $q->where('type_of_service', 'ILL');
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return view('operations.deliverables.acceptance', [
            'records' => $records,
            'permissions' => TemplateHelper::getUserMenuPermissions('operations Deliverables'),
        ]);
    }

    // Acceptance page for a specific deliverable
    public function operationsAcceptanceShow($id)
    {
        $perPage = request()->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $records = Deliverables::with([
                'feasibility.client',
                'feasibility.company',
                'feasibility.feasibilityStatus',
            ])
            ->where('status', 'Delivery')
            ->whereHas('feasibility', function ($q) {
                $q->where('type_of_service', 'ILL');
            })
            ->where('id', $id)
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return view('operations.deliverables.acceptance', [
            'records' => $records,
            'permissions' => TemplateHelper::getUserMenuPermissions('operations Deliverables'),
        ]);
    }

    // Helper to generate circuit_id
    public function generateCircuitId($companyName, $clientShortName, $state, $year, $serial )
    {
        // State abbreviation mapping (reuse from CircuitIdHelper or define here)
        $stateAbbr = [
            'Andhra Pradesh' => 'AP', 'Arunachal Pradesh' => 'AR', 'Assam' => 'AS', 'Bihar' => 'BR',
            'Chhattisgarh' => 'CG', 'Goa' => 'GA', 'Gujarat' => 'GJ', 'Haryana' => 'HR', 'Himachal Pradesh' => 'HP',
            'Jammu and Kashmir' => 'JK', 'Jharkhand' => 'JH', 'Karnataka' => 'KA', 'Kerala' => 'KL', 'Madhya Pradesh' => 'MP',
            'Maharashtra' => 'MH', 'Manipur' => 'MN', 'Meghalaya' => 'ML', 'Mizoram' => 'MZ', 'Nagaland' => 'NL',
            'Orissa' => 'OR', 'Punjab' => 'PB', 'Rajasthan' => 'RJ', 'Sikkim' => 'SK', 'Tamil Nadu' => 'TN', 'Tripura' => 'TR',
            'Uttarakhand' => 'UK', 'Uttar Pradesh' => 'UP', 'West Bengal' => 'WB', 'Telangana' => 'TS',
            'Andaman and Nicobar Islands' => 'AN', 'Chandigarh' => 'CH', 'Dadra and Nagar Haveli' => 'DH', 'Daman and Diu' => 'DD',
            'Delhi' => 'DL', 'Lakshadweep' => 'LD', 'Pondicherry' => 'PY',
        ];
        $yy = substr($year, -2);
        $company = strtoupper(substr($companyName, 0, 3));
        $client = strtoupper(substr($clientShortName, 0, 3));
        $stateCode = isset($stateAbbr[$state]) ? $stateAbbr[$state] : strtoupper(substr($state, 0, 2));
        $serial  = str_pad($serial , 4, '0', STR_PAD_LEFT);
        return "$yy$company$client$stateCode$serial";
    }

    // Example usage in deliverable creation
    public function createFromFeasibility($feasibilityId)
    {
        $feasibility = \App\Models\Feasibility::with('client', 'company')->findOrFail($feasibilityId);
        $companyName = $feasibility->company->company_name ?? '';
        $clientShortName = $feasibility->client->short_name ?? $feasibility->client->client_name ?? '';
        $state = $feasibility->state ?? '';
        $year = date('Y');
        $serial  = Deliverables::whereYear('created_at', $year)
            ->whereHas('feasibility.company', function($q) use ($companyName) {
                $q->where('company_name', $companyName);
            })
            ->whereHas('feasibility.client', function($q) use ($clientShortName) {
                $q->where('short_name', $clientShortName);
            })
            ->where('state', $state)
            ->count() + 1;
        $circuit_id = $this->generateCircuitId($companyName, $clientShortName, $state, $year, $serial );
        // ...create deliverable using $circuit_id...
        // Example:
        // Deliverables::create([
        //     'feasibility_id' => $feasibility->id,
        //     'circuit_id' => $circuit_id,
        //     ...other fields...
        // ]);
        // ...existing code...
    }
   
    /* ===================== HELPERS ===================== */

    private function preparePublicAsset(?string $relativePath): array
    {
        if (!$relativePath) {
            return ['asset' => '', 'path' => ''];
        }

        // If the path is already an absolute filesystem path, use it directly.
        if (preg_match('#^[A-Za-z]:\\\\#', $relativePath) || str_starts_with($relativePath, '\\')) {
            return [
                'asset' => '',
                'path' => $relativePath,
            ];
        }

        $cleanPath = ltrim($relativePath, '/');
        if (str_starts_with($cleanPath, 'public/')) {
            $cleanPath = substr($cleanPath, 7);
        }

        return [
            'asset' => asset($cleanPath),
            'path' => public_path($cleanPath),
        ];
    }

    private function date($v) {
        return $v ? Carbon::parse(str_replace('/','-',$v))->format('Y-m-d') : null;
    }

    private function isStatic($mode) {
        return in_array($mode,['Static','Static IP'],true);
    }

}