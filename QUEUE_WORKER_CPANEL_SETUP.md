# Queue Worker Setup Using Laravel Scheduler

This guide explains how to set up a Laravel queue worker using Laravel's built-in scheduler, which is the recommended approach for handling scheduled tasks.

## What We've Implemented

Laravel's scheduler has been configured in `routes/console.php` to:

-   Run the queue worker every minute
-   Prevent overlapping executions
-   Set a 90-second timeout to prevent memory leaks
-   Retry failed jobs up to 3 times
-   Stop when the queue is empty
-   Run in the background for better performance

## Laravel Scheduler Configuration

The scheduler is now configured in `routes/console.php`:

```php
Schedule::command('queue:work --timeout=90 --tries=3 --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
```

## cPanel Cron Job Setup

### Step 1: Single Cron Job Required

With Laravel's scheduler, you only need **one cron job** that runs Laravel's scheduler:

```
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

### Step 2: Customize the Path

Replace the following in the cron command:

-   `username`: Your cPanel username
-   `public_html`: Your Laravel application directory

#### Common Directory Variations:

-   Standard: `/home/username/public_html`
-   Subdomain: `/home/username/subdomain.yourdomain.com`
-   Custom directory: `/home/username/custom_folder`

### Step 3: Alternative Frequency Options

If you want to run the scheduler less frequently:

#### Every 5 Minutes:

```
*/5 * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

#### Every 10 Minutes:

```
*/10 * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

## Troubleshooting Cron Job Issues

### Problem: Cron Job Shows Help Output Instead of Running

If your cron job outputs the Laravel help text instead of running the scheduler, it means the cron job can't find your Laravel application.

#### Solution 1: Use Absolute Paths

```
* * * * * cd /home/username/public_html && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

#### Solution 2: Use Full Path to Artisan

```
* * * * * /usr/bin/php /home/username/public_html/artisan schedule:run >> /dev/null 2>&1
```

#### Solution 3: Set Environment Variables

```
* * * * * cd /home/username/public_html && /usr/bin/php artisan schedule:run --env=production >> /dev/null 2>&1
```

### Problem: Permission Denied

If you get permission errors:

#### Solution: Use cPanel's PHP Selector

1. Go to **Software** → **PHP Selector** in cPanel
2. Note the PHP version and path
3. Use the full path in your cron job:

```
* * * * * cd /home/username/public_html && /opt/alt/php82/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

### Problem: Environment Not Set

If the scheduler runs but doesn't work properly:

#### Solution: Specify Environment

```
* * * * * cd /home/username/public_html && /usr/bin/php artisan schedule:run --env=production >> /dev/null 2>&1
```

### Testing Your Cron Job Path

Before setting up the cron job, test the path manually:

1. **SSH into your server** or use cPanel's Terminal
2. **Test the command**:

    ```bash
    cd /home/username/public_html
    php artisan schedule:list
    ```

3. **If that works, test the scheduler**:

    ```bash
    php artisan schedule:run
    ```

4. **Check PHP path**:
    ```bash
    which php
    ```

### Recommended Cron Job Format

Based on common cPanel setups, use this format:

```
* * * * * cd /home/username/public_html && /usr/bin/php artisan schedule:run --env=production >> storage/logs/scheduler.log 2>&1
```

Or with logging:

```
* * * * * cd /home/username/public_html && /usr/bin/php artisan schedule:run --env=production >> storage/logs/scheduler.log 2>&1
```

## Advantages of Laravel Scheduler

### 1. **Single Cron Job**

-   Only one cron job needed for all scheduled tasks
-   Laravel handles the scheduling logic internally

### 2. **Better Integration**

-   Native Laravel functionality
-   Automatic logging and error handling
-   Built-in overlapping prevention

### 3. **Flexible Scheduling**

-   Easy to modify schedules without changing cron jobs
-   Rich scheduling API (everyMinute, hourly, daily, etc.)
-   Conditional scheduling based on environment

### 4. **Built-in Features**

-   `withoutOverlapping()`: Prevents multiple instances
-   `runInBackground()`: Non-blocking execution
-   Command-line options for queue worker configuration

## Testing the Setup

### 1. **Test the Scheduler Manually**

```bash
cd /path/to/your/laravel/app
php artisan schedule:run
```

### 2. **List Scheduled Commands**

```bash
php artisan schedule:list
```

### 3. **Test Queue Worker Directly**

```bash
php artisan queue:work --timeout=90 --tries=3 --stop-when-empty
```

## Monitoring and Logging

### Enable Scheduler Logging

Add logging to monitor the scheduler:

```
* * * * * cd /home/username/public_html && php artisan schedule:run >> storage/logs/scheduler.log 2>&1
```

### Check Scheduler Status

```bash
# View scheduled tasks
php artisan schedule:list

