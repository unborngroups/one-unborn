<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use App\Helpers\TemplateHelper;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with('feasibility.client')->orderBy('created_at', 'desc')->get();
        $permissions = TemplateHelper::getUserMenuPermissions('User Type') ?? (object)[
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
            'po_number' => 'required|string|max:255|unique:purchase_orders,po_number',
            'po_date' => 'required|date',
            'no_of_links' => 'required|integer|min:1|max:4',
            'contract_period' => 'required|integer|min:1',
        ];
        
        // Dynamic validation for pricing fields based on number of links
        $noOfLinks = $request->input('no_of_links');
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $rules["arc_link_{$i}"] = 'required|numeric|min:0';
            $rules["otc_link_{$i}"] = 'required|numeric|min:0';
            $rules["static_ip_link_{$i}"] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);
        
        // Calculate totals from individual link pricing
        $totalARC = 0;
        $totalOTC = 0;
        $totalStaticIP = 0;
        
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $totalARC += $request->input("arc_link_{$i}");
            $totalOTC += $request->input("otc_link_{$i}");
            $totalStaticIP += $request->input("static_ip_link_{$i}");
        }
        
        // Prepare data for storage
        $poData = [
            'feasibility_id' => $validated['feasibility_id'],
            'po_number' => $validated['po_number'],
            'po_date' => $validated['po_date'],
            'no_of_links' => $validated['no_of_links'],
            'contract_period' => $validated['contract_period'],
            'arc_per_link' => $totalARC / $noOfLinks, // Average per link (backward compatibility)
            'otc_per_link' => $totalOTC / $noOfLinks, // Average per link (backward compatibility)
            'static_ip_cost_per_link' => $totalStaticIP / $noOfLinks, // Average per link (backward compatibility)
            'status' => 'Active'
        ];
        
        // Add individual link data to support multi-vendor validation
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $poData["arc_link_{$i}"] = $request->input("arc_link_{$i}");
            $poData["otc_link_{$i}"] = $request->input("otc_link_{$i}");
            $poData["static_ip_link_{$i}"] = $request->input("static_ip_link_{$i}");
        }

        PurchaseOrder::create($poData);

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
        
        // Base validation
        $validated = $request->validate([
            'feasibility_id' => 'required|exists:feasibilities,id',
            'po_number' => 'required|string|max:255|unique:purchase_orders,po_number,' . $id,
            'po_date' => 'required|date',
            'no_of_links' => 'required|integer|min:1|max:4',
            'contract_period' => 'required|integer|min:1',
        ]);

        $noOfLinks = $validated['no_of_links'];
        
        // Dynamic validation for link pricing
        $linkValidationRules = [];
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $linkValidationRules["arc_link_{$i}"] = 'required|numeric|min:0';
            $linkValidationRules["otc_link_{$i}"] = 'required|numeric|min:0';
            $linkValidationRules["static_ip_link_{$i}"] = 'required|numeric|min:0';
        }
        
        $request->validate($linkValidationRules);
        
        // Calculate totals for backward compatibility
        $totalARC = 0;
        $totalOTC = 0;
        $totalStaticIP = 0;
        
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $totalARC += $request->input("arc_link_{$i}");
            $totalOTC += $request->input("otc_link_{$i}");
            $totalStaticIP += $request->input("static_ip_link_{$i}");
        }
        
        // Prepare data for update
        $poData = [
            'feasibility_id' => $validated['feasibility_id'],
            'po_number' => $validated['po_number'],
            'po_date' => $validated['po_date'],
            'no_of_links' => $validated['no_of_links'],
            'contract_period' => $validated['contract_period'],
            'arc_per_link' => $totalARC / $noOfLinks, // Average per link (backward compatibility)
            'otc_per_link' => $totalOTC / $noOfLinks, // Average per link (backward compatibility)
            'static_ip_cost_per_link' => $totalStaticIP / $noOfLinks, // Average per link (backward compatibility)
        ];
        
        // Add individual link data
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $poData["arc_link_{$i}"] = $request->input("arc_link_{$i}");
            $poData["otc_link_{$i}"] = $request->input("otc_link_{$i}");
            $poData["static_ip_link_{$i}"] = $request->input("static_ip_link_{$i}");
        }
        
        // Clear unused link fields
        for ($i = $noOfLinks + 1; $i <= 4; $i++) {
            $poData["arc_link_{$i}"] = null;
            $poData["otc_link_{$i}"] = null;
            $poData["static_ip_link_{$i}"] = null;
        }

        $purchaseOrder->update($poData);

        return redirect()->route('sm.purchaseorder.index')
            ->with('success', 'Purchase Order updated successfully!');
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
        if ($feasibilityStatus->vendor1_name) {
            $vendorPricing['vendor1'] = [
                'name' => $feasibilityStatus->vendor1_name,
                'arc' => (float) ($feasibilityStatus->vendor1_arc ?? 0),
                'otc' => (float) ($feasibilityStatus->vendor1_otc ?? 0),
                'static_ip_cost' => (float) ($feasibilityStatus->vendor1_static_ip_cost ?? 0)
            ];
        }
        
        // Add vendor2 if it has data
        if ($feasibilityStatus->vendor2_name) {
            $vendorPricing['vendor2'] = [
                'name' => $feasibilityStatus->vendor2_name,
                'arc' => (float) ($feasibilityStatus->vendor2_arc ?? 0),
                'otc' => (float) ($feasibilityStatus->vendor2_otc ?? 0),
                'static_ip_cost' => (float) ($feasibilityStatus->vendor2_static_ip_cost ?? 0)
            ];
        }
        
        // Add vendor3 if it has data
        if ($feasibilityStatus->vendor3_name) {
            $vendorPricing['vendor3'] = [
                'name' => $feasibilityStatus->vendor3_name,
                'arc' => (float) ($feasibilityStatus->vendor3_arc ?? 0),
                'otc' => (float) ($feasibilityStatus->vendor3_otc ?? 0),
                'static_ip_cost' => (float) ($feasibilityStatus->vendor3_static_ip_cost ?? 0)
            ];
        }
        
        // Add vendor4 if it has data
        if ($feasibilityStatus->vendor4_name) {
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
            'no_of_links' => $feasibility->no_of_links,
            'arc_per_link' => (float) $pricing['arc_per_link'],
            'otc_per_link' => (float) $pricing['otc_per_link'],
            'static_ip_cost_per_link' => (float) $pricing['static_ip_cost_per_link'],
            'vendor_pricing' => $vendorPricing
        ]);
    }
}
