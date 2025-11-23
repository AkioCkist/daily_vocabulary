# API Token Manager - Complete API Reference

## Base URL
```
https://daily-vocabulary.test/api
```

## Authentication
All endpoints require the `Authorization: Bearer {token}` header with a valid Sanctum token.

---

## Endpoints

### 1. Get Current User

Returns the authenticated user's information.

```http
GET /api/user
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "email_verified_at": "2025-11-23T10:00:00Z",
  "created_at": "2025-11-20T10:00:00Z",
  "updated_at": "2025-11-23T10:00:00Z"
}
```

---

### 2. List Personal Access Tokens

Retrieve all personal access tokens created by the authenticated user.

```http
GET /api/tokens
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Mobile App",
      "abilities": ["*"],
      "last_used_at": "2 hours ago",
      "last_used_at_date": "2025-11-23T15:30:00Z",
      "created_at": "2025-11-23 10:00:00",
      "expires_at": null,
      "is_expired": false
    },
    {
      "id": 2,
      "name": "Integration",
      "abilities": ["read", "create"],
      "last_used_at": null,
      "last_used_at_date": null,
      "created_at": "2025-11-23 12:00:00",
      "expires_at": "2025-12-31 23:59:59",
      "is_expired": false
    }
  ],
  "count": 2
}
```

**Fields:**
- `id` - Token ID (use for delete/regenerate)
- `name` - Human-readable token name
- `abilities` - Array of scopes granted to this token
- `last_used_at` - Human-friendly "time ago" string
- `last_used_at_date` - ISO 8601 timestamp of last usage
- `created_at` - Token creation date
- `expires_at` - Token expiration date (null if no expiration)
- `is_expired` - Boolean indicating if token has expired

---

### 3. Create Personal Access Token

Generate a new personal access token with specified scopes and optional expiration.

```http
POST /api/tokens
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Third-party Integration",
  "scopes": ["read", "create"],
  "expires_in_days": 90
}
```

**Request Body:**
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `name` | string | ✅ Yes | Token name (max 255 chars). Must be unique per user. |
| `scopes` | array | ❌ No | Array of scope strings. Defaults to `["*"]` |
| `expires_in_days` | integer | ❌ No | Expiration in days (1-365). Null = no expiration |

**Valid Scopes:**
- `*` - Full access to all endpoints
- `read` - Read-only access (GET requests)
- `create` - Create data (POST requests)
- `update` - Update data (PUT/PATCH requests)
- `delete` - Delete data (DELETE requests)

**Response (201 Created):**
```json
{
  "success": true,
  "message": "API token created successfully.",
  "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYzAbCdEfGhIjKlMnOp",
  "warning": "Save this token in a safe place. You will not be able to see it again!"
}
```

⚠️ **Important:** The `token` value is shown only once. Save it immediately. You cannot retrieve it later. If lost, regenerate a new token.

**Error Responses:**

**(422 Unprocessable Entity) - Validation Error:**
```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "name": ["A token with this name already exists."],
    "expires_in_days": ["Expiration cannot exceed 365 days."]
  }
}
```

**(401 Unauthorized) - Authentication Failed:**
```json
{
  "message": "Unauthenticated."
}
```

---

### 4. Revoke Personal Access Token

Permanently delete (revoke) a personal access token. The token will no longer work.

```http
DELETE /api/tokens/{token_id}
Authorization: Bearer {token}
Accept: application/json
```

**Parameters:**
| Parameter | Type | Location | Required | Notes |
|-----------|------|----------|----------|-------|
| `token_id` | integer | URL path | ✅ Yes | The ID of the token to revoke |

**Response (200 OK):**
```json
{
  "success": true,
  "message": "API token revoked successfully."
}
```

**Error Responses:**

**(404 Not Found) - Token Not Found:**
```json
{
  "success": false,
  "message": "Token not found."
}
```

**(401 Unauthorized) - Authentication Failed:**
```json
{
  "message": "Unauthenticated."
}
```

---

### 5. Regenerate Personal Access Token

Revoke the current token and create a new one with the same name and scopes. Useful for rotating credentials while maintaining the same access level.

```http
PATCH /api/tokens/{token_id}/regenerate
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Parameters:**
| Parameter | Type | Location | Required | Notes |
|-----------|------|----------|----------|-------|
| `token_id` | integer | URL path | ✅ Yes | The ID of the token to regenerate |

**Response (201 Created):**
```json
{
  "success": true,
  "message": "API token regenerated successfully.",
  "token": "2|XyZaBcDeFgHiJkLmNoPqRsT",
  "warning": "Save this token in a safe place. The old token has been revoked."
}
```

**Error Responses:**

**(404 Not Found) - Token Not Found:**
```json
{
  "success": false,
  "message": "Token not found."
}
```

**(401 Unauthorized) - Authentication Failed:**
```json
{
  "message": "Unauthenticated."
}
```

---

## Using Tokens

### Token Format

Sanctum tokens follow this format:
```
{id}|{plaintext_hash}
```

Example: `1|AbCdEfGhIjKlMnOpQrStUvWxYz`

The plaintext token is shown only once during creation. The database stores only the hashed version.

### Adding Token to Requests

#### cURL
```bash
curl -H "Authorization: Bearer {token}" \
     -H "Accept: application/json" \
     https://daily-vocabulary.test/api/user
