<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TemplateHelper;
use App\Models\DeliverablePlan;
use App\Models\Termination;


class TerminationController extends Controller
{
    public function index(Request $request)
    {
        $permissions = TemplateHelper::getUserMenuPermissions('Termination') ?? (object)[
            'can_menu'   => true,
            'can_add'    => true,
            'can_edit'   => true,
            'can_delete' => true,
            'can_view'   => true,
        ];
       $perPage = (int) $request->get('per_page', 10);
       $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
         $terminations = Termination::query();
        if ($request->filled('search')) {
           $search = $request->search;
           $terminations->where(function($q) use ($search) {
               $q->where('company_name', 'like', "%{$search}%")
                   ->orWhere('address', 'like', "%{$search}%")
                   ->orWhere('bandwidth', 'like', "%{$search}%")
                   ->orWhere('asset_id', 'like', "%{$search}%")
                   ->orWhere('asset_mac', 'like', "%{$search}%")
                   ->orWhere('asset_serial', 'like', "%{$search}%")
                   ->orWhere('date_of_activation', 'like', "%{$search}%")
                   ->orWhere('date_of_last_renewal', 'like', "%{$search}%")
                   ->orWhere('date_of_expiry', 'like', "%{$search}%")
                   ->orWhere('termination_request_date', 'like', "%{$search}%")
                   ->orWhere('termination_requested_by', 'like', "%{$search}%")
                   ->orWhere('termination_request_document', 'like', "%{$search}%")
                   ->orWhere('termination_date', 'like', "%{$search}%");
           });
       }
        $terminations = Termination::orderByDesc('id','desc')->paginate($perPage)->withQueryString();

        return view('operations.termination.index', compact('permissions', 'terminations'));
    }

    public function create()
    {
        $deliverables_plans = DeliverablePlan::with(['deliverable.feasibility.company', 'deliverable'])->get();

        // Map all required fields for auto-fill
        $plans = $deliverables_plans->map(function($plan) {
            $deliverable = $plan->deliverable;
            $company = optional(optional($deliverable)->feasibility)->company;
            // Robust address fallback (now includes feasibility address)
            $address = '';
            if (!empty($deliverable->site_address)) {
                $address = $deliverable->site_address;
            } elseif (!empty($company->address)) {
                $address = $company->address;
            } elseif (!empty($deliverable->feasibility->address)) {
                $address = $deliverable->feasibility->address;
            } elseif (!empty($deliverable->client_address)) {
                $address = $deliverable->client_address;
            } else {
                $address = '[NO ADDRESS FOUND]';
            }

            $asset = null;
            $asset_id = '';
            $asset_mac = '';
            $asset_serial = '';
            if (!empty($deliverable->asset_id)) {
                $asset = \App\Models\Asset::where('asset_id', $deliverable->asset_id)->first();
                if ($asset) {
                    $asset_id = $asset->asset_id ?? $deliverable->asset_id ?? '';
                    $asset_mac = $asset->mac_no ?? $deliverable->asset_mac_no ?? '';
                    $asset_serial = $asset->serial_no ?? $deliverable->asset_serial_no ?? '';
                } else {
                    $asset_id = '[NO ASSET FOUND]';
                    $asset_mac = $deliverable->asset_mac_no ?? '';
                    $asset_serial = $deliverable->asset_serial_no ?? '';
                }
            } else {
                $asset_id = '[NO ASSET ID]';
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
                'date_of_last_renewal' => optional($plan->renewals()->orderByDesc('date_of_renewal')->first())->date_of_renewal ?? '',
                'date_of_expiry' => $plan->date_of_expiry ? $plan->date_of_expiry->format('Y-m-d') : '',
            ];
        });
        return view('operations.termination.create', ['deliverables_plans' => $plans]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'deliverable_id' => 'required',
            'circuit_id' => 'required',
            'company_name' => 'required',
            'address' => 'required',
            'bandwidth' => 'nullable',
            'asset_id' => 'nullable',
            'asset_mac' => 'nullable',
            'asset_serial' => 'nullable',
            'date_of_activation' => 'nullable|date',
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

        // Set status based on dates
        if (!empty($data['termination_date'])) {
            $data['status'] = 'Terminated';
        } elseif (!empty($data['termination_request_date'])) {
            $data['status'] = 'Pending';
        } else {
            $data['status'] = null; // or '-'
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
            'date_of_last_renewal' => 'nullable|date',
            'date_of_expiry' => 'nullable|date',
            'termination_request_date' => 'nullable|date',
            'termination_requested_by' => 'nullable',
            'termination_request_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'termination_date' => 'nullable|date',
            'status' => 'nullable',
        ]);
        // Set status based on dates
        if (!empty($data['termination_date'])) {
            $data['status'] = 'Terminated';
        } elseif (!empty($data['termination_request_date'])) {
            $data['status'] = 'Pending';
        } else {
            $data['status'] = null; // or '-'
        }
        $termination->update($data);
        return redirect()->route('operations.termination.index')->with('success', 'Termination updated successfully.');
    }

    /**
     * Bulk delete clients selected from the index table.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:terminations,id',
        ]);

        Termination::whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('operations.termination.index')
            ->with('success', count($request->input('ids')) . ' termination(s) deleted successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Termination $termination)
    {
        $termination->delete();
        return redirect()->route('operations.termination.index')->with('success', 'Termination deleted successfully.');
    }
}
