# Ticket Payment System - Test Cases

## Test Environment Setup
- **PayPal Mode**: Sandbox (for testing)
- **Test Accounts**: Use PayPal sandbox test accounts
- **Base URL**: `https://bengalurutechsummit.com/bts-2026/public/` (replace with your actual domain)
- **Event Slug**: `bts-2026` (replace with your actual event slug)

### URL Patterns Reference
| Page | URL Pattern | Example |
|------|-------------|---------|
| **Registration** | `{BASE_URL}/tickets/{eventSlug}/register` | `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/register` |
| **Preview** | `{BASE_URL}/tickets/{eventSlug}/preview` | `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/preview` |
| **Payment Page** | `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}` | `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/payment/TIN-BTS-2026-TKT-123456` |
| **Payment Process** | `{BASE_URL}/tickets/{eventSlug}/payment/{orderNo}` | `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/payment/TIN-BTS-2026-TKT-123456` |
| **Lookup** | `{BASE_URL}/tickets/{eventSlug}/payment/lookup` | `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/payment/lookup` |
| **Confirmation** | `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}` | `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/confirmation/abc123...` |
| **GST Validation** | `POST {BASE_URL}/tickets/validate-gst` | API endpoint |
| **Payment Callback** | `{BASE_URL}/tickets/{eventSlug}/payment/callback/{gateway}` | `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/payment/callback/paypal` |

### Quick URL Examples
Replace `{BASE_URL}` with your actual domain and `{eventSlug}` with your event slug (e.g., `bts-2026`):
- Registration: `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/register`
- Payment by TIN: `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/payment/TIN-BTS-2026-TKT-123456`
- Lookup: `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/payment/lookup`

---

## 1. Registration Form Tests

### 1.1 Basic Registration Flow
- [ ] **TC-001**: User can access ticket registration page
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Example**: `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/register`

- [ ] **TC-002**: User can fill all required fields (company name, phone, email, etc.)
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`

- [ ] **TC-003**: User can select nationality (Indian/International)
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`

- [ ] **TC-004**: User can add multiple delegates
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`

- [ ] **TC-005**: User can remove delegates
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`

- [ ] **TC-006**: Form validation works for empty required fields
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Action**: Submit form with empty required fields

- [ ] **TC-007**: Form validation works for invalid email format
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Action**: Enter invalid email (e.g., "invalid-email")

- [ ] **TC-008**: Form validation works for invalid phone number format
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Action**: Enter invalid phone number

### 1.2 Phone Number Handling
- [ ] **TC-009**: Phone number with spaces is automatically trimmed on submit
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Action**: Enter phone with spaces (e.g., "91 9487 9384 73"), submit form

- [ ] **TC-010**: Phone number country code and number are stored separately
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Verify**: Check database - country code and number in separate fields

- [ ] **TC-011**: Phone number is merged as `+CC-NUMBER` format on submit
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Verify**: Check database - phone stored as `+91-9487938473` format

- [ ] **TC-012**: If validation fails, phone number is split back to country code and number
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Action**: Submit form with validation error, verify phone fields populate correctly

