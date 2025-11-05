# Events Reference

## Overview

Slick Forms dispatches **15 events** throughout the form lifecycle, allowing you to hook into critical moments and add custom logic. These events enable seamless integration with your application's notification systems, logging, analytics, and business processes.

All events use Laravel's event system and can be listened to using `Event::listen()`, event subscribers, or by registering listeners in your `EventServiceProvider`.

## Table of Contents

- [Email Notification Events](#email-notification-events)
- [Webhook Events](#webhook-events)
- [Form Access & Security Events](#form-access--security-events)
- [Model Binding Events](#model-binding-events)
- [Dynamic Options Events](#dynamic-options-events)
- [Form Versioning Events](#form-versioning-events)
- [URL & Pre-fill Events](#url--pre-fill-events)
- [Event Registration](#event-registration)
- [Best Practices](#best-practices)

---

## Email Notification Events

### EmailNotificationSending

**Class**: `DigitalisStudios\SlickForms\Events\EmailNotificationSending`

**Dispatched**: Before an email notification is sent for a form submission

**Payload**:
- `$event->submission` - `CustomFormSubmission` instance

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\EmailNotificationSending;
use Illuminate\Support\Facades\Event;

Event::listen(EmailNotificationSending::class, function ($event) {
    $submission = $event->submission;

    // Log email sending attempt
    logger()->info("Sending email notification for form submission", [
        'form_id' => $submission->form_id,
        'submission_id' => $submission->id,
    ]);

    // Add custom data to submission before email is sent
    // (useful for last-minute calculations or enrichment)
});
```

**Common Use Cases**:
- Log email sending attempts
- Enrich submission data before sending
- Trigger pre-email validations
- Update external systems before notification
- Conditionally prevent email sending (return false from listener)

---

### EmailNotificationSent

**Class**: `DigitalisStudios\SlickForms\Events\EmailNotificationSent`

**Dispatched**: After an email notification is successfully sent

**Payload**:
- `$event->submission` - `CustomFormSubmission` instance

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\EmailNotificationSent;
use Illuminate\Support\Facades\Event;

Event::listen(EmailNotificationSent::class, function ($event) {
    $submission = $event->submission;

    // Update delivery tracking
    Cache::increment("emails_sent_today");

    // Notify external monitoring system
    Http::post('https://monitoring.example.com/email-sent', [
        'form_id' => $submission->form_id,
        'timestamp' => now(),
    ]);
});
```

**Common Use Cases**:
- Track email delivery metrics
- Update CRM systems with notification status
- Trigger follow-up workflows
- Log successful deliveries to analytics
- Send confirmation to administrators

---

### EmailNotificationFailed

**Class**: `DigitalisStudios\SlickForms\Events\EmailNotificationFailed`

**Dispatched**: When an email notification fails to send

**Payload**:
- `$event->submission` - `CustomFormSubmission` instance
- `$event->error` - Error message (string)

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\EmailNotificationFailed;
use Illuminate\Support\Facades\Event;

Event::listen(EmailNotificationFailed::class, function ($event) {
    $submission = $event->submission;
    $error = $event->error;

    // Alert administrators via Slack
    Notification::route('slack', config('services.slack.webhook'))
        ->notify(new EmailFailedNotification($submission, $error));

    // Log detailed error for debugging
    logger()->error("Email notification failed", [
        'submission_id' => $submission->id,
        'form_id' => $submission->form_id,
        'error' => $error,
        'timestamp' => now(),
    ]);
});
```

**Common Use Cases**:
- Alert administrators of email failures
- Log errors for debugging
- Trigger fallback notification methods (SMS, Slack, etc.)
- Update monitoring dashboards
- Retry via alternative email provider

---

## Webhook Events

### WebhookSending

**Class**: `DigitalisStudios\SlickForms\Events\WebhookSending`

**Dispatched**: Before a webhook payload is sent to an external endpoint

**Payload**:
- `$event->webhook` - `FormWebhook` instance
- `$event->payload` - Webhook payload data (array)

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\WebhookSending;
use Illuminate\Support\Facades\Event;

Event::listen(WebhookSending::class, function ($event) {
    $webhook = $event->webhook;
    $payload = $event->payload;

    // Log webhook attempt with payload details
    logger()->info("Sending webhook", [
        'webhook_id' => $webhook->id,
        'url' => $webhook->url,
        'payload_size' => strlen(json_encode($payload)),
    ]);

    // Add custom headers or modify payload before sending
    // Note: Modify payload by reference if needed
});
```

**Common Use Cases**:
- Log webhook delivery attempts
- Add custom authentication tokens
- Validate payload before sending
- Enrich payload with additional data
- Track webhook usage metrics

---

### WebhookSent

**Class**: `DigitalisStudios\SlickForms\Events\WebhookSent`

**Dispatched**: After a webhook is successfully delivered

**Payload**:
- `$event->webhook` - `FormWebhook` instance
- `$event->response` - HTTP response data (array)

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\WebhookSent;
use Illuminate\Support\Facades\Event;

Event::listen(WebhookSent::class, function ($event) {
    $webhook = $event->webhook;
    $response = $event->response;

    // Track successful deliveries
    Cache::increment("webhooks_delivered_today");

    // Log response for debugging
    logger()->info("Webhook delivered successfully", [
        'webhook_id' => $webhook->id,
        'status_code' => $response['status_code'] ?? null,
        'response_time' => $response['response_time'] ?? null,
    ]);
});
```

**Common Use Cases**:
- Track delivery metrics
- Log successful webhook deliveries
- Update integration status dashboards
- Trigger dependent workflows
- Monitor response times and performance

---

### WebhookFailed

**Class**: `DigitalisStudios\SlickForms\Events\WebhookFailed`

**Dispatched**: When a webhook fails to deliver (after all retry attempts)

**Payload**:
- `$event->webhook` - `FormWebhook` instance
- `$event->error` - Error message (string)

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\WebhookFailed;
use Illuminate\Support\Facades\Event;

Event::listen(WebhookFailed::class, function ($event) {
    $webhook = $event->webhook;
    $error = $event->error;

    // Alert team via Slack or email
    Notification::route('slack', config('services.slack.webhook'))
        ->notify(new WebhookFailedNotification($webhook, $error));

    // Disable webhook if repeated failures
    if ($webhook->consecutive_failures >= 5) {
        $webhook->update(['is_active' => false]);
        logger()->warning("Webhook disabled due to repeated failures", [
            'webhook_id' => $webhook->id,
        ]);
    }
});
```

**Common Use Cases**:
- Alert administrators of webhook failures
- Automatically disable failing webhooks
- Log errors for debugging
- Trigger alternative integration methods
- Monitor integration health

---

## Form Access & Security Events

### FormAccessDenied

**Class**: `DigitalisStudios\SlickForms\Events\FormAccessDenied`

**Dispatched**: When access to a form is denied (expired, inactive, or permissions issue)

**Payload**:
- `$event->form` - `CustomForm` instance
- `$event->reason` - Denial reason (string)

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\FormAccessDenied;
use Illuminate\Support\Facades\Event;

Event::listen(FormAccessDenied::class, function ($event) {
    $form = $event->form;
    $reason = $event->reason;

    // Log access denial attempts
    logger()->warning("Form access denied", [
        'form_id' => $form->id,
        'form_name' => $form->name,
        'reason' => $reason,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);

    // Track suspicious activity
    if ($reason === 'expired') {
        Cache::increment("expired_form_access_attempts:{$form->id}");
    }
});
```

**Common Use Cases**:
- Log unauthorized access attempts
- Track security events for auditing
- Alert administrators of suspicious activity
- Monitor form expiration issues
- Analyze access patterns

---

### SpamDetected

**Class**: `DigitalisStudios\SlickForms\Events\SpamDetected`

**Dispatched**: When spam is detected on a form submission (honeypot, rate limit, or CAPTCHA failure)

**Payload**:
- `$event->form` - `CustomForm` instance
- `$event->method` - Detection method: 'honeypot', 'rate_limit', or 'captcha' (string)
- `$event->ipAddress` - IP address of spam attempt (string)

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\SpamDetected;
use Illuminate\Support\Facades\Event;

Event::listen(SpamDetected::class, function ($event) {
    $form = $event->form;
    $method = $event->method;
    $ipAddress = $event->ipAddress;

    // Block IP after repeated spam attempts
    $attempts = Cache::increment("spam_attempts:{$ipAddress}", 1);

    if ($attempts >= 5) {
        // Add to blacklist
        DB::table('ip_blacklist')->insert([
            'ip_address' => $ipAddress,
            'reason' => "Multiple spam attempts on form {$form->id}",
            'created_at' => now(),
        ]);

        logger()->warning("IP blocked for spam", [
            'ip_address' => $ipAddress,
            'form_id' => $form->id,
        ]);
    }

    // Track spam by detection method
    Cache::increment("spam_detected_{$method}_today");
});
```

**Common Use Cases**:
- Block IP addresses after repeated spam
- Track spam patterns and methods
- Alert security team of attack attempts
- Update firewall rules automatically
- Analyze spam prevention effectiveness

---

## Model Binding Events

### ModelBound

**Class**: `DigitalisStudios\SlickForms\Events\ModelBound`

**Dispatched**: When a form is bound to an Eloquent model (for pre-filling form data)

**Payload**:
- `$event->form` - `CustomForm` instance
- `$event->model` - Eloquent `Model` instance or null

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\ModelBound;
use Illuminate\Support\Facades\Event;

Event::listen(ModelBound::class, function ($event) {
    $form = $event->form;
    $model = $event->model;

    // Log model binding for audit trail
    if ($model) {
        logger()->info("Model bound to form", [
            'form_id' => $form->id,
            'model_class' => get_class($model),
            'model_id' => $model->id ?? null,
        ]);

        // Track which models are being edited via forms
        Cache::increment("model_edits:" . get_class($model));
    }
});
```

**Common Use Cases**:
- Audit trail of model editing
- Track which models are frequently edited
- Add custom authorization checks
- Pre-process model data before binding
- Log model access for compliance

---

### ModelSaved

**Class**: `DigitalisStudios\SlickForms\Events\ModelSaved`

**Dispatched**: When a bound model is saved from form submission data

**Payload**:
- `$event->form` - `CustomForm` instance
- `$event->model` - Eloquent `Model` instance (saved model)

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\ModelSaved;
use Illuminate\Support\Facades\Event;

Event::listen(ModelSaved::class, function ($event) {
    $form = $event->form;
    $model = $event->model;

    // Trigger dependent updates
    if ($model instanceof \App\Models\User) {
        // Clear user-related caches
        Cache::forget("user_profile:{$model->id}");

        // Send profile update notification
        $model->notify(new ProfileUpdatedNotification());
    }

    // Log model saves
    logger()->info("Model saved via form", [
        'form_id' => $form->id,
        'model_class' => get_class($model),
        'model_id' => $model->id,
        'changes' => $model->getChanges(),
    ]);
});
```

**Common Use Cases**:
- Clear related caches after model updates
- Send notifications to model owners
- Trigger dependent workflow updates
- Log model changes for auditing
- Update search indexes

---

## Dynamic Options Events

### DynamicOptionsLoaded

**Class**: `DigitalisStudios\SlickForms\Events\DynamicOptionsLoaded`

**Dispatched**: When dynamic options are successfully loaded for a field (from API or database)

**Payload**:
- `$event->field` - `CustomFormField` instance
- `$event->options` - Loaded options data (array)

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\DynamicOptionsLoaded;
use Illuminate\Support\Facades\Event;

Event::listen(DynamicOptionsLoaded::class, function ($event) {
    $field = $event->field;
    $options = $event->options;

    // Log successful option loads
    logger()->info("Dynamic options loaded", [
        'field_id' => $field->id,
        'field_name' => $field->name,
        'option_count' => count($options),
    ]);

    // Track API usage for monitoring
    Cache::increment("dynamic_options_loaded_today");
});
```

**Common Use Cases**:
- Monitor dynamic option load performance
- Track API usage and costs
- Cache frequently loaded options
- Log option availability for debugging
- Analyze user interaction patterns

---

### DynamicOptionsFailed

**Class**: `DigitalisStudios\SlickForms\Events\DynamicOptionsFailed`

**Dispatched**: When dynamic options fail to load (API error, database error, etc.)

**Payload**:
- `$event->field` - `CustomFormField` instance
- `$event->exception` - `Exception` instance containing error details

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\DynamicOptionsFailed;
use Illuminate\Support\Facades\Event;

Event::listen(DynamicOptionsFailed::class, function ($event) {
    $field = $event->field;
    $exception = $event->exception;

    // Alert administrators of option loading failures
    logger()->error("Dynamic options failed to load", [
        'field_id' => $field->id,
        'field_name' => $field->name,
        'error' => $exception->getMessage(),
        'trace' => $exception->getTraceAsString(),
    ]);

    // Fallback to static options if available
    if ($field->hasStaticFallback()) {
        $field->switchToStaticOptions();
    }

    // Alert via Slack for critical fields
    if ($field->is_required) {
        Notification::route('slack', config('services.slack.webhook'))
            ->notify(new DynamicOptionsFailedNotification($field, $exception));
    }
});
```

**Common Use Cases**:
- Alert administrators of API failures
- Implement fallback options
- Track integration reliability
- Debug option loading issues
- Monitor external service health

---

## Form Versioning Events

### FormVersionPublished

**Class**: `DigitalisStudios\SlickForms\Events\FormVersionPublished`

**Dispatched**: When a new version of a form is published (form structure changed)

**Payload**:
- `$event->form` - `CustomForm` instance
- `$event->version` - `FormVersion` instance (new version)

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\FormVersionPublished;
use Illuminate\Support\Facades\Event;

Event::listen(FormVersionPublished::class, function ($event) {
    $form = $event->form;
    $version = $event->version;

    // Notify team members of form changes
    $form->team->notify(new FormUpdatedNotification($form, $version));

    // Log version for audit trail
    logger()->info("Form version published", [
        'form_id' => $form->id,
        'version_number' => $version->version_number,
        'changes' => $version->change_summary,
    ]);

    // Clear cached form structures
    Cache::forget("form_structure:{$form->id}");
});
```

**Common Use Cases**:
- Notify team members of form updates
- Track form evolution over time
- Audit form changes for compliance
- Clear form caches after updates
- Trigger form testing workflows

---

## URL & Pre-fill Events

### SignedUrlGenerated

**Class**: `DigitalisStudios\SlickForms\Events\SignedUrlGenerated`

**Dispatched**: When a signed URL is generated for a form (time-limited access)

**Payload**:
- `$event->form` - `CustomForm` instance
- `$event->url` - Generated signed URL (string)

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\SignedUrlGenerated;
use Illuminate\Support\Facades\Event;

Event::listen(SignedUrlGenerated::class, function ($event) {
    $form = $event->form;
    $url = $event->url;

    // Log signed URL generation for security audit
    logger()->info("Signed URL generated", [
        'form_id' => $form->id,
        'url' => $url,
        'generated_by' => auth()->id(),
        'ip_address' => request()->ip(),
    ]);

    // Track URL generation for analytics
    Cache::increment("signed_urls_generated_today");
});
```

**Common Use Cases**:
- Audit trail of URL generation
- Track URL usage patterns
- Monitor security-sensitive forms
- Implement URL generation limits
- Send URLs via secure channels automatically

---

### PrefillDataDecrypted

**Class**: `DigitalisStudios\SlickForms\Events\PrefillDataDecrypted`

**Dispatched**: When pre-fill data is successfully decrypted from a URL

**Payload**:
- `$event->form` - `CustomForm` instance
- `$event->data` - Decrypted pre-fill data (array)

**Usage**:
```php
use DigitalisStudios\SlickForms\Events\PrefillDataDecrypted;
use Illuminate\Support\Facades\Event;

Event::listen(PrefillDataDecrypted::class, function ($event) {
    $form = $event->form;
    $data = $event->data;

    // Log pre-fill usage
    logger()->info("Pre-fill data decrypted", [
        'form_id' => $form->id,
        'fields_prefilled' => count($data),
        'ip_address' => request()->ip(),
    ]);

    // Track pre-fill feature usage
    Cache::increment("prefill_urls_used_today");

    // Audit sensitive data access
    if ($form->contains_sensitive_data) {
        AuditLog::create([
            'form_id' => $form->id,
            'action' => 'prefill_data_accessed',
            'ip_address' => request()->ip(),
        ]);
    }
});
```

**Common Use Cases**:
- Audit pre-fill data access
- Track feature usage analytics
- Monitor sensitive data handling
- Implement access logging for compliance
- Detect potential data leakage

---

## Event Registration

### Using EventServiceProvider

The recommended way to register event listeners is in your `EventServiceProvider`:

```php
// app/Providers/EventServiceProvider.php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use DigitalisStudios\SlickForms\Events\EmailNotificationSent;
use DigitalisStudios\SlickForms\Events\WebhookFailed;
use DigitalisStudios\SlickForms\Events\SpamDetected;
use App\Listeners\LogEmailNotification;
use App\Listeners\AlertWebhookFailure;
use App\Listeners\BlockSpamIP;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        EmailNotificationSent::class => [
            LogEmailNotification::class,
        ],
        WebhookFailed::class => [
            AlertWebhookFailure::class,
        ],
        SpamDetected::class => [
            BlockSpamIP::class,
        ],
    ];
}
```

### Using Event::listen()

You can also register listeners dynamically in a service provider's `boot()` method:

```php
// app/Providers/AppServiceProvider.php

use Illuminate\Support\Facades\Event;
use DigitalisStudios\SlickForms\Events\FormVersionPublished;

public function boot()
{
    Event::listen(FormVersionPublished::class, function ($event) {
        // Handle event inline
        logger()->info("Form version published: {$event->form->name}");
    });
}
```

### Creating Event Listeners

Generate a listener class:

```bash
php artisan make:listener LogEmailNotification --event=EmailNotificationSent
```

Example listener:

```php
// app/Listeners/LogEmailNotification.php

namespace App\Listeners;

use DigitalisStudios\SlickForms\Events\EmailNotificationSent;

class LogEmailNotification
{
    public function handle(EmailNotificationSent $event)
    {
        logger()->info("Email notification sent", [
            'submission_id' => $event->submission->id,
            'form_id' => $event->submission->form_id,
        ]);
    }
}
```

### Using Event Subscribers

For listening to multiple events in one class:

```php
// app/Listeners/SlickFormsEventSubscriber.php

namespace App\Listeners;

use Illuminate\Events\Dispatcher;
use DigitalisStudios\SlickForms\Events\EmailNotificationSent;
use DigitalisStudios\SlickForms\Events\WebhookSent;
use DigitalisStudios\SlickForms\Events\SpamDetected;

class SlickFormsEventSubscriber
{
    public function handleEmailSent(EmailNotificationSent $event)
    {
        // Handle email sent
    }

    public function handleWebhookSent(WebhookSent $event)
    {
        // Handle webhook sent
    }

    public function handleSpamDetected(SpamDetected $event)
    {
        // Handle spam detected
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            EmailNotificationSent::class,
            [SlickFormsEventSubscriber::class, 'handleEmailSent']
        );

        $events->listen(
            WebhookSent::class,
            [SlickFormsEventSubscriber::class, 'handleWebhookSent']
        );

        $events->listen(
            SpamDetected::class,
            [SlickFormsEventSubscriber::class, 'handleSpamDetected']
        );
    }
}
```

Register the subscriber in `EventServiceProvider`:

```php
protected $subscribe = [
    \App\Listeners\SlickFormsEventSubscriber::class,
];
```

---

## Best Practices

### 1. Use Queued Listeners for Heavy Operations

If your event listener performs time-consuming operations (API calls, file processing, etc.), implement `ShouldQueue`:

```php
namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use DigitalisStudios\SlickForms\Events\WebhookFailed;

class AlertWebhookFailure implements ShouldQueue
{
    public function handle(WebhookFailed $event)
    {
        // Send Slack notification (queued)
        Notification::route('slack', config('services.slack.webhook'))
            ->notify(new WebhookFailedNotification($event->webhook));
    }
}
```

### 2. Avoid Modifying Event Data

Events should be treated as read-only notifications. If you need to modify data, do so before the event is dispatched or use a different pattern.

### 3. Handle Exceptions Gracefully

Event listeners should not throw exceptions that break the main application flow:

```php
Event::listen(EmailNotificationSent::class, function ($event) {
    try {
        // Your logic here
        Http::post('https://external-api.com/track', [
            'submission_id' => $event->submission->id,
        ]);
    } catch (\Exception $e) {
        // Log error but don't throw
        logger()->error("Failed to track email notification", [
            'error' => $e->getMessage(),
        ]);
    }
});
```

### 4. Use Descriptive Listener Names

Choose listener class names that clearly describe their purpose:

- ✅ Good: `LogEmailNotification`, `AlertWebhookFailure`, `BlockSpamIP`
- ❌ Bad: `Handler`, `EventListener`, `ProcessEvent`

### 5. Group Related Listeners

Use event subscribers to group related event handlers for better organization.

### 6. Monitor Listener Performance

Track listener execution time to ensure they don't slow down your application:

```php
Event::listen(EmailNotificationSent::class, function ($event) {
    $start = microtime(true);

    // Your logic

    $duration = microtime(true) - $start;

    if ($duration > 1.0) {
        logger()->warning("Slow event listener detected", [
            'event' => 'EmailNotificationSent',
            'duration' => $duration,
        ]);
    }
});
```

### 7. Document Custom Listeners

Add docblocks to your listener classes explaining what they do and why:

```php
/**
 * Logs all email notifications sent via Slick Forms
 * for compliance and auditing purposes.
 *
 * Data is stored in the audit_log table for 7 years
 * per company retention policy.
 */
class LogEmailNotification
{
    // ...
}
```

---

## Related Documentation

- [Jobs Reference](JOBS.md) - Queue jobs used by Slick Forms
- [Webhooks Guide](WEBHOOKS.md) - Configure webhook integrations
- [Email Notifications Guide](EMAIL_NOTIFICATIONS.md) - Set up email notifications
- [Spam Protection Guide](SPAM_PROTECTION.md) - Configure spam prevention
- [Form Versioning Guide](FORM_VERSIONING.md) - Manage form versions

---

## Support

For questions or issues related to events:

1. Check the [full documentation](../README.md)
2. Review [examples](EXAMPLES.md) for practical implementations
3. Report bugs on [GitHub Issues](https://github.com/your-org/slick-forms/issues)

---

**Last Updated**: January 2025
**Package Version**: v2.1.0
