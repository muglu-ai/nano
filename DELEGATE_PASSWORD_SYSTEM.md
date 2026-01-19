# Delegate Password System - Documentation

## 📋 Overview

This document explains how the delegate password system works, where passwords are stored, and the logic behind password management.

---

## 🔐 Password Storage

### Database Table: `ticket_accounts`

**Table Structure:**
```sql
CREATE TABLE ticket_accounts (
    id BIGINT PRIMARY KEY,
    contact_id BIGINT,           -- Foreign key to ticket_contacts
    password VARCHAR(255),        -- Hashed password (bcrypt)
    email_verified_at TIMESTAMP,
    remember_token VARCHAR(100), -- Used for password reset tokens
    status VARCHAR(20),          -- 'active', 'suspended', 'inactive'
    last_login_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Password Hashing

**Location:** `app/Models/Ticket/TicketAccount.php`

```php
protected function casts(): array
{
    return [
        'password' => 'hashed',  // Laravel automatically hashes passwords
    ];
}
```

**How it works:**
- When you set `$account->password = 'plaintext'`, Laravel automatically hashes it using **bcrypt**
- The hash is stored in the database (60 characters)
- When checking: `Hash::check($plaintext, $account->password)` compares plaintext with hash

**Example:**
```php
// Setting password (automatically hashed)
$account->password = 'myPassword123';
$account->save();
// Database stores: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

// Checking password
Hash::check('myPassword123', $account->password); // Returns true
Hash::check('wrongPassword', $account->password); // Returns false
```

---

## 🔄 Password Reset Flow

### Step 1: User Requests Password Reset

**Route:** `POST /delegate/password/email`

**Process:**
1. User enters email on forgot password page
2. System finds contact by email (or delegate email)
3. Gets or creates `ticket_account` for that contact
4. Generates random 64-character token
5. Stores hashed token in `remember_token` field
6. Sends email with reset link

**Code:**
```php
$token = Str::random(64);
$account->update([
    'remember_token' => Hash::make($token),
]);
```

### Step 2: User Clicks Reset Link

**Route:** `GET /delegate/password/reset/{token}?email=user@example.com`

**Process:**
1. System extracts token and email from URL
2. Finds contact by email
3. Gets account
4. Verifies token: `Hash::check($token, $account->remember_token)`
5. Shows reset password form if valid

### Step 3: User Sets New Password

**Route:** `POST /delegate/password/reset`

**Process:**
1. Validates password (min 8 chars, confirmed)
2. Verifies token again
3. Updates password: `$account->password = $request->password` (automatically hashed)
4. Clears reset token: `remember_token = null`
5. Redirects to login

**Code:**
```php
$account->update([
    'password' => $request->password,  // Automatically hashed by cast
    'remember_token' => null,          // Clear reset token
]);
```

---

## 🔑 Password Login Flow

### Step 1: User Enters Credentials

**Route:** `POST /delegate/login`

**Process:**
1. Finds contact by email (or delegate email)
2. Gets or creates account
3. Checks if account is active
4. Verifies password: `Hash::check($password, $account->password)`
5. Logs in user if valid

**Code:**
```php
if (!$account->password || !Hash::check($credentials['password'], $account->password)) {
    return back()->withErrors(['email' => 'Invalid credentials']);
}

Auth::guard('delegate')->login($account);
```

---

## 📧 Email Templates

### Password Reset Email

**Template:** `resources/views/emails/delegate-password-reset.blade.php`

**Contains:**
- Reset link with token and email
- Instructions
- Expiration notice (60 minutes)

**Link Format:**
```
/delegate/password/reset/{token}?email=user@example.com
```

### OTP Email (Different from Password Reset)

**Template:** `resources/views/emails/delegate-otp.blade.php`

**Contains:**
- 6-digit OTP code
- Instructions
- Expiration notice (10 minutes)

**Note:** OTP and Password Reset are **separate systems**:
- **OTP:** For quick login without password
- **Password Reset:** For setting/changing password

---

## 🐛 Common Issues & Fixes

### Issue 1: Password Reset Sends OTP Instead

**Problem:** Wrong email template being used

**Check:**
```php
// In DelegateAuthController::sendPasswordResetLink()
Mail::send('emails.delegate-password-reset', [  // Should be this
    'resetUrl' => $resetUrl,
    // ...
]);
```

**Fix:** Ensure `emails.delegate-password-reset` template exists and is being used.

### Issue 2: OTP Email Sent But Shows Error

**Problem:** AJAX response not properly handled

**Fix:** Updated login.blade.php to properly check `data.success` in response.

### Issue 3: Password Not Hashing

**Problem:** Password cast not working

**Check:**
```php
// In TicketAccount model
protected function casts(): array
{
    return [
        'password' => 'hashed',  // Must be 'hashed', not 'string'
    ];
}
```

### Issue 4: Token Verification Fails

**Problem:** Token not matching

**Check:**
- Token is hashed when stored: `Hash::make($token)`
- Token is checked correctly: `Hash::check($request->token, $account->remember_token)`
- Token hasn't been cleared (used or expired)

---

## 🔍 Debugging

### Check Password Hash

```php
php artisan tinker

$account = \App\Models\Ticket\TicketAccount::find(1);
echo $account->password;  // Shows hashed password
```

### Test Password Check

```php
$account = \App\Models\Ticket\TicketAccount::find(1);
Hash::check('test123', $account->password);  // Returns true/false
```

### Check Reset Token

```php
$account = \App\Models\Ticket\TicketAccount::find(1);
echo $account->remember_token;  // Shows hashed token
```

### Verify Email Template

```bash
ls -la resources/views/emails/delegate-password-reset.blade.php
```

---

## 📝 Summary

1. **Password Storage:**
   - Table: `ticket_accounts`
   - Column: `password` (hashed with bcrypt)
   - Auto-hashing via Laravel cast: `'password' => 'hashed'`

2. **Password Reset:**
   - Token stored in: `remember_token` (hashed)
   - Email template: `emails.delegate-password-reset`
   - Link format: `/delegate/password/reset/{token}?email={email}`

3. **Password Login:**
   - Verification: `Hash::check($plaintext, $hashed)`
   - Guard: `delegate`
   - Model: `TicketAccount`

4. **OTP System:**
   - Separate from password reset
   - Stored in: `ticket_otp_requests` table
   - Email template: `emails.delegate-otp`

---

## ✅ Verification Checklist

- [ ] Password is stored hashed in database
- [ ] Password reset link is sent (not OTP)
- [ ] Reset token is hashed before storage
- [ ] Password is automatically hashed when set
- [ ] Token verification works correctly
- [ ] Email templates exist and are correct
- [ ] AJAX responses are properly handled
