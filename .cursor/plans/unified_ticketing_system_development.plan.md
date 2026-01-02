# Unified Event Ticketing & Association Platform - Development Plan

## Overview
This plan outlines the development of a configurable ticketing system that integrates with the existing BTS Portal (Laravel 11.31) while maintaining separation from the portal users table to avoid scaling issues. The system supports guest-first registration, unlimited delegates, association quotas, promo codes, and comprehensive admin reporting.

**Key Change**: Admin configuration is prioritized - admins must set up events, ticket categories, and catalog before public registration is available.

## Architecture Overview

### System Integration
- **Reuse**: `events` table, `users` table (portal admins/sponsors only), payment gateways (PayPal, CCAvenue), GST API integration
- **New Domain**: Separate `ticket_*` tables for all attendee data
- **Authentication**: Guest-first with optional OTP/magic links; portal users remain for admin/sponsor access

### Route Structure
- **New File**: `routes/tickets.php` - All ticket registration routes (public and guest)
- **Existing**: `routes/web.php` - Admin ticket management routes (protected)
- **Existing**: `routes/api.php` - API endpoints if needed

### Admin Setup Workflow
```
1. Admin creates/selects Event (reuse existing events table)
2. Admin configures Event Settings (auth policy, selection mode, email routing)
3. Admin defines Event Days
4. Admin creates Registration Categories (Delegate/Visitor/VIP/Student)
5. Admin creates Ticket Categories (Delegate/VIP/Workshop)
6. Admin creates Ticket Subcategories (Member/Non-member/Student)
7. Admin creates Ticket Types (with pricing, capacity, sale windows)
8. Admin maps Ticket Types to Event Days
9. Admin sets up Associations (optional)
10. Admin creates Promo Codes (optional)
11. System ready for public registration
```

## Phase 1: Database Schema & Models

### 1.1 Core Identity Tables
**Files**: `database/migrations/2025_XX_XX_create_ticket_identity_tables.php`

Create migrations for:
- `ticket_contacts` - High-volume attendee identity (email, phone, verified flags)
- `ticket_accounts` - Optional login layer (1:1 with contact)
- `ticket_otp_requests` - Enhanced OTP with throttling (channel, otp_hash, expires_at, attempts, IP tracking)
- `ticket_magic_links` - Guest access tokens (token, purpose, expires_at)

**Models**: 
- `app/Models/Ticket/TicketContact.php`
- `app/Models/Ticket/TicketAccount.php`
- `app/Models/Ticket/TicketOtpRequest.php`
- `app/Models/Ticket/TicketMagicLink.php`

### 1.2 Event Configuration Tables (PRIORITY - Admin Setup)
**Files**: `database/migrations/2025_XX_XX_create_ticket_event_config_tables.php`

- `ticket_events_config` - Event-level behavior (auth_policy, selection_mode, email_cc_json, receipt_pattern)
  - `event_id` (FK to events.id)
  - `auth_policy` ENUM('guest', 'otp_required', 'login_required')
  - `selection_mode` ENUM('same_ticket', 'per_delegate')
  - `allow_subcategory` BOOLEAN
  - `allow_day_select` BOOLEAN
  - `email_cc_json` JSON (array of email addresses)
  - `receipt_pattern` VARCHAR (e.g., "TKT-{event}-{year}-{seq}")
  - `is_active` BOOLEAN (admin can disable registration)
  
- `event_days` - Explicit event days
  - `event_id` (FK to events.id)
  - `label` VARCHAR (e.g., "Day 1", "Day 2", "VIP Day")
  - `date` DATE
  - `sort_order` INT
  
- `ticket_registration_categories` - Registration categories (separate from ticket type)
  - `event_id` (FK to events.id)
  - `name` VARCHAR (Delegate/Visitor/VIP/Student)
  - `description` TEXT
  - `is_active` BOOLEAN
  - `sort_order` INT
  
- `ticket_categories` - Ticket grouping
  - `event_id` (FK to events.id)
  - `name` VARCHAR (Delegate/VIP/Workshop)
  - `description` TEXT
  - `sort_order` INT
  
- `ticket_subcategories` - Sub grouping
  - `category_id` (FK to ticket_categories.id)
  - `name` VARCHAR (Member/Non-member/Student)
  - `description` TEXT
  - `sort_order` INT
  
