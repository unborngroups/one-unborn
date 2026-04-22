# Purchase Order Validation Test Guide

## ✅ Validation Rules Implemented

### Backend (PurchaseOrderController.php)
**validatePricing() method:**

#### Self Vendor (UBN, UBS, UBL, INF):
- PO amount must be **≤** feasibility amount (lower or equal)
- If PO amount > feasibility amount → Error

#### External Vendor:
- PO amount must be **>** feasibility amount (only higher)
- If PO amount ≤ feasibility amount → Error

### Frontend (create.blade.php)
**Real-time validation:**
- Checks vendor_type from feasibility
- Shows alerts immediately when invalid amount entered
- Red border on invalid fields

## 🧪 How to Test

### Step 1: Select Feasibility
1. Open Purchase Order create page
2. Select a **Feasibility** from dropdown
3. This loads the feasibility data including vendor_type

### Step 2: Select Number of Links
1. Select **Number of Links** (1, 2, 3, or 4)
2. Pricing fields will appear dynamically

### Step 3: Test Self Vendor (UBN, UBS, UBL, INF)
**Scenario:** Feasibility has vendor_type = "UBN" (Self)
- Feasibility ARC = ₹1000
- Enter PO ARC = ₹900 → ✅ **PASS** (lower)
- Enter PO ARC = ₹1000 → ✅ **PASS** (equal)
- Enter PO ARC = ₹1100 → ❌ **FAIL** - Shows error: "INVALID PRICE - For Self Vendor, ARC cannot be higher..."

### Step 4: Test External Vendor
**Scenario:** Feasibility has vendor_type = "Airtel" (External)
- Feasibility ARC = ₹1000
- Enter PO ARC = ₹900 → ❌ **FAIL** - Shows error: "INVALID PRICE - For External Vendor, ARC must be higher..."
- Enter PO ARC = ₹1000 → ❌ **FAIL** - Shows error (cannot be equal)
- Enter PO ARC = ₹1100 → ✅ **PASS** (higher)

## 📝 Expected Behavior

### Real-time Validation (Frontend):
- Validates while typing in amount fields
- Shows red border on invalid field
- Shows alert with clear error message

### Submit Validation (Backend):
- Validates all fields before saving
- Returns error message if any amount is invalid
- Checks all links individually

## ✅ Confirmed Working

✔️ vendor_type loaded from feasibility
✔️ Self vendor check (UBN, UBS, UBL, INF)
✔️ External vendor check
✔️ Per-link validation
✔️ Clear error messages with amounts
✔️ Both frontend and backend validation

## 🔍 Troubleshooting

**Issue:** Number of links not showing fields
**Solution:** Make sure you selected a Feasibility first

**Issue:** Validation not working
**Solution:** Clear cache with `php artisan view:clear`

**Issue:** Wrong vendor type detected
**Solution:** Check feasibility table vendor_type column value
