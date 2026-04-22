<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveType;
use App\Helpers\TemplateHelper;
// 
class LeaveTypeController extends Controller
{
    public function index()
    {
         $permissions = TemplateHelper::getUserMenuPermissions('Leave Management') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

        // Fetch paginated leave types from DB
        $leavetypetable = LeaveType::paginate(10);
        return view('hr.leavetype.index', compact('leavetypetable', 'permissions'));
    }

    /**
     * Update the specified leave type in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'leavetype' => 'required|string|max:255',
            'shortcode' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        $leavetype = LeaveType::findOrFail($id);
        $leavetype->update($validated);

        return redirect()->route('hr.leavetype.index')->with('success', 'Leave type updated successfully.');
    }

    /**
     * Remove the specified leave type from storage.
     */
    public function destroy($id)
    {
        $leavetype = LeaveType::findOrFail($id);
        $leavetype->delete();
        return redirect()->route('hr.leavetype.index')->with('success', 'Leave type deleted successfully.');
    }

    

        /**
         * Show the form for editing the specified leave type.
         */
        public function edit($id)
        {
            $leavetypetable = LeaveType::findOrFail($id);
            return view('hr.leavetype.edit', compact('leavetypetable'));
        }
    
    public function create()
    {
        return view('hr.leavetype.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leavetype' => 'required|string|max:255',
            'shortcode' => 'required|string|max:255',
        ]);

        LeaveType::create([
            'leavetype' => $validated['leavetype'],
            'shortcode' => $validated['shortcode'],
            'status' => 'Active',
        ]);

        return redirect()->route('hr.leavetype.index')->with('success', 'Leave type created successfully.');
    }
    /**
     * Display the specified leave type.
     */
    public function view($id)
    {
        $leavetypetable = LeaveType::findOrFail($id);
        return view('hr.leavetype.view', compact('leavetypetable'));
    }

    
 /**
     * Bulk delete leave types selected from the index table.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:leave_types,id',
        ]);

        LeaveType::whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('hr.leavetype.index')
            ->with('success', count($request->input('ids')) . ' leave type(s) deleted successfully.');
    }
}