- `ticket_types` - Sellable ticket types
  - `event_id` (FK to events.id)
  - `category_id` (FK to ticket_categories.id)
  - `subcategory_id` (FK to ticket_subcategories.id, nullable)
  - `name` VARCHAR
  - `description` TEXT
  - `price` DECIMAL(10,2)
  - `capacity` INT (nullable for unlimited)
  - `sale_start_at` DATETIME
  - `sale_end_at` DATETIME
  - `is_active` BOOLEAN
  - `sort_order` INT
  
- `ticket_type_day_access` - Ticket type → allowed days mapping
  - `ticket_type_id` (FK to ticket_types.id)
  - `event_day_id` (FK to event_days.id)
  - Unique constraint on (ticket_type_id, event_day_id)
  
- `ticket_inventory` - Atomic stock control
  - `ticket_type_id` (FK to ticket_types.id, unique)
  - `reserved_qty` INT DEFAULT 0
  - `sold_qty` INT DEFAULT 0
  - `available_qty` INT (calculated: capacity - reserved_qty - sold_qty)
  
- `ticket_category_ticket_rules` - Allowed combinations validation
  - `registration_category_id` (FK to ticket_registration_categories.id)
  - `ticket_type_id` (FK to ticket_types.id)
  - `subcategory_id` (FK to ticket_subcategories.id, nullable)
  - `allowed_days_json` JSON (array of event_day_ids)

**Models**: Create corresponding Eloquent models in `app/Models/Ticket/`
- `TicketEventConfig.php`
- `EventDay.php`
- `TicketRegistrationCategory.php`
- `TicketCategory.php`
- `TicketSubcategory.php`
- `TicketType.php`
- `TicketTypeDayAccess.php`
- `TicketInventory.php`
- `TicketCategoryTicketRule.php`

### 1.3 Registration & Tickets Tables
**Files**: `database/migrations/2025_XX_XX_create_ticket_registration_tables.php`

- `ticket_registrations` - Company header + UTM tracking
- `ticket_delegates` - Unlimited delegates per registration
- `ticket_delegate_assignments` - Delegate → ticket selection snapshot
- `tickets` - Issued tickets (one per delegate)

**Models**: `app/Models/Ticket/TicketRegistration.php`, `TicketDelegate.php`, `TicketDelegateAssignment.php`, `Ticket.php`

### 1.4 Commerce Tables
**Files**: `database/migrations/2025_XX_XX_create_ticket_commerce_tables.php`

- `ticket_orders` - Checkout orders
- `ticket_order_items` - Order line items
- `ticket_payments` - Payment records (reuse existing payment gateway integration)
- `ticket_payment_events` - Webhook audit logs
- `ticket_receipts` - Receipt generation (provisional + acknowledgment)

**Models**: `app/Models/Ticket/TicketOrder.php`, `TicketOrderItem.php`, `TicketPayment.php`, `TicketPaymentEvent.php`, `TicketReceipt.php`

### 1.5 Association & Promo Tables
**Files**: `database/migrations/2025_XX_XX_create_ticket_association_tables.php`

- `ticket_associations` - Association/sponsor profiles
- `ticket_association_admins` - Portal user → association mapping
- `ticket_association_allocations` - Quota allocations (atomic used_qty tracking)
- `ticket_association_links` - Shareable association links
- `ticket_promo_codes` - Admin promo rules
- `ticket_promo_redemptions` - Promo audit
- `ticket_admin_invites` - Admin paid invite links
- `ticket_bulk_import_jobs` - Import job tracking
- `ticket_bulk_import_rows` - Import row errors
- `ticket_upgrades` - Upgrade history

**Models**: `app/Models/Ticket/TicketAssociation.php`, `TicketPromoCode.php`, etc.

## Phase 2: Admin Controllers & Setup Interface (PRIORITY)

### 2.1 Admin Ticket Configuration Controller
**File**: `app/Http/Controllers/Ticket/AdminTicketConfigController.php`

