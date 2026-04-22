<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\UserMenuPrivilege;
use App\Models\UserType;
use App\Models\UserTypeMenuPrivilege;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('id')->get();
        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        return view('menus.create');
    }
     public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            // 'user_type' => 'required',
        ]);

        Menu::create($request->only(['name', 'route', 'icon', 'user_type']));
        return redirect()->route('menus.index')->with('success', 'Menu created successfully!');
    }

    public function edit(Menu $menu)
    {
        return view('menus.edit', compact('menu'));
    }
    public function update(Request $request, Menu $menu)
    {
        $menu->update($request->all());
        return redirect()->route('menus.index')->with('success', 'Menu updated successfully!');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully!');
    }

    // ✅ Show privilege edit page
    public function editPrivileges($userId)
    {
        $user = User::findOrFail($userId);

        // Show only menus relevant to user type

        // Show only main menu entries (sub_section == null)
        $menus = Menu::where('user_type', $user->user_type)
                ->orderBy('module_name')
                ->orderBy('id')
                ->get();

        // Group menus by module_name for UI
        $groupedMenus = $menus->groupBy('module_name');

        // 🟢 Load existing privileges for this user
        $userPrivileges = UserMenuPrivilege::where('user_id', $userId)
                    ->get()
                    ->keyBy('menu_id');

        return view('menus.editprivileges', compact('user', 'groupedMenus', 'menus', 'userPrivileges'));
    }

    // ✅ Save privileges
    public function updatePrivileges(Request $request, $userId)
    {
        $privileges = $request->input('privileges', []);

        // Delete all old privileges first (to prevent duplicates)
        UserMenuPrivilege::where('user_id', $userId)->delete();

        // Insert new privileges
        foreach ($privileges as $menuId => $rights) {
            UserMenuPrivilege::create([
                'user_id' => $userId,
                'menu_id' => $menuId,
                'can_menu' => isset($rights['can_menu']),
                'can_add' => isset($rights['can_add']),
                'can_edit' => isset($rights['can_edit']),
                'can_delete' => isset($rights['can_delete']),
                'can_view' => isset($rights['can_view']),
            ]);
        }
        return redirect()->route('users.index')->with('success', 'Menu privileges updated successfully!');
    }

    /**
     * ✅ Show user type privilege management page
     * 
     * This manages default privileges for a user type. When a user is created with this
     * user type, they will inherit these privileges automatically.
     * 
     * @param int $userTypeId - The user type ID to manage privileges for
     */
   public function editUserTypePrivileges($userTypeId)
{
    $userType = UserType::findOrFail($userTypeId);

    // FIXED: whereIn instead of where
    $menus = Menu::whereIn('user_type', ['superadmin', 'All', 'all'])
            
                ->orderBy('module_name')
                ->orderBy('id')
                ->get();

    // Show all menu entries including sub-sections
    $groupedMenus = $menus->groupBy('module_name');

    // Load privileges
    $userTypePrivileges = UserTypeMenuPrivilege::where('user_type_id', $userTypeId)
                            ->get()
                            ->keyBy('menu_id');

    return view('menus.edit-usertype-privileges', compact(
        'groupedMenus',
        'userType',
        'menus',
        'userTypePrivileges'
    )); 
}

    /**
     * ✅ Update user type privileges
     * 
     * Updates the default privileges for a user type. When new users are created with
     * this user type, they will automatically get these privileges.
     * 
     * @param Request $request
     * @param int $userTypeId
     */
    public function updateUserTypePrivileges(Request $request, $userTypeId)
    {
        $privileges = $request->input('privileges', []);

        // Delete all old user type privileges first
        UserTypeMenuPrivilege::where('user_type_id', $userTypeId)->delete();

        // Insert new user type privileges
        foreach ($privileges as $menuId => $rights) {
            UserTypeMenuPrivilege::create([
                'user_type_id' => $userTypeId,
                'menu_id' => $menuId,
                'can_menu' => isset($rights['can_menu']),
                'can_add' => isset($rights['can_add']),
                'can_edit' => isset($rights['can_edit']),
                'can_delete' => isset($rights['can_delete']),
                'can_view' => isset($rights['can_view']),
            ]);
        }

        // Sync the new defaults to every user who belongs to this type
        // Only update users who do NOT have custom privileges (skip users with any UserMenuPrivilege records)
        $users = User::where('user_type_id', $userTypeId)->get();
        foreach ($users as $user) {
            $hasCustom = UserMenuPrivilege::where('user_id', $user->id)->exists();
            if ($hasCustom) {
                // Skip users with custom privileges
                continue;
            }
            foreach ($privileges as $menuId => $rights) {
                UserMenuPrivilege::create([
                    'user_id' => $user->id,
                    'menu_id' => $menuId,
                    'can_menu' => isset($rights['can_menu']),
                    'can_add' => isset($rights['can_add']),
                    'can_edit' => isset($rights['can_edit']),
                    'can_delete' => isset($rights['can_delete']),
                    'can_view' => isset($rights['can_view']),
                ]);
            }
        }
        
        return redirect()->route('usertypetable.index')->with('success', 'User Type privileges updated successfully! New users with this type will inherit these privileges.');
    }
}