- [ ] **TC-013**: Phone number displays correctly after returning from preview page
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register` (after returning from preview)
  - **Action**: Go back from preview, verify phone number displays correctly

- [ ] **TC-014**: International phone numbers work correctly (non-Indian country codes)
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Action**: Select non-Indian country code (e.g., +1, +44), enter phone number

### 1.3 GST Validation
- [ ] **TC-015**: GST validation button appears when "GST Required" is "Yes"
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Action**: Select "GST Required: Yes"

- [ ] **TC-016**: GST validation works with valid GSTIN
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **API Endpoint**: `POST {BASE_URL}/tickets/validate-gst`
  - **Action**: Click "Validate GST" button with valid GSTIN

- [ ] **TC-017**: GST validation fails with invalid GSTIN
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **API Endpoint**: `POST {BASE_URL}/tickets/validate-gst`
  - **Action**: Click "Validate GST" button with invalid GSTIN

- [ ] **TC-018**: GST details auto-fill after successful validation
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Action**: Validate GST and verify auto-fill

- [ ] **TC-019**: Rate limiting works (3 hits per IP per 24 hours)
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **API Endpoint**: `POST {BASE_URL}/tickets/validate-gst`
  - **Action**: Make 4+ GST validation requests from same IP

- [ ] **TC-020**: Rate limit message displays correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Action**: Exceed rate limit and verify message

- [ ] **TC-021**: Manual GST entry works when rate limit is exceeded
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Action**: Enter GST details manually after rate limit

- [ ] **TC-022**: GST validation uses cached data when available
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Action**: Validate same GSTIN twice (second should use cache)

### 1.4 Form Submission
- [ ] **TC-023**: Form submits successfully with all valid data
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Submit URL**: `POST {BASE_URL}/tickets/{eventSlug}/register`

- [ ] **TC-024**: reCAPTCHA validation works (if enabled)
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Action**: Submit form without completing reCAPTCHA

- [ ] **TC-025**: Form redirects to preview page after successful submission
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Expected Redirect**: `{BASE_URL}/tickets/{eventSlug}/preview`

- [ ] **TC-026**: Session data is stored correctly for preview
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`
  - **Action**: Verify all form data displays correctly

---

## 2. Preview Page Tests

### 2.1 Currency Display
- [ ] **TC-027**: Indian nationality shows ₹ (INR) currency symbol
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`
  - **Prerequisite**: Submit form with Indian nationality

- [ ] **TC-028**: International nationality shows $ (USD) currency symbol
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`
  - **Prerequisite**: Submit form with International nationality

- [ ] **TC-029**: All price breakdowns show correct currency symbol
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`

- [ ] **TC-030**: Ticket price displays with correct currency
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`

- [ ] **TC-031**: GST amount displays with correct currency
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`

- [ ] **TC-032**: Processing charge displays with correct currency
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`

- [ ] **TC-033**: Total amount displays with correct currency
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`

### 2.2 Processing Charge Calculation
- [ ] **TC-034**: Indian nationality shows 3% processing charge
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`
  - **Prerequisite**: Submit form with Indian nationality

- [ ] **TC-035**: International nationality shows 9% processing charge
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`
  - **Prerequisite**: Submit form with International nationality

- [ ] **TC-036**: Processing charge is calculated correctly for Indian (3%)
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`
  - **Action**: Verify calculation: (Ticket Price + GST) × 3%

- [ ] **TC-037**: Processing charge is calculated correctly for International (9%)
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`
  - **Action**: Verify calculation: (Ticket Price + GST) × 9%

- [ ] **TC-038**: Total amount includes correct processing charge
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`
  - **Action**: Verify: Total = Ticket Price + GST + Processing Charge

### 2.3 Data Display
- [ ] **TC-039**: All registration details display correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`

- [ ] **TC-040**: Delegate information displays correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`

- [ ] **TC-041**: GST information displays (if applicable)
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`
  - **Prerequisite**: GST Required = Yes

- [ ] **TC-042**: "Proceed to Payment" button works
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`
  - **Submit URL**: `POST {BASE_URL}/tickets/{eventSlug}/payment/initiate`
  - **Expected Redirect**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`

---

## 3. Payment Initiation Tests

### 3.1 Order Creation
- [ ] **TC-043**: Order is created successfully after preview submission
  - **Submit URL**: `POST {BASE_URL}/tickets/{eventSlug}/payment/initiate`
  - **Verify**: Check database `ticket_orders` table

- [ ] **TC-044**: Order number (TIN) is generated correctly
  - **Format**: `TIN-BTS-2026-TKT-XXXXXX`
  - **Verify**: Check `ticket_orders.order_no` field

- [ ] **TC-045**: Order status is set to "pending"
  - **Verify**: Check database `ticket_orders.status` = 'pending'

- [ ] **TC-046**: Invoice is created with correct currency (INR/USD)
  - **Verify**: Check database `invoices` table
  - **Indian**: currency = 'INR'
  - **International**: currency = 'USD'

- [ ] **TC-047**: Registration tracking record is created/updated
  - **Verify**: Check database `ticket_registration_tracking` table

- [ ] **TC-048**: Initial confirmation email is sent (with "Initiating" subject)
  - **Verify**: Check user's email inbox
  - **Subject**: "Thank You for Initiating Registration at {Event} {Year}"

### 3.2 Payment Page Access
- [ ] **TC-049**: Payment page is accessible via TIN in URL
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`
  - **Example**: `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/payment/TIN-BTS-2026-TKT-123456`

