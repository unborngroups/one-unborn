<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\UserTypeTable;
use App\Helpers\TemplateHelper;
use App\Models\UserType;


class UserTypeController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

         // ✅ Use the helper correctly
        $permissions = TemplateHelper::getUserMenuPermissions('User Type') ?? (object)[
    'can_add' => true,
    'can_edit' => true,
    'can_delete' => true,
    'can_view' => true,
];
$perPage = (int) $request->get('per_page', 10);
    $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $usertypetable = UserType::orderBy('id', 'asc')->paginate($perPage);

        return view('usertypetable.index', compact('usertypetable', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $usertypetable = UserType::all(); // Fetch all user types
        return view('usertypetable.create', compact('usertypetable'));
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'Description' => 'required|string|max:225',
            'status' => 'required|in:Active,Inactive',
        ]);
      UserType::create([
    'name'      => $request->name,
    'email'     => $request->email,
    'Description' => $request->Description,
    'status'    => $request->status,
]);


        return redirect()->route('usertypetable.index')->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
     public function edit(UserType $usertypetable)
    {
        return view('usertypetable.edit', compact('usertypetable'));
    }

     /**
     * Display the specified resource.
     */
    public function view($id)
{
    $usertypetable = \App\Models\UserType::findOrFail($id);
    return view('usertypetable.view', compact('usertypetable'));
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  UserType $usertypetable)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'Description' => 'required|string|max:225',
            'status' => 'required|in:Active,Inactive',
        ]);

        $usertypetable->update($request->all());
        return redirect()->route('usertypetable.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserType $usertypetable)
    {
         $usertypetable->delete();
        return redirect()->route('usertypetable.index')->with('success', 'User deleted successfully!');
    }
// Active or Inactive button

 public function toggleStatus($id)
{
    $usertypetable = UserType::findOrFail($id);

    // Toggle Active/Inactive
    $usertypetable->status = $usertypetable->status === 'Active' ? 'Inactive' : 'Active';
    $usertypetable->save();

    return redirect()->route('usertypetable.index')
                     ->with('success', 'usertype status updated successfully.');
}

 /**
     * Bulk delete clients selected from the index table.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:user_types,id',
        ]);

        UserType::whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('usertypetable.index')
            ->with('success', count($request->input('ids')) . ' user type(s) deleted successfully.');
    }
}
