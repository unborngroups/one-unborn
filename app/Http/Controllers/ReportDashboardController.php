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
        $poStatuses = ['Open' => 'open',  'Closed' => 'closed'];
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
            $q = PurchaseOrder::with(['feasibility', 'updatedUser'])->where('status', ucfirst($status));
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
        $html = '<div class="card mt-4"><div class="card-body"><table class="table table-bordered table-striped"><thead><tr>';
        foreach ($columns as $col) {
            $html .= '<th>' . $col . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            if ($type === 'feasibility') {
                $html .= '<td>' . ($row->feasibility->feasibility_request_id ?? '-') . '</td>';
                $html .= '<td>' . ($row->feasibility->client->name ?? '-') . '</td>';
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
            $html .= '<tr><td colspan="' . count($columns) . '" class="text-center">No records found.</td></tr>';
        }
        $html .= '</tbody></table></div></div>';
        return response($html);
    }
}
