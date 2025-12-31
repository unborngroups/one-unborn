<?php

namespace App\Http\Controllers;

use App\Helpers\TemplateHelper;
use App\Models\Asset;
use App\Models\Company;
use App\Models\AssetType;
use App\Models\MakeType;
use App\Models\ModelType;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use Shuchkin\SimpleXLSX;
require_once app_path('Libraries/SimpleXLSX.php');


class AssetController extends Controller
{
    private ?int $globalSerial = null;

    public function index(Request $request)
    {
        // $assets = Asset::orderBy('id', 'desc')->paginate(20);
        $permissions = TemplateHelper::getUserMenuPermissions('Asset') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];
        $companies = Company::all();
        $assetTypes = AssetType::all();
        $makes = MakeType::all();
        $models = ModelType::all();

        $perPage = (int) $request->get('per_page', 10);
    $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

    // Paginated vendors
    $assets = Asset::orderBy('id', 'desc')->paginate($perPage);

        return view('operations.asset.index', compact('assets', 'companies', 'assetTypes', 'makes', 'permissions', 'models'));
    }

    public function create()
    {
        $companies = Company::all();
        $assetTypes = AssetType::all();
        $makes = MakeType::all();
        $models = ModelType::all();
        $vendors = Vendor::whereNotNull('vendor_name')->where('vendor_name', '!=', '')->get();
        $permissions = TemplateHelper::getUserMenuPermissions('Asset') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

        return view('operations.asset.create', compact('companies', 'assetTypes', 'makes', 'vendors', 'permissions', 'models'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'asset_type_id' => 'required|exists:asset_types,id',
            'make_type_id' => 'required|exists:make_types,id',
            'model' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'serial_no' => 'required|string|max:255',
            'mac_no' => 'nullable|string|max:255',
            'procured_from' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty' => 'nullable|string|max:100',
            'po_no' => 'nullable|string|max:255',
            'mrp' => 'nullable|numeric|min:0',
            'purchase_cost' => 'nullable|numeric|min:0',
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $brandValue = $validated['brand'] ?? '';
        $modelValue = $validated['model'] ?? '';
        $prefix = $this->makeAssetPrefix($company->company_name, $brandValue, $modelValue);

        if (!empty($validated['purchase_date'])) {
            try {
                $validated['purchase_date'] = Carbon::createFromFormat('Y-m-d', $validated['purchase_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                $validated['purchase_date'] = Carbon::parse($validated['purchase_date'])->format('Y-m-d');
            }
        }

        $asset = new Asset($validated);
        $asset->asset_id = $prefix . $this->generateNextSerial();
        $asset->save();
        // // page data fetch without reload
        // if ($request->ajax()) {
        //     return response()->json(['success' => true, 'asset' => $asset]);
        // }
        return redirect()->route('operations.asset.index')->with('success', 'Asset created successfully.');
    }

    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $companies = Company::all();
        $assetTypes = AssetType::all();
        $makes = MakeType::all();
        $models = ModelType::all();
        $permissions = TemplateHelper::getUserMenuPermissions('Asset') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

        return view('operations.asset.edit', compact('asset', 'companies', 'assetTypes', 'makes', 'permissions', 'models'));
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'asset_type_id' => 'required|exists:asset_types,id',
            'make_type_id' => 'required|exists:make_types,id',
            'model' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'serial_no' => 'required|string|max:255',
            'mac_no' => 'nullable|string|max:255',
            'procured_from' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty' => 'nullable|string|max:100',
            'po_no' => 'nullable|string|max:255',
            'mrp' => 'nullable|numeric|min:0',
            'purchase_cost' => 'nullable|numeric|min:0',
        ]);

        $asset->update($validated);
        // if ($request->ajax()) {
        //     return response()->json(['success' => true, 'asset' => $asset]);
        // }
        return redirect()->route('operations.asset.index')->with('success', 'Asset updated successfully.');
    }

    public function view($id)
    {
        $asset = Asset::with(['company', 'assetType', 'makeType'])->findOrFail($id);
        $permissions = TemplateHelper::getUserMenuPermissions('Asset') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

        return view('operations.asset.view', compact('asset', 'permissions'));
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        // if (request()->ajax()) {
        //     return response()->json(['success' => true]);
        // }
        return redirect()->route('operations.asset.index')->with('success', 'Asset deleted successfully.');
    }

    public function nextAssetID(Request $request)
    {
        $prefix = $this->makeAssetPrefix(
            $request->query('company', ''),
            $request->query('brand', ''),
            $request->query('model', '')
        );
        $serial = $this->peekNextSerial();

        return response()->json([
            'prefix' => $prefix,
            'no' => $serial,
        ]);
    }

    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:csv,txt,xlsx,xls,ods',
    ]);

    $extension = $request->file->getClientOriginalExtension();
    $filepath = $request->file->getRealPath();

    $rows = [];

    // =======================
    // CASE 1 → CSV or TXT
    // =======================
    if (in_array($extension, ['csv', 'txt'])) {
        $handle = fopen($filepath, 'r');
        $header = fgetcsv($handle);

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = array_combine($header, $data);
        }

        fclose($handle);
    }

    // =======================
    // CASE 2 → XLSX / XLS / ODS
    // =======================
    else {
        if (!$xlsx = SimpleXLSX::parse($filepath)) {
            return back()->with('error', SimpleXLSX::parseError());
        }

        $sheet = $xlsx->rows();
        $header = array_map('trim', $sheet[0]);

        for ($i = 1; $i < count($sheet); $i++) {
            if (count($header) !== count($sheet[$i])) {
                // Optionally log or collect error for this row
                continue; // Skip this row
            }
            $rows[] = array_combine($header, $sheet[$i]);
        }
    }

    // =======================
    // PROCESS INSERT LOGIC
    // =======================

    $imported = 0;
    $errors = [];

    foreach ($rows as $index => $row) {

        $rowNumber = $index + 2; // because row 1 is header

        // Normalize row
        $data = $this->normalizeImportRow($row);

        // Resolve relations
        $company = $this->resolveCompany($data, $rowNumber, $errors);
        $assetType = $this->resolveAssetType($data, $rowNumber, $errors);
        $makeType = $this->resolveMakeType($data, $rowNumber, $errors);
        $modelType = $this->resolveModelType($data, $rowNumber, $errors);

        if (!$company || !$assetType || !$makeType) {
            continue;
        }

        // Prepare data
        $assetData = [
            'company_id' => $company->id,
            'asset_type_id' => $assetType->id,
            'make_type_id' => $makeType->id,
            'model_type_id' => $modelType->id,
            // 'model' => $data['model'] ?? null,
            'brand' => $data['brand'] ?? $makeType->make_name,
            'serial_no' => $data['serial_no'] ?? null,
            'mac_no' => $data['mac_no'] ?? null,
            'procured_from' => $data['procured_from'] ?? null,
            'warranty' => $data['warranty'] ?? null,
            'po_no' => $data['po_no'] ?? null,
            'mrp' => $data['mrp'] ?? null,
            'purchase_cost' => $data['purchase_cost'] ?? null,
        ];

        if (!empty($data['purchase_date'])) {
            $normalizedDate = $this->normalizePurchaseDate($data['purchase_date']);
            if (!$normalizedDate) {
                $errors[] = "Row $rowNumber: Invalid purchase_date";
                continue;
            }
            $assetData['purchase_date'] = $normalizedDate;
        }

        $prefix = $this->makeAssetPrefix($company->company_name, $assetData['brand'], $assetData['model'] ?? '');
        $assetData['asset_id'] = $prefix . $this->generateNextSerial();

        try {
            Asset::create($assetData);
            $imported++;
        } catch (\Throwable $e) {
            $errors[] = "Row $rowNumber: Failed to save (" . $e->getMessage() . ")";
        }
    }
    // Final response
    $response = back()->with('success', "$imported assets imported successfully.");

    if (!empty($errors)) {
        $response->with('import_errors', $errors);
    }

    return $response;
}