**Routes** (in `routes/web.php`, protected by existing `Auth` middleware):
```php
Route::middleware(['auth', Auth::class])->prefix('admin/tickets')->name('admin.tickets.')->group(function () {
    // Event Selection/Configuration
    Route::get('/events', [AdminTicketConfigController::class, 'events'])->name('events');
    Route::get('/events/{eventId}/setup', [AdminTicketConfigController::class, 'setup'])->name('events.setup');
    Route::post('/events/{eventId}/config', [AdminTicketConfigController::class, 'updateConfig'])->name('events.config.update');
    
    // Event Days Management
    Route::get('/events/{eventId}/days', [AdminTicketConfigController::class, 'days'])->name('events.days');
    Route::post('/events/{eventId}/days', [AdminTicketConfigController::class, 'storeDay'])->name('events.days.store');
    Route::put('/events/{eventId}/days/{dayId}', [AdminTicketConfigController::class, 'updateDay'])->name('events.days.update');
    Route::delete('/events/{eventId}/days/{dayId}', [AdminTicketConfigController::class, 'deleteDay'])->name('events.days.delete');
    
    // Registration Categories
    Route::get('/events/{eventId}/registration-categories', [AdminTicketConfigController::class, 'registrationCategories'])->name('events.registration-categories');
    Route::post('/events/{eventId}/registration-categories', [AdminTicketConfigController::class, 'storeRegistrationCategory'])->name('events.registration-categories.store');
    Route::put('/events/{eventId}/registration-categories/{categoryId}', [AdminTicketConfigController::class, 'updateRegistrationCategory'])->name('events.registration-categories.update');
    Route::delete('/events/{eventId}/registration-categories/{categoryId}', [AdminTicketConfigController::class, 'deleteRegistrationCategory'])->name('events.registration-categories.delete');
    
    // Ticket Categories
    Route::get('/events/{eventId}/categories', [AdminTicketConfigController::class, 'categories'])->name('events.categories');
    Route::post('/events/{eventId}/categories', [AdminTicketConfigController::class, 'storeCategory'])->name('events.categories.store');
    Route::put('/events/{eventId}/categories/{categoryId}', [AdminTicketConfigController::class, 'updateCategory'])->name('events.categories.update');
    Route::delete('/events/{eventId}/categories/{categoryId}', [AdminTicketConfigController::class, 'deleteCategory'])->name('events.categories.delete');
    
    // Ticket Subcategories
    Route::get('/events/{eventId}/categories/{categoryId}/subcategories', [AdminTicketConfigController::class, 'subcategories'])->name('events.subcategories');
    Route::post('/events/{eventId}/categories/{categoryId}/subcategories', [AdminTicketConfigController::class, 'storeSubcategory'])->name('events.subcategories.store');
    Route::put('/events/{eventId}/subcategories/{subcategoryId}', [AdminTicketConfigController::class, 'updateSubcategory'])->name('events.subcategories.update');
    Route::delete('/events/{eventId}/subcategories/{subcategoryId}', [AdminTicketConfigController::class, 'deleteSubcategory'])->name('events.subcategories.delete');
    
    // Ticket Types
    Route::get('/events/{eventId}/ticket-types', [AdminTicketConfigController::class, 'ticketTypes'])->name('events.ticket-types');
    Route::get('/events/{eventId}/ticket-types/create', [AdminTicketConfigController::class, 'createTicketType'])->name('events.ticket-types.create');
    Route::post('/events/{eventId}/ticket-types', [AdminTicketConfigController::class, 'storeTicketType'])->name('events.ticket-types.store');
    Route::get('/events/{eventId}/ticket-types/{ticketTypeId}/edit', [AdminTicketConfigController::class, 'editTicketType'])->name('events.ticket-types.edit');
    Route::put('/events/{eventId}/ticket-types/{ticketTypeId}', [AdminTicketConfigController::class, 'updateTicketType'])->name('events.ticket-types.update');
    Route::delete('/events/{eventId}/ticket-types/{ticketTypeId}', [AdminTicketConfigController::class, 'deleteTicketType'])->name('events.ticket-types.delete');
    
    // Ticket Type Day Access Mapping
    Route::post('/events/{eventId}/ticket-types/{ticketTypeId}/day-access', [AdminTicketConfigController::class, 'updateDayAccess'])->name('events.ticket-types.day-access');
    
    // Ticket Rules (Registration Category → Ticket Type mapping)
    Route::get('/events/{eventId}/rules', [AdminTicketConfigController::class, 'rules'])->name('events.rules');
    Route::post('/events/{eventId}/rules', [AdminTicketConfigController::class, 'storeRule'])->name('events.rules.store');
    Route::delete('/events/{eventId}/rules/{ruleId}', [AdminTicketConfigController::class, 'deleteRule'])->name('events.rules.delete');
});
```

**Features**:
- Multi-step setup wizard for event configuration
- Validation to ensure all required setup is complete before enabling registration
- Bulk operations for day access mapping
- Visual hierarchy display (Category → Subcategory → Ticket Type)

