<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OtpController;
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
use App\Http\Controllers\Finance\VendorInvoiceController;
use App\Http\Controllers\Finance\InvoiceController;
use App\Http\Controllers\Report\DeliverableController;
use App\Http\Controllers\Finance\ExpenseController;
use App\Http\Controllers\Finance\DebitNoteController; 
use App\Http\Controllers\TerminationController;
use App\Http\Controllers\RenewalController;
// use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PincodeLookupController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\FeasibilityController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\FeasibilityExcelController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\ModelTypeController;
use App\Http\Controllers\SlaReportController;
use App\Http\Controllers\MikrotikController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DeliverablesController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PrivateChatController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\hrController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\Asset_typeController;
use App\Http\Controllers\Make_typeController;
use App\Http\Controllers\VendorMakeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\StrategyController;
use App\Http\Controllers\AssuranceController;
use App\Http\Controllers\Finance\BankingController;
use App\Http\Controllers\Finance\AccountController;
use App\Http\Controllers\Finance\ReportController;
use App\Http\Controllers\Finance\FinanceGstController;
use App\Http\Controllers\Finance\FinanceTdsController;
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
   



// OTP routes for login (must be above fallback and client portal)
Route::post('/otp/send', [OtpController::class, 'send'])->name('otp.send');
Route::post('/otp/verify', [OtpController::class, 'verify'])->name('otp.verify');

// Internal chat APIs used by the floating widget
Route::prefix('chat')
    ->middleware(['auth', \App\Http\Middleware\CheckProfileCreated::class])
    ->group(function () {
        Route::get('/group/{id}/messages', [App\Http\Controllers\ChatController::class,'fetchMessages'])->name('chat.messages');
        Route::post('/send', [App\Http\Controllers\ChatController::class,'send'])->name('chat.send');
        Route::get('/group/{id}/online-users', [App\Http\Controllers\ChatController::class,'onlineUsers'])->name('chat.online-users');
        Route::get('/all-users', [App\Http\Controllers\ChatController::class, 'allOnlineUsers'])->name('chat.all-online-users');
        Route::post('/typing', [App\Http\Controllers\ChatController::class,'typing'])->name('chat.typing');
        Route::get('/bootstrap', [App\Http\Controllers\ChatController::class,'bootstrap'])->name('chat.bootstrap');
    });

    // Private chat APIs for one-to-one chat
    Route::prefix('private-chat')
    ->middleware(['auth', \App\Http\Middleware\CheckProfileCreated::class])
    ->group(function () {
        Route::get('/users/online', [PrivateChatController::class, 'onlineUsers'])->name('private-chat.online-users');
        Route::get('/messages/{userId}', [PrivateChatController::class, 'fetchMessages'])->name('private-chat.messages');
        Route::post('/send', [PrivateChatController::class, 'sendMessage'])->name('private-chat.send');
    });

// Heartbeat route for user activity
Route::post('/user/activity/heartbeat', [\App\Http\Controllers\UserActivityController::class, 'heartbeat'])->middleware(['auth']);
// Tab close route for user activity
Route::post('/user/activity/tab-close', [\App\Http\Controllers\UserActivityController::class, 'tabClose'])->middleware(['auth']);


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
// Password forgot Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
// reset password routes
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);

// 👤 Profile Routes
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
    // 🏠 Dashboard
Route::get('/welcome', [DashboardController::class, 'index'])->name('welcome');

    //     // 👤 Users (Privilege control)
    // 👤 User routes (Privilege controlled)
