# Token Manager - Troubleshooting Checklist

## "Unauthenticated" Error - SOLVED ✓

### The Fix Applied
- ✅ Updated `bootstrap.js` to extract CSRF token from meta tag
- ✅ Enabled `withCredentials` in axios for cookie-based auth
- ✅ Simplified TokenManager.vue to use axios defaults

### After Update - Do This

1. **Rebuild Frontend Assets**
   ```bash
   npm run build
   ```

2. **Clear Browser Cache**
   - Press `Ctrl+Shift+Delete`
   - Select "All time"
   - Check: Cookies, Cached images/files
   - Click Clear

3. **Fresh Browser Session**
   - Close all browser windows
   - Open new window
   - Go to your app
   - Login again

4. **Test It**
   - Navigate to `/tokens`
   - Click "Create Token"
   - Should work now!

---

## Other Common Issues

### Issue: "Token not found" on Create
**Solution:**
- The token name might already exist
- Try a different token name like "Test Token 2"
- Names must be unique per user

### Issue: "CSRF token mismatch"
**Solution:**
```bash
# Clear the view cache
php artisan view:clear

# Verify meta tag exists in app.blade.php
# Should have: <meta name="csrf-token" content="{{ csrf_token() }}">
```

### Issue: "Failed to fetch tokens" (blank list)
**Solution:**
1. Make sure you're logged in
2. Check DevTools Network tab for 401 errors
3. If still getting 401, verify:
   ```bash
   # Check session is working
   php artisan tinker
   >>> Session::all()
   ```

### Issue: Tokens visible but actions don't work
**Solution:**
- Revoke action should show confirmation
- If confirmation appears but nothing happens:
  1. Check browser console for JavaScript errors (F12)
  2. Check Network tab for failed requests
  3. Verify 200 status on DELETE requests

---

## Verification Steps

### Browser DevTools (F12)

**1. Check CSRF Token**
```
Go to Console and run:
document.querySelector('meta[name="csrf-token"]').content
Should return a long token string like: "abcdef123456..."
```

**2. Check Request Headers**
```
1. Go to Network tab
2. Click "Create Token" button
3. Look for POST /api/tokens
4. Click it, then "Headers" tab
5. Verify these Request Headers:
   - X-Requested-With: XMLHttpRequest
   - X-CSRF-TOKEN: (should have a value)
6. Verify Cookie header includes: laravel_session=...
```

**3. Check Response**
```
1. In same Network request details
2. Click "Response" tab
3. Should show JSON with success: true
4. Should include "token": "1|..."
```

---

## Testing the API Manually

### Using cURL (Command Line)

```bash
# 1. Get CSRF token (from session cookie)
curl -c cookies.txt https://daily-vocabulary.test/tokens

# 2. Extract CSRF token from cookies
# (Should be in laravel_session cookie)

# 3. Use token to create API token
curl -b cookies.txt \
  -H "X-CSRF-TOKEN: YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","scopes":["*"]}' \
  https://daily-vocabulary.test/api/tokens
```

### Using Postman

1. **Set up**
   - Create POST request to `https://your-domain/api/tokens`
   - Go to "Auth" tab → Type: "Cookie"

2. **Get CSRF Token First**
   - Make GET request to `/tokens` (web page)
   - Copy CSRF token from response headers

3. **Create Token Request**
   - Set Body (JSON):
     ```json
     {
       "name": "Postman Test",
       "scopes": ["*"],
       "expires_in_days": 30
     }
     ```
   - Headers:
     - `X-CSRF-TOKEN: {token_from_step_2}`
     - `Accept: application/json`
   - Click Send

---

## If All Else Fails

### Full Reset

```bash
# 1. Clear everything
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# 2. Recompile assets
npm run build

# 3. Check database
php artisan tinker
>>> User::find(1)->tokens()->count()
# Should return number of tokens

# 4. Test API with tinker
>>> $user = User::find(1);
>>> $token = $user->createToken('Test');
>>> echo $token->plainTextToken;
# Should show token like: 1|AbCd...

# 5. Test that token works
>>> $test = User::find(1)->tokens()->first();
>>> $test->can('*');
# Should return: true
```

### Still Not Working?

Check Laravel logs:
```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Then try to create a token in UI
# Errors should appear in the log
```

---

## Configuration Check

### Verify Sanctum Config

```bash
php artisan tinker

# Check stateful domains
>>> config('sanctum.stateful')
# Should include your domain like 'localhost', '127.0.0.1:8000'

# Check guards
>>> config('sanctum.guard')
# Should be: ['web']
```

### Verify Routes

```bash
php artisan route:list | grep tokens

# Should show:
# api/tokens                    GET|HEAD
# api/tokens                    POST
# api/tokens/{token_id}         DELETE
# api/tokens/{token_id}/regenerate  PATCH
```

---

## Success Indicators ✓

- [ ] No "Unauthenticated" error on `/tokens`
- [ ] Token list loads (even if empty)
- [ ] Can create a token successfully
- [ ] Token appears in the list after creation
- [ ] Can revoke tokens with confirmation
- [ ] Can regenerate tokens
- [ ] All actions complete without errors
- [ ] Network requests show 200/201 status codes

---

## Still Need Help?

1. **Check the logs**: `storage/logs/laravel.log`
2. **Check browser console**: F12 → Console tab
3. **Check network requests**: F12 → Network tab
4. **Verify configuration**: Review files in `config/sanctum.php`
5. **Review documentation**: `SANCTUM_IMPLEMENTATION.md`

---

**Last Updated:** November 24, 2025  
**Status:** Fixed and Ready to Use ✓
