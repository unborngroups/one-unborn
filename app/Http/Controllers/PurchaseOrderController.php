<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use App\Models\Deliverables;
use App\Helpers\TemplateHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::orderBy('id', 'asc')->get();
 
        $purchaseOrders = PurchaseOrder::with('feasibility.client')->orderBy('created_at', 'asc')->get();
        $permissions = TemplateHelper::getUserMenuPermissions('Purchase Order') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];
        return view('sm.purchaseorder.index', compact('purchaseOrders', 'permissions'));
    }

    public function create()
    {
        // Get only closed feasibilities that don't have a Purchase Order yet
        $usedFeasibilityIds = PurchaseOrder::pluck('feasibility_id')->toArray();

        $closedFeasibilities = FeasibilityStatus::with('feasibility.client')
            ->where('status', 'Closed')
            ->whereNotIn('feasibility_id', $usedFeasibilityIds)
            ->get();

        return view('sm.purchaseorder.create', compact('closedFeasibilities'));
    }

    public function store(Request $request)
    {
        // Basic validation for main fields
        $rules = [
            'feasibility_id' => 'required|exists:feasibilities,id',
            // 'po_number' => 'required|string|max:255|unique:purchase_orders,po_number',
            // 'po_number' => 'required',
            'po_date' => 'required|date',
            'no_of_links' => 'required|integer|min:1|max:4',
            'contract_period' => 'required|integer|min:1',
            
        ];
// Apply unique rule ONLY when reuse is NOT allowed
    if ($request->allow_reuse == 1) {
        $rules['po_number'] = 'required';
    } else {
        $rules['po_number'] = 'required|unique:purchase_orders,po_number';
    }
// if (!$request->allow_reuse) {
//     $rules['po_number'] .= '|unique:purchase_orders,po_number';
// }

// $request->validate($rules);


        // Check if type_of_service is ILL - make static IP mandatory
        $feasibility = Feasibility::find($request->feasibility_id);
        $isILL = $feasibility && ($feasibility->type_of_service === 'ILL');

        // Dynamic validation for pricing fields based on number of links
        $noOfLinks = (int)$request->input('no_of_links', 1);
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $rules["arc_link_{$i}"] = 'required|numeric|min:0';
            $rules["otc_link_{$i}"] = 'required|numeric|min:0';

            // Static IP is required for ILL connections
            if ($isILL) {
                $rules["static_ip_link_{$i}"] = 'required|numeric|min:0.01';
            } else {
                $rules["static_ip_link_{$i}"] = 'required|numeric|min:0';
            }
        }

        $validated = $request->validate($rules);

        // Get feasibility vendor minimum values
        $feasibilityStatus = FeasibilityStatus::where('feasibility_id', $validated['feasibility_id'])->first();

        // Calculate totals from individual link pricing
        $totalARC = 0;
        $totalOTC = 0;
        $totalStaticIP = 0;
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $totalARC += (float)$request->input("arc_link_{$i}");
            $totalOTC += (float)$request->input("otc_link_{$i}");
            $totalStaticIP += (float)$request->input("static_ip_link_{$i}");
        }

        // Server-side validation: Check exact match and 20% minimum requirement
        $error = $this->validatePricing($feasibilityStatus, $request, $noOfLinks);
        if ($error) {
            return back()->withInput()->with('error', $error);
        }

        // Prepare data for storage
        $poData = [
            'feasibility_id' => $validated['feasibility_id'],
            'po_number' => $validated['po_number'],
            'po_date' => $validated['po_date'],
            'no_of_links' => $validated['no_of_links'],
            'contract_period' => $validated['contract_period'],
            'arc_per_link' => $totalARC / $noOfLinks, // Average per link (backwards compatibility)
            'otc_per_link' => $totalOTC / $noOfLinks,
            'static_ip_cost_per_link' => $totalStaticIP / $noOfLinks,
            'status' => 'Active'
        ];

        // Add individual link data to support multi-vendor validation
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $poData["arc_link_{$i}"] = $request->input("arc_link_{$i}");
            $poData["otc_link_{$i}"] = $request->input("otc_link_{$i}");
            $poData["static_ip_link_{$i}"] = $request->input("static_ip_link_{$i}");
        }

        $allowReuse = (int) $request->input('allow_reuse', 0) === 1;
        $existingPO = $allowReuse ? PurchaseOrder::where('po_number', $validated['po_number'])->first() : null;

        if ($existingPO) {
            $purchaseOrder = $existingPO;
            $purchaseOrder->update($poData);
        } else {
            $purchaseOrder = PurchaseOrder::create($poData);
        }

        // Check if status is Active and create deliverable
        if ($purchaseOrder->status === 'Active') {
            app(\App\Http\Controllers\DeliverablesController::class)
                ->createFromPurchaseOrder($purchaseOrder);
            
            // Redirect to deliverables page
            // return redirect()->route('operations.deliverables.open')
                // ->with('success', 'Purchase Order created successfully and deliverable generated! Redirected to Deliverables.');
        }

        return redirect()->route('sm.purchaseorder.index')
            ->with('success', 'Purchase Order created successfully!');
    }

    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with('feasibility.client')->findOrFail($id);
        return view('sm.purchaseorder.view', compact('purchaseOrder'));
    }

    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        // Get closed feasibilities excluding those already used, but include current PO's feasibility
        $usedFeasibilityIds = PurchaseOrder::where('id', '!=', $id)->pluck('feasibility_id')->toArray();

        $closedFeasibilities = FeasibilityStatus::with('feasibility.client')
            ->where('status', 'Closed')
            ->whereNotIn('feasibility_id', $usedFeasibilityIds)
            ->get();

        return view('sm.purchaseorder.edit', compact('purchaseOrder', 'closedFeasibilities'));
    }

    public function update(Request $request, $id)
{
    $purchaseOrder = PurchaseOrder::findOrFail($id);

    // Validate base fields
    $rules = [
    'feasibility_id' => 'required|exists:feasibilities,id',
    // 'po_number' => 'required',
    'po_date' => 'required|date',
    'no_of_links' => 'required|integer|min:1|max:4',
    'contract_period' => 'required|integer|min:1',
    'status' => 'sometimes|string'
];
// Apply unique rule ONLY when reuse is NOT allowed
    if ($request->allow_reuse == 1) {
        $rules['po_number'] = 'required';
    } else {
        $rules['po_number'] = 'required|unique:purchase_orders,po_number';
    }
$validated = $request->validate($rules);

    $noOfLinks = $validated['no_of_links'];

    // Validate dynamic link fields
    $feasibility = Feasibility::find($validated['feasibility_id']);
    $isILL = $feasibility && $feasibility->type_of_service === 'ILL';

    $dynamicRules = [];
    for ($i = 1; $i <= $noOfLinks; $i++) {
        $dynamicRules["arc_link_{$i}"] = 'required|numeric|min:0';
        $dynamicRules["otc_link_{$i}"] = 'required|numeric|min:0';
        $dynamicRules["static_ip_link_{$i}"] = $isILL
            ? 'required|numeric|min:0.01'
            : 'required|numeric|min:0';
    }

    $request->validate($dynamicRules);

    DB::beginTransaction();
    try {
        // TAKE OLD STATUS BEFORE UPDATING
        $oldStatus = $purchaseOrder->status;
        $newStatus = $request->input('status', $oldStatus);

        // Build update data
        $poData = $validated;
        $poData['status'] = $newStatus;

        // Add dynamic link values
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $poData["arc_link_{$i}"] = $request->input("arc_link_{$i}");
            $poData["otc_link_{$i}"] = $request->input("otc_link_{$i}");
            $poData["static_ip_link_{$i}"] = $request->input("static_ip_link_{$i}");
        }

        // Clear unused extra link slots
        for ($i = $noOfLinks + 1; $i <= 4; $i++) {
            $poData["arc_link_{$i}"] = null;
            $poData["otc_link_{$i}"] = null;
            $poData["static_ip_link_{$i}"] = null;
        }

        // FINAL UPDATE CALL — ONLY ONE
        $purchaseOrder->update($poData);

        // DELIVERABLE CREATION (only when status changes to Active)
        if ($oldStatus !== 'Active' && $newStatus === 'Active') {
            app(\App\Http\Controllers\DeliverablesController::class)
                ->createFromPurchaseOrder($purchaseOrder);
            
            DB::commit();
            // Redirect to deliverables page
            return redirect()->route('operations.deliverables.open')
                ->with('success', 'Purchase Order updated successfully! Status changed to Active and deliverable created.');
        }

        DB::commit();
        return redirect()->route('sm.purchaseorder.index')
            ->with('success', 'Purchase Order updated successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("PO Update Failed: " . $e->getMessage());
        return back()->with('error', 'Failed to update PO. Please try again.');
    }
}


    public function toggleStatus($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        // Toggle Active/Inactive
        $purchaseOrder->status = $purchaseOrder->status === 'Active' ? 'Inactive' : 'Active';
        $purchaseOrder->save();

        return redirect()->route('sm.purchaseorder.index')
            ->with('success', 'Purchase Order status updated successfully.');
    }

    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->delete();

        return redirect()->route('sm.purchaseorder.index')
            ->with('success', 'Purchase Order deleted successfully!');
    }

    // AJAX method to get feasibility details
    public function getFeasibilityDetails($id)
    {
        $feasibility = Feasibility::with(['client', 'feasibilityStatus'])->findOrFail($id);

        // Get vendor pricing from feasibility status
        $feasibilityStatus = $feasibility->feasibilityStatus;

        // For now, use vendor1 pricing (you can modify this logic to select specific vendor)
        $pricing = [
            'arc_per_link' => $feasibilityStatus->vendor1_arc ?? 0,
            'otc_per_link' => $feasibilityStatus->vendor1_otc ?? 0,
            'static_ip_cost_per_link' => $feasibilityStatus->vendor1_static_ip_cost ?? 0
        ];

        // Build vendor pricing array for multi-vendor validation
        $vendorPricing = [];

        // Add vendor1 if it has data
        if (!empty($feasibilityStatus->vendor1_name)) {
            $vendorPricing['vendor1'] = [
                'name' => $feasibilityStatus->vendor1_name,
                'arc' => (float) ($feasibilityStatus->vendor1_arc ?? 0),
                'otc' => (float) ($feasibilityStatus->vendor1_otc ?? 0),
                'static_ip_cost' => (float) ($feasibilityStatus->vendor1_static_ip_cost ?? 0)
            ];
        }

        // Add vendor2 if it has data
        if (!empty($feasibilityStatus->vendor2_name)) {
            $vendorPricing['vendor2'] = [
                'name' => $feasibilityStatus->vendor2_name,
                'arc' => (float) ($feasibilityStatus->vendor2_arc ?? 0),
                'otc' => (float) ($feasibilityStatus->vendor2_otc ?? 0),
                'static_ip_cost' => (float) ($feasibilityStatus->vendor2_static_ip_cost ?? 0)
            ];
        }

        // Add vendor3 if it has data
        if (!empty($feasibilityStatus->vendor3_name)) {
            $vendorPricing['vendor3'] = [
                'name' => $feasibilityStatus->vendor3_name,
                'arc' => (float) ($feasibilityStatus->vendor3_arc ?? 0),
                'otc' => (float) ($feasibilityStatus->vendor3_otc ?? 0),
                'static_ip_cost' => (float) ($feasibilityStatus->vendor3_static_ip_cost ?? 0)
            ];
        }

        // Add vendor4 if it has data
        if (!empty($feasibilityStatus->vendor4_name)) {
            $vendorPricing['vendor4'] = [
                'name' => $feasibilityStatus->vendor4_name,
                'arc' => (float) ($feasibilityStatus->vendor4_arc ?? 0),
                'otc' => (float) ($feasibilityStatus->vendor4_otc ?? 0),
                'static_ip_cost' => (float) ($feasibilityStatus->vendor4_static_ip_cost ?? 0)
            ];
        }

        return response()->json([
            'client' => $feasibility->client,
            'feasibility' => $feasibility,
            'vendor_type' => $feasibility->vendor_type, // Add vendor_type for validation
            'no_of_links' => $feasibility->no_of_links,
            'arc_per_link' => (float) $pricing['arc_per_link'],
            'otc_per_link' => (float) $pricing['otc_per_link'],
            'static_ip_cost_per_link' => (float) $pricing['static_ip_cost_per_link'],
            'vendor_pricing' => $vendorPricing
        ]);
    }

    private function validatePricing($feas, $request, $noOfLinks)
    {
        if (!$feas || !$noOfLinks) {
            return null;
        }

        // Get feasibility to check vendor_type (use relationship from FeasibilityStatus)
        $feasibility = $feas->feasibility;

        Log::info('validatePricing called', [
            'feasibility_id' => $feasibility ? $feasibility->id : 'null',
            'vendor_type' => $feasibility ? $feasibility->vendor_type : 'null'
        ]);

        // Check if vendor_type is Self (UBN, UBS, UBL, INF)
        $selfVendors = ['UBN', 'UBS', 'UBL', 'INF'];
        $isSelfVendor = $feasibility && in_array($feasibility->vendor_type, $selfVendors);

        // Get minimum vendor values per link
        $vendorARCs = [];
        $vendorOTCs = [];
        $vendorStaticIPs = [];

        for ($v = 1; $v <= 4; $v++) {
            $arc = $feas->{"vendor{$v}_arc"};
            $otc = $feas->{"vendor{$v}_otc"};
            $sip = $feas->{"vendor{$v}_static_ip_cost"};

            if ($arc > 0) $vendorARCs[] = $arc;
            if ($otc > 0) $vendorOTCs[] = $otc;
            if ($sip > 0) $vendorStaticIPs[] = $sip;
        }

        $minARC = !empty($vendorARCs) ? min($vendorARCs) : 0;
        $minOTC = !empty($vendorOTCs) ? min($vendorOTCs) : 0;
        $minSIP = !empty($vendorStaticIPs) ? min($vendorStaticIPs) : 0;

        // PER-LINK VALIDATION
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $arc = (float)$request->input("arc_link_{$i}");
            $otc = (float)$request->input("otc_link_{$i}");
            $sip = (float)$request->input("static_ip_link_{$i}");

            if ($isSelfVendor) {
                // SELF VENDOR: PO amount must be ≤ feasibility amount (lower or equal, NOT higher)
                Log::info('Self vendor validation', ['vendor_type' => $feasibility->vendor_type]);

                // ARC
                if ($minARC > 0 && $arc > $minARC) {
                    return "INVALID PRICE - For Self Vendor, ARC for Link {$i} (₹{$arc}) cannot be higher than feasibility ARC of ₹{$minARC}. It must be lower or equal.";
                }

                // OTC
                if ($minOTC > 0 && $otc > $minOTC) {
                    return "INVALID PRICE - For Self Vendor, OTC for Link {$i} (₹{$otc}) cannot be higher than feasibility OTC of ₹{$minOTC}. It must be lower or equal.";
                }

                // Static IP
                if ($minSIP > 0 && $sip > $minSIP) {
                    return "INVALID PRICE - For Self Vendor, Static IP for Link {$i} (₹{$sip}) cannot be higher than feasibility Static IP of ₹{$minSIP}. It must be lower or equal.";
                }

            } else {
                // EXTERNAL VENDOR: PO amount must be > feasibility amount (only higher, NOT lower or equal)
                Log::info('External vendor validation', ['vendor_type' => $feasibility ? $feasibility->vendor_type : 'null']);

                // ARC
                if ($minARC > 0) {
                    if ($arc <= $minARC) {
                        return "INVALID PRICE - For External Vendor, ARC for Link {$i} (₹{$arc}) must be higher than feasibility ARC of ₹{$minARC}. It cannot be lower or equal.";
                    }
                }

                // OTC
                if ($minOTC > 0) {
                    if ($otc <= $minOTC) {
                        return "INVALID PRICE - For External Vendor, OTC for Link {$i} (₹{$otc}) must be higher than feasibility OTC of ₹{$minOTC}. It cannot be lower or equal.";
                    }
                }

                // Static IP
                if ($minSIP > 0) {
                    if ($sip <= $minSIP) {
                        return "INVALID PRICE - For External Vendor, Static IP for Link {$i} (₹{$sip}) must be higher than feasibility Static IP of ₹{$minSIP}. It cannot be lower or equal.";
                    }
                }
            }
        }

        return null;
    }
public function checkPoNumber(Request $request)
{
    $exists = PurchaseOrder::where('po_number', $request->po_number)->exists();
    return response()->json(['exists' => $exists]);
}

}
