# Slick Forms

**Build production-ready Laravel forms without writing frontend code**

A comprehensive Laravel package featuring a visual drag-and-drop form builder, 32 premium field types, advanced conditional logic, and a schema-driven architecture that makes extending effortless.

[![Laravel](https://img.shields.io/badge/Laravel-11%20%7C%2012-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)](https://php.net)
[![Livewire](https://img.shields.io/badge/Livewire-3.0-FB70A9?logo=livewire)](https://livewire.laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

![Form Builder Interface](docs/screenshots/slick-form-preview_compressed.gif)

*Drag-and-drop interface: field palette, canvas, and schema-driven properties panel, fully functional out of the box*

---

## What Makes Slick Forms Badass?

### ✨ Zero Frontend Code Required
Build complex, production-ready forms through a visual interface. No Vue, React, or HTML knowledge needed. Pure Laravel and Livewire magic.

### 📦 Self Contained
Although we integrate best-in-class libraries (Tom Select, Flatpickr, Quill, Ace Editor), you don't need to install them as dependencies. The entire package works right out of the box on any Laravel project that uses Livewire - zero configuration required.

### 🧩 32 Premium Field Types
Integrated with best-in-class libraries:
- **Tom Select v2.3.1** - Searchable, keyboard-navigable dropdowns
- **Flatpickr v4.6.13** - Powerful date/time pickers with multiple formats
- **Quill v1.3.6** - Rich WYSIWYG editor for paragraph content
- **Ace Editor v1.32.2** - Code editor with syntax highlighting
- **Signature Pad v4.1.7** 🆕 - Canvas-based signature capture
- **Leaflet v1.9.4** 🆕 - Interactive maps with location picking
- **Plus:** Password strength indicators, phone fields with country codes, dual-range sliders, rating matrices, and more

## What Makes It Badass-er

### 🎨 Advanced Conditional Logic
- **Visual Field Picker** - Eyedropper tool to select target fields
- **91 Operator Mappings** - Field-type specific operators (text contains, number greater than, date after, etc.)
- **AND/OR Logic** - Build complex conditional rules
- **Real-time Evaluation** - Instant show/hide with Livewire

### 📊 Built-in Analytics & Reporting
- **Form Performance Tracking** - Views, starts, completions, conversion rates
- **Field-Level Analytics** - Completion rates, drop-off points
- **Device & Browser Breakdown** - User agent tracking
- **Validation Error Analysis** - Most common validation failures
- **Time-Based Metrics** - Average completion time, submissions over time
- **Visual Dashboards** - Chart.js integration for data visualization

### 📱 Bootstrap 5 Grid System
- **6 Responsive Breakpoints** - xs, sm, md, lg, xl, xxl
- **Advanced Components** - Cards, tabs, accordions with full customization
- **Layout Elements** - Containers, rows, columns with offsets and ordering
- **Table Layout** - Grid-based structured data display

### 🔌 Three Integration Methods
Choose the approach that fits your workflow:
1. **Built-in Routes** - Ready-to-use form management UI at `/slick-forms/*` (can be overridden)
2. **Livewire Components** - Embed `<livewire:slick-forms::form-builder />` anywhere
3. **Programmatic API** - Build forms via Eloquent models

---

## 🚀 What's New in V2

Slick Forms V2 brings enterprise-grade features for advanced form workflows:

### 📧 Email Notifications
- **Custom Templates** - Design email notifications with Blade templates
- **Conditional Sending** - Send emails based on form data (e.g., only for VIP customers)
- **Variable Substitution** - Insert form values into subject/body with `{field_name}` syntax
- **Delivery Tracking** - Log all sent emails with timestamps and status
- **Multiple Recipients** - CC/BCC support with dynamic recipient lists

### 🔗 Webhook Integrations
- **Real-time POST** - Send form submissions to external APIs instantly
- **Custom Headers** - Add authentication tokens, API keys, etc.
- **Payload Transformation** - Map form fields to API structure
- **Retry Logic** - Automatic retries with exponential backoff
- **Event Logging** - Track webhook success/failure with detailed logs
- **Note:** Webhook payloads contain numeric IDs for database lookups, while public-facing URLs use hashids for privacy

### 🔒 Advanced Security & Spam Protection
- **Honeypot Fields** - Invisible spam traps that catch bots
- **Rate Limiting** - Prevent abuse with IP-based throttling (configurable per form)
- **CAPTCHA Integration** - Support for Google reCAPTCHA v2/v3 and hCaptcha
- **Spam Log Analysis** - Dashboard showing blocked attempts with IP tracking
- **IP Blacklisting** - Automatically block repeat offenders

### 🔐 Secure Hashid URLs
All forms use short, non-sequential hashid URLs:
- **Format** - `/form/x9kL2p` (short, shareable, privacy-friendly)
- **Custom Salt** - Per-form salt option for additional obfuscation
- **Signed URLs** - Add signatures for time-limited, one-time access
- **Pre-fill Support** - Hashid URLs work with pre-filled form data

Hashid URLs hide sequential IDs while remaining compact and shareable. Optional signed URLs add expiration and usage limits - perfect for private forms or surveys.

### 🗄️ Model Binding
Populate forms from existing database records and save back on submit:
- **Auto-populate** - Fill form fields from Eloquent models
- **Two-way Binding** - Create new records or update existing ones
- **Dot Notation** - Map nested relationships like `user.profile.bio`
- **Field Transformers** - Apply custom logic before save (e.g., encrypt sensitive data)
- **Conditional Updates** - Only update if user has permission

Perfect for edit profiles, update account settings, or any CRUD operation.

### 📡 Dynamic Options (Cascading Dropdowns)
Load select options from external sources:
- **API Integration** - Fetch options from REST endpoints
- **Database Queries** - Load from Eloquent models with scopes/filters
- **Cascading Logic** - Child dropdown options based on parent selection
- **Caching** - Configurable TTL to reduce API calls
- **Placeholder Support** - Replace `{parent}` in URLs for cascading

Example: Country → State → City dropdowns with real-time updates.

### 🎉 Success Screen Customization
Control what happens after form submission:
- **Custom Messages** - Display personalized thank you messages with variable substitution
- **Conditional Redirects** - Route users to different pages based on their answers
- **Show Submission Data** - Display submitted values with selective field hiding (e.g., hide passwords)
- **Download Buttons** - Offer PDF/CSV downloads of submission data
- **Edit Links** - Allow users to edit their submission after submit
- **Message Then Redirect** - Show a message for X seconds, then auto-redirect
- **Variable Replacement** - Use `{{field_name}}` and `{{submission.id}}` in messages/URLs

Perfect for confirmation pages, payment success screens, or multi-step workflows.

### 🎨 Enhanced Field Types
Three powerful new field types for advanced use cases:
- **Signature Pad** - Canvas-based signature capture with configurable pen colors and canvas size
- **Location Picker** - Interactive maps powered by OpenStreetMap with search, click-to-place, and drag markers
- **Rating Matrix** - Multi-item rating surveys (rate 5 products on same scale) with radio or dropdown input

### 📚 Form Versioning
Track changes and rollback when needed:
- **Version Snapshots** - Save complete form state (fields, layout, pages, settings)
- **Version History** - View all versions with timestamps and change summaries
- **One-Click Rollback** - Restore any previous version with full data integrity
- **Nested Element Support** - Properly handles complex layout hierarchies
- **Version Comparison** - See what changed between any two versions
- **Submission Tracking** - Know which version collected each submission

Perfect for maintaining audit trails, testing changes safely, or reverting breaking updates.

---

## Quick Start

Get your first form running in under 2 minutes:

```bash
# Install the package
composer require digitalisstudios/slick-forms

# Run migrations
php artisan migrate

# (Optional) Seed with demo forms, templates, and fake analytics
php artisan db:seed --class=DigitalisStudios\\SlickForms\\Database\\Seeders\\DemoFormsWithAnalyticsSeeder

# Or just seed the templates without demo data
php artisan db:seed --class=DigitalisStudios\\SlickForms\\Database\\Seeders\\FormTemplatesSeeder

# Visit the builder
open http://your-app.test/slick-forms/manage
```

That's it! Start building forms immediately.

---

## Key Features

| Feature | Description | Learn More |
|---------|-------------|------------|
| **Visual Form Builder** | Drag-and-drop interface with real-time preview | Built-in |
| **32 Field Types** | Text, email, select, date, signatures, maps, rating matrices, and more | [Field Types Reference](docs/FIELD_TYPES_REFERENCE.md) |
| **Conditional Logic** | Show/hide fields with visual picker and 91 operators | [Conditional Logic Guide](docs/CONDITIONAL_LOGIC.md) |
| **Advanced Layouts** | Bootstrap 5 grid with containers, rows, columns, cards, tabs | [Bootstrap Docs](https://getbootstrap.com/docs/5.3/layout/grid/) |
| **Multi-Page Forms** | Wizard-style forms with progress tracking | [Multi-Page Forms Guide](docs/MULTI_PAGE_FORMS.md) |
| **Form Analytics** | Track submissions, conversions, drop-offs, devices | [Analytics Guide](docs/ANALYTICS.md) |
| **Calculation Fields** | Auto-calculate values with formulas (SUM, AVG, etc.) | [Calculation Fields Guide](docs/CALCULATION_FIELDS.md) |
| **Repeater Fields** | Dynamic field groups users can add/remove | [Repeater Fields Guide](docs/REPEATER_FIELDS.md) |
| **Input Masks** | Format as you type (phone, SSN, credit card) | [Input Masks Guide](docs/INPUT_MASKS.md) |
| **Email Notifications** 🆕 | Send custom emails on submission with templates | [Email Notifications Guide](docs/EMAIL_NOTIFICATIONS.md) |
| **Webhook Integrations** 🆕 | POST submissions to external APIs with retry logic | [Webhooks Guide](docs/WEBHOOKS.md) |
| **Spam Protection** 🆕 | Honeypot, rate limiting, CAPTCHA, IP blacklisting | [Spam Protection Guide](docs/SPAM_PROTECTION.md) |
| **Hashid URLs** 🆕 | Short, non-sequential URLs with optional signatures | [QR Codes & URLs Guide](docs/QR_CODES.md) |
| **Model Binding** 🆕 | Populate forms from database, save back to models | [Schema Reference](docs/SCHEMA_REFERENCE.md) |
| **Dynamic Options** 🆕 | Cascading dropdowns from APIs or database queries | [Field Types Reference](docs/FIELD_TYPES_REFERENCE.md) |
| **Success Screen** 🆕 | Custom messages, redirects, downloads, edit links | [Success Screens Guide](docs/SUCCESS_SCREENS.md) |
| **Signature Pad** 🆕 | Canvas-based signature capture with pen customization | [Field Types Reference](docs/FIELD_TYPES_REFERENCE.md) · [Schema](docs/schema-reference/fields/signature-field.md) |
| **Location Picker** 🆕 | Interactive maps with search and marker placement | [Field Types Reference](docs/FIELD_TYPES_REFERENCE.md) · [Schema](docs/schema-reference/fields/location-field.md) |
| **Rating Matrix** 🆕 | Multi-item rating surveys on same scale | [Field Types Reference](docs/FIELD_TYPES_REFERENCE.md) · [Schema](docs/schema-reference/fields/rating_matrix-field.md) |
| **Form Versioning** 🆕 | Snapshot, rollback, compare versions, track changes | [Form Versioning Guide](docs/FORM_VERSIONING.md) |
| **Form Templates** | 5 pre-built professional templates | [Templates Guide](docs/TEMPLATES.md) |
| **Export Submissions** | CSV, Excel, PDF export | [Exports Guide](docs/EXPORTS.md) |
| **Form Submissions** | Track, view, and manage submissions with timestamps | Built-in viewer |
| **Extensibility** | Create custom field types in minutes with BaseFieldType | [Custom Field Types](docs/CUSTOM_FIELD_TYPES.md) |
| **Production Ready** | XSS prevention, CSRF protection, server-side validation | Secure by design |

---

## Installation

### Requirements

- PHP 8.2 or higher
- Laravel 11.0 or 12.0
- Livewire 3.0
- Bootstrap 5 (for styling)
- Bootstrap Icons (for field type icons)

### Install via Composer

```bash
composer require digitalisstudios/slick-forms
```

### Run Migrations

```bash
php artisan migrate
```

### Publish Configuration (Optional)

```bash
# Publish config file
php artisan vendor:publish --tag=slick-forms-config

# Publish views for customization
php artisan vendor:publish --tag=slick-forms-views
```

### Seed Demo Forms (Optional)

```bash
# Seed with demo forms, templates, submissions, and analytics
php artisan db:seed --class=DigitalisStudios\\SlickForms\\Database\\Seeders\\DemoFormsWithAnalyticsSeeder
```

This creates a complete demo environment:
- **10 Template Forms** - Contact, Lead Capture, Registration, Job Application, Event Registration, Survey, Product Order, Support Ticket, Newsletter, Booking
- **10 Working Forms** - Duplicates of templates ready for use
- **187 Submissions** - Realistic test data across all forms
- **342 Analytics Sessions** - User tracking with device/browser data
- **4,210 Analytics Events** - Views, starts, interactions, validations

**Alternatively, seed just templates without demo data:**

```bash
php artisan db:seed --class=DigitalisStudios\\SlickForms\\Database\\Seeders\\FormTemplatesSeeder
```

### Optional: Export Functionality

Slick Forms supports exporting submissions to CSV, Excel, and PDF formats. These features require optional packages:

```bash
# Install export packages
composer require maatwebsite/excel barryvdh/laravel-dompdf
```

**What happens without these packages?**
- Export buttons are automatically hidden from the submission viewer
- The package remains fully functional for form building and submission management
- Users see a helpful message explaining how to enable exports

**When to install:**
- You need to export submission data for analysis
- Clients require downloadable reports
- You want CSV/Excel integration with external tools

---

## Usage Patterns

Choose the integration method that fits your needs:

### Option 1: Built-in Routes (Recommended for Quick Start)

Perfect for admins who need a turnkey form management system.

```
GET  /slick-forms/manage              - List all forms
GET  /slick-forms/manage/create       - Create new form
GET  /slick-forms/builder/{form}      - Build form fields
GET  /slick-forms/form/{form}         - Display form to users
GET  /slick-forms/submissions/{form}  - View submissions
```

**When to use:** You want a standalone form builder without custom integration.

### Option 2: Livewire Components (For Custom Pages)

Embed form builder, renderer, or submission viewer in your existing Livewire/Blade views.

```blade
{{-- Form Builder --}}
<livewire:slick-forms::form-builder :form-id="1" />

{{-- Form Renderer --}}
<livewire:slick-forms::form-renderer :form-id="1" />

{{-- Submission Viewer --}}
<livewire:slick-forms::submission-viewer :form-id="1" />
```

**When to use:** You need forms embedded in existing application pages.

### Option 3: Programmatic API (For Developers)

Create and manage forms entirely via code.

```php
use DigitalisStudios\SlickForms\Models\CustomForm;

// Create a form
$form = CustomForm::create([
    'name' => 'Lead Capture Form',
    'description' => 'Collect leads from landing page',
    'is_active' => true,
]);

// Add fields
$form->fields()->create([
    'field_type' => 'email',
    'label' => 'Email Address',
    'name' => 'email',
    'is_required' => true,
    'order' => 1,
]);

// Retrieve submissions
$submissions = $form->submissions()
    ->with('fieldValues')
    ->latest()
    ->get();

foreach ($submissions as $submission) {
    // Process submission data
    $email = $submission->fieldValues
        ->where('field.name', 'email')
        ->first()->value;
}
```

**When to use:** You need programmatic form creation or headless usage.

**Learn more:** [Working with Submissions](docs/WORKING_WITH_SUBMISSIONS.md)

---

## Field Types Overview

Slick Forms includes 32 field types organized by category:

| Category | Fields | Key Features |
|----------|--------|--------------|
| **Input** | Text, Email, Number, Password, Phone, URL | Password strength indicator, phone country codes |
| **Selection** | Select, Radio, Checkbox, Switch, Tags | Tom Select searchable dropdowns |
| **Date/Time** | Date, Time, Date Range | Flatpickr with multiple formats, inline calendar |
| **Files** | File, Image, Video | Drag-and-drop upload, image previews |
| **Interactive** | Star Rating, Slider, Range, Color Picker | Dual sliders, color palette |
| **Content** | Header, Paragraph, Code | Quill WYSIWYG, Ace Editor syntax highlighting |
| **Advanced** | Calculation, Repeater, Hidden, Table | Formula evaluation, dynamic rows |

→ **Full details with examples:** [Field Types Reference](docs/FIELD_TYPES_REFERENCE.md)

---

## Documentation

### Getting Started
- **Installation & Setup** - _(see above)_
- **Building Your First Form** - Use the Quick Start guide
- **Real-World Examples** - [10 production-ready form examples](docs/EXAMPLES.md)
- **Field Types Reference** - [Complete catalog of all 32 field types](docs/FIELD_TYPES_REFERENCE.md)

### Advanced Features
- **Conditional Logic** - [Visual picker, operators, AND/OR logic](docs/CONDITIONAL_LOGIC.md)
- **Schema Reference** - [Complete schema configuration guide](docs/SCHEMA_REFERENCE.md)
- **Events Reference** - [All 15 events with usage examples](docs/EVENTS.md)
- **Jobs Reference** - [Queue jobs and configuration](docs/JOBS.md)

### V2 Features
- **Form Versioning** - [Version tracking, comparison, and restoration](docs/FORM_VERSIONING.md)
- **Success Screens** - [Custom success messages and redirects](docs/SUCCESS_SCREENS.md)
- **QR Codes** - [Generate QR codes for form URLs](docs/QR_CODES.md)
- **Webhooks** - [Real-time integration with external services](docs/WEBHOOKS.md)
- **Email Notifications** - [Automated email templates and delivery](docs/EMAIL_NOTIFICATIONS.md)
- **Spam Protection** - [Honeypot, rate limiting, and CAPTCHA](docs/SPAM_PROTECTION.md)

### Component & Configuration Reference
- **Livewire Components** - [Complete reference for all 10 components](docs/COMPONENTS.md)
- **Configuration Guide** - [Full configuration reference with all V2 settings](docs/CONFIGURATION.md)

### Extending Slick Forms
- **Custom Field Types** - [Create your own field types with BaseFieldType](docs/CUSTOM_FIELD_TYPES.md)
- **Custom Layout Elements** - [Build custom layout components](docs/CUSTOM_LAYOUT_ELEMENTS.md)
- **Migration Guide** - [Upgrade to schema-driven properties](docs/MIGRATION_GUIDE.md)

### External Resources
- **Bootstrap 5 Documentation** - [Layout system, components, utilities](https://getbootstrap.com/docs/5.3/getting-started/introduction/)
- **Livewire 3 Documentation** - [Reactive components, forms, validation](https://livewire.laravel.com/docs)
- **Laravel Validation** - [Available validation rules](https://laravel.com/docs/validation#available-validation-rules)

---

## Configuration

Slick Forms includes comprehensive configuration options for routes, field types, uploads, email notifications, spam protection, webhooks, and more.

### Quick Configuration

Basic configuration options (config/slick-forms.php):

```php
return [
    // Enable/disable package routes
    'load_routes' => true,

    // Layout to extend for full-page views
    'layout' => 'layouts.app',

    // File upload settings
    'uploads' => [
        'disk' => 'public',
        'max_size' => 10240, // KB
    ],

    // Enable conditional logic
    'conditional_logic_enabled' => true,

    // V2: Email notifications
    'email' => [
        'enabled' => true,
        'queue' => true,
    ],

    // V2: Spam protection
    'spam' => [
        'honeypot' => ['enabled' => true],
        'rate_limiting' => ['enabled' => true],
    ],

    // V2: Webhooks
    'webhooks' => [
        'enabled' => true,
        'queue' => true,
    ],
];
```

### Environment Variables

```env
# Core
SLICK_FORMS_LOAD_ROUTES=true
SLICK_FORMS_LAYOUT=layouts.app
SLICK_FORMS_UPLOAD_DISK=public

# Email Notifications (V2)
SLICK_FORMS_EMAIL_ENABLED=true
SLICK_FORMS_EMAIL_QUEUE=true
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io

# Spam Protection (V2)
SLICK_FORMS_HONEYPOT_ENABLED=true
SLICK_FORMS_RATE_LIMIT_ENABLED=true
RECAPTCHA_SITE_KEY=your-site-key
RECAPTCHA_SECRET_KEY=your-secret-key

# Webhooks (V2)
SLICK_FORMS_WEBHOOKS_ENABLED=true
SLICK_FORMS_WEBHOOK_QUEUE=true

# URL Obfuscation (V2)
SLICK_FORMS_HASHID_SALT="${APP_KEY}"
SLICK_FORMS_HASHID_MIN_LENGTH=6
```

**Complete Configuration Guide**: [See docs/CONFIGURATION.md](docs/CONFIGURATION.md) for all settings, SMTP setup, CAPTCHA integration, and production best practices.

### Route Customization

Slick Forms V2 introduces comprehensive route customization with **hashid-only URLs** for enhanced security and privacy.

#### Default Routes

All package routes use secure hashid URLs that are short, non-sequential, and shareable:

```
/slick-forms/manage                    - Form management dashboard
/slick-forms/builder/x9kL2p            - Form builder (hashid)
/slick-forms/form/x9kL2p               - Public form display (hashid)
/slick-forms/submissions/x9kL2p        - Submission viewer (hashid)
/slick-forms/analytics/x9kL2p          - Analytics dashboard (hashid)
```

**Security Benefits:**
- ✅ **No ID Enumeration** - Hashids prevent sequential form discovery
- ✅ **No Information Leakage** - Can't determine form count or creation order
- ✅ **Privacy-Friendly** - URLs don't reveal internal database structure
- ✅ **Short & Shareable** - 6+ characters (vs 36 for UUIDs)
- ✅ **Per-Form Salts** - Optional custom salt per form for extra obfuscation

#### Customizing Routes

Publish the config to customize all routes, middleware, and URL segments:

```bash
php artisan vendor:publish --tag=slick-forms-config
```

**Example 1: Change Route Prefix**

```php
// config/slick-forms.php
'routes' => [
    'prefix' => 'admin/forms',  // Changed from 'slick-forms'
    // ...
],
```

Now routes become: `/admin/forms/manage`, `/admin/forms/builder/x9kL2p`, etc.

**Example 2: Customize URL Segments**

```php
// config/slick-forms.php
'routes' => [
    'segments' => [
        'builder' => 'editor',        // /admin/forms/editor/x9kL2p
        'manage' => 'dashboard',       // /admin/forms/dashboard
        'submissions' => 'responses',  // /admin/forms/responses/x9kL2p
        // ... customize any segment
    ],
],
```

**Example 3: Per-Route Middleware Control**

```php
// config/slick-forms.php
'routes' => [
    'middleware' => [
        'admin' => ['web', 'auth', 'role:admin'],  // Require admin role
        'public' => ['web', 'throttle:60,1'],      // Rate limit public forms
    ],

    'builder' => [
        'middleware' => 'admin',  // Use admin middleware group
        'show' => '{builder}/{form}',
    ],

    'submissions' => [
        'middleware' => 'admin',
        // Override single route with custom middleware
        'export_csv' => [
            'uri' => '{submissions}/{form}/{export}/{csv}',
            'middleware' => ['web', 'auth', 'can:export-submissions'],
        ],
    ],
],
```

**Example 4: Disable Package Routes (Use Your Own)**

```php
// config/slick-forms.php
'load_routes' => false,
```

```php
// routes/web.php
use DigitalisStudios\SlickForms\Http\Controllers\FormBuilderController;
use DigitalisStudios\SlickForms\Http\Controllers\FormRendererController;

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/forms', [FormBuilderController::class, 'index']);
    Route::get('/forms/builder/{form}', [FormBuilderController::class, 'show']);
});

// Public form route (no auth required)
Route::get('/f/{hash}', [FormRendererController::class, 'showByHash'])
    ->name('custom.form.show');
```

**Important:** When using custom routes, all `{form}` and `{template}` parameters automatically use hashid binding. Pass model objects to `route()` helper and they'll be converted to hashids automatically.

#### Migration from Numeric IDs

**Breaking Change in V2:** All numeric ID and UUID routes have been removed for security.

**Before (V1):**
```php
// Numeric ID routes (REMOVED)
/slick-forms/form/123
/slick-forms/builder/123

// UUID routes (REMOVED)
/slick-forms/form/550e8400-e29b-41d4-a716-446655440000
```

**After (V2):**
```php
// Hashid routes only
/slick-forms/form/x9kL2p
/slick-forms/builder/x9kL2p
```

**Migration Steps:**
1. Update any bookmarked URLs to use hashid format
2. Update external integrations to use hashid URLs from your application
3. **Important:** Webhook payloads intentionally contain numeric `form_id` and `submission_id` for efficient database lookups by external systems. Only public-facing URLs use hashids for privacy.

**Generating Hashid URLs in Code:**

```php
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Services\UrlObfuscationService;

$form = CustomForm::find(1);

// Method 1: Pass model to route() (automatic hashid conversion)
$url = route('slick-forms.form.show.hash', ['hash' => $form]);

// Method 2: Use UrlObfuscationService
$urlService = app(UrlObfuscationService::class);
$hashid = $urlService->encodeId($form->id);
$url = route('slick-forms.form.show.hash', ['hash' => $hashid]);

// Method 3: Use convenience method
$url = $urlService->generateFormUrl($form);
```

→ **Complete Config Reference:** See `config/slick-forms.php` after publishing for all available options and detailed documentation.

---

## Analytics & Reporting

Track form performance with built-in analytics:

```php
// Access analytics dashboard
Route: /slick-forms/analytics/{form}
```

**Metrics Tracked:**
- Form views, starts, completions
- Conversion rates and drop-off points
- Field completion rates
- Device and browser breakdown
- Common validation errors
- Average completion time
- Submissions over time (charts)

**Requirements:** No additional packages needed - fully built-in.

→ **Complete Guide:** [Analytics Documentation](docs/ANALYTICS.md)

---

## Multi-Page Forms

Break long forms into steps with progress tracking:

```php
// Enable in form settings
$form->settings = [
    'multi_page_enabled' => true,
    'progress_style' => 'steps', // or 'bar', 'breadcrumb'
];
```

**Features:**
- Unlimited pages per form
- Three progress indicator styles (Steps, Bar, Breadcrumbs)
- Per-page validation
- Next/Previous navigation
- Page-specific fields and layout
- Drag-and-drop page reordering

→ **Complete Guide:** [Multi-Page Forms Documentation](MULTI_PAGE_FORMS.md)

---

## Form Templates

Start quickly with 10 professionally designed templates:

```bash
# Seed template forms only
php artisan db:seed --class=DigitalisStudios\\SlickForms\\Database\\Seeders\\FormTemplatesSeeder

# Or seed templates + demo forms + submissions + analytics
php artisan db:seed --class=DigitalisStudios\\SlickForms\\Database\\Seeders\\DemoFormsWithAnalyticsSeeder
```

**Templates Included:**
1. **Contact Form** - Basic contact form with name, email, phone, message
2. **Lead Capture** - Lead generation with conditional logic
3. **Multi-Step Registration** - Registration with tabs
4. **Job Application** - Employment application with file upload
5. **Event Registration** - Registration form with attendee details
6. **Customer Satisfaction Survey** - Rating scales and feedback
7. **Product Order Form** - Order form with repeater fields
8. **Support Ticket** - Technical support request form
9. **Newsletter Signup** - Email subscription with preferences
10. **Booking Form** - Appointment booking with date range

**Browse Templates:** `/slick-forms/templates` (when templates exist)

→ **Complete Guide:** [Templates Documentation](docs/TEMPLATES.md)

---

## Advanced Features

### Calculation Fields

Auto-calculate values based on other fields:

```php
$form->fields()->create([
    'field_type' => 'calculation',
    'name' => 'total',
    'options' => [
        'formula' => '{price} * {quantity}',
        'prefix' => '$',
        'decimal_places' => 2,
    ],
]);
```

**Supported Operations:**
- Basic operators: `+`, `-`, `*`, `/`, `%`, `()`
- Functions: `SUM()`, `AVG()`, `MIN()`, `MAX()`, `ROUND()`, `ABS()`
- Field references: `{field_name}`
- Display formats: Number, Currency, Percentage

→ **Complete Guide:** [Calculation Fields Documentation](CALCULATION_FIELDS.md)

### Repeater Fields

Let users add/remove groups of fields:

```php
$form->fields()->create([
    'field_type' => 'repeater',
    'name' => 'work_experience',
    'options' => [
        'min_instances' => 1,
        'max_instances' => 10,
        'layout_style' => 'card', // or 'accordion', 'plain'
    ],
]);
```

**Features:**
- Dynamic add/remove instances
- Drag-to-reorder instances
- Min/max instance limits
- Three layout styles
- Nested field support

→ **Complete Guide:** [Repeater Fields Documentation](REPEATER_FIELDS.md)

### Input Masks

Format input as users type:

```php
// Available masks: phone, credit_card, ssn, date, time, currency, etc.
$field->options['input_mask'] = 'phone_us'; // (555) 123-4567
$field->options['input_mask'] = 'credit_card'; // 1234 5678 9012 3456
$field->options['input_mask'] = 'ssn'; // 123-45-6789
```

**Mask Types:**
- Phone (US and International)
- Credit Card (auto-detects type)
- Date (multiple formats)
- Time (12/24 hour)
- Currency, Percentage
- SSN, ZIP codes
- Custom patterns

→ **Complete Guide:** [Input Masks Documentation](INPUT_MASKS.md)

### Export Submissions

Export form submissions in multiple formats:

```bash
# Install optional packages for Excel/PDF
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

**Export Formats:**
- **CSV** - Built-in, no packages required
- **Excel** - Requires `maatwebsite/excel`
- **PDF** - Requires `barryvdh/laravel-dompdf`

→ **Complete Guide:** [Exports Documentation](docs/EXPORTS.md)

---

## Database Schema

All tables use the `slick_` prefix to avoid conflicts:

### Core Tables (v1.0)
- `slick_forms` - Form definitions with settings and configuration
- `slick_form_fields` - Form fields with validation and conditional logic
- `slick_form_layout_elements` - Layout structure (containers, rows, columns, cards, tabs, accordions, carousels)
- `slick_form_submissions` - User submissions with IP tracking and metadata
- `slick_form_field_values` - Submitted field values
- `slick_form_pages` - Multi-page form support with progress tracking

### Analytics Tables (v1.0)
- `slick_form_analytics_sessions` - User session tracking (device, browser, timing)
- `slick_form_analytics_events` - Analytics events (views, starts, interactions, validations)

### Integration Tables (v2.0)
- `slick_form_email_templates` - Email notification templates with variables
- `slick_form_email_logs` - Email delivery tracking and status
- `slick_form_webhooks` - Webhook endpoint configurations
- `slick_form_webhook_logs` - Webhook delivery attempts and responses
- `slick_form_spam_logs` - Spam attempt logging with IP tracking
- `slick_dynamic_options_cache` - Cached dynamic dropdown options
- `slick_form_model_bindings` - Form-to-Eloquent model mappings
- `slick_form_signed_urls` - Signed URL tracking with expiration
- `slick_form_versions` - Form version snapshots and history

**Total:** 17 tables

---

## Security & Performance

### Security Features
- **XSS Prevention** - All user input properly escaped
- **CSRF Protection** - Laravel middleware integration
- **Server-Side Validation** - Never trust client-side validation
- **File Upload Validation** - Type and size restrictions
- **IP Tracking** - Log submission sources

### Performance
- **Optimized Queries** - Eager loading to prevent N+1
- **Livewire Integration** - Minimal JavaScript overhead
- **Asset Management** - CDN-ready third-party libraries
- **Efficient Rendering** - Component-based view architecture

---

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

**Note:** JavaScript must be enabled for the drag-and-drop builder interface.

---

## Roadmap

### V2.1 Planned Features
- API endpoints for headless usage (RESTful API for forms and submissions)
- Advanced form scheduling (timezone support, recurring availability)
- Performance monitoring dashboard (page load times, field render times)
- Bulk operations (duplicate multiple forms, bulk delete submissions)

### V3.0 Vision
- Advanced field types:
  - File manager (browse/select existing uploaded files)
  - Drawing canvas (free-form drawing with tools)
  - Audio recorder (voice memos and audio capture)
- Advanced permissions (team collaboration, role-based access)
- Advanced automation (Zapier integration, custom workflows)
- White-label customization (custom branding, logo, colors)

---

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request to the [GitHub repository](https://github.com/digitalisstudios/slick-forms).

---

## Credits

**Created by:** [Brandon Moore](mailto:brandon@digitalisstudios.com)

**Built with:**
- [Laravel](https://laravel.com) - PHP web framework
- [Livewire](https://livewire.laravel.com) - Reactive components
- [Bootstrap 5](https://getbootstrap.com) - UI framework
- [Tom Select](https://tom-select.js.org) v2.3.1 - Searchable dropdowns
- [Flatpickr](https://flatpickr.js.org) v4.6.13 - Date/time picker
- [Quill](https://quilljs.com) v1.3.6 - WYSIWYG editor
- [Ace Editor](https://ace.c9.io) v1.32.2 - Code editor
- [SortableJS](https://sortablejs.github.io/Sortable/) - Drag-and-drop
- [Signature Pad](https://github.com/szimek/signature_pad) v4.1.7 - Signature capture
- [Leaflet](https://leafletjs.com) v1.9.4 - Interactive maps
- [Swiper](https://swiperjs.com) v12 - Carousel/slider functionality
- [Hashids](https://hashids.org) - URL obfuscation

**Inspired By**

No source code from the following projects was actually used, but these projects all had features that inspired this project:
- [formBuilder.online](https://formbuilder.online/) - Form building concepts
- [VvvebJs](https://www.vvveb.com/vvvebjs/editor.html) - Visual page builder approach
- [Editor.js](https://editorjs.io/) - Block-based editing interface

---

## License

Slick Forms is open-source software licensed under the [MIT license](LICENSE).

---

## Support

For issues, questions, or feature requests:
- **Issues:** [GitHub Issue Tracker](https://github.com/digitalisstudios/slick-forms/issues)
- **Documentation:** [Complete guides in docs/](docs/)
- **Email:** brandon@digitalisstudios.com

**Star this repository** if Slick Forms helps your project!
