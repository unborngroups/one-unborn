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
use IPLib\Address\IPv4;


class DeliverablesController extends Controller
{
    // ====================================
    // LIST PAGES
    // ====================================

    public function operationsOpen()
    {
        $records = Deliverables::with([
            'feasibility',
            'feasibility.client',
            'feasibility.company',
            'feasibility.feasibilityStatus'
        ])
        ->where('status', 'Open')
        ->latest()
        ->get();

        return view('operations.deliverables.open', compact('records'));
    }

    public function operationsInProgress()
    {
        $records = Deliverables::with([
            'feasibility',
            'feasibility.client',
            'feasibility.company',
            'feasibility.feasibilityStatus'
        ])
        ->where('status', 'InProgress')
        ->latest()
        ->get();

        return view('operations.deliverables.inprogress', compact('records'));
    }

    public function operationsDelivery()
    {
        $records = Deliverables::with([
            'feasibility',
            'feasibility.client',
            'feasibility.company',
            'feasibility.feasibilityStatus'
        ])
        ->where('status', 'Delivery')
        ->latest()
        ->get();

        return view('operations.deliverables.delivery', compact('records'));
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

        $vendors = \App\Models\Vendor::orderBy('vendor_name')->get();

        return view('operations.deliverables.edit', compact('record', 'vendors'));
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
            'otc_extra_charges' => 'nullable|numeric',
            'otc_bill_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $updateData = [
            'plans_name' => $request->plans_name,
            'speed_in_mbps_plan' => $request->speed_in_mbps_plan,
            'no_of_months_renewal' => $request->no_of_months_renewal,
            'sla' => $request->sla,
            'status_of_link' => $request->status_of_link,
            'mode_of_delivery' => $request->mode_of_delivery,
            // 'circuit_id' => $request->circuit_id,
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

        // STATUS CHANGE based on action
        if ($request->action === 'save') {
            $updateData['status'] = 'InProgress';
        } elseif ($request->action === 'submit') {
            $updateData['status'] = 'Delivery';
        }

        $deliverable->update($updateData);

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