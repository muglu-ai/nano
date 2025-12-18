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
| Association Name | `assoc_mem` | string | GCPIT, ELEVATE, IBioM, etc. |
| Promo Code | `RegSource` | string | For TIESB/TIESNB tracking |
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

## 2. New Table: Association Pricing Rules (Admin Configurable)

### 2.1 Table: `association_pricing_rules`

```sql
CREATE TABLE `association_pricing_rules` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `association_name` varchar(255) NOT NULL UNIQUE,
  `display_name` varchar(255) NOT NULL,
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
  INDEX `idx_is_active` (`is_active`)
);
```

**Purpose**: Allow super-admin to configure pricing rules for different associations/partners.

**Admin Interface**: Add to Super Admin panel for managing these rules.

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

#### **Association & Tracking**
- `assoc_mem` → Association name (GCPIT, ELEVATE, IBioM, etc.)
- `RegSource` → Promo code (TIESB, TIESNB for tracking limits)

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

## 4. Admin Interface for Association Pricing Rules

### 4.1 Super Admin Routes
```php
Route::get('/super-admin/association-pricing', [SuperAdminController::class, 'associationPricing'])
    ->name('super-admin.association-pricing');
Route::post('/super-admin/association-pricing', [SuperAdminController::class, 'storeAssociationPricing'])
    ->name('super-admin.association-pricing.store');
Route::put('/super-admin/association-pricing/{id}', [SuperAdminController::class, 'updateAssociationPricing'])
    ->name('super-admin.association-pricing.update');
Route::delete('/super-admin/association-pricing/{id}', [SuperAdminController::class, 'deleteAssociationPricing'])
    ->name('super-admin.association-pricing.delete');
```

### 4.2 Admin Features
- **CRUD Operations**: Create, Read, Update, Delete association rules
- **Fields to Manage**:
  - Association Name (unique identifier)
  - Display Name (for UI)
  - Base Price (default: ₹52,000)
  - Special Price (discounted price)
  - Is Complimentary (free registration)
  - Max Registrations (limit, e.g., 25 for TIESB/TIESNB)
  - Current Registrations (auto-calculated)
  - Active Status (enable/disable)
  - Description
  - Entitlements (JSON or text)
  - Valid From/Until dates

### 4.3 View Location
- `resources/views/super-admin/association-pricing.blade.php`

---

## 5. Implementation Structure

### 5.1 Routes
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

### 5.2 Controller: `StartupZoneController`
**Methods:**
- `showForm()` - Display registration form with association pricing
- `store()` - Store application and event contact (Step 1)
- `preview()` - Show preview page (Step 2)
- `processPayment()` - Create invoice and redirect to payment (Step 3)
- `success()` - Payment success page
- `captcha()` - Generate CAPTCHA image
- `checkAvailability()` - AJAX endpoint to check association limits
- `calculatePricing()` - Calculate prices based on association

### 5.3 Models
- `Application` (existing)
- `EventContact` (existing)
- `Invoice` (existing)
- `Payment` (existing)
- `AssociationPricingRule` (new)

### 5.4 Views Structure
```
resources/views/startup-zone/
├── register.blade.php (Step 1: Main form)
├── preview.blade.php (Step 2: Preview)
├── payment.blade.php (Step 3: Payment processing)
└── success.blade.php (Success page)

resources/views/super-admin/
└── association-pricing.blade.php (Admin interface)
```

---

## 6. Form Validation Rules

### 6.1 Server-Side Validation (Laravel)

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

## 7. Pricing Calculation Logic

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

## 8. Registration Limits Logic

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

## 9. Implementation Phases

### Phase 1: Database & Admin Interface (Day 1)
- [ ] Create `association_pricing_rules` migration
- [ ] Create `AssociationPricingRule` model
- [ ] Add admin routes for pricing rules
- [ ] Build admin interface (CRUD)
- [ ] Seed default association rules

### Phase 2: Basic Form Structure (Day 1-2)
- [ ] Create routes and controller
- [ ] Build Step 1 form view (Bootstrap)
- [ ] Implement association selection/detection
- [ ] Display dynamic pricing table

### Phase 3: Form Functionality (Day 2-3)
- [ ] Add all form fields mapped to applications table
- [ ] Implement file upload for certificate
- [ ] Add international telephone input
- [ ] Integrate CAPTCHA
- [ ] Client-side validation
- [ ] Store application and event contact

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

## 10. Files to Create/Modify

### New Files:
- `app/Http/Controllers/StartupZoneController.php`
- `app/Models/AssociationPricingRule.php`
- `database/migrations/xxxx_create_association_pricing_rules_table.php`
- `database/seeders/AssociationPricingRuleSeeder.php`
- `resources/views/startup-zone/register.blade.php`
- `resources/views/startup-zone/preview.blade.php`
- `resources/views/startup-zone/payment.blade.php`
- `resources/views/startup-zone/success.blade.php`
- `resources/views/super-admin/association-pricing.blade.php`
- `app/Mail/StartupZoneRegistrationConfirmation.php`
- `app/Mail/StartupZonePaymentSuccess.php`
- `public/js/startup-zone-form.js`

### Modified Files:
- `routes/web.php` (add startup zone routes)
- `app/Http/Controllers/SuperAdminController.php` (add association pricing methods)
- `app/Models/Application.php` (add any needed relationships/accessors)

---

## 11. Key Differences from Original Plan

1. ✅ **No new registration table** - Uses `applications` table
2. ✅ **No new payment table** - Uses existing `invoices` and `payments`
3. ✅ **Uses `event_contacts`** - For contact person details
4. ✅ **Admin-configurable pricing** - New `association_pricing_rules` table
5. ✅ **Leverages existing infrastructure** - Invoice generation, payment processing

---

## 12. Data Flow

### 12.1 Registration Flow
1. User fills form → Store in `applications` table
2. Store contact person → Store in `event_contacts` table
3. Preview → Show summary
4. Payment → Create `invoice` record
5. Payment processing → Create `payment` record
6. Success → Update `application.status` to "submitted"

### 12.2 Pricing Flow
1. Form loads → Fetch active `association_pricing_rules`
2. User selects association → Look up pricing rule
3. Display price → Show in tariff table
4. Calculate totals → Base + GST + Processing
5. Create invoice → Store calculated amounts

---

## 13. Questions to Clarify

1. Should we create user accounts automatically after registration?
2. How to handle guest registrations (no user account)?
3. Should `application_type = "startup-zone"` be a separate enum value?
4. Do we need integration with existing ApplicationController?
5. Should startup zone applications appear in regular admin dashboard?
6. What happens if user already has an application for the event?

---

**End of Revised Plan**
