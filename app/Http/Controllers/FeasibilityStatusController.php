<?php

namespace App\Http\Controllers;

use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use App\Models\Deliverables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Mail\FeasibilityStatusMail;
use App\Helpers\TemplateHelper;

class FeasibilityStatusController extends Controller
{
    public function index(Request $request, $status = 'Open')
    {
    //  $feasibilityStatuses = FeasibilityStatus::orderBy('id', 'desc')->get();

        $statuses = ['Open', 'InProgress', 'Closed'];
        $records = FeasibilityStatus::with('feasibility')
            ->where('status', $status)
            ->get();
            // Use the helper correctly
        $permissions = TemplateHelper::getUserMenuPermissions('operations Feasibility') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

        // 
         $perPage = (int) $request->get('per_page', 10);
    $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

    // Paginated vendors
            $feasibilityStatuses = FeasibilityStatus::orderBy('id', 'desc')->paginate($perPage);


        return view('feasibility.feasibility_status.index', compact('records', 'status', 'statuses', 'permissions', 'feasibilityStatuses'));
    }

    public function show($id)
    {
        $record = FeasibilityStatus::with('feasibility')->findOrFail($id);
        return view('feasibility.feasibility_status.show', compact('record'));
    }

//     public function update(Request $request, $id)
// {
//     $data = $request->validate([
//         'vendor1_name' => 'nullable|string',
//         'vendor1_arc' => 'nullable|string',
//         'vendor1_otc' => 'nullable|string',
//         'vendor1_static_ip_cost' => 'nullable|string',
//         'vendor1_delivery_timeline' => 'nullable|string',

//         'vendor2_name' => 'nullable|string',
//         'vendor2_arc' => 'nullable|string',
//         'vendor2_otc' => 'nullable|string',
//         'vendor2_static_ip_cost' => 'nullable|string',
//         'vendor2_delivery_timeline' => 'nullable|string',

//         'vendor3_name' => 'nullable|string',
//         'vendor3_arc' => 'nullable|string',
//         'vendor3_otc' => 'nullable|string',
//         'vendor3_static_ip_cost' => 'nullable|string',
//         'vendor3_delivery_timeline' => 'nullable|string',

//         'vendor4_name' => 'nullable|string',
//         'vendor4_arc' => 'nullable|string',
//         'vendor4_otc' => 'nullable|string',
//         'vendor4_static_ip_cost' => 'nullable|string',
//         'vendor4_delivery_timeline' => 'nullable|string',

//         'status' => 'required|in:Open,InProgress,Closed'
//     ]);

//     $record = FeasibilityStatus::with('feasibility')->findOrFail($id);
//     $oldStatus = $record->status;

//     $record->update($data);

//     if ($oldStatus !== $data['status']) {
//         $feasibility = $record->feasibility;
//         $recipient = $feasibility->spoc_email ?? 'admin@example.com';
//         Mail::to($recipient)->send(new FeasibilityStatusMail($feasibility, $record));
//     }

//     return redirect()->route('feasibility.status.index', $data['status'])
//         ->with('success', 'Feasibility status updated successfully.');
// }

public function edit($id)
{
    $record = FeasibilityStatus::with('feasibility')->findOrFail($id);
    return view('feasibility.feasibility_status.edit', compact('record'));
}

public function editSave(Request $request, $id)
{
    $data = $request->validate([
        'vendor1_name' => 'nullable|string',
        'vendor1_arc' => 'nullable|string',
        'vendor1_otc' => 'nullable|string',
        'vendor1_static_ip_cost' => 'nullable|string',
        'vendor1_delivery_timeline' => 'nullable|string',

        'vendor2_name' => 'nullable|string',
        'vendor2_arc' => 'nullable|string',
        'vendor2_otc' => 'nullable|string',
        'vendor2_static_ip_cost' => 'nullable|string',
        'vendor2_delivery_timeline' => 'nullable|string',

        'vendor3_name' => 'nullable|string',
        'vendor3_arc' => 'nullable|string',
        'vendor3_otc' => 'nullable|string',
        'vendor3_static_ip_cost' => 'nullable|string',
        'vendor3_delivery_timeline' => 'nullable|string',

        'vendor4_name' => 'nullable|string',
        'vendor4_arc' => 'nullable|string',
        'vendor4_otc' => 'nullable|string',
        'vendor4_static_ip_cost' => 'nullable|string',
        'vendor4_delivery_timeline' => 'nullable|string',

        'status' => 'required|in:Open,InProgress,Closed'
    ]);

    $record = FeasibilityStatus::findOrFail($id);
    $record->update($data);

    return redirect()->route('feasibility.status.index', $data['status'])
        ->with('success', 'Feasibility status updated successfully.');
}

