<?php

use DigitalisStudios\SlickForms\FieldTypes\CalculationField;
use DigitalisStudios\SlickForms\FieldTypes\CheckboxField;
use DigitalisStudios\SlickForms\FieldTypes\CodeField;
use DigitalisStudios\SlickForms\FieldTypes\ColorPickerField;
use DigitalisStudios\SlickForms\FieldTypes\DateField;
use DigitalisStudios\SlickForms\FieldTypes\DateRangeField;
use DigitalisStudios\SlickForms\FieldTypes\EmailField;
use DigitalisStudios\SlickForms\FieldTypes\FileField;
use DigitalisStudios\SlickForms\FieldTypes\HeaderField;
use DigitalisStudios\SlickForms\FieldTypes\HiddenField;
use DigitalisStudios\SlickForms\FieldTypes\ImageField;
use DigitalisStudios\SlickForms\FieldTypes\LocationPickerField;
use DigitalisStudios\SlickForms\FieldTypes\NumberField;
use DigitalisStudios\SlickForms\FieldTypes\ParagraphField;
use DigitalisStudios\SlickForms\FieldTypes\PasswordField;
use DigitalisStudios\SlickForms\FieldTypes\PdfEmbedField;
use DigitalisStudios\SlickForms\FieldTypes\PhoneField;
use DigitalisStudios\SlickForms\FieldTypes\RadioField;
use DigitalisStudios\SlickForms\FieldTypes\RangeField;
use DigitalisStudios\SlickForms\FieldTypes\RatingMatrixField;
use DigitalisStudios\SlickForms\FieldTypes\RepeaterField;
use DigitalisStudios\SlickForms\FieldTypes\SelectField;
use DigitalisStudios\SlickForms\FieldTypes\SignaturePadField;
use DigitalisStudios\SlickForms\FieldTypes\SliderField;
use DigitalisStudios\SlickForms\FieldTypes\StarRatingField;
use DigitalisStudios\SlickForms\FieldTypes\SwitchField;
use DigitalisStudios\SlickForms\FieldTypes\TagsField;
use DigitalisStudios\SlickForms\FieldTypes\TextareaField;
use DigitalisStudios\SlickForms\FieldTypes\TextField;
use DigitalisStudios\SlickForms\FieldTypes\TimeField;
use DigitalisStudios\SlickForms\FieldTypes\UrlField;
use DigitalisStudios\SlickForms\FieldTypes\VideoField;
// Layout Element Types
use DigitalisStudios\SlickForms\LayoutElementTypes\AccordionItemElement;
use DigitalisStudios\SlickForms\LayoutElementTypes\AccordionType;
use DigitalisStudios\SlickForms\LayoutElementTypes\CardType;
use DigitalisStudios\SlickForms\LayoutElementTypes\CarouselSlideElement;
use DigitalisStudios\SlickForms\LayoutElementTypes\CarouselType;
use DigitalisStudios\SlickForms\LayoutElementTypes\ColumnType;
use DigitalisStudios\SlickForms\LayoutElementTypes\ContainerType;
use DigitalisStudios\SlickForms\LayoutElementTypes\RowType;
use DigitalisStudios\SlickForms\LayoutElementTypes\TabElement;
use DigitalisStudios\SlickForms\LayoutElementTypes\TableCellElement;
use DigitalisStudios\SlickForms\LayoutElementTypes\TableRowElement;
use DigitalisStudios\SlickForms\LayoutElementTypes\TableSectionElement;
use DigitalisStudios\SlickForms\LayoutElementTypes\TableType;
use DigitalisStudios\SlickForms\LayoutElementTypes\TabsType;

