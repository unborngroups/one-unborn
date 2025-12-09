<?php
namespace App\Http\Controllers;

use App\Helpers\TemplateHelper;
use App\Models\Vendor;
use App\Models\VendorMake;
use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Company;
use App\Models\AssetType;
use App\Models\MakeType;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    // List Assets
    public function index()
{
    // $assets = Asset::latest()->get();
        $assets = Asset::orderBy('id', 'asc')->paginate(20);
        $permissions = TemplateHelper::getUserMenuPermissions('Asset') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

    return view('asset.index', compact('assets', 'permissions'));
}
// Create Asset
public function create()
{
    $companies = Company::all();
    $assetTypes = AssetType::all();
    $makes = MakeType::all();
    return view('asset.create', compact('companies', 'assetTypes', 'makes'));
}
// Store Asset
    public function store(Request $request)
    {
    $request->validate([
        'company_id' => 'required',
        'asset_type_id' => 'required',
        'make_type_id' => 'required',
        'model' => 'required',
        'brand' => 'nullable|string|max:255',
        'warranty' => 'nullable|string|max:100',
        'purchase_date' => 'required',
        'purchase_cost' => 'required',
        'po_no' => 'required',
        'serial_no' => 'required',
    ]);

    $company = Company::findOrFail($request->company_id);
    $makeType = MakeType::findOrFail($request->make_type_id);
    $brandSource = trim($request->brand ?: $makeType->make_name);
    $prefix = $this->makeAssetPrefix($company->company_name, $brandSource);

    $next = $this->nextSerial();
    $serial = str_pad($next, 4, '0', STR_PAD_LEFT);

    $asset = new Asset($request->all());
    $asset->asset_id = $prefix . $serial;

    if ($request->purchase_date) {
        $asset->purchase_date = Carbon::createFromFormat('d-m-Y', $request->purchase_date)->format('Y-m-d');
    }

    $asset->save();

    return redirect()->route('asset.index')->with('success', 'Asset Created');
}

// Edit Asset
public function edit($id)
{
    $asset = Asset::findOrFail($id);
    $companies = Company::all();
    $assetTypes = AssetType::all();
    $makes = MakeType::all();
    return view('asset.edit', compact('asset','companies','assetTypes','makes'));
}
// Update Asset
public function update(Request $request, $id)
{
    $asset = Asset::findOrFail($id);

    // Don't allow asset_id to change
    $data = $request->except('asset_id');

    // Convert date before update
    if ($request->purchase_date) {
        $data['purchase_date'] = Carbon::createFromFormat('d-m-Y', $request->purchase_date)->format('Y-m-d');
    }

    $asset->update($data);

    return redirect()->route('asset.index')->with('success', 'Asset Updated');
}


// View Asset
public function view($id)
{
    $asset = Asset::with(['company', 'assetType', 'makeType'])->findOrFail($id);
    return view('asset.view', compact('asset'));
}

// Generate next Asset ID
public function nextAssetID(Request $request)
{
    $companySegment = $this->makeAssetSegment($request->query('company', ''));
    $brandSegment = $this->makeAssetSegment($request->query('brand', ''));
    $prefix = $companySegment . $brandSegment;

        $num = $this->nextSerial();

    return response()->json([
        'no' => str_pad($num, 4, '0', STR_PAD_LEFT),
        'prefix' => $prefix,
    ]);
}

    private function makeAssetPrefix(string $companyName, string $brandName): string
    {
        return $this->makeAssetSegment($companyName) . $this->makeAssetSegment($brandName);
    }

    private function nextSerial(): int
{
    $maxSerial = Asset::selectRaw('MAX(CAST(RIGHT(asset_id, 4) AS UNSIGNED)) as max_serial')->value('max_serial');
    return ($maxSerial ?? 0) + 1;
}


    private function makeAssetSegment(string $value): string
    {
        $clean = preg_replace('/[^A-Za-z]/', '', strtoupper($value));
        if ($clean === '') {
            return 'XXX';
        }
        return str_pad(substr($clean, 0, 3), 3, 'X', STR_PAD_RIGHT);
    }
// Delete Asset
    public function destroy(Asset $asset)
{
    $asset->delete();
    return redirect()->route('asset.index')->with('success', 'Asset deleted.');
}

}