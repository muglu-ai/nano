# Enquiry System Improvement Plan

## Executive Summary

The current `it_enq_table` schema is a legacy structure with poor naming conventions, redundant fields, mixed concerns, and unclear field mappings. This plan proposes a modern, normalized database design that aligns with the Prawaas 2024 enquiry form and follows Laravel best practices.

---

## 1. Current Problems Analysis

### 1.1 Schema Issues

#### **Poor Naming Conventions**
- Abbreviated field names (`fname`, `lname`, `org`, `desig`, `cellno`, `fone`)
- Generic field names (`enq1` through `enq9`) - unclear purpose
- Inconsistent naming (`ddate`, `ttime` vs `followup_ddate`, `followup_dtime`)

#### **Redundant Fields**
- Multiple name fields: `title`, `fname`, `lname`, `name` (unclear which to use)
- Multiple phone fields: `cellno`, `fone`, `fax` (unclear distinction)
- Duplicate status tracking: `status` + `followup_status` with similar fields

#### **Mixed Concerns**
- Form data mixed with workflow/status tracking
- Followup data stored in same table (should be separate)
- Deletion tracking mixed with main data

#### **Data Type Issues**
- Dates stored as `varchar` instead of `date`/`datetime` (`ddate`, `ttime`, `followup_ddate`, `followup_dtime`, `del_date`, `del_time`)
- No proper timestamps (`created_at`, `updated_at`)
- No soft deletes support

#### **Missing Relationships**
- No foreign key to `events` table
- No proper user assignment relationships
- No structured way to handle multiple enquiry interests

#### **Unclear Field Mappings**
- Form shows checkboxes for "Want Information About" (multiple selections)
- Current schema has `enq1`-`enq9` - unclear mapping
- No clear structure for storing multiple interests

---

## 2. Form Field Analysis

Based on the Prawaas 2024 enquiry form, the following fields are present:

### 2.1 Required Fields
- **Title** (dropdown: Mr, Mrs, Ms, Dr, etc.)
- **Name** (text input)
- **Organisation** (text input)
- **Designation** (text input)
- **Email Address** (email input)
- **Contact Number** (country code + phone number)
- **Comment** (textarea, 250 char limit)
- **City** (text input)
- **Country** (dropdown)
- **How did you know about this event?** (dropdown)
- **Captcha** (validation only, not stored)

### 2.2 Optional Fields
- **Want Information About** (checkboxes - multiple selections):
  - Attending as Delegate
  - Speaking Opportunity
  - Exhibiting
  - Sponsoring
  - B2B Meetings
  - Visitor
  - Conference & Awards
  - Other

---

## 3. Proposed New Schema Design

### 3.1 Core Tables

