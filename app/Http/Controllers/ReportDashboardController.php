<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use App\Models\PurchaseOrder;
use App\Models\Deliverables;

class ReportDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Advanced filter logic
        $filterDate = $request->input('filter_date');
        $filterMonth = $request->input('filter_month');
        $filterDay = $request->input('filter_day');
        $filterYear = $request->input('filter_year');

        // Treat empty strings as null for all filters
        $filterDate = $filterDate === '' ? null : $filterDate;
        $filterMonth = $filterMonth === '' ? null : $filterMonth;
        $filterDay = $filterDay === '' ? null : $filterDay;
        $filterYear = $filterYear === '' ? null : $filterYear;

        // Build date filter closure
        $dateFilter = function ($query, $column = 'created_at') use ($filterDate, $filterMonth, $filterDay, $filterYear) {
            if ($filterDate) {
                $query->whereDate($column, $filterDate);
            } elseif ($filterMonth) {
                $query->whereYear($column, substr($filterMonth, 0, 4))
                      ->whereMonth($column, substr($filterMonth, 5, 2));
            } elseif ($filterDay && $filterYear) {
                $query->whereYear($column, $filterYear)
                      ->whereDay($column, $filterDay);
            } elseif ($filterYear) {
                $query->whereYear($column, $filterYear);
            }
        };

        // Feasibility widgets
        $feasibilityCreatedQ = Feasibility::query();
        $dateFilter($feasibilityCreatedQ);
        $feasibilityCounts['created'] = $feasibilityCreatedQ->count();

        $statuses = ['Open' => 'open', 'InProgress' => 'inprogress', 'Closed' => 'closed'];
        $feasibilityCounts += ['open' => 0, 'inprogress' => 0, 'closed' => 0];
        $feasibilityUpdates = ['open' => null, 'inprogress' => null, 'closed' => null];
        foreach ($statuses as $statusVal => $key) {
            $q = FeasibilityStatus::where('status', $statusVal);
            $dateFilter($q);
            $feasibilityCounts[$key] = $q->count();
            $feasibilityUpdates[$key] = $q->latest('updated_at')->with('updatedUser')->first();
        }

        // Purchase Order widgets
        $poCreatedQ = PurchaseOrder::query();
        $dateFilter($poCreatedQ);
        $poCounts['created'] = $poCreatedQ->count();
        $poStatuses = ['Active' => 'open',  'Closed' => 'closed'];
        $poCounts += ['open' => 0,  'closed' => 0];
        $poUpdates = ['open' => null, 'closed' => null];
        foreach ($poStatuses as $statusVal => $key) {
            $q = PurchaseOrder::where('status', $statusVal);
            $dateFilter($q);
            $poCounts[$key] = $q->count();
            $poUpdates[$key] = $q->latest('updated_at')->with('updatedUser')->first();
        }

        // Deliverables widgets
        $deliverableStatuses = ['Open' => 'open', 'InProgress' => 'inprogress', 'Delivery' => 'delivered'];
        $deliverableCounts = ['open' => 0, 'inprogress' => 0, 'delivered' => 0];
        $deliverableUpdates = ['open' => null, 'inprogress' => null, 'delivered' => null];
        foreach ($deliverableStatuses as $statusVal => $key) {
            $q = Deliverables::where('status', $statusVal);
            $dateFilter($q);
            $deliverableCounts[$key] = $q->count();
            $deliverableUpdates[$key] = $q->latest('updated_at')->with('updatedUser')->first();
        }

        return view('report_dashboard', compact(
            'filterDate', 'filterMonth', 'filterDay', 'filterYear',
            'feasibilityCounts', 'feasibilityUpdates',
            'poCounts', 'poUpdates',
            'deliverableCounts', 'deliverableUpdates',
        ));
    }
    // AJAX: Return filtered table for dashboard
    public function table(Request $request)
    {
        $type = $request->input('type'); // feasibility, po, deliverable
        $status = $request->input('status');
        $filterDate = $request->input('filter_date');
        $filterMonth = $request->input('filter_month');
        $filterDay = $request->input('filter_day');
        $filterYear = $request->input('filter_year');

        // Treat empty strings as null for all filters
        $filterDate = $filterDate === '' ? null : $filterDate;
        $filterMonth = $filterMonth === '' ? null : $filterMonth;
        $filterDay = $filterDay === '' ? null : $filterDay;
        $filterYear = $filterYear === '' ? null : $filterYear;

        $dateFilter = function ($query, $column = 'created_at') use ($filterDate, $filterMonth, $filterDay, $filterYear) {
            if ($filterDate) {
                $query->whereDate($column, $filterDate);
            } elseif ($filterMonth) {
                $query->whereYear($column, substr($filterMonth, 0, 4))
                      ->whereMonth($column, substr($filterMonth, 5, 2));
            } elseif ($filterDay && $filterYear) {
                $query->whereYear($column, $filterYear)
                      ->whereDay($column, $filterDay);
            } elseif ($filterYear) {
                $query->whereYear($column, $filterYear);
            }
        };

        $rows = [];
        $columns = [];
        if ($type === 'feasibility') {
            $q = FeasibilityStatus::with(['feasibility', 'updatedUser'])->where('status', ucfirst($status));
            $dateFilter($q);
            $rows = $q->orderByDesc('updated_at')->limit(100)->get();
            $columns = ['Feasibility ID', 'Client', 'Status', 'Updated By', 'Updated At'];
        } elseif ($type === 'po') {
            $statusMap = ['open' => 'Active'];
            $dbStatus = $statusMap[$status] ?? ucfirst($status);
            $q = PurchaseOrder::with(['feasibility', 'updatedUser'])->where('status', $dbStatus);
            $dateFilter($q);
            $rows = $q->orderByDesc('updated_at')->limit(100)->get();
            $columns = ['PO Number', 'Feasibility', 'Status', 'Updated By', 'Updated At'];
        } elseif ($type === 'deliverable') {
            $statusMap = ['delivered' => 'Delivery'];
            $dbStatus = $statusMap[$status] ?? ucfirst($status);
            $q = Deliverables::with(['feasibility', 'updatedUser'])->where('status', $dbStatus);
            $dateFilter($q);
            $rows = $q->orderByDesc('updated_at')->limit(100)->get();
            $columns = ['Delivery ID', 'Feasibility', 'Status', 'Updated By', 'Updated At'];
        }

        // Render HTML table
        $html = '<div class="card mt-4"><div class="card-body"><div class="table-responsive"><table class="table table-bordered table-striped" id="dashboardTable"><thead class="table-dark"><tr>';
        $html .= '<th width="50"><input type="checkbox" id="select_all" onchange="toggleAllCheckboxes()"></th>';
        $html .= '<th width="50">S.No</th>';
        foreach ($columns as $col) {
            $html .= '<th>' . $col . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($rows as $index => $row) {
            $html .= '<tr>';
            $html .= '<td><input type="checkbox" class="row-checkbox" value="' . $row->id . '" onchange="updateDownloadButton()"></td>';
            $html .= '<td>' . ($index + 1) . '</td>';
            if ($type === 'feasibility') {
                $html .= '<td>' . ($row->feasibility->feasibility_request_id ?? '-') . '</td>';
                // Fix client relationship - use client_name field instead of name
                $html .= '<td>' . ($row->feasibility->client->client_name ?? '-') . '</td>';
                $html .= '<td>' . ($row->status ?? '-') . '</td>';
                $html .= '<td>' . (optional($row->updatedUser)->name ?? '-') . '</td>';
                $html .= '<td>' . ($row->updated_at ? $row->updated_at->format('Y-m-d H:i') : '-') . '</td>';
            } elseif ($type === 'po') {
                $html .= '<td>' . ($row->po_number ?? '-') . '</td>';
                $html .= '<td>' . ($row->feasibility->feasibility_request_id ?? '-') . '</td>';
                $html .= '<td>' . ($row->status ?? '-') . '</td>';
                $html .= '<td>' . (optional($row->updatedUser)->name ?? '-') . '</td>';
                $html .= '<td>' . ($row->updated_at ? $row->updated_at->format('Y-m-d H:i') : '-') . '</td>';
            } elseif ($type === 'deliverable') {
                $html .= '<td>' . ($row->delivery_id ?? '-') . '</td>';
                $html .= '<td>' . ($row->feasibility->feasibility_request_id ?? '-') . '</td>';
                $html .= '<td>' . ($row->status ?? '-') . '</td>';
                $html .= '<td>' . (optional($row->updatedUser)->name ?? '-') . '</td>';
                $html .= '<td>' . ($row->updated_at ? $row->updated_at->format('Y-m-d H:i') : '-') . '</td>';
            }
            $html .= '</tr>';
        }
        if (count($rows) === 0) {
            $html .= '<tr><td colspan="' . (count($columns) + 2) . '" class="text-center">No records found.</td></tr>';
        }
        $html .= '</tbody></table></div></div></div>';
        
        // Add JavaScript for checkbox functionality
        $html .= '<script>
        function toggleAllCheckboxes() {
            const selectAll = document.getElementById("select_all");
            const checkboxes = document.querySelectorAll(".row-checkbox");
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateDownloadButton();
        }
        
        function updateDownloadButton() {
            const checkboxes = document.querySelectorAll(".row-checkbox");
            const downloadBtn = document.getElementById("downloadExcelBtn");
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            if (downloadBtn) {
                downloadBtn.classList.toggle("d-none", !anyChecked);
            }
        }
        </script>';
        
        return response($html);
    }

    public function downloadExcel(Request $request)
    {
        $type = $request->input('type');
        $ids = $request->input('ids', []);
        $downloadAll = $request->input('download_all', 'false') === 'true';
        
        if (!$downloadAll && empty($ids)) {
            return back()->with('error', 'No records selected for download.');
        }

        // Build date filter closure
        $filterDate = $request->input('filter_date');
        $filterMonth = $request->input('filter_month');
        $filterDay = $request->input('filter_day');
        $filterYear = $request->input('filter_year');

        // Treat empty strings as null for all filters
        $filterDate = $filterDate === '' ? null : $filterDate;
        $filterMonth = $filterMonth === '' ? null : $filterMonth;
        $filterDay = $filterDay === '' ? null : $filterDay;
        $filterYear = $filterYear === '' ? null : $filterYear;

        $dateFilter = function ($query, $column = 'created_at') use ($filterDate, $filterMonth, $filterDay, $filterYear) {
            if ($filterDate) {
                $query->whereDate($column, $filterDate);
            } elseif ($filterMonth) {
                $query->whereYear($column, substr($filterMonth, 0, 4))
                      ->whereMonth($column, substr($filterMonth, 5, 2));
            } elseif ($filterDay && $filterYear) {
                $query->whereYear($column, $filterYear)
                      ->whereDay($column, $filterDay);
            } elseif ($filterYear) {
                $query->whereYear($column, $filterYear);
            }
        };

        $rows = [];
        $filename = '';
        $sheetName = '';

        if ($type === 'feasibility') {
            $q = FeasibilityStatus::with(['feasibility.client', 'updatedUser']);
            if (!$downloadAll) {
                $q->whereIn('id', $ids);
            }
            $dateFilter($q);
            $rows = $q->orderByDesc('updated_at')->get();
            $filename = 'feasibility_report_' . date('Y-m-d_H-i-s') . '.csv';
            $sheetName = 'Feasibility';
        } elseif ($type === 'po') {
            $q = PurchaseOrder::with(['feasibility.client', 'updatedUser']);
            if (!$downloadAll) {
                $q->whereIn('id', $ids);
            }
            $dateFilter($q);
            $rows = $q->orderByDesc('updated_at')->get();
            $filename = 'purchase_orders_report_' . date('Y-m-d_H-i-s') . '.csv';
            $sheetName = 'Purchase Orders';
        } elseif ($type === 'deliverable') {
            $q = Deliverables::with(['feasibility.client', 'updatedUser']);
            if (!$downloadAll) {
                $q->whereIn('id', $ids);
            }
            $dateFilter($q);
            $rows = $q->orderByDesc('updated_at')->get();
            $filename = 'deliverables_report_' . date('Y-m-d_H-i-s') . '.csv';
            $sheetName = 'Deliverables';
        }

        if (empty($rows)) {
            return back()->with('error', 'No records found for download.');
        }

        // Prepare data for Excel
        $data = [];
        $headers = [];

        if ($type === 'feasibility') {
            $headers = ['Feasibility ID', 'Client Name', 'Status', 'Updated By', 'Updated At'];
            foreach ($rows as $row) {
                $data[] = [
                    $row->feasibility->feasibility_request_id ?? '-',
                    $row->feasibility->client->client_name ?? '-',
                    $row->status ?? '-',
                    optional($row->updatedUser)->name ?? '-',
                    $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '-'
                ];
            }
        } elseif ($type === 'po') {
            $headers = ['PO Number', 'Feasibility ID', 'Client Name', 'Status', 'Updated By', 'Updated At'];
            foreach ($rows as $row) {
                $data[] = [
                    $row->po_number ?? '-',
                    $row->feasibility->feasibility_request_id ?? '-',
                    $row->feasibility->client->client_name ?? '-',
                    $row->status ?? '-',
                    optional($row->updatedUser)->name ?? '-',
                    $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '-'
                ];
            }
        } elseif ($type === 'deliverable') {
            $headers = ['Delivery ID', 'Feasibility ID', 'Client Name', 'Status', 'Updated By', 'Updated At'];
            foreach ($rows as $row) {
                $data[] = [
                    $row->delivery_id ?? '-',
                    $row->feasibility->feasibility_request_id ?? '-',
                    $row->feasibility->client->client_name ?? '-',
                    $row->status ?? '-',
                    optional($row->updatedUser)->name ?? '-',
                    $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '-'
                ];
            }
        }

        // Create Excel file
        return $this->createExcelFile($data, $headers, $filename, $sheetName);
    }

    private function createExcelFile($data, $headers, $filename, $sheetName)
    {
        // Use simple CSV approach for now (can be upgraded to Laravel Excel later)
        $csv = implode(',', $headers) . "\n";
        foreach ($data as $row) {
            $csvRow = [];
            foreach ($row as $cell) {
                // Escape commas and quotes in CSV
                $cell = str_replace('"', '""', $cell);
                if (strpos($cell, ',') !== false || strpos($cell, '"') !== false) {
                    $cell = '"' . $cell . '"';
                }
                $csvRow[] = $cell;
            }
            $csv .= implode(',', $csvRow) . "\n";
        }

        $response = response($csv);
        $response->header('Content-Type', 'text/csv');
        $response->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        return $response;
    }
}
