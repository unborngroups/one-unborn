
# One-Unborn ISP/Telecom Management System

## Project Overview
A Laravel 12 application for managing ISP/telecom business operations, including feasibility requests, purchase orders, client/vendor management, and multi-company support with granular role-based access control (RBAC).

---

## Key Features
- Multi-company support with RBAC (Superadmin, Admin, User)
- OTP-based secure login flow
- Feasibility request and status tracking
- Purchase order (PO) management with auto-numbering
- Client and vendor management
- Deliverables and milestone tracking
- WhatsApp and email notifications
- Surepass API integration for GSTIN/PAN lookup
- Dynamic document numbering (FY/vendor/client based)
- Customizable menu-level permissions

---

## User Roles & Access
- **Superadmin (type 1):** Full access to all companies and data
- **Admin (type 2):** Full access to all companies and data
- **Regular User (other types):** Access only to assigned companies (via `company_user` pivot)
- Menu-level permissions: can_menu, can_add, can_edit, can_delete, can_view (via `UserMenuPrivilege`)
- Profile creation is mandatory for all users before dashboard access

---

## Module Descriptions

### 1. User Management & RBAC
- Users are assigned roles (Superadmin, Admin, User)
- Company assignment via `company_user` pivot
- Menu-level permissions managed via `UserMenuPrivilege`

### 2. Login & OTP Verification
- Email-based login with OTP verification
- OTP sent from company settings email
- Password required after OTP verification
- AJAX-based login for smooth UX

### 3. Feasibility Management
- Create and track feasibility requests
- Linked status tracking via `FeasibilityStatus`
- Service type, vendor type, pincode, client, company fields

### 4. Purchase Order (PO) Management
- Create POs linked to feasibility
- Auto-numbering with PrefixGenerator (FY/vendor/client based)
- Pricing validation (self-vendor vs external)

### 5. Deliverables
- Track activation/installation milestones
- Linked to feasibility and PO

### 6. Notifications & Integrations
- WhatsApp notifications via WhatsAppHelper
- Email notifications via EmailHelper
- Surepass API for GSTIN/PAN lookup

---

## Main Workflows

### Login & OTP Verification (Sequence)

```text
User
	|
	|--[1] Enter Email & Click Verify
	|         |
	|         |--[2] System sends OTP to email (from company settings)
	|         |
	|<--------|
	|--[3] Enter OTP & Click Verify OTP
	|         |
	|         |--[4] System verifies OTP
	|         |
	|<--------|
	|--[5] Enter Password & Click Sign In
	|         |
	|         |--[6] System checks password
	|         |     |-- If correct: login success
	|         |     |-- If wrong: show error, allow retry (no OTP resend)
	|<--------|
```

### Feasibility & PO Workflow (Sequence)

```text
User
	|
	|--[1] Create Feasibility Request
	|         |
	|         |--[2] System creates Feasibility + FeasibilityStatus
	|         |
	|<--------|
	|--[3] Create PO (linked to Feasibility)
	|         |
	|         |--[4] System validates pricing (self/external vendor logic)
	|         |--[5] System generates PO number (PrefixGenerator)
	|         |
	|<--------|
	|--[6] Track Deliverables (activation/installation)
```

---

## Database Structure (Main Tables)

### Entity-Relationship Diagram (Text)

```text
users
	|--< company_user >-- companies
	|-- user_types
	|--< user_menu_privileges >-- menus

clients
vendors
feasibilities --< feasibility_statuses
feasibilities --< purchase_orders
purchase_orders --< deliverables

prefix_configurations
login_logs
```

---

## External Integrations
- **Email:** Laravel Mail, sender from company settings
- **WhatsApp:** Unofficial API via WhatsAppHelper
- **Surepass API:** GSTIN-to-PAN lookup (SurepassService)

---

## Development & Deployment

### Setup
1. Clone repo, run `composer install` and `npm install`
2. Configure `.env` (DB, mail, etc.)
3. Run migrations: `php artisan migrate`
4. Start: `composer dev` or manually run server, queue, vite

### Testing
- `composer test` or `php artisan test`

### Code Structure
- Controllers: `app/Http/Controllers/`
- Models: `app/Models/`
- Helpers: `app/Helpers/`
- Services: `app/Services/`
- Views: `resources/views/`
- Routes: `routes/web.php`

### Customization
- Menu/privilege logic: `PrivilegeHelper`, `TemplateHelper`, `UserMenuPrivilege`
- Document numbering: `PrefixGenerator`, `PrefixConfiguration`
- Email sender: company settings (`CompanySetting` model)
- Add new modules: follow Eloquent, migration, and RBAC patterns

---

## Security & Best Practices
- All sensitive actions protected by RBAC and middleware
- OTP and password never stored together
- CSRF protection enabled
- All user input validated
- Passwords hashed (bcrypt)

---

## Support & Extension
- Add new companies, users, or roles via admin panel
- Extend document numbering via `PrefixGenerator`
- Integrate new APIs via `app/Services/`
- Customize notifications in `EmailHelper` and `WhatsAppHelper`

---

## Exporting to Word/PDF
You can copy this Markdown file into Microsoft Word or use VS Code extensions like "Markdown PDF" to export as PDF/Word. For diagrams, you can use the text diagrams above or recreate them visually in draw.io or similar tools if needed.

---
For further details, see code comments and helper documentation in `app/Helpers/` and `app/Services/`.