Route::get('users', [UserController::class, 'index'])->middleware(\App\Http\Middleware\CheckPrivilege::class .':view')->name('users.index');
Route::get('users/create', [UserController::class, 'create'])->middleware(\App\Http\Middleware\CheckPrivilege::class .':add')->name('users.create');
Route::post('users', [UserController::class, 'store'])->middleware(\App\Http\Middleware\CheckPrivilege::class .':add')->name('users.store');
Route::get('users/{user}/edit', [UserController::class, 'edit'])->middleware(\App\Http\Middleware\CheckPrivilege::class .':edit')->name('users.edit');
Route::put('users/{user}', [UserController::class, 'update'])->middleware(\App\Http\Middleware\CheckPrivilege::class .':edit')->name('users.update');
Route::delete('users/{user}', [UserController::class, 'destroy'])->middleware(\App\Http\Middleware\CheckPrivilege::class .':delete')->name('users.destroy');
Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
   


    // 👤 Users, Companies, Vendors, Clients, Emails, etc.
    Route::resources([
        // 'users' => UserController::class,
        'companies' => CompanyController::class,
        'vendors' => VendorController::class,
        'clients' => ClientController::class,
        'emails' => EmailTemplateController::class,
    ]);
    // bulk delete routes
    Route::post('/clients/bulk-delete', [ClientController::class, 'bulkDestroy'])->name('clients.bulk-delete');
    Route::post('/vendors/bulk-delete', [VendorController::class, 'bulkDestroy'])->name('vendors.bulk-delete');
    Route::post('/users/bulk-delete', [UserController::class, 'bulkDestroy'])->name('users.bulk-delete');
    Route::post('/usertypetable/bulk-delete', [UserTypeController::class, 'bulkDestroy'])->name('usertypetable.bulk-delete');
    Route::post('/companies/bulk-delete', [CompanyController::class, 'bulkDestroy'])->name('companies.bulk-delete');
    Route::post('/sm/purchaseorder/bulk-delete', [PurchaseOrderController::class, 'bulkDestroy'])->name('sm.purchaseorder.bulk-delete');
    Route::post('/operations/asset/bulk-delete', [AssetController::class, 'bulkDestroy'])->name('operations.asset.bulk-delete');
    Route::post('/operations/asset/bulk-print', [AssetController::class, 'bulkPrint'])->name('operations.asset.bulk-print');
    Route::post('/assetmaster/asset_type/bulk-delete', [Asset_typeController::class, 'bulkDestroy'])->name('assetmaster.asset_type.bulk-delete');
    Route::post('/assetmaster/make_type/bulk-delete', [Make_typeController::class, 'bulkDestroy'])->name('assetmaster.make_type.bulk-delete');
    Route::post('/assetmaster/model_type/bulk-delete', [ModelTypeController::class, 'bulkDestroy'])->name('assetmaster.model_type.bulk-delete');
    Route::post('/hr/leavetype/bulk-delete', [LeaveTypeController::class, 'bulkDelete'])->name('hr.leavetype.bulk-delete');
    Route::post('/operations/termination/bulk-delete', [TerminationController::class, 'bulkDestroy'])->name('operations.termination.bulk-delete');
    Route::post('/operations/feasibility/bulk-delete', [FeasibilityStatusController::class, 'bulkDestroy'])->name('operations.feasibility.bulk-delete');

    
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
Route::get('/get-privileges/{usertype}', [UserController::class, 'getPrivileges']);

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
    Route::patch('/clients/{id}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggle-status');
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
    Route::post('/sm/feasibility/{id}/exception', [FeasibilityStatusController::class, 'smSendException'])->name('sm.feasibility.exception');

    // ✅ operations Feasibility Routes (Full functionality like S&M)
    Route::get('/operations/feasibility/open', [FeasibilityStatusController::class, 'operationsOpen'])->name('operations.feasibility.open');
    Route::post('/operations/feasibility/{id}/notfeasible', [FeasibilityStatusController::class, 'operationsNotFeasible'])->name('operations.feasibility.notfeasible');
    Route::post('/operations/feasibility/{id}/makefeasible', [FeasibilityStatusController::class, 'operationsMakeFeasible'])->name('operations.feasibility.makefeasible');
    Route::get('/operations/feasibility/notfeasible', [FeasibilityStatusController::class, 'operationsNotFeasibleList'])->name('operations.feasibility.notfeasible.list');
    Route::get('/operations/feasibility/inprogress', [FeasibilityStatusController::class, 'operationsInProgress'])->name('operations.feasibility.inprogress');
    Route::get('/operations/feasibility/closed', [FeasibilityStatusController::class, 'operationsClosed'])->name('operations.feasibility.closed');
    Route::get('/operations/feasibility/{id}/view', [FeasibilityStatusController::class, 'operationsView'])->name('operations.feasibility.view');
    Route::get('/operations/feasibility/{id}/edit', [FeasibilityStatusController::class, 'operationsEdit'])->name('operations.feasibility.edit');
    Route::post('/operations/feasibility/{id}/save', [FeasibilityStatusController::class, 'operationsSave'])->name('operations.feasibility.save');
    Route::post('/operations/feasibility/{id}/submit', [FeasibilityStatusController::class, 'operationsSubmit'])->name('operations.feasibility.submit');
    Route::post('/operations/feasibility/{id}/exception', [FeasibilityStatusController::class, 'operationsSendException'])->name('operations.feasibility.exception');


    // ✅ operations Feasibility Routes (Full functionality like S&M)
    Route::get('/operations/deliverables/open', [DeliverablesController::class, 'operationsOpen'])->name('operations.deliverables.open');
    Route::get('/operations/deliverables/inprogress', [DeliverablesController::class, 'operationsInProgress'])->name('operations.deliverables.inprogress');
    Route::get('/operations/deliverables/delivery', [DeliverablesController::class, 'operationsDelivery'])->name('operations.deliverables.delivery');
    // Acceptance list page (no specific deliverable)
    Route::get('/operations/deliverables/acceptance', [DeliverablesController::class, 'operationsAcceptance'])->name('operations.deliverables.acceptance');
    // Acceptance page for a specific deliverable
    Route::get('/operations/deliverables/{id}/acceptance', [DeliverablesController::class, 'operationsAcceptanceShow'])->name('operations.deliverables.acceptance.show');
    Route::get('/operations/deliverables/{id}/view', [DeliverablesController::class, 'operationsView'])->name('operations.deliverables.view');
    Route::get('/operations/deliverables/{id}/edit', [DeliverablesController::class, 'operationsEdit'])->name('operations.deliverables.edit');
    Route::post('/operations/deliverables/{id}/save', [DeliverablesController::class, 'operationsSave'])->name('operations.deliverables.save');
    Route::post('/operations/deliverables/{id}/submit', [DeliverablesController::class, 'operationsSubmit'])->name('operations.deliverables.submit');
    Route::get('/operations/deliverables/create-from-feasibility/{feasibilityId}', [DeliverablesController::class, 'createFromFeasibility'])->name('operations.deliverables.create-from-feasibility');
    Route::get('/calculate-subnet', [DeliverablesController::class, 'calculateSubnet'])->name('calculate.subnet');

    // renewal
    route::get('/operations/renewals', [RenewalController::class, 'index'])->name('operations.renewals.index');
    route::get('/operations/renewals/create', [RenewalController::class, 'create'])->name('operations.renewals.create');
    route::post('/operations/renewals', [RenewalController::class, 'store'])->name('operations.renewals.store');
    Route::get('/operations/renewals/{id}/edit', [RenewalController::class, 'edit'])->name('operations.renewals.edit');
    Route::put('/operations/renewals/{id}', [RenewalController::class, 'update'])->name('operations.renewals.update');
    Route::delete('/operations/renewals/{id}', [RenewalController::class, 'destroy'])->name('operations.renewals.destroy');
    Route::get('/operations/renewals/{id}/view', [RenewalController::class, 'view'])->name('operations.renewals.view');
    Route::patch('/operations/renewals/{id}/toggle-status', [RenewalController::class, 'toggleStatus'])->name('operations.renewals.toggle-status');
    
