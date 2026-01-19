# Quick Start Guide - Delegate Panel System

## 🚀 Getting Started in 5 Steps

### Step 1: Ensure Database is Migrated

```bash
php artisan migrate
```

This creates all necessary tables:
- `ticket_contacts`
- `ticket_accounts`
- `ticket_registrations`
- `ticket_orders`
- `ticket_upgrade_requests`
- And all related tables

### Step 2: Configure Payment Gateways

Edit `.env` file:

```env
# CCAvenue (for INR payments)
CCAVENUE_ENV=test  # or 'production'
CCAVENUE_MERCHANT_ID=your_merchant_id
CCAVENUE_ACCESS_CODE=your_access_code
CCAVENUE_WORKING_KEY=your_working_key

# PayPal (for USD payments)
PAYPAL_MODE=sandbox  # or 'live'
PAYPAL_SANDBOX_CLIENT_ID=your_client_id
PAYPAL_SANDBOX_SECRET=your_secret
PAYPAL_LIVE_CLIENT_ID=your_live_client_id
PAYPAL_LIVE_SECRET=your_live_secret
```

### Step 3: Create a Test Registration

**Option A: Via Public Form**
1. Visit: `http://your-domain/events/{eventSlug}/tickets/register`
2. Fill in all details
3. Add delegate information
4. Submit registration
5. Note the TIN number

**Option B: Via Admin Panel (if available)**
1. Login as admin
2. Go to Tickets → Create Registration
3. Fill in details
4. Create registration

### Step 4: Complete Payment

1. Visit: `http://your-domain/events/{eventSlug}/tickets/payment-lookup`
2. Enter the TIN number from Step 3
3. Click "Pay Now"
4. Complete payment using test credentials:
   - **CCAvenue Test:** Use test card numbers
   - **PayPal Sandbox:** Use sandbox account

### Step 5: Access Delegate Panel

#### Method 1: Email/Password Login

1. Visit: `http://your-domain/delegate/login`
2. Click "Forgot Password"
3. Enter the email used in registration
4. Check email for reset link
5. Set password
6. Login with email/password

#### Method 2: Email/OTP Login

1. Visit: `http://your-domain/delegate/login`
2. Enter email
3. Click "Send OTP"
4. Check email for 6-digit OTP
5. Enter OTP
6. Auto-logged in

---

## 📍 Key URLs

### Public URLs
- Registration: `/events/{eventSlug}/tickets/register`
- Payment Lookup: `/events/{eventSlug}/tickets/payment-lookup`
- Delegate Login: `/delegate/login`

### Delegate Panel (Protected)
- Dashboard: `/delegate/dashboard`
- Registrations: `/delegate/registrations`
- Upgrades: `/delegate/upgrades`
- Receipts: `/delegate/receipts`
- Notifications: `/delegate/notifications`
- Badges: `/delegate/badges/{delegateId}`

### Admin URLs
- Delegate Notifications: `/admin/delegate-notifications`

---

## 🧪 Testing Checklist

### Registration & Payment
- [ ] Create registration via public form
- [ ] Verify TIN number generated
- [ ] Complete CCAvenue payment (INR)
- [ ] Complete PayPal payment (USD)
- [ ] Verify tickets issued
- [ ] Check confirmation email received

### Delegate Authentication
- [ ] Test password reset flow
- [ ] Test email/password login
- [ ] Test OTP generation
- [ ] Test OTP verification
- [ ] Verify session persistence

### Delegate Panel Features
- [ ] View dashboard
- [ ] View registrations list
- [ ] View individual registration details
- [ ] View badges
- [ ] Download badge PDF
- [ ] View receipts
- [ ] Download receipt PDF
- [ ] View notifications
- [ ] Mark notifications as read

### Upgrade Flow
- [ ] View available upgrades
- [ ] Initiate individual upgrade
- [ ] Verify remaining amount calculation
- [ ] Complete upgrade payment
- [ ] Verify master tables updated
- [ ] Verify upgrade request kept in temp table
- [ ] View upgrade receipt

---

## 🔍 Verification Steps

### After Registration
```sql
-- Check registration created
SELECT * FROM ticket_registrations ORDER BY id DESC LIMIT 1;

-- Check order created
SELECT * FROM ticket_orders ORDER BY id DESC LIMIT 1;

-- Check delegates created
SELECT * FROM ticket_delegates WHERE registration_id = [last_registration_id];
```

### After Payment
```sql
-- Check payment record
SELECT * FROM ticket_payments ORDER BY id DESC LIMIT 1;

-- Check order status
SELECT id, order_no, status FROM ticket_orders ORDER BY id DESC LIMIT 1;
-- Should be 'paid'

-- Check tickets issued
SELECT * FROM tickets WHERE delegate_id IN (
    SELECT id FROM ticket_delegates WHERE registration_id = [last_registration_id]
);

-- Check assignments with price snapshots
SELECT * FROM ticket_delegate_assignments WHERE delegate_id IN (
    SELECT id FROM ticket_delegates WHERE registration_id = [last_registration_id]
);
```

