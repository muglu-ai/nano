# Exhibitor Payment Form - Implementation Plan

## Overview
Rebuild the exhibitor payment form (`exhibitor_payment_form.php`) as a modern Laravel application with Bootstrap framework, proper validation, and multi-step wizard functionality.

---

## 1. Form Analysis Summary

### 1.1 Key Features Identified
- **Multi-step wizard** (3 steps: Exhibitor Details → Preview → Payment)
- **Association/Partner-based pricing** (GCPIT, ELEVATE, IBioM, Presidency, TIESB, TIESNB, KDEM, STPI, leap, beyondBengaluru)
- **Dynamic tariff table** with special offers based on association
- **File upload** for Company Registration Certificate (PDF, max 2MB)
- **International telephone input** with country code selection
- **CAPTCHA verification**
- **Date-based form availability** (closes on 2025-12-21)
- **Registration limits** (TIESB/TIESNB: 25 each)
- **GST compliance** handling (Registered/Unregistered)
- **Payment mode selection** (CCAvenue, PayPal, Bank Transfer)

### 1.2 Form Fields Breakdown

#### Step 1: Exhibitor Details
1. **Booth Selection**
   - Booth Type: Startup Booth (default, fixed)
   - Booth Size: Hidden field (value: "Booth / POD")

2. **Sector & Subsector**
   - Sector: Dropdown (20+ options including "Others")
   - Subsector: Dropdown (15+ options including "Other")
   - Other Sector Name: Conditional field (shown when "Other" selected)

3. **Company Information**
   - Name of Exhibitor (Organization Name): Text (max 100 chars)
   - Company Registration Certificate: File upload (PDF, max 2MB)
   - Company Age: Dropdown (1-7 years)
   - Invoice Address: Text
   - City, State, Postal Code: Three separate fields
   - Telephone Number: International input with country code
   - Website: Text with http:// prefix

4. **Tax Information**
   - GST Status: Dropdown (Registered/Unregistered)
   - GST Number: Conditional field (shown when Registered, validated format)
   - PAN Number: Text (max 12 chars, required)

5. **Contact Person Details**
   - Title: Dropdown (Mr., Mrs., Ms., Dr., Prof.)
   - First Name: Text (max 100 chars)
   - Last Name: Text (max 100 chars)
   - Designation: Text (required)
   - Email: Email validation
   - Mobile: International input with country code

6. **Payment Mode**
   - Payment Method: Radio buttons
     - CCAvenue (Credit/Debit/Net Banking/UPI) - Default
     - PayPal (International)
     - Bank Transfer (Hidden/Offline)

7. **Security**
   - CAPTCHA: Image verification

---

## 2. Database Schema Design

### 2.1 New Tables Required

#### `exhibitor_startup_registrations`
```sql
- id (bigint, primary)
- association_name (varchar, nullable) - GCPIT, ELEVATE, etc.
- booth_type (varchar, default: 'Startup Booth')
- booth_size (varchar, default: 'Booth / POD')
- sector (varchar)
- subsector (varchar)
- other_sector_name (varchar, nullable)
- company_name (varchar, 100)
- registration_certificate_path (varchar, nullable)
- company_age_years (integer, 1-7)
- invoice_address (text)
- city (varchar)
- state (varchar)
- postal_code (varchar)
- country_id (foreign key)
- telephone_country_code (varchar)
- telephone_number (varchar, 20)
- website (varchar)
- gst_status (enum: 'Registered', 'Unregistered')
- gst_number (varchar, 15, nullable)
- pan_number (varchar, 12)
- contact_title (varchar)
- contact_first_name (varchar, 100)
- contact_last_name (varchar, 100)
- contact_designation (varchar)
- contact_email (varchar)
- contact_mobile_country_code (varchar)
- contact_mobile (varchar, 10)
- payment_mode (varchar)
- base_price (decimal, 10, 2)
- special_offer_price (decimal, 10, 2)
- gst_amount (decimal, 10, 2)
- processing_charges (decimal, 10, 2)
- total_amount (decimal, 10, 2)
- status (enum: 'pending', 'submitted', 'paid', 'cancelled')
- promocode (varchar, nullable) - For TIESB, TIESNB tracking
- user_id (foreign key, nullable) - If user is logged in
- session_id (varchar, nullable) - For guest registrations
- created_at, updated_at
```

