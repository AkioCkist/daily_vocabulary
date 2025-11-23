# Laravel Sanctum API Authentication Implementation

## Overview

This document outlines the complete implementation of **Laravel Sanctum** for secure API token-based authentication in the Daily Vocabulary application. The implementation includes:

- ✅ Personal Access Token management
- ✅ Token-based API authentication
- ✅ Scope-based authorization
- ✅ Token expiration support
- ✅ Web UI for token management
- ✅ Navigation integration in header

## Architecture

### Components

1. **User Model** (`app/Models/User.php`)
   - Added `HasApiTokens` trait from Sanctum
   - Enables token creation and management methods

2. **API Routes** (`routes/api.php`)
   - Protected routes using `auth:sanctum` middleware
   - Token CRUD endpoints

3. **Token Controller** (`app/Http/Controllers/Api/TokenController.php`)
   - `index()` - List all user tokens
   - `store()` - Create new token
   - `destroy()` - Revoke token
   - `regenerate()` - Revoke old token and create new one

4. **Actions**
   - `CreateApiTokenAction` - Business logic for token creation
   - `RevokeApiTokenAction` - Business logic for token revocation

5. **Web UI**
   - `TokenManagerController` - Display token manager page
   - `TokenManager.vue` - Vue component for token management
   - Integrated into header navigation

6. **Database**
   - `personal_access_tokens` table (created by Sanctum)
   - Stores token hash, scopes, expiration, usage tracking

## Installation & Configuration

### 1. User Model Setup ✅

The `HasApiTokens` trait has been added to the User model:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasApiTokens;
    // ...
}
```

### 2. Sanctum Configuration ✅

Published configuration at `config/sanctum.php`:

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    Sanctum::currentApplicationUrlWithPort(),
))),

'guard' => ['web'],
'expiration' => null,  // No default expiration
'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),
```

**Key Points:**
- Stateful domains configured for SPA authentication
- Uses web guard for initial authentication
- Supports custom token prefixes for security scanning

### 3. API Routes ✅

Routes registered in `routes/api.php`:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('tokens')->name('api.tokens.')->group(function () {
        Route::get('/', [TokenController::class, 'index'])->name('index');
        Route::post('/', [TokenController::class, 'store'])->name('store');
        Route::delete('/{token_id}', [TokenController::class, 'destroy'])->name('destroy');
        Route::patch('/{token_id}/regenerate', [TokenController::class, 'regenerate'])->name('regenerate');
    });
});
```

### 4. Database Migration ✅

Sanctum provides the `personal_access_tokens` table:

```sql
CREATE TABLE personal_access_tokens (
  id BIGINT PRIMARY KEY,
  tokenable_type VARCHAR(255),
  tokenable_id BIGINT,
  name TEXT,
  token VARCHAR(64) UNIQUE,
  abilities TEXT,
  last_used_at TIMESTAMP NULL,
  expires_at TIMESTAMP NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
)
```

**Columns:**
- `token` - Hashed token (secure storage)
- `abilities` - JSON array of scopes
- `last_used_at` - Track token usage
- `expires_at` - Optional expiration date

## API Endpoints

### List Tokens
```http
GET /api/tokens
Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Mobile App",
      "abilities": ["*"],
      "last_used_at": "2 hours ago",
      "created_at": "2025-11-23 10:30:00",
      "expires_at": null,
      "is_expired": false
    }
  ],
  "count": 1
}
```

### Create Token
```http
POST /api/tokens
Content-Type: application/json
Authorization: Bearer {token}

{
  "name": "Third-party Integration",
  "scopes": ["read", "create"],
  "expires_in_days": 90
}
```

**Response:**
```json
{
  "success": true,
  "message": "API token created successfully.",
  "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz",
  "warning": "Save this token in a safe place. You will not be able to see it again!"
}
```

### Revoke Token
```http
DELETE /api/tokens/{token_id}
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "API token revoked successfully."
}
```

### Regenerate Token
```http
PATCH /api/tokens/{token_id}/regenerate
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "API token regenerated successfully.",
  "token": "2|XyZaBcDeFgHiJkLmNoPqRsT",
  "warning": "Save this token in a safe place. The old token has been revoked."
}
```

## Token Usage

### Authenticating with Bearer Token

When consuming your API with a personal access token:

```bash
curl -H "Authorization: Bearer {token}" \
     https://daily-vocabulary.test/api/user
```

### PHP Client Example

```php
$response = Http::withToken($token)
    ->get('https://daily-vocabulary.test/api/user');
```

### JavaScript/Axios Example

```javascript
const response = await axios.get('/api/user', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
    }
});
```

## Scopes

Scopes define what actions a token can perform. Available scopes:

| Scope | Permission |
|-------|-----------|
| `*` | Full access to all endpoints |
| `read` | Read-only operations (GET) |
| `create` | Create operations (POST) |
| `update` | Update operations (PUT/PATCH) |
| `delete` | Delete operations (DELETE) |

### Restricting Endpoints by Scope

```php
// Only allow if token has 'read' ability
Route::get('/words', function () {
    // ...
})->middleware('ability:read');

// Multiple scopes (OR logic)
Route::post('/words', function () {
    // ...
})->middleware('ability:create,admin');

