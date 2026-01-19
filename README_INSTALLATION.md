# Event Portal SaaS - Installation Guide

## Quick Installation

### Step 1: Install Dependencies
```bash
composer install
npm install
```

### Step 2: Configure Environment
Copy `.env.example` to `.env` and configure your database settings:
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your database credentials and API key:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Country/State API Key (optional but recommended)
# Get your free API key from: https://countrystatecity.in/
COUNTRY_STATE_API_KEY=your_api_key_here
```

### Step 3: Run Installation Command
```bash
php artisan portal:install
```

This command will:
- ✅ Run all database migrations
- ✅ Seed countries and states
- ✅ Create super admin account
- ✅ Create default event configuration
- ✅ Seed default sectors and organization types

### Step 4: Access Super Admin Panel
1. Log in with the super admin credentials you created
2. Visit `/super-admin/event-config` to configure your event details
3. Visit `/super-admin/sectors` to manage sectors, sub-sectors, and organization types

## Manual Installation Steps

If you prefer to install manually:

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Countries and States
```bash
php artisan db:seed --class=CountryStateSeeder
```

**Note**: The seeder will automatically fetch countries and states from the API if `COUNTRY_STATE_API_KEY` is set in your `.env` file. If the API key is not provided, it will fall back to:
1. Local JSON file (`database/seeders/data/countries_states.json`) if exists
2. Basic seed data (limited countries and states)

**To get an API key:**
1. Visit https://countrystatecity.in/
2. Sign up for a free account
3. Get your API key from the dashboard
4. Add it to your `.env` file as `COUNTRY_STATE_API_KEY=your_key_here`

### 3. Create Super Admin
```bash
php artisan tinker
```
Then run:
```php
User::create([
    'name' => 'Super Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('your_password'),
    'role' => 'super-admin',
    'email_verified_at' => now(),
]);
```

## Super Admin Features

### Event Configuration (`/super-admin/event-config`)
- Configure event name, year, dates, venue
- Set organizer information
- Configure pricing (shell scheme, raw space, processing charges, GST)
- Update social media links

### Sector Management (`/super-admin/sectors`)
- Add/Edit/Delete Sectors
- Add/Edit/Delete Sub-Sectors
- Add/Edit/Delete Organization Types
- Enable/Disable items
- Set sort order

## Notes

- The installation command checks if the application is already installed
- Super admin role is required to access configuration pages
- Event configuration updates also update `config/constants.php` file
- All sectors, sub-sectors, and organization types are stored in the database for easy management


1. We will give each delegate panel where he can login, can aceess he badge, any notification that can be pushed by admin, if he want to upgrade the ticket, from one categroy to another and higher categroy he can do that. can see the receipt, if group registration is done then he should be able see all the registration. 

We will make a different auth system for the same.