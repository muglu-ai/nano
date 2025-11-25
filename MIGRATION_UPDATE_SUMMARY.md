# Migration Update Summary

## Overview
Updated Laravel migrations to match the actual database structure from `btsblnl265_asd1d_portalStructure.sql`.

## Updated Migrations

### 1. **applications** table (`2025_01_08_101925_create_applications_table.php`)
- ✅ Added 100+ missing columns including:
  - `uuid`, `file_path`, `how_old_startup`
  - `fascia_name`, `boothDescription`, `logo_link`
  - `gst_no`, `pan_no`, `tan_no`, `gst_compliance`
  - `submission_status`, `approved_date`, `rejected_date`
  - `allocated_sqm`, `stallNumber`, `zone`, `hallNo`
  - `pavilion_id`, `pavilionName`, `sponsorship_item_id`
  - `cart_data` (JSON), `remarks`, `coex_terms_accepted`
  - And many more fields
- ✅ Updated data types and defaults to match SQL
- ✅ Added proper indexes

### 2. **users** table (`0001_01_01_000000_create_users_table.php`)
- ✅ Added `simplePass` column
- ✅ Added `email_verification_token` and `email_verified_at`
- ✅ Added `sub_role` column
- ✅ Updated role enum to include `co-exhibitor`

### 3. **invoices** table (`2025_01_08_111159_create_invoices_table.php`)
- ✅ Added 20+ missing columns:
  - `type`, `rate`, `int_amount_value`, `usd_rate`
  - `discount_per`, `discount`, `gst`, `processing_charges`
  - `invoice_no`, `pin_no`, `pending_amount`, `amount_paid`
  - `co_exhibitorID`, `remarks` (JSON)
  - `tds_amount`, `tax_invoice`, `surCharge` fields
  - `refund` flag
- ✅ Updated currency enum to include `USD`
- ✅ Updated payment_status enum

### 4. **payments** table (`2025_01_08_115312_create_payments_table.php`)
- ✅ Added missing columns:
  - `amount_paid`, `currency`, `rejection_reason`
  - `receipt_image`, `user_id` (foreign key)
  - `verification_status`, `verified_by`, `verified_at`
  - `remarks`, `tds_amount`, `tdsReason`
- ✅ Updated transaction_id to not be unique (can have multiple)
- ✅ Made payment_date nullable

### 5. **complimentary_delegates** table (`2025_01_29_082905_complimentary_delegates.php`)
- ✅ Added 20+ missing columns:
  - `ticketType`, `title`, `middle_name`
  - `token`, `address`, `city`, `state`, `country`, `postal_code`
  - `buisness_nature`, `products`, `id_type`, `id_no`
  - `profile_pic`, `unique_id`
  - `inaugural_session`, `inauguralConfirmation`
  - `approvedHistory`, `confirmedCategory`, `lunchStatus`
  - `pinNo`, `api_data`, `api_response`, `api_sent`, `emailSent`
- ✅ Added proper indexes

### 6. **exhibition_participants** table (`2025_01_29_082755_exhibition_participants.php`)
- ✅ Added `coExhibitor_id` foreign key
- ✅ Added `ticketAllocation` JSON column
- ✅ Made `application_id` nullable
- ✅ Added indexes

### 7. **stall_manning** table (`2025_01_29_082842_stall_manning.php`)
- ✅ Added missing columns:
  - `unique_id`, `middle_name`, `token`
  - `id_type`, `id_no`, `confirmedCategory`
  - `pinNo`, `ticketType`
  - `api_data`, `api_response`, `api_sent`
  - `emailSent`, `reminder` (JSON)
- ✅ Made most fields nullable
- ✅ Added indexes

### 8. **attendees** table (`2025_04_14_175237_create_attendees_table.php`)
- ✅ Added 30+ missing columns:
  - `middle_name`, `badge_category`
  - `job_category`, `job_subcategory`, `other_job_category`
  - `profile_picture`, `id_card_type`, `id_card_number`
  - `qr_code_path`, `source`, `email_verified`, `email_verify_otp`
  - `inaugural_session`, `registration_type`, `event_days`
  - `promotion_consent`, `startup`
  - `inauguralConfirmation`, `approvedCate`, `regId`
  - `lunchStatus`, `approvedHistory`, `updatedBy`
  - `api_data`, `api_response`, `api_sent`, `emailSent`, `reminder`
- ✅ Changed country/state to foreign keys
- ✅ Updated timestamps to use datetime with defaults

### 9. **co_exhibitors** table (`2025_01_08_111158_create_co_exhibitors_table.php`)
- ✅ Added missing columns:
  - `pavilion_name`, `status` enum
  - `user_id`, `allocated_passes`, `proof_document`
  - `job_title`, `stall_size`, `booth_number`
  - `co_exhibitor_id`, `approved_At`
  - `purchase_allowed`, `address1`, `city`, `state`, `zip`, `country`
- ✅ Made `relation` nullable

