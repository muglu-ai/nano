# OTP Email Troubleshooting Guide

## Common Issues and Solutions

### Issue: "Failed to send OTP. Please try again."

This error can occur due to several reasons. Follow these steps to diagnose and fix:

---

## Step 1: Check Mail Configuration

### Verify `.env` file has mail settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### For Gmail:
1. Enable 2-Factor Authentication
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Use the app password (not your regular password) in `MAIL_PASSWORD`

### For Other SMTP Providers:

**Outlook/Hotmail:**
```env
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

**SendGrid:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
```

**Mailgun:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_mailgun_username
MAIL_PASSWORD=your_mailgun_password
MAIL_ENCRYPTION=tls
```

---

## Step 2: Test Mail Configuration

### Option A: Using Tinker

```bash
php artisan tinker
```

Then run:
```php
Mail::raw('Test email', function($message) {
    $message->to('your-test-email@example.com')
            ->subject('Test Email');
});
```

### Option B: Using Artisan Command

Create a test command:
```bash
php artisan make:command TestMail
```

Or use Laravel's built-in mail test:
```bash
php artisan tinker
```

```php
use Illuminate\Support\Facades\Mail;
Mail::send('emails.delegate-otp', [
    'otp' => '123456',
    'eventName' => 'Test Event',
    'eventYear' => '2025',
    'supportEmail' => 'support@example.com'
], function($message) {
    $message->to('your-email@example.com')
            ->subject('Test OTP Email');
});
```

---

## Step 3: Check Logs

### View Laravel Logs:

```bash
tail -f storage/logs/laravel.log
```

Look for errors like:
- `Connection could not be established`
- `Authentication failed`
- `Could not instantiate mailer`
- `Template not found`

### Common Log Errors:

**Error: "Connection could not be established"**
- Solution: Check `MAIL_HOST` and `MAIL_PORT` are correct
- Verify firewall allows outbound SMTP connections

**Error: "Authentication failed"**
- Solution: Check `MAIL_USERNAME` and `MAIL_PASSWORD` are correct
- For Gmail, use App Password, not regular password

**Error: "Template not found"**
- Solution: Ensure `resources/views/emails/delegate-otp.blade.php` exists

---

## Step 4: Verify Email Template Exists

Check if the template file exists:
```bash
ls -la resources/views/emails/delegate-otp.blade.php
```

If it doesn't exist, create it or check the path in the controller.

---

## Step 5: Check Application Environment

### Development Mode (Shows Detailed Errors):

In `.env`:
```env
APP_ENV=local
APP_DEBUG=true
```

This will show actual error messages instead of generic ones.

### Production Mode (Hides Errors):

In `.env`:
```env
APP_ENV=production
APP_DEBUG=false
```

---

## Step 6: Verify Database Connection

OTP is stored in `ticket_otp_requests` table. Check if it's being created:

```sql
SELECT * FROM ticket_otp_requests 
ORDER BY created_at DESC 
LIMIT 5;
```

If OTP records are created but email fails, it's a mail configuration issue.

---

## Step 7: Check Queue Configuration (If Using Queues)

If you're using queues for emails:

```env
QUEUE_CONNECTION=database
```

Then run:
```bash
php artisan queue:work
```

---

## Step 8: Test with Different Mail Driver

### Try Log Driver (for testing):

In `.env`:
```env
MAIL_MAILER=log
```

This will write emails to `storage/logs/laravel.log` instead of sending them.

Check the log:
```bash
tail -f storage/logs/laravel.log
```

You should see the email content in the log.

---

## Step 9: Check PHP Configuration

### Verify PHP mail functions are enabled:

```bash
php -m | grep -i mail
```

### Check `php.ini`:

```ini
[mail function]
SMTP = smtp.gmail.com
smtp_port = 587
sendmail_path = /usr/sbin/sendmail -t -i
```

---

## Step 10: Common Gmail Issues

### Gmail App Password Setup:

1. Go to: https://myaccount.google.com/security
2. Enable 2-Step Verification
3. Go to: https://myaccount.google.com/apppasswords
4. Generate app password for "Mail"
5. Use the 16-character password in `MAIL_PASSWORD`

### Gmail "Less Secure Apps" (Deprecated):

Gmail no longer supports "Less Secure Apps". You MUST use App Passwords.

---

## Quick Fix Checklist

- [ ] `.env` file has correct mail configuration
- [ ] `MAIL_PASSWORD` is correct (App Password for Gmail)
- [ ] `MAIL_HOST` and `MAIL_PORT` are correct
- [ ] Email template exists: `resources/views/emails/delegate-otp.blade.php`
- [ ] Database connection is working
- [ ] `ticket_otp_requests` table exists
- [ ] Check `storage/logs/laravel.log` for detailed errors
- [ ] Test with `MAIL_MAILER=log` to verify email content
- [ ] Verify firewall allows SMTP connections
- [ ] Check spam folder for test emails

---

## Debug Mode

To see detailed error messages, set in `.env`:

```env
APP_ENV=local
APP_DEBUG=true
```

Then the error message will show the actual error instead of generic message.

---

## Contact Information

If issues persist:
1. Check `storage/logs/laravel.log` for full error details
2. Verify mail configuration matches your provider's requirements
3. Test mail sending using `php artisan tinker` with Mail::raw()
4. Check if emails are going to spam folder

---

## Example Working Configuration

### Gmail Example:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yourname@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=yourname@gmail.com
MAIL_FROM_NAME="Event Name"
```

### SendGrid Example:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxxxxxxxxxxxxxxxxxxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Event Name"
```
