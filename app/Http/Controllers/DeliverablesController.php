<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use App\Models\PurchaseOrder;
use App\Models\Deliverables;
use App\Models\ClientLink;
use App\Models\Vendor;
use App\Helpers\TemplateHelper;
use IPLib\Address\IPv4;
use Carbon\Carbon;


class DeliverablesController extends Controller
{
    // ====================================
    // LIST PAGES
    // ====================================

    public function operationsOpen()
    {
        return $this->renderDeliverablesPage('open', 'Open', $this->deliverablesPermissions('operations Deliverables'));
    }

    public function operationsInProgress()
    {
        return $this->renderDeliverablesPage('inprogress', 'InProgress', $this->deliverablesPermissions('operations Deliverables'));
    }

    public function operationsDelivery()
    {
        return $this->renderDeliverablesPage('delivery', 'Delivery', $this->deliverablesPermissions('operations Deliverables'));
    }

    public function smOpen()
    {
        return $this->renderDeliverablesPage('open', 'Open', $this->deliverablesPermissions('sm Deliverables'));
    }

    public function smInProgress()
    {
        return $this->renderDeliverablesPage('inprogress', 'InProgress', $this->deliverablesPermissions('sm Deliverables'));
    }

    public function smDelivery()
    {
        return $this->renderDeliverablesPage('delivery', 'Delivery', $this->deliverablesPermissions('sm Deliverables'));
    }

    private function renderDeliverablesPage(string $view, string $status, object $permissions)
    {
        $data = $this->loadDeliverablesByStatus($status);
        $data['permissions'] = $permissions;
        return view("operations.deliverables.{$view}", $data);
    }

