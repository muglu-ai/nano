# Ticket System Architecture Explanation

## 1. EventDay vs Events Table Dates

### Why EventDay is Needed

The `events` table has `start_date` and `end_date` which define the overall event period. However, `EventDay` serves a different purpose:

**Events Table:**
- `start_date`: When the event begins (e.g., 2026-11-19)
- `end_date`: When the event ends (e.g., 2026-11-21)
- This is the **overall event duration**

**EventDay Table:**
- Allows **granular day-based access control**
- Example: A 3-day event might have:
  - Day 1: "Opening Day" (2026-11-19)
  - Day 2: "Main Conference" (2026-11-20)
  - Day 3: "VIP Day" (2026-11-21)
  - Special: "Workshop Day" (2026-11-20) - separate from main conference

**Use Cases:**
1. **Day-Specific Tickets**: Some ticket types might only allow access to Day 1 and Day 2, but not Day 3
2. **VIP Access**: VIP tickets might have access to all days, while regular tickets only to Day 1-2
3. **Workshop Tickets**: Separate workshop tickets that only work on specific days
4. **Flexible Scheduling**: If event structure changes, you can add/remove days without changing the main event dates

**Example Scenario:**
```
Event: Bengaluru Tech Summit (Nov 19-21, 2026)

EventDays:
- Day 1: Opening Ceremony (Nov 19)
- Day 2: Main Conference (Nov 20)
- Day 3: Closing & Networking (Nov 21)

Ticket Types:
- Regular Delegate: Access to Day 1, Day 2 (not Day 3)
- VIP Delegate: Access to Day 1, Day 2, Day 3
- Workshop Only: Access to Day 2 only (workshop sessions)
```

**If you don't need day-based access control**, you can simplify by:
- Auto-generating EventDays from start_date to end_date
- Or removing EventDay and using a simpler approach

## 2. Ticket Registration, Category, Subcategory Structure

### Current Structure:
```
TicketRegistrationCategory (Registration Category)
  └─> Used for: Delegate/Visitor/VIP/Student classification
  └─> Purpose: Segmentation and business rules

TicketCategory (Ticket Category)
  └─> Used for: Delegate/VIP/Workshop grouping
  └─> Purpose: High-level ticket grouping

TicketSubcategory (Ticket Subcategory)
  └─> Used for: Member/Non-member/Student pricing tiers
  └─> Purpose: Pricing differentiation within a category

TicketType (Actual Sellable Ticket)
  └─> Links to: Category + Subcategory
  └─> Has: Pricing, capacity, sale windows
```

### Is This the Best Approach?

**Pros:**
- Very flexible - supports complex pricing structures
- Can handle multiple dimensions (category + subcategory + registration category)
- Good for events with member/non-member pricing

**Cons:**
- Can be complex for simple events
- More tables to manage
- Might be overkill if you don't need subcategory pricing

### Alternative Simplified Approach:

**Option 1: Remove Subcategory**
```
TicketCategory → TicketType
- Simpler structure
- Pricing differences handled at TicketType level
- Good if you don't need member/non-member pricing
```

**Option 2: Flatten Everything**
```
TicketType only
- All attributes in one table
- Simpler but less flexible
- Good for very simple events
```

**Recommendation:**
- **Keep current structure** if you need:
  - Member vs Non-member pricing
  - Complex pricing tiers
  - Association-based discounts
  
- **Simplify** if:
  - All tickets have same pricing structure
  - No member/non-member distinction needed
  - Simpler event requirements

## 3. Early Bird Pricing & Reminders

### Implementation:
- `TicketType` now has:
  - `early_bird_price`: Discounted price
  - `regular_price`: Standard price after early bird ends
  - `early_bird_end_date`: When early bird pricing expires
  - `early_bird_reminder_sent`: Track if reminder was sent

- `TicketEarlyBirdReminder` table:
  - Tracks reminder history
  - Links to sales team users
  - Supports email/notification reminders

### Reminder Logic:
- System checks 7 days before `early_bird_end_date`
- Sends reminder to sales team if not already sent
- Can be automated via cron job

## 4. GST & Processing Charges in TicketOrderItem

### Current Implementation:
`TicketOrderItem` now includes:
- `subtotal`: Quantity × unit_price
- `gst_rate`: GST percentage (e.g., 18)
- `gst_amount`: Calculated GST
- `processing_charge_rate`: Processing fee percentage (3% or 9%)
- `processing_charge_amount`: Calculated processing fee
- `total`: Final amount (subtotal + GST + processing)
- `pricing_type`: Snapshot of 'early_bird' or 'regular'

### Why in OrderItem?
- Each item can have different rates
- Historical snapshot of pricing at time of purchase
- Easier to calculate totals and generate receipts
- Supports item-level discounts

## 5. TicketPayment - Multiple Orders & PG Logging

### Changes Made:
- `order_ids_json`: Array of order IDs (supports multiple orders in one payment)
- `pg_request_json`: Full request sent to payment gateway
- `pg_response_json`: Full response from payment gateway
- `pg_webhook_json`: Webhook payload received

### Use Cases:
1. **Multiple Orders**: User can pay for multiple orders in one transaction
2. **Audit Trail**: Complete logging of all PG interactions
3. **Debugging**: Easy to troubleshoot payment issues
4. **Reconciliation**: Match PG responses with our records

### Example:
```json
{
  "order_ids_json": [1, 2, 3],
  "amount": 15000,
  "pg_request_json": {
    "merchant_id": "...",
    "amount": "15000",
    "order_id": "TKT-123"
  },
  "pg_response_json": {
    "status": "success",
    "transaction_id": "TXN123"
  }
}
```