// Multiple scopes (AND logic)
Route::delete('/words/{id}', function () {
    // ...
})->middleware('can:delete,ability:delete');
```

## Web UI - Token Manager

### Location
Accessible at `/tokens` for authenticated users

### Features

1. **View All Tokens**
   - Name, scopes, status, creation date, last usage
   - Real-time status updates (Active, Expired, Never Used)

2. **Create New Token**
   - Input: Token name (required)
   - Input: Select scopes (optional, defaults to full access)
   - Input: Expiration in days (optional)
   - Display: Complete token on creation (shown once only)

3. **Revoke Token**
   - Permanently revoke any token
   - Requires confirmation
   - Immediate effect

4. **Regenerate Token**
   - Creates new token with same name/scopes
   - Revokes old token automatically
   - Preserves expiration duration

### Navigation
Added to header dropdown menu for authenticated users:

```vue
<DropdownLink :href="route('tokens.index')">
    API Token Manager
</DropdownLink>
```

## Security Best Practices

### Token Storage

✅ **DO:**
- Store tokens in environment variables
- Use secure password managers
- Store in server configuration files with restricted permissions
- Rotate tokens regularly

❌ **DON'T:**
- Commit tokens to version control
- Share tokens via email or chat
- Store in client-side storage (localStorage, cookies)
- Use same token across multiple applications

### Token Expiration

- Set expiration dates for temporary access (max 365 days)
- Regenerate tokens periodically
- Immediately revoke compromised tokens

### Scope Limitation

- Use minimal required scopes (principle of least privilege)
- Avoid granting full access (`*`) unless necessary
- Create separate tokens for different purposes/integrations

### Rate Limiting

Consider adding rate limiting to token endpoints:

```php
Route::middleware('throttle:60,1')->group(function () {
    // Token creation/regeneration
});
```

## Implementation Files

### Core Files
```
app/Models/User.php                                    ✅ Added HasApiTokens trait
app/Http/Controllers/Api/TokenController.php          ✅ Token CRUD operations
app/Http/Controllers/TokenManagerController.php       ✅ Web UI display
app/Http/Requests/CreateApiTokenRequest.php           ✅ Request validation
app/Actions/CreateApiTokenAction.php                  ✅ Token creation logic
app/Actions/RevokeApiTokenAction.php                  ✅ Token revocation logic
routes/api.php                                        ✅ API routes
config/sanctum.php                                    ✅ Sanctum configuration
bootstrap/app.php                                     ✅ Added api routes
```

### UI Files
```
resources/js/Pages/TokenManager.vue                   ✅ Token management page
resources/js/Layouts/AuthenticatedLayout.vue          ✅ Header navigation link
```

### Database
```
database/migrations/2025_11_23_225452_*               ✅ personal_access_tokens table
```

## Testing

### Create a Token via CLI

```bash
php artisan tinker

# Create token
$user = User::find(1);
$token = $user->createToken('Testing Token');
echo $token->plainTextToken;

# Use token
$response = Http::withToken('1|...')->get('/api/user');

# Revoke token
$user->tokens()->where('name', 'Testing Token')->first()->delete();
```

### Test Authentication

```bash
# Test with invalid token
curl -H "Authorization: Bearer invalid" \
     https://daily-vocabulary.test/api/tokens

# Expected: 401 Unauthorized

# Test with valid token
curl -H "Authorization: Bearer {valid_token}" \
     https://daily-vocabulary.test/api/tokens

# Expected: 200 OK with token list
```

## Middleware

### `auth:sanctum`
Authenticates using Sanctum tokens OR traditional session-based authentication.

### `ability:scope`
Checks if the token has the specified scope/ability.

```php
Route::delete('/user', function () {
    // ...
})->middleware('ability:delete');
```

## Environment Variables

Optional configuration in `.env`:

```env
SANCTUM_TOKEN_PREFIX=dv     # Token prefix for security scanning
SANCTUM_EXPIRATION=365      # Default token expiration (days)
```

## Common Issues & Solutions

### Issue: Token not working
**Solution:**
- Verify token format: `{id}|{hash}`
- Check token hasn't expired
- Confirm token bearer header format: `Bearer {token}`
- Verify user still exists

### Issue: Scope restrictions not working
**Solution:**
- Ensure middleware applied: `->middleware('ability:scope')`
- Check scope name matches exactly
- Verify token includes scope when created

### Issue: CORS errors
**Solution:**
- Configure CORS in `config/cors.php`
- Add `credentials: 'include'` in frontend requests
- Ensure Origin header is whitelisted

## Future Enhancements

1. **Personal Access Token Audit Log**
   - Track token creation, usage, revocation
   - Log IP addresses, user agents

2. **Token Permissions Matrix**
   - Granular endpoint-level permissions
   - Custom scope definitions per integration

3. **Two-Factor Authentication for Token Creation**
   - Require 2FA to generate new tokens
   - Additional security layer

4. **Token Usage Analytics**
   - Dashboard showing token usage patterns
   - Rate limiting per token

5. **OAuth2 Integration**
   - Add OAuth2 support alongside personal tokens
   - Third-party app integrations

## References

- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [API Authentication Best Practices](https://owasp.org/www-project-api-security/)
- [Token Security Guidelines](https://tools.ietf.org/html/rfc6750)

---

**Implementation Date:** November 23, 2025
**Laravel Version:** 12.0
**Sanctum Version:** 4.0