    private function deliverablesPermissions(string $menuName): object
    {
        return TemplateHelper::getUserMenuPermissions($menuName) ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];
    }

    private function loadDeliverablesByStatus(string $status): array
    {
        $deliverables = Deliverables::orderBy('id', 'asc')->get();

        $records = Deliverables::with([
            'feasibility',
            'feasibility.client',
            'feasibility.company',
            'feasibility.feasibilityStatus'
        ])
        ->where('status', $status)
        ->orderBy('id', 'asc')
        ->get();

        return compact('records', 'deliverables');
    }

    // ====================================
    // VIEW + EDIT
    // ====================================

    public function operationsView($id)
    {

        $record = Deliverables::with(['feasibility', 'feasibility.client'])->findOrFail($id);
        return view('operations.deliverables.view', compact('record'));
    }

    public function operationsEdit($id)
    {
        $record = Deliverables::with(['feasibility', 'feasibility.client'])->findOrFail($id);

        if ($record->status === 'Delivery') {
            return redirect()->route('operations.deliverables.delivery')
                ->with('error', 'Delivered records cannot be edited.');
        }

        $assetOptions = Vendor::whereNotNull('asset_id')
            ->whereNotNull('serial_no')
            ->orderBy('asset_id')
            ->get(['asset_id', 'serial_no', 'vendor_name']);

        $hardwareDetails = $record->feasibility->hardware_details;
        if (is_string($hardwareDetails)) {
            $hardwareDetails = json_decode($hardwareDetails, true) ?: [];
        }
        if (!is_array($hardwareDetails)) {
            $hardwareDetails = [];
        }

        return view('operations.deliverables.edit', compact('record', 'assetOptions', 'hardwareDetails'));
    }

    // ====================================
    // SAVE DELIVERABLE (from edit form)
    // ====================================

    public function operationsSave(Request $request, $id)
    {
        $deliverable = Deliverables::findOrFail($id);

        $validated = $request->validate([
            'plans_name' => 'nullable|string',
            'speed_in_mbps_plan' => 'nullable|string',
            'no_of_months_renewal' => 'nullable|integer',
            'sla' => 'nullable|string',
            'status_of_link' => 'nullable|string',
            'mode_of_delivery' => 'required|string',
            'date_of_activation' => 'nullable|date',
            'date_of_expiry' => 'nullable|date',
            // 'circuit_id' => 'nullable|string',
            'circuit_id'          => 'nullable|string|max:50|unique:deliverables,circuit_id',

            'pppoe_username' => 'nullable|string',
            'pppoe_password' => 'nullable|string',
            'pppoe_vlan' => 'nullable|string',
            'dhcp_ip_address' => 'nullable|string',
            'dhcp_vlan' => 'nullable|string',
            'static_ip_address' => 'nullable|string',
            'static_subnet_mask' => 'nullable|string',
            'static_gateway' => 'nullable|string',
            'static_vlan_tag' => 'nullable|string',

            'payment_login_url' => 'nullable|string',
            'payment_quick_url' => 'nullable|string',
            'payment_account_or_username' => 'nullable|string',
            'payment_password' => 'nullable|string',
            'mtu' => 'required|string|max:255',
            'wifi_username' => 'nullable|string|max:255',
            'wifi_password' => 'nullable|string|max:255',
    'lan_ip_1' => 'required|string|max:255',
    'lan_ip_2' => 'nullable|string|max:255',
    'lan_ip_3' => 'nullable|string|max:255',
    'lan_ip_4' => 'nullable|string|max:255',
    'ipsec' => 'nullable|in:Yes,No',
    'phase_1' => $request->ipsec == 'Yes' ? 'nullable|string|max:255' : 'nullable|string|max:255',
    'phase_2' => $request->ipsec == 'Yes' ? 'nullable|string|max:255' : 'nullable|string|max:255',
    'ipsec_interface' => $request->ipsec == 'Yes' ? 'nullable|string|max:255' : 'nullable|string|max:255',
            'account_id' => 'nullable|string|max:255',    
            'asset_id' => 'nullable|string|max:255',
            'asset_serial_no' => 'nullable|string|max:255',
            'otc_extra_charges' => 'nullable|numeric',
            'otc_bill_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $updateData = [
            'plans_name' => $request->plans_name,
            'speed_in_mbps_plan' => $request->speed_in_mbps_plan,
            'no_of_months_renewal' => $request->no_of_months_renewal,
            'date_of_activation' => $this->parseDeliverableDate($request->date_of_activation),
            'date_of_expiry' => $this->parseDeliverableDate($request->date_of_expiry),
            'sla' => $request->sla,
            'status_of_link' => $request->status_of_link,
            'mode_of_delivery' => $request->mode_of_delivery,
            // 'circuit_id' => $request->circuit_id,
            'circuit_id'          => $request->circuit_id,
            'mtu' => $request->mtu,
            'wifi_username' => $request->wifi_username,
            'wifi_password' => $request->wifi_password,
            'lan_ip_1' => $request->lan_ip_1,
            'lan_ip_2' => $request->lan_ip_2,
            'lan_ip_3' => $request->lan_ip_3,
            'lan_ip_4' => $request->lan_ip_4,
            'ipsec' => $request->ipsec,
            'account_id' => $request->account_id,
            'asset_id' => $request->asset_id,
            'asset_serial_no' => $request->asset_serial_no,
            'otc_extra_charges' => $request->otc_extra_charges,
        ];

        // PPPoE MODE
        if ($request->mode_of_delivery === 'PPPoE') {
            $updateData['pppoe_username'] = $request->pppoe_username;
            $updateData['pppoe_password'] = $request->pppoe_password;
            $updateData['pppoe_vlan'] = $request->pppoe_vlan;
        }

        // DHCP MODE
        if ($request->mode_of_delivery === 'DHCP') {
            $updateData['dhcp_ip_address'] = $request->dhcp_ip_address;
            $updateData['dhcp_vlan'] = $request->dhcp_vlan;
        }
        // PAYMENT DETAILS
        if ($request->mode_of_delivery === 'Payment Gateway'){
            $updateData['payment_login_url'] = $request->payment_login_url;
            $updateData['payment_quick_url'] = $request->payment_quick_url;
            $updateData['payment_account_or_username'] = $request->payment_account_or_username;
            $updateData['payment_password'] = $request->payment_password;
        }

        // Static IP MODE
        if ($this->isStaticMode($request->mode_of_delivery)) {
            $updateData['static_ip_address'] = $request->static_ip_address;
            $updateData['static_subnet_mask'] = $request->static_subnet_mask;
            $updateData['static_gateway'] = $request->gateway ?? $request->static_gateway;
            $updateData['static_vlan_tag'] = $request->static_vlan_tag;
        }

        // IPSEC MODE
        if ($request->ipsec === 'No') {
            $updateData['phase_1'] = null;
            $updateData['phase_2'] = null;
            $updateData['ipsec_interface'] = null;
        }
          if ($request->ipsec === 'Yes') {
            $updateData['phase_1'] = $request->phase_1;
            $updateData['phase_2'] = $request->phase_2;
            $updateData['ipsec_interface'] = $request->ipsec_interface;
        }
        for ($i = 1; $i <= $request->no_of_links; $i++) {
    $updateData["pppoe_username_$i"] = $request->input("pppoe_username_$i");
    $updateData["pppoe_password_$i"] = $request->input("pppoe_password_$i");
    $updateData["vlan_$i"] = $request->input("vlan_$i");
}
        // Handle file upload
        if ($request->hasFile('otc_bill_file')) {
            $file = $request->file('otc_bill_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Save to public/images/deliverableotcbill/
            $destinationPath = public_path('images/deliverableotcbill');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            
            $updateData['otc_bill_file'] = 'images/deliverableotcbill/' . $filename;
        }

        // 

        // EXPORT FILE - Save as Temp (InProgress) OR Final (Submit)
        if ($request->hasFile('export_file')) {
    $file = $request->file('export_file');
    $ext = $file->getClientOriginalExtension();
    $safeName = 'EXPORT_' . ($deliverable->circuit_id ?: 'UNKNOWN') . '_' . time();
    $filename = $safeName . ($ext ? '.' . $ext : '');

    if ($request->action === 'save') {
        // Temp folder
        $path = 'images/exportdeliverables/temp/';
    } else {
        // Final folder
        $path = 'images/exportdeliverables/';
    }

    $destination = public_path($path);
    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }

    $file->move($destination, $filename);
    $updateData['export_file'] = $path . $filename;
}

        // 
        // STATUS CHANGE based on action
        if ($request->action === 'save') {
            $updateData['status'] = 'InProgress';
        } elseif ($request->action === 'submit') {
            $updateData['status'] = 'Delivery';
        }

        $deliverable->update($updateData);

        // Insert client link when status changes to Delivery
if ($updateData['status'] === 'Delivery') {

    ClientLink::updateOrCreate(
        [
            'service_id' => $deliverable->circuit_id,   // unique service id
        ],
        [
            'client_id'  => $deliverable->feasibility->client->id,
            'link_type'  => $deliverable->link_type ?? $deliverable->feasibility->type_of_service,
            'router_id'  => $deliverable->router_id ? null : null,
            'bandwidth'  => $deliverable->speed_in_mbps_plan ?? $deliverable->speed_in_mbps,
         // 🔥 Add this mandatory field
            'interface_name' => $deliverable->mode_of_delivery ?? 'Unknown',
            'status' => 'Active', // or any appropriate default status
        ]
    );
}

        // Redirect based on new status
        if ($updateData['status'] === 'InProgress') {
            return redirect()->route('operations.deliverables.inprogress')
                ->with('success', 'Deliverable saved and moved to In Progress!');
        } elseif ($updateData['status'] === 'Delivery') {
            return redirect()->route('operations.deliverables.delivery')
                ->with('success', 'Deliverable submitted to Delivery!');
        }

        return redirect()->route('operations.deliverables.open')
            ->with('success', 'Deliverable updated successfully!');
    }

    private function parseDeliverableDate(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        $value = trim(str_replace('/', '-', $value));
        $formats = ['d-m-Y', 'Y-m-d'];
        foreach ($formats as $format) {
            try {
                $dt = Carbon::createFromFormat($format, $value);
                return $dt->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }
        return null;
    }

    // ====================================
    // UPDATE DELIVERABLE
    // ====================================

    public function update(Request $request, $id)
    {
        $deliverable = Deliverables::findOrFail($id);

        $validated = $request->validate([
            'mode_of_delivery' => 'required|string',
            'date_of_activation' => 'nullable|date',

            'delivery_ip' => 'nullable|string',
            'delivery_subnet_mask' => 'nullable|string',
            'delivery_gateway' => 'nullable|string',
            'delivery_dns1' => 'nullable|string',
            'delivery_dns2' => 'nullable|string',

            'pppoe_username' => 'nullable|string',
            'pppoe_password' => 'nullable|string',
            'pppoe_vlan' => 'nullable|string',

            'static_ip_address' => 'nullable|string',
            'static_subnet_mask' => 'nullable|string',
            'static_gateway' => 'nullable|string',
            'static_vlan' => 'nullable|string',
        ]);

        $updateData = [
            'mode_of_delivery' => $request->mode_of_delivery,
        ];

        // STATIC MODE
        if ($this->isStaticMode($request->mode_of_delivery)) {
            $updateData['static_ip_address'] = $request->static_ip_address;
            $updateData['static_subnet_mask'] = $request->static_subnet_mask;
            $updateData['static_gateway'] = $request->static_gateway ?? $request->gateway;
            $updateData['static_vlan'] = $request->static_vlan;
        }

        // PPPoE MODE
        if ($request->mode_of_delivery === 'PPPoE') {
            $updateData['pppoe_username'] = $request->pppoe_username;
            $updateData['pppoe_password'] = $request->pppoe_password;
            $updateData['pppoe_vlan'] = $request->pppoe_vlan;
        }

        // DHCP MODE
        if ($request->mode_of_delivery === 'DHCP') {
            $updateData['delivery_ip'] = $request->delivery_ip;
            $updateData['delivery_subnet_mask'] = $request->delivery_subnet_mask;
            $updateData['delivery_gateway'] = $request->delivery_gateway;
            $updateData['delivery_dns1'] = $request->delivery_dns1;
            $updateData['delivery_dns2'] = $request->delivery_dns2;
        }

        // STATUS CHANGE
        if ($request->has('save')) {
            $updateData['status'] = 'InProgress';
        }

        if ($request->has('submit')) {
            $updateData['status'] = 'Delivery';
        }

        $deliverable->update($updateData);

        return redirect()
            ->route('deliverables.open')
            ->with('success', 'Deliverable updated successfully');
    }

    // ====================================
    // CREATE Deliverable when PO closes
    // ====================================

    public function createFromPurchaseOrder($purchaseOrder, $oldStatus = null)
    {
        try {
            if (is_numeric($purchaseOrder)) {
                $purchaseOrder = PurchaseOrder::find($purchaseOrder);
            }

            if (!$purchaseOrder) return;

            // Prevent duplicates - check by po_number instead of purchase_order_id
            if (Deliverables::where('po_number', $purchaseOrder->po_number)->exists()) {
                return;
            }

            // Only if PO is Active
            if ($purchaseOrder->status !== 'Active') return;

            if ($oldStatus === 'Active') return; // already Active earlier

            $feasibility = $purchaseOrder->feasibility;
            $feasibilityStatus = FeasibilityStatus::where('feasibility_id', $purchaseOrder->feasibility_id)->first();
       // Get Feasibility fields
$client = optional($feasibility)->client;

// Last Deliverable ID for auto numbering
$last = Deliverables::latest('id')->first();
$nextNumber = ($last->id ?? 0) + 1;


$countryShort = strtoupper(substr(str_replace(' ', '', $feasibility->country ?? 'IN'), 0, 2));
// Company short-form (first 3 letters)
$companyName = $feasibility->company->company_name ?? 'CMP';
$companyShort = strtoupper(substr(str_replace(' ', '', $companyName), 0, 3));

// State short-form (first 3 letters)
$stateName = $feasibility->state ?? 'Unknown';
$stateShort = strtoupper(substr(str_replace(' ', '', $stateName), 0, 3));

// Static IP from Feasibility
$staticIp = $feasibility->static_ip_subnet ?? 'NOIP';

// Build Circuit ID: 25-UNB-TAM-STATICIP-0001
$circuitID = date('y')
            . '' . $countryShort
            . '' . $companyShort
            . '' . $stateShort
            . '' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $deliverable = Deliverables::create([
                'feasibility_id' => $purchaseOrder->feasibility_id,
                'status' => 'Open',
                'circuit_id' => $circuitID, // ✅ Auto generated

                'site_address' => $purchaseOrder->site_address ?? ($feasibility->address ?? ''),
                'local_contact' => $purchaseOrder->local_contact ?? ($feasibility->spoc_name ?? ''),
                'state' => $purchaseOrder->state ?? ($feasibility->state ?? ''),
                'gst_number' => $client->gstin ?? '',
                'link_type' => $purchaseOrder->connection_type ?? $feasibility->type_of_service,
                'speed_in_mbps' => $purchaseOrder->bandwidth ?? $feasibility->speed,
                'no_of_links' => $purchaseOrder->no_of_links ?? $feasibility->no_of_links,
                'vendor' => $feasibilityStatus->vendor1_name ?? '',
                'po_number' => $purchaseOrder->po_number,
                'po_date' => $purchaseOrder->po_date,
            ]);

            Log::info("Deliverable created for PO: {$purchaseOrder->po_number}");

        } catch (\Exception $e) {
            Log::error("Deliverable creation failed: " . $e->getMessage());
        }
    }

    public function calculateSubnet(Request $request)
{
    $ip = $request->ip;
    $subnet = str_replace('/', '', $request->subnet); // "/29" → "29"

    if (!$ip || !$subnet) {
        return response()->json([]);
    }

    // Convert IP to long integer
    $ipLong = ip2long($ip);

    // Subnet mask calculate
    $maskLong = -1 << (32 - $subnet);
    $mask = long2ip($maskLong);

    // Network IP
    $networkLong = $ipLong & $maskLong;
    $networkIp = long2ip($networkLong);

    // Broadcast
    $broadcastLong = $networkLong | (~$maskLong);
    $broadcast = long2ip($broadcastLong);

    // First & last usable
    $firstUsable = long2ip($networkLong + 1);
    $lastUsable = long2ip($broadcastLong - 1);

    // Gateway = first usable by default
    $gateway = $firstUsable;

    return response()->json([
        'network_ip'   => $networkIp,
        'gateway'      => $gateway,
        'subnet_mask'  => $mask,
        'usable_ips'   => $firstUsable . " - " . $lastUsable,
    ]);
}

    private function isStaticMode(?string $mode): bool
    {
        return in_array($mode, ['Static IP', 'Static'], true);
    }

}