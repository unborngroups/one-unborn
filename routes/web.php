<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\TaxInvoiceSettingsController;
use App\Http\Controllers\FeasibilityStatusController;
use App\Http\Controllers\PincodeLookupController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\FeasibilityController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\FeasibilityExcelController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\SlaReportController;
use App\Http\Controllers\MikrotikController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DeliverablesController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\HrController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\Asset_typeController;
use App\Http\Controllers\make_typeController;
use App\Http\Controllers\VendorMakeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\StrategyController;
use App\Http\Controllers\AssuranceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use App\Models\Vendor;
use Carbon\Carbon;
use App\Models\Company;
use App\Http\Middleware\ClientAuth;
   

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('welcome'); // logged-in users → dashboard
    }
    return redirect()->route('login'); // guests → login
}); 
//
// 🔐 AUTH ROUTES
//
Route::middleware('guest')->group(function () {
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
});
// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Route::post('/logout', function () {
//     Auth::logout();
//     return redirect()->route('login');
// })->name('logout');

// Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');

// Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');



Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);


Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);


Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/create', [ProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/view', [ProfileController::class, 'view'])->name('profile.view');
});

//
// 🚫 MAIN APP ACCESS — only if profile created (or superadmin)
//
Route::middleware(['auth', \App\Http\Middleware\CheckProfileCreated::class])->group(function () {

// Route::middleware(['auth', 'profile.created'])->group(function () {
// Route::middleware(['auth', CheckProfileCreated::class])->group(function () {
    // 🏠 Dashboard
    Route::get('/welcome', [DashboardController::class, 'index'])
        ->name('welcome');

    //     // 👤 Users (Privilege control)
    // 👤 User routes (Privilege controlled)
Route::get('users', [UserController::class, 'index'])
    ->middleware(\App\Http\Middleware\CheckPrivilege::class .':view')
    ->name('users.index');

Route::get('users/create', [UserController::class, 'create'])
    ->middleware(\App\Http\Middleware\CheckPrivilege::class .':add')
    ->name('users.create');

Route::post('users', [UserController::class, 'store'])
    ->middleware(\App\Http\Middleware\CheckPrivilege::class .':add')
    ->name('users.store');

Route::get('users/{user}/edit', [UserController::class, 'edit'])
    ->middleware(\App\Http\Middleware\CheckPrivilege::class .':edit')
    ->name('users.edit');

Route::put('users/{user}', [UserController::class, 'update'])
    ->middleware(\App\Http\Middleware\CheckPrivilege::class .':edit')
    ->name('users.update');

Route::delete('users/{user}', [UserController::class, 'destroy'])
    ->middleware(\App\Http\Middleware\CheckPrivilege::class .':delete')
    ->name('users.destroy');

    Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
   


    // 👤 Users, Companies, Vendors, Clients, Emails, etc.
    Route::resources([
        // 'users' => UserController::class,
        'companies' => CompanyController::class,
        'vendors' => VendorController::class,
        'clients' => ClientController::class,
        'emails' => EmailTemplateController::class,
    ]);

    //view path
    Route::get('/users/{id}/view', [UserController::class, 'view'])->name('users.view');
    Route::get('/usertypetable/{id}/view', [App\Http\Controllers\UserTypeController::class, 'view'])->name('usertypetable.view');
    Route::get('/companies/{id}/view', [App\Http\Controllers\CompanyController::class, 'view'])->name('companies.view');
    Route::get('/clients/{id}/view', [App\Http\Controllers\ClientController::class, 'view'])->name('clients.view');
    Route::get('/vendors/{id}/view', [App\Http\Controllers\VendorController::class, 'view'])->name('vendors.view');

    // ⚙️ Settings Routes
    Route::get('/company-settings', [CompanySettingsController::class, 'index'])->name('settings.company');
    Route::put('/company-settings', [CompanySettingsController::class, 'update'])->name('company.settings.update');
    Route::get('/tax-invoice-settings', [TaxInvoiceSettingsController::class, 'index'])->name('settings.tax.invoice');
    Route::put('/tax-invoice-settings', [TaxInvoiceSettingsController::class, 'update'])->name('settings.tax.invoice.update');
    Route::get('/system-settings', [SystemSettingsController::class, 'index'])->name('settings.system');
    Route::post('/system-settings', [SystemSettingsController::class, 'update'])->name('settings.system.update');
    
    // 📱 WhatsApp Settings
    Route::get('/whatsapp-settings', [App\Http\Controllers\WhatsAppSettingsController::class, 'index'])->name('settings.whatsapp');
    Route::post('/whatsapp-settings', [App\Http\Controllers\WhatsAppSettingsController::class, 'update'])->name('settings.whatsapp.update');
    Route::get('/whatsapp-test', [App\Http\Controllers\WhatsAppSettingsController::class, 'showTestForm'])->name('settings.whatsapp.test');
    Route::post('/whatsapp-test', [App\Http\Controllers\WhatsAppSettingsController::class, 'sendTestMessage'])->name('settings.whatsapp.test.send');

     // 📋 Menus (secured inside main app)
    
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
Route::get('/menus/create', [MenuController::class, 'create'])->name('menus.create');
Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');

// ✅ User Privileges
Route::get('/menus/privileges/{userId}', [MenuController::class, 'editPrivileges'])->name('menus.editPrivileges');
Route::post('/menus/privileges/{userId}', [MenuController::class, 'updatePrivileges'])->name('menus.updatePrivileges');

// ✅ User Type Privileges 
Route::get('/menus/usertype-privileges/{userTypeId}', [MenuController::class, 'editUserTypePrivileges'])->name('menus.editUserTypePrivileges');
Route::post('/menus/usertype-privileges/{userTypeId}', [MenuController::class, 'updateUserTypePrivileges'])->name('menus.updateUserTypePrivileges');

    // 🧩 User Type Master
    Route::resource('usertypetable', UserTypeController::class);
    Route::patch('/usertypetable/{id}/toggle-status', [UserTypeController::class, 'toggleStatus'])->name('usertypetable.toggle-status');

    // 🏢 Company Config + Status
    Route::patch('/companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
    Route::get('/companies/{id}/email-config', [CompanyController::class, 'emailConfig'])->name('companies.email.config');
    Route::post('/companies/{id}/email-config', [CompanyController::class, 'saveEmailConfig'])->name('companies.save.email.config');
    
    // 🔍 Company PAN Fetch for Client Form
    Route::get('/company/fetch/{pan}', [CompanyController::class, 'fetchByPan'])->name('company.fetch-by-pan');

    // 📧 Template Toggle
    Route::patch('/templates/{id}/toggle-status', [EmailTemplateController::class, 'toggleStatus'])->name('templates.toggle-status');

    // 👨‍💼 Client Toggle
    Route::patch('/clients/{id}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggle-status');

    // 🧾 Vendor Toggle
    Route::patch('/vendors/{id}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendors.toggle-status');

    // 🏢 GSTIN by PAN API Routes
    Route::post('/clients/fetch-gstin-by-pan', [ClientController::class, 'fetchGstinByPan'])->name('clients.fetch-gstin-by-pan');
    Route::post('/clients/save-selected-gstins', [ClientController::class, 'saveSelectedGstins'])->name('clients.save-selected-gstins');
    Route::post('/vendors/fetch-gstin-by-pan', [VendorController::class, 'fetchGstinByPan'])->name('vendors.fetch-gstin-by-pan');
    Route::post('/vendors/save-selected-gstins', [VendorController::class, 'saveSelectedGstins'])->name('vendors.save-selected-gstins');

    // vendor makes (API)
Route::get('vendor-makes/{id}', [\App\Http\Controllers\VendorMakeController::class, 'show'])->name('vendor-makes.show');
Route::get('/get-make-details/{id}', [VendorController::class, 'getMakeDetails']);
Route::get('/get-models/{make}', [FeasibilityController::class, 'getModels']);

// vendors store/update
Route::resource('vendors', \App\Http\Controllers\VendorController::class);

// barcode image
Route::get('vendors/{assetId}/barcode.png', [\App\Http\Controllers\VendorController::class, 'barcode'])->name('vendors.barcode');
Route::get('vendor-makes', [\App\Http\Controllers\VendorMakeController::class, 'index'])->name('vendor-makes.index'); // optional

//
// 📧 TEST EMAIL ROUTE
//
Route::get('/test-email', function () {
    Mail::raw('This is a test email from Laravel!', function ($message) {
        $message->to('your_email@example.com')->subject('Test Email');
    });
    return 'Email sent!';
});
    // 🧾 GST & PAN Fetch Routes
    // Route::get('/gst/fetch/{gstin}', [GSTController::class, 'fetch']);
    // Route::get('/company/fetch/{pan}', [GSTController::class, 'fetchByPAN']);

    // ✅ Sales & Marketing Feasibility Routes
    Route::get('/sm/feasibility/open', [FeasibilityStatusController::class, 'smOpen'])->name('sm.feasibility.open');
    Route::get('/sm/feasibility/inprogress', [FeasibilityStatusController::class, 'smInProgress'])->name('sm.feasibility.inprogress');
    Route::get('/sm/feasibility/closed', [FeasibilityStatusController::class, 'smClosed'])->name('sm.feasibility.closed');
    Route::get('/sm/feasibility/{id}/view', [FeasibilityStatusController::class, 'smView'])->name('sm.feasibility.view');
    Route::get('/sm/feasibility/{id}/edit', [FeasibilityStatusController::class, 'smEdit'])->name('sm.feasibility.edit');
    Route::post('/sm/feasibility/{id}/save', [FeasibilityStatusController::class, 'smSave'])->name('sm.feasibility.save');
    Route::post('/sm/feasibility/{id}/submit', [FeasibilityStatusController::class, 'smSubmit'])->name('sm.feasibility.submit');

    // ✅ operations Feasibility Routes (Full functionality like S&M)
    Route::get('/operations/feasibility/open', [FeasibilityStatusController::class, 'operationsOpen'])->name('operations.feasibility.open');
    Route::get('/operations/feasibility/inprogress', [FeasibilityStatusController::class, 'operationsInProgress'])->name('operations.feasibility.inprogress');
    Route::get('/operations/feasibility/closed', [FeasibilityStatusController::class, 'operationsClosed'])->name('operations.feasibility.closed');
    Route::get('/operations/feasibility/{id}/view', [FeasibilityStatusController::class, 'operationsView'])->name('operations.feasibility.view');
    Route::get('/operations/feasibility/{id}/edit', [FeasibilityStatusController::class, 'operationsEdit'])->name('operations.feasibility.edit');
    Route::post('/operations/feasibility/{id}/save', [FeasibilityStatusController::class, 'operationsSave'])->name('operations.feasibility.save');
    Route::post('/operations/feasibility/{id}/submit', [FeasibilityStatusController::class, 'operationsSubmit'])->name('operations.feasibility.submit');
    

    // ✅ operations Feasibility Routes (Full functionality like S&M)
    Route::get('/operations/deliverables/open', [DeliverablesController::class, 'operationsOpen'])->name('operations.deliverables.open');
    Route::get('/operations/deliverables/inprogress', [DeliverablesController::class, 'operationsInProgress'])->name('operations.deliverables.inprogress');
    Route::get('/operations/deliverables/delivery', [DeliverablesController::class, 'operationsDelivery'])->name('operations.deliverables.delivery');
    Route::get('/operations/deliverables/{id}/view', [DeliverablesController::class, 'operationsView'])->name('operations.deliverables.view');
    Route::get('/operations/deliverables/{id}/edit', [DeliverablesController::class, 'operationsEdit'])->name('operations.deliverables.edit');
    Route::post('/operations/deliverables/{id}/save', [DeliverablesController::class, 'operationsSave'])->name('operations.deliverables.save');
    Route::post('/operations/deliverables/{id}/submit', [DeliverablesController::class, 'operationsSubmit'])->name('operations.deliverables.submit');
    Route::post('/operations/deliverables/create-from-feasibility/{feasibilityId}', [DeliverablesController::class, 'createFromFeasibility'])->name('operations.deliverables.create-from-feasibility');
    Route::get('/calculate-subnet', [App\Http\Controllers\DeliverablesController::class, 'calculateSubnet'])->name('calculate.subnet');


    // ✅ Legacy operations Feasibility Status Routes (Keep for backward compatibility)
    Route::get('/feasibility/status/{status}', [FeasibilityStatusController::class, 'index'])->name('feasibility.status');
    
    Route::prefix('feasibility/feasibility-status')->group(function () {
    Route::get('/{status?}', [App\Http\Controllers\FeasibilityStatusController::class, 'index'])->name('feasibility.status.index');
    Route::get('/show/{id}', [App\Http\Controllers\FeasibilityStatusController::class, 'show'])->name('feasibility.status.show');
    
    // Route::put('feasibility/feasibility-status/update/{id}', [FeasibilityStatusController::class, 'update'])->name('feasibility.status.update');
    Route::get('/edit/{id}', [App\Http\Controllers\FeasibilityStatusController::class, 'edit'])->name('feasibility.status.edit');
    Route::post('feasibility/feasibility-status/edit-save/{id}', [FeasibilityStatusController::class, 'editSave'])->name('feasibility.status.editSave');
});

    // ✅ Feasibility Module (Resource routes should come after specific routes)
    Route::resource('feasibility', FeasibilityController::class);
   // Export all users to Excel
Route::get('/export-feasibility', [FeasibilityExcelController::class, 'export'])->name('feasibility.export');
// Import feasibilitys from Excel
Route::post('/import-feasibility', [FeasibilityExcelController::class, 'import'])->name('feasibility.import');


    // Route::post('feasibility-import', [FeasibilityController::class, 'import'])->name('feasibility.import');
    Route::get('/get-client-details/{id}', [ClientController::class, 'getDetails']);


    // ✅ Purchase Order Routes (SM Section)
    Route::prefix('sm/purchaseorder')->name('sm.purchaseorder.')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
        Route::post('/store', [PurchaseOrderController::class, 'store'])->name('store');
        Route::get('/{id}', [PurchaseOrderController::class, 'show'])->name('show');
        Route::get('/{id}/view', [PurchaseOrderController::class, 'show'])->name('view');
        Route::get('/{id}/edit', [PurchaseOrderController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PurchaseOrderController::class, 'update'])->name('update');
        // Route::post('/{id}/toggle-status', [PurchaseOrderController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{id}', [PurchaseOrderController::class, 'destroy'])->name('destroy');
        Route::get('/feasibility/{id}/details', [PurchaseOrderController::class, 'getFeasibilityDetails'])->name('feasibility.details');
    Route::patch('/{id}/toggle-status', [PurchaseOrderController::class, 'toggleStatus'])->name('toggle-status');
        // Route::get('/check-po-number', [PurchaseOrderController::class, 'checkPONumber']);
        // Route::get('/sm/purchaseorder/check-po/{po}', [PurchaseOrderController::class, 'checkPo'])->name('po.check');
           Route::get('/check-po-number', [PurchaseOrderController::class, 'checkPoNumber']);

    });

    Route::prefix('sm')->name('sm.')->middleware(['auth'])->group(function () {

    Route::prefix('proposal')->name('proposal.')->group(function () {

        Route::get('/', [ProposalController::class, 'index'])
            ->name('index');

    });

});
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    });


