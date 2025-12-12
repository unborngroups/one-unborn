<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use App\Models\Deliverables;
use App\Helpers\TemplateHelper;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    
    public function index()
    {
        $purchaseOrders = PurchaseOrder::orderBy('id', 'asc')->get();

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
            'po_number' => 'required|string|max:255',
            'allow_reuse' => 'required|in:0,1',
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

        $allowReuse = $validated['allow_reuse'] === '1';
        if (!$allowReuse && PurchaseOrder::where('po_number', $validated['po_number'])->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'PO number already exists. Please use a different PO number.')
                ->with('po_duplicate', $validated['po_number']);
        }

        // Calculate totals from individual link pricing
        $totalARC = 0;
        $totalOTC = 0;
        $totalStaticIP = 0;
        
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $totalARC += $request->input("arc_link_{$i}");
            $totalOTC += $request->input("otc_link_{$i}");
            $totalStaticIP += $request->input("static_ip_link_{$i}");
        }
        
        // 🔥 PRICE VALIDATION: Check if PO amount is at least 20% higher than feasibility amount
        $feasibilityStatus = FeasibilityStatus::where('feasibility_id', $validated['feasibility_id'])->first();
        
        if ($feasibilityStatus) {
            // Get all vendor amounts and find the minimum (most conservative validation)
            $vendorPrices = [];
            
            for ($v = 1; $v <= 4; $v++) {
                $arcField = "vendor{$v}_arc";
                $otcField = "vendor{$v}_otc"; 
                $staticIpField = "vendor{$v}_static_ip_cost";
                
                if ($feasibilityStatus->$arcField || $feasibilityStatus->$otcField || $feasibilityStatus->$staticIpField) {
                    $vendorPrices[] = [
                        'vendor' => $v,
                        'arc' => (float)($feasibilityStatus->$arcField ?? 0),
                        'otc' => (float)($feasibilityStatus->$otcField ?? 0), 
                        'static_ip' => (float)($feasibilityStatus->$staticIpField ?? 0)
                    ];
                }
            }
            
            if (!empty($vendorPrices)) {
                // Use the minimum price among all vendors (most conservative)
                $minARC = min(array_column($vendorPrices, 'arc'));
                $minOTC = min(array_column($vendorPrices, 'otc')); 
                $minStaticIP = min(array_column($vendorPrices, 'static_ip'));
                
                // Calculate minimum required amounts (20% higher than feasibility minimum)
                $minRequiredARC = $minARC * 1.20; // 20% higher
                $minRequiredOTC = $minOTC * 1.20; // 20% higher  
                $minRequiredStaticIP = $minStaticIP * 1.20; // 20% higher
                
                // Check if PO amounts meet minimum requirements
                $errors = [];
                
                // 🚨 Check for exact match first (not allowed)
                if ($minARC > 0 && abs($totalARC - $minARC) < 0.01) {
                    $errors[] = "❌ ARC amount cannot match exactly with feasibility amount (₹" . number_format($minARC, 2) . "). Must be at least 20% higher: ₹" . number_format($minARC * 1.20, 2);
                }
                
                if ($minOTC > 0 && abs($totalOTC - $minOTC) < 0.01) {
                    $errors[] = "❌ OTC amount cannot match exactly with feasibility amount (₹" . number_format($minOTC, 2) . "). Must be at least 20% higher: ₹" . number_format($minOTC * 1.20, 2);
                }
                
                if ($minStaticIP > 0 && abs($totalStaticIP - $minStaticIP) < 0.01) {
                    $errors[] = "❌ Static IP cost cannot match exactly with feasibility amount (₹" . number_format($minStaticIP, 2) . "). Must be at least 20% higher: ₹" . number_format($minStaticIP * 1.20, 2);
                }
                
                // Check for minimum 20% higher requirement (if not exact match)
                if ($minARC > 0 && $totalARC < $minRequiredARC && abs($totalARC - $minARC) >= 0.01) {
                    $errors[] = "ARC amount (₹" . number_format($totalARC, 2) . ") must be at least 20% higher than feasibility minimum (₹" . number_format($minARC, 2) . "). Required: ₹" . number_format($minRequiredARC, 2);
                }
                
                if ($minOTC > 0 && $totalOTC < $minRequiredOTC && abs($totalOTC - $minOTC) >= 0.01) {
                    $errors[] = "OTC amount (₹" . number_format($totalOTC, 2) . ") must be at least 20% higher than feasibility minimum (₹" . number_format($minOTC, 2) . "). Required: ₹" . number_format($minRequiredOTC, 2);
                }
                
                if ($minStaticIP > 0 && $totalStaticIP < $minRequiredStaticIP && abs($totalStaticIP - $minStaticIP) >= 0.01) {
                    $errors[] = "Static IP cost (₹" . number_format($totalStaticIP, 2) . ") must be at least 20% higher than feasibility minimum (₹" . number_format($minStaticIP, 2) . "). Required: ₹" . number_format($minRequiredStaticIP, 2);
                }
                
                // // If validation fails, return with errors
                // if (!empty($errors)) {
                //     return redirect()->back()
                //         ->withInput()
                //         ->with('error', 'Correct the amount: ' . implode(' | ', $errors));
                // }
            }
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

        $purchaseOrder = PurchaseOrder::create($poData);

        // 🚀 AUTO-CREATE DELIVERABLE when Purchase Order is created
        $this->createDeliverableFromPurchaseOrder($purchaseOrder);
        

        return redirect()->route('sm.purchaseorder.index')
            ->with('success', 'Purchase Order created successfully and deliverable generated!');
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
        
        // 🔥 PRICE VALIDATION for UPDATE: Check if PO amount is at least 20% higher than feasibility amount
        $feasibilityStatus = FeasibilityStatus::where('feasibility_id', $validated['feasibility_id'])->first();
        
        if ($feasibilityStatus) {
            // Get all vendor amounts and find the minimum (most conservative validation)
            $vendorPrices = [];
            
            for ($v = 1; $v <= 4; $v++) {
                $arcField = "vendor{$v}_arc";
                $otcField = "vendor{$v}_otc"; 
                $staticIpField = "vendor{$v}_static_ip_cost";
                
                if ($feasibilityStatus->$arcField || $feasibilityStatus->$otcField || $feasibilityStatus->$staticIpField) {
                    $vendorPrices[] = [
                        'vendor' => $v,
                        'arc' => (float)($feasibilityStatus->$arcField ?? 0),
                        'otc' => (float)($feasibilityStatus->$otcField ?? 0), 
                        'static_ip' => (float)($feasibilityStatus->$staticIpField ?? 0)
                    ];
                }
            }
            
            if (!empty($vendorPrices)) {
                // Use the minimum price among all vendors (most conservative)
                $minARC = min(array_column($vendorPrices, 'arc'));
                $minOTC = min(array_column($vendorPrices, 'otc')); 
                $minStaticIP = min(array_column($vendorPrices, 'static_ip'));
                
                // Calculate minimum required amounts (20% higher than feasibility minimum)
                $minRequiredARC = $minARC * 1.20; // 20% higher
                $minRequiredOTC = $minOTC * 1.20; // 20% higher  
                $minRequiredStaticIP = $minStaticIP * 1.20; // 20% higher
                
                // Check if PO amounts meet minimum requirements
                $errors = [];
                
                // 🚨 Check for exact match first (not allowed)
                if ($minARC > 0 && abs($totalARC - $minARC) < 0.01) {
                    $errors[] = "❌ ARC amount cannot match exactly with feasibility amount (₹" . number_format($minARC, 2) . "). Must be at least 20% higher: ₹" . number_format($minARC * 1.20, 2);
                }
                
                if ($minOTC > 0 && abs($totalOTC - $minOTC) < 0.01) {
                    $errors[] = "❌ OTC amount cannot match exactly with feasibility amount (₹" . number_format($minOTC, 2) . "). Must be at least 20% higher: ₹" . number_format($minOTC * 1.20, 2);
                }
                
                if ($minStaticIP > 0 && abs($totalStaticIP - $minStaticIP) < 0.01) {
                    $errors[] = "❌ Static IP cost cannot match exactly with feasibility amount (₹" . number_format($minStaticIP, 2) . "). Must be at least 20% higher: ₹" . number_format($minStaticIP * 1.20, 2);
                }
                
                // Check for minimum 20% higher requirement (if not exact match)
                if ($minARC > 0 && $totalARC < $minRequiredARC && abs($totalARC - $minARC) >= 0.01) {
                    $errors[] = "ARC amount (₹" . number_format($totalARC, 2) . ") must be at least 20% higher than feasibility minimum (₹" . number_format($minARC, 2) . "). Required: ₹" . number_format($minRequiredARC, 2);
                }
                
                if ($minOTC > 0 && $totalOTC < $minRequiredOTC && abs($totalOTC - $minOTC) >= 0.01) {
                    $errors[] = "OTC amount (₹" . number_format($totalOTC, 2) . ") must be at least 20% higher than feasibility minimum (₹" . number_format($minOTC, 2) . "). Required: ₹" . number_format($minRequiredOTC, 2);
                }
                
                if ($minStaticIP > 0 && $totalStaticIP < $minRequiredStaticIP && abs($totalStaticIP - $minStaticIP) >= 0.01) {
                    $errors[] = "Static IP cost (₹" . number_format($totalStaticIP, 2) . ") must be at least 20% higher than feasibility minimum (₹" . number_format($minStaticIP, 2) . "). Required: ₹" . number_format($minRequiredStaticIP, 2);
                }
                
                // If validation fails, return with errors
                // if (!empty($errors)) {
                //     return redirect()->back()
                //         ->withInput()
                //         ->with('error', 'Correct the amount: ' . implode(' | ', $errors));
                // }
            }
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
    public function checkPoNumber(Request $request)
    {
        $poNumber = trim((string) $request->query('po_number', ''));
        $exists = false;

        if ($poNumber !== '') {
            $exists = PurchaseOrder::where('po_number', $poNumber)->exists();
        }

        return response()->json(['exists' => $exists]);
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
            'vendor_pricing' => $vendorPricing,
            'vendor_type' => strtoupper($feasibility->vendor_type ?? '')
        ]);
    }

 
    /**
     * Create deliverable from purchase order
     */
    private function createDeliverableFromPurchaseOrder($purchaseOrder)
    {
        try {
            // Check if deliverable already exists for this feasibility
            $existingDeliverable = Deliverables::where('feasibility_id', $purchaseOrder->feasibility_id)->first();
            
            if ($existingDeliverable) {
                Log::info("Deliverable already exists for feasibility ID: {$purchaseOrder->feasibility_id}");
                return;
            }
            
            // Get feasibility and feasibility status data
            $feasibility = $purchaseOrder->feasibility;
            $feasibilityStatus = FeasibilityStatus::where('feasibility_id', $purchaseOrder->feasibility_id)->first();
            
            if (!$feasibility) {
                Log::error("Feasibility not found for Purchase Order ID: {$purchaseOrder->id}");
                return;
            }
            
            // Create deliverable with data from feasibility and purchase order
            $deliverable = Deliverables::create([
                'feasibility_id' => $feasibility->id,
                'purchase_order_id' => $purchaseOrder->id,
                'status' => 'Open',
                
                // Site Information from Feasibility
                'site_address' => $feasibility->site_address ?? '',
                'local_contact' => $feasibility->contact_person ?? '',
                'state' => $feasibility->state ?? '',
                'gst_number' => $feasibility->gst_number ?? '',
                
                // Network Configuration from Feasibility
                'link_type' => $feasibility->connection_type ?? '',
                'speed_in_mbps' => $feasibility->bandwidth ?? '',
                'no_of_links' => $purchaseOrder->no_of_links ?? 1,
                
                // Vendor Information from Feasibility Status
                'vendor' => $feasibilityStatus->vendor1_name ?? '',
                
                // Pricing from Purchase Order
                'arc_cost' => $purchaseOrder->arc_per_link ?? 0,
                'otc_cost' => $purchaseOrder->otc_per_link ?? 0,
                'static_ip_cost' => $purchaseOrder->static_ip_cost_per_link ?? 0,
                
                // PO Details
                'po_number' => $purchaseOrder->po_number,
                'po_date' => $purchaseOrder->po_date,
            ]);
            
            Log::info("Deliverable created successfully with ID: {$deliverable->id} for Purchase Order: {$purchaseOrder->po_number}");
            
        } catch (\Exception $e) {
            Log::error("Failed to create deliverable from purchase order: " . $e->getMessage());
        }
    }
}