    // ====================================
    // Sales & Marketing Methods
    // ====================================

    public function smOpen(Request $request)
    {
        $permissions = TemplateHelper::getUserMenuPermissions('Feasibility Master', 'SM Feasibility Open') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];


         $perPage = (int) $request->get('per_page', 10);
         $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $records = FeasibilityStatus::with(['feasibility', 'feasibility.client'])
            ->where('status', 'Open')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
            
        return view('sm.feasibility.open', compact('records', 'permissions'));
    }

    public function smInProgress(Request $request)
    {
        $permissions = TemplateHelper::getUserMenuPermissions('Feasibility Master', 'SM Feasibility In Progress') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

         $perPage = (int) $request->get('per_page', 10);
         $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $records = FeasibilityStatus::with(['feasibility', 'feasibility.client'])
            ->where('status', 'InProgress')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return view('sm.feasibility.inprogress', compact('records', 'permissions'));
    }

    public function smClosed(Request $request)
    {
        $permissions = TemplateHelper::getUserMenuPermissions('Feasibility Master', 'SM Feasibility Closed') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

         $perPage = (int) $request->get('per_page', 10);
         $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $records = FeasibilityStatus::with(['feasibility', 'feasibility.client'])
            ->where('status', 'Closed')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return view('sm.feasibility.closed', compact('records', 'permissions'));
    }

    public function smView($id)
    {
        $record = FeasibilityStatus::with(['feasibility', 'feasibility.client'])->findOrFail($id);
        return view('sm.feasibility.view', compact('record'));
    }

    public function smEdit($id)
    {
        $record = FeasibilityStatus::with(['feasibility', 'feasibility.client'])->findOrFail($id);
        
        // ✅ Get all vendors for dropdown
        $vendors = \App\Models\Vendor::orderBy('vendor_name')->get();
        
        return view('sm.feasibility.edit', compact('record', 'vendors'));
    }

    // ============================
    // SM SAVE
    // ============================
    public function smSave(Request $request)
    {
        return $this->commonSmSave($request, false);
    }

    // ============================
    // SM SUBMIT
    // ============================
    public function smSubmit(Request $request)
    {
        return $this->commonSmSave($request, true);
    }

    // ============================
    // COMMON FOR smSave & smSubmit
    // ============================
    private function commonSmSave(Request $request, $submit)
    {
        $connectionType = $request->input('connection_type');

        $rules = [];

        for ($i = 1; $i <= 4; $i++) {

            $rules["vendor{$i}_name"] = "nullable|string";

            if ($request->input("vendor{$i}_name")) {
                $rules["vendor{$i}_arc"] = "required|string";
                $rules["vendor{$i}_otc"] = "required|string";
                $rules["vendor{$i}_delivery_timeline"] = "required|string";

                if ($connectionType == "ILL") {
                    $rules["vendor{$i}_static_ip_cost"] = "nullable|string";
                } else {
                    $rules["vendor{$i}_static_ip_cost"] = "required|string";
                }
            }
        }

        $data = $request->validate($rules);
        $data['feasibility_id'] = $request->feasibility_id;

        // If submit button clicked → change status
        if ($submit) {
            $data['status'] = "sm_completed";
        }

        FeasibilityStatus::updateOrCreate(
            ['feasibility_id' => $data['feasibility_id']],
            $data
        );

        return back()->with('success', $submit ? 'Submitted Successfully' : 'Saved Successfully');
    }

    // ====================================
    // operations Methods (Read-only)
    // ====================================

    public function operationsOpen(Request $request)
    {
        $permissions = TemplateHelper::getUserMenuPermissions('operations Feasibility', 'Operations Feasibility Open') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $records = FeasibilityStatus::with(['feasibility', 'feasibility.client'])
            ->where('status', 'Open')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        // $permissions = $this->getOperationsFeasibilityPermissions();
        return view('operations.feasibility.open', compact('records', 'permissions'));
    }

    public function operationsInProgress(Request $request)
    {
        $permissions = TemplateHelper::getUserMenuPermissions('operations Feasibility', 'Operations Feasibility In Progress') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

         $perPage = (int) $request->get('per_page', 10);
         $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $records = FeasibilityStatus::with(['feasibility', 'feasibility.client'])
            ->where('status', 'InProgress')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
           
        $permissions = $this->getOperationsFeasibilityPermissions();
        return view('operations.feasibility.inprogress', compact('records', 'permissions'));
    }

    public function operationsClosed(Request $request)
    {
        $permissions = TemplateHelper::getUserMenuPermissions('operations Feasibility', 'Operations Feasibility Closed') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];


        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $records = FeasibilityStatus::with(['feasibility', 'feasibility.client'])
            ->where('status', 'Closed')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return view('operations.feasibility.closed', compact('records', 'permissions'));
    }

    public function operationsView($id)
    {
        $record = FeasibilityStatus::with(['feasibility', 'feasibility.client'])->findOrFail($id);
        return view('operations.feasibility.view', compact('record'));
    }

    public function operationsEdit($id)
    {
        $record = FeasibilityStatus::with(['feasibility', 'feasibility.client'])->findOrFail($id);
        
        // ✅ Get all vendors for dropdown
        $vendors = \App\Models\Vendor::orderBy('vendor_name')->get();
        
        return view('operations.feasibility.edit', compact('record', 'vendors'));
    }

   // ============================
    // OPERATIONS SAVE
    // ============================
    public function operationsSave(Request $request, $id)
{
    $data = $request->validate([
            'vendor1_name' => 'nullable|string',
            'vendor1_arc' => 'nullable|string',
            'vendor1_otc' => 'nullable|string',
            'vendor1_static_ip_cost' => 'nullable|string',
            'vendor1_delivery_timeline' => 'nullable|string',

            'vendor2_name' => 'nullable|string',
            'vendor2_arc' => 'nullable|string',
            'vendor2_otc' => 'nullable|string',
            'vendor2_static_ip_cost' => 'nullable|string',
            'vendor2_delivery_timeline' => 'nullable|string',

            'vendor3_name' => 'nullable|string',
            'vendor3_arc' => 'nullable|string',
            'vendor3_otc' => 'nullable|string',
            'vendor3_static_ip_cost' => 'nullable|string',
            'vendor3_delivery_timeline' => 'nullable|string',

            'vendor4_name' => 'nullable|string',
            'vendor4_arc' => 'nullable|string',
            'vendor4_otc' => 'nullable|string',
            'vendor4_static_ip_cost' => 'nullable|string',
            'vendor4_delivery_timeline' => 'nullable|string',
        ]);

        $record = FeasibilityStatus::findOrFail($id);
        
        $data['status'] = 'InProgress';
        $record->update($data);

    $connectionType = $request->input('connection_type');

    $rules = [];

    for ($i = 1; $i <= 4; $i++) {
        $rules["vendor{$i}_name"] = "nullable|string";

        if ($request->input("vendor{$i}_name")) {
            $rules["vendor{$i}_arc"] = "required|string";
            $rules["vendor{$i}_otc"] = "required|string";
            $rules["vendor{$i}_delivery_timeline"] = "required|string";

            $rules["vendor{$i}_static_ip_cost"] =
                $connectionType === "ILL" ? "nullable|string" : "required|string";
        }
    }

    $data = $request->validate($rules);

    // ✅ Load the correct record
    // $record = FeasibilityStatus::findOrFail($id);

    // ✅ Update vendors
    $record->update($data);

    // ✅ THIS IS THE MAIN FIX
    $record->status = 'InProgress';
    $record->save();

    return redirect()
        ->route('operations.feasibility.inprogress')
        ->with('success', 'Feasibility moved to In Progress successfully');
}

    public function operationsSubmit(Request $request, $id)
    {
        $data = $request->validate([
            'vendor1_name' => 'nullable|string',
            'vendor1_arc' => 'nullable|string',
            'vendor1_otc' => 'nullable|string',
            'vendor1_static_ip_cost' => 'nullable|string',
            'vendor1_delivery_timeline' => 'nullable|string',

            'vendor2_name' => 'nullable|string',
            'vendor2_arc' => 'nullable|string',
            'vendor2_otc' => 'nullable|string',
            'vendor2_static_ip_cost' => 'nullable|string',
            'vendor2_delivery_timeline' => 'nullable|string',

            'vendor3_name' => 'nullable|string',
            'vendor3_arc' => 'nullable|string',
            'vendor3_otc' => 'nullable|string',
            'vendor3_static_ip_cost' => 'nullable|string',
            'vendor3_delivery_timeline' => 'nullable|string',

            'vendor4_name' => 'nullable|string',
            'vendor4_arc' => 'nullable|string',
            'vendor4_otc' => 'nullable|string',
            'vendor4_static_ip_cost' => 'nullable|string',
            'vendor4_delivery_timeline' => 'nullable|string',
        ]);

        $record = FeasibilityStatus::findOrFail($id);
        $previousStatus = $record->status;
        
        $data['status'] = 'Closed';
        $record->update($data);

        // � Auto-create deliverable when feasibility is closed
        // $this->createDeliverableFromFeasibility($record);
        
        // �📧 Send email notification for status change
        $this->sendStatusChangeEmail($record, 'Closed', $previousStatus);

        return redirect()->route('operations.feasibility.closed')
            ->with('success', 'Feasibility closed and deliverable created successfully!');
    }

    private function getOperationsFeasibilityPermissions()
    {
        return TemplateHelper::getUserMenuPermissions('operations Feasibility') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];
    }

    /**
     * Move feasibility record from S&M to operations
     */
    public function moveTooperations($id)
    {
        $record = FeasibilityStatus::findOrFail($id);
        
        // You can add additional logic here if needed
        // For now, the record stays the same but will be viewed from operations perspective
        
        return redirect()->route('operations.feasibility.open')
            ->with('success', 'Feasibility record moved to operations successfully.');
    }

    /**
     * Move feasibility record from operations to S&M
     */
    public function moveToSM($id)
    {
        $record = FeasibilityStatus::findOrFail($id);
        
        // You can add additional logic here if needed
        // For now, the record stays the same but will be viewed from S&M perspective
        
        return redirect()->route('sm.feasibility.open')
            ->with('success', 'Feasibility record moved to S&M successfully.');
    }

    /**
 * Send email notification for status change
 *
 * @param \App\Models\FeasibilityStatus $feasibilityStatus
 * @param string $newStatus
 * @param string|null $previousStatus
 * @return void
 */
    private function sendStatusChangeEmail($feasibilityStatus, $newStatus, $previousStatus = null)
    {
        try {
            $feasibility = $feasibilityStatus->feasibility;
            $actionBy = Auth::user();
            
            // 🔍 DEBUG: Log the status change attempt
            Log::info('🔍 Email trigger attempt', [
                'feasibility_id' => $feasibility->id,
                'new_status' => $newStatus,
                'previous_status' => $previousStatus,
                'action_by' => $actionBy->name ?? 'Unknown'
            ]);
            
            // Determine who should receive the email based on status
            $recipients = $this->getEmailRecipients($feasibility, $newStatus, $previousStatus);
            
            // 🔍 DEBUG: Log recipients found
            Log::info('🔍 Email recipients found', [
                'recipients_count' => is_array($recipients) ? count($recipients) : 0,
                'recipients' => $recipients ?? [],
                'feasibility_spoc' => $feasibility->spoc_email ?? 'No SPOC',
                'feasibility_creator' => $feasibility->createdBy->email ?? 'No creator email'
            ]);
            
            if (empty($recipients)) {
                Log::warning('⚠️ No email recipients found for feasibility status change', [
                    'feasibility_id' => $feasibility->id,
                    'new_status' => $newStatus,
                    'previous_status' => $previousStatus
                ]);
                return; // Exit early if no recipients
            }
            
            foreach ($recipients as $recipient) {
                if ($recipient && filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($recipient)->send(new FeasibilityStatusMail(
                        $feasibility, 
                        $newStatus, 
                        $previousStatus, 
                        $actionBy, 
                        'status_change'
                    ));
                }
            }
            
            // Log the email sending for debugging
            Log::info('✅ Feasibility status email sent successfully', [
                'feasibility_id' => $feasibility->id,
                'new_status' => $newStatus,
                'previous_status' => $previousStatus,
                'recipients' => $recipients
            ]);
            
        } catch (\Exception $e) {
            // Log error but don't break the flow since this is a live system
            Log::error('Failed to send feasibility status email', [
                'error' => $e->getMessage(),
                'feasibility_id' => $feasibilityStatus->feasibility_id ?? 'unknown'
            ]);
        }
    }

    /**
     * Get email recipients based on status change
     *
     * @param \App\Models\Feasibility $feasibility The feasibility record
     * @param string $newStatus The new status
     * @return array Array of email addresses
     */
    private function getEmailRecipients($feasibility, $newStatus, $previousStatus = null)
{
    // Open → Send to Operations Team
    if ($newStatus == 'Open') {
        return \App\Models\User::whereHas('userType', function ($q) {
            $q->where('name', 'Team OPS');
        })
        ->whereNotNull('official_email')
        ->pluck('official_email')
        ->toArray();
    }

    // Closed → Send ONLY to Creator (S&M)
    if ($newStatus == 'Closed' && $feasibility->createdByUser) {
        $creatorEmail = $feasibility->createdByUser->official_email ?? $feasibility->createdByUser->email;
        return $creatorEmail ? [$creatorEmail] : [];
    }

    return [];
}
}
