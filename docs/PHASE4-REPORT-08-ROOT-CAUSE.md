# Phase 4 Report 8 — Root Cause Vulnerability Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 4 — Enterprise Security & Permission Audit (Red Team)

---

## Executive Summary

Two confirmed software vulnerabilities were identified and fixed during Phase 4:

| Bug ID | Severity | Category | Status |
|--------|----------|----------|--------|
| BUG-P4-001 | Critical | Error Handling / DoS | FIXED |
| BUG-P4-002 | Medium | Security Misconfiguration | FIXED |

---

## BUG-P4-001 — Unhandled Stripe Exception Causing HTTP 500

### Root Cause Analysis

**File:** `app/Http/Controllers/StripeController.php` — `success()` method  
**Category:** CWE-755: Improper Handling of Exceptional Conditions  
**OWASP:** A05:2021 — Security Misconfiguration, A09:2021 — Security Logging & Monitoring  

**Root Cause:**  
The `success()` method called `\Stripe\Checkout\Session::retrieve()` without wrapping it in a try/catch block. The Stripe PHP SDK throws `\Stripe\Exception\InvalidRequestException` when the provided `session_id` does not correspond to a real Stripe checkout session. Because this exception was not caught, it propagated up to Laravel's exception handler, which returned an HTTP 500 response.

**Attack Chain:**
```
Attacker crafts URL: GET /stripe/success?session_id=cs_fake_XXXXXX
  → PHP processes request
  → StripeController::success() called
  → Stripe::retrieve(['id' => 'cs_fake_XXXXXX']) called
  → Stripe API returns error: "No such checkout.session: cs_fake_XXXXXX"
  → \Stripe\Exception\InvalidRequestException thrown
  → No catch block → exception propagates
  → Laravel exception handler: HTTP 500
  → (If APP_DEBUG=true) Stack trace + file paths disclosed
```

**Impact:**
- Availability: Predictable 500 endpoint (automated DoS via rapid requests)
- Confidentiality: Stack trace disclosure when APP_DEBUG=true (file paths, library versions)
- Integrity: No impact (no state change occurs)

**Why It Was Missed:**  
The `success()` method only reaches the Stripe API call if `$request->session_id` is provided. The absence of `session_id` was handled (early return), but the presence of an *invalid* session_id was not.

**Fix:**
```php
try {
    $session = \Stripe\Checkout\Session::retrieve([...]);
} catch (\Stripe\Exception\InvalidRequestException $e) {
    Log::warning('Stripe success: invalid session_id — '.$e->getMessage(), ['user_id' => Auth::id()]);
    return redirect()->route('pricing')->with('error', 'Payment session not found or already processed.');
} catch (\Throwable $e) {
    Log::error('Stripe success error: '.$e->getMessage(), ['user_id' => Auth::id()]);
    return redirect()->route('pricing')->with('error', 'An error occurred verifying your payment.');
}
```

**Fix Rationale:**
- Specific `InvalidRequestException` catch handles the expected "fake ID" case gracefully
- Generic `\Throwable` catch handles unexpected SDK/network failures
- `Log::warning()` and `Log::error()` preserve observability without leaking to user
- User is redirected to `/pricing` with a safe, generic error message

---

## BUG-P4-002 — Missing Security Response Headers

### Root Cause Analysis

**Files:** 
- `app/Http/Middleware/SecurityHeaders.php` (new)
- `bootstrap/app.php` (modified)
- `C:\xampp\php\php.ini` (modified)

**Category:** CWE-693: Protection Mechanism Failure  
**OWASP:** A05:2021 — Security Misconfiguration  

**Root Cause:**  
The application had no middleware or server configuration responsible for setting security response headers. Laravel does not add these headers by default. The absence of these headers exposes users to:

1. **Missing `X-Content-Type-Options: nosniff`** — Browsers may MIME-sniff responses, potentially executing malicious content as a different content type (e.g., treating a text file as JavaScript)

2. **Missing `X-Frame-Options: SAMEORIGIN`** — The application could be embedded in an iframe on a malicious site, enabling clickjacking attacks where users unwittingly perform actions on GoBazaar

3. **Missing `Referrer-Policy`** — Full URLs (including query parameters) may be sent in the `Referer` header to third-party sites, potentially leaking search terms, listing IDs, or user-specific paths

4. **`X-Powered-By: PHP/8.2.12` present** — Disclosed exact PHP version to attackers, enabling targeted exploit selection

**Fix:**
- Created `SecurityHeaders` middleware with `header_remove('X-Powered-By')` (runs before Laravel response object is built) and then sets all required headers on the response object
- Registered as global middleware in `bootstrap/app.php`
- Set `expose_php = Off` in `php.ini` as belt-and-suspenders to prevent PHP itself from adding the header

---

## False Positives Identified

### D-05 — Chat Body XSS (FALSE POSITIVE)
**Initial classification:** FAIL  
**Corrected classification:** PASS — False positive  

**Root Cause of False Positive:** The test script sent a `<script>alert(1)</script>` message and examined the raw JSON response from the chat poll endpoint. The JSON body contained the raw unescaped string, which the test script flagged as "XSS present."

**Why it is safe:** The JSON response delivers raw data to the frontend, which applies escaping before DOM insertion:
- Server-rendered (page load): `{{ $msg->body }}` — Blade double-curly escapes HTML entities
- AJAX-rendered (new messages): `${escHtml(msg.body)}` where `escHtml()` uses `document.createTextNode()`, which is a DOM-based escape that is not vulnerable to XSS

### G-03 — Auto-flag on 3 Reports (TEST SEQUENCING ISSUE)
**Initial classification:** FAIL  
**Corrected classification:** N/A — Test environment contamination  

**Root Cause of False Classification:** The G-03 test submitted reports using the same user accounts that had already submitted reports in G-01. The duplicate report protection blocked most G-03 report submissions, leaving fewer than 3 new reports, so the auto-flag threshold was never reached. This is a test design issue, not an application bug.

---

## Vulnerability Root Cause Grouping

| Root Cause Pattern | Bug IDs | Prevention |
|-------------------|---------|------------|
| Missing exception handling for external API calls | BUG-P4-001 | Always wrap SDK calls in try/catch |
| Missing security headers (no secure defaults) | BUG-P4-002 | Add SecurityHeaders middleware to all new projects |
| PHP version disclosure via php.ini default | BUG-P4-002 | Set `expose_php=Off` in php.ini |
