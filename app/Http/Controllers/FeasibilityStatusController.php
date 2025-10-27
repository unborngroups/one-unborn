<?php

namespace App\Http\Controllers;

use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeasibilityStatusMail;

class FeasibilityStatusController extends Controller
{
    public function index($status = 'Open')
    {
        $statuses = ['Open', 'InProgress', 'Closed'];
        $records = FeasibilityStatus::with('feasibility')
            ->where('status', $status)
            ->get();

        return view('feasibility.feasibility_status.index', compact('records', 'status', 'statuses'));
    }

    public function show($id)
    {
        $record = FeasibilityStatus::with('feasibility')->findOrFail($id);
        return view('feasibility.feasibility_status.view', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'vendor_name' => 'required|string',
            'arc' => 'nullable|string',
            'otc' => 'nullable|string',
            'static_ip_cost' => 'nullable|string',
            'delivery_timeline' => 'nullable|string',
            'status' => 'required|in:Open,InProgress,Closed'
        ]);

        $record = FeasibilityStatus::with('feasibility')->findOrFail($id);

    // 🕓 Store old status before updating
    $oldStatus = $record->status;

    // 🔄 Update status
    $record->update($data);

    // ✅ Send email only if the status actually changed
    if ($oldStatus !== $data['status']) {
        $feasibility = $record->feasibility;

        // Choose recipient
        $recipient = $feasibility->spoc_email ?? 'admin@example.com';

        // Send email
        Mail::to($recipient)->send(new FeasibilityStatusMail($feasibility, $record));
    }

        return redirect()->route('feasibility.status.index', $data['status'])
            ->with('success', 'Feasibility status updated successfully.');
    }
}
