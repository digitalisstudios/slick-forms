# Form Templates Guide

## Overview

Slick Forms includes a template system that allows you to:
1. Create forms from pre-built professional templates
2. Save your own forms as reusable templates
3. Share templates across projects
4. Speed up form creation with proven designs

## Table of Contents

- [Built-In Templates](#built-in-templates)
- [Browsing Templates](#browsing-templates)
- [Using Templates](#using-templates)
- [Creating Custom Templates](#creating-custom-templates)
- [Template Categories](#template-categories)
- [Sharing Templates](#sharing-templates)
- [Template Metadata](#template-metadata)
- [Best Practices](#best-practices)
- [Troubleshooting](#troubleshooting)

---

## Built-In Templates

Slick Forms comes with 5 professionally designed templates ready to use.

### Seeding Template Forms

```bash
php artisan db:seed --class=SlickFormsTemplateSeeder
```

This command creates the following templates:

### 1. Contact Form

**Description:** Basic contact form for website inquiries

**Fields:**
- Name (Text)
- Email (Email with validation)
- Phone (Phone with US mask)
- Subject (Select dropdown)
- Message (Textarea)

**Use Cases:**
- Website contact pages
- Support request forms
- General inquiry forms

**Features:**
- Email validation
- Required fields
- Phone number formatting
- Subject categorization

---

### 2. Customer Satisfaction Survey

**Description:** Measure customer satisfaction and gather feedback

**Fields:**
- Overall Satisfaction (Star Rating 1-5)
- Product Quality (Radio buttons: Excellent, Good, Fair, Poor)
- Customer Service (Radio buttons)
- Likelihood to Recommend (Slider 0-10)
- Additional Comments (Textarea)

**Use Cases:**
- Post-purchase surveys
- Service feedback
- Customer experience measurement

**Features:**
- Star rating for quick feedback
- NPS-style recommendation scale
- Multiple rating criteria
- Open-ended feedback option

---

### 3. Product Knowledge Quiz

**Description:** Assessment form with multiple choice questions and scoring

**Fields:**
- Question 1: Product Features (Radio - multiple choice)
- Question 2: Best Practices (Radio - multiple choice)
- Question 3: Troubleshooting (Radio - multiple choice)
- Question 4: Advanced Topics (Radio - multiple choice)
- Final Score (Calculation field - auto-calculated)

**Use Cases:**
- Employee training assessments
- Customer education quizzes
- Product knowledge tests
- Certification exams

**Features:**
- Multiple choice questions
- Auto-calculated score
- Instant feedback
- Pass/fail threshold

---

### 4. Client Onboarding

**Description:** Multi-page onboarding form with organized sections

**Structure:**
- **Page 1: Personal Information**
  - Full Name
  - Email
  - Phone
  - Address

- **Page 2: Business Details**
  - Company Name
  - Industry (Select)
  - Company Size
  - Website

- **Page 3: Preferences**
  - Communication Preferences (Checkboxes)
  - Preferred Contact Method (Radio)
  - Newsletter Subscription (Switch)

**Use Cases:**
- Client intake forms
- Customer onboarding
- Account setup
- Service registration

**Features:**
- Multi-page wizard interface
- Progress bar
- Organized sections with tabs
- Per-page validation

---

### 5. Event Registration

**Description:** Comprehensive event registration form

**Fields:**
- Attendee Name
- Email
- Company/Organization
- Ticket Type (Select: General, VIP, Student)
- Number of Guests (Number field 0-5)
- Dietary Restrictions (Checkboxes: Vegetarian, Vegan, Gluten-Free, etc.)
- Special Requirements (Textarea)
- T-Shirt Size (Select)
- Emergency Contact Name
- Emergency Contact Phone

**Use Cases:**
- Conference registration
- Workshop signups
- Event attendee management
- Training session registration

**Features:**
- Ticket type selection
- Guest management
- Dietary accommodations
- Emergency contact collection

---

## Browsing Templates

### Via Web Interface

**Route:** `/slick-forms/templates`

The templates browser shows:
- Template name and description
- Preview screenshot (if available)
- Field count and complexity indicator
- Category tags
- "Preview" and "Use This Template" buttons

### Via Code

```php
use DigitalisStudios\SlickForms\Models\CustomForm;

// Get all templates
$templates = CustomForm::where('is_template', true)->get();

// Get templates by category
$contactTemplates = CustomForm::where('is_template', true)
    ->whereJsonContains('settings->template->category', 'Contact Forms')
    ->get();

// Search templates
$searchResults = CustomForm::where('is_template', true)
    ->where(function ($query) use ($searchTerm) {
        $query->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('description', 'LIKE', "%{$searchTerm}%");
    })
    ->get();
```

---

## Using Templates

### Method 1: Via UI

1. Navigate to `/slick-forms/templates`
2. Browse available templates
3. Click "Preview" to see the template structure
4. Click "Use This Template"
5. Enter a name for your new form
6. Click "Create Form"
7. The form builder opens with all template fields and layout

### Method 2: Programmatically

```php
use DigitalisStudios\SlickForms\Services\FormTemplateService;
use DigitalisStudios\SlickForms\Models\CustomForm;

$templateService = app(FormTemplateService::class);

// Find template
$template = CustomForm::where('is_template', true)
    ->where('name', 'Contact Form')
    ->first();

// Create form from template
$newForm = $templateService->createFromTemplate($template, [
    'name' => 'My Contact Form',
    'description' => 'Contact form for our website',
]);

// The new form has all fields and layout from the template
```

### Method 3: During Form Creation

```php
// In your custom form creation logic
public function createFormFromTemplate(int $templateId, array $attributes)
{
    $template = CustomForm::findOrFail($templateId);

    if (!$template->is_template) {
        throw new \Exception('Not a template');
    }

    return app(FormTemplateService::class)
        ->createFromTemplate($template, $attributes);
}
```

---

## Creating Custom Templates

### Mark Existing Form as Template

```php
$form = CustomForm::find($formId);
$form->is_template = true;
$form->save();

// The form now appears in the templates list
```

### Create Template from Scratch

```php
use DigitalisStudios\SlickForms\Models\CustomForm;

$template = CustomForm::create([
    'name' => 'Feedback Form Template',
    'description' => 'Standard feedback form for all departments',
    'is_template' => true,
    'is_active' => false, // Templates don't need to be active
    'settings' => [
        'template' => [
            'category' => 'Feedback Forms',
            'tags' => ['feedback', 'survey', 'standard'],
            'difficulty' => 'beginner',
            'estimated_time' => '5 minutes',
        ],
    ],
]);

// Add fields to template
$template->fields()->create([
    'field_type' => 'text',
    'name' => 'feedback_title',
    'label' => 'Feedback Title',
    'is_required' => true,
    'order' => 1,
]);

$template->fields()->create([
    'field_type' => 'star_rating',
    'name' => 'overall_rating',
    'label' => 'Overall Rating',
    'options' => ['max_stars' => 5],
    'order' => 2,
]);

// Add more fields...
```

### Template Best Practices

When creating templates:

1. **Use Clear Labels** - Make field labels self-explanatory
2. **Add Help Text** - Provide guidance for complex fields
3. **Set Sensible Defaults** - Pre-configure common settings
4. **Include Validation** - Add appropriate validation rules
5. **Use Placeholders** - Add helpful placeholder text
6. **Organize Layout** - Use containers, rows, columns for structure
7. **Add Metadata** - Include category, tags, difficulty level
8. **Test Thoroughly** - Ensure template works in different contexts

---

## Template Categories

Organize templates into logical categories:

### Contact Forms
- Basic contact forms
- Support request forms
- Inquiry forms
- Feedback forms

### Surveys & Feedback
- Customer satisfaction surveys
- Employee feedback forms
- Market research surveys
- Exit surveys

### Registrations
- Event registration
- Course enrollment
- Membership signup
- Account creation

### Applications
- Job applications
- School enrollment
- Program applications
- Grant applications

### Assessments
- Quizzes
- Tests
- Evaluations
- Certifications

### Onboarding
- Client onboarding
- Employee onboarding
- Vendor registration
- Partner onboarding

### Booking & Orders
- Appointment booking
- Reservation forms
- Product orders
- Service requests

### Custom Categories
Define your own categories:

```php
$template->settings = [
    'template' => [
        'category' => 'Healthcare Forms',
        'subcategory' => 'Patient Intake',
        'tags' => ['medical', 'hipaa', 'intake'],
    ],
];
```

---

## Sharing Templates

### Export Template

```php
use DigitalisStudios\SlickForms\Services\FormTemplateService;

$templateService = app(FormTemplateService::class);
$template = CustomForm::find($templateId);

// Export to JSON
$json = $templateService->exportTemplate($template);

// Save to file
file_put_contents('my-template.json', $json);

// Or return as download
return response($json)
    ->header('Content-Type', 'application/json')
    ->header('Content-Disposition', 'attachment; filename="template.json"');
```

### Import Template

```php
use DigitalisStudios\SlickForms\Services\FormTemplateService;

$templateService = app(FormTemplateService::class);

// Read JSON file
$json = file_get_contents('my-template.json');

// Import template
$template = $templateService->importTemplate($json);

// Template is now available in your templates list
```

### Share Across Projects

1. **Export from Project A:**
   ```bash
   php artisan tinker
   >>> $json = app(\DigitalisStudios\SlickForms\Services\FormTemplateService::class)
           ->exportTemplate(\DigitalisStudios\SlickForms\Models\CustomForm::find(5));
   >>> file_put_contents('contact-form-template.json', $json);
   ```

2. **Import to Project B:**
   ```bash
   php artisan tinker
   >>> $json = file_get_contents('contact-form-template.json');
   >>> app(\DigitalisStudios\SlickForms\Services\FormTemplateService::class)
           ->importTemplate($json);
   ```

---

## Template Metadata

### Complete Metadata Structure

```php
$template->settings = [
    'template' => [
        // Basic Info
        'category' => 'Contact Forms',
        'subcategory' => 'General Inquiry',

        // Tags for searching/filtering
        'tags' => ['contact', 'basic', 'website', 'inquiry'],

        // Difficulty level
        'difficulty' => 'beginner', // beginner, intermediate, advanced

        // Estimated setup time
        'estimated_time' => '5 minutes',

        // Preview image
        'preview_image' => 'templates/contact-form-preview.png',

        // Author info
        'author' => 'Your Company',
        'author_url' => 'https://example.com',

        // Version
        'version' => '1.0.0',
        'created_date' => '2025-01-15',

        // Requirements
        'requires' => [
            'multi_page' => false,
            'conditional_logic' => false,
            'calculation_fields' => false,
        ],

        // Usage statistics
        'usage_count' => 0,
        'popularity' => 0,

        // Additional metadata
        'field_count' => 5,
        'estimated_completion_time' => '2 minutes',
        'language' => 'en',
    ],
];
```

### Accessing Metadata

```php
$category = $template->settings['template']['category'] ?? 'Uncategorized';
$tags = $template->settings['template']['tags'] ?? [];
$difficulty = $template->settings['template']['difficulty'] ?? 'intermediate';
```

---

## Template Structure

### JSON Export Format

When exported, templates include:

```json
{
  "name": "Contact Form Template",
  "description": "Basic contact form",
  "is_template": true,
  "settings": {
    "template": {
      "category": "Contact Forms",
      "tags": ["contact", "basic"]
    }
  },
  "fields": [
    {
      "field_type": "text",
      "name": "name",
      "label": "Full Name",
      "is_required": true,
      "order": 1,
      "validation_rules": ["required", "string", "max:255"],
      "options": {
        "placeholder": "Enter your full name"
      }
    },
    {
      "field_type": "email",
      "name": "email",
      "label": "Email Address",
      "is_required": true,
      "order": 2,
      "validation_rules": ["required", "email"],
      "options": {
        "placeholder": "you@example.com"
      }
    }
  ],
  "layout_elements": [
    {
      "element_type": "container",
      "order": 1,
      "settings": {
        "fluid": false,
        "max_width": "lg"
      },
      "children": []
    }
  ],
  "pages": [
    {
      "title": "Contact Information",
      "order": 1,
      "settings": {
        "show_in_progress": true
      }
    }
  ]
}
```

---

## Template Maintenance

### Updating Templates

```php
$template = CustomForm::where('is_template', true)
    ->where('name', 'Contact Form')
    ->first();

// Update fields
$emailField = $template->fields()->where('name', 'email')->first();
$emailField->update([
    'help_text' => 'We will never share your email',
]);

// Update metadata
$settings = $template->settings;
$settings['template']['version'] = '1.1.0';
$settings['template']['updated_date'] = now()->toDateString();
$template->settings = $settings;
$template->save();
```

### Versioning Templates

```php
// Create new version of template
$newVersion = $templateService->createFromTemplate($oldTemplate, [
    'name' => $oldTemplate->name . ' v2',
]);

// Update version metadata
$settings = $newVersion->settings;
$settings['template']['version'] = '2.0.0';
$settings['template']['previous_version'] = $oldTemplate->id;
$newVersion->settings = $settings;
$newVersion->save();
```

### Deleting Templates

```php
$template = CustomForm::find($templateId);
$template->delete();

// Forms created from this template are NOT deleted
// They are independent copies
```

---

## Best Practices

### 1. Clear Naming
Use descriptive template names that indicate purpose:
- ✅ "Customer Feedback Survey"
- ✅ "Employee Onboarding Form"
- ❌ "Template 1"
- ❌ "My Form"

### 2. Complete Documentation
Add comprehensive descriptions:

```php
$template->description = "Comprehensive customer satisfaction survey with NPS score, feature ratings, and open feedback. Includes conditional follow-up questions based on satisfaction level.";
```

### 3. Appropriate Defaults
Pre-configure common settings:

```php
// Set sensible validation rules
$field->validation_rules = ['required', 'email', 'max:255'];

// Add helpful placeholders
$field->options['placeholder'] = 'you@example.com';

// Enable common features
$field->options['show_character_count'] = true;
```

### 4. Minimal Required Fields
Don't over-require fields in templates:

```php
// Only require truly essential fields
$field->is_required = ($field->name === 'email' || $field->name === 'name');
```

### 5. Responsive Design
Ensure layout works on all devices:

```php
// Use responsive column widths
$column->settings = [
    'xs' => 12,  // Full width on mobile
    'md' => 6,   // Half width on tablet
    'lg' => 4,   // Third width on desktop
];
```

### 6. Add Help Text
Provide guidance for complex fields:

```php
$field->help_text = "Enter your phone number including area code. Format: (555) 123-4567";
```

### 7. Include Examples
Use placeholder text to show expected format:

```php
$field->options['placeholder'] = "e.g., acme-corp.com";
```

### 8. Test Thoroughly
Before marking as template:
- Fill out the entire form
- Test validation rules
- Verify conditional logic
- Check mobile responsiveness
- Review confirmation messages

---

## Common Use Cases

### Creating Department-Specific Templates

```php
// Marketing team template
$marketingTemplate = CustomForm::create([
    'name' => 'Marketing Campaign Feedback',
    'is_template' => true,
    'settings' => [
        'template' => [
            'category' => 'Marketing',
            'department' => 'Marketing',
            'visibility' => 'department', // Only visible to marketing team
        ],
    ],
]);
```

### Template Inheritance

```php
// Create specialized template from base template
$baseTemplate = CustomForm::where('name', 'Base Contact Form')->first();
$specializedForm = $templateService->createFromTemplate($baseTemplate, [
    'name' => 'Sales Inquiry Form',
]);

// Add sales-specific fields
$specializedForm->fields()->create([
    'field_type' => 'select',
    'name' => 'product_interest',
    'label' => 'Product Interest',
    'options' => [
        'values' => [
            ['label' => 'Product A', 'value' => 'product_a'],
            ['label' => 'Product B', 'value' => 'product_b'],
        ],
    ],
]);

// Mark as template
$specializedForm->is_template = true;
$specializedForm->save();
```

### Multi-Language Templates

```php
// English version
$enTemplate = CustomForm::create([
    'name' => 'Contact Form (English)',
    'is_template' => true,
    'settings' => [
        'template' => [
            'language' => 'en',
            'translations' => ['es', 'fr'],
        ],
    ],
]);

// Spanish version
$esTemplate = CustomForm::create([
    'name' => 'Formulario de Contacto (Español)',
    'is_template' => true,
    'settings' => [
        'template' => [
            'language' => 'es',
            'original_template_id' => $enTemplate->id,
        ],
    ],
]);
```

---

## Troubleshooting

### Template Not Appearing in List

**Possible Causes:**
1. `is_template` flag not set to `true`
2. Template soft-deleted
3. Missing required fields

**Solutions:**
```php
// Check template flag
$form = CustomForm::find($formId);
dd($form->is_template);

// Check if soft-deleted
$template = CustomForm::withTrashed()->find($formId);
if ($template->trashed()) {
    $template->restore();
}

// Ensure has fields
dd($form->fields()->count());
```

### Template Import Fails

**Possible Causes:**
1. Invalid JSON format
2. Field types don't exist in target installation
3. Layout elements don't exist
4. Circular references in conditional logic

**Solutions:**
```php
try {
    $template = $templateService->importTemplate($json);
} catch (\Exception $e) {
    // Log error
    \Log::error('Template import failed: ' . $e->getMessage());

    // Check JSON validity
    json_decode($json);
    if (json_last_error() !== JSON_ERROR_NONE) {
        dd('Invalid JSON: ' . json_last_error_msg());
    }
}
```

### Created Form Missing Fields

**Possible Cause:** Relationship not eager loaded during copy

**Solution:**
```php
// Ensure fields are loaded
$template = CustomForm::with(['fields', 'layoutElements'])->find($templateId);
$newForm = $templateService->createFromTemplate($template, $attributes);
```

---

## API Reference

### FormTemplateService Methods

```php
// Create form from template
createFromTemplate(CustomForm $template, array $attributes): CustomForm

// Export template to JSON
exportTemplate(CustomForm $template): string

// Import template from JSON
importTemplate(string $json): CustomForm

// Duplicate template
duplicateTemplate(CustomForm $template, string $newName): CustomForm

// Get template usage statistics
getTemplateUsageStats(CustomForm $template): array
```

---

## Related Documentation

- [Form Builder Guide](CUSTOM_FIELD_TYPES.md)
- [Working with Submissions](WORKING_WITH_SUBMISSIONS.md)
- [Multi-Page Forms](../MULTI_PAGE_FORMS.md)
- [Field Types Reference](FIELD_TYPES_REFERENCE.md)

---

**Need Help?** Visit the [Slick Forms repository](https://bitbucket.org/bmooredigitalisstudios/slick-forms) for support.