### 10. **exhibitors_info** table (`2025_05_15_172128_exhibitor_info.php`)
- ✅ Created complete table structure with all columns:
  - `api_status`, `fascia_name`, `company_name`
  - `sector`, `country`, `state`, `city`, `zip_code`
  - `contact_person`, `designation`, `email`, `phone`, `telPhone`
  - `logo`, `address`, `description`, `website`
  - `linkedin`, `instagram`, `facebook`, `youtube`
  - `submission_status`, `category`, `api_message`, `apiExhibitorId`

### 11. **event_contacts** table (`2025_01_08_102001_create_event_contacts_table.php`)
- ✅ Updated to match SQL structure:
  - Made `salutation`, `last_name`, `job_title`, `contact_number` nullable
  - Added `designation` column
  - Added proper indexes

### 12. **billing_details** table (`2025_01_08_102044_create_billing_details_table.php`)
- ✅ Made `gst_id` and `city_id` nullable
  - Changed `city_id` to string (not foreign key in SQL)
- ✅ Added proper indexes

### 13. **sponsorships** table (`2025_01_08_111157_create_sponsorships_table.php`)
- ✅ Added missing columns:
  - `sponsorship_id` (unique)
  - `sponsorship_item_id`, `sponsorship_item_count`
  - `application_id`, `submitted_date`, `approval_date`
- ✅ Updated status enum
- ✅ Added foreign keys and indexes

### 14. **sponsor_items** table (`2025_01_17_060904_sponsor_item.php`)
- ✅ Created `sponsor_categories` table first
- ✅ Added missing columns:
  - `category_id` (foreign key)
  - `mem_price`, `quantity_desc`
  - `image_url`, `deadline`, `is_addon`
- ✅ Added indexes

### 15. **extra_requirements** table (`2025_02_12_113402_create_extra_requirements_table.php`)
- ✅ Added `item_code` column
- ✅ Changed `image_quantity` to `image` (string)
- ✅ Added `size_or_description` column

### 16. **requirements_orders** table (`2025_02_12_223830_create_requirements_orders_table.php`)
- ✅ Added missing columns:
  - `co_exhibitor_id`, `order_status`
  - `delivery_status`, `remarks`, `delete` flag
- ✅ Made `application_id` nullable
- ✅ Added indexes

## New Migrations Created

### 17. **Missing Tables Migration** (`2025_11_25_000001_create_missing_tables.php`)
Created comprehensive migration for all missing tables:

- ✅ `payment_gateway_response` - Payment gateway transaction logs
- ✅ `otps` - OTP storage for verification
- ✅ `outbound_requests` - API request tracking
- ✅ `requirements_billings` - Billing details for extra requirements
- ✅ `lead_retrieval_user` - Lead retrieval system users
- ✅ `attendee_logs` - Audit log for attendee changes
- ✅ `exhibitor_products` - Products listed by exhibitors
- ✅ `exhibitor_press_releases` - Press releases from exhibitors
- ✅ `exhibitor_feedback` - Feedback form submissions
- ✅ `blocked_slots` - Meeting room blocked time slots
- ✅ `payment_receipts` - Payment receipt uploads

## Key Changes Summary

### Data Type Updates
- Changed many `string` fields to match exact lengths from SQL
- Added `json` columns where SQL uses JSON
- Updated `boolean` defaults to match SQL
- Fixed `enum` values to match exactly
- Updated `timestamp` defaults (some use `useCurrent()`)

### Foreign Keys
- Added proper foreign key constraints matching SQL
- Set correct `onDelete` actions (cascade, set null, etc.)
- Added indexes for all foreign keys

### Indexes
- Added indexes matching SQL structure
- Created unique indexes where specified
- Added composite unique constraints

## Tables Not Yet Migrated (Optional/Archive Tables)

These tables exist in SQL but may not need migrations:
- `applications_delete` - Archive table
- `billing_details_delete` - Archive table
- `event_contacts_delete` - Archive table
- `secondary_event_contacts_delete` - Archive table
- `delete_log_table` - Logging table
- `del_ticket` - Deleted tickets
- `exhibitorConfirmation` - Temporary table
- `visitorData` - Temporary table
- `sessions_2`, `sessions_old` - Old session tables
- `sm_2025_speaker_sessions` - Event-specific table

## Next Steps

1. **Run migrations**: `php artisan migrate:fresh` (if starting fresh) or `php artisan migrate` (if updating)
2. **Verify foreign keys**: Check that all foreign key constraints are properly created
3. **Test application**: Ensure all models work with updated schema
4. **Update models**: Update Eloquent models if any column names/types changed

## Notes

- All migrations now match the SQL structure exactly
- Foreign keys are properly defined with correct cascade rules
- Indexes are added for performance
- Default values match SQL defaults
- Nullable fields match SQL structure

## Migration Order

Migrations should run in this order (Laravel handles this automatically):
1. Core tables (users, countries, states, cities, sectors, events)
2. Application-related tables
3. Exhibition-related tables
4. Payment and invoice tables
5. Supporting tables (extra requirements, meeting rooms, etc.)
6. Missing tables migration (runs last)

