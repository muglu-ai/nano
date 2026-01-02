# Ticket System Development Phases

## Overview
This document outlines the step-wise development phases for the Unified Event Ticketing & Association Platform.

---

## Phase 1: Database Setup & Models ✅

### 1.1 Run Migrations
```bash
php artisan migrate
```

**Migrations to run (in order):**
1. `2026_01_01_000001_create_ticket_identity_tables.php`
2. `2026_01_01_000002_create_ticket_event_config_tables.php`
3. `2026_01_01_000003_create_ticket_registration_tables.php`
4. `2026_01_01_000004_create_ticket_commerce_tables.php`
5. `2026_01_01_000005_create_ticket_association_tables.php`
6. `2026_01_01_000006_create_ticket_early_bird_reminders_table.php`

### 1.2 Verify Models
- All models are created in `app/Models/Ticket/`
- Relationships are properly defined
- Test model relationships with tinker

**Deliverable:** Database schema ready, all models functional

---

## Phase 2: Admin Setup Interface (PRIORITY)

### 2.1 Create Admin Ticket Configuration Controller
**File:** `app/Http/Controllers/Ticket/AdminTicketConfigController.php`

**Features:**
- Event selection/creation
- Event configuration (auth policy, selection mode, email routing)
- Event days management (CRUD)
- Registration categories management (CRUD)
- Ticket categories management (CRUD)
- Ticket subcategories management (CRUD)
- Ticket types management (CRUD with early bird pricing)
- Ticket type day access mapping
- Ticket rules configuration

**Routes:** Add to `routes/web.php`
```php
Route::middleware(['auth', Auth::class])->prefix('admin/tickets')->name('admin.tickets.')->group(function () {
    // Event Selection/Configuration
    Route::get('/events', [AdminTicketConfigController::class, 'events'])->name('events');
    Route::get('/events/{eventId}/setup', [AdminTicketConfigController::class, 'setup'])->name('events.setup');
    Route::post('/events/{eventId}/config', [AdminTicketConfigController::class, 'updateConfig'])->name('events.config.update');
    
    // Event Days, Categories, Ticket Types, Rules...
});
```

### 2.2 Create Admin Setup Views
**Directory:** `resources/views/tickets/admin/events/`

**Views to create:**
- `index.blade.php` - Event list/selection
- `setup.blade.php` - Main setup page with tabs
- `config.blade.php` - Event configuration form
- `days.blade.php` - Event days management
- `registration-categories.blade.php` - Registration categories CRUD
- `categories.blade.php` - Ticket categories CRUD
- `subcategories.blade.php` - Ticket subcategories CRUD
- `ticket-types/index.blade.php` - Ticket types list
- `ticket-types/create.blade.php` - Create ticket type
- `ticket-types/edit.blade.php` - Edit ticket type
- `rules.blade.php` - Ticket rules configuration

### 2.3 Create Ticket Catalog Service
**File:** `app/Services/TicketCatalogService.php`

**Methods:**
- `getEventConfig($eventId)` - Load event configuration
- `validateTicketAvailability($ticketTypeId, $quantity)` - Check availability
- `getCurrentPrice($ticketTypeId)` - Get current price (early bird or regular)
- `isEventSetupComplete($eventId)` - Validate setup completion
- `getTicketTypesForEvent($eventId)` - Get available ticket types

### 2.4 Setup Validation
- Add validation to ensure all required setup is complete
- Show setup progress indicator
- Block public registration if incomplete

**Deliverable:** Admin can fully configure events, ticket catalog, and rules

---

## Phase 3: Core Services

### 3.1 Enhanced OTP Service
**File:** `app/Services/TicketOtpService.php`

**Features:**
- Generate and hash OTPs
- Rate limiting (by contact + IP)
- Email/SMS channel support
- Throttling logic
- Integration with existing mail system

### 3.2 Magic Link Service
**File:** `app/Services/TicketMagicLinkService.php`

**Features:**
- Generate secure tokens
- Token expiration
- One-time use logic
- OTP fallback mechanism

### 3.3 Association Quota Service
**File:** `app/Services/TicketAssociationService.php`

**Features:**
- Atomic quota reservation
- Generate unique association links
- Track quota usage
- Prevent overuse with transactions

### 3.4 Promo Code Service
**File:** `app/Services/TicketPromoService.php`

**Features:**
- Validate promo codes
- Calculate discounts
- Enforce caps (per-contact, total)
- Stack rules validation

### 3.5 Receipt Service
**File:** `app/Services/TicketReceiptService.php`

**Features:**
- Generate provisional receipts
- Generate acknowledgment receipts
- PDF generation (DomPDF)
- Email delivery
- Configurable receipt numbering

### 3.6 Payment Integration Service
**File:** `app/Services/TicketPaymentService.php`

