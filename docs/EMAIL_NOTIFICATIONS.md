# Email Notifications

**Slick Forms v2.0+** - Complete guide for email notification configuration and templates

---

## Table of Contents

- [Overview](#overview)
- [Quick Start](#quick-start)
- [Email Template Types](#email-template-types)
- [Template Configuration](#template-configuration)
- [Variable Substitution](#variable-substitution)
- [Conditional Sending](#conditional-sending)
- [Recipients Configuration](#recipients-configuration)
- [SMTP Setup](#smtp-setup)
- [Queue Configuration](#queue-configuration)
- [PDF Attachments](#pdf-attachments)
- [Email Logs](#email-logs)
- [Template Examples](#template-examples)
- [Testing Emails](#testing-emails)
- [Troubleshooting](#troubleshooting)
- [Events](#events)
- [API Reference](#api-reference)

---

## Overview

Email notifications allow you to automatically send customized emails when forms are submitted. Slick Forms supports two types of notifications:

1. **Admin Notifications** - Sent to form administrators/staff
2. **User Confirmations** - Sent to form submitters

### Key Features

- ✅ Blade template syntax support
- ✅ Variable substitution (`{{field_name}}`)
- ✅ Conditional sending rules
- ✅ Multiple recipients (to, cc, bcc)
- ✅ Dynamic recipient emails from form fields
- ✅ PDF attachments
- ✅ Queue support for performance
- ✅ Comprehensive delivery logs
- ✅ Priority-based sending
- ✅ Rich HTML emails with custom styling

---

## Quick Start

### 1. Configure SMTP

```bash
# .env configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourapp.com
MAIL_FROM_NAME="${APP_NAME}"

# Slick Forms email settings
SLICK_FORMS_EMAIL_ENABLED=true
SLICK_FORMS_EMAIL_QUEUE=true
```

### 2. Create Email Template

```php
use DigitalisStudios\SlickForms\Models\FormEmailTemplate;
use DigitalisStudios\SlickForms\Models\CustomForm;

$form = CustomForm::find(1);

$template = FormEmailTemplate::create([
    'form_id' => $form->id,
    'type' => 'admin', // 'admin' or 'user_confirmation'
    'name' => 'New Contact Form Submission',
    'enabled' => true,
    'priority' => 10,
    'recipients' => ['admin@example.com', 'sales@example.com'],
    'subject' => 'New Contact from {{full_name}}',
    'body_template' => view('emails.contact-admin')->render(),
]);
```

### 3. Submit Form

Emails are automatically sent when forms are submitted:

```php
// Automatically handled by Slick Forms
// FormSubmitted event → SendEmailNotification job → Email sent
```

---

## Email Template Types

### Admin Notifications

Sent to form administrators when forms are submitted.

```php
$template = FormEmailTemplate::create([
    'form_id' => $form->id,
    'type' => 'admin',
    'name' => 'Admin Alert - Contact Form',
    'recipients' => ['admin@example.com'],
    'subject' => 'New Contact Form Submission',
    'body_template' => '<p>New submission from {{full_name}}</p>',
]);
```

**Use Cases**:
- Sales lead notifications
- Support ticket creation
- Application submissions
- Order notifications

---

### User Confirmations

Sent to form submitters as confirmation.

```php
$template = FormEmailTemplate::create([
    'form_id' => $form->id,
    'type' => 'user_confirmation',
    'name' => 'Thank You Email',
    'recipients' => ['field:email'], // Dynamic from form field
    'subject' => 'Thanks for contacting us, {{first_name}}!',
    'body_template' => '<p>We received your message and will respond within 24 hours.</p>',
]);
```

**Use Cases**:
- Thank you emails
- Confirmation receipts
- Next steps information
- Appointment confirmations

---

## Template Configuration

### Database Schema

Templates are stored in `slick_form_email_templates` table:

```php
Schema::create('slick_form_email_templates', function (Blueprint $table) {
    $table->id();
    $table->foreignId('form_id')->constrained('slick_forms')->cascadeOnDelete();
    $table->string('type'); // 'admin', 'user_confirmation'
    $table->string('name'); // Human-readable identifier
    $table->boolean('enabled')->default(true);
    $table->integer('priority')->default(10); // Higher = sent first
    $table->json('recipients'); // Array of email addresses or field references
    $table->string('subject');
    $table->longText('body_template'); // HTML with Blade syntax
    $table->json('conditional_rules')->nullable(); // When to send
    $table->boolean('attach_pdf')->default(false);
    $table->timestamps();
});
```

---

### Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `type` | string | required | 'admin' or 'user_confirmation' |
| `name` | string | required | Template identifier |
| `enabled` | boolean | true | Enable/disable template |
| `priority` | integer | 10 | Send order (higher first) |
| `recipients` | array | required | Email addresses or field references |
| `subject` | string | required | Email subject (supports variables) |
| `body_template` | string | required | HTML body (supports Blade) |
| `conditional_rules` | array | null | Conditions for sending |
| `attach_pdf` | boolean | false | Attach submission PDF |

---

### Priority-Based Sending

When multiple templates match, they're sent in priority order:

```php
// High-priority urgent notification
$urgentTemplate = FormEmailTemplate::create([
    'name' => 'Urgent - High Value Lead',
    'priority' => 100,
    'recipients' => ['ceo@example.com'],
    'conditional_rules' => [
        ['field' => 'budget', 'operator' => 'greater_than', 'value' => 100000],
    ],
]);

// Normal priority
$normalTemplate = FormEmailTemplate::create([
    'name' => 'Standard Lead Notification',
    'priority' => 10,
    'recipients' => ['sales@example.com'],
]);

// Low priority CC
$ccTemplate = FormEmailTemplate::create([
    'name' => 'CC to Marketing',
    'priority' => 1,
    'recipients' => ['marketing@example.com'],
]);
```

---

## Variable Substitution

Insert submission data into email content using `{{variable}}` syntax.

### Available Variables

```php
// Submission metadata
{{submission.id}}           // 12345
{{submission.created_at}}   // 2025-01-04 10:30:00
{{ip_address}}             // 192.168.1.1
{{form_name}}              // Contact Form

// Any form field by name
{{full_name}}              // John Doe
{{email}}                  // john@example.com
{{phone}}                  // (555) 123-4567
{{message}}                // User's message text
```

---

### Subject Line Variables

```php
$template->subject = 'New contact from {{full_name}} ({{email}})';

// Result: "New contact from John Doe (john@example.com)"
```

---

### Email Body Variables

```blade
<p>Hello team,</p>

<p>New submission from <strong>{{full_name}}</strong> ({{email}})</p>

<h3>Details:</h3>
<ul>
    <li><strong>Name:</strong> {{full_name}}</li>
    <li><strong>Email:</strong> {{email}}</li>
    <li><strong>Phone:</strong> {{phone}}</li>
    <li><strong>Company:</strong> {{company}}</li>
</ul>

<h3>Message:</h3>
<p>{{message}}</p>

<hr>
<p><small>Submission ID: {{submission.id}} | Submitted: {{submission.created_at}}</small></p>
```

---

### Blade Template Support

Use full Blade syntax in templates:

```blade
<p>Dear {{first_name}},</p>

<p>Thank you for your inquiry about <strong>{{service_type}}</strong>.</p>

@if($field_values['budget']->value > 10000)
    <p>We're excited to discuss this high-value project with you!</p>
@else
    <p>We'll review your request and respond within 48 hours.</p>
@endif

<h3>Your Information:</h3>
<ul>
    @foreach($field_values as $fieldName => $fieldValue)
        <li><strong>{{ $fieldValue->field->label }}:</strong> {{ $fieldValue->value }}</li>
    @endforeach
</ul>

<p>Best regards,<br>{{ config('app.name') }}</p>
```

---

### Available Template Variables

```php
// Variables passed to Blade templates:
$submission         // CustomFormSubmission instance
$form              // CustomForm instance
$field_values      // Collection of field values keyed by field name
$submitted_at      // Carbon timestamp
$ip_address        // Submitter's IP address
```

---

### Safe Variable Output

All variables are automatically escaped to prevent XSS:

```blade
{{-- Escaped automatically --}}
<p>{{user_input}}</p>

{{-- Raw output (use with caution) --}}
<p>{!! sanitized_html_content !!}</p>
```

---

## Conditional Sending

Send emails only when specific conditions are met.

### Configuration

```php
$template->conditional_rules = [
    [
        'field' => 'inquiry_type',
        'operator' => 'equals',
        'value' => 'sales',
    ],
    [
        'field' => 'budget',
        'operator' => 'greater_than',
        'value' => 5000,
    ],
];

$template->save();
```

This template only sends when:
- `inquiry_type` field equals "sales" **AND**
- `budget` field is greater than 5000

---

### Supported Operators

- `equals`, `not_equals`
- `contains`, `not_contains`
- `greater_than`, `less_than`
- `is_empty`, `is_not_empty`

---

### Example: Budget-Based Routing

```php
// High-value leads → Sales Director
$highValueTemplate = FormEmailTemplate::create([
    'type' => 'admin',
    'name' => 'High Value Lead - Sales Director',
    'recipients' => ['director@example.com'],
    'subject' => 'High Value Lead: {{company}} - ${{budget}}',
    'conditional_rules' => [
        ['field' => 'budget', 'operator' => 'greater_than', 'value' => 50000],
    ],
]);

// Medium leads → Sales Team
$mediumLeadTemplate = FormEmailTemplate::create([
    'type' => 'admin',
    'name' => 'Medium Lead - Sales Team',
    'recipients' => ['sales@example.com'],
    'subject' => 'New Lead: {{company}}',
    'conditional_rules' => [
        ['field' => 'budget', 'operator' => 'greater_than', 'value' => 5000],
        ['field' => 'budget', 'operator' => 'less_than', 'value' => 50000],
    ],
]);

// Small inquiries → Support
$supportTemplate = FormEmailTemplate::create([
    'type' => 'admin',
    'name' => 'General Inquiry - Support',
    'recipients' => ['support@example.com'],
    'subject' => 'General Inquiry from {{full_name}}',
    'conditional_rules' => [
        ['field' => 'budget', 'operator' => 'less_than', 'value' => 5000],
    ],
]);
```

---

## Recipients Configuration

### Static Recipients

Fixed email addresses:

```php
$template->recipients = [
    'admin@example.com',
    'manager@example.com',
];
```

---

### Dynamic Recipients (from Form Fields)

Pull recipient email from form submission:

```php
// Use email from the 'email' field
$template->recipients = ['field:email'];

// Use email from custom field name
$template->recipients = ['field:contact_email'];
```

---

### Mixed Recipients

Combine static and dynamic recipients:

```php
$template->recipients = [
    'admin@example.com',        // Static
    'field:email',              // Dynamic - submitter's email
    'field:supervisor_email',   // Dynamic - from supervisor field
];
```

---

### Multiple Recipient Types (To, CC, BCC)

Currently, all recipients are added to "To" field. For CC/BCC support:

```php
// Future feature - use advanced recipient configuration
$template->recipients_config = [
    'to' => ['admin@example.com'],
    'cc' => ['manager@example.com'],
    'bcc' => ['archive@example.com'],
    'reply_to' => 'field:email', // Reply to submitter
];
```

---

### Field Reference Validation

The system validates that field references exist:

```php
// Valid - 'email' field exists in form
$template->recipients = ['field:email'];

// Invalid - 'non_existent_field' doesn't exist
// No email sent, logged as error
$template->recipients = ['field:non_existent_field'];
```

---

## SMTP Setup

### Popular SMTP Providers

#### Gmail

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Your Name"
```

**Note**: Use [App Passwords](https://support.google.com/accounts/answer/185833) for Gmail.

---

#### SendGrid

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your App"
```

---

#### Mailgun

```bash
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.yourdomain.com
MAILGUN_SECRET=your-mailgun-api-key
MAILGUN_ENDPOINT=api.mailgun.net
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your App"
```

---

#### AWS SES

```bash
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your App"
```

---

#### Mailtrap (Testing)

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=test@example.com
MAIL_FROM_NAME="Test App"
```

---

### Slick Forms Email Configuration

```bash
# Enable email notifications
SLICK_FORMS_EMAIL_ENABLED=true

# Queue email sending (recommended)
SLICK_FORMS_EMAIL_QUEUE=true

# Override default from address (optional)
SLICK_FORMS_EMAIL_FROM=noreply@yourapp.com

# Override default from name (optional)
SLICK_FORMS_EMAIL_FROM_NAME="Your App Name"
```

---

## Queue Configuration

### Why Queue Emails?

- **Performance**: Don't delay form submission response
- **Reliability**: Retry failed sends automatically
- **Scalability**: Handle high email volumes

---

### Enable Queue

```bash
# .env
QUEUE_CONNECTION=redis
SLICK_FORMS_EMAIL_QUEUE=true
```

---

### Start Queue Worker

```bash
# Development
php artisan queue:work

# Production (with Supervisor)
php artisan queue:work --queue=emails --tries=3 --timeout=60
```

---

### Supervisor Configuration

```ini
[program:slick-forms-email-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work redis --queue=emails --sleep=3 --tries=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/email-worker.log
stopwaitsecs=3600
```

---

### Monitor Queue

```bash
# Check queue size
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

---

## PDF Attachments

Attach a formatted PDF of the submission to emails.

### Enable PDF Attachment

```php
$template->attach_pdf = true;
$template->save();
```

---

### PDF Content

The PDF includes:
- Form name and description
- Submission date and ID
- All field labels and values
- Professional formatting

---

### Custom PDF Template

Create custom PDF view in `resources/views/vendor/slick-forms/emails/submission-pdf.blade.php`:

```blade
<!DOCTYPE html>
<html>
<head>
    <title>{{ $form->name }} - Submission {{ $submission->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <h1>{{ $form->name }}</h1>
    <p><strong>Submission ID:</strong> {{ $submission->id }}</p>
    <p><strong>Submitted:</strong> {{ $submission->created_at->format('M d, Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($field_values as $value)
                <tr>
                    <td><strong>{{ $value->field->label }}</strong></td>
                    <td>{{ $value->value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
```

---

### PDF Filename

Automatic filename: `submission-{$submissionId}.pdf`

Example: `submission-12345.pdf`

---

## Email Logs

All email deliveries are logged to `slick_form_email_logs` table.

### Log Schema

```php
Schema::create('slick_form_email_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('submission_id')->constrained('slick_form_submissions')->cascadeOnDelete();
    $table->foreignId('template_id')->constrained('slick_form_email_templates')->cascadeOnDelete();
    $table->string('to'); // Recipient email
    $table->string('subject');
    $table->longText('body')->nullable();
    $table->string('status'); // sent, failed, queued
    $table->text('error_message')->nullable();
    $table->timestamp('sent_at')->nullable();
    $table->timestamps();
});
```

---

### Viewing Logs

```php
use DigitalisStudios\SlickForms\Models\FormEmailLog;

// Get recent deliveries
$logs = FormEmailLog::with('submission', 'template')
    ->latest()
    ->paginate(50);

foreach ($logs as $log) {
    echo "To: {$log->to}\n";
    echo "Subject: {$log->subject}\n";
    echo "Status: {$log->status}\n";
    echo "Sent: {$log->sent_at}\n";

    if ($log->error_message) {
        echo "Error: {$log->error_message}\n";
    }

    echo "\n";
}
```

---

### Filter by Status

```php
// Failed deliveries
$failed = FormEmailLog::where('status', 'failed')->get();

// Successful deliveries
$sent = FormEmailLog::where('status', 'sent')->get();

// Queued (pending)
$queued = FormEmailLog::where('status', 'queued')->get();
```

---

## Template Examples

### Example 1: Simple Admin Notification

```php
$template = FormEmailTemplate::create([
    'form_id' => $form->id,
    'type' => 'admin',
    'name' => 'Contact Form - Admin Alert',
    'recipients' => ['admin@example.com'],
    'subject' => 'New Contact: {{full_name}}',
    'body_template' => '
        <h2>New Contact Form Submission</h2>
        <p><strong>From:</strong> {{full_name}} ({{email}})</p>
        <p><strong>Phone:</strong> {{phone}}</p>
        <h3>Message:</h3>
        <p>{{message}}</p>
        <hr>
        <p><small>Submission ID: {{submission.id}}</small></p>
    ',
]);
```

---

### Example 2: User Confirmation with Blade

```php
$template = FormEmailTemplate::create([
    'form_id' => $form->id,
    'type' => 'user_confirmation',
    'name' => 'Thank You Email',
    'recipients' => ['field:email'],
    'subject' => 'Thanks for contacting us, {{first_name}}!',
    'body_template' => '
        <html>
        <body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background: #007bff; color: white; padding: 20px; text-align: center;">
                <h1>Thank You!</h1>
            </div>

            <div style="padding: 20px;">
                <p>Dear {{first_name}},</p>

                <p>Thank you for reaching out to us. We have received your message and will respond within 24 hours.</p>

                <div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0;">
                    <h3>Your Message:</h3>
                    <p>{{message}}</p>
                </div>

                <p>If you have any urgent questions, please call us at (555) 123-4567.</p>

                <p>Best regards,<br>
                <strong>The {{ config(\'app.name\') }} Team</strong></p>
            </div>

            <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666;">
                <p>This email was sent because you submitted a form on our website.</p>
                <p>Submission ID: {{submission.id}} | {{ config(\'app.url\') }}</p>
            </div>
        </body>
        </html>
    ',
]);
```

---

### Example 3: Order Confirmation

```php
$template = FormEmailTemplate::create([
    'form_id' => $form->id,
    'type' => 'user_confirmation',
    'name' => 'Order Confirmation',
    'recipients' => ['field:customer_email'],
    'subject' => 'Order Confirmation #{{order_number}}',
    'attach_pdf' => true,
    'body_template' => '
        <h1>Order Confirmation</h1>

        <p>Hi {{customer_name}},</p>

        <p>Thank you for your order! Your order #{{order_number}} has been received and is being processed.</p>

        <h3>Order Details:</h3>
        <ul>
            <li><strong>Order Number:</strong> {{order_number}}</li>
            <li><strong>Order Date:</strong> {{submission.created_at}}</li>
            <li><strong>Product:</strong> {{product_name}}</li>
            <li><strong>Quantity:</strong> {{quantity}}</li>
            <li><strong>Total:</strong> ${{total_amount}}</li>
        </ul>

        <h3>Shipping Address:</h3>
        <p>
            {{shipping_address}}<br>
            {{shipping_city}}, {{shipping_state}} {{shipping_zip}}
        </p>

        <p>We will send you another email when your order ships.</p>

        <p>Thank you for your business!</p>
    ',
]);
```

---

### Example 4: Event Registration

```php
$template = FormEmailTemplate::create([
    'form_id' => $form->id,
    'type' => 'user_confirmation',
    'name' => 'Event Registration Confirmation',
    'recipients' => ['field:email'],
    'subject' => 'You\'re registered for {{event_name}}!',
    'body_template' => '
        <h1>Event Registration Confirmed</h1>

        <p>Hi {{first_name}},</p>

        <p>You\'re all set for <strong>{{event_name}}</strong>!</p>

        <div style="background: #e9ecef; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <h3>Event Details:</h3>
            <p>
                <strong>Date:</strong> {{event_date}}<br>
                <strong>Time:</strong> {{event_time}}<br>
                <strong>Location:</strong> {{event_location}}<br>
                <strong>Ticket Type:</strong> {{ticket_type}}
            </p>
        </div>

        @if($field_values[\'dietary_restrictions\']->value)
            <p><strong>Dietary Restrictions Noted:</strong> {{dietary_restrictions}}</p>
        @endif

        <p>Please bring this confirmation email or show it on your mobile device at check-in.</p>

        <p>We look forward to seeing you there!</p>

        <p><small>Confirmation #{{submission.id}}</small></p>
    ',
]);
```

---

## Testing Emails

### Test SMTP Connection

```bash
php artisan tinker
```

```php
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')
        ->subject('Test Email from Slick Forms');
});

// Check for errors
echo "Email sent successfully!";
```

---

### Test Template Rendering

```php
use DigitalisStudios\SlickForms\Services\EmailNotificationService;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;

$service = app(EmailNotificationService::class);
$submission = CustomFormSubmission::find(1);
$template = FormEmailTemplate::find(1);

// Render template
$renderedBody = $service->renderEmailTemplate($template, $submission);

// Output to browser for preview
echo $renderedBody;
```

---

### Send Test Email

```php
use DigitalisStudios\SlickForms\Models\FormEmailTemplate;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Services\EmailNotificationService;

$template = FormEmailTemplate::find(1);
$submission = CustomFormSubmission::find(1);
$service = app(EmailNotificationService::class);

// Send email immediately (bypass queue)
config(['slick-forms.email.queue' => false]);

$service->sendEmail($submission, $template);

echo "Test email sent!";
```

---

## Troubleshooting

### Emails Not Sending

**Check:**
1. Email notifications enabled: `SLICK_FORMS_EMAIL_ENABLED=true`
2. SMTP configured correctly in `.env`
3. Template is `enabled = true`
4. Recipients are valid
5. Conditional rules are satisfied
6. Queue is running (if queued)

**Debug:**
```php
// Test SMTP
Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));

// Check template
$template = FormEmailTemplate::find(1);
dd($template->enabled, $template->recipients);

// Check logs
$logs = FormEmailLog::where('status', 'failed')->latest()->get();
dd($logs);
```

---

### Variables Not Substituting

**Problem**: Variables show as `{{field_name}}` instead of values

**Cause**: Field name doesn't match variable

**Solution:**
```php
// Check field names
$form->fields->pluck('name', 'id');

// Use correct field name
// If field name is "email_address" not "email":
{{email_address}}  // ✅ Correct
{{email}}          // ❌ Won't work
```

---

### Blade Syntax Errors

**Problem**: Email fails to render, shows error

**Cause**: Invalid Blade syntax

**Solution**:
```php
// Test template rendering
$service = app(EmailNotificationService::class);

try {
    $rendered = $service->renderEmailTemplate($template, $submission);
    echo "Template renders successfully";
} catch (\Exception $e) {
    echo "Blade error: " . $e->getMessage();
}
```

---

### SMTP Authentication Failed

**Problem**: `Swift_TransportException: Expected response code 250 but got code "535"`

**Solutions:**
```bash
# Check credentials
echo $MAIL_USERNAME
echo $MAIL_PASSWORD

# For Gmail, use App Passwords
# https://support.google.com/accounts/answer/185833

# For SendGrid, username must be "apikey"
MAIL_USERNAME=apikey

# Check SMTP port and encryption
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

---

### Queue Not Processing

**Problem**: Emails stuck in queue

**Solution:**
```bash
# Start queue worker
php artisan queue:work

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear queue (careful!)
php artisan queue:flush
```

---

## Events

### EmailNotificationSending

Dispatched before email is sent.

```php
use DigitalisStudios\SlickForms\Events\EmailNotificationSending;

Event::listen(EmailNotificationSending::class, function ($event) {
    $submission = $event->submission;
    $template = $event->template;

    Log::info('Sending email notification', [
        'template' => $template->name,
        'submission_id' => $submission->id,
    ]);
});
```

---

### EmailNotificationSent

Dispatched after successful email delivery.

```php
use DigitalisStudios\SlickForms\Events\EmailNotificationSent;

Event::listen(EmailNotificationSent::class, function ($event) {
    Log::info('Email sent successfully', [
        'template' => $event->template->name,
    ]);
});
```

---

### EmailNotificationFailed

Dispatched when email delivery fails.

```php
use DigitalisStudios\SlickForms\Events\EmailNotificationFailed;

Event::listen(EmailNotificationFailed::class, function ($event) {
    // Alert administrators
    Mail::to('admin@example.com')->send(
        new EmailFailureAlert($event->submission, $event->errorMessage)
    );
});
```

See [Events Documentation](EVENTS.md) for complete event reference.

---

## API Reference

### EmailNotificationService

#### `sendAdminNotification()`

Send admin notification emails for a submission.

```php
public function sendAdminNotification(CustomFormSubmission $submission): void
```

---

#### `sendUserConfirmation()`

Send user confirmation email for a submission.

```php
public function sendUserConfirmation(CustomFormSubmission $submission): void
```

---

#### `renderEmailTemplate()`

Render email template with submission data.

```php
public function renderEmailTemplate(
    FormEmailTemplate $template,
    CustomFormSubmission $submission
): string
```

---

#### `evaluateConditionalRules()`

Check if email should be sent based on rules.

```php
public function evaluateConditionalRules(
    FormEmailTemplate $template,
    CustomFormSubmission $submission
): bool
```

---

#### `attachSubmissionPdf()`

Generate PDF attachment of submission.

```php
public function attachSubmissionPdf(CustomFormSubmission $submission): string
```

**Returns:** Path to generated PDF file

---

## Related Documentation

- [Events Reference](EVENTS.md) - Email events
- [Jobs Reference](JOBS.md) - SendEmailNotification job
- [Success Screens](SUCCESS_SCREENS.md) - Similar variable substitution
- [Conditional Logic](CONDITIONAL_LOGIC.md) - Condition operators

---

## Summary

Email notifications keep everyone informed:

- ✅ Admin notifications for new submissions
- ✅ User confirmations for submitters
- ✅ Variable substitution with `{{field_name}}`
- ✅ Full Blade template syntax support
- ✅ Conditional sending based on form values
- ✅ Dynamic recipients from form fields
- ✅ PDF attachments
- ✅ Queue support for performance
- ✅ Comprehensive delivery logs

Professional, branded email notifications enhance user experience and streamline workflows.