private function normalizePurchaseDate(mixed $value): ?string
{
    if (empty($value)) {
        return null;
    }

    if (is_numeric($value)) {
        $timestamp = ($value - 25569) * 86400;
        return Carbon::createFromTimestampUTC((int) floor($timestamp))->format('Y-m-d');
    }

    $value = trim((string) $value);
    $formats = ['Y-m-d', 'd/m/Y', 'd.m.Y', 'Y-m-d', 'Y/m/d'];
    foreach ($formats as $format) {
        try {
            return Carbon::createFromFormat($format, $value)->format('Y-m-d');
        } catch (\Exception $e) {
            // try next format
        }
    }

    try {
        return Carbon::parse($value)->format('Y-m-d');
    } catch (\Exception $e) {
        return null;
    }
}

    private function resolveCompany(array $data, int $rowNumber, array &$errors): ?Company
    {
        if (!empty($data['company_id'])) {
            return Company::find($data['company_id']);
        }

        if (!empty($data['company_name'])) {
            return Company::where('company_name', $data['company_name'])->first();
        }

        if (!empty($data['company'])) {
            return Company::where('company_name', $data['company'])->first();
        }

        $errors[] = "Row $rowNumber: company_id/company_name is required";
        return null;
    }

    private function resolveAssetType(array $data, int $rowNumber, array &$errors): ?AssetType
    {
        if (!empty($data['asset_type_id'])) {
            return AssetType::find($data['asset_type_id']);
        }

        if (!empty($data['asset_type'])) {
            return AssetType::where('type_name', $data['asset_type'])->first();
        }

        if (!empty($data['asset_type_name'])) {
            return AssetType::where('type_name', $data['asset_type_name'])->first();
        }

        $errors[] = "Row $rowNumber: asset_type_id/asset_type is required";
        return null;
    }

    private function resolveMakeType(array $data, int $rowNumber, array &$errors): ?MakeType
    {
        if (!empty($data['make_type_id'])) {
            return MakeType::find($data['make_type_id']);
        }

        if (!empty($data['make_name'])) {
            return MakeType::where('make_name', $data['make_name'])->first();
        }

        if (!empty($data['make_type'])) {
            return MakeType::where('make_name', $data['make_type'])->first();
        }

        if (!empty($data['make'])) {
            return MakeType::where('make_name', $data['make'])->first();
        }

        if (!empty($data['model_type_id'])) {
            return ModelType::find($data['model_type_id'])?->makeType;
        }
        $errors[] = "Row $rowNumber: make_type_id/make_name is required";
        return null;
    }

    private function normalizeImportRow(array $row): array
    {
        return collect($row)->mapWithKeys(function ($value, $key) {
            $normalizedKey = Str::of($key)
                ->lower()
                ->trim()
                ->replaceMatches('/[^a-z0-9]+/', '_')
                ->trim('_')
                ->__toString();

            $cleanValue = $value;
            if ($cleanValue instanceof \DateTimeInterface) {
                $cleanValue = $cleanValue->format('Y-m-d');
            }
            if ($cleanValue !== null) {
                $cleanValue = trim((string) $cleanValue);
                if ($cleanValue === '') {
                    $cleanValue = null;
                }
            }

            return [$normalizedKey => $cleanValue];
        })->toArray();
    }

    private function makeAssetPrefix(string $companyName, string $brandName, string $modelName = ''): string
    {
        return $this->makeAssetSegment($companyName)
            . $this->makeAssetSegment($brandName)
            . $this->makeModelSegment($modelName);
    }

    private function getCurrentMaxSerial(): int
    {
        if ($this->globalSerial === null) {
            $maxSerial = Asset::selectRaw('MAX(CAST(RIGHT(asset_id, 4) AS UNSIGNED)) as max_serial')
                ->value('max_serial');
            $this->globalSerial = $maxSerial ?? 0;
        }

        return $this->globalSerial;
    }

    private function generateNextSerial(): string
    {
        $next = $this->getCurrentMaxSerial() + 1;
        $this->globalSerial = $next;
        return str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function peekNextSerial(): string
    {
        return str_pad($this->getCurrentMaxSerial() + 1, 4, '0', STR_PAD_LEFT);
    }

    private function makeAssetSegment(string $value): string
    {
        $clean = preg_replace('/[^A-Za-z]/', '', strtoupper($value));
        return $clean === '' ? '' : substr($clean, 0, 3);
    }

    private function makeModelSegment(string $modelName): string
    {
        $clean = preg_replace('/[^A-Za-z]/', '', strtoupper($modelName));
        return $clean === '' ? '' : substr($clean, 0, 2);
    }

    // private function makeModelSegment(string $modelName): string
    // {
    //     $words = preg_split('/\s+/', trim($modelName), -1, PREG_SPLIT_NO_EMPTY);
    //     $segments = [];

    //     foreach (array_slice($words, 0, 2) as $word) {
    //         $segment = $this->makeAssetSegment($word);
    //         if ($segment !== '') {
    //             $segments[] = $segment;
    //         }
    //     }

    //     return implode('', $segments);
    // }

    /*
      * Bulk delete clients selected from the index table.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:clients,id',
        ]);

        Asset::whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('asset.index')
            ->with('success', count($request->input('ids')) . ' asset(s) deleted successfully.');
    }

    public function bulkPrint(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:assets,id',
        ]);

        $assets = Asset::whereIn('id', $data['ids'])->get();

        return view('operations.asset.bulk-print', compact('assets'));
    }


    public function exportAssets()
    {
        $assets = Asset::with(['company', 'assetType', 'makeType'])->get();
        $filename = "assets_" . date('Y-m-d_H-i-s') . ".csv";

        return response()->streamDownload(function () use ($assets) {
            $file = fopen('php://output', 'w');

        // CSV Header
        fputcsv($file, [
            'Asset ID',
            'Company',
            'Asset Type',
            'Make / Brand',
            'Model',
            'Serial No',
            'MAC No',
            'Procured From',
            'Purchase Date',
            'Warranty',
            'PO No',
            'MRP',
            'Purchase Cost'
        ]);

        // CSV Rows
        foreach ($assets as $a) {
            fputcsv($file, [
                $a->asset_id,
                $a->company->company_name ?? '',
                $a->assetType->type_name ?? '',
                $a->makeType->make_name ?? '',
                $a->model,
                $a->serial_no,
                $a->mac_no,
                $a->procured_from,
                $a->purchase_date,
                $a->warranty,
                $a->po_no,
                $a->mrp,
                $a->purchase_cost
            ]);
        }

            fclose($file);
        }, $filename);
    }



    public function print($id)
    {
        $asset = Asset::findOrFail($id);
        return view('operations.asset.print', compact('asset'));
    }
}