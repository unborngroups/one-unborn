# GSTIN to PAN Feature - Implementation Guide

## Overview
This feature automatically fetches all GSTIN numbers associated with a PAN when creating/editing Clients and Vendors.

## Installation Steps

### 1. Run Migrations
```bash
php artisan migrate
```

This will create:
- `surepass_api_token` field in `system_settings` table
- `gstins` table for storing GSTIN records

### 2. Configure API Token
1. Go to **System → System Settings**
2. Scroll to "GSTIN to PAN Settings" section
3. Enter your API Bearer Token (from your GSTIN verification API provider)
4. Save settings

### 3. Include JavaScript Files

**For Client Master:**
Add to `resources/views/clients/create.blade.php` and `resources/views/clients/edit.blade.php`:
```html
<script src="{{ asset('js/gstin-fetch.js') }}"></script>
```

**For Vendor Master:**
Add to `resources/views/vendors/create.blade.php` and `resources/views/vendors/edit.blade.php`:
```html
<script src="{{ asset('js/gstin-fetch-vendor.js') }}"></script>
```

### 4. Add Required HTML Elements

**Client Forms** - Ensure these IDs exist:
- `pan_number` - PAN input field
- `gstStatus` - Status message display area
- `gstin` - GSTIN input field
- `business_display_name` - Business name field
- `address1` - Address field
- `state` - State dropdown
- `pincode` - Pincode field

**Vendor Forms** - Ensure these IDs exist:
- `pan_no` - PAN input field
- `gstStatus` - Status message display area
- `gstin` - GSTIN input field
- `business_display_name` - Business name field
- `address1` - Address field
- `state` - State dropdown
- `pincode` - Pincode field

## How It Works

### User Flow:
1. User enters PAN number in Client/Vendor form
2. On blur (when user leaves the PAN field), system automatically:
   - Validates PAN format (10 characters)
   - Calls backend API to fetch GSTINs
   - Displays all GSTINs found for that PAN
3. User selects the appropriate GSTIN from the list
4. System auto-fills:
   - GSTIN number
   - Trade/Business name
   - Principal business address
   - State
   - Pincode
5. User can modify any auto-filled data before saving

### Backend Process:
1. `ClientController@fetchGstinByPan` or `VendorController@fetchGstinByPan` receives request
2. `SurepassService` makes API call to fetch GSTIN data
3. Response is parsed and formatted
4. Data is saved to `gstins` table (linked to client/vendor)
5. Formatted data returned to frontend

## Database Structure

### `gstins` Table
```
- id (primary key)
- entity_type (client/vendor)
- entity_id (foreign key to clients/vendors)
- gstin (15 chars, unique)
- trade_name
- principal_business_address (text)
- state
- state_code (2 chars)
- pincode (10 chars)
- status (Active/Inactive)
- is_primary (boolean)
- timestamps
```

## API Endpoints

### Fetch GSTIN by PAN (Client)
**POST** `/clients/fetch-gstin-by-pan`

**Request:**
```json
{
    "pan_number": "AAAPL1234C",
    "client_id": 123  // Optional, for saving to DB
}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "gstin": "29AAAPL1234C1Z5",
            "trade_name": "ABC Company Pvt Ltd",
            "principal_business_address": "123, Main St, Bangalore",
            "state": "Karnataka",
            "state_code": "29",
            "pincode": "560001",
            "status": "Active",
            "is_primary": false
        }
    ],
    "message": "GSTIN details fetched successfully"
}
```

### Fetch GSTIN by PAN (Vendor)
**POST** `/vendors/fetch-gstin-by-pan`

Same structure as above, with `vendor_id` instead of `client_id`.

## Security Features

1. **URL Masking**: API endpoints don't reveal third-party service names
2. **No Provider References**: Frontend has no mention of API provider
3. **Token Storage**: API token stored securely in database, not exposed to frontend
4. **CSRF Protection**: All POST requests protected with CSRF tokens
5. **Validation**: PAN format validated before API calls
6. **Error Handling**: Graceful fallback if API unavailable

## Troubleshooting

### GSTIN Not Fetching
1. Check if API token is configured in System Settings
2. Verify token is valid and active
3. Check network connectivity
4. Review Laravel logs: `storage/logs/laravel.log`

### JavaScript Not Working
1. Ensure JS files are included in blade templates
2. Check browser console for errors
3. Verify field IDs match expected values
4. Clear browser cache

### Wrong Data Auto-filled
1. Verify PAN number is correct
2. Check if multiple GSTINs exist (user must select correct one)
3. Review API response in network tab

## Maintenance

### Updating Existing Records
Run this artisan command to fetch GSTINs for existing clients/vendors:
```bash
php artisan gstin:sync-existing
```

### Clearing Cached GSTINs
```bash
php artisan cache:clear
```

## Support
For issues or questions, contact system administrator.