### 2.2 Admin Ticket Management Controller
**File**: `app/Http/Controllers/Ticket/AdminTicketController.php`

**Routes** (in `routes/web.php`):
```php
Route::middleware(['auth', Auth::class])->prefix('admin/tickets')->name('admin.tickets.')->group(function () {
    // Registrations Management
    Route::get('/registrations', [AdminTicketController::class, 'registrations'])->name('registrations');
    Route::get('/registrations/{registrationId}', [AdminTicketController::class, 'showRegistration'])->name('registrations.show');
    
    // Orders Management
    Route::get('/orders', [AdminTicketController::class, 'orders'])->name('orders');
    Route::get('/orders/{orderId}', [AdminTicketController::class, 'showOrder'])->name('orders.show');
    
    // Reports & BI Dashboard
    Route::get('/reports', [AdminTicketController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [AdminTicketController::class, 'export'])->name('reports.export');
    
    // Associations Management
    Route::get('/associations', [AdminTicketController::class, 'associations'])->name('associations');
    Route::post('/associations', [AdminTicketController::class, 'storeAssociation'])->name('associations.store');
    Route::post('/associations/{associationId}/allocations', [AdminTicketController::class, 'createAllocation'])->name('associations.allocations.create');
    
    // Promo Codes Management
    Route::get('/promo-codes', [AdminTicketController::class, 'promoCodes'])->name('promo-codes');
    Route::post('/promo-codes', [AdminTicketController::class, 'storePromoCode'])->name('promo-codes.store');
    Route::put('/promo-codes/{promoCodeId}', [AdminTicketController::class, 'updatePromoCode'])->name('promo-codes.update');
    
    // Bulk Import
    Route::get('/bulk-import', [AdminTicketController::class, 'bulkImport'])->name('bulk-import');
    Route::post('/bulk-import', [AdminTicketController::class, 'processBulkImport'])->name('bulk-import.process');
});
```

### 2.3 Admin Views (Setup Interface)
**Directory**: `resources/views/tickets/admin/`

**Setup Wizard Views**:
- `events/index.blade.php` - List events, select/create event for ticketing
- `events/setup.blade.php` - Main setup page with tabs/steps
- `events/config.blade.php` - Event configuration form (auth policy, selection mode, email routing)
- `events/days.blade.php` - Event days management
- `events/registration-categories.blade.php` - Registration categories CRUD
- `events/categories.blade.php` - Ticket categories CRUD
- `events/subcategories.blade.php` - Ticket subcategories CRUD
- `events/ticket-types/index.blade.php` - List ticket types
- `events/ticket-types/create.blade.php` - Create ticket type form
- `events/ticket-types/edit.blade.php` - Edit ticket type form
- `events/rules.blade.php` - Ticket rules configuration

**Management Views**:
- `registrations/index.blade.php` - Registration list with filters
- `registrations/show.blade.php` - Registration details
- `orders/index.blade.php` - Order list
- `orders/show.blade.php` - Order details
- `reports/index.blade.php` - BI dashboard
- `associations/index.blade.php` - Associations list
- `promo-codes/index.blade.php` - Promo codes list

## Phase 3: Core Services & Business Logic

### 3.1 Ticket Catalog Service
**File**: `app/Services/TicketCatalogService.php`

- Load event configuration
- Validate ticket availability (check `ticket_inventory`)
- Apply day access rules
- Calculate pricing (member/non-member, subcategory)
- Enforce sale windows
- Check if event setup is complete (validation method)

### 3.2 Enhanced OTP Service
**File**: `app/Services/TicketOtpService.php`

- Extend existing `OTPController` pattern
- Add throttling (rate limit by contact + IP)
- Support email and SMS channels
- Hash OTPs in database (use `ticket_otp_requests`)
- Integration with existing `App\Mail\SendOTP` mailable

### 3.3 Magic Link Service
**File**: `app/Services/TicketMagicLinkService.php`

- Generate secure tokens for guest access
- Support "manage-booking" purpose
- OTP fallback mechanism
- Token expiration and one-time use logic

### 3.4 Association Quota Service
**File**: `app/Services/TicketAssociationService.php`

- Atomic quota reservation (DB transactions with row locks)
- Generate unique association links
- Track quota usage
- Prevent overuse with transaction-based enforcement