```

#### Fetch API
```javascript
const token = 'YOUR_TOKEN_HERE';

fetch('/api/user', {
    method: 'GET',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
    }
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error(error));
```

#### Axios
```javascript
import axios from 'axios';

const token = 'YOUR_TOKEN_HERE';
const api = axios.create({
    baseURL: 'https://daily-vocabulary.test',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
    }
});

api.get('/api/user')
    .then(response => console.log(response.data))
    .catch(error => console.error(error));
```

#### PHP Guzzle
```php
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'https://daily-vocabulary.test',
    'headers' => [
        'Authorization' => 'Bearer YOUR_TOKEN_HERE',
        'Accept' => 'application/json',
    ]
]);

$response = $client->get('/api/user');
$userData = json_decode($response->getBody(), true);
```

#### Laravel HTTP Client
```php
use Illuminate\Support\Facades\Http;

$response = Http::withToken('YOUR_TOKEN_HERE')
    ->get('https://daily-vocabulary.test/api/user');

$userData = $response->json();
```

---

## Common Status Codes

| Status | Meaning | Likely Cause |
|--------|---------|--------------|
| 200 | OK | Request successful |
| 201 | Created | Token created successfully |
| 400 | Bad Request | Malformed request body |
| 401 | Unauthorized | Invalid/missing token, token expired |
| 404 | Not Found | Token ID doesn't exist |
| 422 | Unprocessable Entity | Validation errors in request data |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Server Error | Internal server error |

---

## Rate Limiting

Currently, token endpoints follow the application's default rate limiting:
- Check response headers for rate limit information
- If rate limited: `Retry-After` header indicates wait time (seconds)

---

## Security Headers

Include these headers for better security and compatibility:

```http
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
X-Requested-With: XMLHttpRequest
```

---

## Pagination

Currently, token endpoints return all tokens without pagination. Implement pagination if token count exceeds ~1000:

```javascript
// Future: Pagination support
const response = await fetch('/api/tokens?page=1&per_page=50', {
    headers: { 'Authorization': `Bearer ${token}` }
});
```

---

## Error Handling

### Graceful Error Handling Pattern

```javascript
try {
    const response = await fetch('/api/tokens', {
        headers: { 'Authorization': `Bearer ${token}` }
    });

    if (!response.ok) {
        const error = await response.json();
        
        if (response.status === 401) {
            console.error('Token expired or invalid. Please create a new token.');
            // Redirect to token manager
        } else if (response.status === 422) {
            console.error('Validation error:', error.errors);
        } else {
            console.error('API Error:', error.message);
        }
        return;
    }

    const data = await response.json();
    console.log('Success:', data);
} catch (error) {
    console.error('Network error:', error);
}
```

---

## Best Practices

### ✅ DO
1. **Use specific scopes** - Don't use `*` unless necessary
2. **Set expiration dates** - Rotate tokens regularly
3. **Store securely** - Use environment variables, not in code
4. **Include error handling** - Handle all response statuses
5. **Log token usage** - Track which tokens access what

### ❌ DON'T
1. **Log tokens** - Never log the full token value
2. **Commit to git** - Use environment variables
3. **Share in chat/email** - Use secure channels
4. **Hardcode tokens** - Always use configuration
5. **Ignore expiration** - Implement token rotation

---

## Testing with curl

```bash
# Set token variable
TOKEN="your_token_here"

# List tokens
curl -H "Authorization: Bearer $TOKEN" \
     https://daily-vocabulary.test/api/tokens

# Create token
curl -X POST \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     -d '{
       "name": "Test Token",
       "scopes": ["read"],
       "expires_in_days": 30
     }' \
     https://daily-vocabulary.test/api/tokens

# Revoke token (replace 1 with actual token ID)
curl -X DELETE \
     -H "Authorization: Bearer $TOKEN" \
     https://daily-vocabulary.test/api/tokens/1

# Regenerate token
curl -X PATCH \
     -H "Authorization: Bearer $TOKEN" \
     https://daily-vocabulary.test/api/tokens/1/regenerate
```

---

## Postman Collection

Import into Postman for easy API testing:

```json
{
  "info": {
    "name": "Daily Vocabulary API",
    "description": "Sanctum Token Management"
  },
  "item": [
    {
      "name": "Get User",
      "request": {
        "method": "GET",
        "url": "{{base_url}}/api/user",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{token}}"
          },
          {
            "key": "Accept",
            "value": "application/json"
          }
        ]
      }
    },
    {
      "name": "List Tokens",
      "request": {
        "method": "GET",
        "url": "{{base_url}}/api/tokens",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{token}}"
          }
        ]
      }
    }
  ],
  "variable": [
    {
      "key": "base_url",
      "value": "https://daily-vocabulary.test"
    },
    {
      "key": "token",
      "value": ""
    }
  ]
}
```

---

**Version:** 1.0  
**Last Updated:** November 23, 2025  
**API Version:** Sanctum 4.0
