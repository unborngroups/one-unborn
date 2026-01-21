<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TemplateHelper;
use App\Models\DeliverablePlan;
use App\Models\Termination;


class TerminationController extends Controller
{
    public function index()
    {
        $permissions = TemplateHelper::getUserMenuPermissions('Termination') ?? (object)[
            'can_menu'   => true,
            'can_add'    => true,
            'can_edit'   => true,
            'can_delete' => true,
            'can_view'   => true,
        ];
        $terminations = Termination::orderByDesc('id')->get();
        return view('operations.termination.index', compact('permissions', 'terminations'));
    }

    public function create()
    {
        $deliverables_plans = DeliverablePlan::with(['deliverable.feasibility.company', 'deliverable'])->get();

        // Map all required fields for auto-fill
        $plans = $deliverables_plans->map(function($plan) {
            $deliverable = $plan->deliverable;
            $company = optional(optional($deliverable)->feasibility)->company;
            // Prefer company address if deliverable site_address is empty
            $address = !empty($deliverable->site_address) ? $deliverable->site_address : ($company->address ?? '');
            $asset = null;
            $asset_id = '';
            $asset_mac = '';
            $asset_serial = '';
            if (!empty($deliverable->asset_id)) {
                $asset = \App\Models\Asset::find($deliverable->asset_id);
                if ($asset) {
                    $asset_id = $asset->id ?? $deliverable->asset_id ?? '';
                    $asset_mac = $asset->mac_no ?? $deliverable->asset_mac_no ?? '';
                    $asset_serial = $asset->serial_no ?? $deliverable->asset_serial_no ?? '';
                } else {
                    $asset_mac = $deliverable->asset_mac_no ?? '';
                    $asset_serial = $deliverable->asset_serial_no ?? '';
                }
            } else {
                $asset_mac = $deliverable->asset_mac_no ?? '';
                $asset_serial = $deliverable->asset_serial_no ?? '';
            }

            return [
                'id' => $plan->id,
                'deliverable_id' => $plan->deliverable_id,
                'circuit_id' => $plan->circuit_id,
                'company_name' => $company->company_name ?? '',
                'address' => $address,
                'bandwidth' => $plan->speed_in_mbps_plan ?? $deliverable->speed_in_mbps ?? '',
                'asset_id' => $asset_id,
                'asset_mac' => $asset_mac,
                'asset_serial' => $asset_serial,
                'date_of_activation' => $plan->date_of_activation ? $plan->date_of_activation->format('Y-m-d') : ($deliverable->date_of_activation ?? ''),
                'date_of_delivered' => $deliverable->delivered_at ? (is_string($deliverable->delivered_at) ? $deliverable->delivered_at : $deliverable->delivered_at->format('Y-m-d')) : '',
                'date_of_last_renewal' => '', // You may fill this from Renewal if needed
                'date_of_expiry' => $plan->date_of_expiry ? $plan->date_of_expiry->format('Y-m-d') : '',
            ];
        });
        return view('operations.termination.create', ['deliverables_plans' => $plans]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'deliverable_id' => 'required',
            // 'circuit_id' => 'required',
            'company_name' => 'required',
            'address' => 'required',
            'bandwidth' => 'nullable',
            'asset_id' => 'nullable',
            'asset_mac' => 'nullable',
            'asset_serial' => 'nullable',
            'date_of_activation' => 'nullable|date',
            'date_of_delivered' => 'nullable|date',
            'date_of_last_renewal' => 'nullable|date',
            'date_of_expiry' => 'nullable|date',
            'termination_request_date' => 'nullable|date',
            'termination_requested_by' => 'nullable',
            'termination_request_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'termination_date' => 'nullable|date',
            'status' => 'nullable',
        ]);

        
    // ✅ File upload
    if ($request->hasFile('termination_request_document')) {
        $file = $request->file('termination_request_document');
        $filename = time() . '_' . $file->getClientOriginalName();

        $file->move(public_path('images/termination_req_doc'), $filename);

        $data['termination_request_document'] =
            'images/termination_req_doc/' . $filename;
    }

        Termination::create($data);

        return redirect()->route('operations.termination.index')->with('success', 'Termination created successfully.');
    }

        public function view($id)
    {
        $termination = Termination::findOrFail($id);
        return view('operations.termination.view', compact('termination'));
    }

    public function edit($id)
    {
        $termination = Termination::findOrFail($id);
        return view('operations.termination.edit', compact('termination'));
    }

    public function update(Request $request, $id)
    {
        $termination = Termination::findOrFail($id);
        $data = $request->validate([
            'company_name' => 'required',
            'address' => 'required',
            'bandwidth' => 'nullable',
            'asset_id' => 'nullable',
            'asset_mac' => 'nullable',
            'asset_serial' => 'nullable',
            'date_of_activation' => 'nullable|date',
            'date_of_delivered' => 'nullable|date',
            'date_of_last_renewal' => 'nullable|date',
            'date_of_expiry' => 'nullable|date',
            'termination_request_date' => 'nullable|date',
            'termination_requested_by' => 'nullable',
            'termination_request_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'termination_date' => 'nullable|date',
            'status' => 'nullable',
        ]);
        $termination->update($data);
        return redirect()->route('termination.index')->with('success', 'Termination updated successfully.');
    }
}