Route::get('/hr', [HrController::class, 'index'])->name('hr.index');
Route::get('/compliance', [ComplianceController::class, 'index'])->name('compliance.index');
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::get('/training', [TrainingController::class, 'index'])->name('training.index');
Route::get('/strategy', [StrategyController::class, 'index'])->name('strategy.index');
Route::get('/assurance', [AssuranceController::class, 'index'])->name('assurance.index');
Route::get('/assetmaster/asset_type', [Asset_typeController::class, 'index'])->name('assetmaster.asset_type.index');
Route::get('/assetmaster/make_type', [Make_typeController::class, 'index'])->name('assetmaster.make_type.index');
Route::get('/asset', [AssetController::class, 'index'])->name('asset.index');
Route::get('/asset', [AssetController::class, 'index'])->name('asset.create');





//time log report 

// Pincode Lookup

    // 🧪 Debug route to test user type privilege system
    Route::get('/debug-privileges', function () {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'No authenticated user']);
        }
        
        // Test permission checks for key menus
        $manageUsersPermission = \App\Helpers\TemplateHelper::getUserMenuPermissions('Manage User');
        $dashboardPermission = \App\Helpers\TemplateHelper::getUserMenuPermissions('Dashboard');
        $feasibilityPermission = \App\Helpers\TemplateHelper::getUserMenuPermissions('Feasibility Master');
        
        // Get privilege counts
        $userPrivCount = \App\Models\UserMenuPrivilege::where('user_id', $user->id)->count();
        $userTypePrivCount = \App\Models\UserTypeMenuPrivilege::where('user_type_id', $user->user_type_id)->count();
        
        return response()->json([
            'user_info' => [
                'name' => $user->name,
                'user_type' => $user->userType->name ?? 'No type',
                'user_type_id' => $user->user_type_id
            ],
            'privilege_counts' => [
                'individual_user_privileges' => $userPrivCount,
                'user_type_privileges' => $userTypePrivCount
            ],
            'permission_tests' => [
                'manage_users' => $manageUsersPermission,
                'dashboard' => $dashboardPermission,
                'feasibility_master' => $feasibilityPermission
            ],
            'test_info' => [
                'description' => 'Testing user type privilege fallback system',
                'expected_behavior' => 'Should show permissions based on user type when no individual privileges exist',
                'note' => 'Individual user privilege button has been commented out in users/index.blade.php'
            ]
        ]);
    })->name('debug.privileges');

    // Fallback route to handle undefined routes
    Route::fallback(function () {
        return redirect('/welcome');
    });



    
}

);


