<?php

namespace App\Helpers;

use App\Models\UserMenuPrivilege;
use App\Models\UserTypeMenuPrivilege;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TemplateHelper
{
    /**
     * Replace placeholders like {{name}}, {{company_name}} etc.
     */
    public static function renderTemplate($content, $data = [])
    {
        foreach ($data as $key => $value) {
            $content = str_replace('{{'.$key.'}}', $value, $content);
        }
        return $content;
    }

    /**
     * ✅ Get current logged-in user's permissions for a given menu name.
     */
    public static function getUserMenuPermissions($menuId, $subSection = null)
    {
        $user = Auth::user();

        if (!$user) {
            return (object)[
                'can_menu' => false,
                'can_add' => false,
                'can_edit' => false,
                'can_delete' => false,
                'can_view' => false,
            ];
        }

        // Get user's specific privileges for the requested menu


        $priv = UserMenuPrivilege::where('user_id', $user->id)
            ->whereHas('menu', function ($query) use ($menuId, $subSection) {
                $query->where('name', $menuId);
                if ($subSection !== null) {
                    $query->where('sub_section', $subSection);
                }
            })
            ->with('menu')
            ->first();

        if ($priv) {
            Log::info('✅ UserMenuPrivilege found for user_id=' . $user->id . ', menu=' . $menuId . ', privilege_id=' . $priv->id);
        } else {
            Log::warning('❌ No UserMenuPrivilege found for user_id=' . $user->id . ', menu=' . $menuId);
        }

        // If individual privilege exists, use it
        if ($priv) {
            Log::info('✅ Using individual privileges for user: ' . $user->name . ' on menu ID: ' . $menuId);
            return (object)[
                'can_menu' => (bool)$priv->can_menu,
                'can_add' => (bool)$priv->can_add,
                'can_edit' => (bool)$priv->can_edit,
                'can_delete' => (bool)$priv->can_delete,
                'can_view' => (bool)$priv->can_view,
            ];
        }

        // ✅ FALLBACK TO USER TYPE PRIVILEGES if no individual user privilege exists

        if ($user->userType) {
            Log::info('No individual privilege found for user: ' . $user->name . ', checking user type privileges for: ' . $menuId);
            // Check user type default privileges
            $userTypePriv = UserTypeMenuPrivilege::where('user_type_id', $user->user_type_id)
                ->whereHas('menu', function ($query) use ($menuId, $subSection) {
                    $query->where('name', $menuId);
                    if ($subSection !== null) {
                        $query->where('sub_section', $subSection);
                    }
                })
                ->with('menu')
                ->first();
            if ($userTypePriv) {
                Log::info('✅ Using user type privileges for user: ' . $user->name . ' on menu ID: ' . $menuId);
                return (object)[
                    'can_menu' => (bool)$userTypePriv->can_menu,
                    'can_add' => (bool)$userTypePriv->can_add,
                    'can_edit' => (bool)$userTypePriv->can_edit,
                    'can_delete' => (bool)$userTypePriv->can_delete,
                    'can_view' => (bool)$userTypePriv->can_view,
                ];
            }
        }

        // Return false for all if no privileges found
        Log::info('❌ No privileges found for user: ' . $user->name . ' on menu ID: ' . $menuId);
        return (object)[
            'can_menu' => false,
            'can_add' => false,
            'can_edit' => false,
            'can_delete' => false,
            'can_view' => false,
        ];
    }
}
