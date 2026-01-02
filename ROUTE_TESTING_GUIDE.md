# Route Testing Guide - Ticket System

## ✅ Routes Status: ALL REGISTERED

All routes are successfully registered! Here's how to test them.

## 🚀 Quick Start Testing

### Step 1: Clear All Caches
```bash
php artisan optimize:clear
```

### Step 2: Run Route Test Script
```bash
php test-routes.php
```

This will show you:
- All available routes
- Event information
- Full URLs to test

## 📋 Testing Admin Routes (Requires Login)

### 1. Login as Admin
```
URL: http://your-domain/login
```
- Use admin or super-admin credentials
- Role must be `admin` or `super-admin`

### 2. Access Ticket Events List
```
URL: http://your-domain/admin/tickets/events
Route: admin.tickets.events
```
**Expected:** List of events with "Configure" button

### 3. Access Event Setup
```
URL: http://your-domain/admin/tickets/events/1/setup
Route: admin.tickets.events.setup
```
**Replace `1` with your event ID**

**Expected:**
- Setup wizard with tabs
- Progress indicator
- Configuration form

### 4. Test Other Admin Pages
```
Event Days:        /admin/tickets/events/1/days
Reg Categories:    /admin/tickets/events/1/registration-categories
Ticket Categories: /admin/tickets/events/1/categories
Ticket Types:      /admin/tickets/events/1/ticket-types
Rules:             /admin/tickets/events/1/rules
```

## 🌐 Testing Public Routes (No Login)

### 1. Ticket Discovery
```
URL: http://your-domain/tickets/1
Route: tickets.discover
```
**Note:** Event slug is not set, so using ID. Will show stub message.

### 2. Registration Form
```
URL: http://your-domain/tickets/1/register
Route: tickets.register
```
**Note:** Will show stub message until implemented.

### 3. Manage Booking
```
URL: http://your-domain/manage-booking/{token}
Route: tickets.manage
```
**Note:** Requires valid magic link token.

### 4. Payment
```
URL: http://your-domain/ticket-payment/{orderId}
Route: tickets.payment
```
**Note:** Requires valid order ID.

## 🔍 Verify Routes Are Registered

### Check All Admin Ticket Routes
```bash
php artisan route:list --name=admin.tickets
```

### Check All Public Ticket Routes
```bash
php artisan route:list --path=tickets
```

### Check Specific Route
```bash
php artisan route:list --name=admin.tickets.events
```

### See Full Route List
```bash
php artisan route:list | grep tickets
```

## 📝 Route Summary

### Admin Routes (in `routes/web.php`)
- ✅ All 20+ admin routes registered
- ✅ Protected with auth middleware
- ✅ Organized under `/admin/tickets/` prefix

### Public Routes (in `routes/tickets.php`)
- ✅ All public routes registered
- ✅ No authentication required
- ✅ Organized separately for clarity

## 🐛 Common Issues & Fixes

### Issue: 404 Not Found
**Fix:**
```bash
php artisan route:clear
php artisan optimize:clear
```

### Issue: 403 Forbidden
**Fix:**
- Make sure you're logged in
- Check user role: `php artisan tinker` → `\App\Models\User::find(1)->role`
- Should be `admin` or `super-admin`

### Issue: Controller Not Found
**Fix:**
```bash
composer dump-autoload
php artisan optimize:clear
```

### Issue: View Not Found
**Fix:**
- Views are partially created (setup page exists)
- Other views will be created as we build
- For now, you may see errors for missing views

## ✅ Testing Checklist

### Admin Routes
- [ ] Can access `/admin/tickets/events` (after login)
- [ ] Can access `/admin/tickets/events/1/setup`
- [ ] Setup page loads with tabs
- [ ] Configuration form is visible
- [ ] Can navigate to different sections

### Public Routes
- [ ] Can access `/tickets/1` (no login)
- [ ] Can access `/tickets/1/register` (no login)
- [ ] Routes don't require authentication

## 🎯 Next Steps

1. **Test Admin Interface:**
   - Login and access `/admin/tickets/events`
   - Click "Configure" on an event
   - Try the setup wizard

2. **Create Missing Views:**
   - Event days management
   - Registration categories
   - Ticket categories
   - Ticket types
   - Rules configuration

3. **Implement Controllers:**
   - Public registration logic
   - Payment processing
   - Magic link handling

## 📞 Quick Reference

**Base URL:** Check your `.env` file or run:
```bash
php artisan tinker
config('app.url')
```

**Event ID:** Get from database:
```bash
php artisan tinker
\App\Models\Events::first()->id
```

**Test Script:** Run anytime to see all routes:
```bash
php test-routes.php
```