### After Delegate Login
```sql
-- Check account created
SELECT * FROM ticket_accounts WHERE contact_id = (
    SELECT id FROM ticket_contacts WHERE email = 'test@example.com'
);

-- Check last login updated
SELECT last_login_at FROM ticket_accounts WHERE contact_id = (
    SELECT id FROM ticket_contacts WHERE email = 'test@example.com'
);
```

### After Upgrade
```sql
-- Check upgrade request (temp table)
SELECT * FROM ticket_upgrade_requests ORDER BY id DESC LIMIT 1;
-- Status should be 'paid'

-- Check upgrade record (master table)
SELECT * FROM ticket_upgrades ORDER BY id DESC LIMIT 1;

-- Check ticket updated
SELECT id, ticket_type_id, status FROM tickets WHERE id = [upgraded_ticket_id];
-- ticket_type_id should be new type

-- Check assignment updated
SELECT * FROM ticket_delegate_assignments WHERE delegate_id = [delegate_id];
-- price_snapshot should be new price
```

---

## 🐛 Troubleshooting

### Issue: Delegate can't login

**Check:**
1. Does `ticket_account` exist?
   ```sql
   SELECT * FROM ticket_accounts WHERE contact_id = (
       SELECT id FROM ticket_contacts WHERE email = 'delegate@example.com'
   );
   ```

2. If not, create manually:
   ```sql
   INSERT INTO ticket_accounts (contact_id, status, created_at, updated_at)
   VALUES (
       (SELECT id FROM ticket_contacts WHERE email = 'delegate@example.com'),
       'active',
       NOW(),
       NOW()
   );
   ```

3. Then use password reset to set password

### Issue: Upgrade calculation shows wrong amount

**Check:**
1. Verify price snapshot exists:
   ```sql
   SELECT price_snapshot FROM ticket_delegate_assignments 
   WHERE delegate_id = [delegate_id];
   ```

2. If NULL, check original order:
   ```sql
   SELECT unit_price FROM ticket_order_items 
   WHERE order_id = [order_id];
   ```

3. Manually set price snapshot if needed:
   ```sql
   UPDATE ticket_delegate_assignments 
   SET price_snapshot = [original_price]
   WHERE delegate_id = [delegate_id];
   ```

### Issue: Payment callback not working

**Check:**
1. Verify callback URL in payment gateway settings:
   - CCAvenue: Should be `https://your-domain/events/{eventSlug}/tickets/payment/callback/{token}`
   - PayPal: Should be `https://your-domain/events/{eventSlug}/tickets/payment/callback?gateway=paypal`

2. Check logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. Verify CSRF token is disabled for webhooks (if applicable)

### Issue: OTP not received

**Check:**
1. Mail configuration in `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your_email@gmail.com
   MAIL_PASSWORD=your_password
   MAIL_ENCRYPTION=tls
   ```

2. Test mail sending:
   ```bash
   php artisan tinker
   Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
   ```

3. Check `ticket_otp_requests` table:
   ```sql
   SELECT * FROM ticket_otp_requests ORDER BY id DESC LIMIT 1;
   ```

---

## 📝 Sample Test Data

### Create Test Registration via Tinker

```php
php artisan tinker

// Create contact
$contact = \App\Models\Ticket\TicketContact::create([
    'email' => 'test@example.com',
    'phone' => '+919876543210',
    'name' => 'Test User',
    'email_verified_at' => now(),
    'phone_verified_at' => now(),
]);

// Create registration
$registration = \App\Models\Ticket\TicketRegistration::create([
    'event_id' => 1, // Your event ID
    'contact_id' => $contact->id,
    'company_name' => 'Test Company',
    'company_country' => 'India',
    'nationality' => 'Indian',
    'industry_sector' => 'Technology',
    'organisation_type' => 'Corporate',
]);

// Create delegate
$delegate = \App\Models\Ticket\TicketDelegate::create([
    'registration_id' => $registration->id,
    'first_name' => 'Test',
    'last_name' => 'Delegate',
    'email' => 'test@example.com',
    'phone' => '+919876543210',
]);

// Create order
$order = \App\Models\Ticket\TicketOrder::create([
    'registration_id' => $registration->id,
    'order_no' => 'TIN-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
    'subtotal' => 1000,
    'gst_total' => 180,
    'processing_charge_total' => 30,
    'total' => 1210,
    'status' => 'paid',
]);

// Create account for login
$account = \App\Models\Ticket\TicketAccount::create([
    'contact_id' => $contact->id,
    'status' => 'active',
]);
```

---

## 🎯 Next Steps

1. **Test Complete Flow:**
   - Registration → Payment → Login → Panel Access

2. **Test Upgrade Flow:**
   - Login → View Upgrades → Initiate → Pay → Verify

3. **Configure Production:**
   - Update payment gateway credentials
   - Set mail configuration
   - Test with real credentials

4. **Monitor:**
   - Check logs regularly
   - Monitor payment callbacks
   - Track upgrade requests

---

## 📞 Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Check database tables for data integrity
3. Verify routes: `php artisan route:list | grep delegate`
4. Test authentication: `php artisan tinker` → `Auth::guard('delegate')->check()`
