<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Helpers\TemplateHelper;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\Vendor;
use App\Models\VendorMake;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Gstin;
use Illuminate\Support\Str;
use App\Services\SurepassService;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Shuchkin\SimpleXLSX;
require_once app_path('Libraries/SimpleXLSX.php');


class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected array $importErrors = [];

   public function index(Request $request)
{
    // Permissions
    $permissions = TemplateHelper::getUserMenuPermissions('Vendor Master') ?? (object)[
        'can_add' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_view' => true,
    ];

    // Per page (frontend only)
    $perPage = (int) $request->get('per_page', 10);
    $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

    // Paginated vendors
    $vendors = Vendor::orderBy('id', 'asc')->paginate($perPage);

    return view('vendors.index', compact('vendors', 'permissions'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendorMakes = VendorMake::all();
        $nextSequence = str_pad((Vendor::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);
        $vendor = new Vendor();
        return view('vendors.create', compact('vendorMakes', 'nextSequence', 'vendor'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $request->validate([
            'pan_number' => 'nullable|string|size:10',
            'vendor_name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255',
            'vendor_code' => 'nullable|string|max:50|unique:vendors,vendor_code',
            'business_display_name' => 'nullable|string|max:255',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'address3' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',

            // Business Contact
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_mobile' => 'nullable|string|max:20',
            'contact_person_email' => 'nullable|email|max:255',
            'gstin' => 'nullable|string|max:20',

            'pan_no' => 'nullable|string|max:20',
            'bank_account_no' => 'nullable|string|max:30',
            'ifsc_code' => 'nullable|string|max:30',

            // 'product_category' => 'nullable|string',
            // 'make_id' => 'nullable|exists:vendor_makes,id',
            // 'company_name' => 'nullable|string',
            // 'make_contact_no' => 'nullable|string',
            // 'make_email' => 'nullable|email',
            // 'model_no' => 'nullable|string',
            // 'serial_no' => 'nullable|string',
            // 'asset_id' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ]);

        $make = null;
        if (!empty($data['make_id'])) {
            $make = VendorMake::find($data['make_id']);
            if ($make) {
                $data['company_name'] = $data['company_name'] ?? $make->company_name;
                $data['make_contact_no'] = $data['make_contact_no'] ?? $make->contact_no;
                $data['make_email'] = $data['make_email'] ?? $make->email_id;
            }
        }

        $lastId = Vendor::max('id') ?? 0;
        $sequenceNumber = str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        if (empty($data['vendor_code'])) {
            $data['vendor_code'] = 'V' . $sequenceNumber;
        }

        if (empty($data['asset_id'])) {
            $companySource = $data['business_display_name'] ?? $data['vendor_name'] ?? 'INF';
            $makeSource = $make
                ? ($make->company_name ?: $make->make_name)
                : ($data['company_name'] ?? 'GEN');
            $data['asset_id'] = $this->assetPrefix($companySource)
                . $this->assetPrefix($makeSource)
                . $sequenceNumber;
        }

        Vendor::create($data);

        return redirect()->route('vendors.index')->with('success', 'Vendor created successfully!');
    }

    /**
     * Render a barcode PNG for the given Asset ID.
     */
    public function barcode($assetId)
    {
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($assetId, $generator::TYPE_CODE_128);
        return response($barcode, 200, ['Content-Type' => 'image/png']);
    }

    /**
     * Return a cleaned three-character prefix for asset IDs.
     */
    private function assetPrefix(?string $value): string
    {
        $clean = preg_replace('/[^A-Z0-9]/', '', Str::upper($value ?? ''));
        return str_pad(substr($clean, 0, 3), 3, 'X');
    }


    /**
     * Display the specified resource.
     */
    public function view($id)
{
    $vendor = \App\Models\Vendor::findOrFail($id);
    return view('vendors.view', compact('vendor'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
         $vendor = Vendor::findOrFail($vendor->id);
    $vendorMakes = VendorMake::all();  
        return view('vendors.edit', compact('vendor', 'vendorMakes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated =  $request->validate([
        'pan_number' => 'nullable|string|size:10',
       'vendor_name'          => 'required|string|max:255',
            'vendor_code'          => 'nullable|string|max:50|unique:vendor,vendor_code,' . ($vendor->id ?? 'null'),
            'business_display_name'=> 'nullable|string|max:255',
            'address1'             => 'nullable|string|max:255',
            'address2'             => 'nullable|string|max:255',
            'address3'             => 'nullable|string|max:255',
            'city'                 => 'nullable|string|max:100',
            'state'                => 'nullable|string|max:100',
            'country'              => 'nullable|string|max:100',
            'pincode'              => 'nullable|string|max:10',

            // Business Contact
            'contact_person_name'    => 'nullable|string|max:255',
            'contact_person_mobile' => 'nullable|string|max:20',
            'contact_person_email'   => 'nullable|email|max:255',
            'gstin'                => 'nullable|string|max:20',

            'pan_no'  => 'nullable|string|max:20',
            'bank_account_no'  => 'nullable|string|max:30',
            'ifsc_code'  =>  'nullable|string|max:30',

            'status'           => 'required|in:Active,Inactive',
        ]);


        $vendor->update($validated);

        return redirect()->route('vendors.index')->with('success', 'Vendor updated successfully.');
    }

    /**
     * Bulk delete clients selected from the index table.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:vendors,id',
        ]);

        Vendor::whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('vendors.index')
            ->with('success', count($request->input('ids')) . ' vendor(s) deleted successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully.');
    }
    /**
     * 
     */
     public function toggleStatus($id)
{
    $vendor = Vendor::findOrFail($id);

    // Toggle Active/Inactive
    $vendor->status = $vendor->status === 'Active' ? 'Inactive' : 'Active';
    $vendor->save();

    return redirect()->route('vendors.index')
                     ->with('success', 'Vendor status updated successfully.');
}
// Get Client Details for Feasibility
public function getDetails($id)
{
    $vendor = Vendor::find($id);
    return response()->json($vendor);
}

// PAN Verification
public function verifyPan(Request $request)
{
    $company = Company::where('pan_no', $request->pan)->first();
    if ($company) {
        return response()->json(['success' => true, 'data' => $company]);
    } else {
        return response()->json(['success' => false, 'message' => 'PAN not found']);
    }
}
// Fetch GST Details
public function fetchGST($pan, $state)
{
    $pan = strtoupper($pan);
    if (strlen($pan) !== 10) {
        return response()->json(['success' => false, 'message' => 'Invalid PAN']);
    }

    // Step 1: Generate GSTIN without checksum
    $partialGSTIN = $state . $pan . "1Z";

    // Step 2: Generate checksum
    $checksum = $this->getGSTChecksum($partialGSTIN);

    // Final GSTIN
    $gstin = $partialGSTIN . $checksum;

    // Step 3: Call GST API
    $url = "https://sheet.gstincheck.co.in/check/{$gstin}";
    $response = Http::timeout(10)->get($url);

    if ($response->failed() || !isset($response['tradeNam'])) {
        return response()->json(['success' => false]);
    }

    $data = [
        'gstin' => $gstin,
        'trade_name' => $response['tradeNam'],
        'address' => $response['pradr']['addr']['bno']
                    . ", " . $response['pradr']['addr']['st']
                    . ", " . $response['pradr']['addr']['dst'],
        'company_email' => $response['pradr']['email'] ?? '',
        'company_phone' => $response['pradr']['phone'] ?? '',
    ];

    return response()->json(['success' => true, 'data' => $data]);
}

/* Generate GSTIN Checksum */
private function getGSTChecksum($input)
{
    $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $factor = 1;
    $sum = 0;

    for ($i = 0; $i < strlen($input); $i++) {
        $codePoint = strpos($chars, $input[$i]);
        $digit = $factor * $codePoint;

        $factor = ($factor == 1) ? 2 : 1;

        $digit = floor($digit / 36) + ($digit % 36);
        $sum += $digit;
    }

    $checksumPoint = (36 - ($sum % 36)) % 36;
    return $chars[$checksumPoint];
}

/**
 * Fetch GSTIN by PAN using Surepass API
 */
public function fetchGstinByPan(Request $request)
{
    $request->validate([
        'pan_number' => 'required|string|size:10',
        'vendor_id' => 'nullable|integer'
    ]);

    $surepassService = new SurepassService();
    $result = $surepassService->getGstinByPan($request->pan_number);

    if (!$result['success']) {
        return response()->json($result);
    }

    // Parse the GSTIN data
    $gstinList = $surepassService->parseGstinData($result['data']);

    if (empty($gstinList)) {
        return response()->json([
            'success' => false,
            'message' => 'No GSTIN found for this PAN'
        ]);
    }

    // Don't save automatically - let user select which GSTINs to save
    return response()->json([
        'success' => true,
        'data' => $gstinList,
        'message' => 'GSTIN details fetched successfully'
    ]);
}

/**
 * Save selected GSTINs for a vendor
 */
public function saveSelectedGstins(Request $request)
{
    $request->validate([
        'vendor_id' => 'required|integer|exists:vendors,id',
        'gstins' => 'required|array|min:1',
        'gstins.*.gstin' => 'required|string|size:15',
        'gstins.*.trade_name' => 'nullable|string',
        'gstins.*.legal_name' => 'nullable|string',
        'gstins.*.principal_business_address' => 'nullable|string',
        'gstins.*.building_name' => 'nullable|string',
        'gstins.*.building_number' => 'nullable|string',
        'gstins.*.floor_number' => 'nullable|string',
        'gstins.*.street' => 'nullable|string',
        'gstins.*.location' => 'nullable|string',
        'gstins.*.district' => 'nullable|string',
        'gstins.*.city' => 'nullable|string',
        'gstins.*.state' => 'nullable|string',
        'gstins.*.state_code' => 'nullable|string|max:2',
        'gstins.*.pincode' => 'nullable|string|max:10',
        'gstins.*.is_primary' => 'nullable|boolean',
    ]);

    try {
        // If a GSTIN is marked as primary, unmark all others
        $hasPrimary = collect($request->gstins)->contains('is_primary', true);
        
        if ($hasPrimary) {
            Gstin::where('entity_type', 'vendor')
                ->where('entity_id', $request->vendor_id)
                ->update(['is_primary' => false]);
        }

        $conflicts = [];
        $savedCount = 0;

        // Save each selected GSTIN with duplicate checks across entities
        foreach ($request->gstins as $gstinData) {
            $existingOther = Gstin::where('gstin', $gstinData['gstin'])
                ->where(function($q) use ($request) {
                    $q->where('entity_type', '!=', 'vendor')
                      ->orWhere('entity_id', '!=', $request->vendor_id);
                })
                ->first();

            if ($existingOther) {
                $conflicts[] = $gstinData['gstin'];
                continue; // skip saving this GSTIN
            }

            Gstin::updateOrCreate(
                [
                    'entity_type' => 'vendor',
                    'entity_id' => $request->vendor_id,
                    'gstin' => $gstinData['gstin']
                ],
                [
                    'trade_name' => $gstinData['trade_name'] ?? null,
                    'legal_name' => $gstinData['legal_name'] ?? null,
                    'principal_business_address' => $gstinData['principal_business_address'] ?? null,
                    'building_name' => $gstinData['building_name'] ?? null,
                    'building_number' => $gstinData['building_number'] ?? null,
                    'floor_number' => $gstinData['floor_number'] ?? null,
                    'street' => $gstinData['street'] ?? null,
                    'location' => $gstinData['location'] ?? null,
                    'district' => $gstinData['district'] ?? null,
                    'city' => $gstinData['city'] ?? null,
                    'state' => $gstinData['state'] ?? null,
                    'state_code' => $gstinData['state_code'] ?? null,
                    'pincode' => $gstinData['pincode'] ?? null,
                    'status' => 'Active',
                    'is_primary' => $gstinData['is_primary'] ?? false,
                ]
            );
            $savedCount++;
        }

        $message = $savedCount . ' GSTIN(s) saved successfully';
        if (!empty($conflicts)) {
            $message .= '. Skipped duplicates already linked to other entities: ' . implode(', ', $conflicts);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error saving GSTINs: ' . $e->getMessage()
        ], 500);
    }
}
}