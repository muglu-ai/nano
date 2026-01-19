# Delegate Panel System - Complete Flow Guide

## 📋 Table of Contents
1. [Overview](#overview)
2. [Complete Registration Flow](#complete-registration-flow)
3. [Payment Flow](#payment-flow)
4. [Delegate Account Creation](#delegate-account-creation)
5. [Delegate Panel Access](#delegate-panel-access)
6. [Upgrade Flow](#upgrade-flow)
7. [Getting Started](#getting-started)

---

## Overview

The delegate panel system allows registered delegates to:
- Access their badges
- View notifications from admin
- Upgrade tickets to higher categories
- View and download receipts
- See all registrations in their group

---

## Complete Registration Flow

### Step 1: Public Ticket Registration

**Route:** `/events/{eventSlug}/tickets/register`

1. **User fills registration form:**
   - Contact information (name, email, phone)
   - Company details (name, country, state, city)
   - Organization type and industry sector
   - Registration category
   - GST details (if required)
   - Nationality (Indian/International)
   - Ticket type selection
   - Delegate information (one or more delegates)

2. **System creates:**
   - `ticket_contacts` - Core contact identity
   - `ticket_registrations` - Registration record
   - `ticket_delegates` - Delegate records
   - `ticket_orders` - Order with pending status
   - `ticket_order_items` - Order line items
   - `invoices` - Invoice record for payment tracking

3. **Order Number (TIN) generated:**
   - Format: `TIN-YYYY-XXXXXX`
   - Used for payment lookup

### Step 2: Payment Initiation

**Route:** `/events/{eventSlug}/tickets/payment/{token}` or `/events/{eventSlug}/tickets/payment-lookup`

1. **User views order details:**
   - Shows provisional receipt
   - Displays total amount
   - Shows payment status (pending)

2. **Payment gateway selection:**
   - **International (USD):** PayPal
   - **National (INR):** CCAvenue

3. **Payment processing:**
   - User redirected to payment gateway
   - Payment processed
   - Callback received

### Step 3: Payment Success

**Route:** `/events/{eventSlug}/tickets/payment/callback/{token}`

1. **Payment verification:**
   - Gateway response validated
   - Payment record created in `ticket_payments`
   - Order status updated to 'paid'
   - Invoice status updated to 'paid'

2. **Ticket issuance:**
   - `tickets` records created for each delegate
   - `ticket_delegate_assignments` created with price snapshots
   - Badges generated with QR codes

3. **Confirmation:**
   - Confirmation email sent
   - Receipt generated
   - User redirected to confirmation page

---

## Payment Flow

### For National (INR) Payments - CCAvenue

```
1. User clicks "Pay Now"
   ↓
2. System creates payment data:
   - Order ID: {order_no}_{timestamp}
   - Amount: Order total
   - Currency: INR
   - Billing details
   ↓
3. Redirect to CCAvenue payment page
   ↓
4. User completes payment
   ↓
5. CCAvenue redirects to callback URL
   ↓
6. System decrypts response
   ↓
7. If success:
   - Create TicketPayment record
   - Update order status to 'paid'
   - Issue tickets
   - Send confirmation email
```

### For International (USD) Payments - PayPal

```
1. User clicks "Pay Now"
   ↓
2. System creates PayPal order:
   - Amount in USD
   - Return/Cancel URLs
   ↓
3. Redirect to PayPal approval page
   ↓
4. User approves payment
   ↓
5. PayPal redirects to callback URL
   ↓
6. System captures payment
   ↓
7. If success:
   - Create TicketPayment record
   - Update order status to 'paid'
   - Issue tickets
   - Send confirmation email
```

---

## Delegate Account Creation

### Automatic Account Creation

When a ticket registration is completed and paid:

1. **Contact exists:** System uses existing `ticket_contact` record
2. **Account creation:**
   - `ticket_accounts` record created (if not exists)
   - Linked to `ticket_contact`
   - Status: 'active'
   - Password: Not set initially (delegate must set via password reset)

### Manual Account Setup

Delegates can set up their account in two ways:

#### Option 1: Email/Password Login
1. Go to `/delegate/login`
2. Click "Forgot Password"
3. Enter email
4. Receive password reset link
5. Set password
6. Login with email/password

#### Option 2: Email/OTP Login
1. Go to `/delegate/login`
2. Enter email
3. Click "Send OTP"
4. Receive 6-digit OTP via email
5. Enter OTP
6. Auto-logged in (no password needed)

---

## Delegate Panel Access

### Login Routes

**Base URL:** `/delegate/login`

### Authentication Methods

#### 1. Email/Password Login
```
POST /delegate/login
Body: {
  email: "delegate@example.com",
  password: "password123",
  remember: true (optional)
}
```

#### 2. Email/OTP Login
```
Step 1: POST /delegate/otp/send
Body: {
  email: "delegate@example.com"
}

Step 2: POST /delegate/otp/verify
Body: {
  email: "delegate@example.com",
  otp: "123456"
}
```

### Protected Routes (Require Authentication)

All delegate panel routes are protected by `DelegateAuthMiddleware`:

- `/delegate/dashboard` - Dashboard overview
- `/delegate/registrations` - View all registrations
- `/delegate/upgrades` - Ticket upgrades
- `/delegate/receipts` - View receipts
- `/delegate/notifications` - View notifications
- `/delegate/badges` - View/download badges

---

## Upgrade Flow

### Step 1: View Available Upgrades

**Route:** `/delegate/upgrades`

1. Delegate sees list of their tickets
2. System shows:
   - Current ticket type
   - Current price (from `price_snapshot`)
   - Available higher categories
   - Upgrade button for each ticket

### Step 2: Initiate Upgrade

**Route:** `/delegate/upgrades/form/{ticketId}`

1. Delegate selects new ticket type (must be higher category)
2. System calculates:
   - **Old total:** What was already paid (from `price_snapshot` + GST + charges)
   - **New total:** New ticket price + GST + charges
   - **Remaining amount:** New total - Old total
3. Upgrade request created in `ticket_upgrade_requests` (temp table)
4. Provisional receipt generated

### Step 3: Payment for Upgrade

**Route:** `/delegate/upgrades/payment/initiate/{requestId}`

1. System creates upgrade order
2. Payment gateway selection:
   - International → PayPal
   - National → CCAvenue
3. User redirected to payment gateway
4. Payment processed

### Step 4: Payment Success & Master Table Update

**Route:** `/delegate/upgrades/payment/success/{requestId}`

1. Payment verified
2. **Master tables updated:**
   - `tickets.ticket_type_id` → Updated to new type
   - `ticket_delegate_assignments` → Updated with new price snapshot
   - `ticket_upgrades` → Upgrade record created
3. **Temp table kept:**
   - `ticket_upgrade_requests` → Status updated to 'paid' (kept for audit)
4. Final receipt generated
5. Delegate redirected to receipt page

---

## Getting Started

### Prerequisites

1. **Database Setup:**
   ```bash
   php artisan migrate
   ```

2. **Configuration:**
   - Payment gateway credentials in `.env`:
     - CCAvenue (for INR)
     - PayPal (for USD)
   - Mail configuration for OTP emails

3. **Event Setup:**
   - Create event in admin panel
   - Configure ticket types
   - Set pricing (national/international)

### Testing the Flow

#### 1. Create a Test Registration

```bash
# Via public registration form
Visit: /events/{eventSlug}/tickets/register

# Or via admin panel (for testing)
Admin → Tickets → Create Registration
```

#### 2. Complete Payment

```bash
# For INR payments (CCAvenue test mode)
Visit: /events/{eventSlug}/tickets/payment-lookup
Enter TIN number
Click "Pay Now"
Use CCAvenue test credentials

# For USD payments (PayPal sandbox)
Visit: /events/{eventSlug}/tickets/payment-lookup
Enter TIN number
Click "Pay Now"
Use PayPal sandbox account
```

#### 3. Access Delegate Panel

```bash
# Option 1: Email/Password
Visit: /delegate/login
Click "Forgot Password"
Set password
Login

# Option 2: Email/OTP
Visit: /delegate/login
Enter email
Click "Send OTP"
Check email for OTP
Enter OTP
Auto-logged in
```

#### 4. Test Upgrade Flow

```bash
# 1. Login to delegate panel
/delegate/login

# 2. Go to upgrades
/delegate/upgrades

# 3. Click "Upgrade" on a ticket

# 4. Select higher category

# 5. Review remaining amount

# 6. Click "Pay Now"

# 7. Complete payment

# 8. Verify ticket upgraded in master tables
```

### Key Database Tables

| Table | Purpose |
|-------|---------|
| `ticket_contacts` | Core contact identity |
| `ticket_accounts` | Delegate login accounts |
| `ticket_registrations` | Registration records |
| `ticket_delegates` | Delegate information |
| `ticket_orders` | Orders (pending/paid) |
| `ticket_payments` | Payment records |
| `tickets` | Issued tickets |
| `ticket_delegate_assignments` | Price snapshots |
| `ticket_upgrade_requests` | Upgrade requests (temp) |
| `ticket_upgrades` | Upgrade history (master) |
| `ticket_receipts` | Receipt records |

### Important Routes

#### Public Routes
- `/events/{eventSlug}/tickets/register` - Registration form
- `/events/{eventSlug}/tickets/payment-lookup` - Payment lookup
- `/delegate/login` - Delegate login

#### Protected Delegate Routes
- `/delegate/dashboard` - Dashboard
- `/delegate/registrations` - Registrations list
- `/delegate/upgrades` - Upgrades
- `/delegate/receipts` - Receipts
- `/delegate/notifications` - Notifications
- `/delegate/badges` - Badges

#### Payment Callbacks
- `/events/{eventSlug}/tickets/payment/callback/{token}` - Payment callback
- `/delegate/upgrades/payment/success/{requestId}` - Upgrade payment success
- `/delegate/upgrades/payment/failure/{requestId}` - Upgrade payment failure

### Testing Checklist

- [ ] Create test registration
- [ ] Complete payment (INR)
- [ ] Complete payment (USD)
- [ ] Verify tickets issued
- [ ] Test delegate login (password)
- [ ] Test delegate login (OTP)
- [ ] View registrations in delegate panel
- [ ] View badges
- [ ] Initiate upgrade
- [ ] Complete upgrade payment
- [ ] Verify master tables updated
- [ ] View receipts
- [ ] Download PDF receipts

### Common Issues & Solutions

#### Issue: Delegate can't login
**Solution:**
- Check if `ticket_account` exists for contact
- Verify email matches `ticket_contacts.email`
- Check account status is 'active'

#### Issue: Upgrade calculation wrong
**Solution:**
- Verify `price_snapshot` exists in `ticket_delegate_assignments`
- Check ticket type pricing in `ticket_types`
- Ensure nationality matches (national/international)

#### Issue: Payment callback not working
**Solution:**
- Check payment gateway credentials
- Verify callback URLs in gateway settings
- Check logs: `storage/logs/laravel.log`

---

## Summary

The complete flow is:

1. **Registration** → User registers for tickets
2. **Payment** → User pays via CCAvenue (INR) or PayPal (USD)
3. **Ticket Issuance** → System creates tickets and delegate accounts
4. **Delegate Login** → Delegate logs in via email/password or email/OTP
5. **Panel Access** → Delegate accesses dashboard, badges, receipts, etc.
6. **Upgrade** → Delegate upgrades ticket (if needed)
7. **Upgrade Payment** → Delegate pays remaining amount
8. **Master Update** → System updates tickets and keeps audit trail

All data flows through proper tables with audit trails and proper relationships maintained throughout the system.
