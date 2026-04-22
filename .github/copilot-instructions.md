# One Unborn - AI Agent Instructions

## Project Overview
Laravel 12 application for managing ISP/telecom business operations including feasibility requests, purchase orders, client/vendor management, and multi-company support with granular role-based access control.

## Architecture & Key Concepts

### Multi-Company + Role-Based Access (RBAC)
- **Three-tier access model**: Superadmin (type 1) → Admin (type 2) → Regular Users (other types)
- Superadmins/Admins see ALL data across companies; regular users only see their assigned companies via `company_user` pivot
- **Menu-level permissions** via `UserMenuPrivilege` model: `can_menu`, `can_add`, `can_edit`, `can_delete`, `can_view`
- Routes use `CheckPrivilege` middleware with action parameter: `->middleware(CheckPrivilege::class . ':view')`
- Views use `TemplateHelper::getUserMenuPermissions('Menu Name')` to check permissions and conditionally show/hide UI elements
- Profile creation (`profile_created` flag) is mandatory for ALL users before dashboard access via `CheckProfileCreated` middleware

### Document Numbering System
- Automated prefix generation via `PrefixGenerator` service (e.g., PO numbers, Feasibility IDs)
- Two strategies: Financial Year-based (FY) or Vendor/Client-based (VB/CB)
- Uses `PrefixConfiguration` model with auto-resetting sequences on FY change
- **Pattern**: `{prefix}-{sequence}` (e.g., `PO/FY/2024-25/001`)
- Call `PrefixGenerator::generatePONumber($vendorId)` or `::generateFeasibilityId($clientId)`

### Feasibility & Purchase Order Workflow
1. **Feasibility Request** created with client, company, pincode, service type, vendor type
2. Auto-creates linked `FeasibilityStatus` record for operations tracking
3. **SELF vendors** (UNB/UNS/UBL/INF): PO amount must be ≤ feasibility amount
4. **External vendors** (Airtel, etc.): PO amount must be > feasibility amount
5. PurchaseOrder validates pricing via `validatePricing()` method before save
6. Deliverables track activation/installation milestones linked to feasibilities

### External Integrations
- **Surepass API**: GSTIN-to-PAN lookup for auto-filling client/vendor details (`SurepassService`)
- **WhatsApp Notifications**: Via `WhatsAppHelper::whatsappNotification()` using unofficial API
- **Email System**: Dynamic templates via `EmailHelper` and `Mail` facade using Mailable classes

## Development Workflows

### Running the Application
```powershell
# Development (starts server + queue + vite in parallel)
composer dev

# Manual approach
php artisan serve           # Web server
php artisan queue:listen    # Queue worker
npm run dev                 # Vite asset compilation
```

### Database & Migrations
- Use descriptive migration names: `2025_11_01_073628_create_purchase_orders_table.php`
- Always add relationships in models when creating new tables
- Run: `php artisan migrate`

### Testing
```powershell
composer test           # Runs PHPUnit tests
php artisan test       # Alternative Laravel test runner
```

## Code Conventions

### Models
- Use Eloquent relationships extensively: `belongsTo()`, `hasMany()`, `belongsToMany()`
- Add `$fillable` arrays for mass assignment protection
- Use `$casts` for date/boolean conversions
- Boot methods for auto-generated fields (see `Feasibility::boot()` for auto-ID generation)

### Controllers
- Filter data by company access: `$user->companies()->pluck('companies.id')` then `whereIn('company_id', $companyIds)`
- Validate input with `$request->validate([...])` 
- Use `Auth::user()` to get current user, check `user_type_id` for role logic
- Date format conversion: Frontend sends `DD-MM-YYYY`, convert to `YYYY-MM-DD` for database

### Helpers (Located in `app/Helpers/`)
- `PrivilegeHelper::can($route, $action)` - Check user permissions
- `TemplateHelper::getUserMenuPermissions($menuName)` - Get menu permissions object for views
- `EmailHelper::sendDynamicEmail($user)` - Send templated emails
- `WhatsAppHelper::whatsappNotification($message, $phone, $document)` - Send WhatsApp
- Helpers are auto-loaded, call statically

### Views & Frontend
- Blade templates in `resources/views/`
- Use `@if($permissions->can_add)` to conditionally show add buttons
- JavaScript files in `public/js/` (e.g., `gstin-fetch.js` for GSTIN lookup)
- TailwindCSS 4 via Vite - use utility classes
- Date inputs use `DD-MM-YYYY` format for user display

### Routes
- Group by middleware: `Route::middleware(['auth', CheckProfileCreated::class])->group(...)`
- Named routes MUST match menu `route` column for privilege checking
- Resource routes: `Route::resources(['clients' => ClientController::class])`
- Custom view routes: `Route::get('/clients/{id}/view', [ClientController::class, 'view'])->name('clients.view')`

## Critical Gotchas

1. **Always filter by company** for regular users - check `user_type_id` before querying
2. **Middleware order matters**: `['auth', CheckProfileCreated::class, CheckPrivilege::class . ':view']`
3. **Menu names are case-sensitive** when using `TemplateHelper::getUserMenuPermissions()`
4. **Self vs External vendor** logic differs for pricing validation
5. **Date conversions**: Always convert `DD-MM-YYYY` ↔ `YYYY-MM-DD` between frontend/backend
6. **Profile creation is mandatory** - even superadmins must create profile first

## Key Files Reference
- Privilege logic: `app/Helpers/PrivilegeHelper.php`, `app/Http/Middleware/CheckPrivilege.php`
- Document numbering: `app/Services/PrefixGenerator.php`, `app/Models/PrefixConfiguration.php`
- User model: `app/Models/User.php` (relationships: `companies()`, `userType()`, `menuPrivileges()`)
- Main routes: `routes/web.php` (grouped by auth/profile/privilege middleware)
- Menu rendering: `resources/views/layouts/partials/fullmenu.blade.php`

## ChatWidget Addon
- Separate Laravel addon in `ChatWidget/` directory
- Has own routes (`routes.php`), controllers, migrations, views
- Uses DataTable pattern for listings (`Datatable/ChatWidgetDataTable.php`)
- Repository pattern (`Repository/ChatWidgetRepository.php`)
