# "My Tickets" Logic Explanation

## Quick Overview

"My Tickets" shows **all tickets** that belong to registrations where the logged-in delegate is the **primary contact** (the person who created the registration).

## Database Relationships

```
TicketContact (Primary Contact)
    ↓ (has many)
TicketRegistration
    ↓ (has many)
TicketDelegate
    ↓ (has one)
Ticket
```

## How It Works

### 1. **Authentication Flow**
```php
// User logs in as delegate
$account = Auth::guard('delegate')->user();
$contact = $account->contact; // Gets the TicketContact record
```

### 2. **Ticket Query Logic** (from `DelegateDashboardController.php`)

```php
$tickets = Ticket::whereHas('delegate.registration', function ($query) use ($contact) {
    $query->where('contact_id', $contact->id);
})->with(['delegate', 'ticketType.category', 'event'])->get();
```

**What this does:**
- Finds all `Ticket` records
- Where the ticket's `delegate` belongs to a `registration`
- Where that `registration` has `contact_id` matching the logged-in user's contact ID
- Eager loads related data (delegate, ticket type, event)

### 3. **The Relationship Chain**

```
Ticket
  → belongs to TicketDelegate (via delegate_id)
    → belongs to TicketRegistration (via registration_id)
      → belongs to TicketContact (via contact_id)
```

## Key Points

### ✅ **What "My Tickets" Includes:**
- All tickets from registrations where you are the **primary contact**
- Tickets for **all delegates** in your registrations (not just your own)
- This means if you registered 5 people, you'll see all 5 tickets

### ❌ **What It Does NOT Include:**
- Tickets from registrations where you are just a delegate (not the primary contact)
- Tickets from other people's registrations where you might be listed as a delegate

## Example Scenario

**Scenario:**
- You (john@example.com) create a registration
- You add 3 delegates: Alice, Bob, and yourself
- All 3 get tickets issued

**Result:**
- "My Tickets" count: **3 tickets** (Alice's, Bob's, and yours)
- You can see and manage all 3 tickets

**Another Scenario:**
- Someone else (mary@example.com) creates a registration
- They add you as a delegate
- You get a ticket

**Result:**
- "My Tickets" count: **0 tickets** (because you're not the primary contact)
- You won't see this ticket in "My Tickets"
- But you might see it in "My Registrations" if that feature shows delegate-level registrations

## Code Location

**Controller:** `app/Http/Controllers/Delegate/DelegateDashboardController.php`
- Method: `dashboard()` (line 17-57)

**View:** `resources/views/delegate/dashboard/index.blade.php`
- Line 191: Displays the count `{{ $tickets->count() }}`
- Line 192: Label "My Tickets"
- Lines 223-244: Lists recent tickets

## Data Flow Diagram

```
User Login
    ↓
Get TicketAccount (Auth::guard('delegate')->user())
    ↓
Get TicketContact (account->contact)
    ↓
Query Tickets:
  WHERE ticket.delegate.registration.contact_id = contact.id
    ↓
Returns: All tickets from registrations where user is primary contact
    ↓
Display: Count and list of tickets
```

## Why This Design?

1. **Primary Contact = Owner**: The person who created the registration is considered the "owner" of all tickets in that registration
2. **Group Management**: Allows the primary contact to see and manage all tickets in their group
3. **Clear Ownership**: Prevents confusion about who can see/manage which tickets

## Related Features

- **"Registrations"**: Shows all registrations where you're the primary contact
- **"Receipts"**: Shows receipts for orders from your registrations
- **"Upgrades"**: Shows upgrade options for tickets in your registrations

## Summary

**"My Tickets" = All tickets from registrations where I am the primary contact**

This includes tickets for all delegates in those registrations, giving you a complete view of all tickets you're responsible for managing.