**Features:**
- Integrate with CCAvenue
- Integrate with PayPal
- Webhook handling with idempotency
- Payment status updates
- Store PG requests/responses

**Deliverable:** All core services functional

---

## Phase 4: Public Registration Flow

### 4.1 Create Routes File
**File:** `routes/tickets.php`

**Routes:**
- `GET /tickets/{eventSlug}` - Ticket discovery
- `GET /tickets/{eventSlug}/register` - Registration form
- `POST /tickets/{eventSlug}/register` - Submit registration
- `GET /tickets/{eventSlug}/register/{token}` - Continue registration

**Register in `routes/web.php`:**
```php
require __DIR__.'/tickets.php';
```

### 4.2 Create Public Ticket Controller
**File:** `app/Http/Controllers/Ticket/PublicTicketController.php`

**Features:**
- Ticket discovery page
- Single-page registration form
- Dynamic delegate sections
- GST API integration
- UTM parameter capture
- Association link detection
- Real-time availability checks
- Validation that event setup is complete

### 4.3 Create Public Views
**Directory:** `resources/views/tickets/public/`

**Views:**
- `discover.blade.php` - Ticket catalog page
- `register.blade.php` - Single-page registration form

**JavaScript Features:**
- Dynamic delegate section addition/removal
- Real-time price calculation
- GST lookup AJAX
- Form validation

### 4.4 Guest Management Controller
**File:** `app/Http/Controllers/Ticket/GuestTicketController.php`

**Routes (in `routes/tickets.php`):**
- `GET /manage-booking/{token}` - Magic link access
- `POST /manage-booking/request-link` - Request magic link
- `POST /manage-booking/verify-otp` - OTP verification

**Deliverable:** Public can register for tickets

---

## Phase 5: Payment Processing

### 5.1 Create Ticket Payment Controller
**File:** `app/Http/Controllers/Ticket/TicketPaymentController.php`

**Routes (in `routes/tickets.php`):**
- `GET /ticket-payment/{orderId}` - Payment page
- `POST /ticket-payment/{orderId}/process` - Process payment
- `POST /ticket-payment/webhook` - Payment webhook

### 5.2 Payment Integration
- Integrate with existing `PaymentGatewayController` (CCAvenue)
- Integrate with existing `PayPalController`
- Store all PG requests/responses
- Handle webhooks with idempotency
- Support multiple orders per payment

### 5.3 Payment Views
**Directory:** `resources/views/tickets/payment/`

**Views:**
- `show.blade.php` - Payment page
- `success.blade.php` - Payment success
- `failure.blade.php` - Payment failure

**Deliverable:** Payment processing functional

---

## Phase 6: Admin Management Interface

### 6.1 Create Admin Ticket Controller
**File:** `app/Http/Controllers/Ticket/AdminTicketController.php`

**Routes (in `routes/web.php`):**
- `GET /admin/tickets/registrations` - List registrations
- `GET /admin/tickets/registrations/{id}` - View registration
- `GET /admin/tickets/orders` - List orders
- `GET /admin/tickets/orders/{id}` - View order
- `GET /admin/tickets/reports` - BI dashboard
- `GET /admin/tickets/reports/export` - Export reports
- `GET /admin/tickets/associations` - Associations list
- `POST /admin/tickets/associations` - Create association
- `POST /admin/tickets/associations/{id}/allocations` - Create allocation
- `GET /admin/tickets/promo-codes` - Promo codes list
- `POST /admin/tickets/promo-codes` - Create promo code
- `GET /admin/tickets/bulk-import` - Bulk import form
- `POST /admin/tickets/bulk-import` - Process import

### 6.2 Create Admin Management Views
**Directory:** `resources/views/tickets/admin/`

**Views:**
- `registrations/index.blade.php` - Registration list
- `registrations/show.blade.php` - Registration details
- `orders/index.blade.php` - Order list
- `orders/show.blade.php` - Order details
- `reports/index.blade.php` - BI dashboard
- `associations/index.blade.php` - Associations list
- `promo-codes/index.blade.php` - Promo codes list
- `bulk-import/index.blade.php` - Bulk import form

### 6.3 BI Dashboard Features
- Revenue analytics (paid vs free vs sponsor-free)
- Ticket performance (category/subcategory/type)
- Source analytics (UTM breakdown)
- Promo performance
- Association utilization
- CSV/Excel exports

**Deliverable:** Admin can manage registrations, view reports, manage associations

---

## Phase 7: Association Dashboard

### 7.1 Create Association Controller
**File:** `app/Http/Controllers/Ticket/AssociationController.php`

**Routes (in `routes/web.php`):**
- `GET /association/dashboard` - Association dashboard
- `GET /association/quota` - Quota usage
- `GET /association/participants` - Participant list
- `GET /association/export` - CSV/Excel export