#### `exhibitor_startup_payments`
```sql
- id (bigint, primary)
- registration_id (foreign key)
- payment_gateway (varchar) - CCAvenue, PayPal, Bank Transfer
- transaction_id (varchar, nullable)
- amount (decimal, 10, 2)
- processing_charges (decimal, 10, 2)
- gst_amount (decimal, 10, 2)
- total_amount (decimal, 10, 2)
- payment_status (enum: 'pending', 'processing', 'success', 'failed', 'refunded')
- payment_date (datetime, nullable)
- gateway_response (text, nullable) - JSON
- receipt_path (varchar, nullable)
- created_at, updated_at
```

#### `association_pricing_rules` (Configuration Table)
```sql
- id (bigint, primary)
- association_name (varchar, unique)
- base_price (decimal, 10, 2) - Default: 52000
- special_price (decimal, 10, 2)
- is_complimentary (boolean, default: false)
- max_registrations (integer, nullable) - For TIESB/TIESNB: 25
- is_active (boolean, default: true)
- created_at, updated_at
```

---

## 3. Implementation Structure

### 3.1 Routes
```php
// Public routes (no auth required)
Route::get('/startup-zone/register', [StartupZoneController::class, 'showForm'])
    ->name('startup-zone.register');
Route::post('/startup-zone/register', [StartupZoneController::class, 'store'])
    ->name('startup-zone.store');
Route::get('/startup-zone/preview/{id}', [StartupZoneController::class, 'preview'])
    ->name('startup-zone.preview');
Route::post('/startup-zone/payment', [StartupZoneController::class, 'processPayment'])
    ->name('startup-zone.payment');
Route::get('/startup-zone/success', [StartupZoneController::class, 'success'])
    ->name('startup-zone.success');
Route::get('/startup-zone/captcha', [StartupZoneController::class, 'captcha'])
    ->name('startup-zone.captcha');
```

### 3.2 Controller: `StartupZoneController`
**Methods:**
- `showForm()` - Display registration form
- `store()` - Store registration data (Step 1)
- `preview()` - Show preview page (Step 2)
- `processPayment()` - Handle payment (Step 3)
- `success()` - Payment success page
- `captcha()` - Generate CAPTCHA image
- `checkAvailability()` - Check association limits
- `calculatePricing()` - Calculate prices based on association

### 3.3 Models
- `ExhibitorStartupRegistration`
- `ExhibitorStartupPayment`
- `AssociationPricingRule`

### 3.4 Views Structure
```
resources/views/startup-zone/
├── register.blade.php (Step 1: Main form)
├── preview.blade.php (Step 2: Preview)
├── payment.blade.php (Step 3: Payment processing)
└── success.blade.php (Success page)
```

---

## 4. Form Validation Rules

### 4.1 Server-Side Validation (Laravel)
```php
'booth_type' => 'required|string',
'sector' => 'required|string|max:255',
'subsector' => 'required|string|max:255',
'other_sector_name' => 'required_if:subsector,Other|string|max:255',
'company_name' => 'required|string|max:100',
'registration_certificate' => 'required|file|mimes:pdf|max:2048',
'company_age_years' => 'required|integer|between:1,7',
'invoice_address' => 'required|string|max:500',
'city' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
'state' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
'postal_code' => 'required|string|max:10',
'telephone_country_code' => 'required|string',
'telephone_number' => 'required|string|max:20|regex:/^[0-9-]+$/',
'website' => 'required|string|url',
'gst_status' => 'required|in:Registered,Unregistered',
'gst_number' => 'required_if:gst_status,Registered|string|size:15|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
'pan_number' => 'required|string|size:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
'contact_title' => 'required|in:Mr.,Mrs.,Ms.,Dr.,Prof.',
'contact_first_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
'contact_last_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
'contact_designation' => 'required|string|max:100',
'contact_email' => 'required|email|max:255',
'contact_mobile_country_code' => 'required|string',
'contact_mobile' => 'required|string|size:10|regex:/^[0-9]+$/',
'payment_mode' => 'required|in:Credit Card,Paypal,Bank Transfer',
'captcha' => 'required|string|size:5'
```

