<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetType;
use App\Models\Company;
use App\Helpers\TemplateHelper;

class Asset_typeController extends Controller
{
    public function index()
    {
        // $assetTypes = AssetType::with('company')
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(20);
        $assetTypes = AssetType::orderBy('id', 'asc')->paginate(20);

            $permissions = TemplateHelper::getUserMenuPermissions('Asset Type') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

        return view('assetmaster.asset_type.index', compact('assetTypes', 'permissions'));
    }

    public function create()
    {
        // $companies = Company::orderBy('company_name')->get();
        return view('assetmaster.asset_type.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // 'company_id' => 'required|exists:companies,id',
            'type_name' => 'required|string|max:255',
        ]);

        AssetType::create($data);

        return redirect()->route('assetmaster.asset_type.index')
            ->with('success', 'Asset Type added successfully.');
    }

    public function edit(AssetType $assetType)
    {
        // $companies = Company::orderBy('company_name')->get();
        return view('assetmaster.asset_type.edit', compact('assetType'));
    }

    public function update(Request $request, AssetType $assetType)
    {
        $data = $request->validate([
            // 'company_id' => 'required|exists:companies,id',
            'type_name' => 'required|string|max:255',
        ]);

        $assetType->update($data);

        return redirect()->route('assetmaster.asset_type.index')
            ->with('success', 'Asset Type updated successfully.');
    }

    public function destroy(AssetType $assetType)
    {
        $assetType->delete();

        return redirect()->route('assetmaster.asset_type.index')
            ->with('success', 'Asset Type deleted.');
    }
}
