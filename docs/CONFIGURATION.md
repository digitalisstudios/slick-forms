# Configuration Reference

**Slick Forms v2.0+** - Complete configuration guide for all package settings including V2 features.

## Overview

Slick Forms uses a single configuration file located at `config/slick-forms.php` with comprehensive settings for routes, field types, layout elements, uploads, email notifications, spam protection, webhooks, and more.

**Configuration Sections**:
1. [Routes & Middleware](#routes--middleware)
2. [Field Types](#field-types)
3. [Layout Elements](#layout-elements)
4. [File Uploads](#file-uploads)
5. [Email Notifications](#email-notifications-v2)
6. [Spam Protection](#spam-protection-v2)
7. [Webhooks](#webhooks-v2)
8. [URL Obfuscation](#url-obfuscation-v2)
9. [Dynamic Options](#dynamic-options-v2)
10. [Default Form Settings](#default-form-settings)
11. [Environment Variables](#environment-variables-reference)

---

## Publishing Configuration

To customize any configuration, publish the config file:

```bash
php artisan vendor:publish --tag=slick-forms-config
```

This creates `config/slick-forms.php` in your application where you can override any settings.

---

## Routes & Middleware

### Route Loading

Control whether package routes are automatically loaded:

```php
'load_routes' => env('SLICK_FORMS_LOAD_ROUTES', true),
```

Set to `false` if you want to manually define routes or use only Livewire components directly.

**.env**:
```bash
SLICK_FORMS_LOAD_ROUTES=true
```

### Route Configuration

Slick Forms uses a hybrid approach with reusable segments that compose into route URIs:

```php
'routes' => [
    'prefix' => 'slick-forms',           // All routes prefixed with /slick-forms
    'name_prefix' => 'slick-forms.',     // All route names prefixed with slick-forms.

    'middleware' => [
        'admin' => ['web', 'auth'],      // Protected admin routes
        'public' => ['web'],             // Public form display routes
    ],

    'segments' => [
        'builder' => 'builder',          // Change to 'editor' to use /slick-forms/editor
        'manage' => 'manage',            // Change to 'admin' to use /slick-forms/admin
        'submissions' => 'submissions',
        'analytics' => 'analytics',
        // ... more segments
    ],
],
```

### Customizing Routes

#### Change All Management Routes

Change the `manage` segment to `admin`:

```php
'segments' => [
    'manage' => 'admin',  // /slick-forms/admin instead of /slick-forms/manage
],
```

**Result**:
- `/slick-forms/admin` (form list)
- `/slick-forms/admin/create` (create form)
- `/slick-forms/admin/{form}/edit` (edit form)

#### Custom Middleware

Add role-based protection:

```php
'middleware' => [
    'admin' => ['web', 'auth', 'role:admin'],
    'public' => ['web'],
],
```

#### Override Individual Routes

Use array syntax to override specific routes:

```php
'builder' => [
    'middleware' => 'admin',
    'show' => [
        'uri' => 'custom/path/{form}',
        'middleware' => ['web', 'auth', 'special'],
    ],
],
```

### Complete Route Reference

| Route Name | Default URI | Middleware | Description |
|------------|-------------|------------|-------------|
| `slick-forms.builder.show` | `/builder/{form}` | `admin` | Form builder interface |
| `slick-forms.form.show` | `/form/{hash}` | `public` | Public form display (hashid) |
| `slick-forms.form.show_prefilled` | `/form/{hash}/prefill/{data}` | `public` | Pre-filled form with encrypted data |
| `slick-forms.submissions.show` | `/submissions/{form}` | `admin` | Submission viewer |
| `slick-forms.submissions.export_csv` | `/submissions/{form}/export/csv` | `admin` | Export submissions to CSV |
| `slick-forms.submissions.export_excel` | `/submissions/{form}/export/excel` | `admin` | Export submissions to Excel |
| `slick-forms.submissions.export_pdf` | `/submissions/{form}/export/pdf` | `admin` | Export submissions to PDF |
| `slick-forms.analytics.show` | `/analytics/{form}` | `admin` | Analytics dashboard |
| `slick-forms.manage.index` | `/manage` | `admin` | Form list |
| `slick-forms.manage.create` | `/manage/create` | `admin` | Create form |
| `slick-forms.manage.store` | `/manage` (POST) | `admin` | Store new form |
| `slick-forms.manage.edit` | `/manage/{form}/edit` | `admin` | Edit form settings |
| `slick-forms.manage.update` | `/manage/{form}` (PUT) | `admin` | Update form settings |
| `slick-forms.manage.destroy` | `/manage/{form}` (DELETE) | `admin` | Delete form |
| `slick-forms.forms.duplicate` | `/forms/{form}/duplicate` (POST) | `admin` | Duplicate form |
| `slick-forms.forms.toggle_active` | `/forms/{form}/toggle-active` (POST) | `admin` | Toggle form active status |
| `slick-forms.templates.use` | `/templates/{template}/use` (POST) | `admin` | Create form from template |
| `slick-forms.templates.save_as_template` | `/forms/{form}/save-as-template` (POST) | `admin` | Save form as template |

---

## Field Types

### Registering Field Types

All 32 field types are pre-registered. To add custom field types:

```php
'field_types' => [
    // Built-in types...
    'text' => TextField::class,
    'email' => EmailField::class,

    // Your custom field type
    'custom' => \App\CustomFormFields\CustomField::class,
],
```

### Field Type Categories

**Basic Input Fields** (8):
- `text` - Single-line text input
- `textarea` - Multi-line text input
- `email` - Email validation
- `number` - Numeric input
- `password` - Password input
- `phone` - Phone number with formatting
- `url` - URL validation
- `hidden` - Hidden field

**Selection Fields** (4):
- `select` - Dropdown select
- `radio` - Radio buttons
- `checkbox` - Checkboxes
- `switch` - Toggle switch

**Date/Time Fields** (3):
- `date` - Date picker
- `time` - Time picker
- `date_range` - Date range picker

**File Fields** (4):
- `file` - File upload
- `image` - Image upload with preview
- `video` - Video upload
- `pdf_embed` - PDF embed viewer (V2)

**Advanced Fields** (5):
- `star_rating` - Star rating (1-5 stars)
- `slider` - Slider input
- `range` - Range slider (min/max)
- `color_picker` - Color picker
- `tags` - Tag input

**Special Fields** (4):
- `calculation` - Formula-based calculated field
- `repeater` - Repeating field groups
- `signature` - Signature pad (V2)
- `location` - Location picker with map (V2)
- `rating_matrix` - Matrix rating table (V2)

**Display Fields** (3):
- `header` - Text header
- `paragraph` - Paragraph text
- `code` - Code block with syntax highlighting

### Disabling Field Types

Remove unwanted field types from the builder palette:

```php
'field_types' => [
    // Comment out or remove field types you don't want
    // 'video' => VideoField::class,
    // 'signature' => SignaturePadField::class,
],
```

---

## Layout Elements

### Registering Layout Elements

All layout elements are pre-registered:

```php
'layout_element_types' => [
    'container' => ContainerType::class,
    'row' => RowType::class,
    'column' => ColumnType::class,
    'card' => CardType::class,
    'tabs' => TabsType::class,
    'tab' => TabElement::class,
    'accordion' => AccordionType::class,
    'accordion_item' => AccordionItemElement::class,
    'carousel' => CarouselType::class,           // V2
    'carousel_slide' => CarouselSlideElement::class,  // V2
    'table' => TableType::class,                  // V2
    'table_header' => TableSectionElement::class, // V2
    'table_body' => TableSectionElement::class,   // V2
    'table_footer' => TableSectionElement::class, // V2
    'table_row' => TableRowElement::class,        // V2
    'table_cell' => TableCellElement::class,      // V2
],
```

### Layout Element Categories

**Structural Elements**:
- `container` - Top-level wrapper (fluid or fixed-width)
- `row` - Bootstrap grid row
- `column` - Responsive column with breakpoint widths

**Component Elements**:
- `card` - Card with header/body/footer
- `accordion` - Collapsible sections
- `tabs` - Tabbed interface
- `carousel` - Swiper.js carousel/slider (V2)
- `table` - Semantic HTML table (V2)

### Custom Layout Elements

Add your own layout elements:

```php
'layout_element_types' => [
    // Built-in elements...

    // Your custom element
    'alert' => \App\CustomLayoutElements\AlertElement::class,
],
```

---

## File Uploads

### Upload Configuration

```php
'uploads' => [
    'disk' => env('SLICK_FORMS_UPLOAD_DISK', 'public'),
    'path' => 'form-uploads',
    'max_size' => 10240, // KB (10 MB)
    'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
],
```

### Customizing Uploads

#### Change Upload Disk

Use S3 for uploads:

```php
'uploads' => [
    'disk' => 's3',
    'path' => 'forms',
],
```

**.env**:
```bash
SLICK_FORMS_UPLOAD_DISK=s3
```

#### Increase Max File Size

Allow up to 50 MB:

```php
'uploads' => [
    'max_size' => 51200, // 50 MB in KB
],
```

#### Add Allowed File Types

```php
'uploads' => [
    'allowed_types' => [
        'jpg', 'jpeg', 'png', 'gif', 'webp',        // Images
        'pdf',                                       // PDF
        'doc', 'docx', 'odt', 'rtf',                // Documents
        'xls', 'xlsx', 'csv',                        // Spreadsheets
        'zip', 'rar', '7z',                          // Archives
        'mp4', 'avi', 'mov', 'wmv',                  // Videos
    ],
],
```

### PHP Upload Limits

Don't forget to update PHP settings in `php.ini`:

```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
```

---

## Email Notifications (V2)

### Email Configuration

```php
'email' => [
    'enabled' => env('SLICK_FORMS_EMAIL_ENABLED', true),
    'from_address' => env('SLICK_FORMS_EMAIL_FROM', env('MAIL_FROM_ADDRESS')),
    'from_name' => env('SLICK_FORMS_EMAIL_FROM_NAME', env('MAIL_FROM_NAME')),
    'queue' => env('SLICK_FORMS_EMAIL_QUEUE', true),
    'queue_connection' => env('SLICK_FORMS_EMAIL_QUEUE_CONNECTION', 'default'),
],
```

### Environment Variables

**.env**:
```bash
# Enable/Disable Email Notifications
SLICK_FORMS_EMAIL_ENABLED=true

# From Address
SLICK_FORMS_EMAIL_FROM=noreply@yourapp.com
SLICK_FORMS_EMAIL_FROM_NAME="Your App Name"

# Queue Settings
SLICK_FORMS_EMAIL_QUEUE=true
SLICK_FORMS_EMAIL_QUEUE_CONNECTION=redis

# SMTP Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### Disable Email Notifications

Temporarily disable all email notifications:

```bash
SLICK_FORMS_EMAIL_ENABLED=false
```

### Use Sync Queue

Send emails immediately without queue:

```bash
SLICK_FORMS_EMAIL_QUEUE=false
```

### SMTP Provider Examples

#### Gmail

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

#### SendGrid

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

#### Amazon SES

```bash
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
```

#### Mailgun

```bash
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-mailgun-key
```

**Related Documentation**: [Email Notifications Guide](EMAIL_NOTIFICATIONS.md)

---

## Spam Protection (V2)

### Spam Configuration

```php
'spam' => [
    'honeypot' => [
        'enabled' => env('SLICK_FORMS_HONEYPOT_ENABLED', true),
        'field_name' => env('SLICK_FORMS_HONEYPOT_FIELD', 'website'),
        'time_threshold' => env('SLICK_FORMS_HONEYPOT_TIME', 3), // seconds
    ],
    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'score_threshold' => env('RECAPTCHA_SCORE_THRESHOLD', 0.5), // v3 only
    ],
    'hcaptcha' => [
        'site_key' => env('HCAPTCHA_SITE_KEY'),
        'secret_key' => env('HCAPTCHA_SECRET_KEY'),
    ],
    'rate_limiting' => [
        'enabled' => env('SLICK_FORMS_RATE_LIMIT_ENABLED', true),
        'max_attempts' => env('SLICK_FORMS_RATE_LIMIT_ATTEMPTS', 5),
        'decay_minutes' => env('SLICK_FORMS_RATE_LIMIT_DECAY', 60),
    ],
],
```

### Honeypot Configuration

**.env**:
```bash
SLICK_FORMS_HONEYPOT_ENABLED=true
SLICK_FORMS_HONEYPOT_FIELD=website
SLICK_FORMS_HONEYPOT_TIME=3
```

**How it works**:
- Adds hidden field named `website` (bots fill it, humans don't)
- Requires minimum 3 seconds before submission (bots submit instantly)
- No user interaction required

### Google reCAPTCHA v3

**.env**:
```bash
RECAPTCHA_SITE_KEY=your-site-key
RECAPTCHA_SECRET_KEY=your-secret-key
RECAPTCHA_SCORE_THRESHOLD=0.5
```

**Get Keys**: https://www.google.com/recaptcha/admin

**Score Threshold**:
- `0.0` - Most lenient (allows more submissions)
- `0.5` - Balanced (recommended)
- `1.0` - Most strict (blocks more submissions)

### hCaptcha

**.env**:
```bash
HCAPTCHA_SITE_KEY=your-site-key
HCAPTCHA_SECRET_KEY=your-secret-key
```

**Get Keys**: https://www.hcaptcha.com/

### Rate Limiting

**.env**:
```bash
SLICK_FORMS_RATE_LIMIT_ENABLED=true
SLICK_FORMS_RATE_LIMIT_ATTEMPTS=5
SLICK_FORMS_RATE_LIMIT_DECAY=60
```

**Configuration**:
- Allow 5 submissions per IP address
- Within 60 minute window
- Automatically blocks after limit exceeded

### Disable Spam Protection

Disable all spam protection (not recommended for production):

```bash
SLICK_FORMS_HONEYPOT_ENABLED=false
SLICK_FORMS_RATE_LIMIT_ENABLED=false
```

**Related Documentation**: [Spam Protection Guide](SPAM_PROTECTION.md)

---

## Webhooks (V2)

### Webhook Configuration

```php
'webhooks' => [
    'enabled' => env('SLICK_FORMS_WEBHOOKS_ENABLED', true),
    'timeout' => env('SLICK_FORMS_WEBHOOK_TIMEOUT', 30),        // seconds
    'max_retries' => env('SLICK_FORMS_WEBHOOK_RETRIES', 3),
    'retry_delay' => env('SLICK_FORMS_WEBHOOK_RETRY_DELAY', 60), // seconds
    'queue' => env('SLICK_FORMS_WEBHOOK_QUEUE', true),
],
```

### Environment Variables

**.env**:
```bash
SLICK_FORMS_WEBHOOKS_ENABLED=true
SLICK_FORMS_WEBHOOK_TIMEOUT=30
SLICK_FORMS_WEBHOOK_RETRIES=3
SLICK_FORMS_WEBHOOK_RETRY_DELAY=60
SLICK_FORMS_WEBHOOK_QUEUE=true
```

### Retry Strategy

Default retry schedule:
1. **Attempt 1**: Immediate
2. **Attempt 2**: 60 seconds later
3. **Attempt 3**: 120 seconds later (2 minutes)
4. **Attempt 4**: 240 seconds later (4 minutes)

Customize retry delay:

```bash
SLICK_FORMS_WEBHOOK_RETRY_DELAY=300  # 5 minutes between retries
```

### Increase Timeout

For slow webhook endpoints:

```bash
SLICK_FORMS_WEBHOOK_TIMEOUT=60  # 60 seconds
```

### Disable Webhooks

```bash
SLICK_FORMS_WEBHOOKS_ENABLED=false
```

**Related Documentation**: [Webhooks Guide](WEBHOOKS.md)

---

## URL Obfuscation (V2)

### URL Configuration

```php
'urls' => [
    'hashid_salt' => env('SLICK_FORMS_HASHID_SALT', env('APP_KEY')),
    'hashid_min_length' => env('SLICK_FORMS_HASHID_MIN_LENGTH', 6),
    'signed_url_expiration' => env('SLICK_FORMS_SIGNED_URL_EXPIRATION', 24), // hours
],
```

### Environment Variables

**.env**:
```bash
SLICK_FORMS_HASHID_SALT="${APP_KEY}"
SLICK_FORMS_HASHID_MIN_LENGTH=6
SLICK_FORMS_SIGNED_URL_EXPIRATION=24
```

### Hashid Configuration

**What are hashids?**
- Convert numeric IDs to short, non-sequential strings
- Example: Form ID `123` becomes `/form/x9kL2p`
- Privacy-friendly (doesn't reveal form count or sequence)
- Shareable and compact

**Min Length**:
- `6` - Shortest (recommended for most cases)
- `8` - More characters for extra obfuscation
- `10` - Even longer hashes

```bash
SLICK_FORMS_HASHID_MIN_LENGTH=8  # Results in longer hashes like /form/m3nX4pQ8
```

**Custom Salt**:

Use a unique salt for extra security:

```bash
SLICK_FORMS_HASHID_SALT="my-super-secret-salt-12345"
```

**Warning**: Changing the salt will break all existing form URLs!

### Signed URLs

Configure expiration for signed URLs (used for pre-fill and one-time access):

```bash
SLICK_FORMS_SIGNED_URL_EXPIRATION=48  # Expires after 48 hours
```

**Related Documentation**: [QR Codes Guide](QR_CODES.md)

---

## Dynamic Options (V2)

### Dynamic Options Configuration

```php
'dynamic_options' => [
    'cache_enabled' => env('SLICK_FORMS_OPTIONS_CACHE', true),
    'cache_ttl' => env('SLICK_FORMS_OPTIONS_CACHE_TTL', 300), // seconds
    'timeout' => env('SLICK_FORMS_OPTIONS_TIMEOUT', 10),      // HTTP timeout
],
```

### Environment Variables

**.env**:
```bash
SLICK_FORMS_OPTIONS_CACHE=true
SLICK_FORMS_OPTIONS_CACHE_TTL=300
SLICK_FORMS_OPTIONS_TIMEOUT=10
```

### Cache Configuration

**Enable Caching**:
```bash
SLICK_FORMS_OPTIONS_CACHE=true
```

Caches dropdown options fetched from:
- External APIs
- Eloquent model queries

**Cache TTL** (Time To Live):
```bash
SLICK_FORMS_OPTIONS_CACHE_TTL=600  # Cache for 10 minutes
```

Longer TTL = Better performance but less fresh data
Shorter TTL = More API calls but fresher data

**Disable Caching**:
```bash
SLICK_FORMS_OPTIONS_CACHE=false
```

### HTTP Timeout

For slow external APIs:

```bash
SLICK_FORMS_OPTIONS_TIMEOUT=30  # Wait up to 30 seconds
```

---

## Default Form Settings

### Form Defaults Configuration

```php
'defaults' => [
    'is_active' => true,
    'store_ip_address' => true,
    'allow_guest_submissions' => true,
],
```

### Customizing Defaults

#### Disable New Forms by Default

```php
'defaults' => [
    'is_active' => false,  // New forms start inactive
],
```

#### Disable IP Address Storage

For GDPR compliance:

```php
'defaults' => [
    'store_ip_address' => false,
],
```

#### Require Authentication

Disable guest submissions:

```php
'defaults' => [
    'allow_guest_submissions' => false,  // Only logged-in users can submit
],
```

---

## Layout Configuration

### Application Layout

Specify which layout view to extend for full-page routes:

```php
'layout' => env('SLICK_FORMS_LAYOUT', 'layouts.app'),
```

**.env**:
```bash
SLICK_FORMS_LAYOUT=layouts.app
```

**Options**:
- `layouts.app` - Your application's main layout (default)
- `slick-forms::layouts.app` - Package's standalone layout

### Custom Layout

Create your own layout:

```blade
{{-- resources/views/layouts/forms.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Forms</title>
    @livewireStyles
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>
```

Update config:

```bash
SLICK_FORMS_LAYOUT=layouts.forms
```

---

## Conditional Logic

### Enable/Disable Conditional Logic

```php
'conditional_logic_enabled' => true,
```

Set to `false` to disable conditional field visibility and validation throughout the package.

---

## Bootstrap Configuration

### Bootstrap Version

```php
'bootstrap_version' => 5,
```

Currently only Bootstrap 5 is supported. This setting is reserved for future versions.

---

## Environment Variables Reference

### Core Settings

```bash
# Routes
SLICK_FORMS_LOAD_ROUTES=true
SLICK_FORMS_LAYOUT=layouts.app

# Uploads
SLICK_FORMS_UPLOAD_DISK=public
```

### Email Settings

```bash
# Email Notifications
SLICK_FORMS_EMAIL_ENABLED=true
SLICK_FORMS_EMAIL_FROM=noreply@yourapp.com
SLICK_FORMS_EMAIL_FROM_NAME="Your App"
SLICK_FORMS_EMAIL_QUEUE=true
SLICK_FORMS_EMAIL_QUEUE_CONNECTION=redis

# SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Spam Protection Settings

```bash
# Honeypot
SLICK_FORMS_HONEYPOT_ENABLED=true
SLICK_FORMS_HONEYPOT_FIELD=website
SLICK_FORMS_HONEYPOT_TIME=3

# reCAPTCHA v3
RECAPTCHA_SITE_KEY=your-site-key
RECAPTCHA_SECRET_KEY=your-secret-key
RECAPTCHA_SCORE_THRESHOLD=0.5

# hCaptcha
HCAPTCHA_SITE_KEY=your-site-key
HCAPTCHA_SECRET_KEY=your-secret-key

# Rate Limiting
SLICK_FORMS_RATE_LIMIT_ENABLED=true
SLICK_FORMS_RATE_LIMIT_ATTEMPTS=5
SLICK_FORMS_RATE_LIMIT_DECAY=60
```

### Webhook Settings

```bash
SLICK_FORMS_WEBHOOKS_ENABLED=true
SLICK_FORMS_WEBHOOK_TIMEOUT=30
SLICK_FORMS_WEBHOOK_RETRIES=3
SLICK_FORMS_WEBHOOK_RETRY_DELAY=60
SLICK_FORMS_WEBHOOK_QUEUE=true
```

### URL Settings

```bash
SLICK_FORMS_HASHID_SALT="${APP_KEY}"
SLICK_FORMS_HASHID_MIN_LENGTH=6
SLICK_FORMS_SIGNED_URL_EXPIRATION=24
```

### Dynamic Options Settings

```bash
SLICK_FORMS_OPTIONS_CACHE=true
SLICK_FORMS_OPTIONS_CACHE_TTL=300
SLICK_FORMS_OPTIONS_TIMEOUT=10
```

### Queue Settings

```bash
# Queue Connection
QUEUE_CONNECTION=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## Production Configuration Best Practices

### 1. Use Queues

Enable queues for emails and webhooks:

```bash
SLICK_FORMS_EMAIL_QUEUE=true
SLICK_FORMS_WEBHOOK_QUEUE=true
QUEUE_CONNECTION=redis
```

### 2. Enable Spam Protection

Enable all spam protection layers:

```bash
SLICK_FORMS_HONEYPOT_ENABLED=true
SLICK_FORMS_RATE_LIMIT_ENABLED=true
RECAPTCHA_SITE_KEY=your-key
RECAPTCHA_SECRET_KEY=your-secret
```

### 3. Secure Upload Directory

Use private disk for sensitive uploads:

```bash
SLICK_FORMS_UPLOAD_DISK=local
```

Configure in `config/filesystems.php`:

```php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
        'visibility' => 'private',
    ],
],
```

### 4. Use Strong Hashid Salt

Generate unique salt:

```bash
SLICK_FORMS_HASHID_SALT="$(php artisan key:generate --show)"
```

### 5. Enable Caching

Cache dynamic options:

```bash
SLICK_FORMS_OPTIONS_CACHE=true
SLICK_FORMS_OPTIONS_CACHE_TTL=600
```

### 6. Configure SMTP

Use reliable SMTP provider:

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

### 7. Set Reasonable Timeouts

Balance reliability and performance:

```bash
SLICK_FORMS_WEBHOOK_TIMEOUT=30
SLICK_FORMS_OPTIONS_TIMEOUT=10
```

### 8. Monitor Queue Workers

Run queue workers with supervisor:

```bash
php artisan queue:work redis --queue=default,emails,webhooks --tries=3
```

---

## Development Configuration

### Disable Queues

Send emails/webhooks synchronously for debugging:

```bash
SLICK_FORMS_EMAIL_QUEUE=false
SLICK_FORMS_WEBHOOK_QUEUE=false
```

### Use Log Driver

Send emails to log for testing:

```bash
MAIL_MAILER=log
```

### Disable Spam Protection

For easier form testing:

```bash
SLICK_FORMS_HONEYPOT_ENABLED=false
SLICK_FORMS_RATE_LIMIT_ENABLED=false
```

### Use Mailtrap

Capture test emails:

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
```

---

## Troubleshooting

### Routes Not Loading

**Issue**: Package routes return 404

**Solution**:
1. Verify `SLICK_FORMS_LOAD_ROUTES=true`
2. Clear route cache: `php artisan route:clear`
3. Ensure service provider registered

### Emails Not Sending

**Issue**: Email notifications not delivered

**Solutions**:
1. Check `SLICK_FORMS_EMAIL_ENABLED=true`
2. Verify SMTP credentials
3. Test mail config: `php artisan mail:test`
4. Check queue workers running if `EMAIL_QUEUE=true`
5. View email logs: `/slick-forms/logs/email/{formId}`

### File Uploads Failing

**Issue**: File upload returns error

**Solutions**:
1. Check disk exists in `config/filesystems.php`
2. Verify directory permissions: `storage/app/public` (775)
3. Increase PHP limits in `php.ini`
4. Check `max_size` in config

### Webhooks Timing Out

**Issue**: Webhook delivery fails with timeout

**Solutions**:
1. Increase timeout: `SLICK_FORMS_WEBHOOK_TIMEOUT=60`
2. Check webhook endpoint response time
3. View webhook logs: `/slick-forms/logs/webhook/{formId}`

### CAPTCHA Not Working

**Issue**: reCAPTCHA validation fails

**Solutions**:
1. Verify site key and secret key
2. Check domain whitelist in reCAPTCHA admin
3. Test with different browsers
4. Check JavaScript console for errors

### Rate Limiting Too Aggressive

**Issue**: Legitimate users blocked

**Solutions**:
1. Increase attempts: `SLICK_FORMS_RATE_LIMIT_ATTEMPTS=10`
2. Increase decay: `SLICK_FORMS_RATE_LIMIT_DECAY=120`
3. Whitelist specific IPs in code
4. Disable for development: `SLICK_FORMS_RATE_LIMIT_ENABLED=false`

---

## Related Documentation

- [Email Notifications](EMAIL_NOTIFICATIONS.md)
- [Spam Protection](SPAM_PROTECTION.md)
- [Webhooks](WEBHOOKS.md)
- [QR Codes](QR_CODES.md)
- [Working with Submissions](WORKING_WITH_SUBMISSIONS.md)
- [Analytics](ANALYTICS.md)

---

**Last Updated**: 2025-01-04
**Package Version**: v2.0.0