- [ ] **TC-050**: Payment page shows order details correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`

- [ ] **TC-051**: Payment page shows correct currency symbol
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`

- [ ] **TC-052**: Payment page shows email preview
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`

- [ ] **TC-053**: Payment page shows "Pay Now" button
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`

- [ ] **TC-054**: Payment page displays which payment gateway will be used
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`

- [ ] **TC-055**: Payment page can be refreshed without losing data
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`
  - **Action**: Refresh the page (F5 or Ctrl+R)

- [ ] **TC-056**: Payment page URL can be shared and accessed directly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`
  - **Action**: Open URL in new browser/incognito window

### 3.3 Payment Gateway Selection
- [ ] **TC-057**: Indian nationality redirects to CCAvenue
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`
  - **Pay Now URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{orderNo}`
  - **Expected**: Redirects to CCAvenue payment form

- [ ] **TC-058**: International nationality redirects to PayPal
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`
  - **Pay Now URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{orderNo}`
  - **Expected**: Redirects to PayPal sandbox/live

- [ ] **TC-059**: Currency enforcement: INR → CCAvenue only
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{orderNo}`
  - **Action**: Click "Pay Now" for Indian order

- [ ] **TC-060**: Currency enforcement: USD → PayPal only
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{orderNo}`
  - **Action**: Click "Pay Now" for International order

- [ ] **TC-061**: Payment gateway cannot be manually changed
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{orderNo}`
  - **Action**: Verify system enforces correct gateway

---

## 4. CCAvenue Payment Tests (Indian Users)

### 4.1 Payment Initiation
- [ ] **TC-062**: CCAvenue payment form loads correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{orderNo}` (for Indian order)
  - **Expected**: Redirects to CCAvenue payment form

- [ ] **TC-063**: Amount is in INR currency
  - **Verify**: Check CCAvenue form shows ₹ symbol and INR currency

- [ ] **TC-064**: Order ID is passed correctly to CCAvenue
  - **Verify**: Check CCAvenue form shows correct order ID

- [ ] **TC-065**: Billing details are passed correctly
  - **Verify**: Check CCAvenue form shows correct billing name, email, phone

### 4.2 Payment Success
- [ ] **TC-066**: Payment success callback is received
  - **Callback URL**: `{BASE_URL}/tickets/{eventSlug}/payment/callback/ccavenue`
  - **Action**: Complete payment on CCAvenue

- [ ] **TC-067**: Order status is updated to "paid"
  - **Verify**: Check database `ticket_orders` table

- [ ] **TC-068**: Invoice status is updated to "paid"
  - **Verify**: Check database `invoices` table

- [ ] **TC-069**: TicketPayment record is created
  - **Verify**: Check database `ticket_payments` table

- [ ] **TC-070**: Payment record is created in payments table
  - **Verify**: Check database `payments` table

- [ ] **TC-071**: Payment confirmation email is sent (with "Thank You for Registration" subject)
  - **Verify**: Check user's email inbox

- [ ] **TC-072**: User is redirected to confirmation page
  - **Expected URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-073**: Confirmation page shows payment details
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-074**: Confirmation page shows transaction ID
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-075**: Confirmation page shows payment method
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-076**: Progress bar step 3 is marked as completed (green)
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

### 4.3 Payment Failure
- [ ] **TC-077**: Payment failure callback is received
  - **Callback URL**: `{BASE_URL}/tickets/{eventSlug}/payment/callback/ccavenue`
  - **Action**: Simulate payment failure on CCAvenue

- [ ] **TC-078**: Order status remains "pending"
  - **Verify**: Check database `ticket_orders` table

- [ ] **TC-079**: Invoice status remains "unpaid"
  - **Verify**: Check database `invoices` table

- [ ] **TC-080**: TicketPayment record is created with "failed" status
  - **Verify**: Check database `ticket_payments` table

- [ ] **TC-081**: Payment record is created with "failed" status
  - **Verify**: Check database `payments` table

- [ ] **TC-082**: User is redirected back to payment page with error message
  - **Expected URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`
  - **Action**: Verify error message displays

