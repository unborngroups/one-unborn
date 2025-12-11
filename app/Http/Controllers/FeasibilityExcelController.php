<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Shuchkin\SimpleXLSX;
require_once app_path('Libraries/SimpleXLSX.php');



class FeasibilityExcelController extends Controller
{
    protected array $importErrors = [];

    public function index()
    {
        $Feasibilitys = Feasibility::all();
        return view('feasibility.create', compact('Feasibilitys'));
    }

    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:csv,xlsx'
    ]);

    $file = $request->file('file');
    $extension = strtolower($file->getClientOriginalExtension());
    $path = $file->getRealPath();
    $rows = [];

    /** CSV **/
    if ($extension === 'csv') {
        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }
    }

    /** XLSX **/
    if ($extension === 'xlsx') {
        $xlsx = SimpleXLSX::parse($path);
        if (!$xlsx) {
            return back()->with('error', SimpleXLSX::parseError());
        }
        $rows = $xlsx->rows();
    }

    if (count($rows) <= 1) {
        return back()->with('error', 'No data found in file.');
    }

    $headers = array_map([$this, 'normalizeColumnKey'], $rows[0]);
    $this->importErrors = [];
    $imported = 0;
    $lastFeasibilityId = null;

    foreach ($rows as $index => $row) {

        if ($index === 0) continue;

        /** BUILD $rowData **/
        $rowData = [];
        foreach ($headers as $colIndex => $name) {
            $value = $row[$colIndex] ?? null;
            $rowData[$name] = $value === null ? null : trim((string)$value);
        }

        /** YES/NO → 1/0 **/
        $staticIpFlag     = strtoupper($rowData['static_ip'] ?? '') === 'YES' ? 1 : 0;
        $hardwareFlag     = strtoupper($rowData['hardware_required'] ?? '') === 'YES' ? 1 : 0;

        /** Resolve Company & Client **/
        $companyId = $this->resolveCompanyId($rowData['company_name'] ?? null);
        $clientId  = $this->resolveClientId($rowData['client_name'] ?? null);

        if (!$companyId) {
            $this->importErrors[] = "Row " . ($index + 1) . ": Company not found.";
            continue;
        }

        if (!$clientId) {
            $this->importErrors[] = "Row " . ($index + 1) . ": Client not found.";
            continue;
        }

        /** Pincode API **/
        $state = $rowData['state'] ?? null;
        $district = $rowData['district'] ?? null;
        $area = $rowData['area'] ?? null;

        if (!empty($rowData['pincode'])) {
            try {
                $apiResponse = file_get_contents("https://api.postalpincode.in/pincode/" . $rowData['pincode']);
                $decoded = json_decode($apiResponse, true);

                if ($decoded && $decoded[0]['Status'] === 'Success') {
                    $post = $decoded[0]['PostOffice'][0];
                    $state = $state ?: $post['State'];
                    $district = $district ?: $post['District'];
                    $area = $area ?: $post['Name'];
                }
            } catch (\Throwable $e) {}
        }

        /** Prepare Insert **/
        $prepared = [
            'type_of_service' => $rowData['type_of_service'],
            'company_id' => $companyId,
            'client_id' => $clientId,
            'pincode' => $rowData['pincode'],
            'state' => $state,
            'district' => $district,
            'area' => $area,
            'address' => $rowData['address'],
            'spoc_name' => $rowData['spoc_name'],
            'spoc_contact1' => $rowData['spoc_contact1'],
            'spoc_contact2' => $rowData['spoc_contact2'],
            'spoc_email' => $rowData['spoc_email'],
            'no_of_links' => $rowData['no_of_links'],
            'vendor_type' => $rowData['vendor_type'],
            'speed' => $rowData['speed'],
            'static_ip' => $staticIpFlag,
            'static_ip_subnet' => $rowData['static_ip_subnet'],
            'expected_delivery' => $this->parseDate($rowData['expected_delivery']),
            'expected_activation' => $this->parseDate($rowData['expected_activation']),
            'hardware_required' => $hardwareFlag,
        ];

        try {
            $feasibility = Feasibility::create($prepared);

            FeasibilityStatus::create([
                'feasibility_id' => $feasibility->id,
                'status' => 'Open',
            ]);

            $lastFeasibilityId = $feasibility->id;
            $imported++;

        } catch (\Throwable $e) {
            $this->importErrors[] = 
                "Row " . ($index + 1) . ": Failed to save - " . $e->getMessage();
        }
    }

    if ($lastFeasibilityId) {
        $response = redirect()
            ->route('sm.feasibility.open', $lastFeasibilityId)
            ->with('success', "$imported Records Imported Successfully!");
    } else {
        $response = back()->with('error', "No valid rows imported.");
    }

    if (!empty($this->importErrors)) {
        $response->with('import_errors', $this->importErrors);
    }

    return $response;
}

    /* ========================================
     *  Utility Functions
     * ====================================== */

    protected function parseDate($value)
    {
        if ($value === null || $value === '') return null;

        // Excel number date
        if (is_numeric($value)) {
            $timestamp = ($value - 25569) * 86400;
            return Carbon::createFromTimestampUTC((int)$timestamp)->format('Y-m-d');
        }

        // Normal date string
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeColumnKey(?string $value): string
    {
        return (string)Str::of($value ?? '')
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_');
    }

    protected function validateRequiredFields(array $rowData, string $companyIdentifier, string $clientIdentifier): array
    {
        $missing = [];

        $required = [
            'type_of_service', 'company_id', 'client_id', 'pincode', 'state',
            'district', 'area', 'address', 'spoc_name', 'spoc_contact1',
            'no_of_links', 'vendor_type', 'speed', 'static_ip',
            'expected_delivery', 'expected_activation',
        ];

        foreach ($required as $field) {
            if (empty($rowData[$field])) {
                $missing[] = "Feasibility row for company '{$companyIdentifier}' and client '{$clientIdentifier}' is missing required field '{$field}'.";
            }
        }

        return $this->importErrors = array_merge($this->importErrors, $missing);
    }

    protected function resolveCompanyId($value)
    {
        $value = trim(strtolower($value ?? ''));
        if (!$value) return null;

        if (is_numeric($value)) {
            return Company::find((int)$value)?->id;
        }

        return Company::whereRaw('LOWER(company_name)=?', [$value])->value('id');
    }

    protected function resolveClientId($value)
    {
        $value = trim(strtolower($value ?? ''));
        if (!$value) return null;

        if (is_numeric($value)) {
            return Client::find((int)$value)?->id;
        }

        return Client::where(function ($q) use ($value) {
            $q->whereRaw('LOWER(client_name)=?', [$value])
              ->orWhereRaw('LOWER(business_display_name)=?', [$value]);
        })->value('id');
    }
}
