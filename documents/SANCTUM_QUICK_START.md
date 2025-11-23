# Laravel Sanctum API Authentication - Quick Start Guide

## What Was Implemented

A complete **Laravel Sanctum** authentication system with:
- ✅ Personal access token management
- ✅ Token-based API authentication  
- ✅ Scope-based authorization
- ✅ Web UI in header navigation
- ✅ Token expiration support
- ✅ Secure token storage (hashed)

## Getting Started

### 1. Access Token Manager

1. Login to your application
2. Click your profile dropdown in the header
3. Select **"API Token Manager"**

### 2. Create Your First Token

1. Click **"Create Token"** button
2. Enter a token name (e.g., "Mobile App", "Integration")
3. Select scopes (defaults to full access)
4. Optional: Set expiration (1-365 days)
5. Click **"Create Token"**
6. **Copy and save the token immediately** - you won't see it again!

### 3. Use Your Token

#### cURL
```bash
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
     https://daily-vocabulary.test/api/user
```

#### JavaScript/Fetch
```javascript
const response = await fetch('/api/user', {
    headers: {
        'Authorization': 'Bearer YOUR_TOKEN_HERE',
    }
});
const user = await response.json();
```

#### PHP/Guzzle
```php
use Illuminate\Support\Facades\Http;

$response = Http::withToken('YOUR_TOKEN_HERE')
    ->get('/api/user');
```

## API Endpoints

### Get Current User
```http
GET /api/user
Authorization: Bearer {token}
```

### List Your Tokens
```http
GET /api/tokens
Authorization: Bearer {token}
```

### Create New Token
```http
POST /api/tokens
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "My Integration",
  "scopes": ["read", "create"],
  "expires_in_days": 90
}
```

### Revoke Token
```http
DELETE /api/tokens/{token_id}
Authorization: Bearer {token}
```

### Regenerate Token
```http
PATCH /api/tokens/{token_id}/regenerate
Authorization: Bearer {token}
```

## Scopes

Control what your token can do:

| Scope | Allows |
|-------|--------|
| `*` | Full access (use with caution) |
| `read` | Read data (GET requests) |
| `create` | Create data (POST requests) |
| `update` | Modify data (PUT/PATCH requests) |
| `delete` | Delete data (DELETE requests) |

## Security Tips

### ✅ DO
- Store tokens in environment variables
- Use specific scopes, not `*`
- Set expiration dates
- Rotate tokens regularly
- Revoke compromised tokens immediately

### ❌ DON'T
- Commit tokens to git
- Share via email/Slack
- Store in browser localStorage
- Use same token everywhere
- Leave tokens with "Full Access" enabled

## Troubleshooting

### "401 Unauthorized"
- Check token format: must be `{id}|{hash}`
- Verify header: `Authorization: Bearer {token}`
- Check if token is expired
- Ensure user account still exists

### "Token not found when deleting"
- Verify you're using correct token ID
- Token may already be revoked
- Refresh the page to see latest tokens

### Token not showing in list
- Create a new token using the form
- Wait a moment for page refresh
- Check browser console for errors

## Examples

### Third-party Integration

```php
// config/services.php
'daily_vocabulary_api' => [
    'token' => env('DAILY_VOCAB_API_TOKEN'),
    'url' => env('DAILY_VOCAB_API_URL', 'https://daily-vocabulary.test'),
],

// Usage
$token = config('services.daily_vocabulary_api.token');
$url = config('services.daily_vocabulary_api.url');

$response = Http::withToken($token)
    ->get("$url/api/user");
```

### Mobile App Authentication

```javascript
// Save token securely on device
const token = 'YOUR_TOKEN_FROM_API';
localStorage.setItem('api_token', token);

// Use token in requests
const headers = {
    'Authorization': `Bearer ${localStorage.getItem('api_token')}`,
    'Content-Type': 'application/json',
};

fetch('/api/user', { headers })
    .then(r => r.json())
    .then(user => console.log(user));
```

### Rotating Tokens

```javascript
// Get old token ID
const tokens = await axios.get('/api/tokens');
const oldToken = tokens.data.data[0];

// Regenerate
const response = await axios.patch(
    `/api/tokens/${oldToken.id}/regenerate`
);

// Save new token
console.log('New token:', response.data.token);
```

## File Structure

```
Core Implementation:
  ✅ app/Models/User.php
  ✅ app/Http/Controllers/Api/TokenController.php
  ✅ app/Actions/CreateApiTokenAction.php
  ✅ app/Actions/RevokeApiTokenAction.php
  ✅ routes/api.php

UI Components:
  ✅ resources/js/Pages/TokenManager.vue
  ✅ resources/js/Layouts/AuthenticatedLayout.vue
  ✅ app/Http/Controllers/TokenManagerController.php

Configuration:
  ✅ config/sanctum.php
  ✅ bootstrap/app.php

Database:
  ✅ database/migrations/2025_11_23_225452_*
```

## Next Steps

1. **Create your first token** via the Token Manager UI
2. **Test the API** with your token using cURL or Postman
3. **Integrate with external apps** using the token
4. **Set up monitoring** for token usage and expiration
5. **Document** your API endpoints and required scopes

## Support

For detailed implementation information, see:
- `SANCTUM_IMPLEMENTATION.md` - Complete technical documentation
- Laravel Sanctum Docs: https://laravel.com/docs/sanctum

---

**Version:** 1.0  
**Last Updated:** November 23, 2025
