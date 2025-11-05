# Webhooks

**Slick Forms v2.0+** - Complete guide for webhook integration and configuration

---

## Table of Contents

- [Overview](#overview)
- [Quick Start](#quick-start)
- [Webhook Configuration](#webhook-configuration)
- [Payload Structure](#payload-structure)
- [Custom Headers](#custom-headers)
- [Payload Formats](#payload-formats)
- [Retry Logic](#retry-logic)
- [Conditional Triggers](#conditional-triggers)
- [Webhook Logs](#webhook-logs)
- [Testing Webhooks](#testing-webhooks)
- [Security Best Practices](#security-best-practices)
- [Common Integrations](#common-integrations)
- [Troubleshooting](#troubleshooting)
- [Events](#events)
- [API Reference](#api-reference)

---

## Overview

Webhooks allow Slick Forms to POST submission data to external URLs in real-time. When a form is submitted, webhooks trigger HTTP requests to your configured endpoints, enabling integration with third-party services, CRMs, analytics platforms, and custom applications.

### Key Features

- ✅ Automatic HTTP POST on form submission
- ✅ Multiple webhooks per form
- ✅ Retry logic with exponential backoff (3 attempts)
- ✅ Custom headers for authentication
- ✅ Multiple payload formats (JSON, Form Data, XML)
- ✅ Conditional triggering based on form values
- ✅ Comprehensive delivery logs
- ✅ Test mode for webhook validation
- ✅ Queue support for reliable delivery

---

## Quick Start

### 1. Create Webhook

```php
use DigitalisStudios\SlickForms\Models\FormWebhook;
use DigitalisStudios\SlickForms\Models\CustomForm;

$form = CustomForm::find(1);

$webhook = FormWebhook::create([
    'form_id' => $form->id,
    'name' => 'CRM Integration',
    'url' => 'https://api.example.com/leads',
    'method' => 'POST',
    'format' => 'json', // json, form_data, xml
    'headers' => [
        'Authorization' => 'Bearer your-api-token',
        'Content-Type' => 'application/json',
    ],
    'is_active' => true,
    'retry_attempts' => 3,
    'retry_delay_seconds' => 60,
]);
```

### 2. Submit Form

When a user submits the form, the webhook automatically fires:

```php
// Automatically handled by Slick Forms
// FormSubmitted event → SendWebhook job → HTTP POST to webhook URL
```

### 3. Check Delivery Status

```php
use DigitalisStudios\SlickForms\Models\FormWebhookLog;

$logs = FormWebhookLog::where('webhook_id', $webhook->id)
    ->latest()
    ->get();

foreach ($logs as $log) {
    echo "Status: {$log->status}\n";
    echo "Response: {$log->response_status}\n";
    echo "Error: {$log->error_message}\n\n";
}
```

---

## Webhook Configuration

### Database Schema

Webhooks are stored in the `slick_form_webhooks` table:

```php
Schema::create('slick_form_webhooks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('form_id')->constrained('slick_forms')->cascadeOnDelete();
    $table->string('name'); // "CRM Integration", "Zapier Trigger", etc.
    $table->text('url'); // https://api.example.com/webhook
    $table->string('method')->default('POST'); // POST, PUT, PATCH
    $table->string('format')->default('json'); // json, form_data, xml
    $table->json('headers')->nullable(); // Custom HTTP headers
    $table->json('trigger_conditions')->nullable(); // Conditional logic
    $table->boolean('is_active')->default(true);
    $table->integer('retry_attempts')->default(3);
    $table->integer('retry_delay_seconds')->default(60);
    $table->integer('timeout_seconds')->default(30);
    $table->timestamps();
});
```

---

### Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `name` | string | required | Human-readable identifier |
| `url` | string | required | Webhook endpoint URL |
| `method` | string | POST | HTTP method (POST, PUT, PATCH) |
| `format` | string | json | Payload format (json, form_data, xml) |
| `headers` | array | null | Custom HTTP headers |
| `trigger_conditions` | array | null | Conditional logic (same as field conditions) |
| `is_active` | boolean | true | Enable/disable webhook |
| `retry_attempts` | integer | 3 | Max retry attempts |
| `retry_delay_seconds` | integer | 60 | Initial retry delay |
| `timeout_seconds` | integer | 30 | HTTP request timeout |

---

### Environment Configuration

Configure webhooks globally in `config/slick-forms.php`:

```php
return [
    'webhooks' => [
        // Enable webhooks globally
        'enabled' => env('SLICK_FORMS_WEBHOOKS_ENABLED', true),

        // Queue webhook deliveries
        'queue' => env('SLICK_FORMS_WEBHOOK_QUEUE', true),

        // Queue connection to use
        'queue_connection' => env('SLICK_FORMS_WEBHOOK_QUEUE_CONNECTION', 'redis'),

        // Default timeout for HTTP requests
        'timeout' => env('SLICK_FORMS_WEBHOOK_TIMEOUT', 30),

        // Enable webhook signature verification
        'verify_ssl' => env('SLICK_FORMS_WEBHOOK_VERIFY_SSL', true),
    ],
];
```

### Environment Variables

```bash
# .env configuration
SLICK_FORMS_WEBHOOKS_ENABLED=true
SLICK_FORMS_WEBHOOK_QUEUE=true
SLICK_FORMS_WEBHOOK_QUEUE_CONNECTION=redis
SLICK_FORMS_WEBHOOK_TIMEOUT=30
SLICK_FORMS_WEBHOOK_VERIFY_SSL=true
```

---

## Payload Structure

### Default JSON Payload

```json
{
  "event": "form.submitted",
  "submission_id": 12345,
  "form": {
    "id": 1,
    "name": "Contact Form",
    "description": "Get in touch with us"
  },
  "submitted_at": "2025-01-04T10:30:00Z",
  "ip_address": "192.168.1.1",
  "fields": {
    "full_name": {
      "value": "John Doe",
      "label": "Full Name",
      "type": "text"
    },
    "email": {
      "value": "john@example.com",
      "label": "Email Address",
      "type": "email"
    },
    "message": {
      "value": "I'm interested in your services",
      "label": "Message",
      "type": "textarea"
    }
  }
}
```

### Accessing Payload Data

```php
// In your webhook endpoint
$data = json_decode(file_get_contents('php://input'), true);

$submissionId = $data['submission_id'];
$formName = $data['form']['name'];
$email = $data['fields']['email']['value'];
$message = $data['fields']['message']['value'];

// Process the data...
```

---

### Custom Payload Template

Transform the payload structure using payload templates (future feature):

```php
$webhook->payload_template = [
    'lead' => [
        'name' => '{{full_name}}',
        'email' => '{{email}}',
        'source' => 'web_form',
        'form_id' => '{{form.id}}',
    ],
    'metadata' => [
        'submitted_at' => '{{submitted_at}}',
        'ip' => '{{ip_address}}',
    ],
];
```

---

## Custom Headers

Add authentication tokens, API keys, and custom headers.

### Bearer Token Authentication

```php
$webhook->headers = [
    'Authorization' => 'Bearer sk_live_1234567890abcdef',
];
```

### API Key Authentication

```php
$webhook->headers = [
    'X-API-Key' => 'your-secret-api-key',
];
```

### Basic Authentication

```php
$username = 'api_user';
$password = 'api_password';
$credentials = base64_encode("$username:$password");

$webhook->headers = [
    'Authorization' => "Basic $credentials",
];
```

### Custom Headers

```php
$webhook->headers = [
    'Content-Type' => 'application/json',
    'X-Webhook-Source' => 'Slick Forms',
    'X-Form-ID' => $form->id,
    'User-Agent' => 'SlickForms/2.0',
];
```

---

## Payload Formats

### JSON (Default)

```php
$webhook->format = 'json';

// Sends:
// Content-Type: application/json
// Body: {"event":"form.submitted","submission_id":123,...}
```

### Form Data

```php
$webhook->format = 'form_data';

// Sends:
// Content-Type: application/x-www-form-urlencoded
// Body: event=form.submitted&submission_id=123&...
```

### XML

```php
$webhook->format = 'xml';

// Sends:
// Content-Type: application/xml
// Body:
// <?xml version="1.0"?>
// <webhook>
//   <event>form.submitted</event>
//   <submission_id>123</submission_id>
//   ...
// </webhook>
```

---

## Retry Logic

Webhooks automatically retry failed deliveries with exponential backoff.

### Default Retry Strategy

1. **Attempt 1**: Immediate delivery on submission
2. **Attempt 2**: 60 seconds later (1 minute)
3. **Attempt 3**: 120 seconds later (2 minutes)
4. **Attempt 4**: 240 seconds later (4 minutes)

### Configure Retry Behavior

```php
$webhook->retry_attempts = 5; // Max 5 attempts
$webhook->retry_delay_seconds = 30; // Start with 30-second delay
$webhook->save();
```

### Exponential Backoff Calculation

```php
$delay = $webhook->retry_delay_seconds * pow(2, $retryCount);

// Example with retry_delay_seconds = 60:
// Retry 1: 60 * 2^0 = 60 seconds (1 minute)
// Retry 2: 60 * 2^1 = 120 seconds (2 minutes)
// Retry 3: 60 * 2^2 = 240 seconds (4 minutes)
// Retry 4: 60 * 2^3 = 480 seconds (8 minutes)
```

### Manual Retry

```php
use DigitalisStudios\SlickForms\Services\WebhookService;
use DigitalisStudios\SlickForms\Models\FormWebhookLog;

$log = FormWebhookLog::find($failedLogId);
$service = app(WebhookService::class);

// Retry failed webhook
$service->retryFailedWebhook($log);
```

### Retry Conditions

Webhooks retry on:
- HTTP 5xx errors (500, 502, 503, 504)
- Network timeouts
- Connection failures

Webhooks **do not** retry on:
- HTTP 4xx errors (400, 401, 403, 404) - client errors
- HTTP 200-299 (success responses)

---

## Conditional Triggers

Send webhooks only when specific conditions are met.

### Configuration

```php
$webhook->trigger_conditions = [
    'logic' => 'all', // 'all' (AND) or 'any' (OR)
    'conditions' => [
        [
            'field' => 'inquiry_type',
            'operator' => 'equals',
            'value' => 'sales',
        ],
        [
            'field' => 'budget',
            'operator' => 'greater_than',
            'value' => 10000,
        ],
    ],
];

$webhook->save();
```

### Supported Operators

Same as field conditional logic:

- `equals`, `not_equals`
- `contains`, `not_contains`
- `starts_with`, `ends_with`
- `greater_than`, `less_than`, `>=`, `<=`
- `in`, `not_in`
- `is_empty`, `is_not_empty`
- `regex`

### Example: Sales Lead Routing

```php
// High-value leads → CRM webhook
$crmWebhook->trigger_conditions = [
    'logic' => 'all',
    'conditions' => [
        ['field' => 'budget', 'operator' => 'greater_than', 'value' => 50000],
        ['field' => 'decision_timeframe', 'operator' => 'in', 'value' => ['immediate', '1-3 months']],
    ],
];

// Support requests → Zendesk webhook
$supportWebhook->trigger_conditions = [
    'logic' => 'all',
    'conditions' => [
        ['field' => 'inquiry_type', 'operator' => 'equals', 'value' => 'support'],
    ],
];

// General inquiries → Email webhook
$emailWebhook->trigger_conditions = []; // No conditions = always trigger
```

---

## Webhook Logs

All webhook deliveries are logged to `slick_form_webhook_logs` table.

### Log Schema

```php
Schema::create('slick_form_webhook_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('webhook_id')->constrained('slick_form_webhooks')->cascadeOnDelete();
    $table->foreignId('submission_id')->nullable()->constrained('slick_form_submissions');
    $table->string('event_type')->default('submission'); // submission, test, manual
    $table->text('request_url');
    $table->json('request_headers')->nullable();
    $table->longText('request_body')->nullable();
    $table->integer('response_status')->nullable(); // HTTP status code
    $table->json('response_headers')->nullable();
    $table->longText('response_body')->nullable();
    $table->string('status'); // sent, failed, pending
    $table->integer('retry_count')->default(0);
    $table->text('error_message')->nullable();
    $table->timestamp('sent_at')->nullable();
    $table->timestamps();
});
```

### Viewing Logs

```php
use DigitalisStudios\SlickForms\Models\FormWebhookLog;

// Get recent deliveries
$logs = FormWebhookLog::where('webhook_id', $webhook->id)
    ->with('webhook', 'submission')
    ->latest()
    ->paginate(50);

foreach ($logs as $log) {
    echo "Status: {$log->status}\n";
    echo "HTTP {$log->response_status}\n";
    echo "Sent: {$log->sent_at}\n";
    echo "Retries: {$log->retry_count}\n";

    if ($log->error_message) {
        echo "Error: {$log->error_message}\n";
    }

    echo "\n";
}
```

### Filter by Status

```php
// Failed deliveries
$failed = FormWebhookLog::where('status', 'failed')
    ->where('webhook_id', $webhook->id)
    ->get();

// Successful deliveries
$successful = FormWebhookLog::where('status', 'sent')
    ->where('webhook_id', $webhook->id)
    ->get();

// Pending retries
$pending = FormWebhookLog::where('status', 'pending')
    ->where('webhook_id', $webhook->id)
    ->get();
```

### Log Retention

Configure automatic log cleanup:

```php
// In config/slick-forms.php
'webhooks' => [
    'log_retention_days' => 90, // Delete logs older than 90 days
],

// Scheduled cleanup job
class CleanupWebhookLogs extends Command
{
    public function handle()
    {
        $retentionDays = config('slick-forms.webhooks.log_retention_days', 90);

        FormWebhookLog::where('created_at', '<', now()->subDays($retentionDays))
            ->delete();

        $this->info('Webhook logs cleaned up successfully');
    }
}
```

---

## Testing Webhooks

### Test Webhook Configuration

```php
use DigitalisStudios\SlickForms\Services\WebhookService;

$service = app(WebhookService::class);
$webhook = FormWebhook::find(1);

// Send test request
$result = $service->testWebhook($webhook);

if ($result['success']) {
    echo "✅ Webhook test successful!\n";
    echo "Status: {$result['status']}\n";
    echo "Duration: {$result['duration']}s\n";
    echo "Response: {$result['response_body']}\n";
} else {
    echo "❌ Webhook test failed!\n";
    echo "Error: {$result['error']}\n";
}
```

### Test Payload

The test payload includes sample data:

```json
{
  "test": true,
  "webhook_name": "CRM Integration",
  "form_id": 1,
  "form_name": "Contact Form",
  "timestamp": "2025-01-04T10:30:00Z",
  "sample_data": {
    "name": "Test User",
    "email": "test@example.com",
    "message": "This is a test webhook delivery"
  }
}
```

---

### Testing Tools

#### webhook.site

Free online service for testing webhooks.

1. Visit [webhook.site](https://webhook.site)
2. Copy the unique URL
3. Configure webhook with that URL
4. Submit test form
5. View request details on webhook.site

```php
$webhook->url = 'https://webhook.site/unique-id-here';
$webhook->save();
```

#### RequestBin

Another popular webhook testing service.

```php
$webhook->url = 'https://requestbin.com/r/your-bin-id';
$webhook->save();
```

#### ngrok (Local Development)

Test webhooks against your local development environment:

```bash
# Start ngrok tunnel
ngrok http 8000

# Use ngrok URL in webhook
# https://abc123.ngrok.io/api/webhook
```

---

### Debugging Failed Webhooks

```php
$log = FormWebhookLog::where('status', 'failed')->latest()->first();

echo "Request URL: {$log->request_url}\n";
echo "Request Headers: " . json_encode($log->request_headers, JSON_PRETTY_PRINT) . "\n";
echo "Request Body: {$log->request_body}\n";
echo "Response Status: {$log->response_status}\n";
echo "Response Body: {$log->response_body}\n";
echo "Error: {$log->error_message}\n";
```

---

## Security Best Practices

### 1. Use HTTPS

Always use HTTPS endpoints to protect data in transit:

```php
// Good
$webhook->url = 'https://api.example.com/webhook';

// Bad - data sent unencrypted
$webhook->url = 'http://api.example.com/webhook';
```

---

### 2. Authenticate Requests

#### Webhook Signature Verification

Add signature to webhook headers:

```php
// In Slick Forms
$payload = json_encode($data);
$signature = hash_hmac('sha256', $payload, $secret);

$webhook->headers = [
    'X-Webhook-Signature' => $signature,
];
```

#### Verify Signature in Endpoint

```php
// In your webhook endpoint
$payload = file_get_contents('php://input');
$receivedSignature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'];

$expectedSignature = hash_hmac('sha256', $payload, $secret);

if (!hash_equals($expectedSignature, $receivedSignature)) {
    http_response_code(401);
    exit('Invalid signature');
}

// Signature valid - process webhook
```

---

### 3. Validate Webhook Source

Add webhook source verification:

```php
$webhook->headers = [
    'X-Webhook-Source' => 'Slick Forms',
    'X-Webhook-ID' => $webhook->id,
];
```

```php
// In your endpoint
if ($_SERVER['HTTP_X_WEBHOOK_SOURCE'] !== 'Slick Forms') {
    http_response_code(403);
    exit('Invalid webhook source');
}
```

---

### 4. IP Whitelist

Restrict webhook delivery to specific IP ranges:

```php
// In config/slick-forms.php
'webhooks' => [
    'allowed_hosts' => [
        'api.example.com',
        'webhook.company.com',
    ],
    'blocked_hosts' => [
        'localhost',
        '127.0.0.1',
        '192.168.*',
    ],
],
```

---

### 5. Rate Limiting

Protect webhook endpoints with rate limiting:

```php
// In your webhook endpoint
use Illuminate\Support\Facades\RateLimiter;

$key = 'webhook:' . request()->ip();

if (RateLimiter::tooManyAttempts($key, 60)) {
    http_response_code(429);
    exit('Too many requests');
}

RateLimiter::hit($key, 60); // 60 requests per minute

// Process webhook...
```

---

### 6. Timeout Configuration

Set reasonable timeouts to prevent hanging:

```php
$webhook->timeout_seconds = 10; // 10 seconds for fast APIs
$webhook->save();
```

---

### 7. Secrets Management

Never hardcode secrets in webhook configuration:

```php
// Bad
$webhook->headers = [
    'Authorization' => 'Bearer sk_live_1234567890',
];

// Good - use environment variables
$webhook->headers = [
    'Authorization' => 'Bearer ' . env('CRM_API_TOKEN'),
];
```

---

## Common Integrations

### Zapier

```php
$webhook = FormWebhook::create([
    'form_id' => $form->id,
    'name' => 'Zapier Integration',
    'url' => 'https://hooks.zapier.com/hooks/catch/123456/abcdef/',
    'method' => 'POST',
    'format' => 'json',
]);
```

---

### Make (Integromat)

```php
$webhook = FormWebhook::create([
    'form_id' => $form->id,
    'name' => 'Make Integration',
    'url' => 'https://hook.integromat.com/your-webhook-url',
    'method' => 'POST',
    'format' => 'json',
]);
```

---

### Slack

```php
$webhook = FormWebhook::create([
    'form_id' => $form->id,
    'name' => 'Slack Notifications',
    'url' => 'https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXX',
    'method' => 'POST',
    'format' => 'json',
]);

// Custom payload for Slack
$webhook->payload_template = [
    'text' => 'New form submission!',
    'blocks' => [
        [
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => '*New Contact Form Submission*\n*Name:* {{full_name}}\n*Email:* {{email}}',
            ],
        ],
    ],
];
```

---

### HubSpot CRM

```php
$webhook = FormWebhook::create([
    'form_id' => $form->id,
    'name' => 'HubSpot CRM',
    'url' => 'https://api.hubapi.com/contacts/v1/contact',
    'method' => 'POST',
    'format' => 'json',
    'headers' => [
        'Authorization' => 'Bearer ' . env('HUBSPOT_API_KEY'),
    ],
]);
```

---

### Salesforce

```php
$webhook = FormWebhook::create([
    'form_id' => $form->id,
    'name' => 'Salesforce Lead',
    'url' => 'https://yourinstance.salesforce.com/services/data/v52.0/sobjects/Lead',
    'method' => 'POST',
    'format' => 'json',
    'headers' => [
        'Authorization' => 'Bearer ' . $salesforceToken,
        'Content-Type' => 'application/json',
    ],
]);
```

---

## Troubleshooting

### Webhook Not Firing

**Check:**
1. Webhook is `is_active = true`
2. Global webhooks enabled: `SLICK_FORMS_WEBHOOKS_ENABLED=true`
3. Conditional triggers are satisfied
4. Queue is running: `php artisan queue:work`

**Debug:**
```php
// Check webhook configuration
$webhook = FormWebhook::find(1);
dd($webhook->is_active, config('slick-forms.webhooks.enabled'));

// Check conditional logic
$service = app(WebhookService::class);
$shouldTrigger = $service->evaluateTriggerConditions($webhook, $submission);
dd($shouldTrigger);
```

---

### Webhook Timing Out

**Problem**: Webhooks consistently timeout

**Solutions:**
```php
// Increase timeout
$webhook->timeout_seconds = 60;
$webhook->save();

// Or configure globally
// config/slick-forms.php
'webhooks' => [
    'timeout' => 60,
],
```

---

### 401/403 Authentication Errors

**Check:**
1. Headers are correctly formatted
2. API tokens are valid and not expired
3. Token has required permissions

**Debug:**
```php
$log = FormWebhookLog::where('response_status', 401)->latest()->first();
dd($log->request_headers, $log->response_body);
```

---

### Payload Format Issues

**Problem**: Endpoint expects different format

**Solutions:**
```php
// Try different format
$webhook->format = 'form_data'; // Instead of 'json'
$webhook->save();

// Or adjust headers
$webhook->headers = [
    'Content-Type' => 'application/x-www-form-urlencoded',
];
$webhook->save();
```

---

## Events

### WebhookSending

Dispatched before webhook HTTP request is sent.

```php
use DigitalisStudios\SlickForms\Events\WebhookSending;

Event::listen(WebhookSending::class, function ($event) {
    $webhook = $event->webhook;
    $payload = $event->payload;

    Log::info('Sending webhook', [
        'webhook_id' => $webhook->id,
        'url' => $webhook->url,
    ]);
});
```

---

### WebhookSent

Dispatched after successful webhook delivery.

```php
use DigitalisStudios\SlickForms\Events\WebhookSent;

Event::listen(WebhookSent::class, function ($event) {
    $webhook = $event->webhook;
    $response = $event->response;

    Log::info('Webhook delivered', [
        'webhook_id' => $webhook->id,
        'status' => $response['status'] ?? null,
    ]);
});
```

---

### WebhookFailed

Dispatched when webhook delivery fails (after all retries).

```php
use DigitalisStudios\SlickForms\Events\WebhookFailed;

Event::listen(WebhookFailed::class, function ($event) {
    $webhook = $event->webhook;
    $error = $event->errorMessage;

    // Alert administrators
    Mail::to('admin@example.com')->send(
        new WebhookFailureAlert($webhook, $error)
    );
});
```

See [Events Documentation](EVENTS.md) for complete event reference.

---

## API Reference

### WebhookService

#### `sendWebhook()`

Send webhook for a submission (queued or synchronous).

```php
public function sendWebhook(
    FormWebhook $webhook,
    array $payload,
    ?CustomFormSubmission $submission = null
): void
```

---

#### `testWebhook()`

Test webhook configuration with sample data.

```php
public function testWebhook(FormWebhook $webhook): array
```

**Returns:**
```php
[
    'success' => true,
    'status' => 200,
    'response_body' => '{"success":true}',
    'duration' => 0.345,
    'error' => null,
]
```

---

#### `buildPayload()`

Build webhook payload from submission.

```php
public function buildPayload(CustomFormSubmission $submission): array
```

---

#### `retryFailedWebhook()`

Manually retry a failed webhook.

```php
public function retryFailedWebhook(FormWebhookLog $log): void
```

---

#### `evaluateTriggerConditions()`

Check if webhook should fire based on conditions.

```php
public function evaluateTriggerConditions(
    FormWebhook $webhook,
    CustomFormSubmission $submission
): bool
```

---

## Related Documentation

- [Events Reference](EVENTS.md) - Webhook events (WebhookSending, WebhookSent, WebhookFailed)
- [Jobs Reference](JOBS.md) - SendWebhook job and queue configuration
- [Conditional Logic](CONDITIONAL_LOGIC.md) - Trigger condition operators
- [Security](SECURITY.md) - Webhook security best practices

---

## Summary

Webhooks enable powerful real-time integrations:

- ✅ Configure webhooks with URL, headers, and format
- ✅ Automatic retry with exponential backoff
- ✅ Conditional triggering based on form values
- ✅ Comprehensive delivery logs
- ✅ Test mode for validation
- ✅ Queue support for reliability
- ✅ Multiple payload formats (JSON, Form Data, XML)
- ✅ Custom authentication headers

With webhooks, Slick Forms becomes a powerful data pipeline to any external system or service.
