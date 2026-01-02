# Quick Testing Guide for Ticket Routes

## ✅ Routes Status
All routes are registered and ready to test!

## 🧪 Testing Steps

### Step 1: Clear Cache (Important!)
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Step 2: Get Your Event Information
```bash
php artisan tinker
```
Then run:
```php
$event = \App\Models\Events::first();
echo "Event ID: " . $event->id . "\n";
echo "Event Name: " . $event->event_name . "\n";
echo "Event Slug: " . ($event->slug ?: 'NO SLUG - Use ID instead') . "\n";
exit
```

### Step 3: Test Admin Routes (Requires Login)

#### A. Login First
1. Go to: `http://your-domain/login`
2. Login with admin credentials (role: `admin` or `super-admin`)

#### B. Test Admin Ticket Events List
**URL:** `http://your-domain/admin/tickets/events`

**What to expect:**
- List of all events
- "Configure" button for each event
- Status indicators (Active/Configured/Not Configured)

**If you see 404:**
- Check if you're logged in
- Verify user role is `admin` or `super-admin`
- Check route: `php artisan route:list --name=admin.tickets.events`

#### C. Test Event Setup Page
**URL:** `http://your-domain/admin/tickets/events/1/setup`
(Replace `1` with your actual event ID)

**What to expect:**
- Setup wizard with tabs
- Progress indicator
- Configuration form
- Links to manage days, categories, etc.

**Tabs available:**
1. Configuration - Event settings
2. Event Days - Manage event days
3. Registration Categories - Manage registration categories
4. Ticket Categories - Manage ticket categories
5. Ticket Types - Manage ticket types
6. Rules - Manage ticket rules

### Step 4: Test Public Routes (No Login Required)

#### A. Test Ticket Discovery
**URL:** `http://your-domain/tickets/{eventSlug}`

**Options:**
- If event has slug: `http://your-domain/tickets/bts-2026`
- If no slug, we need to modify route to accept ID: `http://your-domain/tickets/1`

**What to expect:**
- Currently shows "Feature not yet implemented" (stub controller)
- Will show ticket catalog when implemented

#### B. Test Registration Form
**URL:** `http://your-domain/tickets/{eventSlug}/register`

**What to expect:**
- Currently shows "Feature not yet implemented" (stub controller)
- Will show registration form when implemented

## 🔍 Route Verification Commands

### Check All Admin Ticket Routes
```bash
php artisan route:list --name=admin.tickets
```

### Check All Public Ticket Routes
```bash
php artisan route:list | grep -E "tickets|manage-booking|ticket-payment"
```

### Check Specific Route
```bash
php artisan route:list --name=admin.tickets.events
```

### See Full Route Details
```bash
php artisan route:list --columns=method,uri,name,action | grep tickets
```

## 📋 Quick Test Checklist

### Admin Routes (Login Required)
- [ ] Can access `/admin/tickets/events`
- [ ] Can access `/admin/tickets/events/1/setup`
- [ ] Configuration form loads
- [ ] Can navigate to different tabs
- [ ] Can access event days page
- [ ] Can access registration categories page
- [ ] Can access ticket categories page
- [ ] Can access ticket types page
- [ ] Can access rules page

### Public Routes (No Login)
- [ ] Can access `/tickets/{eventSlug}` (may show stub message)
- [ ] Can access `/tickets/{eventSlug}/register` (may show stub message)
- [ ] Routes don't require authentication

## 🐛 Troubleshooting

### Problem: Route Not Found (404)
**Solutions:**
1. Clear route cache: `php artisan route:clear`
2. Check route exists: `php artisan route:list --name=route.name`
3. Verify route file is included in `web.php`

### Problem: 403 Forbidden
**Solutions:**
1. Make sure you're logged in
2. Check user role: `php artisan tinker` → `\App\Models\User::find(1)->role`
3. Verify middleware allows your role

### Problem: Controller Not Found
**Solutions:**
1. Check controller file exists
2. Run: `composer dump-autoload`
3. Verify namespace is correct

### Problem: View Not Found
**Solutions:**
1. Check view file exists in `resources/views/tickets/admin/`
2. Clear view cache: `php artisan view:clear`
3. Verify view path in controller

## 🚀 Quick Start Testing

1. **Clear all caches:**
   ```bash
   php artisan optimize:clear
   ```

2. **Login as admin:**
   - Go to `/login`
   - Use admin credentials

3. **Access ticket events:**
   - Go to `/admin/tickets/events`
   - Click "Configure" on any event

4. **Test setup page:**
   - Should see setup wizard
   - Try clicking different tabs
   - Fill configuration form

5. **Test public routes:**
   - Logout or use incognito window
   - Try accessing `/tickets/{eventSlug}`
   - Should work without login (may show stub)

## 📝 Notes

- Admin routes are in `routes/web.php`
- Public routes are in `routes/tickets.php` (separate file)
- Controllers are stubs (basic structure, not fully implemented)
- Views need to be created for full functionality
- Event slug may not exist - we can modify routes to use ID if needed

## Next Steps

1. Test admin routes work
2. Create remaining admin views
3. Implement public registration controllers
4. Build public registration views

