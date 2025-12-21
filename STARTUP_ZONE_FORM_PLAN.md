# Startup Zone Exhibitor Registration Form - Revised Implementation Plan

## Overview
Rebuild the exhibitor payment form for Startup Zone using **existing tables** (`applications`, `event_contacts`, `invoices`, `payments`) and add **admin-configurable association pricing rules**.

---

## 1. Database Schema - Using Existing Tables

### 1.1 Applications Table - Column Mapping

| Form Field | Applications Column | Data Type | Notes |
|------------|---------------------|-----------|-------|
| **Booth Information** |
| Booth Type | `stall_category` | string | Store: "Startup Booth" |
| Booth Size | `interested_sqm` | string | Store: "Booth / POD" or "4 SQM" |
| **Company Information** |
| Name of Exhibitor | `company_name` | string | Required |
| Company Registration Certificate | `certificate` | string | File path stored |
| Company Age (Years) | `how_old_startup` | integer | 1-7 years |
| Invoice Address | `address` | string | Required |
| City | `city_id` | string | Store city name (or use city_id if available) |
| State | `state_id` | bigint | Foreign key to states table |
| Postal Code | `postal_code` | string | Required |
| Country | `country_id` | bigint | Foreign key to countries table |
| Telephone Number | `landline` | string | With country code |
| Website | `website` | string | Required |
| **Tax Information** |
| GST Status | `gst_compliance` | boolean | true = Registered, false = Unregistered |
| GST Number | `gst_no` | string | Required if Registered |
| PAN Number | `pan_no` | string | Required, validated format |
| **Sector Information** |
| Sector | `sector_id` | string | Foreign key or name |
| Subsector | `subSector` | string | Required |
| Other Sector Name | `type_of_business` | string | If subsector = "Other" |
| **Association/Partner** |
| Promocode | `promocode` | string | **NEW COLUMN: Stores the promocode entered by user** (maps to association) |
| Association Name | `assoc_mem` | string | GCPIT, ELEVATE, IBioM, etc. (auto-filled from promocode lookup) |
| Registration Source | `RegSource` | string | **Stores organization/association name from promocode lookup** (for tracking/reporting) |
| **Application Metadata** |
| Application Type | `application_type` | string | Set to: "startup-zone" |
| Participant Type | `participant_type` | string | Set to: "Startup" |
| Status | `status` | enum | Default: "initiated" |
| Submission Status | `submission_status` | string | Default: "in progress" |
| Terms Accepted | `terms_accepted` | tinyInteger | 1 = accepted |
| Company Email | `company_email` | string | Required |
| Event ID | `event_id` | bigint | Current event ID |
| User ID | `user_id` | bigint | Nullable (guest registration) |

### 1.2 Event Contacts Table - Contact Person Details

| Form Field | Event Contacts Column | Data Type | Notes |
|------------|----------------------|-----------|-------|
| Title | `salutation` | string | Mr., Mrs., Ms., Dr., Prof. |
| First Name | `first_name` | string | Required |
| Last Name | `last_name` | string | Required |
| Designation | `designation` | string | Required |
| Email | `email` | string | Required, validated |
| Mobile | `contact_number` | string | With country code |
| Application ID | `application_id` | bigint | Foreign key to applications |

### 1.3 Invoices Table - Payment Information

| Form Field | Invoices Column | Data Type | Notes |
|------------|------------------|-----------|-------|
| Application ID | `application_id` | bigint | Foreign key |
| Invoice Type | `type` | string | "Startup Zone Registration" |
| Base Price | `price` | double | Association special price |
| GST (18%) | `gst` | double | Calculated: price × 0.18 |
| Processing Charges | `processing_charges` | double | Based on payment mode |
| Processing Rate | `processing_chargesRate` | integer | 3% or 9.5% |
| Total Amount | `total_final_price` | double | price + gst + processing |
| Currency | `currency` | enum | 'INR' or 'USD' |
| Payment Status | `payment_status` | enum | 'unpaid' default |
| Application No | `application_no` | string | From application.application_id |

### 1.4 Payments Table - Payment Processing

| Form Field | Payments Column | Data Type | Notes |
|------------|------------------|-----------|-------|
| Invoice ID | `invoice_id` | bigint | Foreign key |
| Payment Method | `payment_method` | string | CCAvenue, PayPal, Bank Transfer |
| Amount | `amount` | decimal | Total amount to pay |
| Transaction ID | `transaction_id` | string | Gateway transaction ID |
| Status | `status` | enum | 'pending', 'successful', 'failed' |
| Payment Date | `payment_date` | datetime | When payment completed |
| Currency | `currency` | string | 'INR' or 'USD' |
| User ID | `user_id` | bigint | Nullable |

---

## 2. New Tables: Draft Storage, Association Pricing Rules & Field Configuration

### 2.1 Table: `startup_zone_drafts` (Temporary Form Data Storage)

```sql
CREATE TABLE `startup_zone_drafts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL, -- Session token for tracking
  `uuid` char(35) NULL, -- Alternative identifier
  
  -- Booth Information
  `stall_category` varchar(255) NULL,
  `interested_sqm` varchar(125) NULL,
  
  -- Company Information
  `company_name` varchar(255) NULL,
  `certificate_path` varchar(255) NULL, -- File path for uploaded certificate
  `how_old_startup` integer NULL,
  `address` varchar(500) NULL,
  `city_id` varchar(255) NULL,
  `state_id` bigint UNSIGNED NULL,
  `postal_code` varchar(10) NULL,
  `country_id` bigint UNSIGNED NULL,
  `landline` varchar(20) NULL,
  `website` varchar(255) NULL,
  `company_email` varchar(255) NULL,
  
  -- Tax Information
  `gst_compliance` tinyint(1) NULL,
  `gst_no` varchar(15) NULL, -- Encrypted
  `pan_no` varchar(10) NULL, -- Encrypted
  
  -- Sector Information
  `sector_id` varchar(255) NULL,
  `subSector` varchar(255) NULL,
  `type_of_business` varchar(255) NULL,
  
  -- Association & Promocode
  `promocode` varchar(100) NULL,
  `assoc_mem` varchar(125) NULL,
  `RegSource` varchar(250) NULL,
  
  -- Contact Person Details (stored as JSON or separate columns)
  `contact_data` json NULL, -- Stores: title, first_name, last_name, designation, email, mobile, country_code
  
  -- Payment Information
  `payment_mode` varchar(50) NULL,
  
  -- Metadata
  `application_type` varchar(125) DEFAULT 'startup-zone',
  `event_id` bigint UNSIGNED DEFAULT 1,
  `user_id` bigint UNSIGNED NULL, -- If user is logged in
  `last_updated_field` varchar(100) NULL, -- Track which field was last updated
  `progress_percentage` integer DEFAULT 0, -- Form completion percentage
  `is_abandoned` tinyint(1) DEFAULT 0, -- Marked as abandoned after X days
  `abandoned_at` timestamp NULL,
  `expires_at` timestamp NULL, -- Auto-cleanup after expiration
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  
  PRIMARY KEY (`id`),
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_uuid` (`uuid`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_is_abandoned` (`is_abandoned`),
  INDEX `idx_expires_at` (`expires_at`),
  FOREIGN KEY (`state_id`) REFERENCES `states`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
);
```

**Purpose**: Store temporary form data while user is filling the form. Separate from applications table for better data management.

**Key Features**:
- Stores all form field values as user types
- Tracks form completion progress
- Marks abandoned drafts after inactivity
- Auto-expires after set period (e.g., 30 days)
- Can be restored and converted to application

**Security**:
- Sensitive fields (GST, PAN) stored encrypted
- Session-based access control
- Auto-cleanup of expired drafts

---

## 2. New Tables: Association Pricing Rules & Field Configuration

### 2.2 Table: `association_pricing_rules`

```sql
CREATE TABLE `association_pricing_rules` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `association_name` varchar(255) NOT NULL UNIQUE,
  `display_name` varchar(255) NOT NULL,
  `logo_path` varchar(255) NULL, -- Path to association logo image
  `promocode` varchar(100) NULL UNIQUE, -- Promocode for this association
  `base_price` decimal(10, 2) DEFAULT 52000.00,
  `special_price` decimal(10, 2) NULL,
  `is_complimentary` tinyint(1) DEFAULT 0,
  `max_registrations` integer NULL,
  `current_registrations` integer DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `description` text NULL,
  `entitlements` text NULL, -- JSON or text for entitlements
  `valid_from` date NULL,
  `valid_until` date NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_association_name` (`association_name`),
  INDEX `idx_promocode` (`promocode`),
  INDEX `idx_is_active` (`is_active`)
);
```