- [ ] **TC-083**: User can retry payment
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`
  - **Action**: Click "Pay Now" again

### 4.4 Payment Cancellation
- [ ] **TC-084**: User can cancel payment on CCAvenue
  - **Action**: Click cancel on CCAvenue payment page

- [ ] **TC-085**: User is redirected back to payment page
  - **Expected URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`

- [ ] **TC-086**: Order remains in "pending" status
  - **Verify**: Check database `ticket_orders.status` = 'pending'

- [ ] **TC-087**: User can retry payment after cancellation
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`
  - **Action**: Click "Pay Now" again

---

## 5. PayPal Payment Tests (International Users)

### 5.1 Payment Initiation
- [ ] **TC-088**: PayPal redirect URL is generated correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{orderNo}` (for International order)
  - **Verify**: Check logs for PayPal approval URL

- [ ] **TC-089**: User is redirected to PayPal sandbox/live
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{orderNo}`
  - **Expected**: Redirects to PayPal (sandbox URL if mode=sandbox)

- [ ] **TC-090**: Amount is in USD currency
  - **Verify**: Check PayPal page shows $ symbol and USD currency

- [ ] **TC-091**: Order ID is passed correctly to PayPal
  - **Verify**: Check PayPal order details show correct order reference

- [ ] **TC-092**: Return URL is set correctly
  - **Expected**: `{BASE_URL}/tickets/{eventSlug}/payment/callback/paypal`
  - **Verify**: Check PayPal order creation logs

- [ ] **TC-093**: Cancel URL is set correctly
  - **Expected**: `{BASE_URL}/tickets/{eventSlug}/payment/callback/paypal`
  - **Verify**: Check PayPal order creation logs

### 5.2 Payment Success
- [ ] **TC-094**: PayPal redirects back after payment approval
  - **Return URL**: `{BASE_URL}/tickets/{eventSlug}/payment/callback/paypal?token={paypal_order_id}`
  - **Action**: Approve payment on PayPal

- [ ] **TC-095**: Callback receives token parameter
  - **Callback URL**: `{BASE_URL}/tickets/{eventSlug}/payment/callback/paypal`
  - **Verify**: Check logs for token parameter

- [ ] **TC-096**: Payment is captured successfully
  - **Verify**: Check PayPal API response

- [ ] **TC-097**: Order status is updated to "paid"
  - **Verify**: Check database `ticket_orders` table

- [ ] **TC-098**: Invoice status is updated to "paid"
  - **Verify**: Check database `invoices` table

- [ ] **TC-099**: TicketPayment record is created
  - **Verify**: Check database `ticket_payments` table

- [ ] **TC-100**: Payment record is created in payments table
  - **Verify**: Check database `payments` table

- [ ] **TC-101**: Payment confirmation email is sent
  - **Verify**: Check user's email inbox

- [ ] **TC-102**: User is redirected to confirmation page
  - **Expected URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-103**: Confirmation page shows PayPal payment details
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-104**: Confirmation page shows transaction ID
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-105**: Confirmation page shows payment method as "PayPal"
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-106**: Progress bar step 3 is marked as completed (green)
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

### 5.3 Payment Failure
- [ ] **TC-107**: PayPal payment failure is handled
  - **Callback URL**: `{BASE_URL}/tickets/{eventSlug}/payment/callback/paypal`
  - **Action**: Simulate payment failure on PayPal

- [ ] **TC-108**: Order status remains "pending"
  - **Verify**: Check database `ticket_orders.status` = 'pending'

- [ ] **TC-109**: Invoice status remains "unpaid"
  - **Verify**: Check database `invoices.payment_status` = 'unpaid'

- [ ] **TC-110**: TicketPayment record is created with "failed" status
  - **Verify**: Check database `ticket_payments.status` = 'failed'

- [ ] **TC-111**: Payment record is created with "failed" status
  - **Verify**: Check database `payments.status` = 'failed'

- [ ] **TC-112**: User is redirected back with error message
  - **Expected URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`
  - **Action**: Verify error message displays

