# Bulk Exhibitor Import Feature

## Overview
This feature allows you to bulk import exhibitor data using a CSV file. The system will automatically create user accounts, applications, and send credentials via email.

## Files Created/Modified

### 1. Sample CSV Template
- **Location**: `public/sample_exhibitor_import.csv`
- **Purpose**: Template file showing the exact format required for import
- **Action**: Download this file, fill in your data, and upload it

### 2. Import Controller Method
- **File**: `app/Http/Controllers/ImportData.php`
- **Method**: `importExhibitorsBulk(Request $request)`
- **Purpose**: Handles the CSV processing and database insertion

### 3. Import View
- **File**: `resources/views/admin/import-exhibitors.blade.php`
- **Route**: `/admin/import-exhibitors`
- **Purpose**: User interface for uploading CSV files

### 4. Routes
- **File**: `routes/web.php`
- **Routes Added**:
  - `GET /admin/import-exhibitors` - Display import form
  - `POST /admin/import-exhibitors-bulk` - Process the CSV import

## CSV Format

The CSV must include these headers (in exact order):

1. **Organisation (Exhibitor Name)** - Required
2. **Entity is Sponsor/ Exhibitor / Startup?** - Optional (defaults to 'Exhibitor')
3. **Exhibition booth Size: in SQM** - Optional (defaults to 9)
4. **Exhibitions Space Type (Raw / Shell)** - Optional (defaults to 'Shell Scheme')
5. **Focus Sectors (if any)** - Optional
6. **Onboarding Status (From TechTeam)** - Optional
7. **Exhibitor Contact Person Name** - Required
8. **Exhibitor Contact Mobile Country Code *** - Optional (defaults to '+91')
9. **Exhibitor Contact Person Mobile *** - Required
10. **Exhibitor Contact Person Email *** - Required
11. **Any ads received (if yes then Description of where to use & drive link for creative) From MMA Team** - Optional
12. **Entitled Startup booths Requirements (default : 0)** - Optional
13. **VIP Pass Requirement (Default: 0)** - Optional
14. **Premium delegate Pass Requirement (Default: 0)** - Optional
15. **Standard delegate Pass Requirement (Default: 0)** - Optional
16. **FMC PREMIUM Delegate Pass Requirement (Default: 0)** - Optional
17. **Exhibitor Pass Requirement (Default: 0)** - Optional
18. **Service Pass Requirement (Default: 0)** - Optional
19. **Business Visitor Pass Requirement (Default: 50)** - Optional
20. **Whose is Handling Client from BTS/MMA Team (Name Email Mobile)** - Optional
21. **Calling Status (from Telecalling team)** - Optional

## How It Works

### Step 1: Download Sample CSV
Navigate to `/admin/import-exhibitors` and click "Download Sample CSV"

### Step 2: Fill in Data
Open the CSV file and fill in the exhibitor information:
- Fill all required fields (marked with *)
- Optional fields can be left empty
- Ensure email addresses are unique

### Step 3: Upload CSV
Upload the filled CSV file and click "Import Exhibitors"

### Step 4: Review Results
The system will display:
- Success count
- Error count
- List of errors (if any)

## What Gets Created

For each row in the CSV:

1. **User Account**
   - Name: Contact Person Name
   - Email: Exhibitor Contact Email
   - Password: Auto-generated 8-character password
   - Role: Exhibitor
   - Phone: Contact Mobile

2. **Application**
   - Company Name: Organisation Name
   - Booth Size: Exhibition Size (SQM)
   - Stall Category: Raw Space / Shell Scheme
   - Exhibitor Type: Sponsor / Exhibitor / Startup
   - Status: Approved (if onboarding status is "Completed") or Pending

3. **Event Contact**
   - Contact information
   - Email and phone details

4. **Billing Details**
   - Company billing information

5. **Exhibition Participant**
   - Ticket allocation based on booth size and requirements
   - Pass requirements stored

6. **Email Sent**
   - Credentials email sent to contact person
   - Includes login URL and password

## Sample Data

See `public/sample_exhibitor_import.csv` for example data with:
- 10 sample exhibitor records
- All fields populated
- Variety of entity types (Exhibitor, Sponsor, Startup)
- Different booth sizes
- Various pass requirements

## Ticket Allocation Logic

The system calculates ticket allocation based on:
- **Booth Size ≤ 9 SQM**: 1 VIP Pass, 2 Premium Passes
- **Booth Size 10-18 SQM**: 2 VIP Passes, 4 Premium Passes
- **Booth Size 19-36 SQM**: 4 VIP Passes, 8 Premium Passes
- **Booth Size > 36 SQM**: Based on requirements

Custom pass requirements from the CSV are added to the defaults.

## Error Handling

The import process:
- Skips rows with missing required fields
- Skips rows where the email already exists
- Continues processing remaining rows
- Returns detailed error messages for failed rows
- Returns success count and error count

## Access Control

- Requires admin authentication
- Protected by Auth middleware
- All routes are under `/admin/`

## Testing

To test the import:
1. Download the sample CSV
2. Add or modify a few records
3. Upload the CSV
4. Check the results
5. Verify created users in `/users/list`
6. Check email delivery for credentials

## Important Notes

- **Email Uniqueness**: Each contact email must be unique. Duplicate emails will be skipped.
- **Password**: Auto-generated passwords are 8 characters long (alphanumeric).
- **Approval Status**: Only rows with "Completed" onboarding status are auto-approved.
- **Default Values**: Most fields have sensible defaults if left empty.
- **CSV Encoding**: Use UTF-8 encoding to avoid character issues.

## Customization

To customize the import:
1. Modify the field mapping in `importExhibitorsBulk()` method
2. Adjust ticket allocation logic in `calculateTicketAllocation()` method
3. Update the CSV template and view as needed
4. Modify email templates if needed

