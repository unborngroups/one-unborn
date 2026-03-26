<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Items;
use App\Models\Deliverables;
use App\Helpers\TemplateHelper;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $items = Items::latest()->get();
        $permissions = TemplateHelper::getUserMenuPermissions('Items') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

        $perPage = $request->input('per_page', 10); // default 10
        $perPage = in_array($perPage, [10,25,50,100]) ? $perPage : 10;
        $query = Items::query();
        if ($request->filled('search')) {
                        $search = $request->search;
                        $query->where(function($q) use ($search) {
                                $q->where('item_name', 'like', "%{$search}%")
                                    ->orWhere('item_description', 'like', "%{$search}%");
                        });
        }
        $items = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        

        return view('finance.items.index', compact('items', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        return view('finance.items.create');    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_name'   => 'required',
            'item_description'  => 'required|string|max:255',
            'item_rate'   => 'required|string|max:255',
            'hsn_sac_code'   => 'required|string|max:255',
            'usage_unit'   => 'required|string|max:255',

        ]);

        Items::create($request->all());

         return redirect()
            ->route('finance.items.index')
            ->with('success', 'Item saved successfully');

        // return back()->with('success', 'Item Created');
    }

    /**
     * Display the specified resource.
     */
    public function view(string $id)
    {
        $items = Items::findOrFail($id);
        return view('finance.items.view', compact('items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $items = Items::findOrFail($id);
        return view('finance.items.edit', compact('items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Items $item)
    {
        $request->validate([
            'item_name'   => 'required',
            'item_description'  => 'required|string|max:255',
            'item_rate'   => 'required|string|max:255',
            'hsn_sac_code'   => 'required|string|max:255',
            'usage_unit'   => 'required|string|max:255',

        ]);

        $item->update([
        'item_name' => $request->item_name,
        'item_description' => $request->item_description,
        'item_rate' => $request->item_rate,
        'hsn_sac_code' => $request->hsn_sac_code,
        'usage_unit' => $request->usage_unit,
    ]);

         return redirect()
            ->route('finance.items.index')
            ->with('success', 'Item saved successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Items $item)
    {
        $item->delete();
        return back()->with('success', 'Item Deleted');
    }
}
