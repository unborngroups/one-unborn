<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Renewal;
use App\Models\Deliverables;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helpers\TemplateHelper;
use App\Models\DeliverablePlan;

class RenewalController extends Controller
{
    public function index(Request $request)
    {
        // $perPage = $request->get('per_page', 10);
        $perPage = (int) $request->get('per_page', 10);
       $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

       $query = Renewal::query();
       if ($request->filled('search')) {
           $search = $request->search;
           $query->where(function($q) use ($search) {
               $q->where('deliverable_id', 'like', "%{$search}%")
                   ->orWhere('date_of_renewal', 'like', "%{$search}%")
                   ->orWhere('renewal_months', 'like', "%{$search}%")
                   ->orWhere('new_expiry_date', 'like', "%{$search}%")
                   ->orWhere('client_name', 'like', "%{$search}%")
                   ->orWhere('area', 'like', "%{$search}%")
                   ->orWhere('state', 'like', "%{$search}%");
           });
       }
       $renewals = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();


        $query = Renewal::query();
        // this lines for filtering today, tomorrow, week in dashboard
        $filter = $request->get('filter');
        if ($filter === 'today') {
            $query->whereDate('alert_date', today());
        } elseif ($filter === 'tomorrow') {
            $query->whereDate('alert_date', today()->addDay());
        } elseif ($filter === 'week') {
            $query->whereBetween('alert_date', [today(), today()->addDays(7)]);
        }
        $renewals = $query->latest()->paginate($perPage);

        $permissions = TemplateHelper::getUserMenuPermissions('Renewals') ?? (object)[
            'can_menu'   => true,
            'can_add'    => true,
            'can_edit'   => true,
            'can_delete' => true,
            'can_view'   => true,
        ];

        $today = Renewal::whereDate('alert_date', today())->get();
        $tomorrow = Renewal::whereDate('alert_date', today()->addDay())->get();
        $week = Renewal::whereBetween('alert_date', [
            today(),
            today()->addDays(7)
        ])->get();

        return view(
            'operations.renewals.index',
            compact('renewals', 'today', 'tomorrow', 'week', 'permissions')
        );
    }

    public function show($id)
    {
        $renewal = Renewal::findOrFail($id);
        return view('operations.renewals.view', compact('renewal'));
    }

    public function create()
    {
        $deliverables_plans = DeliverablePlan::all();
        return view('operations.renewals.create', compact('deliverables_plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'deliverable_id'   => 'required',
            'date_of_renewal'  => 'required|date',
            'renewal_months'   => 'required|integer|min:1'
        ]);

        $renewalDate = Carbon::parse($request->date_of_renewal);

        // Company rule: 1 month = 30 days
        $expiry = $renewalDate->copy()
            ->addDays((int)$request->renewal_months * 30)
            ->subDay();

        // Alert 1 day before expiry
        $alertDate = $expiry->copy()->subDay();

        Renewal::create([
            'deliverable_id'   => $request->deliverable_id,
            'date_of_renewal'  => $renewalDate->format('Y-m-d'),
            'renewal_months'   => $request->renewal_months,
            'new_expiry_date'  => $expiry->format('Y-m-d'),
            'alert_date'       => $alertDate->format('Y-m-d'),
        ]);

        return redirect()
            ->route('operations.renewals.index')
            ->with('success', 'Renewal saved successfully');
    }

    public function edit($id)
    {
        $renewal = Renewal::findOrFail($id);
        $deliverables_plans = DeliverablePlan::all();

        return view(
            'operations.renewals.edit',
            compact('renewal', 'deliverables_plans')
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'deliverable_id'   => 'required',
            'date_of_renewal'  => 'required|date',
            'renewal_months'   => 'required|integer|min:1'
        ]);

        $renewalDate = Carbon::parse($request->date_of_renewal);

        $expiry = $renewalDate->copy()
            ->addDays((int)$request->renewal_months * 30)
            ->subDay();

        $alertDate = $expiry->copy()->subDay();

        $renewal = Renewal::findOrFail($id);
        $renewal->update([
            'deliverable_id'   => $request->deliverable_id,
            'date_of_renewal'  => $renewalDate->format('Y-m-d'),
            'renewal_months'   => $request->renewal_months,
            'new_expiry_date'  => $expiry->format('Y-m-d'),
            'alert_date'       => $alertDate->format('Y-m-d'),
        ]);

        return redirect()
            ->route('operations.renewals.index')
            ->with('success', 'Renewal updated successfully');
    }

    public function destroy($id)
    {
        $renewal = Renewal::findOrFail($id);
        $renewal->delete();

        return redirect()
            ->route('operations.renewals.index')
            ->with('success', 'Renewal deleted successfully');
    }

    // Active or Inactive button

 public function toggleStatus($id)
{
    $renewal = Renewal::findOrFail($id);

    // Toggle Active/Inactive
    $renewal->status = $renewal->status === 'Active' ? 'Inactive' : 'Active';
    $renewal->save();
    return redirect()->route('operations.renewals.index')
                     ->with('success', 'Renewal status updated successfully.');
}

        /**
         * Show the renewal details (for /view route).
         */
        public function view($id)
        {
            $renewal = Renewal::findOrFail($id);
            return view('operations.renewals.view', compact('renewal'));
        }

}
