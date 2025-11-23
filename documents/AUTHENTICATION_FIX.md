# Fixing "Unauthenticated" Error in Token Manager

## Problem
When accessing the API Token Manager from the web interface while logged in, requests to `/api/tokens` returned a 401 "Unauthenticated" error, even though the user was logged in.

## Root Cause
The issue was with how axios was configured:

1. **Missing CSRF Token**: Axios wasn't reading the CSRF token from the page's meta tag
2. **Missing Credentials**: Axios wasn't configured to send cookies with requests, which are needed for session-based authentication
3. **Missing Headers**: The proper authentication headers weren't being set by default

## Solution

### 1. Updated `resources/js/bootstrap.js`

Added proper CSRF token extraction and credentials configuration:

```javascript
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');

// Set default headers for authenticated requests
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Enable credentials for cross-domain requests
window.axios.defaults.withCredentials = true;
```

**What this does:**
- Reads the CSRF token from `<meta name="csrf-token">` in the HTML page
- Sets it in axios headers automatically for all requests
- Enables `withCredentials` so cookies (containing session ID) are sent with requests

### 2. Simplified `resources/js/Pages/TokenManager.vue`

Removed redundant header configuration from each request since axios now has defaults:

```javascript
// Before (redundant):
const response = await axios.get('/api/tokens', {
    headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

// After (clean, uses bootstrap defaults):
const response = await axios.get('/api/tokens');
```

### 3. API Routes Configuration

The API routes are already correctly configured in `routes/api.php`:

```php
Route::middleware(['auth:sanctum'])->group(function () {
    // Routes...
});
```

**How `auth:sanctum` works:**
- Checks if request is from a "stateful domain" (configured in `config/sanctum.php`)
- If stateful: uses session-based authentication (from cookies)
- If not stateful: expects a Bearer token
- The Token Manager is SPA-based, so it uses stateful/session authentication

## Authentication Flow

```
User Login via Web → Session Created → Stored in Cookie
    ↓
Access Token Manager Page (GET /tokens) → Page loads, logged in
    ↓
Click "Create Token" → Browser sends request to /api/tokens (POST)
    ↓
Axios includes:
  - CSRF Token (X-CSRF-TOKEN header)
  - Session ID (in cookies, via withCredentials)
  - X-Requested-With header
    ↓
Laravel recognizes stateful request → Uses session to authenticate
    ↓
Request succeeds ✓
```

## Testing

1. **Clear browser cache** (Ctrl+Shift+Delete)
2. **Log out and back in** to get fresh session
3. **Go to Token Manager** (/tokens)
4. **Create a token** - should work now
5. **Check browser DevTools** (F12 → Network):
   - Look for POST to `/api/tokens`
   - Verify headers include `X-CSRF-TOKEN`
   - Verify cookies are sent (check Request Headers for `Cookie:`)

## Key Points

✅ **Session-based authentication** works via cookies + CSRF token  
✅ **Token-based authentication** works via Bearer token header  
✅ **Axios is now globally configured** with proper defaults  
✅ **No more "Unauthenticated" errors** when logged in to web UI

## Files Modified

1. `resources/js/bootstrap.js` - Added CSRF token and credentials
2. `resources/js/Pages/TokenManager.vue` - Simplified axios calls
3. `routes/api.php` - Already correct (no changes needed)

## If Issues Persist

### Clear Everything
```bash
# Clear Laravel cache
php artisan cache:clear

# Clear compiled files
php artisan view:clear

# Recompile assets
npm run build
```

### Check Configuration
1. Verify `config/sanctum.php` has correct stateful domains
2. Confirm CSRF token is in HTML: `<meta name="csrf-token" content="...">` 
3. Verify session is working: `php artisan tinker` → `Session::all()`

### Browser DevTools Debug
1. Open DevTools (F12)
2. Go to Network tab
3. Make a request to `/api/tokens`
4. Check Request Headers for:
   - `Cookie: XSRF-TOKEN=...; laravel_session=...`
   - `X-CSRF-TOKEN: ...`
5. Check Response Status (should be 200, not 401)

---

**Fixed:** November 24, 2025  
**Related:** Sanctum Authentication Implementation