### 3.5 Promo Code Service
**File**: `app/Services/TicketPromoService.php`

- Validate promo codes (validity, caps, rules)
- Calculate discounts
- Enforce per-contact and total caps
- Stack rules validation

### 3.6 Receipt Service
**File**: `app/Services/TicketReceiptService.php`

- Generate provisional receipts (optional, unpaid)
- Generate acknowledgment receipts (paid/free/invite)
- Configurable receipt numbering pattern (from event config)
- PDF generation (reuse DomPDF from existing system)
- Email delivery

### 3.7 Payment Integration Service
**File**: `app/Services/TicketPaymentService.php`

- Integrate with existing `PaymentGatewayController` (CCAvenue)
- Integrate with existing `PayPalController`
- Webhook handling with idempotency
- Payment status updates
- Support offline/on-spot payments via admin invites

## Phase 4: Public Registration Controllers & Routes

### 4.1 Public Registration Controller
**File**: `app/Http/Controllers/Ticket/PublicTicketController.php`

**Routes** (NEW FILE: `routes/tickets.php`):
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ticket\PublicTicketController;
use App\Http\Controllers\Ticket\GuestTicketController;
use App\Http\Controllers\Ticket\TicketPaymentController;

// Public ticket discovery and registration
Route::get('/tickets/{eventSlug}', [PublicTicketController::class, 'discover'])->name('tickets.discover');
Route::get('/tickets/{eventSlug}/register', [PublicTicketController::class, 'register'])->name('tickets.register');
Route::post('/tickets/{eventSlug}/register', [PublicTicketController::class, 'store'])->name('tickets.store');
Route::get('/tickets/{eventSlug}/register/{token}', [PublicTicketController::class, 'continueRegistration'])->name('tickets.continue');

// Guest management (magic links)
Route::get('/manage-booking/{token}', [GuestTicketController::class, 'manage'])->name('tickets.manage');
Route::post('/manage-booking/request-link', [GuestTicketController::class, 'requestLink'])->name('tickets.request-link');
Route::post('/manage-booking/verify-otp', [GuestTicketController::class, 'verifyOtp'])->name('tickets.verify-otp');