### 5.4 Payment Cancellation
- [ ] **TC-113**: User can cancel payment on PayPal
  - **Action**: Click cancel on PayPal payment page

- [ ] **TC-114**: User is redirected back to payment page
  - **Expected URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`

- [ ] **TC-115**: Order remains in "pending" status
  - **Verify**: Check database `ticket_orders.status` = 'pending'

- [ ] **TC-116**: User can retry payment after cancellation
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`
  - **Action**: Click "Pay Now" again

---

## 6. Confirmation Page Tests

### 6.1 Display
- [ ] **TC-117**: Confirmation page loads correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`
  - **Example**: `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/confirmation/abc123def456...`

- [ ] **TC-118**: Order details display correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-119**: Payment status shows "paid" for successful payments
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-120**: Payment transaction details display (if paid)
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-121**: Payment gateway name displays correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-122**: Payment method displays correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-123**: Transaction ID displays correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-124**: Amount paid displays with correct currency symbol
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-125**: Payment date/time displays correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-126**: Progress bar shows step 3 as completed (green) if paid
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-127**: Progress bar shows step 3 as active (blue) if pending
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`
  - **Prerequisite**: Access confirmation page for unpaid order

### 6.2 Currency Display
- [ ] **TC-128**: Indian orders show ₹ currency symbol
- [ ] **TC-129**: International orders show $ currency symbol
- [ ] **TC-130**: All amounts display with correct currency

---

## 7. Email Tests

### 7.1 Initial Registration Email
- [ ] **TC-131**: Email is sent after order creation
- [ ] **TC-132**: Email subject is "Thank You for Initiating Registration at {Event} {Year}"
- [ ] **TC-133**: Email contains order details
- [ ] **TC-134**: Email contains payment link with TIN
- [ ] **TC-135**: Email is sent only to user (no BCC to admin)

### 7.2 Payment Confirmation Email
- [ ] **TC-136**: Email is sent after successful payment
- [ ] **TC-137**: Email subject is "Thank You for Registration at {Event} {Year}"
- [ ] **TC-138**: Email contains payment confirmation details
- [ ] **TC-139**: Email contains transaction ID
- [ ] **TC-140**: Email contains payment method
- [ ] **TC-141**: Email is sent only to user (no BCC to admin)

### 7.3 Email Content
- [ ] **TC-142**: Email preview matches actual email sent
- [ ] **TC-143**: Email contains correct currency symbol
- [ ] **TC-144**: Email contains correct order number (TIN)
- [ ] **TC-145**: Email contains all delegate information

---

## 8. Lookup Page Tests

### 8.1 Order Lookup
- [ ] **TC-146**: User can access lookup page
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/lookup`
  - **Example**: `https://bengalurutechsummit.com/bts-2026/public/tickets/bts-2026/payment/lookup`

- [ ] **TC-147**: User can search by TIN/Order Number
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/lookup`
  - **Submit URL**: `POST {BASE_URL}/tickets/{eventSlug}/payment/lookup`
  - **Action**: Enter TIN and click "Lookup Order"

- [ ] **TC-148**: Order details display correctly after lookup
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/lookup`
  - **Expected**: Redirects to order details page

- [ ] **TC-149**: Currency symbol displays correctly (₹ or $)
  - **URL**: Order details page (after lookup)

- [ ] **TC-150**: Payment gateway info displays correctly
  - **URL**: Order details page (after lookup)

- [ ] **TC-151**: "Complete Payment" button works (if unpaid)
  - **URL**: Order details page (after lookup)
  - **Button URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{orderNo}`

- [ ] **TC-152**: "View Confirmation" button works (if paid)
  - **URL**: Order details page (after lookup)
  - **Button URL**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

### 8.2 Error Handling
- [ ] **TC-153**: Invalid TIN shows error message
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/lookup`
  - **Action**: Enter invalid TIN (e.g., "INVALID-TIN")

