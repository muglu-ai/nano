# BTS Portal - Application Overview
## Focus: Registration Flows & Database Schema

**Version:** Laravel 11.31  
**Purpose:** Exhibitor & Visitor Management Portal for Bengaluru Tech Summit 2025  
**Last Updated:** 2025-01-XX

---

## Table of Contents
1. [Application Architecture](#1-application-architecture)
2. [Registration Flows](#2-registration-flows)
3. [Database Schema](#3-database-schema)
4. [Key Business Flows](#4-key-business-flows)
5. [Payment Processing Flow](#5-payment-processing-flow)
6. [Delegate & Pass Management Flow](#6-delegate--pass-management-flow)
7. [Data Relationships](#7-data-relationships)

---

## 1. Application Architecture

### 1.1 System Overview
The BTS Portal is a **multi-tenant exhibition management system** that handles:
- **Exhibitor Registration** (Main Exhibitor, Startup Zone, Co-Exhibitor)
- **Visitor/Attendee Registration** (Public registration)
- **Payment Processing** (PayPal, CCAvenue)
- **Booth & Pass Management**
- **Delegate Management** (Complimentary, Stall Manning)
- **Extra Requirements Ordering**
- **Meeting Room Bookings**
- **Sponsorship Management**

### 1.2 User Roles & Access
```
super-admin → Event configuration, multi-event management
admin       → Application approval, booth allocation, user management
exhibitor   → Main exhibitor portal (application, payment, delegates)
co-exhibitor → Sub-account for co-exhibitors
sponsor     → Sponsorship management
visitor     → Public visitor registration
sales       → Sales team access
```

### 1.3 Technology Stack
- **Backend:** Laravel 11.31, PHP 8.2+
- **Database:** MySQL/MariaDB
- **Frontend:** Blade Templates
- **Payment:** PayPal, CCAvenue
- **Email:** ElasticEmail
- **Key Packages:** Excel import/export, PDF generation, QR codes, CAPTCHA

---

## 2. Registration Flows

### 2.1 Main Exhibitor Registration Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    EXHIBITOR REGISTRATION FLOW                   │
└─────────────────────────────────────────────────────────────────┘

1. USER REGISTRATION
   ├─ Route: POST /register (AuthController@register)
   ├─ Creates: User record (role='exhibitor')
   ├─ Email verification token sent
   └─ Redirect: /event-list

2. EVENT SELECTION
   ├─ Route: GET /event-list (AuthController@showEvents)
   ├─ User selects event
   └─ Redirect: /{event}/onboarding

3. ONBOARDING FORM
   ├─ Route: GET /{event}/onboarding (ApplicationController@showForm2)
   ├─ Multi-step form:
   │   ├─ Company Information
   │   ├─ Contact Details (EventContact)
   │   ├─ Billing Details (BillingDetail)
   │   ├─ Booth Selection (stall_category, interested_sqm)
   │   ├─ Sector/Product Selection
   │   └─ Terms Acceptance
   └─ Auto-save to session

4. APPLICATION SUBMISSION
   ├─ Route: POST /exhibitor/application (ApplicationController@submitForm)
   ├─ Creates:
   │   ├─ Application record (status='initiated')
   │   ├─ EventContact record
   │   ├─ SecondaryEventContact (optional)
   │   └─ BillingDetail record
   ├─ Generates: application_id (unique)
   └─ Status: 'submitted' → Admin review

5. ADMIN REVIEW & APPROVAL
   ├─ Route: POST /approve/{id} (AdminController@approve)
   ├─ Updates: Application.status = 'approved'
   ├─ Creates: Invoice record
   └─ Email: Onboarding email sent

6. PAYMENT PROCESSING
   ├─ Route: GET /payment (PaymentController@showOrder)
   ├─ User selects payment gateway (PayPal/CCAvenue)
   ├─ Payment processed
   └─ Invoice updated: payment_status = 'paid'/'partial'

7. POST-PAYMENT ACTIVITIES
   ├─ Booth Allocation (Admin)
   ├─ Pass Allocation (Admin)
   ├─ Delegate Registration (Exhibitor)
   └─ Extra Requirements Ordering (Exhibitor)
```

**Key Models:**
- `User` → `Application` → `EventContact`, `BillingDetail`
- `Application` → `Invoice` → `Payment`
- `Application` → `ExhibitionParticipant` → `ComplimentaryDelegate`, `StallManning`

---

### 2.2 Startup Zone Registration Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                  STARTUP ZONE REGISTRATION FLOW                  │
└─────────────────────────────────────────────────────────────────┘

1. PUBLIC REGISTRATION FORM
   ├─ Route: GET /startup/register (StartupZoneController@showForm)
   ├─ Query Param: ?association={name} (for association-specific pricing)
   ├─ Form Features:
   │   ├─ Dynamic field configuration (FormFieldConfiguration)
   │   ├─ Association pricing rules (AssociationPricingRule)
   │   ├─ Multi-step form with progress tracking
   │   └─ Auto-save to session (no DB writes initially)
   └─ No authentication required

2. AUTO-SAVE DRAFT
   ├─ Route: POST /startup/auto-save (StartupZoneController@autoSave)
   ├─ Stores: Session data (startup_zone_draft)
   ├─ Optional: Creates StartupZoneDraft record (for persistence)
   └─ Tracks: progress_percentage, last_updated_field

3. PROMOCODE VALIDATION
   ├─ Route: POST /startup/validate-promocode
   ├─ Validates: AssociationPricingRule
   └─ Returns: Pricing details, discount

4. GST DETAILS FETCH
   ├─ Route: POST /startup/fetch-gst-details
   ├─ External API: GST lookup service
   └─ Returns: Company details from GST number

5. FORM PREVIEW
   ├─ Route: GET /startup/preview (StartupZoneController@showPreview)
   └─ Shows: Complete form data before submission

6. FINAL SUBMISSION
   ├─ Route: POST /startup/submit-form (StartupZoneController@submitForm)
   ├─ Validates: reCAPTCHA Enterprise
   ├─ Creates:
   │   ├─ User record (if email doesn't exist)
   │   ├─ Application record (application_type='startup-zone')
   │   ├─ EventContact record (from contact_data JSON)
   │   ├─ BillingDetail record (from billing_data JSON)
   │   └─ Links: StartupZoneDraft.converted_to_application_id
   ├─ Generates: application_id
   └─ Email: Admin notification, user confirmation

7. PAYMENT PROCESSING
   ├─ Route: GET /startup/payment/{applicationId}
   ├─ Route: POST /startup/payment/{applicationId}/process
   ├─ Creates: Invoice record
   └─ Payment gateway integration

8. CONFIRMATION
   ├─ Route: GET /startup/confirmation/{applicationId}
   └─ Shows: Registration confirmation, invoice details
```

**Key Models:**
- `StartupZoneDraft` (temporary storage, session-based)
- `AssociationPricingRule` (pricing configuration)
- `FormFieldConfiguration` (dynamic form fields)
- `Application` (final record after conversion)

**Key Features:**
- **No authentication required** for initial registration
- **Session-based draft** (can be converted to DB draft)
- **Association-specific pricing** via promocode
- **Dynamic form configuration** (fields can be enabled/disabled)
- **GST lookup integration** for auto-filling company details
- **reCAPTCHA Enterprise** protection

---

### 2.3 Visitor/Attendee Registration Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                  VISITOR REGISTRATION FLOW                      │
└─────────────────────────────────────────────────────────────────┘

1. PUBLIC REGISTRATION
   ├─ Route: GET /visitor/registration (AttendeeController@showForm)
   ├─ Public access (no authentication)
   ├─ CAPTCHA protection
   └─ Form fields: Personal info, company, job details, event days

2. REGISTRATION SUBMISSION
   ├─ Route: POST /visitor/registration (AttendeeController@visitor_reg)
   ├─ Creates: Attendee record (status='pending')
   ├─ Generates: unique_id, QR code
   └─ Email: Confirmation email (optional)

3. ADMIN APPROVAL
   ├─ Route: POST /approve-attendee (AttendeeController@approveInauguralSession)
   ├─ Updates: Attendee.status = 'approved'
   ├─ Mass approval: POST /attendees/mass-approve
   └─ Email: Approval confirmation

4. QR CODE GENERATION
   ├─ Generated on registration
   ├─ Stored: qr_code_path
   └─ Used: Event check-in

5. API SYNC (Optional)
   ├─ Route: GET /send-data/{unique_id} (ApiRelayController)
   ├─ Syncs: Attendee data to external systems (Interlinx)
   └─ Updates: api_sent, api_data, api_response
```

**Key Models:**
- `Attendee` (visitor registration)
- No relationship to `Application` (separate flow)

---

## 3. Database Schema

### 3.1 Core User & Application Tables

#### `users`
```sql
id                  BIGINT PRIMARY KEY
name                VARCHAR(255)
email               VARCHAR(255) UNIQUE
password            VARCHAR(255)
role                ENUM('admin','exhibitor','co-exhibitor','sponsor','visitor','sales','super-admin')
email_verified_at   TIMESTAMP
simplePass          VARCHAR(255)  -- Plain text password (for email)
sub_role           VARCHAR(255)   -- Granular permissions
created_at, updated_at
```

#### `applications` (Main Exhibitor Applications)
```sql
id                          BIGINT PRIMARY KEY
user_id                     BIGINT FK → users.id
event_id                    BIGINT FK → events.id (default: 1)
application_id              VARCHAR(255) UNIQUE  -- Generated ID (e.g., SI25-EXH-XXXXX)
uuid                        CHAR(35)

-- Company Information
company_name                VARCHAR(255)
address                     VARCHAR(255)
city_id                     VARCHAR(255)
state_id                    BIGINT FK → states.id
country_id                  BIGINT FK → countries.id
postal_code                 VARCHAR(255)
landline                    VARCHAR(255)
company_email               VARCHAR(255)
website                     VARCHAR(255)
headquarters_country_id     BIGINT FK → countries.id

-- Business Details
sector_id                   VARCHAR(255)
subSector                   VARCHAR(255)
type_of_business            VARCHAR(255)
main_product_category       VARCHAR(125)
product_groups              VARCHAR(500)
participant_type            VARCHAR(255)
previous_participation      BOOLEAN
interested_sqm             VARCHAR(125)
allocated_sqm              VARCHAR(125)

-- Booth Information
stall_category             VARCHAR(255)  -- 'Shell Scheme', 'Raw Space', 'Startup Booth'
booth_count                INTEGER
boothDescription           TEXT
fascia_name                VARCHAR(255)
logo_link                  TEXT

-- Tax & Compliance
gst_compliance             BOOLEAN
gst_no                     VARCHAR(255)
pan_no                     VARCHAR(255)
tan_no                     VARCHAR(255)
certificate                VARCHAR(255)  -- File path

-- Application Status
status                     ENUM('initiated','submitted','approved','rejected')
submission_status          VARCHAR(255) DEFAULT 'in progress'
submission_date            DATE
approved_date              DATE
approved_by               VARCHAR(255)
rejected_date              DATE
rejection_reason           TEXT

-- Event Participation
participation_type        VARCHAR(255)
is_pavilion               BOOLEAN
pavilion_id               INTEGER
pavilionName              VARCHAR(255)
has_sponsorship           BOOLEAN
sponsorship_item_id       VARCHAR(255)
sponsorship_count         VARCHAR(255)
spon_discount_eligible    BOOLEAN

-- Booth Allocation (Admin)
stallNumber               VARCHAR(50)
zone                      VARCHAR(255)
hallNo                    VARCHAR(50)
pref_location             VARCHAR(125)

-- Terms & Conditions
terms_accepted            TINYINT
cancellation_terms        TINYINT
coex_terms_accepted       BOOLEAN

-- Pricing
payment_currency          ENUM('EUR','INR') DEFAULT 'INR'
region                    VARCHAR(30)

-- Membership
semi_member               BOOLEAN
semi_memberID             VARCHAR(255)
assoc_mem                 VARCHAR(125)
membership_verified       BOOLEAN

-- Application Type
application_type          VARCHAR(125) DEFAULT 'exhibitor'  -- 'exhibitor', 'startup-zone', 'sponsor'
promocode                 VARCHAR(255)  -- For association pricing

-- Metadata
salesPerson               VARCHAR(125)
RegSource                VARCHAR(250)
userActive                BOOLEAN
companyYears              INTEGER
exhibitorType             VARCHAR(255)
tag                       VARCHAR(255)
cart_data                 JSON
declarationStatus         BOOLEAN
remarks                   TEXT

created_at, updated_at
```

**Key Relationships:**
- `applications.user_id` → `users.id`
- `applications.event_id` → `events.id`
- `applications.state_id` → `states.id`
- `applications.country_id` → `countries.id`
- `applications.billing_country_id` → `countries.id`

---

#### `event_contacts` (Primary Contact Person)
```sql
id              BIGINT PRIMARY KEY
application_id  BIGINT FK → applications.id (CASCADE DELETE)
salutation      VARCHAR(25)
first_name      VARCHAR(255)
last_name       VARCHAR(255)
job_title       VARCHAR(255)
email           VARCHAR(255)
contact_number  VARCHAR(255)
secondary_email VARCHAR(255)
designation     VARCHAR(255)
created_at, updated_at
```

#### `secondary_event_contacts` (Secondary Contact)
```sql
id              BIGINT PRIMARY KEY
application_id  BIGINT FK → applications.id (CASCADE DELETE)
-- Similar structure to event_contacts
```

#### `billing_details` (Billing Information)
```sql
id                  BIGINT PRIMARY KEY
application_id      BIGINT FK → applications.id
company_name        VARCHAR(255)
address             TEXT
country_id          BIGINT FK → countries.id
state_id            BIGINT FK → states.id
city                VARCHAR(255)
postal_code         VARCHAR(255)
telephone           VARCHAR(255)
email               VARCHAR(255)
website             VARCHAR(255)
gst_no              VARCHAR(255)
pan_no              VARCHAR(255)
created_at, updated_at
```

---

### 3.2 Payment & Invoice Tables

#### `invoices`
```sql
id                          BIGINT PRIMARY KEY
application_id              BIGINT FK → applications.id (nullable)
sponsorship_id              BIGINT FK → sponsorships.id (nullable)
type                        VARCHAR(255)  -- 'Stall Booking', 'Extra Requirements', 'Meeting Room', 'Co-Exhibitor'
application_no              VARCHAR(255)  -- Links to Application.application_id
sponsorship_no              VARCHAR(255)

-- Amounts
amount                      DECIMAL(10,2)
rate                        DECIMAL(10,2)
int_amount_value            DECIMAL(10,2)
usd_rate                    DECIMAL(10,2)
currency                    ENUM('EUR','INR','USD')
price                       DOUBLE
discount_per                DOUBLE
discount                    DOUBLE
gst                         DOUBLE
processing_chargesRate      INTEGER
processing_charges          DOUBLE
total_final_price           DECIMAL(10,2)
partial_payment_percentage  DECIMAL(5,2)
pending_amount              DECIMAL(10,2) DEFAULT 0.00
amount_paid                 DECIMAL(10,2) DEFAULT 0.00

-- Payment Status
payment_status              ENUM('unpaid','credit','partial','paid','overdue')
payment_due_date            DATE
invoice_no                  VARCHAR(255)
pin_no                      VARCHAR(50)

-- Additional Charges
tds_amount                  DOUBLE DEFAULT 0
surCharge                   INTEGER DEFAULT 0
surChargepercentage         INTEGER DEFAULT 0
surChargeRemove             BOOLEAN DEFAULT true
surChargeReason             TEXT
removeProcessing            TINYINT DEFAULT 0
tdsReason                   TEXT
surChargeLock               BOOLEAN DEFAULT false

-- Metadata
co_exhibitorID              VARCHAR(100)
remarks                     JSON
tax_invoice                 TEXT
refund                      BOOLEAN DEFAULT false

created_at, updated_at
```

#### `payments`
```sql
id                  BIGINT PRIMARY KEY
invoice_id          BIGINT FK → invoices.id
user_id             BIGINT FK → users.id (nullable)
payment_method      VARCHAR(255)  -- 'PayPal', 'CCAvenue', 'Bank Transfer'
amount              DECIMAL(10,2)
amount_paid         DECIMAL(10,2)
amount_received     DECIMAL(10,2)
transaction_id      VARCHAR(255)
order_id            TEXT
pg_result           VARCHAR(255)  -- Payment gateway result
track_id            VARCHAR(255)
response            TEXT
pg_response_json    JSON
payment_date        DATETIME
currency            VARCHAR(10)
status              ENUM('successful','failed','pending')
rejection_reason    TEXT
receipt_image       TEXT

-- Verification
verification_status VARCHAR(150) DEFAULT 'Pending'
verified_by         VARCHAR(255)
verified_at         TIMESTAMP
remarks             TEXT
tds_amount          DOUBLE DEFAULT 0
tdsReason           TEXT

created_at, updated_at
```

#### `payment_receipts`
```sql
id              BIGINT PRIMARY KEY
invoice_id      BIGINT FK → invoices.id
receipt_path    VARCHAR(255)  -- File path
uploaded_by     BIGINT FK → users.id
created_at, updated_at
```

---

### 3.3 Delegate & Pass Management Tables

#### `exhibition_participants` (Links Application to Delegates)
```sql
id              BIGINT PRIMARY KEY
application_id  BIGINT FK → applications.id
-- Metadata for pass allocation
created_at, updated_at
```

#### `complimentary_delegates` (Complimentary Pass Holders)
```sql
id                          BIGINT PRIMARY KEY
exhibition_participant_id   BIGINT FK → exhibition_participants.id (CASCADE DELETE)
unique_id                   VARCHAR(25) UNIQUE
ticketType                  VARCHAR(125)  -- 'VIP', 'Premium', 'Standard', etc.
title                       VARCHAR(25)
first_name                  VARCHAR(255)
middle_name                 VARCHAR(250)
last_name                   VARCHAR(255)
email                       VARCHAR(255)
mobile                      VARCHAR(25)
job_title                   VARCHAR(255)
organisation_name           VARCHAR(255)
token                       VARCHAR(255)  -- Invitation token
address                     TEXT
city                        VARCHAR(255)
state                       VARCHAR(255)
country                     VARCHAR(255)
postal_code                 VARCHAR(25)
buisness_nature             TEXT
products                    TEXT
id_type                     VARCHAR(150)
id_no                       VARCHAR(50)
profile_pic                 TEXT
pinNo                       VARCHAR(50)

-- Event Participation
inaugural_session           BOOLEAN DEFAULT true
inauguralConfirmation       BOOLEAN DEFAULT false
lunchStatus                 BOOLEAN DEFAULT false
confirmedCategory           VARCHAR(100)
approvedHistory             TEXT

-- API Integration
api_data                    JSON
api_response                JSON
api_sent                    BOOLEAN
emailSent                   BOOLEAN DEFAULT false

created_at, updated_at
```

#### `stall_manning` (Stall Manning Staff)
```sql
-- Similar structure to complimentary_delegates
-- Linked via exhibition_participants
```

---

### 3.4 Visitor/Attendee Tables

#### `attendees` (Public Visitor Registrations)
```sql
id                  BIGINT PRIMARY KEY
unique_id           VARCHAR(255) UNIQUE
badge_category      VARCHAR(255)
title               VARCHAR(25)
first_name          VARCHAR(255)
middle_name         VARCHAR(255)
last_name           VARCHAR(255)
designation         VARCHAR(255)
company             VARCHAR(255)
address             TEXT
country             BIGINT FK → countries.id
state               BIGINT FK → states.id
city                VARCHAR(255)
postal_code         VARCHAR(50)
mobile              VARCHAR(50)
email               VARCHAR(255)
purpose             TEXT
products            TEXT
business_nature     TEXT
job_function        VARCHAR(255)
job_category        VARCHAR(125)
job_subcategory     VARCHAR(125)
other_job_category  VARCHAR(250)
profile_picture     TEXT
id_card_type        VARCHAR(125)
id_card_number      VARCHAR(125)
consent             BOOLEAN DEFAULT false

-- Registration Details
registration_type   VARCHAR(125)  -- 'Online', 'Offline'
event_days         TEXT  -- JSON array of dates
source             TEXT
promotion_consent  VARCHAR(12)
startup            BOOLEAN

-- Status & Approval
status             ENUM('pending','approved','rejected','active') DEFAULT 'pending'
inaugural_session  BOOLEAN
inauguralConfirmation BOOLEAN DEFAULT false
approvedCate       VARCHAR(250)
regId              VARCHAR(50)
lunchStatus        BOOLEAN DEFAULT false
approvedHistory    TEXT
updatedBy          TEXT

-- Email & Verification
email_verified     BOOLEAN DEFAULT false
email_verify_otp   VARCHAR(10)
emailSent          BOOLEAN DEFAULT false

-- QR Code
qr_code_path       TEXT

-- API Integration
api_data           JSON
api_response       JSON
api_sent           BOOLEAN
reminder           JSON

created_at, updated_at
```

**Note:** `attendees` is **independent** of `applications` - visitors register separately from exhibitors.

---

### 3.5 Startup Zone Tables

#### `startup_zone_drafts` (Temporary Draft Storage)
```sql
id                          BIGINT PRIMARY KEY
session_id                   VARCHAR(255)  -- Session identifier
uuid                        CHAR(36)      -- Alternative identifier

-- Booth Information
stall_category              VARCHAR(255)
interested_sqm              VARCHAR(125)

-- Company Information
company_name                VARCHAR(255)
certificate_path            VARCHAR(255)  -- Uploaded certificate
how_old_startup             INTEGER
address                     VARCHAR(500)
city_id                     VARCHAR(255)
state_id                    BIGINT FK → states.id
postal_code                 VARCHAR(10)
country_id                  BIGINT FK → countries.id
landline                    VARCHAR(20)
website                     VARCHAR(255)
company_email               VARCHAR(255)

-- Tax Information (Encrypted)
gst_compliance              BOOLEAN
gst_no                      TEXT  -- Encrypted
pan_no                      TEXT  -- Encrypted

-- Sector Information
sector_id                   VARCHAR(255)
subSector                   VARCHAR(255)
type_of_business            VARCHAR(255)

-- Association & Promocode
promocode                   VARCHAR(100)
assoc_mem                   VARCHAR(125)
RegSource                   VARCHAR(250)

-- Contact & Billing (JSON)
contact_data                JSON  -- {title, first_name, last_name, designation, email, mobile, country_code}
billing_data                JSON  -- Billing details
exhibitor_data              JSON  -- Additional exhibitor data

-- Payment
payment_mode                VARCHAR(50)

-- Metadata
application_type            VARCHAR(125) DEFAULT 'startup-zone'
event_id                    BIGINT DEFAULT 1
user_id                     BIGINT FK → users.id (nullable)
last_updated_field          VARCHAR(100)
progress_percentage         INTEGER DEFAULT 0
is_abandoned                BOOLEAN DEFAULT false
abandoned_at                TIMESTAMP
expires_at                  TIMESTAMP

-- Conversion Tracking
converted_to_application_id BIGINT FK → applications.id (nullable)
converted_at                TIMESTAMP

created_at, updated_at
```

**Key Features:**
- **Session-based** draft storage (can be persisted to DB)
- **JSON fields** for flexible contact/billing data
- **Encrypted** GST/PAN numbers
- **Conversion tracking** to final Application

#### `association_pricing_rules` (Association-Specific Pricing)
```sql
id                  BIGINT PRIMARY KEY
association_name    VARCHAR(255)
promocode           VARCHAR(100) UNIQUE
logo_path           VARCHAR(255)  -- Association logo
discount_percentage DECIMAL(5,2)
is_active           BOOLEAN
valid_from          DATE
valid_to            DATE
created_at, updated_at
```

#### `form_field_configurations` (Dynamic Form Fields)
```sql
id                  BIGINT PRIMARY KEY
form_type           VARCHAR(100)  -- 'startup-zone', 'exhibitor', etc.
field_name          VARCHAR(255)
field_label          VARCHAR(255)
field_type          VARCHAR(50)  -- 'text', 'select', 'textarea', etc.
is_required         BOOLEAN
is_active           BOOLEAN
sort_order          INTEGER
version             INTEGER  -- For versioning
validation_rules    JSON
created_at, updated_at
```

#### `gst_lookups` (GST Details Cache)
```sql
id              BIGINT PRIMARY KEY
gst_no          VARCHAR(255) UNIQUE
company_name    VARCHAR(255)
pan_no          VARCHAR(255)
city            VARCHAR(255)
address         TEXT
created_at, updated_at
```

---

### 3.6 Supporting Tables

#### `events` (Event Master)
```sql
id              BIGINT PRIMARY KEY
name            VARCHAR(255)
slug            VARCHAR(255) UNIQUE
start_date      DATE
end_date        DATE
venue           VARCHAR(255)
status          ENUM('draft','active','completed')
created_at, updated_at
```

#### `countries`, `states`, `cities` (Geo Master Data)
```sql
-- countries: id, name, code, phone_code, etc.
-- states: id, name, country_id FK
-- cities: id, name, state_id FK
```

#### `sectors` (Industry Sectors)
```sql
id              BIGINT PRIMARY KEY
name            VARCHAR(255)
is_active       BOOLEAN
sort_order      INTEGER
created_at, updated_at
```

#### `organization_types` (Business Types)
```sql
id              BIGINT PRIMARY KEY
name            VARCHAR(255)
is_active       BOOLEAN
sort_order      INTEGER
created_at, updated_at
```

---

### 3.7 Extra Requirements & Orders

#### `extra_requirements` (Service Items)
```sql
id              BIGINT PRIMARY KEY
name            VARCHAR(255)
description     TEXT
price           DECIMAL(10,2)
category        VARCHAR(255)
is_active       BOOLEAN
created_at, updated_at
```

#### `requirements_orders` (Orders)
```sql
id                  BIGINT PRIMARY KEY
application_id      BIGINT FK → applications.id
order_number        VARCHAR(255) UNIQUE
status              ENUM('pending','confirmed','delivered','cancelled')
total_amount        DECIMAL(10,2)
delivery_status     VARCHAR(255)
delivered_at        TIMESTAMP
created_at, updated_at
```

#### `requirement_order_items` (Order Line Items)
```sql
id                      BIGINT PRIMARY KEY
requirements_order_id   BIGINT FK → requirements_orders.id
extra_requirement_id    BIGINT FK → extra_requirements.id
quantity                INTEGER
unit_price              DECIMAL(10,2)
total_price             DECIMAL(10,2)
created_at, updated_at
```

#### `requirements_billing` (Order Billing)
```sql
id                  BIGINT PRIMARY KEY
requirements_order_id BIGINT FK → requirements_orders.id
-- Similar to billing_details structure
```

---

### 3.8 Meeting Room Booking Tables

#### `meeting_rooms`
```sql
id                  BIGINT PRIMARY KEY
meeting_room_type_id BIGINT FK → meeting_room_types.id
name                VARCHAR(255)
capacity            INTEGER
is_active           BOOLEAN
created_at, updated_at
```

#### `meeting_room_bookings`
```sql
id                  BIGINT PRIMARY KEY
application_id      BIGINT FK → applications.id
meeting_room_id     BIGINT FK → meeting_rooms.id
booking_date        DATE
slot_id             BIGINT FK → meeting_room_slots.id
status              ENUM('pending','confirmed','cancelled')
payment_status      ENUM('unpaid','paid')
invoice_id          BIGINT FK → invoices.id
created_at, updated_at
```

---

## 4. Key Business Flows

### 4.1 Application Status Workflow

```
initiated → submitted → approved → (payment) → active
                ↓
            rejected
```

**Status Transitions:**
1. **initiated** - Application created, not yet submitted
2. **submitted** - Exhibitor submits application (`submission_status = 'submitted'`)
3. **approved** - Admin approves (`status = 'approved'`, `approved_date` set)
4. **rejected** - Admin rejects (`status = 'rejected'`, `rejection_reason` set)
5. **active** - Payment received (40%+ partial or full), `userActive = true`

---

### 4.2 Invoice & Payment Workflow

```
Application Approved
    ↓
Invoice Created (type='Stall Booking')
    ↓
Payment Gateway Selected
    ↓
Payment Processed
    ↓
Payment Record Created
    ↓
Invoice Updated (payment_status, amount_paid)
    ↓
Receipt Generated
```

**Payment Status Values:**
- `unpaid` - No payment received
- `partial` - Partial payment (40%+ required for activation)
- `paid` - Full payment received
- `credit` - Credit note issued
- `overdue` - Payment past due date

---

### 4.3 Delegate Invitation Flow

```
Exhibitor Dashboard
    ↓
Invite Delegate (POST /invite)
    ↓
ComplimentaryDelegate Created (token generated)
    ↓
Email Invitation Sent (with token link)
    ↓
Delegate Clicks Link (/invited/{token})
    ↓
Delegate Fills Form
    ↓
Delegate Submits (POST /invite/submit)
    ↓
ComplimentaryDelegate Updated (inauguralConfirmation, etc.)
    ↓
QR Code Generated
    ↓
Confirmation Email Sent
```

---

## 5. Payment Processing Flow

### 5.1 Payment Gateway Selection

```
User accesses: GET /payment
    ↓
System checks: Invoice exists? Payment status?
    ↓
User selects: PayPal or CCAvenue
    ↓
Payment form displayed
```

### 5.2 PayPal Flow

```
POST /paypal/create
    ↓
PayPal Order Created (via PayPal API)
    ↓
User redirected to PayPal
    ↓
User completes payment
    ↓
PayPal redirects: /paypal/success
    ↓
POST /paypal/capture-order/{orderId}
    ↓
Payment record created
    ↓
Invoice updated
    ↓
Webhook: POST /paypal/webhook (verification)
```

### 5.3 CCAvenue Flow

```
GET /payment/ccavenue/{id}
    ↓
CCAvenue form displayed
    ↓
POST /payment/ccavenue/{id}
    ↓
User redirected to CCAvenue
    ↓
User completes payment
    ↓
CCAvenue redirects: POST /payment/ccavenue-success
    ↓
Payment record created
    ↓
Invoice updated
    ↓
Webhook: POST /ccavenue/webhook (verification)
```

### 5.4 Payment Verification

```
Manual Verification:
    POST /verify-payment
    Admin reviews payment receipt
    Updates: verification_status = 'verified'

Automatic Verification:
    Webhook received
    Payment gateway response validated
    Payment status updated automatically
```

---

## 6. Delegate & Pass Management Flow

### 6.1 Pass Allocation (Admin)

```
Admin Dashboard
    ↓
Passes Allocation Page (/passes-allocation)
    ↓
Auto-Allocate: POST /auto-allocate-passes
    (Based on booth size: SQM → pass count)
    ↓
Manual Override: POST /update-passes-allocation
    ↓
Passes allocated to ExhibitionParticipant
```

**Pass Allocation Rules:**
- Based on `allocated_sqm` in `applications`
- Different pass types: VIP, Premium, Standard, Exhibitor, Service
- Auto-calculation: SQM × multiplier = pass count

### 6.2 Delegate Registration (Exhibitor)

```
Exhibitor Dashboard
    ↓
View Delegate List: GET /exhibitor/list/{type}
    (type: 'complimentary', 'stall-manning')
    ↓
Invite Delegate: POST /invite
    ↓
Delegate receives email with token
    ↓
Delegate registers: GET /invited/{token}
    ↓
Delegate submits: POST /invite/submit
    ↓
ComplimentaryDelegate/StallManning created
    ↓
QR code generated
    ↓
Confirmation email sent
```

---

## 7. Data Relationships

### 7.1 Core Entity Relationship Diagram

```
users
  ├─→ applications (1:N)
  │     ├─→ event_contacts (1:1)
  │     ├─→ secondary_event_contacts (1:1)
  │     ├─→ billing_details (1:1)
  │     ├─→ invoices (1:N)
  │     │     └─→ payments (1:N)
  │     ├─→ exhibition_participants (1:1)
  │     │     ├─→ complimentary_delegates (1:N)
  │     │     └─→ stall_manning (1:N)
  │     ├─→ co_exhibitors (1:N)
  │     ├─→ requirements_orders (1:N)
  │     ├─→ meeting_room_bookings (1:N)
  │     └─→ sponsorships (1:N)
  │
  └─→ payments (1:N) [direct user payments]

events
  └─→ applications (1:N)

countries
  ├─→ applications (1:N) [country_id, billing_country_id, headquarters_country_id]
  ├─→ states (1:N)
  │     └─→ cities (1:N)
  └─→ attendees (1:N)
```

### 7.2 Startup Zone Relationships

```
startup_zone_drafts
  └─→ converted_to_application_id → applications.id (1:1, nullable)

association_pricing_rules
  └─→ (referenced by promocode in applications/startup_zone_drafts)

form_field_configurations
  └─→ (referenced by form_type='startup-zone')

gst_lookups
  └─→ (referenced by gst_no for auto-fill)
```

### 7.3 Independent Entities

```
attendees
  └─→ (No direct relationship to applications)
      └─→ Standalone visitor registrations

extra_requirements
  └─→ requirement_order_items (1:N)
      └─→ requirements_orders (N:1)
          └─→ applications (N:1)
```

---

## 8. Key Design Patterns & Considerations

### 8.1 Multi-Event Support
- `events` table for event master data
- `applications.event_id` links to event
- Super-admin can configure multiple events
- Event-specific configurations via `event_configurations` table

### 8.2 Session-Based Drafts (Startup Zone)
- Initial draft stored in session (no DB writes)
- Optional persistence to `startup_zone_drafts` table
- Auto-cleanup of abandoned drafts (`expires_at`)
- Conversion to `Application` on final submission

### 8.3 Flexible Pricing
- Association-specific pricing via `association_pricing_rules`
- Promocode-based discounts
- Processing fees: 3% (National) / 9% (International)
- GST: 18% on base amount
- TDS support with manual addition

### 8.4 Payment Flexibility
- Partial payments supported (40% minimum for activation)
- Multiple payment gateways (PayPal, CCAvenue)
- Manual payment verification
- Receipt upload support
- Refund tracking

### 8.5 Pass Management
- Auto-allocation based on booth size
- Manual override available
- Different pass types for different purposes
- Invitation-based delegate registration
- QR code generation for check-in

---

## 9. Expansion Considerations for New Registration System

### 9.1 Current Registration Types
1. **Main Exhibitor** - Full application flow, authenticated
2. **Startup Zone** - Public registration, session-based drafts
3. **Visitor/Attendee** - Public registration, no application link
4. **Co-Exhibitor** - Sub-account, linked to main exhibitor

### 9.2 Recommended Approach for New Registration System

**Option A: Extend Existing Pattern**
- Add new `application_type` value (e.g., 'new-registration-type')
- Reuse `applications` table structure
- Create new controller extending `ApplicationController` pattern
- Use existing payment/invoice flow

**Option B: Separate Registration Flow**
- Create new table (e.g., `new_registrations`)
- Independent flow similar to `attendees`
- Link to `applications` if needed via foreign key
- Separate payment flow if required

**Option C: Hybrid Approach**
- Use `applications` table with new `application_type`
- Custom form fields via `form_field_configurations`
- Association pricing via `association_pricing_rules`
- Reuse payment/invoice infrastructure

### 9.3 Database Schema Recommendations

**If extending `applications` table:**
- Add new columns for registration-specific fields
- Use JSON columns for flexible data (`cart_data` pattern)
- Maintain foreign key relationships

**If creating new table:**
- Follow `applications` table structure as template
- Link to `users` table if authentication required
- Link to `events` table for multi-event support
- Consider linking to `invoices` for payment processing

### 9.4 Flow Integration Points

**Authentication:**
- Use existing `AuthController` or create new
- Reuse session middleware (`CheckUser`, `SharedMiddleware`)

**Payment:**
- Reuse `PaymentController` / `PaymentGatewayController`
- Link to `invoices` table
- Use existing payment gateway integrations

**Email:**
- Create new Mail class extending existing pattern
- Use ElasticEmail service
- Queue support available

**Admin Management:**
- Extend `AdminController` or create new
- Reuse approval workflow pattern
- Use existing export functionality

---

## 10. Promocode System

### 10.1 Promocode Storage

**Primary Table:** `association_pricing_rules`

Promocodes are stored in the `association_pricing_rules` table with the following structure:

```sql
association_pricing_rules
├─ id                      BIGINT PRIMARY KEY
├─ association_name        VARCHAR(255) UNIQUE  -- Association identifier
├─ display_name           VARCHAR(255)         -- Display name for UI
├─ promocode              VARCHAR(100) UNIQUE  -- THE PROMOCODE (unique across all)
├─ logo_path              VARCHAR(255)         -- Association logo
├─ base_price             DECIMAL(10,2)        -- Default price (₹52,000)
├─ special_price          DECIMAL(10,2)        -- Discounted price
├─ is_complimentary       BOOLEAN              -- Free registration flag
├─ max_registrations      INTEGER              -- Registration limit
├─ current_registrations  INTEGER              -- Current count
├─ is_active              BOOLEAN              -- Enable/disable
├─ valid_from             DATE                 -- Promocode validity start
├─ valid_until            DATE                 -- Promocode validity end
├─ description            TEXT                 -- Association description
└─ entitlements           TEXT                 -- JSON or text
```

**Secondary Storage:** `applications.promocode`

When a user applies a promocode during registration, it's also stored in the `applications` table:

```sql
applications
└─ promocode              VARCHAR(255)  -- Stores the promocode used
```

### 10.2 Promocode Creation Process

**Current Status:** ⚠️ **NOT IMPLEMENTED** - The admin interface for creating promocodes is **NOT implemented** in the codebase.

**What EXISTS:**
- ✅ `association_pricing_rules` table (database migration exists)
- ✅ `AssociationPricingRule` model (exists and functional)
- ✅ Promocode validation in `StartupZoneController@validatePromocode` (works)
- ✅ Promocode usage in registration flow (works)

**What DOES NOT EXIST:**
- ❌ Admin routes for association pricing management
- ❌ Controller methods in `SuperAdminController` for CRUD operations
- ❌ Admin views/interface for managing promocodes
- ❌ Logo upload functionality for associations

**Planned Implementation (from STARTUP_ZONE_FORM_PLAN.md - NOT YET IMPLEMENTED):**

The following routes are **planned but NOT implemented** in `routes/web.php`:

```php
// ❌ THESE ROUTES DO NOT EXIST IN THE CODEBASE
GET    /super-admin/association-pricing        -- List all association rules
POST   /super-admin/association-pricing        -- Create new association rule with promocode
PUT    /super-admin/association-pricing/{id}   -- Update association rule
DELETE /super-admin/association-pricing/{id}    -- Delete association rule
```

**Current Workaround - Manual Creation:**

Since the admin interface is not implemented, promocodes must be created manually:

1. **Direct Database Insert:**
   ```sql
   INSERT INTO association_pricing_rules (
       association_name, display_name, promocode, 
       base_price, special_price, is_complimentary,
       max_registrations, is_active, valid_from, valid_until
   ) VALUES (
       'TIESB', 'TIESB Association', 'TIESB2025',
       52000.00, 40000.00, false,
       25, true, '2025-01-01', '2025-12-31'
   );
   ```

2. **Via Database Seeder:**
   ```php
   // Create seeder: php artisan make:seeder AssociationPricingRuleSeeder
   AssociationPricingRule::create([
       'association_name' => 'TIESB',
       'display_name' => 'TIESB Association',
       'promocode' => 'TIESB2025',
       'base_price' => 52000.00,
       'special_price' => 40000.00,
       'is_active' => true,
       'valid_from' => '2025-01-01',
       'valid_until' => '2025-12-31',
   ]);
   ```

3. **Via Tinker:**
   ```bash
   php artisan tinker
   ```
   ```php
   App\Models\AssociationPricingRule::create([
       'association_name' => 'TIESB',
       'display_name' => 'TIESB Association',
       'promocode' => 'TIESB2025',
       'base_price' => 52000.00,
       'special_price' => 40000.00,
       'is_active' => true,
   ]);
   ```

**To Implement Admin Interface (Future Work):**

1. Add routes to `routes/web.php` in the super-admin group
2. Add methods to `SuperAdminController`:
   - `associationPricing()` - List view
   - `storeAssociationPricing()` - Create
   - `updateAssociationPricing()` - Update
   - `deleteAssociationPricing()` - Delete
   - `uploadAssociationLogo()` - Logo upload
3. Create Blade views in `resources/views/super-admin/association-pricing/`
4. Add validation for promocode uniqueness

### 10.3 Promocode Validation Flow

**Validation Endpoint:** `POST /startup/validate-promocode`

**Controller Method:** `StartupZoneController@validatePromocode`

**Validation Logic:**
```php
1. User enters promocode in registration form
2. AJAX call to /startup/validate-promocode
3. System checks:
   - Promocode exists in association_pricing_rules
   - is_active = true
   - valid_from <= today (if set)
   - valid_until >= today (if set)
   - current_registrations < max_registrations (if limit set)
4. Returns association details if valid:
   - Association name
   - Display name
   - Logo URL
   - Effective price (special_price or base_price)
   - Is complimentary flag
5. Frontend updates form with association details
```

**Validation Rules:**
- Promocode must be unique (enforced at database level)
- Case-insensitive matching (convert to uppercase for lookup)
- Must be active (`is_active = true`)
- Must be within validity dates (if set)
- Registration limit must not be exceeded

### 10.4 Promocode Usage Flow

```
1. User visits: /startup/register?association={name} OR enters promocode
   ↓
2. If promocode entered:
   - AJAX validation: POST /startup/validate-promocode
   - If valid: Auto-fill association details, update pricing
   ↓
3. User completes registration form
   ↓
4. Form submission: POST /startup/submit-form
   - Promocode stored in: startup_zone_drafts.promocode (session)
   ↓
5. Draft converted to Application:
   - Promocode copied to: applications.promocode
   - Association name stored in: applications.assoc_mem
   - Association name stored in: applications.RegSource
   ↓
6. Invoice generation:
   - Uses association pricing rules
   - Applies special_price or base_price
   - Sets is_complimentary if applicable
```

### 10.5 Promocode Tracking

**Usage Tracking:**
- `applications.promocode` - Stores which promocode was used
- `applications.assoc_mem` - Stores association name
- `applications.RegSource` - Stores registration source (association name)

**Registration Count:**
- `association_pricing_rules.current_registrations` - Auto-incremented after successful payment
- Used to enforce `max_registrations` limit

**Query Examples:**
```sql
-- Find all applications using a specific promocode
SELECT * FROM applications WHERE promocode = 'TIESB2025';

-- Count registrations per association
SELECT assoc_mem, COUNT(*) as count 
FROM applications 
WHERE application_type = 'startup-zone' 
GROUP BY assoc_mem;

-- Check if promocode limit reached
SELECT * FROM association_pricing_rules 
WHERE promocode = 'TIESB2025' 
AND current_registrations >= max_registrations;
```

### 10.6 Promocode Model

**Model:** `App\Models\AssociationPricingRule`

**Key Methods:**
- `scopeActive()` - Filter active rules
- `scopeValid()` - Filter rules within validity dates
- `getEffectivePrice()` - Returns special_price or base_price (or 0 if complimentary)
- `isRegistrationFull()` - Checks if max_registrations reached
- `getLogoUrl()` - Returns full logo URL path

**Example Usage:**
```php
// Find active, valid association by promocode
$association = AssociationPricingRule::where('promocode', 'TIESB2025')
    ->active()
    ->valid()
    ->first();

// Check if registration is full
if ($association->isRegistrationFull()) {
    // Show "Registration Full" message
}

// Get effective price
$price = $association->getEffectivePrice(); // Returns special_price or base_price
```

### 10.7 Promocode Best Practices

1. **Uniqueness:** Always ensure promocode is unique (database constraint enforces this)
2. **Case Handling:** Convert to uppercase for consistency
3. **Validity Dates:** Set `valid_from` and `valid_until` for time-limited promocodes
4. **Registration Limits:** Set `max_registrations` to control capacity
5. **Active Status:** Use `is_active` to enable/disable without deleting
6. **Tracking:** Monitor `current_registrations` vs `max_registrations`
7. **Pricing:** Use `special_price` for discounts, `is_complimentary` for free registrations

### 10.8 Admin Interface Status

**⚠️ NOT IMPLEMENTED - Admin Interface Does Not Exist**

**What's Missing:**
- ❌ No routes in `routes/web.php` for association pricing management
- ❌ No controller methods in `SuperAdminController` for CRUD operations
- ❌ No Blade views for managing association pricing rules
- ❌ No logo upload functionality
- ❌ No usage statistics dashboard

**Planned Features (To Be Implemented):**
- CRUD interface for association pricing rules
- Promocode generation/validation
- Logo upload for associations
- Registration limit management
- Usage statistics per promocode
- View registrations by promocode
- Bulk operations

**Current Workaround (Manual Methods):**
1. **Direct Database Access:**
   - Use MySQL client or phpMyAdmin
   - Insert/update records directly in `association_pricing_rules` table

2. **Database Seeders:**
   - Create seeders for initial data
   - Run: `php artisan db:seed --class=AssociationPricingRuleSeeder`

3. **Laravel Tinker:**
   - Use `php artisan tinker` for quick CRUD operations
   - Example: `AssociationPricingRule::create([...])`

4. **Migration Scripts:**
   - Create data migrations for specific promocodes
   - Run: `php artisan migrate`

---

## 11. Summary

### 10.1 Current System Strengths
✅ **Comprehensive registration flows** (Exhibitor, Startup Zone, Visitor)  
✅ **Flexible database schema** with JSON support for dynamic data  
✅ **Multi-payment gateway support** (PayPal, CCAvenue)  
✅ **Session-based drafts** for better UX (Startup Zone)  
✅ **Association pricing** via promocodes  
✅ **Dynamic form configuration** (Startup Zone)  
✅ **Multi-event support** architecture  
✅ **Robust invoice & payment tracking**

### 10.2 Key Tables for New Registration System
- **Core:** `users`, `applications`, `events`
- **Payment:** `invoices`, `payments`, `payment_receipts`
- **Configuration:** `form_field_configurations`, `association_pricing_rules`
- **Geo:** `countries`, `states`, `cities`
- **Master Data:** `sectors`, `organization_types`

### 10.3 Recommended Next Steps
1. **Define registration requirements** (fields, flow, payment)
2. **Choose integration approach** (extend vs. separate)
3. **Design database schema** (extend `applications` or new table)
4. **Map to existing flows** (payment, email, admin approval)
5. **Implement following existing patterns** (controllers, models, views)

---

**Document Version:** 1.0  
**Last Updated:** 2025-01-XX  
**Maintained By:** Development Team

