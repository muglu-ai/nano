# Quick Test URLs

## 🎯 Ready to Test Right Now!

### Step 1: Clear Cache (Already Done ✅)
```bash
php artisan optimize:clear
```

### Step 2: Get Your Event ID
```bash
php artisan tinker
```
Then:
```php
\App\Models\Events::first()->id
```

### Step 3: Test These URLs

## 🔐 ADMIN ROUTES (Login Required)

1. **Login First:**
   ```
   http://your-domain/login
   ```

2. **Ticket Events List:**
   ```
   http://your-domain/admin/tickets/events
   ```

3. **Event Setup (Replace 1 with your event ID):**
   ```
   http://your-domain/admin/tickets/events/1/setup
   ```

4. **Event Days:**
   ```
   http://your-domain/admin/tickets/events/1/days
   ```

5. **Registration Categories:**
   ```
   http://your-domain/admin/tickets/events/1/registration-categories
   ```

6. **Ticket Categories:**
   ```
   http://your-domain/admin/tickets/events/1/categories
   ```

7. **Ticket Types:**
   ```
   http://your-domain/admin/tickets/events/1/ticket-types
   ```

8. **Ticket Rules:**
   ```
   http://your-domain/admin/tickets/events/1/rules
   ```

## 🌐 PUBLIC ROUTES (No Login)

1. **Ticket Discovery (Replace 1 with event ID or slug):**
   ```
   http://your-domain/tickets/1
   ```

2. **Registration Form:**
   ```
   http://your-domain/tickets/1/register
   ```

## ✅ What You Should See

### Admin Routes:
- ✅ `/admin/tickets/events` → List of events
- ✅ `/admin/tickets/events/1/setup` → Setup wizard with tabs
- ⚠️ Other pages may show "View not found" (we'll create them next)

### Public Routes:
- ⚠️ Will show stub messages (controllers are placeholders)
- ✅ Routes work (no 404 errors)
- ✅ No authentication required

## 🚨 If You See Errors

### View Not Found:
- **Normal!** We're creating views step by step
- The routes work, we just need to create the view files

### 403 Forbidden:
- Make sure you're logged in as admin
- Check user role in database

### 404 Not Found:
- Run: `php artisan route:clear`
- Check route exists: `php artisan route:list --name=route.name`

## 📊 Route Verification

Run this to see all routes:
```bash
php test-routes.php
```

Or check specific routes:
```bash
php artisan route:list --name=admin.tickets
php artisan route:list --path=tickets
```

