# Laravel Pulse Integration Guide

## Setup Complete ✅

Laravel Pulse has been successfully integrated into your Daily Vocabulary application. Here's what was done:

### 1. **Provider Registration**
- Added `Laravel\Pulse\PulseServiceProvider::class` to `bootstrap/providers.php`
- This enables Pulse to record application metrics automatically

### 2. **Routes Configuration**
- Added Pulse routes to `routes/web.php` with authentication middleware
- Pulse dashboard is accessible at: `http://localhost:8000/pulse`
- **Access Control**: Only admin users can access the Pulse dashboard

### 3. **Database Setup**
- Created migration: `add_is_admin_to_users_table` to add admin role support
- Pulse tables automatically created for storing metrics data

### 4. **Admin User Setup**

To make your user an admin (required to access Pulse), run:

```bash
php artisan app:make-user-admin your-email@example.com
```

Replace `your-email@example.com` with your actual email address.

### 5. **Access the Dashboard**

Once set as admin:
1. Go to: `http://localhost:8000/pulse`
2. You'll see real-time monitoring of:
   - **Requests**: HTTP requests, response times, status codes
   - **Slow Queries**: Database queries taking too long
   - **Exceptions**: Application errors and exceptions
   - **Queued Jobs**: Background job status
   - **Cache**: Cache hit/miss rates
   - **Custom Metrics**: Application-specific metrics

## Configuration Options

Edit `config/pulse.php` to customize:

```php
// Enable/disable Pulse
'enabled' => env('PULSE_ENABLED', true),

// Change the dashboard path
'path' => env('PULSE_PATH', 'pulse'),

// Configure storage (default: database)
'storage' => 'database',

// Data retention (in hours)
'retention' => env('PULSE_RETENTION', 24),

// Sample rate (what percentage to record)
'sample_rate' => env('PULSE_SAMPLE_RATE', 1),
```

## Environment Variables (Optional)

Add to `.env` if you want to customize:

```env
PULSE_ENABLED=true
PULSE_PATH=pulse
PULSE_DOMAIN=null
PULSE_RETENTION=24
PULSE_SAMPLE_RATE=1
```

## Monitoring What Gets Recorded

By default, Pulse records:
- ✅ **Requests & Performance** - All HTTP requests with response times
- ✅ **Database Queries** - Slow queries (> 1 second by default)
- ✅ **Exceptions** - All application errors
- ✅ **Jobs** - Queue job execution status
- ✅ **Cache** - Cache operations

You can disable specific recorders in `config/pulse.php` under the `recorders` section.

## Next Steps

1. Make yourself an admin:
   ```bash
   php artisan app:make-user-admin your-email@example.com
   ```

2. Visit the Pulse dashboard:
   - URL: `http://localhost:8000/pulse`
   - Monitor application performance in real-time

3. **Optional**: Customize alert thresholds in `config/pulse.php`

## Troubleshooting

### Dashboard returns 403 Forbidden
- Ensure your user is marked as admin: `php artisan app:make-user-admin your-email@example.com`
- Check you're authenticated with the correct user

### Not seeing metrics
- Ensure `PULSE_ENABLED=true` in your `.env`
- Check that Pulse has time to collect data (let app run for a few minutes)
- Verify middleware is enabled in `config/pulse.php`

### High database usage
- Reduce `PULSE_SAMPLE_RATE` in `.env` (default: 1, meaning 100%)
- Decrease `PULSE_RETENTION` to keep less history
- Disable specific recorders you don't need

## Files Modified

1. ✅ `bootstrap/providers.php` - Added PulseServiceProvider
2. ✅ `routes/web.php` - Added Pulse routes with auth middleware
3. ✅ `database/migrations/2025_12_04_012625_add_is_admin_to_users_table.php` - Added is_admin column
4. ✅ `app/Console/Commands/MakeUserAdmin.php` - Created command to set admin users

## Summary

Your application is now equipped with enterprise-level monitoring via Laravel Pulse. You can track performance, identify bottlenecks, and monitor system health in real-time!
