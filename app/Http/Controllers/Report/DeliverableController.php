<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DeliverableController extends Controller
{
    public function open(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $search = $request->input('search');

        $records = \App\Models\Deliverables::with([
            'feasibility.client',
            'feasibility.company',
            'feasibility.feasibilityStatus',
            'deliverablePlans',
        ])
        ->where('status', 'Open')
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%$search%")
                  ->orWhere('feasibility_id', 'like', "%$search%")
                  ->orWhere('circuit_id', 'like', "%$search%")
                  ->orWhereHas('feasibility', function ($fq) use ($search) {
                      $fq->where('feasibility_request_id', 'like', "%$search%")
                         ->orWhere('address', 'like', "%$search%")
                         ->orWhereHas('client', function ($cq) use ($search) {
                             $cq->where('client_name', 'like', "%$search%")
                                 ->orWhere('gstin', 'like', "%$search%")
                                 ->orWhere('status_of_link', 'like', "%$search%")
                                 ->orWhere('circuit_id', 'like', "%$search%")
                                 ->orWhere('client_circuit_id', 'like', "%$search%")
                                 ->orWhere('client_feasibility', 'like', "%$search%")
                                 ->orWhere('vendor_code', 'like', "%$search%")
                                 ->orWhere('asset_serial_no', 'like', "%$search%")
                                 ->orWhere('asset_mac_no', 'like', "%$search%")
                                 ->orWhere('otc_extra_charges', 'like', "%$search%")
                                 ->orWhere('ipsec', 'like', "%$search%")
                                 ;
                         })
                         ->orWhereHas('company', function ($cq) use ($search) {
                             $cq->where('company_name', 'like', "%$search%")
                                 ;
                         });
                  })
                  ->orWhereHas('deliverablePlans', function ($dpq) use ($search) {
                      $dpq->where('plans_name', 'like', "%$search%")
                          ->orWhere('circuit_id', 'like', "%$search%")
                          ->orWhere('speed_in_mbps_plan', 'like', "%$search%")
                          ->orWhere('sla', 'like', "%$search%")
                          ->orWhere('status_of_link', 'like', "%$search%")
                          ->orWhere('mode_of_delivery', 'like', "%$search%")
                          ->orWhere('client_circuit_id', 'like', "%$search%")
                          ->orWhere('client_feasibility', 'like', "%$search%")
                          ->orWhere('vendor_code', 'like', "%$search%")
                          ->orWhere('mtu', 'like', "%$search%")
                          ->orWhere('wifi_username', 'like', "%$search%")
                          ->orWhere('wifi_password', 'like', "%$search%")
                          ->orWhere('router_username', 'like', "%$search%")
                          ->orWhere('router_password', 'like', "%$search%")
                          ->orWhere('payment_login_url', 'like', "%$search%")
                          ->orWhere('payment_quick_url', 'like', "%$search%")
                          ->orWhere('payment_account', 'like', "%$search%")
                          ->orWhere('payment_username', 'like', "%$search%")
                          ->orWhere('payment_password', 'like', "%$search%")
                          ->orWhere('pppoe_username', 'like', "%$search%")
                          ->orWhere('pppoe_password', 'like', "%$search%")
                          ->orWhere('pppoe_vlan', 'like', "%$search%")
                          ->orWhere('dhcp_ip_address', 'like', "%$search%")
                          ->orWhere('dhcp_vlan', 'like', "%$search%")
                          ->orWhere('static_ip_address', 'like', "%$search%")
                          ->orWhere('static_vlan', 'like', "%$search%")
                          ->orWhere('network_ip', 'like', "%$search%")
                          ->orWhere('static_subnet_mask', 'like', "%$search%")
                          ->orWhere('static_gateway', 'like', "%$search%")
                          ->orWhere('usable_ips', 'like', "%$search%")
                          ->orWhere('remarks', 'like', "%$search%")
                          ;
                  });
            });
        })
        ->orderBy('id','desc')
        ->paginate($perPage)
        ->appends($request->except('page'));

        $records = $this->getDeliverables($request, 'Open');

        return view('report.deliverable.open', compact('records'));
    }


    public function inprogress(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $search = $request->input('search');

        $records = \App\Models\Deliverables::with([
            'feasibility.client',
            'feasibility.company',
            'feasibility.feasibilityStatus',
            'deliverablePlans',
        ])
        ->where('status', 'InProgress')
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%$search%")
                  ->orWhere('feasibility_id', 'like', "%$search%")
                  ->orWhere('circuit_id', 'like', "%$search%")
                  ->orWhereHas('feasibility', function ($fq) use ($search) {
                      $fq->where('feasibility_request_id', 'like', "%$search%")
                         ->orWhere('address', 'like', "%$search%")
                         ->orWhereHas('client', function ($cq) use ($search) {
                             $cq->where('client_name', 'like', "%$search%")
                                 ->orWhere('gstin', 'like', "%$search%")
                                 ->orWhere('status_of_link', 'like', "%$search%")
                                 ->orWhere('circuit_id', 'like', "%$search%")
                                 ->orWhere('client_circuit_id', 'like', "%$search%")
                                 ->orWhere('client_feasibility', 'like', "%$search%")
                                 ->orWhere('vendor_code', 'like', "%$search%")
                                 ->orWhere('asset_serial_no', 'like', "%$search%")
                                 ->orWhere('asset_mac_no', 'like', "%$search%")
                                 ->orWhere('otc_extra_charges', 'like', "%$search%")
                                 ->orWhere('ipsec', 'like', "%$search%")
                                 ;
                         })
                         ->orWhereHas('company', function ($cq) use ($search) {
                             $cq->where('company_name', 'like', "%$search%")
                                 ;
                         });
                  })
                  ->orWhereHas('deliverablePlans', function ($dpq) use ($search) {
                      $dpq->where('plans_name', 'like', "%$search%")
                          ->orWhere('circuit_id', 'like', "%$search%")
                          ->orWhere('speed_in_mbps_plan', 'like', "%$search%")
                          ->orWhere('sla', 'like', "%$search%")
                          ->orWhere('status_of_link', 'like', "%$search%")
                          ->orWhere('mode_of_delivery', 'like', "%$search%")
                          ->orWhere('client_circuit_id', 'like', "%$search%")
                          ->orWhere('client_feasibility', 'like', "%$search%")
                          ->orWhere('vendor_code', 'like', "%$search%")
                          ->orWhere('mtu', 'like', "%$search%")
                          ->orWhere('wifi_username', 'like', "%$search%")
                          ->orWhere('wifi_password', 'like', "%$search%")
                          ->orWhere('router_username', 'like', "%$search%")
                          ->orWhere('router_password', 'like', "%$search%")
                          ->orWhere('payment_login_url', 'like', "%$search%")
                          ->orWhere('payment_quick_url', 'like', "%$search%")
                          ->orWhere('payment_account', 'like', "%$search%")
                          ->orWhere('payment_username', 'like', "%$search%")
                          ->orWhere('payment_password', 'like', "%$search%")
                          ->orWhere('pppoe_username', 'like', "%$search%")
                          ->orWhere('pppoe_password', 'like', "%$search%")
                          ->orWhere('pppoe_vlan', 'like', "%$search%")
                          ->orWhere('dhcp_ip_address', 'like', "%$search%")
                          ->orWhere('dhcp_vlan', 'like', "%$search%")
                          ->orWhere('static_ip_address', 'like', "%$search%")
                          ->orWhere('static_vlan', 'like', "%$search%")
                          ->orWhere('network_ip', 'like', "%$search%")
                          ->orWhere('static_subnet_mask', 'like', "%$search%")
                          ->orWhere('static_gateway', 'like', "%$search%")
                          ->orWhere('usable_ips', 'like', "%$search%")
                          ->orWhere('remarks', 'like', "%$search%")
                          ;
                  });
            });
        })
        ->orderBy('id','desc')
        ->paginate($perPage)
        ->appends($request->except('page'));

         $records = $this->getDeliverables($request, 'InProgress');

        return view('report.deliverable.inprogress', compact('records'));
    }

    public function delivery(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $search = $request->input('search');

        $records = \App\Models\Deliverables::with([
            'feasibility.client',
            'feasibility.company',
            'feasibility.feasibilityStatus',
            'deliverablePlans',
        ])
        ->where('status', 'Delivery')
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%$search%")
                  ->orWhere('feasibility_id', 'like', "%$search%")
                  ->orWhere('circuit_id', 'like', "%$search%")
                  ->orWhereHas('feasibility', function ($fq) use ($search) {
                      $fq->where('feasibility_request_id', 'like', "%$search%")
                         ->orWhere('address', 'like', "%$search%")
                         ->orWhereHas('client', function ($cq) use ($search) {
                             $cq->where('client_name', 'like', "%$search%")
                                 ->orWhere('gstin', 'like', "%$search%")
                                 ->orWhere('status_of_link', 'like', "%$search%")
                                 ->orWhere('circuit_id', 'like', "%$search%")
                                 ->orWhere('client_circuit_id', 'like', "%$search%")
                                 ->orWhere('client_feasibility', 'like', "%$search%")
                                 ->orWhere('vendor_code', 'like', "%$search%")
                                 ->orWhere('asset_serial_no', 'like', "%$search%")
                                 ->orWhere('asset_mac_no', 'like', "%$search%")
                                 ->orWhere('otc_extra_charges', 'like', "%$search%")
                                 ->orWhere('ipsec', 'like', "%$search%")
                                 ;
                         })
                         ->orWhereHas('company', function ($cq) use ($search) {
                             $cq->where('company_name', 'like', "%$search%")
                                 ;
                         });
                  })
                  ->orWhereHas('deliverablePlans', function ($dpq) use ($search) {
                      $dpq->where('plans_name', 'like', "%$search%")
                          ->orWhere('circuit_id', 'like', "%$search%")
                          ->orWhere('speed_in_mbps_plan', 'like', "%$search%")
                          ->orWhere('sla', 'like', "%$search%")
                          ->orWhere('status_of_link', 'like', "%$search%")
                          ->orWhere('mode_of_delivery', 'like', "%$search%")
                          ->orWhere('client_circuit_id', 'like', "%$search%")
                          ->orWhere('client_feasibility', 'like', "%$search%")
                          ->orWhere('vendor_code', 'like', "%$search%")
                          ->orWhere('mtu', 'like', "%$search%")
                          ->orWhere('wifi_username', 'like', "%$search%")
                          ->orWhere('wifi_password', 'like', "%$search%")
                          ->orWhere('router_username', 'like', "%$search%")
                          ->orWhere('router_password', 'like', "%$search%")
                          ->orWhere('payment_login_url', 'like', "%$search%")
                          ->orWhere('payment_quick_url', 'like', "%$search%")
                          ->orWhere('payment_account', 'like', "%$search%")
                          ->orWhere('payment_username', 'like', "%$search%")
                          ->orWhere('payment_password', 'like', "%$search%")
                          ->orWhere('pppoe_username', 'like', "%$search%")
                          ->orWhere('pppoe_password', 'like', "%$search%")
                          ->orWhere('pppoe_vlan', 'like', "%$search%")
                          ->orWhere('dhcp_ip_address', 'like', "%$search%")
                          ->orWhere('dhcp_vlan', 'like', "%$search%")
                          ->orWhere('static_ip_address', 'like', "%$search%")
                          ->orWhere('static_vlan', 'like', "%$search%")
                          ->orWhere('network_ip', 'like', "%$search%")
                          ->orWhere('static_subnet_mask', 'like', "%$search%")
                          ->orWhere('static_gateway', 'like', "%$search%")
                          ->orWhere('usable_ips', 'like', "%$search%")
                          ->orWhere('remarks', 'like', "%$search%")
                          ;
                  });
            });
        })
        ->orderBy('id','desc')
        ->paginate($perPage)
        ->appends($request->except('page'));

        $records = $this->getDeliverables($request, 'Delivery');

        return view('report.deliverable.delivery', compact('records'));
    }
    /**
     * Download selected deliverables as Excel (stub implementation)
     */
    public function downloadExcel(Request $request)
    {
        $ids = $request->input('ids', []);
        $columns = [
            'client_name' => 'Client Name',
            'status_of_link' => 'Status of Link',
            'location_id' => 'Location ID',
            'area' => 'Area',
            'address' => 'Address',
            'circuit_id' => 'Circuit ID',
            'date_of_activation' => 'Date of Activation',
            'mode_of_delivery' => 'Mode of Delivery',
            'static_ip_address' => 'Static IP Address',
            'static_subnet_mask' => 'Static IP Subnet',
            'static_vlan' => 'Static VLAN Tag',
            'network_ip' => 'Network IP',
            'static_gateway' => 'Gateway',
            'subnet_mask' => 'Subnet Mask',
            'usable_ips' => 'Usable IPs',
        ];
        $records = \App\Models\Deliverables::with(['feasibility.client', 'deliverablePlans'])
            ->whereIn('id', $ids)
            ->get();

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            // Header
            fputcsv($file, array_values($columns));
            // Rows
            foreach ($records as $record) {
                $plan = $record->deliverablePlans->first();
                fputcsv($file, [
                    $record->feasibility->client->client_name ?? 'N/A',
                    $plan->status_of_link ?? 'N/A',
                    $record->feasibility->location_id ?? 'N/A',
                    $record->feasibility->area ?? 'N/A',
                    $record->feasibility->address ?? 'N/A',
                    $plan->circuit_id ?? 'N/A',
                    $plan && $plan->date_of_activation ? \Carbon\Carbon::parse($plan->date_of_activation)->format('Y-m-d') : 'N/A',
                    $plan->mode_of_delivery ?? 'N/A',
                    $plan->static_ip_address ?? 'N/A',
                    $plan->static_subnet_mask ?? 'N/A',
                    $plan->static_vlan ?? 'N/A',
                    $plan->network_ip ?? 'N/A',
                    $plan->static_gateway ?? 'N/A',
                    $plan->static_subnet_mask ?? 'N/A',
                    $plan->usable_ips ?? 'N/A',
                ]);
            }
            fclose($file);
        };

        $filename = 'deliverables_' . date('Ymd_His') . '.csv';
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    private function getDeliverables(Request $request, $status)
{
    $perPage = $request->input('per_page', 10);
    $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
    $search = $request->input('search');

    $query = \App\Models\Deliverables::with([
        'feasibility.client',
        'feasibility.company',
        'feasibility.feasibilityStatus',
        'deliverablePlans',
    ])->where('status', $status);

    // 🔍 Date Filter
    if ($request->filled('date_filter')) {

        $today = Carbon::now();

        if ($request->date_filter == 'month') {
            $startDate = $today->copy()->startOfMonth();
            $endDate   = $today->copy()->endOfMonth();
        }

        elseif ($request->date_filter == 'quarter') {
            $startDate = $today->copy()->startOfQuarter();
            $endDate   = $today->copy()->endOfQuarter();
        }

        elseif ($request->date_filter == 'half') {
            $startDate = $today->copy()->subMonths(6)->startOfMonth();
            $endDate   = $today;
        }

        $query->whereHas('deliverablePlans', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('date_of_activation', [$startDate, $endDate]);
        });
    }

    // 🔎 Search
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('po_number', 'like', "%$search%")
              ->orWhere('feasibility_id', 'like', "%$search%")
              ->orWhere('circuit_id', 'like', "%$search%");
        });
    }

    return $query->orderBy('id','desc')
                 ->paginate($perPage)
                 ->appends($request->except('page'));
}

    }