### 7.2 Create Association Views
**Directory:** `resources/views/tickets/association/`

**Views:**
- `dashboard.blade.php` - Association dashboard
- `participants.blade.php` - Participant list

**Deliverable:** Associations can view quota usage and participants

---

## Phase 8: Email Templates & Notifications

### 8.1 Create Email Templates
**Directory:** `resources/views/emails/tickets/`

**Templates:**
- `registration-success.blade.php`
- `payment-success.blade.php`
- `sponsor-free-confirmation.blade.php`
- `invite-link-usage.blade.php`
- `upgrade-confirmation.blade.php`
- `receipt-acknowledgment.blade.php`
- `early-bird-reminder.blade.php` (for sales team)

### 8.2 Create Mail Classes
**Directory:** `app/Mail/Ticket/`

**Classes:**
- `RegistrationSuccessMail.php`
- `PaymentSuccessMail.php`
- `SponsorFreeConfirmationMail.php`
- `EarlyBirdReminderMail.php`
- etc.

**Deliverable:** All email notifications functional

---

## Phase 9: Early Bird Reminder System

### 9.1 Create Reminder Command
**File:** `app/Console/Commands/CheckEarlyBirdReminders.php`

**Features:**
- Check ticket types with early bird ending in 7 days
- Send reminders to sales team
- Track reminder history

### 9.2 Schedule Command
**File:** `app/Console/Kernel.php`

```php
$schedule->command('tickets:check-early-bird-reminders')
    ->daily()
    ->at('09:00');
```

**Deliverable:** Automated early bird reminders

---

## Phase 10: Bulk Import

### 10.1 Create Bulk Import Controller
**File:** `app/Http/Controllers/Ticket/BulkImportController.php`

**Features:**
- CSV/Excel upload
- Template validation
- Row-by-row processing
- Error tracking
- Success/failure reporting

### 10.2 Create Import Job
**File:** `app/Jobs/ProcessTicketBulkImport.php`

**Features:**
- Queue-based processing
- Progress tracking
- Error handling

**Deliverable:** Bulk import functional

---

## Phase 11: Testing & Refinement

### 11.1 Unit Tests
- Service layer tests
- Model relationship tests
- Pricing calculation tests

### 11.2 Feature Tests
- Admin setup workflow
- Registration flow
- Payment processing
- Association quota usage
- Promo code application

### 11.3 Integration Tests
- Payment gateway webhooks
- GST API integration
- Email delivery

**Deliverable:** Comprehensive test coverage

---

## Phase 12: Performance Optimization

### 12.1 Caching
- Cache ticket catalog
- Cache event configuration
- Cache GST lookups
- Cache association quota status

### 12.2 Database Optimization
- Add missing indexes
- Optimize queries
- Use eager loading

### 12.3 Queue Jobs
- Email sending
- Receipt generation
- Bulk import processing

**Deliverable:** Optimized performance

---

## Phase 13: Documentation & Deployment

### 13.1 API Documentation
- Swagger/OpenAPI docs (if needed)

### 13.2 User Documentation
- Admin setup guide
- Association dashboard guide
- Public registration guide

### 13.3 Deployment Checklist
- Environment variables
- Database migrations
- Queue workers
- Cron jobs
- Cache clearing

**Deliverable:** System ready for production

---

## Quick Start Checklist

### Immediate Next Steps (Phase 2):
1. ✅ Run migrations: `php artisan migrate`
2. ✅ Create `AdminTicketConfigController`
3. ✅ Create admin setup views
4. ✅ Create `TicketCatalogService`
5. ✅ Add routes to `web.php`
6. ✅ Test admin setup workflow

### Testing Each Phase:
- Test with sample data
- Verify database relationships
- Test form submissions
- Test payment flows
- Test email delivery

---

## Estimated Timeline

- **Phase 1:** 1 day (Database setup)
- **Phase 2:** 3-5 days (Admin setup - PRIORITY)
- **Phase 3:** 2-3 days (Core services)
- **Phase 4:** 3-4 days (Public registration)
- **Phase 5:** 2-3 days (Payment processing)
- **Phase 6:** 3-4 days (Admin management)
- **Phase 7:** 1-2 days (Association dashboard)
- **Phase 8:** 1-2 days (Email templates)
- **Phase 9:** 1 day (Early bird reminders)
- **Phase 10:** 2 days (Bulk import)
- **Phase 11:** 3-4 days (Testing)
- **Phase 12:** 1-2 days (Optimization)
- **Phase 13:** 1 day (Documentation)

**Total Estimated Time:** 24-35 days

---

## Notes

- **Start with Phase 2** (Admin Setup) as it's the foundation
- Test each phase before moving to the next
- Use feature branches for each phase
- Keep database migrations in order
- Document any deviations from the plan

