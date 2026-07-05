# Phase 4 Report 13 — Production Security Recommendations
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 4 — Enterprise Security & Permission Audit (Red Team)

---

## Priority 1 — Critical (Must Do Before Production Launch)

### P1-01 — Disable Debug Mode

**Current state:** `APP_DEBUG=true` in `.env`  
**Risk:** Full stack traces, file paths, SQL queries, and environment variables exposed to end users on any error  
**Action:**
```env
# .env
APP_DEBUG=false
APP_ENV=production
```
**Note:** Verify error pages show generic messages after disabling. Ensure log channel is set to `stack` or `errorlog` for proper server-side logging.

---

### P1-02 — Enable HTTPS and Secure Session Cookie

**Current state:** Running over HTTP; `SESSION_SECURE_COOKIE=false`  
**Risk:** Session cookies and all data transmitted in plaintext; session hijacking possible on network  
**Actions:**
1. Obtain TLS certificate (Let's Encrypt / cPanel SSL)
2. Set in `.env`:
```env
APP_URL=https://gobazzar.com
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
```
3. Add HSTS header in `SecurityHeaders.php`:
```php
$response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
```
4. Redirect all HTTP to HTTPS in Apache:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]
```

---

### P1-03 — Secure Stripe Keys

**Current state:** Stripe keys in `.env` (acceptable), but must verify:
- Test keys (`sk_test_`) are not in production `.env`
- Webhook secret is correct for production endpoint URL
- Production Stripe mode enabled (`sk_live_`)

**Actions:**
1. Create separate Stripe production account / live mode keys
2. Register webhook endpoint with Stripe for production URL
3. Update `.env` in production:
```env
STRIPE_KEY=pk_live_XXXX
STRIPE_SECRET=sk_live_XXXX
STRIPE_WEBHOOK_SECRET=whsec_XXXX
```

---

## Priority 2 — High (Complete Within 2 Weeks of Launch)

### P2-01 — Add Content Security Policy Header

**Current state:** No CSP header  
**Risk:** XSS attacks could execute injected scripts from any origin  
**Action:** Add to `SecurityHeaders.php`:
```php
$response->headers->set(
    'Content-Security-Policy',
    "default-src 'self'; " .
    "script-src 'self' 'nonce-{$nonce}' https://js.stripe.com; " .
    "frame-src https://js.stripe.com; " .
    "img-src 'self' data: https://res.cloudinary.com; " .
    "style-src 'self' 'unsafe-inline'; " .
    "connect-src 'self' https://api.stripe.com"
);
```
**Note:** Start with `Content-Security-Policy-Report-Only` header to detect violations before enforcing.

---

### P2-02 — Minimize Server Header Disclosure

**Current state:** `Server: Apache/2.4.58 (Win64) OpenSSL/3.1.3 PHP/8.2.12` — full version disclosure  
**Risk:** Attackers can target known CVEs for exact versions  
**Actions:**
1. In `httpd.conf` (Apache — cannot be set in `.htaccess`):
```apache
ServerTokens Prod
ServerSignature Off
```
2. For Windows shared hosting: may need to use `mod_security` or configure via hosting panel

---

### P2-03 — Rate Limiting on Registration

**Current state:** No rate limiting on `POST /register`  
**Risk:** Account farming for spam, brute-force of verification emails  
**Action:** Add to `routes/web.php`:
```php
Route::middleware('throttle:10,1')->post('/register', [RegisteredUserController::class, 'store']);
```

---

### P2-04 — Database User Principle of Least Privilege

**Current state:** App connects as MySQL `root` (inferred from connection config)  
**Risk:** SQL injection (even one query) could `DROP DATABASE` or read `mysql.user`  
**Action:** Create dedicated database user:
```sql
CREATE USER 'gobazzar_app'@'localhost' IDENTIFIED BY 'strong_random_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON gobazzar.* TO 'gobazzar_app'@'localhost';
FLUSH PRIVILEGES;
```
Update `.env`:
```env
DB_USERNAME=gobazzar_app
DB_PASSWORD=strong_random_password
```

---

## Priority 3 — Medium (Address Within 1 Month)

### P3-01 — PHP-Level Upload Directory Protection

**Recommendation:** Verify that the upload directory (`storage/app/public/`) does not have PHP execution enabled.  
Add to Apache site config (not just `.htaccess`):
```apache
<Directory "/path/to/storage/app/public">
    php_flag engine off
    Options -ExecCGI
    AddHandler cgi-script .php .pl .py .jsp .asp .sh .cgi
