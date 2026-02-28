<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Items;
use App\Helpers\TemplateHelper;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
         $permissions = TemplateHelper::getUserMenuPermissions('Items') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

        $perPage = (int) $request->get('per_page', 10);
    $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $items = Items::orderBy('id', 'desc')->paginate($perPage);
        return view('finance.items.index', compact('permissions', 'items'));
    }

    public function create()
    {
        return view('finance.items.create');
    }   

    public function store(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'item_name' => 'required|string|max:255',
            'item_description' => 'nullable|string',
            'item_rate' => 'required|numeric',
            'hsn_sac_code' => 'nullable|integer',
            'usage_unit' => 'required|string|max:50',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Create new item using the validated data
        items::create($validatedData);

        // Redirect back to items index with success message
        return redirect()->route('finance.items.index')
                         ->with('success', 'Item created successfully.');
    }

    public function view($id)
    {
        $items = Items::findOrFail($id);
        return view('finance.items.view', compact('items'));
    }

    public function edit($id)
    {
        $items = Items::findOrFail($id);
        return view('finance.items.edit', compact('items'));
    }

    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'item_name' => 'required|string|max:255',
            'item_description' => 'nullable|string',
            'item_rate' => 'required|numeric',
            'hsn_sac_code' => 'nullable|integer',
            'usage_unit' => 'required|string|max:50',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Find the item and update it with the validated data
        $items = Items::findOrFail($id);
        $items->update($validatedData);

        // Redirect back to items index with success message
        return redirect()->route('finance.items.index')
                         ->with('success', 'Item updated successfully.');
    }

    public function destroy($id)
    {
        $items = Items::findOrFail($id);
        $items->delete();

        return redirect()->route('finance.items.index')
                         ->with('success', 'Item deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $items = Items::findOrFail($id);
        $items->status = $items->status === 'Active' ? 'Inactive' : 'Active';
        $items->save();

        return redirect()->route('finance.items.index')
                         ->with('success', 'Item status updated successfully.');
    }

}