### 4.2 Client-Side Validation (JavaScript)
- Real-time field validation
- GST number format validation
- PAN number format validation
- Phone number format validation
- File size and type validation before upload
- Form step navigation validation

---

## 5. Pricing Logic

### 5.1 Association-Based Pricing
```php
Default Price: ₹52,000
Special Prices:
- ELEVATE: ₹30,000 (strikethrough ₹52,000)
- IBioM: ₹30,000
- Presidency: ₹27,000
- TIESB: ₹15,000
- TIESNB: ₹7,500
- beyondBengaluru: ₹15,000
- leap: Complimentary (Free)
- Others: ₹30,000
```

### 5.2 Calculation Formula
```php
Base Price = Association Special Price (or default)
GST (18%) = Base Price × 0.18
Processing Charges:
  - CCAvenue (Indian): Base Price × 3%
  - PayPal (International): Base Price × 9.5%
Total = Base Price + GST + Processing Charges
```

---

## 6. Bootstrap Navigation & UI Components

### 6.1 Multi-Step Wizard (Bootstrap 5)
- Use Bootstrap's `nav-pills` for step indicators
- Progress bar showing completion percentage
- Step validation before allowing next step
- Back/Next buttons with proper state management

### 6.2 Form Components
- Bootstrap form controls (`form-control`, `form-select`)
- Input groups for telephone inputs
- File input with preview
- Radio buttons styled with Bootstrap
- Alert messages for validation errors
- Modal for confirmation dialogs

### 6.3 Responsive Design
- Mobile-first approach
- Collapsible sections on mobile
- Touch-friendly form controls
- Responsive tables for tariff display

---

## 7. Third-Party Integrations

### 7.1 International Telephone Input
- Use `intl-tel-input` library (already in project)
- Country code selection
- Format validation

### 7.2 CAPTCHA
- Use `mews/captcha` package (already installed)
- Image-based CAPTCHA
- Session-based verification

### 7.3 Payment Gateways
- **CCAvenue**: For Indian payments
- **PayPal**: For international payments
- Integration with existing `PaymentGatewayController`

### 7.4 File Upload
- Store in `storage/app/public/startup-zone/certificates/`
- Generate unique filename
- Validate PDF format and size
- Create symlink for public access

---

## 8. Security Considerations

1. **CSRF Protection**: Laravel's built-in CSRF tokens
2. **File Upload Security**: 
   - Validate file type (MIME type check)
   - Scan for malicious content
   - Store outside web root
3. **SQL Injection**: Use Eloquent ORM (parameterized queries)
4. **XSS Protection**: Blade's automatic escaping
5. **Rate Limiting**: Prevent form spam
6. **Session Security**: Secure session configuration
7. **CAPTCHA**: Prevent automated submissions

---

## 9. Business Logic

### 9.1 Association Limits
- Check TIESB/TIESNB registrations count
- Limit: 25 registrations each
- Show "Full" message if limit reached
- Redirect if limit exceeded

### 9.2 Date-Based Availability
- Form available until: 2025-12-21
- After date: Show "Sold Out" message
- Allow waitlist registration via phone

### 9.3 Registration Flow
1. User fills form (Step 1)
2. Preview and confirm (Step 2)
3. Payment processing (Step 3)
4. Success confirmation
5. Email notification sent

---

## 10. Email Notifications

### 10.1 Emails to Send
1. **Registration Confirmation** (to exhibitor)
   - Registration details
   - Payment instructions (if offline)
   - Next steps

2. **Payment Success** (to exhibitor)
   - Payment receipt
   - Registration confirmation
   - Login credentials (if account created)

