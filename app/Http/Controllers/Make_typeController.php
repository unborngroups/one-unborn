<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MakeType;
use App\Models\Company;
use App\Helpers\TemplateHelper;

class Make_typeController extends Controller
{
    public function index(Request $request)
    {
        // $makeTypes = MakeType::with('company')
        //     ->orderBy('created_at', 'desc')
        //     ->get();
        // $makeTypes = MakeType::orderBy('id', 'asc')->paginate(20);

            $permissions = TemplateHelper::getUserMenuPermissions('Make Type') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];
        // Per page (frontend only)
    $perPage = (int) $request->get('per_page', 10);
    $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

    // Paginated vendors
    $makeTypes = MakeType::orderBy('id', 'asc')->paginate($perPage);

        return view('assetmaster.make_type.index', compact('makeTypes', 'permissions', 'perPage'));
    }

    public function create()
    {
        // $companies = Company::orderBy('company_name')->get();
        return view('assetmaster.make_type.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // 'company_id' => 'required|exists:companies,id',
            'make_name' => 'required|string|max:255',
        ]);

        MakeType::create($data);

        return redirect()->route('assetmaster.make_type.index')
            ->with('success', 'Make Type added successfully.');
    }

    public function edit(MakeType $makeType)
    {
        // $companies = Company::orderBy('company_name')->get();
        return view('assetmaster.make_type.edit', compact('makeType'));
    }

    public function update(Request $request, MakeType $makeType)
    {
        $data = $request->validate([
            // 'company_id' => 'required|exists:companies,id',
            'make_name' => 'required|string|max:255',
        ]);

        $makeType->update($data);

        return redirect()->route('assetmaster.make_type.index')
            ->with('success', 'Make Type updated successfully.');
    }

    public function destroy(MakeType $makeType)
    {
        $makeType->delete();

        return redirect()->route('assetmaster.make_type.index')
            ->with('success', 'Make Type deleted.');
    }

    /**
     * Bulk delete clients selected from the index table.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:make_types,id',
        ]);

        MakeType::whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('assetmaster.make_type.index')
            ->with('success', count($request->input('ids')) . ' make type(s) deleted successfully.');
    }

}
