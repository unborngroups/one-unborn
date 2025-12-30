<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ModelType;
use App\Helpers\TemplateHelper;

class ModelTypeController extends Controller
{
    public function index()
    {
        // $makeTypes = MakeType::with('company')
        //     ->orderBy('created_at', 'desc')
        //     ->get();
        $modelTypes = ModelType::orderBy('id', 'asc')->paginate(20);

            $permissions = TemplateHelper::getUserMenuPermissions('Model Type') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

        return view('assetmaster.model_type.index', compact('modelTypes', 'permissions'));
    }

    public function create()
    {
        // $companies = Company::orderBy('company_name')->get();
        return view('assetmaster.model_type.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // 'company_id' => 'required|exists:companies,id',
            'model_name' => 'required|string|max:255',
        ]);

        ModelType::create($data);

        return redirect()->route('assetmaster.model_type.index')
            ->with('success', 'Model Type added successfully.');
    }

    public function edit(ModelType $modelType)
    {
        // $companies = Company::orderBy('company_name')->get();
        return view('assetmaster.model_type.edit', compact('modelType'));
    }

    public function update(Request $request, ModelType $modelType)
    {
        $data = $request->validate([
            // 'company_id' => 'required|exists:companies,id',
            'model_name' => 'required|string|max:255',
        ]);

        $modelType->update($data);

        return redirect()->route('assetmaster.model_type.index')
            ->with('success', 'Model Type updated successfully.');
    }

    public function destroy(ModelType $modelType)
    {
        $modelType->delete();

        return redirect()->route('assetmaster.model_type.index')
            ->with('success', 'Model Type deleted.');
    }

    /**
     * Bulk delete clients selected from the index table.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:model_types,id',
        ]);

        ModelType::whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('assetmaster.model_type.index')
            ->with('success', count($request->input('ids')) . ' model type(s) deleted successfully.');
    }

}