**Purpose**: Allow super-admin to configure pricing rules for different associations/partners.

**New Fields**: 
- `logo_path` - Stores path to association logo for navbar display
- `promocode` - Unique promocode that maps to this association/organization

**Promocode Logic**:
- Each association can have one promocode
- Promocode is unique across all associations
- When user enters promocode, system looks up association
- If valid promocode found, apply that association's pricing
- Store promocode in `RegSource` column of applications table

**Admin Interface**: Add to Super Admin panel for managing these rules and promocodes.

### 2.3 Table: `form_field_configurations`

```sql
CREATE TABLE `form_field_configurations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_type` varchar(50) NOT NULL DEFAULT 'startup-zone',
  `version` varchar(20) NOT NULL DEFAULT '1.0', -- Version number (e.g., '1.0', '1.1', '2.0')
  `field_name` varchar(100) NOT NULL, -- e.g., 'company_name', 'gst_no'
  `field_label` varchar(255) NOT NULL, -- Display label
  `is_required` tinyint(1) DEFAULT 1,
  `validation_rules` text NULL, -- JSON: additional validation rules
  `field_order` integer DEFAULT 0, -- Display order
  `field_group` varchar(50) NULL, -- e.g., 'company_info', 'contact_info'
  `is_active` tinyint(1) DEFAULT 1,
  `is_current_version` tinyint(1) DEFAULT 0, -- Only one version can be current
  `created_by` bigint UNSIGNED NULL, -- User who created this version
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_form_field_version` (`form_type`, `field_name`, `version`),
  INDEX `idx_form_type` (`form_type`),
  INDEX `idx_version` (`version`),
  INDEX `idx_is_active` (`is_active`),
  INDEX `idx_is_current_version` (`is_current_version`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
);
```

**Purpose**: Allow super-admin to configure which fields are required/optional for the startup zone form with **versioning support**.

**Versioning Features**:
- Multiple versions of field configurations can exist
- Only one version is marked as `is_current_version = 1` (active version)
- Admin can create new versions, activate them, and rollback to previous versions
- Historical versions are preserved for audit trail
- Form uses the current active version

**Version Management**:
- When admin updates field configuration, create new version
- Set old version's `is_current_version = 0`
- Set new version's `is_current_version = 1`
- Track who created each version (`created_by`)

**Admin Interface**: Add to Super Admin panel for managing field requirements with version control.

---

## 3. Column Usage Details

### 3.1 Applications Table - Field Mapping

#### **Booth & Participation**
- `stall_category` → "Startup Booth" (fixed value)
- `interested_sqm` → "Booth / POD" or "4 SQM" (based on association)
- `application_type` → "startup-zone" (to distinguish from regular exhibitor)
- `participant_type` → "Startup"

#### **Company Details**
- `company_name` → Exhibitor/Organization name
- `certificate` → Path to uploaded PDF certificate
- `how_old_startup` → Company age (1-7 years)
- `address` → Invoice address (line 1)
- `city_id` → City name (stored as string, or use city lookup)
- `state_id` → State ID (foreign key)
- `postal_code` → Postal/ZIP code
- `country_id` → Country ID (foreign key)
- `landline` → Telephone with country code (format: +91-123412345)
- `website` → Company website (without http://)
- `company_email` → Company email address

#### **Tax & Compliance**
- `gst_compliance` → true if "Registered", false if "Unregistered"
- `gst_no` → GST number (15 chars, validated format)
- `pan_no` → PAN number (10 chars, validated format)

#### **Sector & Business**
- `sector_id` → Sector ID or name
- `subSector` → Subsector name
- `type_of_business` → "Other Sector Name" if subsector = "Other"

#### **Association & Promocode**
- `promocode` → **NEW COLUMN: Promocode entered by user** (stored as-is, e.g., "TIESB", "TIESNB")
- `assoc_mem` → Association name (GCPIT, ELEVATE, IBioM, etc.) - **Auto-filled from promocode lookup**
- `RegSource` → **Organization/association name from promocode lookup** (for tracking/reporting)
- Promocode lookup flow:
  1. User enters promocode in form
  2. System looks up `association_pricing_rules` by `promocode`
  3. If found: 
     - Store promocode in `promocode` column
     - Auto-fill `assoc_mem` with `association_name`
     - Store `association_name` in `RegSource` column (for tracking)
  4. Apply association's pricing rules

#### **Status & Workflow**
- `status` → "initiated" (default), changes to "submitted" after payment
- `submission_status` → "in progress" → "submitted"
- `terms_accepted` → 1 when terms accepted
- `event_id` → Current event ID
- `user_id` → NULL for guest registration, or user ID if logged in

### 3.2 Event Contacts Table - Contact Person

- `application_id` → Links to applications.id
- `salutation` → Title (Mr., Mrs., Ms., Dr., Prof.)
- `first_name` → Contact person first name
- `last_name` → Contact person last name
- `designation` → Job title/designation
- `email` → Contact email (primary)
- `contact_number` → Mobile with country code (format: +91-xxxxxxxxxx)
- `job_title` → Can be used if different from designation

### 3.3 Invoices Table - Pricing & Payment

- `application_id` → Links to applications.id
- `type` → "Startup Zone Registration"
- `price` → Base price from association pricing rule
- `gst` → GST amount (price × 18%)
- `processing_charges` → Processing fee amount
- `processing_chargesRate` → Processing fee percentage (3 or 9.5)
- `total_final_price` → Final amount to pay
- `currency` → "INR" (default) or "USD" for PayPal
- `payment_status` → "unpaid" (default)
- `application_no` → From application.application_id

### 3.4 Payments Table - Transaction

- `invoice_id` → Links to invoices.id
- `payment_method` → "CCAvenue", "PayPal", or "Bank Transfer"
- `amount` → Total amount
- `transaction_id` → Gateway transaction ID
- `status` → "pending" → "successful" or "failed"
- `payment_date` → When payment completed
- `currency` → "INR" or "USD"

---

## 4. Admin Interface for Association Pricing Rules & Field Configuration

### 4.1 Super Admin Routes - Association Pricing
```php
Route::get('/super-admin/association-pricing', [SuperAdminController::class, 'associationPricing'])
    ->name('super-admin.association-pricing');
Route::post('/super-admin/association-pricing', [SuperAdminController::class, 'storeAssociationPricing'])
    ->name('super-admin.association-pricing.store');
Route::put('/super-admin/association-pricing/{id}', [SuperAdminController::class, 'updateAssociationPricing'])
    ->name('super-admin.association-pricing.update');
Route::delete('/super-admin/association-pricing/{id}', [SuperAdminController::class, 'deleteAssociationPricing'])
    ->name('super-admin.association-pricing.delete');
Route::post('/super-admin/association-pricing/{id}/upload-logo', [SuperAdminController::class, 'uploadAssociationLogo'])
    ->name('super-admin.association-pricing.upload-logo');
```

### 4.2 Super Admin Routes - Field Configuration
```php
Route::get('/super-admin/form-fields', [SuperAdminController::class, 'formFields'])
    ->name('super-admin.form-fields');
Route::get('/super-admin/form-fields/versions', [SuperAdminController::class, 'formFieldVersions'])
    ->name('super-admin.form-fields.versions');