#### **Table 1: `enquiries`** (Main enquiry table)
```sql
CREATE TABLE enquiries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NULL, -- FK to events table
    event_year VARCHAR(10) NULL, -- For legacy compatibility
    
    -- Personal Information
    title VARCHAR(10) NULL, -- Mr, Mrs, Ms, Dr, etc.
    first_name VARCHAR(255) NULL,
    last_name VARCHAR(255) NULL,
    full_name VARCHAR(255) NOT NULL, -- Computed or direct input
    
    -- Organization Information
    organisation VARCHAR(255) NOT NULL,
    designation VARCHAR(255) NOT NULL,
    sector VARCHAR(200) NULL,
    
    -- Contact Information
    email VARCHAR(255) NOT NULL,
    phone_country_code VARCHAR(5) NULL, -- +91, +1, etc.
    phone_number VARCHAR(20) NOT NULL,
    phone_full VARCHAR(30) NULL, -- Computed: country_code + number
    
    -- Address Information
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NULL,
    country VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10) NULL,
    address TEXT NULL,
    
    -- Enquiry Details
    comments TEXT NOT NULL,
    referral_source VARCHAR(100) NULL, -- "How did you know about this event?"
    
    -- Metadata
    source_url VARCHAR(500) NULL, -- Where the form was submitted from
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    
    -- Status & Workflow
    status VARCHAR(50) DEFAULT 'new', -- new, contacted, qualified, converted, closed
    prospect_level VARCHAR(50) NULL, -- hot, warm, cold
    status_comment TEXT NULL,
    
    -- Assignment
    assigned_to_user_id BIGINT UNSIGNED NULL, -- FK to users table
    assigned_to_name VARCHAR(255) NULL, -- Denormalized for quick access
    
    -- Timestamps
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL, -- Soft deletes
    
    -- Indexes
    INDEX idx_event_id (event_id),
    INDEX idx_event_year (event_year),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_assigned_to (assigned_to_user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_deleted_at (deleted_at),
    
    -- Foreign Keys
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Table 2: `enquiry_interests`** (Many-to-many for enquiry interests)
```sql
CREATE TABLE enquiry_interests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    enquiry_id BIGINT UNSIGNED NOT NULL,
    interest_type VARCHAR(50) NOT NULL, -- delegate, speaking, exhibiting, sponsoring, b2b, visitor, conference, other
    interest_other_detail VARCHAR(255) NULL, -- If "other" is selected
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_enquiry_id (enquiry_id),
    INDEX idx_interest_type (interest_type),
    
    FOREIGN KEY (enquiry_id) REFERENCES enquiries(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enquiry_interest (enquiry_id, interest_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Table 3: `enquiry_followups`** (Separate followup tracking)
```sql
CREATE TABLE enquiry_followups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    enquiry_id BIGINT UNSIGNED NOT NULL,
    
    -- Followup Details
    followup_type VARCHAR(50) NULL, -- call, email, meeting, note
    followup_status VARCHAR(50) NULL, -- pending, completed, cancelled
    followup_comment TEXT NULL,
    followup_date DATE NULL,
    followup_time TIME NULL,
    followup_datetime DATETIME NULL, -- Computed or direct
    
    -- Assignment
    assigned_to_user_id BIGINT UNSIGNED NULL,
    assigned_to_name VARCHAR(255) NULL,
    
    -- Prospect Tracking
    prospect_level VARCHAR(50) NULL,
    
    -- Timestamps
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_enquiry_id (enquiry_id),
    INDEX idx_followup_date (followup_date),
    INDEX idx_followup_status (followup_status),
    INDEX idx_assigned_to (assigned_to_user_id),
    
    FOREIGN KEY (enquiry_id) REFERENCES enquiries(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **Table 4: `enquiry_notes`** (Additional notes/comments)
```sql
CREATE TABLE enquiry_notes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    enquiry_id BIGINT UNSIGNED NOT NULL,
    note TEXT NOT NULL,
    note_type VARCHAR(50) DEFAULT 'general', -- general, internal, customer_response
    
    created_by_user_id BIGINT UNSIGNED NULL,
    created_by_name VARCHAR(255) NULL,
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_enquiry_id (enquiry_id),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (enquiry_id) REFERENCES enquiries(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3.2 Benefits of New Schema

1. **Clear Field Names**: Self-documenting field names
2. **Proper Relationships**: Foreign keys to events and users
3. **Separated Concerns**: Form data, interests, followups, and notes in separate tables
4. **Proper Data Types**: Dates as DATE/DATETIME, not VARCHAR
5. **Soft Deletes**: Proper deletion tracking
6. **Multiple Interests**: Proper many-to-many relationship
7. **Extensible**: Easy to add new fields or relationships
8. **Indexed**: Proper indexes for common queries
9. **Laravel-Friendly**: Follows Laravel conventions (timestamps, soft deletes)

---

## 4. Migration Strategy

### 4.1 Phase 1: Create New Tables (Non-Breaking)
1. Create new tables alongside existing `it_enq_table`
2. Keep old table intact for backward compatibility
3. No changes to existing code

### 4.2 Phase 2: Data Migration Script
Create a migration script to copy data from old to new schema:

```php
// Migration logic:
// 1. Map old fields to new fields
// 2. Parse enq1-enq9 to enquiry_interests
// 3. Migrate followup data to enquiry_followups
// 4. Handle date conversions
// 5. Create relationships
```

**Field Mapping:**
- `title` → `title`
- `fname` → `first_name`
- `lname` → `last_name`
- `name` → `full_name` (if fname/lname empty)
- `org` → `organisation`
- `desig` → `designation`
- `email` → `email`
- `cellno` or `fone` → `phone_number` (prefer cellno)
- `city` → `city`
- `state` → `state`
- `country` → `country`
- `zip` → `postal_code`
- `addr` → `address`
- `comments` → `comments`
- `know_from` → `referral_source`
- `event_name` → Lookup `event_id` from events table
- `event_year` → `event_year`
- `status` → `status`
- `prospect` → `prospect_level`
- `enq1-enq9` → Parse and create `enquiry_interests` records
- `ddate` + `ttime` → `created_at` (parse and combine)
- Followup fields → `enquiry_followups` table

### 4.3 Phase 3: Dual-Write Period
1. Update form submission to write to both old and new tables
2. Update admin views to read from new tables
3. Monitor for any issues

### 4.4 Phase 4: Full Migration
1. Update all code to use new tables
2. Remove old table references
3. Archive or drop `it_enq_table` (after backup)

---

## 5. Implementation Steps

### Step 1: Create Migrations
- [ ] Create `create_enquiries_table` migration
- [ ] Create `create_enquiry_interests_table` migration
- [ ] Create `create_enquiry_followups_table` migration
- [ ] Create `create_enquiry_notes_table` migration

### Step 2: Create Models
- [ ] Create `Enquiry` model with relationships
- [ ] Create `EnquiryInterest` model
- [ ] Create `EnquiryFollowup` model
- [ ] Create `EnquiryNote` model

### Step 3: Create Form Controller
- [ ] Create `PublicEnquiryController` for form submission
- [ ] Implement validation rules
- [ ] Handle multiple interest selections
- [ ] Store enquiry with relationships
- [ ] Send confirmation email

### Step 4: Update Admin Interface
- [ ] Update `EnquiryController` to use new models
- [ ] Update views to display new structure
- [ ] Add interest display
- [ ] Add followup management
- [ ] Add notes management

### Step 5: Data Migration
- [ ] Create data migration script
- [ ] Test migration on staging
- [ ] Run migration on production
- [ ] Verify data integrity

### Step 6: API Updates
- [ ] Update any APIs that use `it_enq_table`
- [ ] Update export functionality
- [ ] Update reporting queries

### Step 7: Testing
- [ ] Test form submission
- [ ] Test admin views
- [ ] Test data migration
- [ ] Test relationships
- [ ] Test soft deletes

### Step 8: Documentation
- [ ] Update API documentation
- [ ] Document new schema
- [ ] Create admin user guide

---

## 6. Model Structure (Laravel)

### 6.1 Enquiry Model
```php
class Enquiry extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'event_id', 'event_year', 'title', 'first_name', 'last_name',
        'full_name', 'organisation', 'designation', 'sector',
        'email', 'phone_country_code', 'phone_number', 'phone_full',
        'city', 'state', 'country', 'postal_code', 'address',
        'comments', 'referral_source', 'source_url', 'ip_address',
        'user_agent', 'status', 'prospect_level', 'status_comment',
        'assigned_to_user_id', 'assigned_to_name'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    // Relationships
    public function event() {
        return $this->belongsTo(Events::class);
    }
    
    public function assignedTo() {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
    
    public function interests() {
        return $this->hasMany(EnquiryInterest::class);
    }
    
    public function followups() {
        return $this->hasMany(EnquiryFollowup::class);
    }
    
    public function notes() {
        return $this->hasMany(EnquiryNote::class);
    }
}
```

### 6.2 EnquiryInterest Model
```php
class EnquiryInterest extends Model
{
    protected $fillable = [
        'enquiry_id', 'interest_type', 'interest_other_detail'
    ];
    
    public function enquiry() {
        return $this->belongsTo(Enquiry::class);
    }
}
```

---

## 7. Form Submission Flow

### 7.1 Validation Rules
```php
$rules = [
    'title' => 'nullable|string|max:10',
    'name' => 'required|string|max:255',
    'organisation' => 'required|string|max:255',
    'designation' => 'required|string|max:255',
    'email' => 'required|email|max:255',
    'phone_country_code' => 'nullable|string|max:5',
    'phone_number' => 'required|string|max:20',
    'city' => 'required|string|max:100',
    'country' => 'required|string|max:100',
    'comments' => 'required|string|max:1000',
    'referral_source' => 'nullable|string|max:100',
    'interests' => 'nullable|array',
    'interests.*' => 'in:delegate,speaking,exhibiting,sponsoring,b2b,visitor,conference,other',
    'interest_other' => 'required_if:interests.*,other|string|max:255',
    'captcha' => 'required|captcha',
];
```

### 7.2 Submission Logic
```php
// 1. Validate form data
// 2. Create enquiry record
// 3. Create enquiry_interest records for each selected interest
// 4. Send confirmation email
// 5. Redirect to thank you page
```

---

## 8. Admin Interface Improvements

### 8.1 List View Enhancements
- Show interests as badges
- Show latest followup status
- Show assigned user
- Filter by status, interest type, event
- Search by name, email, organisation

### 8.2 Detail View
- Show all enquiry information
- Display interests
- Show followup history
- Add new followup
- Add notes
- Change assignment
- Update status

### 8.3 Followup Management
- Calendar view for scheduled followups
- Task list for assigned followups
- Followup history timeline

---

## 9. Benefits Summary

1. **Maintainability**: Clear, self-documenting schema
2. **Scalability**: Proper relationships and indexes
3. **Flexibility**: Easy to add new fields or features
4. **Performance**: Optimized queries with proper indexes
5. **Data Integrity**: Foreign keys and constraints
6. **User Experience**: Better admin interface
7. **Reporting**: Easier to generate reports and analytics
8. **Laravel Best Practices**: Follows framework conventions

---

## 10. Risk Mitigation

1. **Data Loss**: Full backup before migration
2. **Downtime**: Dual-write period ensures no data loss
3. **Breaking Changes**: Gradual migration with backward compatibility
4. **Performance**: Test with production-like data volumes
5. **Validation**: Comprehensive testing before production deployment

---

## 11. Timeline Estimate

- **Phase 1** (New Tables): 2-3 days
- **Phase 2** (Models & Relationships): 2-3 days
- **Phase 3** (Form Controller): 2-3 days
- **Phase 4** (Admin Interface): 3-4 days
- **Phase 5** (Data Migration): 2-3 days
- **Phase 6** (Testing & Refinement): 3-4 days

**Total: 14-20 days** (depending on complexity and testing requirements)

---

## 12. Next Steps

1. Review and approve this plan
2. Create detailed technical specifications
3. Set up development environment
4. Begin Phase 1 implementation
5. Schedule regular review meetings

---

## Appendix: Interest Type Constants

```php
class EnquiryInterestType
{
    const DELEGATE = 'delegate';
    const SPEAKING = 'speaking';
    const EXHIBITING = 'exhibiting';
    const SPONSORING = 'sponsoring';
    const B2B_MEETINGS = 'b2b';
    const VISITOR = 'visitor';
    const CONFERENCE = 'conference';
    const OTHER = 'other';
    
    public static function all(): array
    {
        return [
            self::DELEGATE => 'Attending as Delegate',
            self::SPEAKING => 'Speaking Opportunity',
            self::EXHIBITING => 'Exhibiting',
            self::SPONSORING => 'Sponsoring',
            self::B2B_MEETINGS => 'B2B Meetings',
            self::VISITOR => 'Visitor',
            self::CONFERENCE => 'Conference & Awards',
            self::OTHER => 'Other',
        ];
    }
}
```

---

**Document Version**: 1.0  
**Last Updated**: 2024  
**Author**: System Analysis