- [ ] **TC-154**: Non-existent order shows error message
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/lookup`
  - **Action**: Enter non-existent TIN

- [ ] **TC-155**: Error messages are user-friendly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/lookup`

---

## 9. Edge Cases & Error Handling

### 9.1 Session Management
- [ ] **TC-156**: Session expires - user can still access via TIN
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`
  - **Action**: Clear session/cookies, then access payment page directly

- [ ] **TC-157**: Multiple browser tabs work correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`
  - **Action**: Open same payment page in multiple tabs

- [ ] **TC-158**: Payment page can be accessed after session expiry using TIN
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}`
  - **Action**: Wait for session expiry, then access via TIN

### 9.2 Duplicate Payments
- [ ] **TC-159**: Already paid order cannot be paid again
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}` (for paid order)
  - **Pay Now URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{orderNo}`
  - **Expected**: Redirects to confirmation or shows error

- [ ] **TC-160**: Already paid order redirects to confirmation page
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}` (for paid order)
  - **Expected Redirect**: `{BASE_URL}/tickets/{eventSlug}/confirmation/{token}`

- [ ] **TC-161**: Payment retry after failure works correctly
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{TIN}` (for failed payment)
  - **Action**: Click "Pay Now" again after failure

### 9.3 Data Integrity
- [ ] **TC-162**: All payment attempts are recorded (success/failure)
- [ ] **TC-163**: Invoice is always linked correctly
- [ ] **TC-164**: Order ID (TIN) is stored in payments table
- [ ] **TC-165**: Registration tracking is updated at each stage

### 9.4 Network/API Errors
- [ ] **TC-166**: GST API failure allows manual entry
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **API**: `POST {BASE_URL}/tickets/validate-gst`
  - **Action**: Simulate API failure, verify manual entry still works

- [ ] **TC-167**: PayPal API failure shows error message
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{orderNo}` (International)
  - **Action**: Simulate PayPal API failure
  - **Expected**: Error message displayed, user redirected back

- [ ] **TC-168**: CCAvenue API failure shows error message
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/payment/{orderNo}` (Indian)
  - **Action**: Simulate CCAvenue API failure
  - **Expected**: Error message displayed, user redirected back

- [ ] **TC-169**: Email sending failure doesn't break payment flow
  - **Action**: Simulate email service failure
  - **Expected**: Payment still completes, error logged but not shown to user

### 9.5 Currency/Gateway Mismatch Prevention
- [ ] **TC-170**: Cannot force INR payment through PayPal
- [ ] **TC-171**: Cannot force USD payment through CCAvenue
- [ ] **TC-172**: System automatically corrects gateway based on currency

---

## 10. Analytics & Tracking Tests

### 10.1 Registration Tracking
- [ ] **TC-173**: Tracking record is created when form is started
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/register`
  - **Verify**: Check database `ticket_registration_tracking` table

- [ ] **TC-174**: Tracking record is updated at "in_progress" stage
  - **URL**: `POST {BASE_URL}/tickets/{eventSlug}/register`
  - **Verify**: Check `ticket_registration_tracking.status` = 'in_progress'

- [ ] **TC-175**: Tracking record is updated at "preview_viewed" stage
  - **URL**: `{BASE_URL}/tickets/{eventSlug}/preview`
  - **Verify**: Check `ticket_registration_tracking.status` = 'preview_viewed'

- [ ] **TC-176**: Tracking record is updated at "payment_completed" stage
  - **Trigger**: After successful payment callback
  - **Verify**: Check `ticket_registration_tracking.status` = 'payment_completed'

- [ ] **TC-177**: Tracking record is updated at "payment_failed" stage
  - **Trigger**: After failed payment callback
  - **Verify**: Check `ticket_registration_tracking.status` = 'payment_failed'

- [ ] **TC-178**: Registration data is stored in JSON format
  - **Verify**: Check `ticket_registration_tracking.registration_data` field

- [ ] **TC-179**: Calculated totals are stored correctly
  - **Verify**: Check `ticket_registration_tracking.calculated_total` field

