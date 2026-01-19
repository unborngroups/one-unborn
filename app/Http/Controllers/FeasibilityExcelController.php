<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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
    $failedRows = [];
    $originalHeaders = $rows[0] ?? [];
    $sessionHeaders = array_merge($originalHeaders, ['Error Reason']);


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

        // Collect hardware details if present
        $hardwareDetails = null;
        if ($hardwareFlag) {
            $make = $rowData['make'] ?? null;
            $model = $rowData['model'] ?? null;
            if ($make || $model) {
                $hardwareDetails = [
                    [
                        'make' => $make,
                        'model' => $model,
                    ]
                ];
            }
        }

        // Collect all error reasons for this row
        $rowErrors = [];
        // Company and client validation
        $companyId = $this->resolveCompanyId($rowData['company_name'] ?? null);
        if (!$companyId) {
            $rowErrors[] = "Company not found (column: company_name)";
        }
        $clientId  = $this->resolveClientId($rowData['client_name'] ?? null);
        if (!$clientId) {
            $rowErrors[] = "Client not found (column: client_name)";
        }

        // Detailed per-column validation
        if (empty($rowData['type_of_service'])) {
            $rowErrors[] = "Type of Service is required (column: type_of_service)";
        }
        if (empty($rowData['delivery_company_name'])) {
            $rowErrors[] = "Delivery Company Name is required (column: delivery_company_name)";
        }
        if (empty($rowData['location_id'])) {
            $rowErrors[] = "Location ID is required (column: location_id)";
        }
        if (empty($rowData['longitude'])) {
            $rowErrors[] = "Longitude is required (column: longitude)";
        }
        if (empty($rowData['latitude'])) {
            $rowErrors[] = "Latitude is required (column: latitude)";
        }
        if (empty($rowData['pincode']) || !is_numeric($rowData['pincode']) || strlen($rowData['pincode']) != 6) {
            $rowErrors[] = "Invalid or missing Pincode (column: pincode)";
        }
        if (empty($rowData['address'])) {
            $rowErrors[] = "Address is required (column: address)";
        }
        if (empty($rowData['spoc_name'])) {
            $rowErrors[] = "SPOC Name is required (column: spoc_name)";
        }
        if (empty($rowData['spoc_contact1'])) {
            $rowErrors[] = "SPOC Contact 1 is required (column: spoc_contact1)";
        }
        if (empty($rowData['no_of_links']) || !is_numeric($rowData['no_of_links'])) {
            $rowErrors[] = "No. of Links is required and must be numeric (column: no_of_links)";
        }
        if (empty($rowData['vendor_type'])) {
            $rowErrors[] = "Vendor Type is required (column: vendor_type)";
        }
        if (empty($rowData['speed'])) {
            $rowErrors[] = "Speed is required (column: speed)";
        }
        // Add more field validations as needed

        if (!empty($rowErrors)) {
            $this->importErrors[] = "Row " . ($index + 1) . ": " . implode('; ', $rowErrors) . ".";
            $assoc = array_combine($originalHeaders, $row);
            $assoc['Error Reason'] = implode('; ', $rowErrors);
            $failedRows[] = $assoc;
            // Debug: log failed row and error
            Log::info('Feasibility Import Failed Row', ['row' => $assoc, 'row_number' => $index + 1, 'errors' => $rowErrors]);
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
        /** Clean Address (IMPORTANT) **/
$address = $rowData['address'] ?? null;
if ($address !== null) {
    $address = mb_convert_encoding($address, 'UTF-8', 'UTF-8');
    $address = preg_replace('/[^\PC\s]/u', '', $address);
}

        /** Prepare Insert **/
        $prepared = [
            'type_of_service' => $rowData['type_of_service'],
            'company_id' => $companyId,
            'client_id' => $clientId,
            'delivery_company_name' => $rowData['delivery_company_name'],
            'location_id' => $rowData['location_id'],
            'longitude' => $rowData['longitude'],
            'latitude' => $rowData['latitude'],
            'pincode' => $rowData['pincode'],
            'state' => $state,
            'district' => $district,
            'area' => $area,
            'address' => $address,
            // 'address' => $rowData['address'],
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
            'hardware_details' => $hardwareDetails,
        ];

        try {
            $feasibility = Feasibility::create($prepared);

            FeasibilityStatus::create([
                'feasibility_id' => $feasibility->id,
                'status' => 'Open',
            ]);

            // --- Trigger emails as in FeasibilityController ---
            $feasibilityController = app(\App\Http\Controllers\FeasibilityController::class);
            if (method_exists($feasibilityController, 'sendCreatedEmail')) {
                $feasibilityController->sendCreatedEmail($feasibility);
            }
            // If imported status is Closed, also trigger completed email
            $importedStatus = strtolower(trim($rowData['status'] ?? ''));
            if ($importedStatus === 'closed' && method_exists($feasibilityController, 'sendCompletedEmail')) {
                $feasibilityController->sendCompletedEmail($feasibility);
            }

            $lastFeasibilityId = $feasibility->id;
            $imported++;

        } catch (\Throwable $e) {
            $this->importErrors[] = 
                "Row " . ($index + 1) . ": Failed to save - " . $e->getMessage();
        }
    }

    // Always flash failed rows and headers for UI
    session()->flash('failed_rows', $failedRows);
    session()->flash('import_headers', $sessionHeaders);

    if ($lastFeasibilityId) {
        if (!empty($failedRows)) {
            $response = back()
                ->with('success', "$imported Records Imported Successfully!")
                ->with('import_errors', $this->importErrors)
                ->with('failed_rows', $failedRows)
                ->with('import_headers', $sessionHeaders);
        } else {
            // All rows succeeded, show summary table
            $importedRows = array_slice($rows, 1); // skip header
            $response = back()
                ->with('success', "$imported Records Imported Successfully!")
                ->with('imported_rows', $importedRows)
                ->with('import_headers', $originalHeaders);
        }
    } else {
        $response = back()->with('error', "No valid rows imported.");
        if (!empty($this->importErrors)) {
            $response->with('import_errors', $this->importErrors)
                ->with('failed_rows', $failedRows)
                ->with('import_headers', $sessionHeaders);
        }
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
            'type_of_service', 'company_id', 'client_id', 'delivery_company_name',
            'location_id', 'longitude', 'latitude', 'pincode', 'state',
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

     /**
     * Download failed rows as CSV
     */
    public function downloadFailedRows(Request $request)
    {
        $failedRows = session('failed_rows', []);
        $headers = session('import_headers', []);
        if (empty($failedRows) || empty($headers)) {
            return back()->with('error', 'No failed rows to download.');
        }

        $filename = 'failed_rows_' . date('Ymd_His') . '.csv';
        $handle = fopen('php://temp', 'r+');
        // Write headers
        fputcsv($handle, $headers);
        // Write rows
        foreach ($failedRows as $row) {
            // If associative, convert to numeric order by headers
            if (is_array($row) && array_keys($row) !== range(0, count($row) - 1)) {
                $row = array_map(function($h) use ($row) { return $row[$h] ?? ''; }, $headers);
            }
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
