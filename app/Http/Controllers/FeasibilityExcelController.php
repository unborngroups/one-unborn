<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Feasibility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class FeasibilityExcelController extends Controller
{
    protected array $importErrors = [];
    public function index()
    {
        $Feasibilitys = Feasibility::all();
        return view('feasibility.create', compact('Feasibilitys'));
    }

    public function export()
    {
        $Feasibilitys = Feasibility::select(
            'id',
            'type_of_service',
            'company_id',
            'client_id',
            'pincode',
            'state',
            'city',
            'district',
            'area',
            'address',
            'spoc_name',
            'spoc_contact1',
            'spoc_contact2',
            'spoc_email',
            'no_of_links',
            'vendor_type',
            'speed',
            'static_ip',
            'static_ip_subnet',
            'expected_delivery',
            'expected_activation',
            'hardware_required',
            'hardware_model_name'
        )->get();

        return (new FastExcel($Feasibilitys))->download('Feasibilitys.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv,ods']
        ]);

        $this->importErrors = [];
        $importFile = $request->file('file');
        $targetDir = public_path('images/feasibilityimport');
        File::ensureDirectoryExists($targetDir);
        $filename = 'feasibility_import_' . uniqid() . '.' . $importFile->extension();
        $storedPath = $targetDir . DIRECTORY_SEPARATOR . $filename;
        File::copy($importFile->getRealPath(), $storedPath);
        $importPath = $storedPath;

        $preparedRow = null;

        foreach ((new FastExcel)->import($importPath) as $row) {
            $companyIdentifier = $row['Company ID'] ?? $row['Company Name'] ?? $row['Company'] ?? 'unknown';
            $clientIdentifier = $row['Client ID'] ?? $row['Client Name'] ?? $row['Client'] ?? 'unknown';

            $companyId = $this->resolveCompanyId($companyIdentifier);
            $clientId = $this->resolveClientId($clientIdentifier);

            if (!$companyId) {
                $this->importErrors[] = "Company '{$companyIdentifier}' could not be resolved to an existing company record.";
            }

            if (!$clientId) {
                $this->importErrors[] = "Client '{$clientIdentifier}' could not be resolved to an existing client record.";
            }

            if (!$companyId || !$clientId) {
                continue;
            }

            $prepared = [
                'type_of_service' => $this->normalizeString($row['Type of Service'] ?? $row['Service Type'] ?? null),
                'company_id' => $companyId,
                'client_id' => $clientId,
                'pincode' => $this->normalizeString($row['Pincode'] ?? $row['Pin Code'] ?? null),
                'state' => $this->normalizeString($row['State'] ?? null),
                'city' => $this->normalizeString($row['City'] ?? null),
                'district' => $this->normalizeString($row['District'] ?? null),
                'area' => $this->normalizeString($row['Area'] ?? $row['Post Office'] ?? null),
                'address' => $this->normalizeString($row['Address'] ?? null),
                'spoc_name' => $this->normalizeString($row['SPOC Name'] ?? $row['SPOC Contact Name'] ?? null),
                'spoc_contact1' => $this->normalizeString($row['SPOC Contact1'] ?? $row['SPOC Contact 1'] ?? null),
                'spoc_contact2' => $this->normalizeString($row['SPOC Contact2'] ?? $row['SPOC Contact 2'] ?? null),
                'spoc_email' => $this->normalizeString($row['SPOC Email'] ?? $row['SPOC Email ID'] ?? null),
                // 'no_of_links' => $this->toInteger($row['No of Links'] ?? $row['Links'] ?? null),
                'no_of_links' => $this->normalizeString($row['No of Links'] ?? null),
                'vendor_type' => $this->normalizeString($row['Vendor Type'] ?? $row['Vendor'] ?? null),
                'speed' => $this->normalizeString($row['Speed'] ?? null),
                'static_ip' => $this->normalizeString($row['Static IP'] ?? null),
                'static_ip_subnet' => $this->normalizeString($row['Static IP Subnet'] ?? null),
                'expected_delivery' => $this->parseDate($row['Expected Delivery'] ?? $row['Delivery Date'] ?? null),
                'expected_activation' => $this->parseDate($row['Expected Activation'] ?? $row['Activation Date'] ?? null),
                // 'hardware_required' => $this->normalizeBoolean($row['Hardware Required'] ?? $row['Hardware Needed'] ?? null),
                'hardware_required' => $this->normalizeHardwareRequired($row['Hardware Required'] ?? null),
                'hardware_model_name' => $this->normalizeString($row['Hardware Model Name'] ?? $row['Hardware Model'] ?? null),
                'status' => $this->normalizeStatus($row['Status'] ?? null),
            ];

            $missingFields = $this->validateRequiredFields($prepared, $companyIdentifier, $clientIdentifier);

            if (!empty($missingFields)) {
                $this->importErrors = array_merge($this->importErrors, $missingFields);
                continue;
            }

            $preparedRow = $prepared;
            break;
        }
        File::delete($storedPath);

        $redirect = back()->with('success', 'Excel parsed. Review the pre-filled form and hit save when ready.');

        if (!empty($this->importErrors)) {
            $redirect = $redirect->with('import_errors', $this->importErrors);
        }

        if ($preparedRow) {
            $redirect = $redirect->with('imported_row', $preparedRow);
        }

        return $redirect;
    }

    protected function normalizeString($value)
    {
        $value = trim((string) ($value ?? ''));
        return $value === '' ? null : $value;
    }

    protected function toInteger($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        $cleaned = preg_replace('/[^0-9]/', '', (string) $value);

        return $cleaned === '' ? null : (int) $cleaned;
    }

    protected function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $exception) {
            return null;
        }
    }

    protected function normalizeBoolean($value)
    {
        $value = strtolower((string) ($value ?? ''));
        return in_array($value, ['1', 'true', 'yes', 'y']);
    }

    protected function normalizeHardwareRequired($value)
    {
        $normalized = $this->normalizeString($value);

        if ($normalized === null) {
            return null;
        }

        return $this->normalizeBoolean($normalized) ? '1' : '0';
    }

    protected function normalizeChoice($value)
    {
        if (empty($value)) {
            return null;
        }

        $upper = strtoupper(trim($value));
        return in_array($upper, ['YES', 'Y']) ? 'YES' : (in_array($upper, ['NO', 'N']) ? 'NO' : $upper);
    }

    protected function normalizeStatus($value)
    {
        $normalized = $this->normalizeString($value);
        $allowed = ['Active', 'Inactive'];

        if (!$normalized) {
            return 'Active';
        }

        foreach ($allowed as $status) {
            if (strtolower($normalized) === strtolower($status)) {
                return $status;
            }
        }

        return 'Active';
    }

    protected function validateRequiredFields(array $rowData, string $companyIdentifier, string $clientIdentifier): array
    {
        $missing = [];
        $required = [
            'type_of_service',
            'company_id',
            'client_id',
            'pincode',
            'state',
            'district',
            'area',
            'address',
            'spoc_name',
            'spoc_contact1',
            'no_of_links',
            'vendor_type',
            'speed',
            'static_ip',
            'expected_delivery',
            'expected_activation',
        ];

        foreach ($required as $field) {
            if (empty($rowData[$field])) {
                $missing[] = "Feasibility row for company '{$companyIdentifier}' and client '{$clientIdentifier}' is missing required field '{$field}'.";
            }
        }

        return $missing;
    }

    protected function resolveCompanyId($value)
    {
        $value = $this->normalizeString($value);
        if (!$value) {
            return null;
        }

        if (is_numeric($value)) {
            return Company::find((int) $value)?->id;
        }

        return Company::whereRaw('LOWER(company_name) = ?', [Str::lower($value)])->value('id');
    }

    protected function resolveClientId($value)
    {
        $value = $this->normalizeString($value);
        if (!$value) {
            return null;
        }

        if (is_numeric($value)) {
            return Client::find((int) $value)?->id;
        }

        return Client::where(function ($query) use ($value) {
            $query->whereRaw('LOWER(client_name) = ?', [Str::lower($value)])
                ->orWhereRaw('LOWER(business_display_name) = ?', [Str::lower($value)]);
        })->value('id');
    }
}