Route::post('/super-admin/form-fields', [SuperAdminController::class, 'storeFormField'])
    ->name('super-admin.form-fields.store');
Route::put('/super-admin/form-fields/{id}', [SuperAdminController::class, 'updateFormField'])
    ->name('super-admin.form-fields.update');
Route::delete('/super-admin/form-fields/{id}', [SuperAdminController::class, 'deleteFormField'])
    ->name('super-admin.form-fields.delete');
Route::post('/super-admin/form-fields/bulk-update', [SuperAdminController::class, 'bulkUpdateFormFields'])
    ->name('super-admin.form-fields.bulk-update');
Route::post('/super-admin/form-fields/create-version', [SuperAdminController::class, 'createFormFieldVersion'])
    ->name('super-admin.form-fields.create-version');
Route::post('/super-admin/form-fields/activate-version', [SuperAdminController::class, 'activateFormFieldVersion'])
    ->name('super-admin.form-fields.activate-version');
Route::get('/super-admin/form-fields/version/{version}', [SuperAdminController::class, 'viewFormFieldVersion'])
    ->name('super-admin.form-fields.view-version');
Route::post('/super-admin/form-fields/rollback', [SuperAdminController::class, 'rollbackFormFieldVersion'])
    ->name('super-admin.form-fields.rollback');
```

### 4.3 Admin Features - Association Pricing
- **CRUD Operations**: Create, Read, Update, Delete association rules
- **Fields to Manage**:
  - Association Name (unique identifier)
  - Display Name (for UI)
  - **Promocode** (unique, maps to this association)
  - **Logo Upload** (for navbar display)
  - Base Price (default: ₹52,000)
  - Special Price (discounted price)
  - Is Complimentary (free registration)
  - Max Registrations (limit, e.g., 25 for TIESB/TIESNB)
  - Current Registrations (auto-calculated)
  - Active Status (enable/disable)
  - Description
  - Entitlements (JSON or text)
  - Valid From/Until dates (for promocode validity)
  
- **Promocode Management**:
  - Generate unique promocodes
  - Validate promocode uniqueness
  - Set validity dates
  - Track usage count
  - View registrations by promocode

### 4.4 Admin Features - Field Configuration
- **CRUD Operations**: Create, Read, Update, Delete field configurations
- **Version Management**:
  - Create new version of field configuration
  - View all versions with history
  - Activate a specific version (makes it current)
  - Rollback to previous version
  - Compare versions side-by-side
  - View who created each version and when
- **Fields to Manage**:
  - Field Name (database column name)
  - Field Label (display name)
  - Is Required (toggle required/optional)
  - Validation Rules (JSON: min, max, pattern, etc.)
  - Field Order (display sequence)
  - Field Group (grouping for UI)
  - Active Status (enable/disable field)
  - Version Number (auto-incremented or manual)

### 4.5 View Locations
- `resources/views/super-admin/association-pricing.blade.php`
- `resources/views/super-admin/form-fields.blade.php`

---

## 5. Association Logo Display in Navbar

### 5.1 Logo Display Logic
When association name is passed as URL parameter (`?assoc=GCPIT`), the system should:
1. Look up `association_pricing_rules` by `association_name`
2. If found and `logo_path` exists, display logo in navbar
3. Logo should appear at the top of the navbar (before/after main logo)

### 5.2 Implementation
```php
// In StartupZoneController
public function showForm(Request $request) {
    $associationName = $request->query('assoc');
    $promocode = $request->query('promo') ?? session('startup_zone_promocode');
    $association = null;
    $logoPath = null;
    
    // First check URL parameter
    if ($associationName) {
        $association = AssociationPricingRule::where('association_name', $associationName)
            ->where('is_active', true)
            ->first();
    }
    
    // If not found, check promocode
    if (!$association && $promocode) {
        $association = AssociationPricingRule::where('promocode', $promocode)
            ->where('is_active', true)
            ->first();
        
        if ($association) {
            // Store in session for persistence
            session(['startup_zone_promocode' => $promocode]);
            session(['startup_zone_association' => $association->association_name]);
        }
    }
    
    // Get logo path if association found
    if ($association && $association->logo_path) {
        $logoPath = asset('storage/' . $association->logo_path);
    }
    
    return view('startup-zone.register', compact('association', 'logoPath', 'promocode'));
}
```

### 5.3 Promocode Validation Endpoint
```php
public function validatePromocode(Request $request) {
    $request->validate([
        'promocode' => 'required|string|max:100'
    ]);
    
    $promocode = strtoupper(trim($request->promocode));
    
    $association = AssociationPricingRule::where('promocode', $promocode)
        ->where('is_active', true)
        ->first();
    
    if (!$association) {
        return response()->json([
            'valid' => false,
            'message' => 'Invalid promocode. Please check and try again.'
        ], 404);
    }
    
    // Check if promocode is still valid (date range)
    $now = now();
    if ($association->valid_from && $now->lt($association->valid_from)) {
        return response()->json([
            'valid' => false,
            'message' => 'This promocode is not yet valid.'
        ], 400);
    }
    
    if ($association->valid_until && $now->gt($association->valid_until)) {
        return response()->json([
            'valid' => false,
            'message' => 'This promocode has expired.'
        ], 400);
    }
    
    // Check registration limit
    if ($association->max_registrations) {
        $currentCount = Application::where('promocode', $promocode)
            ->where('application_type', 'startup-zone')
            ->whereIn('status', ['submitted', 'approved'])
            ->count();
        
        if ($currentCount >= $association->max_registrations) {
            return response()->json([
                'valid' => false,
                'message' => 'Registration limit reached for this promocode.'
            ], 400);
        }
    }
    
    // Store in session
    session(['startup_zone_promocode' => $promocode]);
    session(['startup_zone_association' => $association->association_name]);
    
    return response()->json([
        'valid' => true,
        'association' => [
            'name' => $association->association_name,
            'display_name' => $association->display_name,
            'logo_path' => $association->logo_path ? asset('storage/' . $association->logo_path) : null,
            'special_price' => $association->special_price ?? $association->base_price,
            'is_complimentary' => $association->is_complimentary,
            'description' => $association->description,
            'entitlements' => $association->entitlements
        ],
        'message' => 'Promocode validated successfully!'
    ]);
}
```

### 5.4 Navbar Integration
```blade
{{-- In layouts/app.blade.php or navbar partial --}}
@if(isset($logoPath) && $logoPath)
    <div class="association-logo">
        <img src="{{ $logoPath }}" alt="{{ $association->display_name ?? 'Association Logo' }}" 
             class="navbar-logo" style="max-height: 50px;">
    </div>
@endif
```

---

## 6. Draft Table Storage with Restore Function

### 6.1 Draft Storage Strategy
- Use **separate `startup_zone_drafts` table** for temporary form data storage
- Store session ID in `session_id` field to link draft to session
- Auto-save form data every 30 seconds or on field blur
- Allow user to go back/forth and edit any field
- Update existing draft record instead of creating new ones
- Track form completion progress (`progress_percentage`)
- Mark as abandoned after inactivity period

### 6.2 Draft Table Structure
- **All form fields** mapped to corresponding columns in `startup_zone_drafts`
- **Contact data** stored as JSON in `contact_data` column
- **Session tracking** via `session_id` column
- **Progress tracking** via `progress_percentage` column
- **Expiration** via `expires_at` timestamp (auto-cleanup)
- **Abandonment tracking** via `is_abandoned` flag

### 6.3 Benefits of Separate Draft Table
- ✅ Keeps draft data separate from actual applications
- ✅ Easier to manage and cleanup
- ✅ Better for analytics (abandonment rates, completion rates)
- ✅ No need to modify applications table structure
- ✅ Can track which fields were last updated
- ✅ Can calculate form completion percentage

### 6.3 Draft Storage Flow
1. **First Visit**: Create draft application with `status = 'draft'`, `uuid = session_id`
2. **Auto-save**: Update existing draft record on field changes
3. **Form Navigation**: Load draft data when user returns
4. **Final Submit**: Change `status` from 'draft' to 'submitted' after payment

### 6.4 Security Measures

#### 6.4.1 Session Validation
```php
// Generate secure session token
$sessionToken = bin2hex(random_bytes(16));
session(['startup_zone_draft_token' => $sessionToken]);

