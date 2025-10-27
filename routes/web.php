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
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\FeasibilityController;
use App\Http\Controllers\GSTController;
use App\Http\Middleware\CheckProfileCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\Vendor;
use Carbon\Carbon;
use App\Models\Company;


//
// 🔁 Root redirect to login page
//
// Route::get('/', function () {
//     return redirect()->route('login');
// });
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
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();        // clears all session data
    session()->regenerateToken();   // prevents CSRF token issues
    return redirect()->route('login');
})->name('logout');


// Route::post('/logout', function () {
//     Auth::logout();
//     return redirect()->route('login');
// })->name('logout');

// 🔑 PASSWORD RESET ROUTES (public)
Route::get('forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink($request->only('email'));

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->name('password.email');

Route::get('reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

Route::post('reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();

            Auth::login($user);
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        $resetUser = \App\Models\User::where('email', $request->email)->first();
        Mail::to($request->email)->send(new \App\Mail\PasswordChangedMail($resetUser));
        return redirect()->route('login')->with('status', __($status));
    }

    return back()->withErrors(['email' => [__($status)]]);
})->name('password.update');
//
// 👤 PROFILE CREATION — allowed for all logged-in users
//

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/create', [ProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
    Route::get('/profile/{id}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{id}/view', [ProfileController::class, 'view'])->name('profile.view');
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
    Route::get('/companies/{id}/view', [App\Http\Controllers\CompanyController::class, 'view'])->name('companies.view');
    Route::get('/clients/{id}/view', [App\Http\Controllers\ClientController::class, 'view'])->name('clients.view');
    Route::get('/vendors/{id}/view', [App\Http\Controllers\VendorController::class, 'view'])->name('vendors.view');

    // ⚙️ Settings Routes
    Route::get('/company-settings', [CompanySettingsController::class, 'index'])->name('company.settings');
    Route::put('/company-settings', [CompanySettingsController::class, 'update'])->name('company.settings.update');
    Route::get('/tax-invoice-settings', [TaxInvoiceSettingsController::class, 'index'])->name('tax.invoice');
    Route::put('/tax-invoice-settings', [TaxInvoiceSettingsController::class, 'update'])->name('tax.invoice.update');
    Route::get('/system-settings', [SystemSettingsController::class, 'index'])->name('system.settings');
    Route::post('/system-settings', [SystemSettingsController::class, 'update'])->name('system.settings.update');

     // 📋 Menus (secured inside main app)
    
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
Route::get('/menus/create', [MenuController::class, 'create'])->name('menus.create');
Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');

// ✅ Privileges
Route::get('/menus/privileges/{userId}', [MenuController::class, 'editPrivileges'])->name('menus.editPrivileges');
Route::post('/menus/privileges/{userId}', [MenuController::class, 'updatePrivileges'])->name('menus.updatePrivileges');

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

//
// 📧 TEST EMAIL ROUTE
//
// Route::get('/test-email', function () {
//     Mail::raw('This is a test email from Laravel!', function ($message) {
//         $message->to('your_email@example.com')->subject('Test Email');
//     });
//     return 'Email sent!';
// });
    // 🧾 GST & PAN Fetch Routes
    // Route::get('/gst/fetch/{gstin}', [GSTController::class, 'fetch']);
    // Route::get('/company/fetch/{pan}', [GSTController::class, 'fetchByPAN']);

    // ✅ Feasibility Module
    Route::resource('feasibility', FeasibilityController::class);
    Route::patch('/feasibility/{id}/toggle-status', [FeasibilityController::class, 'toggleStatus'])->name('feasibility.toggle-status');
    Route::get('/feasibility/{id}/view', [FeasibilityController::class, 'view'])->name('feasibility.view');
    Route::get('/get-client-details/{id}', [ClientController::class, 'getDetails']);

    Route::prefix('operations/feasibility-status')->group(function () {
    Route::get('/{status?}', [App\Http\Controllers\FeasibilityStatusController::class, 'index'])->name('feasibility.status.index');
    Route::get('/view/{id}', [App\Http\Controllers\FeasibilityStatusController::class, 'show'])->name('feasibility.status.view');
    Route::post('/update/{id}', [App\Http\Controllers\FeasibilityStatusController::class, 'update'])->name('feasibility.status.update');
    
});


    // Fallback route to handle undefined routes
    Route::fallback(function () {
        return redirect('/welcome');
    });

}
);