3. **Admin Notification** (to admin)
   - New registration alert
   - Registration details summary

---

## 11. Testing Checklist

### 11.1 Functional Testing
- [ ] Form field validation (all fields)
- [ ] File upload (PDF, size limit)
- [ ] Association-based pricing calculation
- [ ] CAPTCHA verification
- [ ] Multi-step navigation
- [ ] Payment gateway integration
- [ ] Email notifications
- [ ] Registration limits enforcement
- [ ] Date-based availability check

### 11.2 Edge Cases
- [ ] Invalid file format upload
- [ ] File size exceeding limit
- [ ] Invalid GST/PAN format
- [ ] Association limit reached
- [ ] Form submission after deadline
- [ ] Payment gateway failure
- [ ] Session expiration during form fill

### 11.3 Browser Compatibility
- Chrome, Firefox, Safari, Edge
- Mobile browsers (iOS Safari, Chrome Mobile)
- Responsive design on all screen sizes

---

## 12. Implementation Phases

### Phase 1: Database & Models (Day 1)
- Create migrations
- Create models with relationships
- Seed association pricing rules

### Phase 2: Basic Form Structure (Day 1-2)
- Create routes and controller
- Build Step 1 form view (Bootstrap)
- Implement basic validation

### Phase 3: Form Functionality (Day 2-3)
- Add all form fields
- Implement file upload
- Add international telephone input
- Integrate CAPTCHA
- Client-side validation

### Phase 4: Pricing & Preview (Day 3)
- Implement pricing calculation
- Build preview page (Step 2)
- Association-based logic

### Phase 5: Payment Integration (Day 4)
- Integrate payment gateways
- Build payment page (Step 3)
- Handle payment callbacks

### Phase 6: Testing & Refinement (Day 5)
- Comprehensive testing
- Bug fixes
- UI/UX improvements
- Documentation

---

## 13. Additional Features to Consider

1. **Auto-save**: Save form data to session/localStorage
2. **Progress indicator**: Show completion percentage
3. **Form analytics**: Track form abandonment
4. **Export functionality**: Admin can export registrations
5. **Dashboard integration**: Link to exhibitor dashboard if logged in
6. **Multi-language support**: If required in future

---

## 14. Files to Create/Modify

### New Files:
- `app/Http/Controllers/StartupZoneController.php`
- `app/Models/ExhibitorStartupRegistration.php`
- `app/Models/ExhibitorStartupPayment.php`
- `app/Models/AssociationPricingRule.php`
- `database/migrations/xxxx_create_exhibitor_startup_registrations_table.php`
- `database/migrations/xxxx_create_exhibitor_startup_payments_table.php`
- `database/migrations/xxxx_create_association_pricing_rules_table.php`
- `database/seeders/AssociationPricingRuleSeeder.php`
- `resources/views/startup-zone/register.blade.php`
- `resources/views/startup-zone/preview.blade.php`
- `resources/views/startup-zone/payment.blade.php`
- `resources/views/startup-zone/success.blade.php`
- `app/Mail/StartupZoneRegistrationConfirmation.php`
- `app/Mail/StartupZonePaymentSuccess.php`
- `public/js/startup-zone-form.js`

### Modified Files:
- `routes/web.php` (add new routes)
- `config/captcha.php` (if needed)

---

## 15. Next Steps

1. **Review this plan** with stakeholders
2. **Confirm pricing rules** for all associations
3. **Finalize payment gateway** integration approach
4. **Get design mockups** (if available)
5. **Set up development environment**
6. **Begin Phase 1 implementation**

---

## Questions to Clarify

1. Should users be able to edit their registration after submission?
2. What happens if payment fails? Allow retry?
3. Should we create user accounts automatically after registration?
4. Do we need admin panel to manage these registrations?
5. Should there be a cancellation/refund policy?
6. Do we need integration with existing Application model?
7. What's the exact payment gateway configuration (CCAvenue credentials)?

---

**End of Plan**