// Store in application
$application->uuid = 'session_' . $sessionToken;
```

#### 6.4.2 CSRF Protection
- Laravel's built-in CSRF tokens for all form submissions
- Validate CSRF token on every auto-save request

#### 6.4.3 Draft Ownership Validation
```php
// Only allow user to update their own draft
$draft = Application::where('uuid', $sessionToken)
    ->where('status', 'draft')
    ->where('application_type', 'startup-zone')
    ->firstOrFail();

// Verify session matches
if ($draft->uuid !== 'session_' . session('startup_zone_draft_token')) {
    abort(403, 'Unauthorized access to draft');
}
```

#### 6.4.4 Data Encryption (Sensitive Fields)
```php
// Encrypt sensitive data before storing
use Illuminate\Support\Facades\Crypt;

$application->pan_no = Crypt::encryptString($request->pan_no);
$application->gst_no = Crypt::encryptString($request->gst_no);
```

#### 6.4.5 Rate Limiting
```php
// Prevent abuse of auto-save
Route::middleware(['throttle:30,1'])->group(function () {
    Route::post('/startup-zone/auto-save', [StartupZoneController::class, 'autoSave']);
});
```

#### 6.4.6 Input Sanitization
- Validate all inputs server-side
- Sanitize HTML inputs to prevent XSS
- Use Laravel's validation rules

#### 6.4.7 File Upload Security
- Validate file type (MIME type check, not just extension)
- Scan uploaded files for malicious content
- Store files outside web root
- Generate unique filenames

### 6.5 Auto-Save Implementation

#### 6.5.1 Client-Side (JavaScript)
```javascript
// Auto-save on field blur or every 30 seconds
let autoSaveTimer;
const autoSaveInterval = 30000; // 30 seconds

function autoSave() {
    const formData = new FormData(document.getElementById('startup-zone-form'));
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    formData.append('auto_save', true);
    
    fetch('/startup-zone/auto-save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAutoSaveIndicator('Saved');
        }
    });
}

// Auto-save on field blur
document.querySelectorAll('input, select, textarea').forEach(field => {
    field.addEventListener('blur', autoSave);
});

// Periodic auto-save
setInterval(autoSave, autoSaveInterval);
```

#### 6.5.2 Server-Side (Controller)
```php
public function autoSave(Request $request) {
    $sessionToken = session('startup_zone_draft_token');
    
    if (!$sessionToken) {
        $sessionToken = bin2hex(random_bytes(16));
        session(['startup_zone_draft_token' => $sessionToken]);
    }
    
    // Find or create draft
    $draft = Application::firstOrNew([
        'uuid' => 'session_' . $sessionToken,
        'status' => 'draft',
        'application_type' => 'startup-zone'
    ]);
    
    // Get association from promocode if provided
    $assocMem = null;
    $regSource = null;
    $promocode = $request->input('promocode');
    
    if ($promocode) {
        $association = AssociationPricingRule::where('promocode', $promocode)
            ->where('is_active', true)
            ->first();
        
        if ($association) {
            $assocMem = $association->association_name;
            $regSource = $association->association_name; // Store association name in RegSource
            // Store promocode in session
            session(['startup_zone_promocode' => $promocode]);
            session(['startup_zone_association' => $assocMem]);
        }
    }
    
    // Update draft with form data
    $draft->fill($request->only([
        'company_name', 'address', 'city_id', 'state_id', 
        'postal_code', 'country_id', 'landline', 'website',
        'company_email', 'gst_compliance', 'gst_no', 'pan_no',
        'sector_id', 'subSector', 'type_of_business',
        'how_old_startup'
    ]));
    
    // Set promocode, association, and registration source
    $draft->promocode = $promocode ?? $request->input('promocode');
    $draft->assoc_mem = $assocMem ?? $request->input('assoc_mem');
    $draft->RegSource = $regSource ?? $request->input('RegSource');
    
    // Handle file upload
    if ($request->hasFile('certificate')) {
        $path = $request->file('certificate')->store('startup-zone/certificates', 'public');
        $draft->certificate = $path;
    }
    
    $draft->save();
    
    // Save event contact if exists
    if ($draft->id) {
        $contact = EventContact::firstOrNew(['application_id' => $draft->id]);
        $contact->fill($request->only([
            'contact_title', 'contact_first_name', 'contact_last_name',
            'designation', 'email', 'contact_number'
        ]));
        $contact->save();
    }
    
    return response()->json([
        'success' => true,
        'draft_id' => $draft->id,
        'message' => 'Draft saved successfully'
    ]);
}
```

### 6.6 Draft Loading on Page Load
```php
public function showForm(Request $request) {
    $sessionToken = session('startup_zone_draft_token');
    $draft = null;
    $contactData = null;
    $association = null;
    $logoPath = null;
    
    // Check for association from URL parameter or session
    $associationName = $request->query('assoc') ?? session('startup_zone_association');
    $promocode = $request->query('promo') ?? session('startup_zone_promocode');
    
    if ($associationName) {
        $association = AssociationPricingRule::where('association_name', $associationName)
            ->where('is_active', true)
            ->first();
    } elseif ($promocode) {
        $association = AssociationPricingRule::where('promocode', $promocode)
            ->where('is_active', true)
            ->first();
    }
    
    if ($association && $association->logo_path) {
        $logoPath = asset('storage/' . $association->logo_path);
    }
    
    // Load draft from startup_zone_drafts table
    if ($sessionToken) {
        $draft = StartupZoneDraft::where('session_id', $sessionToken)
            ->where('is_abandoned', false)
            ->where('expires_at', '>', now())
            ->first();
        
        if ($draft) {
            // Decrypt sensitive data for display
            if ($draft->gst_no) {
                try {
                    $draft->gst_no = Crypt::decryptString($draft->gst_no);
                } catch (\Exception $e) {
                    $draft->gst_no = null;
                }
            }
            if ($draft->pan_no) {
                try {
                    $draft->pan_no = Crypt::decryptString($draft->pan_no);
                } catch (\Exception $e) {
                    $draft->pan_no = null;
                }
            }
            
            // Parse contact data from JSON
            if ($draft->contact_data) {
                $contactData = json_decode($draft->contact_data, true);
            }
            
            // If draft has promocode, load association
            if ($draft->promocode && !$association) {
                $association = AssociationPricingRule::where('promocode', $draft->promocode)
                    ->where('is_active', true)
                    ->first();
                
                if ($association && $association->logo_path) {
                    $logoPath = asset('storage/' . $association->logo_path);
                }
            }
        }
    }
    
    // Get field configurations (current active version only)
    $fieldConfigs = FormFieldConfiguration::where('form_type', 'startup-zone')
        ->where('is_current_version', true)
        ->where('is_active', true)
        ->orderBy('field_order')
        ->get()
        ->keyBy('field_name');
    
    return view('startup-zone.register', compact('draft', 'contactData', 'fieldConfigs', 'association', 'logoPath', 'promocode'));
}
```

### 6.7 Restore Draft to Application (With Validation)

#### 6.7.1 Restore Function
```php
public function restoreDraftToApplication($draftId) {
    $draft = StartupZoneDraft::findOrFail($draftId);
    
    // Verify ownership
    if ($draft->session_id !== session('startup_zone_draft_token')) {
        abort(403, 'Unauthorized access to draft');
    }
    
    // Validate draft data using current field configuration
    $fieldConfigs = FormFieldConfiguration::where('form_type', 'startup-zone')
        ->where('is_current_version', true)
        ->where('is_active', true)
        ->get()
        ->keyBy('field_name');
    
    $validationRules = $this->getValidationRulesFromConfig($fieldConfigs);
    $draftData = $draft->toArray();
    
    // Decrypt sensitive data for validation
    if ($draft->gst_no) {
        try {
            $draftData['gst_no'] = Crypt::decryptString($draft->gst_no);
        } catch (\Exception $e) {
            $draftData['gst_no'] = null;
        }
    }
    if ($draft->pan_no) {
        try {
            $draftData['pan_no'] = Crypt::decryptString($draft->pan_no);
        } catch (\Exception $e) {
            $draftData['pan_no'] = null;
        }
    }
    
    // Parse contact data
    $contactData = json_decode($draft->contact_data, true);
    if ($contactData) {
        $draftData = array_merge($draftData, [
            'contact_title' => $contactData['title'] ?? null,
            'contact_first_name' => $contactData['first_name'] ?? null,
            'contact_last_name' => $contactData['last_name'] ?? null,
            'contact_designation' => $contactData['designation'] ?? null,
            'contact_email' => $contactData['email'] ?? null,
            'contact_mobile' => $contactData['mobile'] ?? null,
            'contact_mobile_country_code' => $contactData['country_code'] ?? null,
        ]);
    }
    
    // Validate data
    $validator = Validator::make($draftData, $validationRules);
    
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
            'message' => 'Draft data validation failed. Please complete all required fields.'
        ], 422);
    }
    
    // Create application from draft
    DB::beginTransaction();
    try {
        // Generate application ID
        $applicationId = $this->generateApplicationId();
        
        // Create application
        $application = Application::create([
            'user_id' => $draft->user_id,
            'application_id' => $applicationId,
            'application_type' => 'startup-zone',
            'participant_type' => 'Startup',
            'status' => 'initiated',
            'submission_status' => 'in progress',
            'event_id' => $draft->event_id,
            
            // Company information
            'company_name' => $draft->company_name,
            'certificate' => $draft->certificate_path,
            'how_old_startup' => $draft->how_old_startup,
            'address' => $draft->address,
            'city_id' => $draft->city_id,
            'state_id' => $draft->state_id,
            'postal_code' => $draft->postal_code,
            'country_id' => $draft->country_id,
            'landline' => $draft->landline,
            'website' => $draft->website,
            'company_email' => $draft->company_email,
            
            // Tax information
            'gst_compliance' => $draft->gst_compliance,
            'gst_no' => $draftData['gst_no'], // Already decrypted
            'pan_no' => $draftData['pan_no'], // Already decrypted
            
            // Sector information
            'sector_id' => $draft->sector_id,
            'subSector' => $draft->subSector,
            'type_of_business' => $draft->type_of_business,
            
            // Association & Promocode
            'promocode' => $draft->promocode,
            'assoc_mem' => $draft->assoc_mem,
            'RegSource' => $draft->RegSource,
            
            // Booth information
            'stall_category' => $draft->stall_category ?? 'Startup Booth',
            'interested_sqm' => $draft->interested_sqm ?? 'Booth / POD',
        ]);
        
        // Create event contact
        if ($contactData) {
            EventContact::create([
                'application_id' => $application->id,
                'salutation' => $contactData['title'] ?? null,
                'first_name' => $contactData['first_name'],
                'last_name' => $contactData['last_name'] ?? null,
                'designation' => $contactData['designation'] ?? null,
                'email' => $contactData['email'],
                'contact_number' => $contactData['mobile'] ?? null,
            ]);
        }
        
        // Delete draft after successful restore
        $draft->delete();
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'application_id' => $application->id,
            'message' => 'Draft restored to application successfully!'
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error restoring draft to application: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error restoring draft. Please try again.'
        ], 500);
    }
}