return [

    /*
    |--------------------------------------------------------------------------
    | Load Routes
    |--------------------------------------------------------------------------
    |
    | Automatically load the package routes. Set to false if you want to
    | manually define routes or use only the Livewire components directly.
    |
    */

    'load_routes' => env('SLICK_FORMS_LOAD_ROUTES', true),

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Customize all package routes including URIs and middleware. This config
    | uses a hybrid approach: define reusable segments that auto-compose into
    | route URIs, or override individual routes with custom URIs.
    |
    | SECURITY: All form references use hashids (short, non-sequential, obfuscated).
    | Raw numeric IDs are never exposed in URLs for security and privacy.
    |
    | HOW IT WORKS:
    | - Routes use {segment_name} placeholders that substitute from 'segments' array
    | - Middleware references keys from 'middleware' array (e.g., 'admin')
    | - Override any route by using array instead of string: ['uri' => '...', 'middleware' => [...]]
    |
    | EXAMPLES:
    | - Change all 'manage' routes: Update segments.manage to 'admin'
    | - Custom single route: 'show' => ['uri' => 'custom/path', 'middleware' => ['web', 'special']]
    | - Add middleware to group: Change 'middleware' => 'admin' to ['web', 'auth', 'role:admin']
    |
    */

    'routes' => [
        'prefix' => 'slick-forms',
        'name_prefix' => 'slick-forms.',

        /*
        | Middleware Groups
        | Define reusable middleware stacks referenced by routes below.
        | - 'admin' = Protected routes requiring authentication (builder, manage, analytics, etc.)
        | - 'public' = Public routes (form display only)
        */
        'middleware' => [
            'admin' => ['web', 'auth'],
            'public' => ['web'],
        ],

        /*
        | URL Segments
        |
        | Building blocks that compose into route URIs via {segment_name} substitution.
        |
        | FORMAT: 'key' => 'value'
        | - KEY: Reference name used in route definitions below (e.g., {duplicate})
        | - VALUE: Actual URL segment that appears in the browser
        |
        | EXAMPLE: 'duplicate' => 'copy'
        | - Route definition uses: '{forms}/{form}/{duplicate}'
        | - Final URL becomes: /slick-forms/forms/x9kL2p/copy
        |
        | Change VALUES to customize URL appearance. Do not change KEYS unless you also
        | update all route definitions that reference them.
        */
        'segments' => [
            'builder' => 'builder',          // KEY: {builder}, URL: /builder
            'form_path' => 'form',           // KEY: {form_path}, URL: /form
            'manage' => 'manage',            // KEY: {manage}, URL: /manage
            'submissions' => 'submissions',  // KEY: {submissions}, URL: /submissions
            'analytics' => 'analytics',      // KEY: {analytics}, URL: /analytics
            'templates' => 'templates',      // KEY: {templates}, URL: /templates
            'forms' => 'forms',              // KEY: {forms}, URL: /forms (for operations like duplicate)
            'export' => 'export',            // KEY: {export}, URL: /export
            'prefill' => 'prefill',          // KEY: {prefill}, URL: /prefill
            'create' => 'create',            // KEY: {create}, URL: /create
            'edit' => 'edit',                // KEY: {edit}, URL: /edit
            'duplicate' => 'duplicate',      // KEY: {duplicate}, URL: /duplicate
            'toggle_active' => 'toggle-active',  // KEY: {toggle_active}, URL: /toggle-active
            'save_as_template' => 'save-as-template',  // KEY: {save_as_template}, URL: /save-as-template
            'use' => 'use',                  // KEY: {use}, URL: /use
            'csv' => 'csv',                  // KEY: {csv}, URL: /csv
            'excel' => 'excel',              // KEY: {excel}, URL: /excel
            'pdf' => 'pdf',                  // KEY: {pdf}, URL: /pdf
        ],

        /*
        | Form Builder Routes
        | Admin interface for building/editing forms via drag-and-drop
        */
        'builder' => [
            'middleware' => 'admin',
            'show' => '{builder}/{form}',  // Final URL: /slick-forms/builder/x9kL2p
        ],

        /*
        | Form Display Routes (Public)
        | User-facing form rendering and submission - uses hashids only for security
        */
        'form' => [
            'middleware' => 'public',
            'show' => '{form_path}/{hash}',  // Final URL: /slick-forms/form/x9kL2p
            'show_prefilled' => '{form_path}/{hash}/{prefill}/{data}',  // Final URL: /slick-forms/form/x9kL2p/prefill/encrypted_data
        ],

        /*
        | Submission Management Routes
        | View and export form submissions (admin only)
        */
        'submissions' => [
            'middleware' => 'admin',
            'show' => '{submissions}/{form}',  // Final URL: /slick-forms/submissions/x9kL2p
            'export_csv' => '{submissions}/{form}/{export}/{csv}',  // Final URL: /slick-forms/submissions/x9kL2p/export/csv
            'export_excel' => '{submissions}/{form}/{export}/{excel}',  // Final URL: /slick-forms/submissions/x9kL2p/export/excel
            'export_pdf' => '{submissions}/{form}/{export}/{pdf}',  // Final URL: /slick-forms/submissions/x9kL2p/export/pdf
        ],

        /*
        | Analytics Routes
        | View form analytics, metrics, and performance data
        */
        'analytics' => [
            'middleware' => 'admin',
            'show' => '{analytics}/{form}',  // Final URL: /slick-forms/analytics/x9kL2p
        ],

        /*
        | Form Management Routes (CRUD)
        | Create, edit, update, and delete forms
        */
        'manage' => [
            'middleware' => 'admin',
            'index' => '{manage}',  // Final URL: /slick-forms/manage
            'create' => '{manage}/{create}',  // Final URL: /slick-forms/manage/create
            'store' => '{manage}',  // POST to: /slick-forms/manage
            'edit' => '{manage}/{form}/{edit}',  // Final URL: /slick-forms/manage/x9kL2p/edit
            'update' => '{manage}/{form}',  // PUT to: /slick-forms/manage/x9kL2p
            'destroy' => '{manage}/{form}',  // DELETE to: /slick-forms/manage/x9kL2p
        ],

        /*
        | Form Operations Routes
        | Duplicate forms and toggle active/inactive status
        */
        'forms' => [
            'middleware' => 'admin',
            'duplicate' => '{forms}/{form}/{duplicate}',  // POST to: /slick-forms/forms/x9kL2p/duplicate
            'toggle_active' => '{forms}/{form}/{toggle_active}',  // POST to: /slick-forms/forms/x9kL2p/toggle-active
        ],

        /*
        | Template Routes
        | Create forms from templates and save forms as templates
        */
        'templates' => [
            'middleware' => 'admin',
            'use' => '{templates}/{template}/{use}',  // POST to: /slick-forms/templates/x9kL2p/use
            'save_as_template' => '{forms}/{form}/{save_as_template}',  // POST to: /slick-forms/forms/x9kL2p/save-as-template
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | The layout view to extend for the package's full-page routes.
    | Set to 'layouts.app' to use your application's main layout, or
    | 'slick-forms::layouts.app' to use the package's standalone layout.
    |
    */

    'layout' => env('SLICK_FORMS_LAYOUT', 'layouts.app'),

    /*
    |--------------------------------------------------------------------------
    | Field Types
    |--------------------------------------------------------------------------
    |
    | Register available field types that will be available in the form builder.
    | You can add your custom field types here.
    |
    */

    'field_types' => [
        // Basic Input Fields
        'text' => TextField::class,
        'textarea' => TextareaField::class,
        'email' => EmailField::class,
        'number' => NumberField::class,
        'password' => PasswordField::class,
        'phone' => PhoneField::class,
        'url' => UrlField::class,
        'hidden' => HiddenField::class,

        // Selection Fields
        'select' => SelectField::class,
        'radio' => RadioField::class,
        'checkbox' => CheckboxField::class,
        'switch' => SwitchField::class,

        // Date/Time Fields
        'date' => DateField::class,
        'time' => TimeField::class,
        'date_range' => DateRangeField::class,

        // File Fields
        'file' => FileField::class,
        'image' => ImageField::class,
        'video' => VideoField::class,
        'pdf_embed' => PdfEmbedField::class,

        // Advanced Fields
        'star_rating' => StarRatingField::class,
        'slider' => SliderField::class,
        'range' => RangeField::class,
        'color_picker' => ColorPickerField::class,
        'tags' => TagsField::class,

        // Calculation Field
        'calculation' => CalculationField::class,

        // Repeater Field
        'repeater' => RepeaterField::class,

        // Signature Field
        'signature' => SignaturePadField::class,

        // Location Field
        'location' => LocationPickerField::class,

        // Rating Matrix Field
        'rating_matrix' => RatingMatrixField::class,

        // Display Fields
        'code' => CodeField::class,
        'header' => HeaderField::class,
        'paragraph' => ParagraphField::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout Element Types
    |--------------------------------------------------------------------------
    |
    | Register available layout element types for the form builder.
    | These provide structural elements like containers, rows, columns, etc.
    | You can add your custom layout element types here.
    |
    */

    'layout_element_types' => [
        'container' => ContainerType::class,
        'row' => RowType::class,
        'column' => ColumnType::class,
        'card' => CardType::class,
        'tabs' => TabsType::class,
        'tab' => TabElement::class,
        'accordion' => AccordionType::class,
        'accordion_item' => AccordionItemElement::class,
        'carousel' => CarouselType::class,
        'carousel_slide' => CarouselSlideElement::class,
        'table' => TableType::class,
        'table_header' => TableSectionElement::class,
        'table_body' => TableSectionElement::class,
        'table_footer' => TableSectionElement::class,
        'table_row' => TableRowElement::class,
        'table_cell' => TableCellElement::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | Configure file upload behavior for file field types.
    |
    */

    'uploads' => [
        'disk' => env('SLICK_FORMS_UPLOAD_DISK', 'public'),
        'path' => 'form-uploads',
        'max_size' => 10240, // KB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bootstrap Version
    |--------------------------------------------------------------------------
    |
    | The Bootstrap version to use for styling. Currently only Bootstrap 5
    | is supported, but this can be extended in the future.
    |
    */

    'bootstrap_version' => 5,

    /*
    |--------------------------------------------------------------------------
    | Conditional Logic
    |--------------------------------------------------------------------------
    |
    | Enable or disable conditional logic features.
    |
    */

    'conditional_logic_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Form Settings
    |--------------------------------------------------------------------------
    |
    | Default settings applied to new forms.
    |
    */

    'defaults' => [
        'is_active' => true,
        'store_ip_address' => true,
        'allow_guest_submissions' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Notifications (V2)
    |--------------------------------------------------------------------------
    |
    | Configure email notification behavior for form submissions. Emails can
    | be queued for async delivery using Laravel's queue system.
    |
    */

    'email' => [
        'enabled' => env('SLICK_FORMS_EMAIL_ENABLED', true),
        'from_address' => env('SLICK_FORMS_EMAIL_FROM', env('MAIL_FROM_ADDRESS')),
        'from_name' => env('SLICK_FORMS_EMAIL_FROM_NAME', env('MAIL_FROM_NAME')),
        'queue' => env('SLICK_FORMS_EMAIL_QUEUE', true),
        'queue_connection' => env('SLICK_FORMS_EMAIL_QUEUE_CONNECTION', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Spam Protection (V2)
    |--------------------------------------------------------------------------
    |
    | Configure spam protection methods including honeypot, reCAPTCHA v3,
    | hCaptcha, and rate limiting. Multiple methods can be enabled simultaneously.
    |
    */

    'spam' => [
        'honeypot' => [
            'enabled' => env('SLICK_FORMS_HONEYPOT_ENABLED', true),
            'field_name' => env('SLICK_FORMS_HONEYPOT_FIELD', 'website'),
            'time_threshold' => env('SLICK_FORMS_HONEYPOT_TIME', 3),
        ],
        'recaptcha' => [
            'site_key' => env('RECAPTCHA_SITE_KEY'),
            'secret_key' => env('RECAPTCHA_SECRET_KEY'),
            'score_threshold' => env('RECAPTCHA_SCORE_THRESHOLD', 0.5),
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

    /*
    |--------------------------------------------------------------------------
    | Dynamic Options (V2)
    |--------------------------------------------------------------------------
    |
    | Configure dynamic option loading from URLs and Eloquent models. Options
    | can be cached to improve performance.
    |
    */

    'dynamic_options' => [
        'cache_enabled' => env('SLICK_FORMS_OPTIONS_CACHE', true),
        'cache_ttl' => env('SLICK_FORMS_OPTIONS_CACHE_TTL', 300),
        'timeout' => env('SLICK_FORMS_OPTIONS_TIMEOUT', 10), // HTTP request timeout
    ],

    /*
    |--------------------------------------------------------------------------
    | URL Configuration (V2)
    |--------------------------------------------------------------------------
    |
    | All forms use hashid URLs (short, non-sequential, shareable).
    | Configure hashid generation and signed URL expiration settings.
    |
    */

    'urls' => [
        'hashid_salt' => env('SLICK_FORMS_HASHID_SALT', env('APP_KEY')),
        'hashid_min_length' => env('SLICK_FORMS_HASHID_MIN_LENGTH', 6),
        'signed_url_expiration' => env('SLICK_FORMS_SIGNED_URL_EXPIRATION', 24),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks (V2)
    |--------------------------------------------------------------------------
    |
    | Configure webhook delivery for form submissions. Webhooks can be queued
    | and will automatically retry failed deliveries.
    |
    */

    'webhooks' => [
        'enabled' => env('SLICK_FORMS_WEBHOOKS_ENABLED', true),
        'timeout' => env('SLICK_FORMS_WEBHOOK_TIMEOUT', 30),
        'max_retries' => env('SLICK_FORMS_WEBHOOK_RETRIES', 3),
        'retry_delay' => env('SLICK_FORMS_WEBHOOK_RETRY_DELAY', 60),
        'queue' => env('SLICK_FORMS_WEBHOOK_QUEUE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags (V2.1) - PRE-INSTALLATION DEFAULTS ONLY
    |--------------------------------------------------------------------------
    |
    | IMPORTANT: These are DEFAULT VALUES used ONLY before installation.
    |
    | After running `php artisan slick-forms:install`, the database table
    | `slick_form_features` becomes the SINGLE SOURCE OF TRUTH for which
    | features are enabled. Changing these config values after installation
    | will have NO EFFECT on feature availability.
    |
    | To enable/disable features after installation:
    | - Run: `php artisan slick-forms:install` again
    | - Or manually update the `slick_form_features` table
    |
    | WARNING: Do NOT rely on config for feature state in production. Config
    | files can be overwritten during deployments, causing inconsistent state.
    | The database is deployment-safe and persists feature selections.
    |
    */

    'features' => [
        'analytics' => env('SLICK_FORMS_FEATURE_ANALYTICS', true),
        'webhooks' => env('SLICK_FORMS_FEATURE_WEBHOOKS', true),
        'spam_logs' => env('SLICK_FORMS_FEATURE_SPAM_LOGS', true),
        'email_notifications' => env('SLICK_FORMS_FEATURE_EMAIL_NOTIFICATIONS', true),
        'versioning' => env('SLICK_FORMS_FEATURE_VERSIONING', true),
        'exports' => env('SLICK_FORMS_FEATURE_EXPORTS', true),
        'qr_codes' => env('SLICK_FORMS_FEATURE_QR_CODES', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Installation (V2.1)
    |--------------------------------------------------------------------------
    |
    | Configure the installation wizard behavior when running
    | php artisan slick-forms:install
    |
    */

    'installation' => [
        'wizard_enabled' => true,
        'auto_install_dependencies' => false,
    ],

];
