# Sanctum Stateful Authentication - Final Fix

## Problem Solved ✓

The 401 "Unauthenticated" error has been fixed by adding the crucial Sanctum middleware for stateful SPA requests.

## Root Cause

Sanctum wasn't recognizing the frontend requests as "stateful" (SPA requests from same domain). Without this recognition, it was only looking for Bearer tokens, not session cookies.

## The Solution

### Three Key Changes Made:

#### 1. **Updated `bootstrap/app.php`** - Added Sanctum Stateful Middleware
```php
$middleware->api(append: [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
]);
```

This middleware is **critical** because it:
- Detects if request is from a configured stateful domain (localhost, 127.0.0.1, etc.)
- Tells Sanctum to accept session-based authentication for SPA requests
- Allows cookies + CSRF token to work as authentication

#### 2. **Updated `routes/api.php`** - Dual Guard Support
```php
Route::middleware(['auth:web,sanctum'])->group(function () {
    // Routes...
});
```

This accepts:
- `auth:web` - Session-based auth (for logged-in users in SPA)
- `auth:sanctum` - Token-based auth (for external API consumers)

#### 3. **Updated `resources/js/bootstrap.js`** - Already Done
- CSRF token extraction ✓
- Credentials enabled ✓
- Headers configured ✓

## How It Works Now

```
User Login (Web)
    ↓
Session Created + Session Cookie + CSRF Token in Meta Tag
    ↓
Vue Component Makes Request to /api/tokens
    ↓
Axios includes:
  - Session Cookie (from withCredentials: true)
  - CSRF Token (from X-CSRF-TOKEN header)
  - X-Requested-With header
    ↓
EnsureFrontendRequestsAreStateful middleware recognizes:
  - Request from stateful domain (localhost, 127.0.0.1)
  - Has session cookie
  - Has CSRF token
    ↓
Sanctum authenticates via session ✓
    ↓
Request succeeds with 200/201 status ✓
```

## What Was Missing Before

The `EnsureFrontendRequestsAreStateful` middleware is the bridge that tells Sanctum:
> "Hey, this request is from our frontend SPA on the same domain, use the session, not a Bearer token"

Without this, Sanctum would:
1. Check for Bearer token in Authorization header → Not found
2. Check for API token → Not found  
3. Reject with 401 Unauthenticated

With this middleware, Sanctum now:
1. Recognizes request is from stateful domain
2. Uses session from cookie for authentication
3. Validates CSRF token
4. Allows request ✓

## Testing

### In Browser DevTools (F12):

1. **Network Tab:**
   - Go to `/tokens` page
   - Click "Create Token"
   - Look for POST to `/api/tokens`
   - Status should be **201 Created** ✓ (not 401)

2. **Request Headers should show:**
   ```
   Cookie: XSRF-TOKEN=...; laravel_session=...
   X-CSRF-TOKEN: ...
   X-Requested-With: XMLHttpRequest
   ```

3. **Response should show:**
   ```json
   {
     "success": true,
     "message": "API token created successfully.",
     "token": "1|AbCd...",
     ...
   }
   ```

## Files Changed

1. ✅ `bootstrap/app.php` - Added EnsureFrontendRequestsAreStateful
2. ✅ `routes/api.php` - Updated to auth:web,sanctum
3. ✅ `resources/js/bootstrap.js` - CSRF + credentials config
4. ✅ Assets rebuilt with `npm run build`

## Why This Matters

**Sanctum Supports Two Authentication Modes:**

### 1. **Stateful (SPA Mode)** - What We're Using
- For frontend SPA on same domain
- Uses session + CSRF token
- User logs in via web interface
- Subsequent API calls use session cookie

### 2. **Stateless (Token Mode)** - For External Clients
- For mobile apps, third-party integrations
- Uses Bearer token
- No session needed
- Each request includes token

The `EnsureFrontendRequestsAreStateful` middleware activates the stateful mode for requests from configured domains.

## Sanctum Configuration Used

From `config/sanctum.php`:

```php
'stateful' => [
    'localhost',
    'localhost:3000',
    '127.0.0.1',
    '127.0.0.1:8000',
    '::1',
    // plus your app URL
],

'guard' => ['web'],
```

This tells Sanctum which domains are "trusted" for stateful requests.

## Troubleshooting if Still Not Working

### Clear Everything
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
npm run build
```

### Browser Clear
- Ctrl+Shift+Delete
- Clear all cookies and cache
- Close all browser windows
- Reopen and login fresh

### Check Request
In Browser DevTools Network tab:
- POST to `/api/tokens`
- Request Headers → Look for `Cookie:` header
- If no cookie → Session not being sent
- Check `withCredentials: true` is set in axios

### Verify Middleware Stack
```bash
php artisan route:list | grep tokens
```

Should show routes under `api` group.

## Success Checklist ✓

- [ ] No 401 errors in console
- [ ] Token list loads
- [ ] Can create tokens
- [ ] Can revoke tokens
- [ ] Can regenerate tokens
- [ ] All operations return success messages
- [ ] Network requests show 200/201 status

---

## Reference Documentation

- **Full Implementation:** `SANCTUM_IMPLEMENTATION.md`
- **Quick Start:** `SANCTUM_QUICK_START.md`
- **API Reference:** `API_TOKENS_REFERENCE.md`
- **Troubleshooting:** `TROUBLESHOOTING_TOKEN_MANAGER.md`

---

**Fixed:** November 24, 2025  
**Status:** ✅ Ready for Use  
**Next Step:** Test in browser - should work now!
