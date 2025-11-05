# Jobs Reference

## Overview

Slick Forms uses Laravel's **queue system** for asynchronous processing of time-consuming operations. This ensures form submissions remain fast while background tasks like sending emails, delivering webhooks, and refreshing dynamic options happen asynchronously.

The package includes **3 queued jobs** that handle email notifications, webhook delivery, and dynamic option caching.

## Table of Contents

- [Queue Configuration](#queue-configuration)
- [Job Catalog](#job-catalog)
  - [SendEmailNotification](#sendemailnotification)
  - [SendWebhook](#sendwebhook)
  - [RefreshDynamicOptions](#refreshdynamicoptions)
- [Queue Workers](#queue-workers)
- [Monitoring & Debugging](#monitoring--debugging)
- [Production Deployment](#production-deployment)
- [Best Practices](#best-practices)

---

## Queue Configuration

### Environment Variables

Configure queue behavior in your `.env` file:

```env
# Queue Connection
QUEUE_CONNECTION=redis  # redis, database, sync, beanstalkd, sqs

# Email Queue Settings
SLICK_FORMS_EMAIL_ENABLED=true
SLICK_FORMS_EMAIL_QUEUE=true
SLICK_FORMS_EMAIL_QUEUE_CONNECTION=default

# Webhook Queue Settings
SLICK_FORMS_WEBHOOKS_ENABLED=true
SLICK_FORMS_WEBHOOK_QUEUE=true
SLICK_FORMS_WEBHOOK_TIMEOUT=30
SLICK_FORMS_WEBHOOK_RETRIES=3
SLICK_FORMS_WEBHOOK_RETRY_DELAY=60

# Dynamic Options Caching
SLICK_FORMS_OPTIONS_CACHE=true
SLICK_FORMS_OPTIONS_CACHE_TTL=300
SLICK_FORMS_OPTIONS_TIMEOUT=10
```

### Configuration File

Job settings are defined in `config/slick-forms.php`:

```php
return [
    // Email notifications
    'email' => [
        'enabled' => env('SLICK_FORMS_EMAIL_ENABLED', true),
        'queue' => env('SLICK_FORMS_EMAIL_QUEUE', true),
        'queue_connection' => env('SLICK_FORMS_EMAIL_QUEUE_CONNECTION', 'default'),
    ],

    // Webhooks
    'webhooks' => [
        'enabled' => env('SLICK_FORMS_WEBHOOKS_ENABLED', true),
        'queue' => env('SLICK_FORMS_WEBHOOK_QUEUE', true),
        'timeout' => env('SLICK_FORMS_WEBHOOK_TIMEOUT', 30),
        'max_retries' => env('SLICK_FORMS_WEBHOOK_RETRIES', 3),
        'retry_delay' => env('SLICK_FORMS_WEBHOOK_RETRY_DELAY', 60),
    ],

    // Dynamic options
    'dynamic_options' => [
        'cache_enabled' => env('SLICK_FORMS_OPTIONS_CACHE', true),
        'cache_ttl' => env('SLICK_FORMS_OPTIONS_CACHE_TTL', 300),
        'timeout' => env('SLICK_FORMS_OPTIONS_TIMEOUT', 10),
    ],
];
```

### Queue Connection Setup

#### Redis (Recommended)

Redis provides fast, reliable queueing with support for job priorities and retries.

**Installation**:
```bash
composer require predis/predis
```

**Configuration** (`.env`):
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Database

Use database queuing for simple setups without Redis.

**Configuration** (`.env`):
```env
QUEUE_CONNECTION=database
```

**Create jobs table**:
```bash
php artisan queue:table
php artisan migrate
```

#### Sync (Development Only)

Execute jobs synchronously (not queued) for local testing.

```env
QUEUE_CONNECTION=sync
```

**Warning**: Do not use `sync` in production as it blocks the request until jobs complete.

---

## Job Catalog

### SendEmailNotification

**Class**: `DigitalisStudios\SlickForms\Jobs\SendEmailNotification`

**Purpose**: Sends email notifications asynchronously based on form email templates

**Queue**: `default` (configurable via `SLICK_FORMS_EMAIL_QUEUE_CONNECTION`)

**Retry Attempts**: 3

**Backoff Strategy**: 60 seconds between retries

**Timeout**: None (uses PHP max execution time)

**Dispatched By**: `EmailNotificationService` when a form submission matches email template conditions

---

#### Job Properties

```php
public CustomFormSubmission $submission;  // Form submission that triggered email
public FormEmailTemplate $template;       // Email template to send
public int $tries = 3;                    // Max retry attempts
public int $backoff = 60;                 // Seconds to wait between retries
```

---

#### Configuration

**Enable/Disable Queuing**:
```env
# Queue emails (recommended)
SLICK_FORMS_EMAIL_QUEUE=true

# Send emails synchronously (not recommended)
SLICK_FORMS_EMAIL_QUEUE=false
```

**Custom Queue Connection**:
```env
# Use dedicated email queue
SLICK_FORMS_EMAIL_QUEUE_CONNECTION=emails
```

---

#### Usage Example

The job is automatically dispatched by `EmailNotificationService`:

```php
use DigitalisStudios\SlickForms\Services\EmailNotificationService;

$emailService = app(EmailNotificationService::class);

// Automatically dispatches SendEmailNotification job for matching templates
$emailService->processSubmission($submission);
```

**Manual Dispatch** (advanced):
```php
use DigitalisStudios\SlickForms\Jobs\SendEmailNotification;

SendEmailNotification::dispatch($submission, $template);

// Or dispatch to specific queue
SendEmailNotification::dispatch($submission, $template)->onQueue('emails');

// Or dispatch with delay
SendEmailNotification::dispatch($submission, $template)->delay(now()->addMinutes(5));
```

---

#### How It Works

1. **Fetch Recipients**: Resolves dynamic recipients using variable substitution (e.g., `{customer_email}`)
2. **Render Subject**: Substitutes `{field_name}` variables in subject line
3. **Render Body**: Processes email template with Blade syntax and variable substitution
4. **Attach PDF** (optional): Generates and attaches submission PDF if configured
5. **Send Email**: Sends via Laravel Mail with configured from address
6. **Log Delivery**: Records success/failure in `slick_form_email_logs` table
7. **Dispatch Events**: Fires `EmailNotificationSent` or `EmailNotificationFailed` events

---

#### Error Handling

**Retry Strategy**:
- Attempt 1: Immediate
- Attempt 2: 60 seconds later
- Attempt 3: 120 seconds later (cumulative backoff)
- After 3 failures: Job marked as failed

**Failed Job Logging**:
All failures are logged to `slick_form_email_logs` with error details.

**Listen to Failures**:
```php
use DigitalisStudios\SlickForms\Events\EmailNotificationFailed;

Event::listen(EmailNotificationFailed::class, function ($event) {
    logger()->error("Email failed", [
        'submission_id' => $event->submission->id,
        'error' => $event->error,
    ]);
});
```

---

### SendWebhook

**Class**: `DigitalisStudios\SlickForms\Jobs\SendWebhook`

**Purpose**: Sends form submission data to external webhook endpoints via HTTP POST

**Queue**: `default` (configurable)

**Retry Attempts**: 3

**Backoff Strategy**: 60 seconds between retries (exponential: 60s, 120s, 240s)

**Timeout**: 30 seconds (configurable via `SLICK_FORMS_WEBHOOK_TIMEOUT`)

**Dispatched By**: `WebhookService` when a form submission occurs and webhooks are configured

---

#### Job Properties

```php
public FormWebhook $webhook;              // Webhook configuration
public array $payload;                    // Data to send to webhook
public ?int $submissionId = null;         // Optional submission ID
public int $tries = 3;                    // Max retry attempts
public int $backoff = 60;                 // Seconds between retries
```

---

#### Configuration

**Enable/Disable Webhooks**:
```env
SLICK_FORMS_WEBHOOKS_ENABLED=true
SLICK_FORMS_WEBHOOK_QUEUE=true
```

**Webhook Settings**:
```env
SLICK_FORMS_WEBHOOK_TIMEOUT=30        # HTTP request timeout (seconds)
SLICK_FORMS_WEBHOOK_RETRIES=3         # Max retry attempts
SLICK_FORMS_WEBHOOK_RETRY_DELAY=60    # Seconds between retries
```

---

#### Usage Example

The job is automatically dispatched by `WebhookService`:

```php
use DigitalisStudios\SlickForms\Services\WebhookService;

$webhookService = app(WebhookService::class);

// Automatically dispatches SendWebhook job for each configured webhook
$webhookService->processSubmission($submission);
```

**Manual Dispatch** (advanced):
```php
use DigitalisStudios\SlickForms\Jobs\SendWebhook;

$payload = [
    'submission_id' => $submission->id,
    'form_id' => $submission->form_id,
    'data' => $submission->field_values,
];

SendWebhook::dispatch($webhook, $payload, $submission->id);

// Or dispatch to specific queue
SendWebhook::dispatch($webhook, $payload, $submission->id)->onQueue('webhooks');
```

---

#### How It Works

1. **Build Payload**: Constructs JSON payload from submission data
2. **Add Custom Headers**: Includes authentication tokens, API keys, etc.
3. **Send HTTP POST**: Delivers payload to webhook URL
4. **Log Attempt**: Records delivery attempt in `slick_form_webhook_logs`
5. **Handle Response**: Logs HTTP status code and response body
6. **Retry on Failure**: Automatically retries failed webhooks with exponential backoff
7. **Dispatch Events**: Fires `WebhookSent` or `WebhookFailed` events

---

#### Error Handling

**Retry Strategy**:
- Attempt 1: Immediate
- Attempt 2: 60 seconds later
- Attempt 3: 120 seconds later
- Attempt 4: 240 seconds later (if `max_retries` increased)
- After all retries: Job marked as failed

**Failed Webhook Logging**:
All attempts (successful and failed) are logged to `slick_form_webhook_logs`.

**Listen to Failures**:
```php
use DigitalisStudios\SlickForms\Events\WebhookFailed;

Event::listen(WebhookFailed::class, function ($event) {
    logger()->error("Webhook failed", [
        'webhook_id' => $event->webhook->id,
        'url' => $event->webhook->url,
        'error' => $event->error,
    ]);

    // Disable webhook after repeated failures
    if ($event->webhook->consecutive_failures >= 5) {
        $event->webhook->update(['is_active' => false]);
    }
});
```

---

### RefreshDynamicOptions

**Class**: `DigitalisStudios\SlickForms\Jobs\RefreshDynamicOptions`

**Purpose**: Refreshes cached dynamic options for select/radio/checkbox fields that load data from external APIs or database queries

**Queue**: `default`

**Retry Attempts**: 2

**Backoff Strategy**: 30 seconds between retries

**Timeout**: 10 seconds (configurable via `SLICK_FORMS_OPTIONS_TIMEOUT`)

**Dispatched By**: `DynamicOptionsService` when cache expires or manual refresh is triggered

---

#### Job Properties

```php
public FormField $field;     // Field with dynamic options configuration
public int $tries = 2;       // Max retry attempts
public int $backoff = 30;    // Seconds between retries
```

---

#### Configuration

**Dynamic Options Settings**:
```env
SLICK_FORMS_OPTIONS_CACHE=true         # Enable option caching
SLICK_FORMS_OPTIONS_CACHE_TTL=300      # Cache duration (seconds)
SLICK_FORMS_OPTIONS_TIMEOUT=10         # HTTP/DB query timeout
```

---

#### Usage Example

**Automatic Refresh** (on cache expiration):
```php
use DigitalisStudios\SlickForms\Services\DynamicOptionsService;

$optionsService = app(DynamicOptionsService::class);

// Automatically dispatches RefreshDynamicOptions if cache is expired
$options = $optionsService->getOptions($field);
```

**Manual Refresh**:
```php
use DigitalisStudios\SlickForms\Jobs\RefreshDynamicOptions;

// Force refresh options cache
RefreshDynamicOptions::dispatch($field);

// Schedule refresh for later
RefreshDynamicOptions::dispatch($field)->delay(now()->addMinutes(10));
```

---

#### How It Works

1. **Check Field Configuration**: Reads `dynamic_source` (url or eloquent)
2. **Fetch Options**:
   - **URL Source**: Makes HTTP GET request to configured endpoint
   - **Eloquent Source**: Queries database with configured model and filters
3. **Transform Data**: Maps response to `[value, label]` format
4. **Update Cache**: Stores options in `slick_dynamic_options_cache` table
5. **Set Expiration**: Sets cache TTL based on configuration
6. **Dispatch Events**: Fires `DynamicOptionsLoaded` or `DynamicOptionsFailed` events

---

#### Error Handling

**Retry Strategy**:
- Attempt 1: Immediate
- Attempt 2: 30 seconds later
- After 2 failures: Job marked as failed, stale cache retained

**Failed Option Loading**:
Logs errors to `slick_form_analytics_events` table.

**Listen to Failures**:
```php
use DigitalisStudios\SlickForms\Events\DynamicOptionsFailed;

Event::listen(DynamicOptionsFailed::class, function ($event) {
    logger()->error("Dynamic options failed", [
        'field_id' => $event->field->id,
        'error' => $event->exception->getMessage(),
    ]);

    // Fallback to static options
    $event->field->update(['use_static_fallback' => true]);
});
```

---

## Queue Workers

### Starting Queue Workers

**Single Worker** (processes all queues):
```bash
php artisan queue:work
```

**Specific Queue**:
```bash
php artisan queue:work --queue=default
```

**Multiple Queues** (priority order):
```bash
php artisan queue:work --queue=emails,webhooks,default
```

**With Options**:
```bash
php artisan queue:work \
    --queue=emails,webhooks \
    --tries=3 \
    --timeout=60 \
    --sleep=3 \
    --max-jobs=1000 \
    --max-time=3600
```

---

### Worker Options

| Option | Description | Example |
|--------|-------------|---------|
| `--queue` | Queue(s) to process | `--queue=emails,webhooks` |
| `--tries` | Max attempts before failure | `--tries=3` |
| `--timeout` | Max seconds per job | `--timeout=60` |
| `--sleep` | Seconds to sleep when idle | `--sleep=3` |
| `--max-jobs` | Max jobs before restart | `--max-jobs=1000` |
| `--max-time` | Max seconds before restart | `--max-time=3600` |
| `--memory` | Max memory (MB) before restart | `--memory=512` |

---

### Running Workers in Production

**Supervisor** (Recommended):

Create `/etc/supervisor/conf.d/slick-forms-worker.conf`:

```ini
[program:slick-forms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/app/artisan queue:work redis --queue=emails,webhooks --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/worker.log
stopwaitsecs=3600
```

**Start Supervisor**:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start slick-forms-worker:*
```

**Restart after code changes**:
```bash
php artisan queue:restart
```

---

### Laravel Horizon (Optional)

For advanced queue monitoring, use **Laravel Horizon**:

**Installation**:
```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

**Configuration** (`config/horizon.php`):
```php
'environments' => [
    'production' => [
        'slick-forms' => [
            'connection' => 'redis',
            'queue' => ['emails', 'webhooks', 'default'],
            'balance' => 'auto',
            'processes' => 3,
            'tries' => 3,
        ],
    ],
],
```

**Start Horizon**:
```bash
php artisan horizon
```

**Access Dashboard**:
```
http://your-app.com/horizon
```

---

## Monitoring & Debugging

### View Failed Jobs

**List all failed jobs**:
```bash
php artisan queue:failed
```

**Sample Output**:
```
+------+---------------+-------+-----------------------------+------------+
| ID   | Connection    | Queue | Failed At                   | Exception  |
+------+---------------+-------+-----------------------------+------------+
| 42   | redis         | emails| 2025-01-04 10:30:00         | SMTP error |
| 43   | redis         | webhooks| 2025-01-04 11:15:00       | Timeout    |
+------+---------------+-------+-----------------------------+------------+
```

---

### Retry Failed Jobs

**Retry specific job**:
```bash
php artisan queue:retry 42
```

**Retry all failed jobs**:
```bash
php artisan queue:retry all
```

**Retry failed jobs for specific queue**:
```bash
php artisan queue:retry --queue=emails
```

---

### Delete Failed Jobs

**Delete specific job**:
```bash
php artisan queue:forget 42
```

**Delete all failed jobs**:
```bash
php artisan queue:flush
```

---

### Monitor Job Processing

**Real-time monitoring**:
```bash
php artisan queue:work --verbose
```

**Check queue size**:
```bash
php artisan queue:monitor redis:default,redis:emails,redis:webhooks
```

**Horizon Metrics** (if using Horizon):
- Visit `/horizon` dashboard
- View real-time job throughput
- Monitor failed jobs
- Track recent job history
- View job details and exceptions

---

### Logging

**Enable verbose logging** (`.env`):
```env
LOG_LEVEL=debug
```

**Job-specific logging**:
```php
// Inside job handle() method
logger()->info("Processing email notification", [
    'submission_id' => $this->submission->id,
    'template_id' => $this->template->id,
]);
```

**View logs**:
```bash
tail -f storage/logs/laravel.log
```

---

## Production Deployment

### Deployment Checklist

1. **Choose Queue Driver**:
   - Redis (recommended for production)
   - Database (simple setups)
   - SQS (AWS environments)
   - Never use `sync` in production

2. **Configure Environment Variables**:
   ```env
   QUEUE_CONNECTION=redis
   SLICK_FORMS_EMAIL_QUEUE=true
   SLICK_FORMS_WEBHOOK_QUEUE=true
   ```

3. **Set Up Queue Workers**:
   - Install Supervisor or use Laravel Forge
   - Configure worker processes (3-5 recommended)
   - Enable auto-restart on failure

4. **Configure Retries**:
   ```env
   SLICK_FORMS_WEBHOOK_RETRIES=3
   SLICK_FORMS_WEBHOOK_RETRY_DELAY=60
   ```

5. **Monitor Failed Jobs**:
   - Set up alerts for failed jobs
   - Review failed jobs daily
   - Implement retry strategies

6. **Restart Workers After Deployment**:
   ```bash
   php artisan queue:restart
   ```

---

### Performance Tuning

**High-Volume Forms**:
- Use Redis for queue backend
- Increase worker processes (5-10)
- Enable job batching where applicable
- Consider dedicated email/webhook queues

**Resource Optimization**:
```bash
# Limit memory usage
php artisan queue:work --memory=512

# Limit job count before restart
php artisan queue:work --max-jobs=1000

# Limit execution time before restart
php artisan queue:work --max-time=3600
```

**Queue Priorities**:
```bash
# Process emails before webhooks
php artisan queue:work --queue=emails,webhooks,default
```

---

## Best Practices

### 1. Always Queue in Production

Never process emails or webhooks synchronously:

```env
# ✅ Good
SLICK_FORMS_EMAIL_QUEUE=true
SLICK_FORMS_WEBHOOK_QUEUE=true

# ❌ Bad
SLICK_FORMS_EMAIL_QUEUE=false
SLICK_FORMS_WEBHOOK_QUEUE=false
```

### 2. Monitor Failed Jobs

Set up alerts for failed jobs using event listeners:

```php
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Notification;

Event::listen(JobFailed::class, function (JobFailed $event) {
    Notification::route('slack', config('services.slack.webhook'))
        ->notify(new JobFailedNotification($event));
});
```

### 3. Use Dedicated Queues

Separate email and webhook processing for better control:

```env
SLICK_FORMS_EMAIL_QUEUE_CONNECTION=emails
```

Then start dedicated workers:
```bash
php artisan queue:work --queue=emails
php artisan queue:work --queue=webhooks
```

### 4. Implement Circuit Breakers

Disable failing webhooks automatically:

```php
use DigitalisStudios\SlickForms\Events\WebhookFailed;

Event::listen(WebhookFailed::class, function ($event) {
    if ($event->webhook->consecutive_failures >= 5) {
        $event->webhook->update(['is_active' => false]);

        // Alert administrators
        logger()->warning("Webhook disabled", [
            'webhook_id' => $event->webhook->id,
        ]);
    }
});
```

### 5. Graceful Restarts

Always use `queue:restart` instead of killing workers:

```bash
# ✅ Good - graceful restart
php artisan queue:restart

# ❌ Bad - kills jobs mid-processing
sudo supervisorctl restart slick-forms-worker:*
```

### 6. Log Job Failures

Track all job failures for debugging:

```php
use Illuminate\Support\Facades\Log;

Event::listen(JobFailed::class, function (JobFailed $event) {
    Log::error("Job failed", [
        'job' => $event->job->resolveName(),
        'exception' => $event->exception->getMessage(),
        'payload' => $event->job->payload(),
    ]);
});
```

---

## Related Documentation

- [Events Reference](EVENTS.md) - All 15 events fired by Slick Forms
- [Email Notifications Guide](EMAIL_NOTIFICATIONS.md) - Configure email templates
- [Webhooks Guide](WEBHOOKS.md) - Set up webhook integrations
- [Configuration Reference](../README.md#configuration) - All config options

---

## Support

For questions or issues related to queue jobs:

1. Check Laravel's [Queue Documentation](https://laravel.com/docs/queues)
2. Review [examples](EXAMPLES.md) for practical implementations
3. Report bugs on [GitHub Issues](https://github.com/your-org/slick-forms/issues)

---

**Last Updated**: January 2025
**Package Version**: v2.1.0