# Check queue status
php artisan queue:failed

# Monitor queue processing
php artisan queue:work --once
```

## Advanced Configuration

### Multiple Queues

If you have multiple queues, you can schedule them separately:

```php
// In routes/console.php
Schedule::command('queue:work --queue=high --timeout=90 --tries=3 --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('queue:work --queue=default,low --timeout=90 --tries=3 --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
```

### Environment-Specific Scheduling

```php
Schedule::command('queue:work --timeout=90 --tries=3 --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->when(function () {
        return app()->environment('production');
    });
```

### Additional Queue Worker Options

You can customize the queue worker with these command-line options:

```php
Schedule::command('queue:work --timeout=90 --tries=3 --stop-when-empty --max-jobs=100 --memory=128')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
```

**Available Options:**

-   `--timeout=90`: Job timeout in seconds
-   `--tries=3`: Number of times to attempt a job
-   `--stop-when-empty`: Stop when queue is empty
-   `--max-jobs=100`: Stop after processing 100 jobs
-   `--max-time=3600`: Stop after running for 1 hour
-   `--memory=128`: Stop when memory usage exceeds 128MB
-   `--sleep=3`: Sleep for 3 seconds when no jobs available
-   `--queue=high,default`: Process specific queues

## Troubleshooting

### Common Issues:

1. **Scheduler Not Running**

    - Verify the cron job is active in cPanel
    - Check if the path to your Laravel app is correct
    - Ensure PHP has execute permissions

2. **Queue Jobs Not Processing**

    - Check queue driver configuration in `.env`
    - Verify queue tables exist: `php artisan queue:table`
    - Check for failed jobs: `php artisan queue:failed`

3. **Permission Issues**
    - Ensure the cron job user has access to the Laravel directory
    - Check file permissions on storage and logs directories

### Debug Commands:

```bash
# Test scheduler manually
php artisan schedule:run

# List all scheduled tasks
php artisan schedule:list

# Check queue status
php artisan queue:work --once

# View failed jobs
php artisan queue:failed

# Clear failed jobs
php artisan queue:flush
```

## Performance Optimization

### Memory Management

-   The scheduler automatically handles memory cleanup
-   `withoutOverlapping()` prevents resource conflicts
-   `--timeout=90` limits job execution time

### Resource Usage

-   `runInBackground()` prevents blocking
-   `--stop-when-empty` saves resources when no jobs exist
-   Automatic cleanup of completed jobs

## Security Considerations

-   The scheduler runs with the same permissions as the cron job
-   Failed jobs are logged for debugging
-   Consider using a dedicated user for production environments
-   Regularly monitor scheduler logs for issues

## Best Practices

1. **Start with Every Minute**: Begin with `* * * * *` for active applications
2. **Monitor Performance**: Watch server resources and adjust frequency as needed
3. **Use Logging**: Enable scheduler logging for debugging
4. **Test Thoroughly**: Always test in development before production
5. **Backup Strategy**: Ensure queue data is backed up regularly

## Migration from Custom Commands

If you were previously using custom Artisan commands:

1. Remove the custom command from `routes/console.php`
2. Replace with the scheduler configuration
3. Update your cron job to use `php artisan schedule:run`
4. Test thoroughly before deploying to production
