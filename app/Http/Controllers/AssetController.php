<?php
namespace App\Http\Controllers;

use App\Helpers\TemplateHelper;
use App\Models\Vendor;
use App\Models\VendorMake;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Display a searchable list of vendor assets.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));

        $query = Vendor::with('make')
            ->whereNotNull('asset_id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('asset_id', 'like', "%{$search}%")
                    ->orWhere('vendor_name', 'like', "%{$search}%")
                    ->orWhere('vendor_code', 'like', "%{$search}%")
                    ->orWhere('serial_no', 'like', "%{$search}%");
            });
        }

        $assets = $query->orderBy('asset_id')->paginate(15)->withQueryString();
        $permissions = TemplateHelper::getUserMenuPermissions('Asset');

        return view('asset.index', compact('assets', 'permissions'));
    }

    public function create()
    {
        $permissions = TemplateHelper::getUserMenuPermissions('Asset');
        $vendorMakes = VendorMake::orderBy('make_name')->get();

        return view('asset.create', compact('permissions', 'vendorMakes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'brand' => 'nullable|string|max:255',
            'make_id' => 'nullable|exists:vendor_makes,id',
            'model' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'mac_number' => 'nullable|string|max:255',
            'asset_id' => 'required|string|max:255|unique:vendors,asset_id',
            'procured_from' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty' => 'nullable|in:1 year,2 years,3 years,4 years,5 years',
            'po_number' => 'nullable|string|max:255',
            'mrp' => 'nullable|numeric|min:0',
            'purchase_cost' => 'nullable|numeric|min:0',
        ]);

        $purchaseDate = null;
        if (!empty($data['purchase_date'])) {
            $dateValue = $data['purchase_date'];
            try {
                if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $dateValue)) {
                    $purchaseDate = Carbon::createFromFormat('d-m-Y', $dateValue)->format('Y-m-d');
                } else {
                    $purchaseDate = Carbon::parse($dateValue)->format('Y-m-d');
                }
            } catch (\Exception) {
                $purchaseDate = null;
            }
        }

        Vendor::create([
            'vendor_name' => $data['brand'] ?? 'Asset ' . $data['asset_id'],
            'brand' => $data['brand'],
            'make_id' => $data['make_id'] ?? null,
            'model_no' => $data['model'],
            'serial_no' => $data['serial_number'] ?? null,
            'mac_number' => $data['mac_number'] ?? null,
            'asset_id' => $data['asset_id'],
            'procured_from' => $data['procured_from'] ?? null,
            'purchase_date' => $purchaseDate,
            'warranty' => $data['warranty'] ?? null,
            'po_number' => $data['po_number'] ?? null,
            'mrp' => $data['mrp'] ?? null,
            'purchase_cost' => $data['purchase_cost'] ?? null,
            'status' => 'Active',
        ]);

        return redirect()->route('asset.index')->with('success', 'Asset added successfully.');
    }
}