Route::get('/operations/assets/next-asset-id', [AssetController::class, 'nextAssetID']);
Route::post('/operations/asset/import', [AssetController::class, 'import'])->name('operations.asset.import');
Route::get('/operations/assets/export', [AssetController::class, 'exportAssets'])->name('operations.asset.export');
Route::get('/operations/asset', [AssetController::class, 'index'])->name('operations.asset.index');     // list page
Route::get('/operations/asset/create', [AssetController::class, 'create'])->name('operations.asset.create'); // add page
Route::post('/operations/asset/store', [AssetController::class, 'store'])->name('operations.asset.store'); // store action
Route::get('/operations/asset/{asset}/view', [AssetController::class, 'view'])->name('operations.asset.view');
Route::get('/operations/asset/{id}/edit', [AssetController::class, 'edit'])->name('operations.asset.edit'); // edit page
Route::put('/operations/asset/{id}', [AssetController::class, 'update'])->name('operations.asset.update'); // update action   
Route::delete('/operations/asset/{asset}', [AssetController::class, 'destroy'])->name('operations.asset.destroy');
Route::get('/operations/asset/{id}/print', [AssetController::class, 'print'])->name('operations.asset.print');

    route::get('/operations/termination', [TerminationController::class, 'index'])->name('operations.termination.index');
    route::get('/operations/termination/create', [TerminationController::class, 'create'])->name('operations.termination.create');
    route::post('/operations/termination', [TerminationController::class, 'store'])->name('operations.termination.store');
    Route::get('/operations/termination/{id}/edit', [TerminationController::class, 'edit'])->name('operations.termination.edit');
    Route::put('/operations/termination/{id}', [TerminationController::class, 'update'])->name('operations.termination.update');
    Route::delete('/operations/termination/{termination}', [TerminationController::class, 'destroy'])->name('operations.termination.destroy');
    Route::get('/operations/termination/{id}/view', [TerminationController::class, 'view'])->name('operations.termination.view');
    // Route::patch('/operations/termination/{id}/toggle-status', [TerminationController::class, 'toggleStatus'])->name('operations.termination.toggle-status');
    
            // Route::get('renewals/{id}/view', [RenewalController::class, 'view'])->name('renewals.view');

    // ✅ Sales & Marketing Deliverables keep their own URLs but reuse the operations controller
    Route::get('/sm/deliverables/open', [DeliverablesController::class, 'smOpen'])->name('sm.deliverables.open');
    Route::get('/sm/deliverables/inprogress', [DeliverablesController::class, 'smInProgress'])->name('sm.deliverables.inprogress');
    Route::get('/sm/deliverables/delivery', [DeliverablesController::class, 'smDelivery'])->name('sm.deliverables.delivery');
    Route::get('/sm/deliverables/acceptance', [DeliverablesController::class, 'operationsAcceptance'])->name('sm.deliverables.acceptance');

    // ✅ Legacy operations Feasibility Status Routes (Keep for backward compatibility)
    Route::get('/feasibility/status/{status}', [FeasibilityStatusController::class, 'index'])->name('feasibility.status');
    
    Route::prefix('feasibility/feasibility-status')->group(function () {
    Route::get('/{status?}', [App\Http\Controllers\FeasibilityStatusController::class, 'index'])->name('feasibility.status.index');
    Route::get('/show/{id}', [App\Http\Controllers\FeasibilityStatusController::class, 'show'])->name('feasibility.status.show');
    
    // Route::put('feasibility/feasibility-status/update/{id}', [FeasibilityStatusController::class, 'update'])->name('feasibility.status.update');
    Route::get('/edit/{id}', [App\Http\Controllers\FeasibilityStatusController::class, 'edit'])->name('feasibility.status.edit');
    Route::post('feasibility/feasibility-status/edit-save/{id}', [FeasibilityStatusController::class, 'editSave'])->name('feasibility.status.editSave');
});
// AJAX route for autofill feasibility by circuit_id

    Route::get('/feasibility/by-circuit/{circuit_id}', [FeasibilityController::class, 'getFeasibilityByCircuit']);
    Route::get('/feasibility/mark/{id}/{status}', [FeasibilityController::class, 'mark'])->name('feasibility.mark');
    // ✅ Feasibility Module (Resource routes should come after specific routes)
    Route::resource('feasibility', FeasibilityController::class);
   // Export all users to Excel
    Route::get('/export-feasibility', [FeasibilityExcelController::class, 'export'])->name('feasibility.export');
    // Feasibility import failed rows download
    Route::post('/feasibility/import/failed-rows', [App\Http\Controllers\FeasibilityExcelController::class, 'downloadFailedRows'])->name('feasibility.downloadFailedRows');
    Route::post('/import-feasibility', [FeasibilityExcelController::class, 'import'])->name('feasibility.import');
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
        Route::delete('/{id}', [PurchaseOrderController::class, 'destroy'])->name('destroy');
        Route::get('/feasibility/{id}/details', [PurchaseOrderController::class, 'getFeasibilityDetails'])->name('feasibility.details');
        Route::patch('/{id}/toggle-status', [PurchaseOrderController::class, 'toggleStatus'])->name('toggle-status');
        // Route::get('/check-po-number', [PurchaseOrderController::class, 'checkPoNumber'])->name('check-po-number');
        Route::get('/purchase-order/check-po-number', [PurchaseOrderController::class, 'checkPoNumber'])->name('check-po-number');
        // API: Fetch PO by number (for autofill)
        Route::get('/fetch-by-number/{po_number}', [PurchaseOrderController::class, 'fetchByNumber'])->name('fetch-by-number');
    });

   

    Route::prefix('sm')->name('sm.')->middleware(['auth'])->group(function () {

    Route::prefix('proposal')->name('proposal.')->group(function () {

        Route::get('/', [ProposalController::class, 'index'])
            ->name('index');

    });

});
    

