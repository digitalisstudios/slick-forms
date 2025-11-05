# Success Screens

**Slick Forms v2.0+** - Complete guide for configuring form success screens

---

## Table of Contents

- [Overview](#overview)
- [Success Action Types](#success-action-types)
- [Success Messages](#success-messages)
- [Redirects](#redirects)
- [Variable Substitution](#variable-substitution)
- [Conditional Redirects](#conditional-redirects)
- [Download Options](#download-options)
- [Edit Submission Links](#edit-submission-links)
- [Configuration Examples](#configuration-examples)
- [Best Practices](#best-practices)
- [Troubleshooting](#troubleshooting)

---

## Overview

Success screens control what users see after successfully submitting a form. Slick Forms offers three success action types:

1. **Show Message** - Display a custom thank you message
2. **Redirect to URL** - Immediately redirect to another page
3. **Show Message, Then Redirect** - Show message first, then redirect after delay

### Key Features

- ✅ Rich HTML success messages with WYSIWYG editor
- ✅ Variable substitution (merge submission data into messages)
- ✅ Conditional redirects based on form values
- ✅ PDF and CSV download buttons
- ✅ Edit submission links with expiration
- ✅ Hide sensitive fields from success screen
- ✅ Pass submission data via URL parameters

---

## Success Action Types

### 1. Show Message

Display a custom success message after form submission.

**Configuration Location**: Form Builder → Settings → Success Screen

```json
{
  "success_action": "message",
  "message": {
    "title": "Thank you!",
    "body": "<p>We've received your submission and will get back to you soon.</p>",
    "show_submission_data": true,
    "hidden_fields": [5, 8]
  }
}
```

**When to Use**:
- Contact forms where you want to confirm receipt
- Surveys where you want to thank participants
- Registration forms with next steps

---

### 2. Redirect to URL

Immediately redirect users to another page after submission.

```json
{
  "success_action": "redirect",
  "redirect": {
    "url": "https://example.com/thank-you",
    "pass_submission_id": true
  }
}
```

**When to Use**:
- Checkout flows that need to go to payment
- Multi-step processes with dedicated thank you pages
- Integration with external systems

---

### 3. Show Message, Then Redirect

Show a success message briefly, then redirect after a delay.

```json
{
  "success_action": "message_then_redirect",
  "message": {
    "title": "Success!",
    "body": "<p>Redirecting to your dashboard...</p>"
  },
  "redirect": {
    "url": "https://example.com/dashboard",
    "delay_seconds": 5
  }
}
```

**When to Use**:
- Give users time to read confirmation before redirect
- Show important next steps before moving on
- Provide download links before redirecting

---

## Success Messages

### Message Title

Short, prominent headline shown at the top of success screen.

**Configuration**:
```php
// In Form Builder
$form->settings['success']['message']['title'] = 'Thank you!';
```

**Examples**:
- "Thank you!"
- "Submission Received"
- "Welcome Aboard!"
- "Registration Complete"

---

### Message Body

Rich HTML content with WYSIWYG editor (Quill).

**Configuration**:
```php
$form->settings['success']['message']['body'] = '<p>Your submission has been received...</p>';
```

**Formatting Options**:
- **Bold**, *Italic*, <u>Underline</u>
- Hyperlinks
- Ordered and unordered lists
- Line breaks and paragraphs

**Example**:
```html
<p><strong>Thank you for contacting us!</strong></p>
<p>We've received your inquiry and will respond within 24 hours.</p>
<p>What happens next:</p>
<ol>
  <li>Our team will review your submission</li>
  <li>You'll receive a confirmation email at {{email}}</li>
  <li>A team member will reach out within 1 business day</li>
</ol>
```

---

### Show Submission Data

Display the submitted form data on the success screen.

**Configuration**:
```php
$form->settings['success']['message']['show_submission_data'] = true;
```

**Output Example**:
```
Your Submission:
─────────────────
Full Name: John Doe
Email: john@example.com
Message: Looking forward to working with you!
```

---

### Hide Sensitive Fields

Exclude specific fields from success screen display (e.g., passwords, credit cards).

**Configuration**:
```php
// Hide fields by ID
$form->settings['success']['message']['hidden_fields'] = [5, 8, 12];
```

**Common Fields to Hide**:
- Password fields
- Credit card numbers
- Social security numbers
- Private comments/notes
- Internal tracking fields

---

## Redirects

### Basic Redirect

Redirect to a static URL immediately after submission.

**Configuration**:
```php
$form->settings['success']['redirect']['url'] = 'https://example.com/thank-you';
```

---

### Redirect with Submission ID

Append submission ID as query parameter for tracking.

**Configuration**:
```php
$form->settings['success']['redirect']['pass_submission_id'] = true;
```

**Result**:
```
https://example.com/thank-you?submission_id=12345
```

**Use Case**:
```php
// In your thank-you page controller
public function thankYou(Request $request)
{
    $submissionId = $request->query('submission_id');
    $submission = CustomFormSubmission::find($submissionId);

    return view('thank-you', compact('submission'));
}
```

---

### Delayed Redirect

Show message for a specified duration before redirecting.

**Configuration**:
```php
$form->settings['success']['success_action'] = 'message_then_redirect';
$form->settings['success']['redirect']['delay_seconds'] = 5; // 1-60 seconds
```

**Visual Countdown**:
The success screen automatically shows a countdown timer:
```
Thank you for your submission!
Redirecting in 5 seconds...
```

---

## Variable Substitution

Insert submission data into success messages and redirect URLs.

### Available Variables

```php
// Submission metadata
{{submission.id}}           // 12345
{{submission.created_at}}   // 2025-01-04 10:30:00

// Any form field by name
{{field_name}}              // Value submitted for that field
{{email}}                   // user@example.com
{{full_name}}               // John Doe
{{order_number}}            // ORD-2025-001
```

---

### Message Variable Example

**Configuration**:
```html
<p>Thank you, <strong>{{full_name}}</strong>!</p>
<p>We've sent a confirmation email to {{email}}.</p>
<p>Your submission ID is: {{submission.id}}</p>
```

**Output** (for John Doe, john@example.com):
```html
<p>Thank you, <strong>John Doe</strong>!</p>
<p>We've sent a confirmation email to john@example.com.</p>
<p>Your submission ID is: 12345</p>
```

---

### Redirect URL Variable Example

**Configuration**:
```php
$form->settings['success']['redirect']['url'] =
    'https://example.com/order/{{order_number}}?email={{email}}';
```

**Result** (for order ORD-2025-001, john@example.com):
```
https://example.com/order/ORD-2025-001?email=john@example.com
```

---

### Variable Safety

All variables are automatically escaped to prevent XSS attacks:

```php
// User submits: <script>alert('XSS')</script>
// Variable output: &lt;script&gt;alert('XSS')&lt;/script&gt;
```

---

## Conditional Redirects

Redirect to different URLs based on form values.

### Use Cases

- **Product selection**: Redirect to different checkout pages
- **User type**: Send businesses to B2B flow, individuals to B2C
- **Geographic routing**: Redirect to regional sites
- **A/B testing**: Redirect to different landing pages

---

### Configuration

Conditional redirects are evaluated in priority order (highest first).

```php
$form->settings['success']['conditional_redirects'] = [
    [
        'priority' => 10,
        'url' => 'https://example.com/enterprise-thank-you',
        'conditions' => [
            ['field' => 'account_type', 'operator' => 'equals', 'value' => 'enterprise']
        ]
    ],
    [
        'priority' => 5,
        'url' => 'https://example.com/business-thank-you',
        'conditions' => [
            ['field' => 'account_type', 'operator' => 'equals', 'value' => 'business']
        ]
    ],
    // Fallback if no conditions match
    [
        'priority' => 1,
        'url' => 'https://example.com/personal-thank-you',
        'conditions' => []
    ]
];
```

---

### Supported Operators

Same operators as conditional field logic:

- `equals`, `not_equals`
- `contains`, `not_contains`
- `greater_than`, `less_than`
- `in`, `not_in` (for multi-select)
- `is_empty`, `is_not_empty`

---

### Example: Product-Based Redirect

```php
// Form has "product" select field with options: "widget", "gadget", "gizmo"
$form->settings['success']['conditional_redirects'] = [
    [
        'priority' => 10,
        'url' => 'https://example.com/widget-upsell',
        'conditions' => [
            ['field' => 'product', 'operator' => 'equals', 'value' => 'widget']
        ]
    ],
    [
        'priority' => 10,
        'url' => 'https://example.com/gadget-upsell',
        'conditions' => [
            ['field' => 'product', 'operator' => 'equals', 'value' => 'gadget']
        ]
    ],
    [
        'priority' => 1,
        'url' => 'https://example.com/general-thank-you',
        'conditions' => []
    ]
];
```

---

### Example: Regional Redirect

```php
// Redirect to regional sites based on country selection
$form->settings['success']['conditional_redirects'] = [
    [
        'priority' => 10,
        'url' => 'https://uk.example.com/thank-you',
        'conditions' => [
            ['field' => 'country', 'operator' => 'in', 'value' => ['UK', 'Ireland']]
        ]
    ],
    [
        'priority' => 10,
        'url' => 'https://au.example.com/thank-you',
        'conditions' => [
            ['field' => 'country', 'operator' => 'in', 'value' => ['Australia', 'New Zealand']]
        ]
    ],
    [
        'priority' => 1,
        'url' => 'https://example.com/thank-you',
        'conditions' => []
    ]
];
```

---

## Download Options

Offer users the ability to download their submission as PDF or CSV.

### PDF Download

Generate a formatted PDF of the submission.

**Configuration**:
```php
$form->settings['success']['downloads']['pdf']['enabled'] = true;
$form->settings['success']['downloads']['pdf']['button_text'] = 'Download PDF';
```

**Features**:
- Professional formatting
- Includes form name and submission date
- Shows all field labels and values
- Automatically generated filename: `submission-12345.pdf`

---

### CSV Download

Export submission data as CSV for spreadsheet import.

**Configuration**:
```php
$form->settings['success']['downloads']['csv']['enabled'] = true;
$form->settings['success']['downloads']['csv']['button_text'] = 'Download CSV';
```

**CSV Format**:
```csv
Field,Value
Full Name,John Doe
Email,john@example.com
Message,Looking forward to working with you
```

---

### Example: Both Downloads Enabled

```blade
{{-- Success screen with download buttons --}}
<div class="success-downloads mt-4">
    <h5>Download Your Submission</h5>
    <div class="btn-group" role="group">
        <a href="/submissions/{{$submission->id}}/pdf" class="btn btn-primary">
            <i class="bi bi-file-pdf"></i> Download PDF
        </a>
        <a href="/submissions/{{$submission->id}}/csv" class="btn btn-secondary">
            <i class="bi bi-file-spreadsheet"></i> Download CSV
        </a>
    </div>
</div>
```

---

## Edit Submission Links

Allow users to edit their submission within a time limit.

### Configuration

**Enable Edit Links**:
```php
$form->settings['success']['edit_link']['enabled'] = true;
$form->settings['success']['edit_link']['text'] = 'Edit Your Submission';
$form->settings['success']['edit_link']['expiration_hours'] = 24; // 1-168 hours
```

---

### How It Works

1. User submits form
2. Success screen shows "Edit Your Submission" link with signed URL
3. Link remains valid for configured duration (e.g., 24 hours)
4. Clicking link pre-fills form with submitted data
5. User makes changes and resubmits
6. Original submission is updated (not duplicated)

---

### Example Output

```blade
<div class="success-message">
    <h3>Thank you for registering!</h3>
    <p>Your registration is complete.</p>

    <div class="alert alert-info mt-4">
        <strong>Need to make changes?</strong><br>
        <a href="/forms/abc123/edit/xyz789?expires=1704398400&signature=...">
            Edit Your Submission
        </a>
        <small class="d-block text-muted">This link expires in 24 hours</small>
    </div>
</div>
```

---

### Security Features

- **Signed URLs**: Tamper-proof links with signature verification
- **Expiration**: Links automatically expire after configured time
- **One-time use option**: Optional single-use edit links
- **IP validation**: Optional IP address verification

---

## Configuration Examples

### Example 1: Simple Contact Form

Show a thank you message with submission data.

```php
use DigitalisStudios\SlickForms\Models\CustomForm;

$form = CustomForm::find(1);

$form->settings = array_merge($form->settings, [
    'success' => [
        'success_action' => 'message',
        'message' => [
            'title' => 'Thank You!',
            'body' => '<p>We appreciate you reaching out. Our team will respond within 24 hours.</p>',
            'show_submission_data' => true,
            'hidden_fields' => [], // Show all fields
        ],
    ],
]);

$form->save();
```

---

### Example 2: E-commerce Order Confirmation

Show message, then redirect to order tracking.

```php
$form->settings = array_merge($form->settings, [
    'success' => [
        'success_action' => 'message_then_redirect',
        'message' => [
            'title' => 'Order Confirmed!',
            'body' => '
                <p>Thank you for your order, <strong>{{customer_name}}</strong>!</p>
                <p>Your order number is: <strong>{{order_number}}</strong></p>
                <p>A confirmation email has been sent to {{email}}.</p>
                <p>Redirecting to order tracking...</p>
            ',
            'show_submission_data' => false,
        ],
        'redirect' => [
            'url' => 'https://example.com/orders/{{order_number}}',
            'delay_seconds' => 5,
            'pass_submission_id' => true,
        ],
        'downloads' => [
            'pdf' => [
                'enabled' => true,
                'button_text' => 'Download Order Receipt',
            ],
        ],
    ],
]);

$form->save();
```

---

### Example 3: Survey with Conditional Redirect

Redirect to different pages based on satisfaction rating.

```php
$form->settings = array_merge($form->settings, [
    'success' => [
        'success_action' => 'redirect',
        'conditional_redirects' => [
            // Happy customers → testimonial request
            [
                'priority' => 10,
                'url' => 'https://example.com/testimonial-request',
                'conditions' => [
                    ['field' => 'satisfaction', 'operator' => 'greater_than', 'value' => 7]
                ],
            ],
            // Unhappy customers → support
            [
                'priority' => 9,
                'url' => 'https://example.com/customer-support',
                'conditions' => [
                    ['field' => 'satisfaction', 'operator' => 'less_than', 'value' => 5]
                ],
            ],
            // Neutral → generic thank you
            [
                'priority' => 1,
                'url' => 'https://example.com/thank-you',
                'conditions' => [],
            ],
        ],
        'redirect' => [
            'pass_submission_id' => true,
        ],
    ],
]);

$form->save();
```

---

### Example 4: Event Registration with Edit Link

Allow registrants to update their information.

```php
$form->settings = array_merge($form->settings, [
    'success' => [
        'success_action' => 'message',
        'message' => [
            'title' => 'Registration Confirmed!',
            'body' => '
                <p>Thank you for registering, {{first_name}}!</p>
                <p><strong>Event:</strong> Annual Conference 2025</p>
                <p><strong>Date:</strong> March 15, 2025</p>
                <p><strong>Location:</strong> {{event_location}}</p>
                <p>A confirmation email has been sent to {{email}}.</p>
            ',
            'show_submission_data' => true,
            'hidden_fields' => [8], // Hide internal notes field
        ],
        'edit_link' => [
            'enabled' => true,
            'text' => 'Need to update your registration?',
            'expiration_hours' => 72, // 3 days
        ],
        'downloads' => [
            'pdf' => [
                'enabled' => true,
                'button_text' => 'Download Registration Confirmation',
            ],
        ],
    ],
]);

$form->save();
```

---

## Best Practices

### 1. Clear Next Steps

Always tell users what happens next:

```html
<p><strong>What happens next:</strong></p>
<ol>
  <li>You'll receive a confirmation email within 5 minutes</li>
  <li>Our team will review your application within 2 business days</li>
  <li>You'll be contacted via email with next steps</li>
</ol>
```

---

### 2. Set Expectations

Include response time estimates:

```html
<p>We typically respond to inquiries within 24 hours during business days.</p>
```

---

### 3. Provide Contact Info

Give users a way to reach out if needed:

```html
<p>Questions? Email us at <a href="mailto:support@example.com">support@example.com</a></p>
```

---

### 4. Use Personalization

Make messages feel personal with variables:

```html
<!-- Generic -->
<p>Thank you for your submission!</p>

<!-- Personalized -->
<p>Thank you, {{first_name}}! We've received your {{inquiry_type}} inquiry.</p>
```

---

### 5. Consider Mobile Users

Keep messages concise for mobile screens:

```html
<!-- Too long -->
<p>Thank you so much for taking the time to fill out our comprehensive form...</p>

<!-- Better -->
<p>Thanks, {{first_name}}! We'll be in touch soon.</p>
```

---

### 6. Test Redirects

Always test conditional redirects with sample data:

```php
// Test script
$testData = [
    ['account_type' => 'enterprise'], // Should go to /enterprise-thank-you
    ['account_type' => 'business'],   // Should go to /business-thank-you
    ['account_type' => 'personal'],   // Should go to /personal-thank-you
];

foreach ($testData as $data) {
    $url = $service->evaluateConditionalRedirect($form, $data);
    echo "Data: " . json_encode($data) . " → URL: $url\n";
}
```

---

### 7. Hide Sensitive Data

Never show sensitive data on success screens:

```php
$form->settings['success']['message']['hidden_fields'] = [
    $passwordFieldId,
    $creditCardFieldId,
    $ssnFieldId,
];
```

---

## Troubleshooting

### Success Screen Not Showing

**Problem**: Form redirects immediately, success screen never appears

**Cause**: Success action set to `redirect` instead of `message`

**Solution**:
```php
// Check current setting
dump($form->settings['success']['success_action']);

// Change to show message
$form->settings['success']['success_action'] = 'message';
$form->save();
```

---

### Variables Not Substituting

**Problem**: Variables show as `{{field_name}}` instead of actual values

**Cause**: Field name doesn't match variable name

**Solution**:
```php
// Check field names
foreach ($form->fields as $field) {
    echo "Field name: {$field->name}\n";
}

// Use correct field name in variable
// If field name is "email_address" not "email":
{{email_address}}  // ✅ Correct
{{email}}          // ❌ Won't work
```

---

### Conditional Redirect Not Working

**Problem**: All submissions go to default URL regardless of conditions

**Cause**: Conditions not evaluating correctly

**Debug**:
```php
$submission = CustomFormSubmission::find($submissionId);
$fieldValues = $submission->fieldValues->pluck('value', 'field.name')->toArray();

// Check what values are being compared
dump($fieldValues);

// Verify conditions match actual data
$conditions = $form->settings['success']['conditional_redirects'][0]['conditions'];
dump($conditions);
```

---

### Edit Link Expired

**Problem**: Edit link shows "Link has expired" immediately

**Cause**: System time misconfiguration or expiration too short

**Solution**:
```php
// Check server time
echo now()->toDateTimeString();

// Increase expiration
$form->settings['success']['edit_link']['expiration_hours'] = 168; // 7 days
$form->save();
```

---

### PDF Download Fails

**Problem**: PDF download returns 500 error

**Cause**: Missing PDF library or template issues

**Solution**:
```bash
# Install dompdf if missing
composer require barryvdh/laravel-dompdf

# Check logs
tail -f storage/logs/laravel.log

# Test PDF generation
$submission = CustomFormSubmission::find(1);
$pdf = Pdf::loadView('slick-forms::emails.submission-pdf', compact('submission'));
$pdf->save(storage_path('test.pdf'));
```

---

## Related Documentation

- [Variable Substitution](EMAIL_NOTIFICATIONS.md#variable-substitution) - Same syntax used in email templates
- [Conditional Logic](CONDITIONAL_LOGIC.md) - Condition operators and evaluation
- [Form Builder](FORM_BUILDER.md) - Configuring success screens in builder
- [URL Obfuscation](QR_CODES.md) - Signed URLs for edit links

---

## Summary

Success screens are the last touchpoint with form users. Make them count:

- ✅ Choose appropriate success action (message, redirect, or both)
- ✅ Personalize messages with variable substitution
- ✅ Use conditional redirects for smart routing
- ✅ Offer downloads for record-keeping
- ✅ Enable edit links for user convenience
- ✅ Hide sensitive fields from display
- ✅ Set clear expectations for next steps

A well-configured success screen provides closure, builds trust, and guides users toward the next action in your workflow.