// Generate unique application ID
private function generateApplicationId() {
    do {
        $applicationId = 'STZ-' . strtoupper(Str::random(8)) . '-' . date('Y');
    } while (Application::where('application_id', $applicationId)->exists());
    
    return $applicationId;
}
```

#### 6.7.2 Restore Route
```php
Route::post('/startup-zone/restore-draft/{draft}', [StartupZoneController::class, 'restoreDraftToApplication'])
    ->name('startup-zone.restore-draft');
```

### 6.8 Draft Cleanup
- **Auto-cleanup**: Delete drafts where `expires_at < now()` (cron job)
- **Abandoned drafts**: Mark as `is_abandoned = 1` after 7 days of inactivity
- **After restore**: Delete draft after successful restore to application
- **Manual cleanup**: Admin can manually delete old/abandoned drafts
- **Cron job**: Run daily to cleanup expired and abandoned drafts

---

## 7. Implementation Structure

### 7.1 Routes
```php
// Public routes (no auth required for registration)
Route::get('/startup-zone/register', [StartupZoneController::class, 'showForm'])
    ->name('startup-zone.register');
Route::post('/startup-zone/register', [StartupZoneController::class, 'store'])
    ->name('startup-zone.store');
Route::get('/startup-zone/preview/{application}', [StartupZoneController::class, 'preview'])
    ->name('startup-zone.preview');
Route::post('/startup-zone/payment', [StartupZoneController::class, 'processPayment'])
    ->name('startup-zone.payment');
Route::get('/startup-zone/success', [StartupZoneController::class, 'success'])
    ->name('startup-zone.success');
Route::get('/startup-zone/captcha', [StartupZoneController::class, 'captcha'])
    ->name('startup-zone.captcha');
Route::get('/startup-zone/check-availability', [StartupZoneController::class, 'checkAvailability'])
    ->name('startup-zone.check-availability');
```

### 7.2 Controller: `StartupZoneController`
**Methods:**
- `showForm()` - Display registration form with association pricing, load draft if exists
- `autoSave()` - Auto-save draft data to `startup_zone_drafts` table (AJAX, rate-limited)
- `validatePromocode()` - Validate promocode and return association details (AJAX)
- `store()` - Final submission: restore draft to application with validation
- `restoreDraftToApplication()` - **NEW: Restore draft to applications table with full validation**
- `preview()` - Show preview page (Step 2)
- `processPayment()` - Create invoice and redirect to payment (Step 3)
- `success()` - Payment success page
- `captcha()` - Generate CAPTCHA image
- `checkAvailability()` - AJAX endpoint to check association limits
- `calculatePricing()` - Calculate prices based on association/promocode
- `deleteDraft()` - Allow user to delete their draft
- `getDraftProgress()` - Get draft completion percentage (AJAX)
- `calculateProgress()` - Calculate form completion percentage

### 7.3 Models
- `Application` (existing) - No draft status needed (drafts in separate table)
- `EventContact` (existing)
- `Invoice` (existing)
- `Payment` (existing)
- `StartupZoneDraft` (new) - Model for draft table
- `AssociationPricingRule` (new)
- `FormFieldConfiguration` (new)

### 7.4 Views Structure
```
resources/views/startup-zone/
├── register.blade.php (Step 1: Main form with auto-save)
├── preview.blade.php (Step 2: Preview)
├── payment.blade.php (Step 3: Payment processing)
└── success.blade.php (Success page)