/*
|--------------------------------------------------------------------------
| 🟢 Client Portal – Authenticated Routes
|--------------------------------------------------------------------------
*/
 /* 🟢 Client Portal – AUTH */
Route::get('client/login', [ClientPortalController::class, 'loginPage'])->name('client.login');
Route::post('client/login', [ClientPortalController::class, 'login'])->name('client.login.submit');

/* 🟢 Client Portal – PROTECTED */
Route::middleware(ClientAuth::class)->prefix('client')->group(function () {
    Route::get('/dashboard', [ClientPortalController::class, 'dashboard'])->name('client.dashboard');
    Route::get('/links', [ClientPortalController::class, 'links'])->name('client.links');
    Route::get('/link/{id}', [ClientPortalController::class, 'linkDetails'])->name('client.link.details');
    
Route::get('/notifications/settings', function () {
    return "Notifications page coming soon!";
})->name('client.notifications.settings');

Route::get('/client/sla-reports/{id}', [ClientPortalController::class, 'slaReports'])
    ->name('client.sla.reports');

     Route::get('/client/live-traffic/{id}', [ClientPortalController::class, 'liveTraffic'])
    ->name('client.live.traffic');

    Route::post('/client/send-password', [ClientPortalController::class, 'sendPassword'])->name('client.sendPassword');


    // logout
    Route::get('/logout', function () {
        auth()->guard('client')->logout();
        return redirect()->route('client.login');
    })->name('client.logout');
});


Route::get('/fix-env', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');

    return "✅ ENV and CONFIG refreshed successfully.";
});
