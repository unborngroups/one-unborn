<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            Log::warning('Unauthorized invoice access attempt from IP: ' . $request->ip());
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Authentication required.',
                'error_code' => 'UNAUTHENTICATED'
            ], 401);
        }

        $user = auth()->user();

        // Check if user is active
        if (!$user->is_active ?? true) {
            Log::warning("Inactive user attempted access: {$user->id}");
            return response()->json([
                'success' => false,
                'message' => 'User account is inactive.',
                'error_code' => 'INACTIVE_USER'
            ], 403);
        }

        // Check if user has invoice management permission
        if (!$this->hasInvoicePermission($user)) {
            Log::warning("User without permission attempted invoice access: {$user->id}");
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access invoices.',
                'error_code' => 'FORBIDDEN'
            ], 403);
        }

        // Log successful authentication
        Log::info("Invoice access authorized for user: {$user->id} from IP: " . $request->ip());

        return $next($request);
    }

    /**
     * Check if user has invoice management permission.
     * Superadmins and Admins always have access.
     */
    private function hasInvoicePermission($user)
    {
        // Superadmin (type 1) or Admin (type 2) always have access
        if ($user->user_type_id <= 2) {
            return true;
        }

        // For regular users, check if they have invoice menu permission
        $hasPermission = $user->menuPrivileges()
            ->where('menu_id', function ($query) {
                $query->select('id')
                    ->from('menus')
                    ->where('route', 'like', '%invoice%')
                    ->orWhere('menu_name', 'like', '%invoice%');
            })
            ->where('can_view', true)
            ->exists();

        return $hasPermission;
    }
}