resources/views/super-admin/
├── association-pricing.blade.php (Admin interface)
└── form-fields.blade.php (Field configuration interface)
```

### 7.5 Dynamic Field Rendering
Form fields will be rendered based on `form_field_configurations` table:
- Required fields show asterisk (*)
- Optional fields don't show asterisk
- Validation rules applied dynamically
- Field order respected
- Inactive fields hidden

---

## 8. Dynamic Form Validation Based on Admin Configuration

### 8.1 Validation Rule Generation
Validation rules are generated dynamically from `form_field_configurations` table:

```php
public function getValidationRules() {
    // Get only current active version
    $fieldConfigs = FormFieldConfiguration::where('form_type', 'startup-zone')
        ->where('is_current_version', true)
        ->where('is_active', true)
        ->get();
    
    $rules = [];
    
    foreach ($fieldConfigs as $config) {
        $fieldRules = [];
        
        // Add required rule if configured
        if ($config->is_required) {
            $fieldRules[] = 'required';
        } else {
            $fieldRules[] = 'nullable';
        }
        
        // Add custom validation rules from JSON
        if ($config->validation_rules) {
            $customRules = json_decode($config->validation_rules, true);
            foreach ($customRules as $rule => $value) {
                $fieldRules[] = $rule . ':' . $value;
            }
        }
        
        $rules[$config->field_name] = implode('|', $fieldRules);
    }
    
    return $rules;
}
```

### 8.2 Default Validation Rules (Fallback)
If field configuration doesn't exist, use these defaults:

### 8.3 Server-Side Validation (Laravel) - Default Rules

```php
// Application validation
'company_name' => 'required|string|max:255',
'certificate' => 'required|file|mimes:pdf|max:2048',
'how_old_startup' => 'required|integer|between:1,7',
'address' => 'required|string|max:500',
'city_id' => 'required|string|max:100',
'state_id' => 'required|exists:states,id',
'postal_code' => 'required|string|max:10',
'country_id' => 'required|exists:countries,id',
'landline' => 'required|string|max:20',
'website' => 'required|string|url',
'company_email' => 'required|email|max:255',
'gst_compliance' => 'required|boolean',
'gst_no' => 'required_if:gst_compliance,1|string|size:15|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
'pan_no' => 'required|string|size:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
'sector_id' => 'required|string|max:255',
'subSector' => 'required|string|max:255',
'type_of_business' => 'required_if:subSector,Other|string|max:255',
'assoc_mem' => 'nullable|string|max:125',
'promocode' => 'nullable|string|max:100|exists:association_pricing_rules,promocode', // Promocode validation
'RegSource' => 'nullable|string|max:250', // Association name (auto-filled, not validated)
'terms_accepted' => 'required|accepted',

// Event Contact validation
'contact_title' => 'required|in:Mr.,Mrs.,Ms.,Dr.,Prof.',
'contact_first_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
'contact_last_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
'contact_designation' => 'required|string|max:100',
'contact_email' => 'required|email|max:255',
'contact_mobile' => 'required|string|size:10|regex:/^[0-9]+$/',
'contact_mobile_country_code' => 'required|string',

// Payment validation
'payment_mode' => 'required|in:Credit Card,Paypal,Bank Transfer',
'captcha' => 'required|string|size:5'
```

---

## 9. Pricing Calculation Logic

### 7.1 Flow
1. User selects/enters association name (`assoc_mem`)
2. System looks up `AssociationPricingRule` by `association_name`
3. If found and active:
   - Use `special_price` if set, else `base_price`
   - Check if `is_complimentary` = true (free)
   - Check `max_registrations` limit
4. Calculate:
   ```php
   Base Price = special_price OR base_price (from rule)
   GST (18%) = Base Price × 0.18
   Processing Charges:
     - CCAvenue (Indian): Base Price × 3%
     - PayPal (International): Base Price × 9.5%
   Total = Base Price + GST + Processing Charges
   ```

### 7.2 Default Pricing (if no rule found)
- Base Price: ₹52,000
- GST: ₹9,360 (18%)
- Processing (CCAvenue): ₹1,560 (3%)
- Total: ₹62,920

---

## 10. Registration Limits Logic

### 8.1 Association Limit Check
```php
// Check if association has registration limit
$rule = AssociationPricingRule::where('association_name', $assocName)
    ->where('is_active', true)
    ->first();

