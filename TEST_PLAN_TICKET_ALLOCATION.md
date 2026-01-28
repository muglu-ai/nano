# Test Plan: Ticket Allocation System

## Overview
This document outlines comprehensive test cases for the new centralized ticket allocation system, including rule-based auto-allocation, invitation management, and admin configuration.

**Date Created:** January 28, 2026  
**Version:** 1.0  
**Status:** Ready for Testing

---

## Table of Contents
1. [Pre-Testing Setup](#pre-testing-setup)
2. [Database Migration Tests](#database-migration-tests)
3. [Helper Class Tests](#helper-class-tests)
4. [Controller Tests](#controller-tests)
5. [Admin Interface Tests](#admin-interface-tests)
6. [Exhibitor Panel Tests](#exhibitor-panel-tests)
7. [Integration Tests](#integration-tests)
8. [Edge Cases & Error Handling](#edge-cases--error-handling)
9. [Performance Tests](#performance-tests)
10. [Regression Tests](#regression-tests)

---

## Pre-Testing Setup

### Prerequisites
- [ ] Database migrations run successfully
- [ ] At least one event exists in the system
- [ ] At least one ticket type exists (preferably multiple types)
- [ ] Test user accounts created:
  - [ ] Super Admin user
  - [ ] Admin user
  - [ ] Exhibitor user (with approved application)
  - [ ] Exhibitor user (without application)
- [ ] Test application created with:
  - [ ] Different booth sizes (3-8, 9-11, 12-14, 15-17, 18-26, 27-29, 30-36, 37-53, 54-71, 72-81, 82-135 sqm)
  - [ ] Both `exhibitor-registration` and `startup-zone` application types

### Test Data Requirements
- [ ] Create test ticket types:
  - [ ] Exhibitor Pass (stall manning)
  - [ ] Complimentary Delegate Pass
  - [ ] Custom ticket type marked as `is_exhibitor_only = true`
- [ ] Create test allocation rules (via seed migration or admin interface)

---

## Database Migration Tests

### Test Case 1.1: Invitation Status Migration
**Objective:** Verify status fields are added to `complimentary_delegates` and `stall_manning` tables

**Steps:**
1. Run migration: `php artisan migrate`
2. Check database schema for both tables

**Expected Results:**
- [ ] `status` column exists (enum: 'pending', 'accepted', 'cancelled')
- [ ] `cancelled_at` column exists (nullable timestamp)
- [ ] `cancelled_by` column exists (nullable foreign key to users)
- [ ] Index on `status` column exists
- [ ] Default value for `status` is 'pending'

**Test Data:**
```sql
-- Verify existing records have default status
SELECT status FROM complimentary_delegates LIMIT 5;
SELECT status FROM stall_manning LIMIT 5;
```

---

### Test Case 1.2: Exhibitor-Only Flag Migration
**Objective:** Verify `is_exhibitor_only` flag is added to `ticket_categories` table

**Steps:**
1. Run migration
2. Check database schema

**Expected Results:**
- [ ] `is_exhibitor_only` column exists (boolean)
- [ ] Default value is `false`
- [ ] Index on `is_exhibitor_only` exists

---

### Test Case 1.3: Ticket Allocation Rules Table Migration
**Objective:** Verify `ticket_allocation_rules` table is created correctly

**Steps:**
1. Run migration
2. Check database schema

**Expected Results:**
- [ ] Table `ticket_allocation_rules` exists
- [ ] Columns: `id`, `event_id`, `application_type`, `booth_area_min`, `booth_area_max`, `ticket_allocations` (JSON), `sort_order`, `is_active`, `timestamps`
- [ ] Foreign key constraint on `event_id` to `events` table
- [ ] Indexes on `[event_id, application_type, is_active]` and `[booth_area_min, booth_area_max]`

---

### Test Case 1.4: Seed Default Allocation Rules
**Objective:** Verify default allocation rules are seeded correctly

**Steps:**
1. Run migration (includes seed)
2. Query `ticket_allocation_rules` table

**Expected Results:**
- [ ] At least 11 rules created for each application type (null, 'exhibitor-registration', 'startup-zone')
- [ ] Rules cover booth area ranges: 3-8, 9-11, 12-14, 15-17, 18-26, 27-29, 30-36, 37-53, 54-71, 72-81, 82-135
- [ ] Rules have correct `sort_order` values
- [ ] Rules are active if ticket types are configured, inactive otherwise

**Test Query:**
```sql
SELECT COUNT(*) as rule_count, application_type 
FROM ticket_allocation_rules 
GROUP BY application_type;
```

---

## Helper Class Tests

### Test Case 2.1: Ticket Allocation - Basic Allocation
**Objective:** Test basic ticket allocation functionality

**Steps:**
1. Create a test application
2. Call `TicketAllocationHelper::allocate($applicationId, [1 => 5, 2 => 3])` where 1 and 2 are ticket type IDs
3. Verify `exhibition_participants` record

**Expected Results:**
- [ ] `exhibition_participants` record created/updated
- [ ] `ticketAllocation` JSON contains: `{"1": 5, "2": 3}`
- [ ] Returns `ExhibitionParticipant` model instance

**Test Code:**
```php
$allocation = TicketAllocationHelper::allocate(1, [1 => 5, 2 => 3]);
$participant = ExhibitionParticipant::where('application_id', 1)->first();
assert(json_decode($participant->ticketAllocation, true) === [1 => 5, 2 => 3]);
```

---

### Test Case 2.2: Ticket Allocation - Validation
**Objective:** Test ticket type validation

**Steps:**
1. Try to allocate with invalid ticket type IDs
2. Try to allocate with inactive ticket types
3. Try to allocate with negative counts

**Expected Results:**
- [ ] Invalid ticket type IDs throw exception or return error
- [ ] Inactive ticket types are rejected
- [ ] Negative counts are rejected

---

### Test Case 2.3: Get Allocation
**Objective:** Test retrieving allocation details

**Steps:**
1. Create allocation with multiple ticket types
2. Call `TicketAllocationHelper::getAllocation($applicationId)`

**Expected Results:**
- [ ] Returns array with ticket type IDs as keys
- [ ] Each entry contains: `name`, `count`, `slug`, `category`, `subcategory`
- [ ] Empty allocation returns empty array

---

### Test Case 2.4: Get Counts From Allocation
**Objective:** Test count calculations from JSON

**Steps:**
1. Create allocation with known ticket types
2. Create some invitations (pending, accepted, cancelled)
3. Call `TicketAllocationHelper::getCountsFromAllocation($applicationId)`

**Expected Results:**
- [ ] Returns array with:
  - `total_allocated`: sum of all counts
  - `stall_manning_count`: sum of exhibitor pass types
  - `complimentary_delegate_count`: sum of delegate pass types
  - `by_ticket_type`: detailed breakdown with `allocated`, `used`, `cancelled`, `available`
- [ ] Cancelled invitations are excluded from `used` count
- [ ] Available = allocated - used - cancelled

---

### Test Case 2.5: Available Slots Calculation
**Objective:** Test available slot calculation

**Steps:**
1. Allocate 5 tickets of type X
2. Create 2 pending invitations
3. Create 1 accepted invitation
4. Create 1 cancelled invitation
5. Call `TicketAllocationHelper::getAvailableSlots($applicationId, $ticketTypeId)`

**Expected Results:**
- [ ] Available slots = 5 - 2 (pending) - 1 (accepted) = 2
- [ ] Cancelled invitations don't count against available slots

---

### Test Case 2.6: Can Invite Validation
**Objective:** Test invitation validation

**Steps:**
1. Allocate 3 tickets
2. Create 3 accepted invitations
3. Try to invite one more
4. Cancel one invitation
5. Try to invite again

**Expected Results:**
- [ ] First attempt (3/3 used) returns `['can_invite' => false]`
- [ ] After cancellation, returns `['can_invite' => true, 'available' => 1]`

---

### Test Case 2.7: Cancel Invitation
**Objective:** Test invitation cancellation

**Steps:**
1. Create a pending invitation
2. Call `TicketAllocationHelper::cancelInvitation($invitationId, 'complimentary_delegate', $userId)`
3. Verify database record

**Expected Results:**
- [ ] `status` updated to 'cancelled'
- [ ] `cancelled_at` timestamp set
- [ ] `cancelled_by` set to user ID
- [ ] Returns `true`
- [ ] Available slots increase by 1

---

### Test Case 2.8: Calculate Allocation From Booth Area
**Objective:** Test rule-based allocation calculation

**Steps:**
1. Create allocation rules for booth area 12-14 sqm
2. Call `TicketAllocationHelper::calculateAllocationFromBoothArea(13, $eventId, 'exhibitor-registration')`
3. Verify returned allocations match rule

**Expected Results:**
- [ ] Returns array with `ticket_allocations` matching rule
- [ ] Handles string booth area values (e.g., "13.5")
- [ ] Returns empty array if no matching rule found
- [ ] Prioritizes event-specific rules over general rules

**Test Cases:**
- Booth area 5 sqm → matches 3-8 range
- Booth area 10 sqm → matches 9-11 range
- Booth area 13 sqm → matches 12-14 range
- Booth area 200 sqm → no match (empty array)

---

### Test Case 2.9: Auto Allocate After Payment
**Objective:** Test automatic allocation after payment

**Steps:**
1. Create application with booth area 15 sqm
2. Simulate payment success
3. Call `TicketAllocationHelper::autoAllocateAfterPayment($applicationId, 15, $eventId, 'exhibitor-registration')`
4. Verify allocation

**Expected Results:**
- [ ] `exhibition_participants` record created/updated
- [ ] `ticketAllocation` JSON matches rule for 15-17 sqm range
- [ ] Returns `ExhibitionParticipant` instance
- [ ] Handles null booth area gracefully

---

### Test Case 2.10: Get Exhibitor Ticket Types
**Objective:** Test filtering exhibitor-only ticket types

**Steps:**
1. Create ticket types with `is_exhibitor_only = true` and `false`
2. Call `TicketAllocationHelper::getExhibitorTicketTypes($eventId)`

**Expected Results:**
- [ ] Returns only ticket types where `is_exhibitor_only = true`
- [ ] Filters by event if `$eventId` provided
- [ ] Returns only active ticket types

---

## Controller Tests

### Test Case 3.1: ExhibitorController - Check Count
**Objective:** Test count checking endpoint

**Steps:**
1. Login as exhibitor
2. Create allocation
3. Call `/exhibitor/check-count` endpoint
4. Verify response

**Expected Results:**
- [ ] Returns JSON with allocation details
- [ ] Includes `ticket_allocation` array with ticket type details
- [ ] Includes `stall_manning_count` and `complimentary_delegate_count` calculated from JSON
- [ ] No longer uses old column values

---

### Test Case 3.2: ExhibitorController - Invite
**Objective:** Test invitation creation

**Steps:**
1. Login as exhibitor
2. Allocate 5 tickets
3. Send POST to `/exhibitor/invite` with valid data
4. Verify invitation created

**Expected Results:**
- [ ] Invitation record created with `status = 'pending'`
- [ ] `ticket_type_id` set correctly
- [ ] Email sent to invitee
- [ ] Available slots decrease
- [ ] Returns success response

**Test Scenarios:**
- [ ] Invite when slots available → success
- [ ] Invite when no slots available → error
- [ ] Invite with invalid ticket type → error
- [ ] Invite with duplicate email → error (unless cancelled)

---

### Test Case 3.3: ExhibitorController - Cancel Invitation
**Objective:** Test invitation cancellation

**Steps:**
1. Create pending invitation
2. Login as exhibitor
3. Send POST to `/exhibitor/invite/cancel` with invitation ID
4. Verify cancellation

**Expected Results:**
- [ ] Invitation status updated to 'cancelled'
- [ ] `cancelled_at` and `cancelled_by` set
- [ ] Available slots increase
- [ ] Returns success message
- [ ] Cannot cancel already cancelled invitation

---

### Test Case 3.4: PassesController - Update Allocation
**Objective:** Test admin allocation update

**Steps:**
1. Login as admin
2. Send POST to `/admin/passes/update-allocation` with ticket allocations
3. Verify allocation updated

**Expected Results:**
- [ ] `ticketAllocation` JSON updated
- [ ] Counts calculated correctly
- [ ] Old column values not updated (deprecated)
- [ ] Returns updated counts

---

### Test Case 3.5: PassesController - Auto Allocate
**Objective:** Test auto-allocation from admin panel

**Steps:**
1. Login as admin
2. Create application with booth area
3. Send POST to `/admin/passes/auto-allocate` with application ID
4. Verify allocation

**Expected Results:**
- [ ] Allocation created based on booth area
- [ ] Uses rule-based calculation
- [ ] Returns allocation details

---

### Test Case 3.6: PaymentGatewayController - Auto Allocation After Payment
**Objective:** Test automatic allocation after CC Avenue payment

**Steps:**
1. Create exhibitor-registration application
2. Simulate successful payment callback
3. Verify allocation created automatically

**Expected Results:**
- [ ] Allocation created immediately after payment success
- [ ] Uses booth area from application
- [ ] Uses correct application type
- [ ] No manual intervention required

**Test Scenarios:**
- [ ] Exhibitor-registration payment → allocation created
- [ ] Startup-zone payment → allocation created
- [ ] Payment without booth area → handled gracefully

---

### Test Case 3.7: PayPalController - Auto Allocation After Payment
**Objective:** Test automatic allocation after PayPal payment

**Steps:**
1. Create startup-zone application
2. Simulate successful PayPal payment callback
3. Verify allocation created automatically

**Expected Results:**
- [ ] Same as Test Case 3.6
- [ ] Works for PayPal payment flow

---

### Test Case 3.8: DashboardController - Ticket Summary
**Objective:** Test dashboard ticket display

**Steps:**
1. Login as exhibitor
2. Create allocation with multiple ticket types
3. Visit dashboard
4. Verify ticket cards displayed

**Expected Results:**
- [ ] Cards show all allocated ticket types
- [ ] Displays used/total counts
- [ ] Links to invitation list work
- [ ] No errors if no allocation exists

---

## Admin Interface Tests

### Test Case 4.1: List Allocation Rules
**Objective:** Test viewing allocation rules

**Steps:**
1. Login as admin/super-admin
2. Navigate to `/admin/ticket-allocation-rules`
3. Verify list displays

**Expected Results:**
- [ ] Rules listed in table
- [ ] Filters work (event, application type, status)
- [ ] Pagination works
- [ ] Ticket allocations displayed correctly

---

### Test Case 4.2: Create Allocation Rule
**Objective:** Test creating new allocation rule

**Steps:**
1. Login as admin
2. Navigate to create form
3. Fill form:
   - Event (optional)
   - Application type (optional)
   - Booth area range: 20-25 sqm
   - Ticket allocations: Type A = 3, Type B = 2
   - Sort order: 10
   - Active: Yes
4. Submit form

**Expected Results:**
- [ ] Rule created successfully
- [ ] Validation prevents overlapping ranges
- [ ] JSON stored correctly
- [ ] Redirects to list page
- [ ] Success message displayed

**Validation Tests:**
- [ ] `booth_area_max` must be >= `booth_area_min`
- [ ] At least one ticket type with count > 0 required
- [ ] Overlapping ranges rejected (for same event/type)

---

### Test Case 4.3: Edit Allocation Rule
**Objective:** Test editing existing rule

**Steps:**
1. Select existing rule
2. Click Edit
3. Modify booth area range or allocations
4. Submit

**Expected Results:**
- [ ] Form pre-populated with current values
- [ ] Updates saved correctly
- [ ] Validation works (no overlapping ranges)
- [ ] Success message displayed

---

### Test Case 4.4: Delete Allocation Rule
**Objective:** Test deleting rule

**Steps:**
1. Select rule
2. Click Delete
3. Confirm deletion

**Expected Results:**
- [ ] Confirmation dialog shown
- [ ] Rule deleted from database
- [ ] Success message displayed
- [ ] Existing allocations not affected

---

### Test Case 4.5: Preview Allocation
**Objective:** Test allocation preview feature

**Steps:**
1. Login as admin
2. Navigate to create/edit form
3. Enter booth area: 13 sqm
4. Click Preview (if implemented)
5. Verify preview shows expected allocations

**Expected Results:**
- [ ] Shows matching rule
- [ ] Displays ticket types and counts
- [ ] Handles no-match scenario

---

## Exhibitor Panel Tests

### Test Case 5.1: View Ticket Allocations
**Objective:** Test exhibitor viewing their allocations

**Steps:**
1. Login as exhibitor
2. Navigate to dashboard
3. View "Your Registration Passes" section

**Expected Results:**
- [ ] Cards display for each ticket type
- [ ] Shows allocated count
- [ ] Shows used count
- [ ] Shows remaining count
- [ ] Links to invitation list work

---

### Test Case 5.2: View Invitation List
**Objective:** Test viewing invitations

**Steps:**
1. Login as exhibitor
2. Create some invitations (pending, accepted, cancelled)
3. Navigate to invitation list
4. Verify display

**Expected Results:**
- [ ] All invitations listed
- [ ] Status badges displayed (Pending, Accepted, Cancelled)
- [ ] Cancel button shown for non-cancelled invitations
- [ ] Cancelled invitations show cancellation message
- [ ] Sorting works
- [ ] Pagination works

---

### Test Case 5.3: Send Invitation
**Objective:** Test sending invitation

**Steps:**
1. Login as exhibitor
2. Navigate to invitation form
3. Fill form with valid data
4. Submit

**Expected Results:**
- [ ] Invitation created with status 'pending'
- [ ] Email sent to invitee
- [ ] Available slots decrease
- [ ] Success message displayed
- [ ] Validation prevents over-allocation

**Validation Tests:**
- [ ] Email required and valid format
- [ ] Name required
- [ ] Cannot exceed available slots
- [ ] Duplicate email rejected (unless previous cancelled)

---

### Test Case 5.4: Cancel Invitation
**Objective:** Test cancelling invitation from exhibitor panel

**Steps:**
1. Create pending invitation
2. Login as exhibitor
3. Navigate to invitation list
4. Click Cancel button
5. Confirm cancellation

**Expected Results:**
- [ ] Confirmation dialog shown
- [ ] Invitation status updated to 'cancelled'
- [ ] Available slots increase
- [ ] Success message displayed
- [ ] Invitation shows as cancelled in list

---

### Test Case 5.5: Invitation Status Display
**Objective:** Test invitation status visibility

**Steps:**
1. Create invitations with different statuses
2. View invitation list
3. Verify status display

**Expected Results:**
- [ ] Pending: Yellow badge, Cancel button available
- [ ] Accepted: Green badge, Cancel button available
- [ ] Cancelled: Red badge, "Your invitation has been cancelled" message, No cancel button

---

## Integration Tests

### Test Case 6.1: Complete Registration Flow
**Objective:** Test end-to-end registration and allocation

**Steps:**
1. Create new exhibitor registration
2. Complete payment
3. Verify automatic allocation
4. Login as exhibitor
5. View allocations
6. Send invitations
7. Verify invitations work

**Expected Results:**
- [ ] Payment triggers allocation
- [ ] Allocation matches booth area rule
- [ ] Exhibitor can view and use allocations
- [ ] Invitations work correctly

---

### Test Case 6.2: Startup Zone Flow
**Objective:** Test startup zone registration and allocation

**Steps:**
1. Create startup-zone application
2. Complete payment (PayPal or CC Avenue)
3. Verify allocation
4. Test invitation functionality

**Expected Results:**
- [ ] Same as Test Case 6.1
- [ ] Uses startup-zone specific rules if configured

---

### Test Case 6.3: Admin Override Flow
**Objective:** Test admin manually updating allocations

**Steps:**
1. Admin views application
2. Updates ticket allocation
3. Exhibitor views updated allocation
4. Exhibitor uses new allocation

**Expected Results:**
- [ ] Admin can update allocation
- [ ] Changes reflect immediately
- [ ] Exhibitor sees updated counts
- [ ] Available slots recalculated

---

### Test Case 6.4: Rule Update Impact
**Objective:** Test impact of rule changes on new allocations

**Steps:**
1. Create rule for 10-15 sqm: 2 tickets
2. Create application with 12 sqm booth
3. Complete payment → allocation = 2 tickets
4. Update rule to 3 tickets
5. Create new application with 12 sqm booth
6. Complete payment → allocation = 3 tickets

**Expected Results:**
- [ ] Existing allocations not affected
- [ ] New allocations use updated rule
- [ ] No conflicts or errors

---

## Edge Cases & Error Handling

### Test Case 7.1: Empty Allocation
**Objective:** Test handling of empty/null allocations

**Steps:**
1. Create application without allocation
2. Try to view allocations
3. Try to send invitation

**Expected Results:**
- [ ] Dashboard shows no allocation (no errors)
- [ ] Invitation form shows 0 available slots
- [ ] No PHP errors or exceptions

---

### Test Case 7.2: Invalid Ticket Type
**Objective:** Test handling of invalid ticket type IDs

**Steps:**
1. Try to allocate with non-existent ticket type ID
2. Try to invite with invalid ticket type

**Expected Results:**
- [ ] Validation error returned
- [ ] No database corruption
- [ ] Clear error message

---

### Test Case 7.3: Booth Area Edge Cases
**Objective:** Test boundary conditions for booth area

**Steps:**
1. Test with booth area exactly at range boundaries:
   - 3 sqm (min of 3-8 range)
   - 8 sqm (max of 3-8 range)
   - 9 sqm (min of 9-11 range)
2. Test with booth area between ranges: 8.5 sqm
3. Test with very large booth area: 200 sqm
4. Test with null/empty booth area

**Expected Results:**
- [ ] Boundary values match correct range
- [ ] Values between ranges handled (no match or closest match)
- [ ] Very large values handled gracefully
- [ ] Null values don't cause errors

---

### Test Case 7.4: Concurrent Invitations
**Objective:** Test race conditions with simultaneous invitations

**Steps:**
1. Allocate 1 ticket
2. Two users try to invite simultaneously
3. Verify only one succeeds

**Expected Results:**
- [ ] Database constraints prevent over-allocation
- [ ] One invitation succeeds, one fails with clear error
- [ ] No data corruption

---

### Test Case 7.5: Cancelled Invitation Reuse
**Objective:** Test reusing slot after cancellation

**Steps:**
1. Allocate 1 ticket
2. Create and accept invitation
3. Cancel invitation
4. Create new invitation

**Expected Results:**
- [ ] Cancelled invitation doesn't block new invitation
- [ ] Available slots correctly calculated
- [ ] New invitation succeeds

---

### Test Case 7.6: Multiple Ticket Types
**Objective:** Test allocation with multiple ticket types

**Steps:**
1. Allocate 3 different ticket types
2. Create invitations for each type
3. Verify counts are separate

**Expected Results:**
- [ ] Each ticket type tracked independently
- [ ] Counts don't mix between types
- [ ] Available slots calculated per type

---

### Test Case 7.7: Co-Exhibitor Allocations
**Objective:** Test allocations for co-exhibitors

**Steps:**
1. Create co-exhibitor
2. Allocate tickets to co-exhibitor
3. Verify separate allocation tracking

**Expected Results:**
- [ ] Co-exhibitor allocations separate from main exhibitor
- [ ] Counts calculated correctly
- [ ] Invitations work independently

---

## Performance Tests

### Test Case 8.1: Large Allocation Rules
**Objective:** Test performance with many rules

**Steps:**
1. Create 100+ allocation rules
2. Query rules for booth area
3. Measure response time

**Expected Results:**
- [ ] Query completes in < 1 second
- [ ] Indexes used effectively
- [ ] No N+1 query problems

---

### Test Case 8.2: Many Invitations
**Objective:** Test performance with many invitations

**Steps:**
1. Create application with 100 allocated tickets
2. Create 100 invitations
3. Load invitation list
4. Measure response time

**Expected Results:**
- [ ] List loads in < 2 seconds
- [ ] Pagination works efficiently
- [ ] Count calculations optimized

---

### Test Case 8.3: Dashboard Load Time
**Objective:** Test dashboard performance

**Steps:**
1. Create complex allocation (multiple types, many invitations)
2. Load exhibitor dashboard
3. Measure load time

**Expected Results:**
- [ ] Dashboard loads in < 3 seconds
- [ ] Ticket summary calculated efficiently
- [ ] No unnecessary queries

---

## Regression Tests

### Test Case 9.1: Existing Applications
**Objective:** Verify existing applications still work

**Steps:**
1. Find existing application with old allocation format
2. View dashboard
3. Try to use existing invitations

**Expected Results:**
- [ ] No errors displayed
- [ ] System handles old format gracefully
- [ ] Migration path available

---

### Test Case 9.2: Existing Invitations
**Objective:** Verify existing invitations still work

**Steps:**
1. Find existing invitation (created before migration)
2. View invitation list
3. Test invitation acceptance

**Expected Results:**
- [ ] Invitations display correctly
- [ ] Status defaults to 'pending' if null
- [ ] Acceptance works

---

### Test Case 9.3: Payment Flows
**Objective:** Verify payment flows still work

**Steps:**
1. Test CC Avenue payment
2. Test PayPal payment
3. Verify allocations created

**Expected Results:**
- [ ] Payment processing unchanged
- [ ] Allocations created automatically
- [ ] No payment failures

---

## Test Execution Checklist

### Phase 1: Database & Core Functionality
- [ ] Run all migrations
- [ ] Test helper class methods
- [ ] Verify model relationships

### Phase 2: Controllers & API
- [ ] Test all controller endpoints
- [ ] Verify request/response formats
- [ ] Test error handling

### Phase 3: Admin Interface
- [ ] Test CRUD operations
- [ ] Verify validation
- [ ] Test filters and search

### Phase 4: Exhibitor Panel
- [ ] Test dashboard display
- [ ] Test invitation management
- [ ] Test cancellation flow

### Phase 5: Integration
- [ ] Test complete registration flow
- [ ] Test payment integration
- [ ] Test rule updates

### Phase 6: Edge Cases
- [ ] Test error scenarios
- [ ] Test boundary conditions
- [ ] Test concurrent operations

### Phase 7: Performance
- [ ] Test with large datasets
- [ ] Verify query optimization
- [ ] Test load times

### Phase 8: Regression
- [ ] Test existing functionality
- [ ] Verify backward compatibility
- [ ] Test migration scenarios

---

## Known Issues & Limitations

### Current Limitations
1. **Old Column Deprecation:** `stall_manning_count` and `complimentary_delegate_count` columns still exist but are deprecated. They should not be updated but may still be read during migration period.

2. **Rule Overlap:** System prevents overlapping rules for same event/type, but doesn't prevent gaps. Admin must ensure complete coverage.

3. **Ticket Type Mapping:** System attempts to auto-detect exhibitor/delegate ticket types in seed migration, but admin may need to manually configure.

### Future Enhancements
1. Bulk invitation import
2. Invitation reminder emails
3. Allocation history/audit log
4. Advanced rule conditions (sector-based, etc.)
5. Automated testing suite

---

## Test Environment Setup

### Required Environment Variables
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bts_portal_test
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=...
```

### Test Database
- Create separate test database
- Run migrations on test database
- Seed test data

### Test Users
Create test users with different roles:
- Super Admin: `superadmin@test.com`
- Admin: `admin@test.com`
- Exhibitor: `exhibitor@test.com`

---

## Sign-Off

**Tested By:** _________________  
**Date:** _________________  
**Status:** ☐ Pass  ☐ Fail  ☐ Partial  
**Notes:** _________________

---

## Appendix: Test Data Scripts

### Create Test Ticket Types
```php
// Run in tinker or seeder
$event = Events::first();
$category = TicketCategory::first();

TicketType::create([
    'event_id' => $event->id,
    'category_id' => $category->id,
    'name' => 'Exhibitor Pass',
    'slug' => 'exhibitor-pass',
    'is_exhibitor_only' => true,
    'is_active' => true,
]);

TicketType::create([
    'event_id' => $event->id,
    'category_id' => $category->id,
    'name' => 'Complimentary Delegate',
    'slug' => 'complimentary-delegate',
    'is_exhibitor_only' => false,
    'is_active' => true,
]);
```

### Create Test Allocation Rules
```php
// Via admin interface or seeder
TicketAllocationRule::create([
    'event_id' => null,
    'application_type' => null,
    'booth_area_min' => 3,
    'booth_area_max' => 8,
    'ticket_allocations' => [1 => 2, 2 => 1], // Exhibitor: 2, Delegate: 1
    'sort_order' => 0,
    'is_active' => true,
]);
```

---

**Document Version:** 1.0  
**Last Updated:** January 28, 2026  
**Next Review:** After initial testing phase
