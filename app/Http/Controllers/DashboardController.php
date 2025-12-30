<?php

namespace App\Http\Controllers;

use App\Models\Deliverables;
use App\Models\FeasibilityStatus;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use App\Models\Feasibility;
use App\Models\Renewal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
// 

 // ---------------- Upcoming Renewals Logic (FIX) ----------------
        $today    = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $weekEnd  = Carbon::today()->addDays(7);

        $renewals = Deliverables::leftJoin('renewals', function ($join) {
                $join->on('deliverables.id', '=', 'renewals.deliverable_id')
                     ->whereRaw('renewals.id = (
                         select max(r2.id)
                         from renewals r2
                         where r2.deliverable_id = deliverables.id
                     )');
            })
            ->select(
                'deliverables.id',
                DB::raw('COALESCE(renewals.new_expiry_date, deliverables.date_of_expiry) as effective_expiry')
            );

$todayCount = (clone $renewals)
    ->whereRaw(
        'DATE(COALESCE(renewals.new_expiry_date, deliverables.date_of_expiry)) = ?',
        [$today]
    )
    ->count();
$tomorrowCount = (clone $renewals)
    ->whereRaw(
        'DATE(COALESCE(renewals.new_expiry_date, deliverables.date_of_expiry)) = ?',
        [$tomorrow]
    )
    ->count();
$weekCount = (clone $renewals)
    ->whereRaw(
        'DATE(COALESCE(renewals.new_expiry_date, deliverables.date_of_expiry)) BETWEEN ? AND ?',
        [$today, $weekEnd]
    )
    ->count();


          // Only menus that the logged-in user's type can view
          $menus = Menu::where('user_type', $userType)
                          ->where('can_view', 1)
                          ->get();

          return view('welcome', compact('menus', 'feasibilityCounts', 'deliverableCounts', 'serviceCounts', 'todayCount', 'tomorrowCount', 'weekCount'));
     }
}