if ($rule && $rule->max_registrations) {
    $currentCount = Application::where('assoc_mem', $assocName)
        ->where('application_type', 'startup-zone')
        ->whereIn('status', ['submitted', 'approved'])
        ->count();
    
    if ($currentCount >= $rule->max_registrations) {
        // Show "Registration Full" message
        // Prevent form submission
    }
}
```

### 8.2 Auto-Update Current Registrations
- After successful payment, increment `current_registrations` in `AssociationPricingRule`

---

## 11. Implementation Phases

### Phase 1: Database & Admin Interface (Day 1)
- [ ] Create `startup_zone_drafts` migration (NEW - separate draft table)
- [ ] Create `association_pricing_rules` migration (with logo_path and promocode)
- [ ] Create `form_field_configurations` migration (with versioning support)
- [ ] **Create migration to add `promocode` column to `applications` table**
- [ ] Create `StartupZoneDraft` model (NEW)
- [ ] Create `AssociationPricingRule` model
- [ ] Create `FormFieldConfiguration` model (with versioning methods)
- [ ] Add admin routes for pricing rules and field configuration
- [ ] Build admin interface for association pricing (CRUD + logo upload + promocode)
- [ ] Build admin interface for field configuration (CRUD + bulk update + versioning)
- [ ] **Build version management UI (create, activate, rollback versions)**
- [ ] Seed default association rules
- [ ] Seed default field configurations (version 1.0)

### Phase 2: Basic Form Structure (Day 1-2)
- [ ] Create routes and controller
- [ ] Implement session token generation
- [ ] Build Step 1 form view (Bootstrap)
- [ ] **Add promocode input field with validation**
- [ ] **Implement promocode lookup and validation (AJAX)**
- [ ] Implement association selection/detection from URL parameter OR promocode
- [ ] **Auto-fill association name when promocode validated**
- [ ] Display association logo in navbar (if configured via URL or promocode)
- [ ] Display dynamic pricing table (update based on promocode)
- [ ] Implement dynamic field rendering based on field configuration

### Phase 3: Form Functionality (Day 2-3)
- [ ] Add all form fields mapped to `startup_zone_drafts` table
- [ ] **Store promocode in `promocode` column (draft table)**
- [ ] **Auto-fill assoc_mem from promocode lookup**
- [ ] Implement file upload for certificate (with security checks)
- [ ] Add international telephone input
- [ ] Integrate CAPTCHA
- [ ] Implement auto-save functionality to `startup_zone_drafts` table:
  - Client-side auto-save (every 30s or on blur)
  - Server-side auto-save endpoint
  - Encrypt sensitive data (GST, PAN)
  - Store contact data as JSON
  - Calculate progress percentage
- [ ] Implement draft loading on page load from `startup_zone_drafts`
- [ ] **Decrypt sensitive data when loading draft**
- [ ] **Parse contact data from JSON when loading draft**
- [ ] **Implement restore function: `restoreDraftToApplication()`**
  - Validate draft data using current field configuration
  - Create application record
  - Create event_contact record
  - Delete draft after successful restore
- [ ] Add draft cleanup mechanism (expired, abandoned)
- [ ] Client-side validation (dynamic based on field config)
- [ ] **Client-side promocode validation (real-time)**
- [ ] Server-side validation (dynamic based on field config)
- [ ] **Server-side promocode validation**
- [ ] Security measures (CSRF, session validation, encryption)
- [ ] Rate limiting for auto-save

### Phase 4: Preview & Pricing (Day 3)
- [ ] Build preview page (Step 2)
- [ ] Implement pricing calculation
- [ ] Display invoice breakdown
- [ ] Association limit checking

### Phase 5: Payment Integration (Day 4)
- [ ] Create invoice after preview confirmation
- [ ] Integrate payment gateways (CCAvenue, PayPal)
- [ ] Handle payment callbacks
- [ ] Update application status after payment

### Phase 6: Testing & Refinement (Day 5)
- [ ] Comprehensive testing
- [ ] Bug fixes
- [ ] UI/UX improvements
- [ ] Email notifications
- [ ] Documentation

---

## 12. Files to Create/Modify

### New Files:
- `app/Http/Controllers/StartupZoneController.php`
- `app/Models/StartupZoneDraft.php` (NEW - Draft model)
- `app/Models/AssociationPricingRule.php`
- `app/Models/FormFieldConfiguration.php`
- `database/migrations/xxxx_create_startup_zone_drafts_table.php` (NEW - Draft table)
- `database/migrations/xxxx_create_association_pricing_rules_table.php`
- `database/migrations/xxxx_create_form_field_configurations_table.php`
- `database/migrations/xxxx_add_promocode_to_applications_table.php` (NEW)
- `database/seeders/AssociationPricingRuleSeeder.php`
- `database/seeders/FormFieldConfigurationSeeder.php`
- `resources/views/startup-zone/register.blade.php`
- `resources/views/startup-zone/preview.blade.php`
- `resources/views/startup-zone/payment.blade.php`
- `resources/views/startup-zone/success.blade.php`
- `resources/views/super-admin/association-pricing.blade.php`
- `resources/views/super-admin/form-fields.blade.php`
- `app/Mail/StartupZoneRegistrationConfirmation.php`
- `app/Mail/StartupZonePaymentSuccess.php`
- `public/js/startup-zone-form.js` (with auto-save functionality, promocode validation)
- `app/Console/Commands/CleanupDraftApplications.php` (cron job)

### Modified Files:
- `routes/web.php` (add startup zone routes)
- `app/Http/Controllers/SuperAdminController.php` (add association pricing & field config methods)
- `app/Models/Application.php` (add promocode to fillable, no draft status needed)
- `resources/views/partials/app-navbar.blade.php` (add association logo display)
- `database/migrations/2025_01_08_101925_create_applications_table.php` (add 'draft' to status enum if needed)

---

## 13. Key Differences from Original Plan

1. ✅ **Separate draft table** - Uses `startup_zone_drafts` table for temporary storage
2. ✅ **No draft status in applications** - Applications table only for submitted applications
3. ✅ **Uses `event_contacts`** - For contact person details
4. ✅ **Admin-configurable pricing** - New `association_pricing_rules` table
5. ✅ **Restore function** - Validates and converts draft to application
6. ✅ **Leverages existing infrastructure** - Invoice generation, payment processing
7. ✅ **Version management** - Form field configurations with versioning
8. ✅ **Promocode column** - Separate `promocode` column in applications table

---

## 14. Data Flow

### 14.1 Registration Flow (with Draft Table)
1. User visits form → Generate session token → Check for existing draft in `startup_zone_drafts` table
2. User enters promocode → Validate → Auto-fill association details
3. User fills form → Auto-save to `startup_zone_drafts` table:
   - Store all form fields in corresponding columns
   - Store contact data as JSON in `contact_data` column
   - Encrypt sensitive data (GST, PAN)
   - Calculate and store `progress_percentage`
   - Set `expires_at` (30 days from now)
4. User navigates back/forth → Load draft data from `startup_zone_drafts` table
5. User completes form → Click "Submit" → **Restore function called**:
   - Validate all draft data using current field configuration
   - If valid → Create `application` record
   - Create `event_contact` record
   - Delete draft from `startup_zone_drafts` table
6. Preview → Show summary from `applications` table
7. Payment → Create `invoice` record
8. Payment processing → Create `payment` record
9. Success → Update `application.status` to "submitted"

**If user drops off**:
- Draft remains in `startup_zone_drafts` table
- Can be restored later using restore function
- Auto-marked as abandoned after 7 days
- Auto-deleted after expiration (30 days)

### 12.2 Pricing Flow
1. Form loads → Fetch active `association_pricing_rules`
2. User selects association → Look up pricing rule
3. Display price → Show in tariff table
4. Calculate totals → Base + GST + Processing
5. Create invoice → Store calculated amounts

---

## 15. Security Checklist

### 15.1 Session Security
- [x] Secure session token generation (random_bytes)
- [x] Session token stored in session (not exposed in URL)
- [x] Draft ownership validation (session token match)
- [x] Session expiration handling

### 15.2 Data Security
- [x] CSRF protection on all forms
- [x] Input sanitization
- [x] SQL injection prevention (Eloquent ORM)
- [x] XSS prevention (Blade escaping)
- [x] Sensitive data encryption (PAN, GST numbers)
- [x] File upload validation (MIME type, size, scanning)

### 15.3 Access Control
- [x] Rate limiting on auto-save endpoint
- [x] Draft access restricted to session owner
- [x] Admin-only access to field configuration
- [x] Association logo upload restricted to admin

### 15.4 Validation Security
- [x] Server-side validation (never trust client)
- [x] Dynamic validation based on admin config
- [x] File type validation (not just extension)
- [x] File size limits enforced

---

## 16. Promocode Implementation Details

### 16.1 Form Field for Promocode
- Add promocode input field in the form (typically at the top, before company information)
- Real-time validation via AJAX on blur or after typing
- Show success/error messages with visual feedback
- Auto-fill association details when valid promocode entered:
  - Association name (`assoc_mem`)
  - Display association logo (if available)
  - Update pricing table with association pricing
  - Show association entitlements
- Store promocode in `RegSource` column (as entered by user)
- Promocode field can be optional or required (configurable by admin)

### 16.1.1 Promocode Input Field (Blade Example)
```blade
<div class="form-group">
    <label class="control-label col-md-3">
        Promocode <span class="text-muted">(Optional)</span>
    </label>
    <div class="col-md-6">
        <div class="input-group">
            <input type="text" 
                   class="form-control" 
                   name="promocode" 
                   id="promocode"
                   value="{{ old('promocode', $draft->promocode ?? '') }}"
                   placeholder="Enter promocode"
                   autocomplete="off">
            <button type="button" 
                    class="btn btn-primary" 
                    id="validate-promocode-btn">
                Validate
            </button>
        </div>
        <small class="text-muted">Enter your promocode to get special pricing</small>
        <div id="promocode-feedback" class="mt-2"></div>
    </div>
</div>
```

### 16.1.2 JavaScript for Promocode Validation
```javascript
document.getElementById('validate-promocode-btn').addEventListener('click', validatePromocode);
document.getElementById('promocode').addEventListener('blur', validatePromocode);