// Payment
Route::get('/ticket-payment/{orderId}', [TicketPaymentController::class, 'show'])->name('tickets.payment');
Route::post('/ticket-payment/{orderId}/process', [TicketPaymentController::class, 'process'])->name('tickets.payment.process');
```

**Register routes in `bootstrap/app.php` or `routes/web.php`**:
```php
// In routes/web.php, add:
require __DIR__.'/tickets.php';
```

**Features**:
- Single-page form with dynamic delegate sections
- GST API integration (reuse `GstLookup` model)
- UTM parameter capture
- Association link detection
- Real-time ticket availability checks
- Validation that event setup is complete

### 4.2 Guest Management Controller
**File**: `app/Http/Controllers/Ticket/GuestTicketController.php`

(Already defined in routes above)

### 4.3 Payment Controller
**File**: `app/Http/Controllers/Ticket/TicketPaymentController.php`

(Already defined in routes above)

### 4.4 Public Views
**Directory**: `resources/views/tickets/public/`

- `discover.blade.php` - Ticket catalog page (shows available ticket types)
- `register.blade.php` - Single-page registration form
  - Dynamic delegate sections (JavaScript)
  - GST lookup integration (AJAX)
  - Real-time price calculation
  - Association field (conditional display)
  - Validation messages if event not fully configured

### 4.5 Guest Management Views
**Directory**: `resources/views/tickets/guest/`

- `manage.blade.php` - Booking management page
- `request-access.blade.php` - Magic link request form

## Phase 5: Association Controllers & Views

### 5.1 Association Controller
**File**: `app/Http/Controllers/Ticket/AssociationController.php`

**Routes** (in `routes/web.php`, for portal users with association access):
```php
Route::middleware(['auth'])->prefix('association')->name('association.')->group(function () {
    Route::get('/dashboard', [AssociationController::class, 'dashboard'])->name('dashboard');
    Route::get('/quota', [AssociationController::class, 'quota'])->name('quota');
    Route::get('/participants', [AssociationController::class, 'participants'])->name('participants');
    Route::get('/export', [AssociationController::class, 'export'])->name('export');
});
```

### 5.2 Association Views
**Directory**: `resources/views/tickets/association/`

- `dashboard.blade.php` - Association dashboard
- `participants.blade.php` - Participant list

## Phase 6: Email Templates

**Directory**: `resources/views/emails/tickets/`

- `registration-success.blade.php` - Registration confirmation
- `payment-success.blade.php` - Payment confirmation
- `sponsor-free-confirmation.blade.php` - Association free ticket confirmation
- `invite-link-usage.blade.php` - Admin invite link usage
- `upgrade-confirmation.blade.php` - Upgrade confirmation
- `receipt-acknowledgment.blade.php` - Receipt email

**Mail Classes**: `app/Mail/Ticket/` (extend existing mail pattern)

## Phase 7: Testing & Validation

### 7.1 Unit Tests
- Service layer tests (quota enforcement, promo validation)
- Model relationship tests
- Event setup validation tests

### 7.2 Feature Tests
- Admin setup workflow
- Registration flow
- Payment processing
- Association quota usage
- Promo code application

### 7.3 Integration Tests
- Payment gateway webhooks
- GST API integration
- Email delivery

## Improvements & Enhancements

### 1. Admin Setup Validation
- **Critical**: Validate that all required setup steps are complete before enabling public registration
- Show setup progress indicator
- Block public registration if event config is incomplete
- Provide clear error messages to admins about missing configuration

### 2. Enhanced OTP System
- **Current**: Basic OTP in `otps` table
- **Improvement**: 
  - Move to `ticket_otp_requests` with hashing
  - Add IP-based throttling
  - Support SMS channel (future)
  - Rate limiting per contact

### 3. Database Transactions
- **Critical**: Use DB transactions for:
  - Quota reservation (prevent overuse)
  - Inventory updates (prevent overselling)
  - Order creation (atomic operations)

### 4. Caching Strategy
- Cache ticket catalog (event config, ticket types)
- Cache GST lookups (already implemented in `GstLookup`)
- Cache association quota status
- Invalidate cache when admin updates configuration

### 5. Queue Jobs
- Email sending (already using queues)
- Receipt generation (heavy PDFs)
- Bulk import processing

### 6. Audit Logging
- Track all quota usage
- Log promo code redemptions
- Log admin actions (extend existing `AdminActionLog`)
- Log configuration changes

### 7. Security Enhancements
- CSRF protection (Laravel default)
- Rate limiting on public endpoints
- Input validation (Laravel validation)
- SQL injection prevention (Eloquent ORM)
- Admin-only access to configuration routes

## Integration Points

### Existing Systems to Reuse
1. **Payment Gateways**: `PaymentGatewayController`, `PayPalController`
2. **GST API**: `GstLookup` model and API integration
3. **Email System**: Existing mail infrastructure
4. **Events Table**: `Events` model (reuse for event selection)
5. **Portal Users**: `User` model for admin/sponsor access
6. **Geo Data**: `GeoController` for countries/states/cities
7. **PDF Generation**: DomPDF (already in use)
8. **Excel Import/Export**: Maatwebsite Excel

### New Integrations Needed
1. **SMS Gateway** (optional, for OTP via SMS)
2. **QR Code Generation** (for Phase 2 check-in)
3. **Analytics Integration** (for BI dashboard)

## Migration Strategy

1. **Phase 1**: Create all database tables (non-breaking)
2. **Phase 2**: Build admin setup interface (PRIORITY)
3. **Phase 3**: Build services and core logic
4. **Phase 4**: Build public registration flow (only after admin setup is complete)
5. **Phase 5**: Build association dashboards
6. **Phase 6**: Testing and refinement
7. **Phase 7**: Production deployment

## Configuration Management

Store event-specific configurations in `ticket_events_config`:
- Auth policy (guest/OTP/login required)
- Selection mode (same ticket vs per-delegate)
- Email CC routing (JSON array)
- Receipt numbering pattern
- Sale windows per ticket type
- `is_active` flag to enable/disable registration

## Performance Considerations

1. **Indexing**: Add indexes on frequently queried columns (email, phone, event_id, order_no)
2. **Pagination**: Use Laravel pagination for large lists
3. **Eager Loading**: Use Eloquent eager loading to prevent N+1 queries
4. **Database Optimization**: Consider read replicas for reporting queries
5. **Cache Event Config**: Cache event configuration to reduce DB queries

## Future Enhancements (Phase 2+)

1. Check-in system with QR codes
2. Scanner app (offline check-in)
3. E-invoice integration
4. CRM automations
5. Marketing journey integration