- [ ] **TC-180**: Timestamps are recorded correctly
  - **Verify**: Check `ticket_registration_tracking.created_at` and `updated_at`

---

## 11. Integration Tests

### 11.1 End-to-End Flow - Indian User
- [ ] **TC-181**: Complete flow: Register → Preview → Payment → CCAvenue → Success → Confirmation
- [ ] **TC-182**: All data persists correctly through the flow
- [ ] **TC-183**: All emails are sent at correct stages
- [ ] **TC-184**: All database records are created correctly

### 11.2 End-to-End Flow - International User
- [ ] **TC-185**: Complete flow: Register → Preview → Payment → PayPal → Success → Confirmation
- [ ] **TC-186**: All data persists correctly through the flow
- [ ] **TC-187**: All emails are sent at correct stages
- [ ] **TC-188**: All database records are created correctly

### 11.3 Payment Retry Flow
- [ ] **TC-189**: User can retry payment after failure
- [ ] **TC-190**: User can retry payment after cancellation
- [ ] **TC-191**: Multiple payment attempts are tracked correctly

---

## 12. Security Tests

### 12.1 Access Control
- [ ] **TC-192**: Payment page requires valid TIN
- [ ] **TC-193**: Invalid TIN shows error
- [ ] **TC-194**: Already paid orders cannot be paid again
- [ ] **TC-195**: CSRF protection works on forms

### 12.2 Data Validation
- [ ] **TC-196**: SQL injection attempts are blocked
- [ ] **TC-197**: XSS attempts are sanitized
- [ ] **TC-198**: Phone number format is validated
- [ ] **TC-199**: Email format is validated

---

## 13. Performance Tests

### 13.1 Load Testing
- [ ] **TC-200**: Multiple concurrent registrations work
- [ ] **TC-201**: Multiple concurrent payments work
- [ ] **TC-202**: GST API rate limiting works correctly
- [ ] **TC-203**: Database queries are optimized

---

## Test Data Requirements

### Test Users
1. **Indian User**
   - Nationality: Indian
   - Phone: +91-XXXXXXXXXX
   - Currency: INR
   - Gateway: CCAvenue

2. **International User**
   - Nationality: International
   - Phone: +1-XXXXXXXXXX (or any non-Indian)
   - Currency: USD
   - Gateway: PayPal

### Test Payment Accounts
1. **CCAvenue Test Account** (if available)
2. **PayPal Sandbox Accounts**
   - Personal buyer account
   - Business merchant account

### Test GST Numbers
- Valid GSTIN for testing
- Invalid GSTIN for error testing

---

## Priority Test Cases (Must Test First)

### Critical Path
1. TC-001 to TC-026: Registration form flow
2. TC-027 to TC-042: Preview page with correct currency
3. TC-057 to TC-061: Payment gateway selection
4. TC-062 to TC-076: CCAvenue payment (Indian)
5. TC-088 to TC-106: PayPal payment (International)
6. TC-117 to TC-130: Confirmation page
7. TC-131 to TC-145: Email sending

### High Priority
- TC-009 to TC-014: Phone number handling
- TC-015 to TC-022: GST validation
- TC-159 to TC-161: Duplicate payment prevention
- TC-170 to TC-172: Currency/gateway enforcement

---

## Notes for Testers

1. **PayPal Sandbox**: Ensure sandbox mode is enabled in config
2. **Currency**: Always verify currency symbol matches nationality
3. **Email**: Check both user inbox and spam folder
4. **Logs**: Check Laravel logs for any errors
5. **Database**: Verify all records are created correctly
6. **Session**: Test with and without session data
7. **Network**: Test with slow/fast connections
8. **Browser**: Test on different browsers (Chrome, Firefox, Safari)

---

## Expected Results Summary

- ✅ Indian users → CCAvenue → INR currency
- ✅ International users → PayPal → USD currency
- ✅ Phone numbers stored as `+CC-NUMBER` format
- ✅ Processing charges: 3% (Indian), 9% (International)
- ✅ Emails sent only to user (no admin BCC)
- ✅ All payment attempts recorded in database
- ✅ Payment page accessible via TIN in URL
- ✅ Confirmation page shows payment details if paid
