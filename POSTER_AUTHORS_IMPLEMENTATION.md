# Poster Registration - Author Separate Table Implementation

## Overview
Authors and their affiliation details are now stored in a separate `poster_authors` table instead of as JSON in the main registration tables.

## Database Structure

### New Table: `poster_authors`
- **Purpose**: Store individual author records with all personal and affiliation details
- **Key Fields**:
  - `token`: Links to poster_registration_demos or poster_registrations
  - `author_index`: 0-based index from the form
  - Personal info: first_name, last_name, email, mobile
  - CV: cv_path, cv_original_name
  - Roles: is_lead_author, is_presenter, will_attend
  - Residential address: country_id, state_id, city, postal_code
  - Affiliation: institution, affiliation_city, affiliation_country_id

### Relationships
```php
PosterRegistrationDemo -> hasMany -> PosterAuthor (via token)
PosterAuthor -> belongsTo -> Country (residential & affiliation)
PosterAuthor -> belongsTo -> State
```

## Key Changes

### 1. Migration Created
- File: `2026_01_27_120000_create_poster_authors_table.php`
- Creates the poster_authors table with proper foreign keys and indexes

### 2. Model Created
- File: `app/Models/PosterAuthor.php`
- Includes relationships to Country and State
- Helper method: `getFullNameAttribute()` for display

### 3. Updated PosterRegistrationDemo Model
- Added relationships:
  - `posterAuthors()`: Get all authors
  - `leadAuthor()`: Get lead author specifically
  - `presenter()`: Get presenter
  - `attendingAuthors()`: Get authors who will attend

### 4. Updated PosterController
#### `storeNewDraft()` method:
- After saving draft, deletes any existing authors for the token
- Loops through each author and creates individual records in poster_authors
- Stores CV path/name if uploaded
- Sets boolean flags for is_lead_author, is_presenter, will_attend

#### `newPreview()` method:
- Loads authors from poster_authors table ordered by author_index
- Passes authors to view along with draft

## Data Flow

### Form Submission → Draft Storage:
1. Form submits with authors as array: `authors[0][field]`, `authors[1][field]`, etc.
2. Controller validates all author data
3. Main registration details saved to `poster_registration_demos`
4. Each author saved as separate row in `poster_authors` with same token
5. CV files stored with lead author record

### Preview/Display:
1. Load draft from `poster_registration_demos` by token
2. Load all authors from `poster_authors` where token matches
3. Authors returned ordered by author_index (0, 1, 2, 3)
4. Display with full relational data (country names, state names, etc.)

## Benefits

1. **Normalization**: Proper database design with normalized data
2. **Querying**: Easy to query authors independently (e.g., all lead authors, all presenters)
3. **Relationships**: Proper foreign keys to countries/states for data integrity
4. **Scalability**: Can add author-specific features without modifying main table
5. **Reporting**: Easier to generate author-based reports and analytics

## Migration Command
```bash
php artisan migrate
```

## Usage Examples

```php
// Get all authors for a registration
$draft = PosterRegistrationDemo::where('token', $token)->first();
$authors = $draft->posterAuthors;

// Get lead author
$leadAuthor = $draft->leadAuthor();

// Get all attending authors
$attendees = $draft->attendingAuthors;

// Access author's country name
$author = PosterAuthor::find(1);
echo $author->country->name; // via relationship
echo $author->affiliationCountry->name; // via relationship
```

## Index Strategy
- `token`: Fast lookup of all authors for a registration
- `(token, author_index)`: Fast ordered retrieval
- `email`: Support for duplicate email checks or email-based queries