</Directory>
```

---

### P3-02 — Implement EXIF Stripping on Image Upload

**Recommendation:** Strip metadata from uploaded images before storage.  
**Reason:** JPEG EXIF data can contain GPS coordinates, device serial numbers, and other private data.  
**Action:** Use `intervention/image` or `spatie/image`:
```php
use Spatie\Image\Image;
Image::load($path)->save(); // strips EXIF by default
```

---

### P3-03 — Session Store for Multi-Server Deployment

**Current:** `SESSION_DRIVER=file` — files on disk  
**Issue:** File sessions don't work across multiple servers (load balancing)  
**Action:** Switch to database or Redis session driver for production:
```env
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
```

---

### P3-04 — Queue-Based Email Sending

**Current:** Emails sent synchronously in request cycle  
**Risk:** If mail server is slow, user-facing requests timeout; email credentials in `.env` should use queue worker  
**Action:** Set `QUEUE_CONNECTION=redis` and run `php artisan queue:work`

---

### P3-05 — Log Rotation and Alerting

**Recommendation:**
- Configure `LOG_CHANNEL=stack` with `daily` rotation
- Set up alerting for `Log::error()` and `Log::critical()` calls (e.g., Sentry, Bugsnag, or email alerts)
- Do not store logs longer than 30 days without encryption

---

## Priority 4 — Low / Informational

### P4-01 — Two-Factor Authentication

**Recommendation:** Offer optional 2FA for user accounts, especially admin accounts.  
**Implementation:** `pragmarx/google2fa-laravel` or Filament 2FA plugin for admin panel.

### P4-02 — Admin Account Hardening

**Recommendation:**
- Rename admin route from `/admin` to a non-guessable path (or restrict by IP)
- Enforce 2FA for all `is_admin` accounts
- Log all admin actions to an audit log

### P4-03 — Security.txt File

**Recommendation:** Add `public/.well-known/security.txt` for responsible disclosure:
```
Contact: security@gobazzar.com
Expires: 2027-01-01T00:00:00.000Z
Preferred-Languages: en
```

### P4-04 — Dependency Audit Schedule

**Recommendation:** Run `composer audit` monthly and set up GitHub Dependabot.  
```bash
composer audit
```

---

## Production Deployment Checklist

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] HTTPS enabled + SSL certificate installed
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `SESSION_SAME_SITE=strict`
- [ ] HSTS header configured
- [ ] Stripe live keys configured
- [ ] Stripe webhook URL updated for production domain
- [ ] Database dedicated user (not root)
- [ ] `ServerTokens Prod` in Apache httpd.conf
- [ ] PHP `expose_php=Off` (already done in this audit)
- [ ] File upload directory PHP execution disabled
- [ ] Log rotation configured
- [ ] Error alerting configured
- [ ] `composer audit` run — no known vulnerabilities
- [ ] Admin panel IP restriction or 2FA enabled
- [ ] Security.txt file added

---

## Summary

GoBazaar has a strong security posture for a Laravel application of its scope. The two vulnerabilities found and fixed in Phase 4 were implementation gaps rather than architectural flaws. The underlying security model (IDOR protection, mass assignment protection, plan gates, CSRF, file upload validation) is solid.

The recommended priority-1 and priority-2 items must be completed before production launch. Priority-3 and priority-4 items are best-practice improvements that will further harden the application post-launch.
