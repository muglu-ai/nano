# Bulk Import Validation - All or Nothing Approach

## Overview
The bulk exhibitor import now uses an "all or nothing" validation approach to ensure data integrity. **No database records are inserted until ALL validations pass successfully.**

## How It Works

### Step 1: Read CSV File
The system reads the entire CSV file into memory.

### Step 2: Validate ALL Rows
Before inserting ANY records, the system validates EVERY row:

1. **Required Field Validation**
   - Organisation (Exhibitor Name)
   - Exhibitor Contact Person Email
   - Exhibitor Contact Person Mobile

2. **Duplicate Email Check in CSV**
   - Checks if the same email appears multiple times within the CSV file
   - Reports which rows have duplicate emails

3. **Database Existence Check**
   - Verifies if a user with that email already exists in the database
   - Prevents creating duplicate accounts

### Step 3: Return OR Process

#### If ANY validation fails:
- ❌ **NO database records are inserted**
- Returns HTTP 400 (Bad Request)
- Provides detailed error list with:
  - Row number for each error
  - Specific error messages
  - Total rows vs valid rows count

#### If ALL validations pass:
- ✅ Proceeds to **insert all records** in a **database transaction**
- Uses `DB::beginTransaction()` to ensure atomicity
- All records are either inserted completely or none at all
- If ANY insertion fails, the entire transaction is rolled back
- Returns success message with count of inserted records

## Error Response Format

When validation fails, you'll receive:

```json
{
  "success": false,
  "message": "Validation failed. Please fix the errors and try again.",
  "total_rows": 10,
  "valid_rows": 7,
  "error_rows": 3,
  "errors": [
    {
      "row": 3,
      "errors": [
        "Missing 'Exhibitor Contact Person Email'",
        "User with email 'john@example.com' already exists in database"
      ]
    },
    {
      "row": 5,
      "errors": [
        "Duplicate email 'jane@example.com' already exists in row 2"
      ]
    }
  ]
}
```

## Success Response Format

When all validations pass and import succeeds:

```json
{
  "success": true,
  "message": "Successfully imported 10 exhibitors",
  "success_count": 10,
  "error_count": 0,
  "inserted_records": [
    {
      "email": "john@example.com",
      "organisation": "Intel Corporation",
      "contact_name": "John Smith"
    }
  ]
}
```

## Database Transaction Safety

The import uses Laravel's database transactions:

```php
DBFacade::beginTransaction();

try {
    // Insert all records...
    
    DBFacade::commit();
    return success_response;
} catch (\Exception $e) {
    DBFacade::rollBack();
    return error_response;
}
```

This ensures:
- **Atomicity**: Either ALL records are inserted or NONE
- **Consistency**: Database maintains data integrity
- **Error Recovery**: Automatic rollback on any failure

## Benefits

1. **Data Integrity**: No partial imports that could corrupt data
2. **Clear Error Reporting**: All errors reported at once
3. **No Orphan Records**: Won't create users without applications
4. **Transactional Safety**: Database-level consistency
5. **User-Friendly**: Fix all errors at once, then re-upload

## Example Scenarios

### Scenario 1: All Valid
- CSV with 10 valid rows
- Result: All 10 exhibitors created
- Email credentials sent to all 10

### Scenario 2: One Invalid Row
- CSV with 10 rows, row 5 has missing email
- Result: ❌ NO exhibitors created (0 inserted)
- Error: "Row 7: Missing 'Exhibitor Contact Person Email'"

### Scenario 3: Duplicate Emails in CSV
- CSV with 10 rows, emails in rows 3 and 8 are identical
- Result: ❌ NO exhibitors created (0 inserted)
- Error: "Row 9: Duplicate email 'john@example.com' already exists in row 4"

### Scenario 4: Email Already Exists in Database
- CSV with 10 rows, email in row 5 already exists
- Result: ❌ NO exhibitors created (0 inserted)
- Error: "Row 7: User with email 'existing@example.com' already exists in database"

## Usage

1. **Download Sample CSV**
   - Go to `/admin/import-exhibitors`
   - Click "Download Sample CSV"

2. **Fill in Data**
   - Ensure all required fields are filled
   - Check for duplicate emails within the file
   - Verify emails don't already exist in database

3. **Upload CSV**
   - Click "Import Exhibitors"
   - System validates all rows first

4. **If Validation Fails**
   - Fix all reported errors
   - Re-upload the corrected CSV

5. **If Validation Succeeds**
   - All exhibitors are created
   - Credentials emailed to all contacts

## Important Notes

- ⚠️ **No Partial Imports**: The system will NEVER insert only some records
- ✅ **All or Nothing**: Either all records are inserted or none
- 🔄 **Transaction Rollback**: Any insertion error rolls back everything
- 📧 **Email Queue**: Credentials are queued to email after successful insert
- 🔒 **Data Safety**: Database integrity is always maintained