function validatePromocode() {
    const promocode = document.getElementById('promocode').value.trim().toUpperCase();
    const feedback = document.getElementById('promocode-feedback');
    
    if (!promocode) {
        feedback.innerHTML = '';
        return;
    }
    
    feedback.innerHTML = '<span class="text-info">Validating...</span>';
    
    fetch('/startup-zone/validate-promocode', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ promocode: promocode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            feedback.innerHTML = `<span class="text-success">
                <i class="fas fa-check-circle"></i> Valid! ${data.association.display_name}
            </span>`;
            // Store promocode in hidden field or form data
            // Auto-fill association name
            if (document.getElementById('assoc_mem')) {
                document.getElementById('assoc_mem').value = data.association.name;
            }
            // Store RegSource (association name for tracking)
            if (document.getElementById('RegSource')) {
                document.getElementById('RegSource').value = data.association.name;
            }
            // Update pricing table
            updatePricingTable(data.association);
            // Show logo if available
            if (data.association.logo_path) {
                showAssociationLogo(data.association.logo_path);
            }
        } else {
            feedback.innerHTML = `<span class="text-danger">
                <i class="fas fa-times-circle"></i> ${data.message}
            </span>`;
        }
    })
    .catch(error => {
        feedback.innerHTML = '<span class="text-danger">Error validating promocode</span>';
    });
}
```

### 16.2 Promocode Validation Rules
- Promocode must be unique across all associations
- Case-insensitive matching (convert to uppercase)
- Check validity dates (valid_from, valid_until)
- Check registration limits
- Check if association is active
- Return association details on successful validation

### 16.3 Data Storage Flow
1. User enters promocode → Validate via AJAX
2. If valid → Auto-fill `assoc_mem` with association name
3. Store promocode in `promocode` column (applications table)
4. Store association name in `RegSource` column (for tracking/reporting)
5. Apply association pricing rules
6. Display association logo if available
7. Track registration by promocode for limit checking

### 16.4 Admin Promocode Management
- Admin can create/edit/delete promocodes
- Each association can have one promocode
- Promocode must be unique
- Admin can view registrations by promocode
- Admin can see usage statistics per promocode

---

## 17. Version Management for Form Field Configuration

### 17.1 Version Workflow
1. **Initial Setup**: Create version 1.0 with default field configurations
2. **Making Changes**: When admin updates field requirements:
   - Create new version (e.g., 1.1, 2.0)
   - Copy current version's data
   - Apply changes to new version
   - Set new version as `is_current_version = 1`
   - Set old version as `is_current_version = 0`
3. **Activating Version**: Admin can activate any existing version
4. **Rollback**: Admin can rollback to previous version
5. **History**: All versions are preserved for audit trail

### 17.2 Version Numbering Strategy
- **Major Version** (e.g., 2.0): Significant changes (new fields, major rule changes)
- **Minor Version** (e.g., 1.1): Small changes (required/optional toggle, validation updates)
- Auto-increment or manual version number entry

### 17.3 Version Comparison
- Admin can compare two versions side-by-side
- Highlight differences (required/optional changes, validation rule changes)
- Show which fields were added/removed/modified

### 17.4 Implementation Example
```php
// Create new version
public function createFormFieldVersion(Request $request) {
    $request->validate([
        'version' => 'required|string|max:20',
        'description' => 'nullable|string'
    ]);
    
    // Get current version
    $currentVersion = FormFieldConfiguration::where('form_type', 'startup-zone')
        ->where('is_current_version', true)
        ->get();
    
    // Deactivate current version
    FormFieldConfiguration::where('form_type', 'startup-zone')
        ->where('is_current_version', true)
        ->update(['is_current_version' => false]);
    
    // Create new version by copying current
    foreach ($currentVersion as $field) {
        FormFieldConfiguration::create([
            'form_type' => $field->form_type,
            'version' => $request->version,
            'field_name' => $field->field_name,
            'field_label' => $field->field_label,
            'is_required' => $field->is_required,
            'validation_rules' => $field->validation_rules,
            'field_order' => $field->field_order,
            'field_group' => $field->field_group,
            'is_active' => $field->is_active,
            'is_current_version' => true,
            'created_by' => auth()->id()
        ]);
    }
    
    return back()->with('success', "Version {$request->version} created successfully!");
}

// Activate version
public function activateFormFieldVersion(Request $request) {
    $request->validate([
        'version' => 'required|string'
    ]);
    
    // Deactivate all versions
    FormFieldConfiguration::where('form_type', 'startup-zone')
        ->update(['is_current_version' => false]);
    
    // Activate selected version
    FormFieldConfiguration::where('form_type', 'startup-zone')
        ->where('version', $request->version)
        ->update(['is_current_version' => true]);
    
    return back()->with('success', "Version {$request->version} activated successfully!");
}
```

---

## 18. Questions to Clarify

1. Should we create user accounts automatically after registration?
2. How to handle guest registrations (no user account)?
3. Should `application_type = "startup-zone"` be a separate enum value?
4. Do we need integration with existing ApplicationController?
5. Should startup zone applications appear in regular admin dashboard?
6. What happens if user already has an application for the event?
7. **Should promocode be required or optional?**
8. **Can one association have multiple promocodes?** (Currently: One promocode per association)
9. **Should promocode be case-sensitive?** (Currently: Case-insensitive, stored as uppercase)
10. **How should version numbers be managed?** (Auto-increment or manual?)
11. **Should old versions be deletable?** (Currently: Preserved for audit)
12. **How many versions should be kept?** (All or limit to last N versions?)

---

## 19. Draft Table vs Applications Table

### 19.1 Why Separate Draft Table?
- **Data Separation**: Draft data separate from actual applications
- **Better Management**: Easier to cleanup, track, and analyze drafts
- **No Schema Changes**: Don't need to modify applications table structure
- **Performance**: Applications table stays clean, only submitted records
- **Analytics**: Can track abandonment rates, completion rates, field-level analytics
- **Security**: Draft data can have different security/retention policies

### 19.2 Draft Table Features
- Stores all form field values as user types
- Tracks completion progress (`progress_percentage`)
- Marks abandoned drafts (`is_abandoned`)
- Auto-expires after 30 days (`expires_at`)
- Encrypts sensitive data (GST, PAN)
- Stores contact data as JSON
- Tracks last updated field

### 19.3 Restore Process
1. User clicks "Submit" or "Continue"
2. System calls `restoreDraftToApplication()` function
3. Validates draft data using current field configuration
4. If valid → Creates application record
5. Creates event_contact record
6. Deletes draft from `startup_zone_drafts` table
7. If validation fails → Returns errors, keeps draft for user to fix

### 19.4 Draft Lifecycle
```
User starts form → Draft created in startup_zone_drafts
  ↓
User fills fields → Auto-save updates draft
  ↓
User drops off → Draft remains (abandoned after 7 days)
  ↓
User returns → Draft loaded, can continue
  ↓
User submits → Restore function validates & creates application
  ↓
Success → Draft deleted, application created
```

---

## 20. Summary of Key Modifications

### 20.1 New Draft Table: `startup_zone_drafts`
- **Purpose**: Store temporary form data while user is filling the form
- **Benefits**: 
  - Separate from applications table
  - Better for analytics and management
  - No need to modify applications table
  - Can track progress and abandonment
- **Features**:
  - All form fields mapped to columns
  - Contact data stored as JSON
  - Sensitive data encrypted
  - Progress tracking
  - Auto-expiration
  - Abandonment tracking

### 20.2 Restore Function
- **Function**: `restoreDraftToApplication($draftId)`
- **Process**:
  1. Load draft from `startup_zone_drafts` table
  2. Decrypt sensitive data
  3. Parse contact data from JSON
  4. Validate using current field configuration
  5. If valid → Create application and event_contact
  6. Delete draft after successful restore
- **Validation**: Uses current active version of field configuration
- **Error Handling**: Returns validation errors if data incomplete

### 20.3 Promocode Column in Applications Table
- **New Column**: `promocode` (varchar 100, nullable, indexed)
- **Purpose**: Store the actual promocode entered by user (e.g., "TIESB", "TIESNB")
- **Migration**: `2025_12_18_000007_add_promocode_to_applications_table.php`
- **Usage**: 
  - Primary field for promocode storage
  - Used for registration limit tracking
  - Used for reporting and analytics
- **Relationship**: 
  - `promocode` → User input (e.g., "TIESB")
  - `assoc_mem` → Auto-filled association name (e.g., "TIE Bangalore")
  - `RegSource` → Association name for tracking (e.g., "TIE Bangalore")

### 20.4 Form Field Configuration Versioning
- **Versioning System**: Multiple versions of field configurations can exist
- **Current Version**: Only one version marked as `is_current_version = 1`
- **Version Fields**:
  - `version` (varchar 20) - Version number (e.g., "1.0", "1.1", "2.0")
  - `is_current_version` (boolean) - Marks active version
  - `created_by` (foreign key) - Tracks who created the version
- **Features**:
  - Create new versions when making changes
  - Activate any existing version
  - Rollback to previous versions
  - Compare versions side-by-side
  - Preserve all versions for audit trail
- **Form Behavior**: Form always uses the current active version (`is_current_version = 1`)

### 20.5 Data Storage Summary

| Data | Column | Source |
|------|--------|--------|
| Promocode (user input) | `promocode` | User enters in form |
| Association Name | `assoc_mem` | Auto-filled from promocode lookup |
| Organization/Association | `RegSource` | Auto-filled from promocode lookup (for tracking) |

### 20.6 Version Management Workflow

```
Version 1.0 (Initial)
  ↓
Admin makes changes
  ↓
Version 1.1 (New version created)
  ↓
Set 1.1 as current (is_current_version = 1)
  ↓
Form now uses Version 1.1
  ↓
If issues found → Rollback to Version 1.0
```

---

**End of Revised Plan**

---

**End of Revised Plan**
