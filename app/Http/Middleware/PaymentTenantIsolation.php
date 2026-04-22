<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentTenantIsolation
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get company from request
        $companyId = $this->getCompanyIdFromRequest($request);
        
        if (!$companyId) {
            return response()->json(['error' => 'Company ID required'], 400);
        }

        // Validate user has access to this company
        if (!$this->userHasCompanyAccess($user, $companyId)) {
            Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'company_id' => $companyId,
                'ip' => $request->ip(),
                'route' => $request->route()->getName(),
            ]);
            
            return response()->json(['error' => 'Access denied to this company'], 403);
        }

        // Add company context to request
        $request->merge(['_company_id' => $companyId]);

        return $next($request);
    }

    /**
     * Get company ID from request
     */
    private function getCompanyIdFromRequest(Request $request): ?int
    {
        // Try route parameter
        $companyId = $request->route('company_id');
        if ($companyId) {
            return (int) $companyId;
        }

        // Try query parameter
        $companyId = $request->query('company_id');
        if ($companyId) {
            return (int) $companyId;
        }

        // Try from authenticated user's current company
        if (Auth::user() && Auth::user()->current_company_id) {
            return Auth::user()->current_company_id;
        }

        return null;
    }

    /**
     * Check if user has access to company
     */
    private function userHasCompanyAccess($user, int $companyId): bool
    {
        // Super admin has access to all companies
        if ($user->role === 'super_admin') {
            return true;
        }

        // Check user's company assignments
        $userCompanies = $user->companies()->pluck('id')->toArray();
        
        return in_array($companyId, $userCompanies);
    }

    /**
     * Validate tenant isolation for payment operations
     */
    public static function validatePaymentAccess(int $userId, int $companyId): bool
    {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return false;
        }

        // Check company payment settings
        $paymentSettings = \App\Models\CompanyPaymentSettings::getForCompany($companyId);
        if (!$paymentSettings || !$paymentSettings->isolation_enabled) {
            return true; // Isolation disabled
        }

        return (new self())->userHasCompanyAccess($user, $companyId);
    }

    /**
     * Get isolated query scope for payments
     */
    public static function scopeIsolated($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
