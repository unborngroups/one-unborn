<?php

namespace App\Http\Controllers;

use App\Models\Deliverables;
use App\Models\FeasibilityStatus;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use App\Models\Feasibility;
use App\Models\PurchaseOrder;
use App\Models\Renewal;

class DashboardController extends Controller
{
     public function index()
     {
          $userType = Auth::user()->user_type ?? 'Employee';

        $feasibilityCounts = [
            'open' => FeasibilityStatus::where('status', 'Open')->count(),
            'inprogress' => FeasibilityStatus::where('status', 'InProgress')->count(),
            'closed' => FeasibilityStatus::where('status', 'Closed')->count(),
        ];

        // Count of InProgress feasibilities that are in "exception" state
        // (same vendor selected for 2 or more links).
        $exceptionInProgressCount = FeasibilityStatus::where('status', 'InProgress')
            ->get()
            ->filter(function ($record) {
                $names = [];
                for ($i = 1; $i <= 4; $i++) {
                    $name = strtolower(trim($record->{"vendor{$i}_name"} ?? ''));
                    if ($name !== '') {
                        $names[] = $name;
                    }
                }

                return count($names) > 1 && count(array_unique($names)) === 1;
            })
            ->count();

        // Purchase Order dashboard logic (based on closed feasibilities)
        // Open   => Closed feasibilities without any Purchase Order yet (PO pending)
        // Closed => Closed feasibilities that already have a Purchase Order
        // InProgress is reserved for future use (currently 0)

        $closedFeasibilityIds = FeasibilityStatus::where('status', 'Closed')
            ->pluck('feasibility_id')
            ->filter()
            ->unique();

        $feasibilityIdsWithPO = PurchaseOrder::whereIn('feasibility_id', $closedFeasibilityIds)
            ->pluck('feasibility_id')
            ->filter()
            ->unique();

        $purchaseOrderCounts = [
            // Closed feasibilities that still do NOT have a PO
            'open' => $closedFeasibilityIds->diff($feasibilityIdsWithPO)->count(),
            // Feasibilities in InProgress that are currently in exception state
            'exception' => $exceptionInProgressCount,
            // Reserved for future (e.g., PO created but deliverables pending)
            'inprogress' => 0,
            // Closed feasibilities that already have a PO
            'closed' => $feasibilityIdsWithPO->count(),
        ];

          $deliverableCounts = [
               'open' => Deliverables::where('status', 'Open')->count(),
               'inprogress' => Deliverables::where('status', 'InProgress')->count(),
               'delivery' => Deliverables::where('status', 'Delivery')->count(),
          ];

          $serviceCounts = [
    'Broadband' => [
        'links' => Feasibility::where('type_of_service', 'Broadband')->sum('no_of_links'),
        'locations' => Feasibility::where('type_of_service', 'Broadband')->distinct('area')->count('area'),
    ],
    'ILL' => [
        'links' => Feasibility::where('type_of_service', 'ILL')->sum('no_of_links'),
        'locations' => Feasibility::where('type_of_service', 'ILL')->distinct('area')->count('area'),
    ],
    'P2P' => [
        'links' => Feasibility::where('type_of_service', 'P2P')->sum('no_of_links'),
        'locations' => Feasibility::where('type_of_service', 'P2P')->distinct('area')->count('area'),
    ],
    'NNI' => [
        'links' => Feasibility::where('type_of_service', 'NNI')->sum('no_of_links'),
        'locations' => Feasibility::where('type_of_service', 'NNI')->distinct('area')->count('area'),
    ]
];


          // Only menus that the logged-in user's type can view
          $menus = Menu::where('user_type', $userType)
                          ->where('can_view', 1)
                          ->get();

        // Upcoming Renewals logic
        $todayRenewals = \App\Models\Renewal::with('deliverable')
            ->whereNotNull('alert_date')
            ->whereDate('alert_date', now()->toDateString())
            ->get();

        // $tomorrowRenewals = \App\Models\Renewal::with('deliverable')
        //     ->whereDate('alert_date', now()->addDay()->toDateString())
        //     ->get();

        $tomorrowRenewals = \App\Models\Renewal::with('deliverable')
            ->whereNotNull('alert_date')
            ->whereDate('alert_date', now()->addDay()->toDateString())
            ->get();

        $weekRenewals = \App\Models\Renewal::with('deliverable')
            ->whereBetween('alert_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->get();

        $renewalCounts = [
            'today' => $todayRenewals->count(),
            'tomorrow' => $tomorrowRenewals->count(),
            'week' => $weekRenewals->count(),
        ];

        return view('welcome', compact(
            'menus',
            'feasibilityCounts',
            'purchaseOrderCounts',
            'deliverableCounts',
            'serviceCounts',
            'renewalCounts',
            'todayRenewals',
            'tomorrowRenewals',
            'weekRenewals'
        ));
     }
}