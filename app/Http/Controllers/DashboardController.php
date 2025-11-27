<?php

namespace App\Http\Controllers;

use App\Models\Deliverables;
use App\Models\FeasibilityStatus;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

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

          // Only menus that the logged-in user's type can view
          $menus = Menu::where('user_type', $userType)
                          ->where('can_view', 1)
                          ->get();

          return view('welcome', compact('menus', 'feasibilityCounts', 'deliverableCounts'));
     }
}