// HR module - list users with profiles and view/edit via profile controller

    // Route::get('/hr', [hrController::class, 'index'])->name('hr.index');
    // Route::get('/hr/{id}/view', [hrController::class, 'show'])->name('hr.view');
    // Route::get('/hr/{id}/edit', [hrController::class, 'edit'])->name('hr.edit');
    Route::get('/hr', [hrController::class, 'index'])->name('hr.index');
// Employee index (HR) - reuses the same listing as HR index
Route::get('/hr/employee', [hrController::class, 'index'])->name('hr.employee.index');
Route::get('/hr/{id}/view', [hrController::class, 'show'])->name('hr.view');
Route::get('/hr/{id}/edit', [hrController::class, 'edit'])->name('hr.edit');
Route::prefix('hr/leavetype')->name('hr.leavetype.')->group(function () {
    Route::get('/', [LeaveTypeController::class, 'index'])->name('index');
    Route::get('/create', [LeaveTypeController::class, 'create'])->name('create');
    Route::post('/', [LeaveTypeController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [LeaveTypeController::class, 'edit'])->name('edit');
    Route::put('/{id}', [LeaveTypeController::class, 'update'])->name('update');
    Route::delete('/{id}', [LeaveTypeController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/view', [LeaveTypeController::class, 'view'])->name('view');
});


Route::get('/compliance', [ComplianceController::class, 'index'])->name('compliance.index');
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::get('/training', [TrainingController::class, 'index'])->name('training.index');
Route::get('/strategy', [StrategyController::class, 'index'])->name('strategy.index');
Route::get('/assurance', [AssuranceController::class, 'index'])->name('assurance.index');

// Route::get('/assetmaster/asset_type', [Asset_typeController::class, 'index'])->name('assetmaster.asset_type.index');
// Route::get('/assetmaster/make_type', [Make_typeController::class, 'index'])->name('assetmaster.make_type.index');

 
Route::prefix('assetmaster/asset_type')->name('assetmaster.asset_type.')->group(function () {
    Route::get('/', [Asset_typeController::class, 'index'])->name('index');
    Route::get('/create', [Asset_typeController::class, 'create'])->name('create');
    Route::post('/', [Asset_typeController::class, 'store'])->name('store');
    Route::get('/{assetType}/edit', [Asset_typeController::class, 'edit'])->name('edit');
    Route::put('/{assetType}', [Asset_typeController::class, 'update'])->name('update');
    Route::delete('/{assetType}', [Asset_typeController::class, 'destroy'])->name('destroy');
});
Route::prefix('assetmaster/make_type')->name('assetmaster.make_type.')->group(function () {
    Route::get('/', [Make_typeController::class, 'index'])->name('index');
    Route::get('/create', [Make_typeController::class, 'create'])->name('create');
    Route::post('/', [Make_typeController::class, 'store'])->name('store');
    Route::get('/{makeType}/edit', [Make_typeController::class, 'edit'])->name('edit');
    Route::put('/{makeType}', [Make_typeController::class, 'update'])->name('update');
    Route::delete('/{makeType}', [Make_typeController::class, 'destroy'])->name('destroy');
    Route::get('/generate-id/{company}/{brand}', [AssetController::class, 'generateAssetId']);
});
Route::prefix('assetmaster/model_type')->name('assetmaster.model_type.')->group(function () {
    Route::get('/', [ModelTypeController::class, 'index'])->name('index');
    Route::get('/create', [ModelTypeController::class, 'create'])->name('create');
    Route::post('/', [ModelTypeController::class, 'store'])->name('store');
    Route::get('/{modelType}/edit', [ModelTypeController::class, 'edit'])->name('edit');
    Route::put('/{modelType}', [ModelTypeController::class, 'update'])->name('update');
    Route::delete('/{modelType}', [ModelTypeController::class, 'destroy'])->name('destroy');
});

// Finance Module Routes
Route::prefix('finance')->name('finance.')->group(function () {
           // accounts
    Route::get('accounts',[AccountController::class,'index'])->name('accounts.index');
    Route::get('accounts/create',[AccountController::class,'create'])->name('accounts.create');
    Route::post('accounts/store',[AccountController::class,'store'])->name('accounts.store');
    Route::get('accounts/{id}/edit',[AccountController::class,'edit'])->name('accounts.edit');
    Route::put('accounts/{account}',[AccountController::class,'update'])->name('accounts.update');
    Route::patch('accounts/{account}/toggle',[AccountController::class,'toggle'])->name('accounts.toggle');
    Route::post('accounts/{account}/submit',[AccountController::class,'submitForApproval'])->name('accounts.submit');
    Route::post('accounts/{account}/approve',[AccountController::class,'approve'])->name('accounts.approve');
    Route::post('accounts/{account}/reject',[AccountController::class,'reject'])->name('accounts.reject');

    // Invoice
    Route::get('/invoices', [InvoiceController::class,'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class,'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class,'store'])->name('invoices.store');
    Route::get('/invoices/{id}', [InvoiceController::class,'view'])->name('invoices.view');
    // Alias for Blade template compatibility
    // Route::get('/invoices/{id}', [InvoiceController::class,'view'])->name('finance.invoices.view');
    // Alias for Blade template compatibility
    // Route::get('/invoices/{id}', [InvoiceController::class,'view'])->name('invoices.show');
    Route::get('/invoices/{id}/edit', [InvoiceController::class,'edit'])->name('invoices.edit');
    Route::put('/invoices/{id}', [InvoiceController::class,'update'])->name('invoices.update');
    // Route::delete('/invoices/{id}', [InvoiceController::class,'destroy'])->name('invoices.destroy');
    // Route::get('/invoices/{id}/pdf', [InvoiceController::class,'pdf'])->name('invoices.downloadPdf');
    // Alias for Blade template compatibility
    Route::delete('/invoices/{id}', [InvoiceController::class,'destroy'])->name('invoices.delete');

    Route::get('/invoices/{id}/pdf', [InvoiceController::class,'pdf'])->name('invoices.pdf');
    Route::get('/invoices/{id}/send-email', [InvoiceController::class,'sendEmail'])->name('invoices.sendEmail');

    // banking
    Route::get('banking',[BankingController::class,'index'])->name('banking.index');
    Route::get('banking/create',[BankingController::class,'create'])->name('banking.create');
    Route::post('banking/store',[BankingController::class,'store'])->name('banking.store');
    Route::get('banking/{id}/transactions',[BankingController::class,'transactions'])->name('banking.transactions');
    Route::post('banking/transaction/store',[BankingController::class,'storeTransaction'])->name('banking.transaction.store');
    Route::get('banking/reconcile/{txn}',[BankingController::class,'reconcile'])->name('banking.reconcile');

    // 
    Route::resource('vendor-invoices', VendorInvoiceController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::resource('debit-notes', DebitNoteController::class);
    Route::get('purchases', function () {
        return view('finance.purchases.index');
    })->name('purchases.index');
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('profit-loss', [ReportController::class,'profitLoss'])->name('profit_loss');
        Route::get('balance-sheet', [ReportController::class,'balanceSheet'])->name('balance_sheet');
        Route::get('cash-flow', [ReportController::class,'cashFlow'])->name('cash_flow');
    });
});

Route::prefix('finance/settings')->middleware(['auth'])->name('finance.settings.')->group(function () {
    Route::get('/', function() {return view('finance.settings.index');})->name('index');
    Route::get('/gst', [FinanceGstController::class, 'index'])->name('gst');
    Route::post('/gst', [FinanceGstController::class, 'update'])->name('gst.update');
    Route::get('/tds', [FinanceTdsController::class, 'index'])->name('tds');
    Route::post('/tds', [FinanceTdsController::class, 'update'])->name('tds.update');
});

Route::prefix('report/deliverable')->name('report.deliverable.')->group(function () {
    Route::get('open', [DeliverableController::class, 'open'])->name('open');
    Route::get('inprogress', [DeliverableController::class, 'inprogress'])->name('inprogress');
    Route::get('delivery', [DeliverableController::class, 'delivery'])->name('delivery');
    Route::post('download-excel', [DeliverableController::class, 'downloadExcel'])->name('downloadExcel');
});

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
