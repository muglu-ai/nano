# Testing Routes Guide

## Quick Route Testing Commands

### 1. List All Ticket Routes
```bash
# List admin ticket routes
php artisan route:list --name=admin.tickets

# List public ticket routes
php artisan route:list --name=tickets

# List all routes (filter for tickets)
php artisan route:list | grep tickets
```

### 2. Check Specific Route
```bash
# Check if a route exists
php artisan route:list --name=admin.tickets.events

# Show route details
php artisan route:show admin.tickets.events
```

### 3. Test Route URLs

#### Admin Routes (Requires Authentication)
1. **Login as Admin/Super-Admin**
   - Go to: `http://your-domain/login`
   - Login with admin credentials

2. **Access Ticket Events List**
   - URL: `http://your-domain/admin/tickets/events`
   - Route Name: `admin.tickets.events`
   - Should show list of events

3. **Access Event Setup**
   - URL: `http://your-domain/admin/tickets/events/{eventId}/setup`
   - Route Name: `admin.tickets.events.setup`
   - Replace `{eventId}` with actual event ID (e.g., 1)
   - Example: `http://your-domain/admin/tickets/events/1/setup`

4. **Other Admin Routes**
   - Event Days: `/admin/tickets/events/{eventId}/days`
   - Registration Categories: `/admin/tickets/events/{eventId}/registration-categories`
   - Ticket Categories: `/admin/tickets/events/{eventId}/categories`
   - Ticket Types: `/admin/tickets/events/{eventId}/ticket-types`
   - Rules: `/admin/tickets/events/{eventId}/rules`

#### Public Routes (No Authentication Required)
1. **Ticket Discovery**
   - URL: `http://your-domain/tickets/{eventSlug}`
   - Route Name: `tickets.discover`
   - Replace `{eventSlug}` with event slug (e.g., `bts-2026`)
   - Example: `http://your-domain/tickets/bts-2026`

2. **Registration Form**
   - URL: `http://your-domain/tickets/{eventSlug}/register`
   - Route Name: `tickets.register`
   - Example: `http://your-domain/tickets/bts-2026/register`

3. **Manage Booking (Magic Link)**
   - URL: `http://your-domain/manage-booking/{token}`
   - Route Name: `tickets.manage`
   - Replace `{token}` with actual magic link token

4. **Payment**
   - URL: `http://your-domain/ticket-payment/{orderId}`
   - Route Name: `tickets.payment`
   - Replace `{orderId}` with actual order ID

## Step-by-Step Testing Guide

### Step 1: Verify Routes Are Registered
```bash
php artisan route:list --name=admin.tickets
php artisan route:list --name=tickets
```

### Step 2: Get Event ID and Slug
```bash
php artisan tinker
```
Then in tinker:
```php
$event = \App\Models\Events::first();
echo "ID: " . $event->id . "\n";
echo "Slug: " . $event->slug . "\n";
```

### Step 3: Test Admin Routes

1. **Login to Admin Panel**
   ```
   http://your-domain/login
   ```

2. **Navigate to Ticket Events**
   ```
   http://your-domain/admin/tickets/events
   ```
   - Should show list of events
   - Click "Configure" on any event

3. **Test Event Setup Page**
   ```
   http://your-domain/admin/tickets/events/1/setup
   ```
   - Should show setup wizard with tabs
   - Try clicking different tabs

4. **Test Configuration Form**
   - Fill in the configuration form
   - Submit and verify it saves

### Step 4: Test Public Routes

1. **Test Ticket Discovery**
   ```
   http://your-domain/tickets/bts-2026
   ```
   - Should show ticket catalog (when implemented)

2. **Test Registration Form**
   ```
   http://your-domain/tickets/bts-2026/register
   ```
   - Should show registration form (when implemented)

## Common Issues & Solutions

### Issue: Route Not Found (404)
**Solution:**
- Check if route is registered: `php artisan route:list --name=route.name`
- Clear route cache: `php artisan route:clear`
- Clear all cache: `php artisan optimize:clear`

### Issue: 403 Unauthorized
**Solution:**
- Make sure you're logged in as admin/super-admin
- Check middleware in controller
- Verify user role in database

### Issue: Controller Not Found
**Solution:**
- Check if controller file exists
- Verify namespace matches
- Run: `composer dump-autoload`

### Issue: View Not Found
**Solution:**
- Check if view file exists
- Verify view path matches controller return
- Clear view cache: `php artisan view:clear`

## Testing Checklist

- [ ] Routes are registered (`php artisan route:list`)
- [ ] Can access admin ticket events list
- [ ] Can access event setup page
- [ ] Configuration form works
- [ ] Can create event days
- [ ] Can create registration categories
- [ ] Can create ticket categories
- [ ] Can create ticket types
- [ ] Public routes are accessible (no auth required)
- [ ] Magic link routes work
- [ ] Payment routes are set up

## Quick Test Script

Create a test file: `test-routes.php`
```php
<?php
// Quick route test
$routes = [
    'admin.tickets.events' => '/admin/tickets/events',
    'tickets.discover' => '/tickets/bts-2026',
];

foreach ($routes as $name => $url) {
    echo "Testing: {$name}\n";
    echo "URL: {$url}\n";
    echo "---\n";
}
```

## Next Steps After Testing

1. **If routes work:**
   - Continue building admin views
   - Implement public registration form
   - Add payment processing

2. **If routes don't work:**
   - Check error logs: `storage/logs/laravel.log`
   - Verify database connection
   - Check middleware configuration

