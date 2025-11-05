# Real-World Form Examples (V2)

**Production-ready form examples you can use immediately**

This guide provides complete, working examples of common form types built with Slick Forms V2. Each example includes full code with correct V2 formats, key features, and customization ideas.

> **Note:** All examples use V2-compatible syntax. Key differences from V1:
> - Conditional logic uses `target_field_id` (not `target_field`)
> - Conditional logic uses `match` (not `logic`)
> - Layout elements use `settings` (not `options`)
> - Column widths go in layout element `settings` (not field `options`)

---

## Table of Contents

- [Simple Contact Form](#1-simple-contact-form)
- [Lead Capture with Conditional Logic](#2-lead-capture-with-conditional-logic)
- [Multi-Step Registration with Tabs](#3-multi-step-registration-with-tabs)
- [Job Application Form](#4-job-application-form)
- [Event Registration with Pricing](#5-event-registration-with-pricing)
- [Customer Satisfaction Survey](#6-customer-satisfaction-survey)
- [Product Order Form with Repeater](#7-product-order-form-with-repeater)
- [Support Ticket Form](#8-support-ticket-form)
- [Newsletter Signup with Preferences](#9-newsletter-signup-with-preferences)
- [Booking Form with Date Range](#10-booking-form-with-date-range)

---

## 1. Simple Contact Form

**Use Case:** Basic contact form for website visitors to get in touch.

**Key Features:**
- Email validation
- Phone field with formatting
- Textarea for message
- 2-column responsive layout
- V2 layout structure with proper settings

**Code:**

```php
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use DigitalisStudios\SlickForms\Models\CustomFormField;

$form = CustomForm::create([
    'name' => 'Contact Us',
    'description' => 'Get in touch with our team',
    'is_active' => true,
    'settings' => [
        'submit_button_text' => 'Send Message',
        'success_message' => 'Thank you! We\'ll respond within 24 hours.',
    ],
]);

// Container (use 'settings' not 'options' in V2)
$container = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'element_type' => 'container',
    'order' => 1,
    'settings' => ['fluid' => false],  // ✅ V2: settings, not options
]);

// Row for name fields
$row1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'row',
    'order' => 1,
    'settings' => ['gutter' => 'g-3'],  // ✅ V2: settings
]);

// First Name Column (widths in settings, not options)
$col1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row1->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '6'],  // ✅ V2: direct in settings
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col1->id,
    'field_type' => 'text',
    'name' => 'first_name',
    'label' => 'First Name',
    'placeholder' => 'John',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'max:50'],
    'order' => 1,
]);

// Last Name Column
$col2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row1->id,
    'element_type' => 'column',
    'order' => 2,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col2->id,
    'field_type' => 'text',
    'name' => 'last_name',
    'label' => 'Last Name',
    'placeholder' => 'Doe',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'max:50'],
    'order' => 2,
]);

// Row 2: Email and Phone
$row2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'row',
    'order' => 2,
    'settings' => ['gutter' => 'g-3'],
]);

$col3 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row2->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col3->id,
    'field_type' => 'email',
    'name' => 'email',
    'label' => 'Email Address',
    'placeholder' => 'john@example.com',
    'is_required' => true,
    'validation_rules' => ['required', 'email'],
    'order' => 3,
]);

$col4 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row2->id,
    'element_type' => 'column',
    'order' => 2,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col4->id,
    'field_type' => 'phone',
    'name' => 'phone',
    'label' => 'Phone Number',
    'placeholder' => '(555) 123-4567',
    'is_required' => false,
    'validation_rules' => ['string', 'max:20'],
    'options' => [
        'default_country' => 'US',
        'show_country_selector' => true,
    ],
    'order' => 4,
]);

// Row 3: Message (full width)
$row3 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'row',
    'order' => 3,
]);

$col5 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row3->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '12'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col5->id,
    'field_type' => 'textarea',
    'name' => 'message',
    'label' => 'Message',
    'placeholder' => 'How can we help you?',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'min:10'],
    'options' => ['rows' => 5],
    'order' => 5,
]);
```

**Customization Ideas:**
- Add a subject dropdown field
- Include a file attachment field for documents
- Add spam protection with honeypot or reCAPTCHA
- Set up email notifications when form is submitted

---

## 2. Lead Capture with Conditional Logic

**Use Case:** Marketing form that shows different fields based on user type (B2B vs B2C).

**Key Features:**
- **V2 Conditional Logic** with correct `target_field_id` format
- Show company fields only for business accounts
- Budget range slider
- Interest checkboxes
- Conditional validation

**Code:**

```php
$form = CustomForm::create([
    'name' => 'Get a Quote',
    'description' => 'Tell us about your project',
    'is_active' => true,
    'settings' => [
        'submit_button_text' => 'Request Quote',
        'success_message' => 'Thanks! We\'ll send you a quote within 24 hours.',
    ],
]);

$container = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'element_type' => 'container',
    'order' => 1,
    'settings' => ['fluid' => false],
]);

// Account Type Radio (trigger field for conditional logic)
$accountTypeField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'radio',
    'name' => 'account_type',
    'label' => 'I am a...',
    'is_required' => true,
    'validation_rules' => ['required', 'in:individual,business'],
    'options' => [
        'values' => [
            ['label' => 'Individual', 'value' => 'individual', 'default' => false],
            ['label' => 'Business', 'value' => 'business', 'default' => false],
        ],
        'layout' => 'inline',
    ],
    'order' => 1,
]);

// Company Name (conditional - only show for business)
// ✅ V2 Format: Uses target_field_id, match, and top-level action
$companyNameField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'text',
    'name' => 'company_name',
    'label' => 'Company Name',
    'placeholder' => 'Acme Corporation',
    'is_required' => false,
    'validation_rules' => ['string', 'max:100'],
    'conditional_logic' => [
        'action' => 'show',  // ✅ V2: Top level, not nested under 'visibility'
        'match' => 'all',    // ✅ V2: 'match' not 'logic'
        'conditions' => [
            [
                'target_field_id' => $accountTypeField->id,  // ✅ V2: Use field ID, not name
                'operator' => 'equals',
                'value' => 'business',
            ],
        ],
        // ✅ V2: Conditional validation - make required when visible
        'conditional_validation' => [
            [
                'rule' => 'required',
                'match' => 'all',
                'conditions' => [
                    [
                        'target_field_id' => $accountTypeField->id,
                        'operator' => 'equals',
                        'value' => 'business',
                    ],
                ],
            ],
        ],
    ],
    'order' => 2,
]);

// Industry Select (conditional - only show for business)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'select',
    'name' => 'industry',
    'label' => 'Industry',
    'is_required' => false,
    'options' => [
        'values' => [
            ['label' => 'Technology', 'value' => 'tech', 'default' => false],
            ['label' => 'Healthcare', 'value' => 'healthcare', 'default' => false],
            ['label' => 'Finance', 'value' => 'finance', 'default' => false],
            ['label' => 'Retail', 'value' => 'retail', 'default' => false],
            ['label' => 'Other', 'value' => 'other', 'default' => false],
        ],
        'searchable' => true,
        'placeholder' => 'Select your industry',
    ],
    'conditional_logic' => [
        'action' => 'show',
        'match' => 'all',
        'conditions' => [
            [
                'target_field_id' => $accountTypeField->id,
                'operator' => 'equals',
                'value' => 'business',
            ],
        ],
    ],
    'order' => 3,
]);

// Budget Range Slider
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'range',
    'name' => 'budget_range',
    'label' => 'Budget Range ($)',
    'is_required' => true,
    'validation_rules' => ['required'],
    'options' => [
        'min' => 0,
        'max' => 50000,
        'step' => 1000,
        'show_values' => true,
        'show_labels' => true,
    ],
    'order' => 4,
]);

// Services Checkboxes
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'checkbox',
    'name' => 'services',
    'label' => 'Services You\'re Interested In',
    'is_required' => true,
    'validation_rules' => ['required', 'array', 'min:1'],
    'options' => [
        'values' => [
            ['label' => 'Web Development', 'value' => 'web_dev', 'default' => false],
            ['label' => 'Mobile Apps', 'value' => 'mobile', 'default' => false],
            ['label' => 'UI/UX Design', 'value' => 'design', 'default' => false],
            ['label' => 'Consulting', 'value' => 'consulting', 'default' => false],
        ],
        'layout' => 'stacked',
    ],
    'order' => 5,
]);

// Email
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'email',
    'name' => 'email',
    'label' => 'Email Address',
    'is_required' => true,
    'validation_rules' => ['required', 'email'],
    'order' => 6,
]);

// Phone
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'phone',
    'name' => 'phone',
    'label' => 'Phone Number',
    'is_required' => false,
    'validation_rules' => ['string', 'max:20'],
    'options' => [
        'default_country' => 'US',
    ],
    'order' => 7,
]);

// Project Details
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'textarea',
    'name' => 'project_details',
    'label' => 'Tell Us About Your Project',
    'placeholder' => 'Describe your needs, timeline, and any specific requirements...',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'min:20'],
    'options' => ['rows' => 6],
    'order' => 8,
]);
```

**Key Conditional Logic Patterns (V2):**

```php
// Show field when condition is met
'conditional_logic' => [
    'action' => 'show',          // or 'hide'
    'match' => 'all',            // or 'any'
    'conditions' => [
        [
            'target_field_id' => $triggerField->id,  // Use database ID
            'operator' => 'equals',                   // See operators below
            'value' => 'expected_value',
        ],
    ],
],

// Make field required when condition is met
'conditional_logic' => [
    'conditional_validation' => [
        [
            'rule' => 'required',  // or 'email', 'min:10', etc.
            'match' => 'all',
            'conditions' => [
                [
                    'target_field_id' => $triggerField->id,
                    'operator' => 'equals',
                    'value' => 'value',
                ],
            ],
        ],
    ],
],
```

**Available Operators by Field Type:**
- Text/Email/Textarea: `equals`, `not_equals`, `contains`, `not_contains`, `starts_with`, `ends_with`, `is_empty`, `is_not_empty`
- Number/Slider/Range: `equals`, `not_equals`, `greater_than`, `less_than`, `>=`, `<=`
- Select/Radio: `equals`, `not_equals`, `in`, `not_in`
- Checkbox/Switch: `checked`, `unchecked`
- Date: `equals`, `not_equals`, `after`, `before`, `after_or_equal`, `before_or_equal`

---

## 3. Multi-Step Registration with Tabs

**Use Case:** User registration form organized into logical sections using tabs.

**Key Features:**
- Tab layout for multi-step experience
- Password field with strength indicator
- Terms acceptance checkbox
- V2 tab structure with correct settings

**Code:**

```php
$form = CustomForm::create([
    'name' => 'Create Account',
    'description' => 'Join our community today',
    'is_active' => true,
    'settings' => [
        'submit_button_text' => 'Create Account',
        'success_message' => 'Welcome! Your account has been created.',
    ],
]);

$container = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'element_type' => 'container',
    'order' => 1,
    'settings' => ['fluid' => false],
]);

// Tabs Container
// ✅ V2: Tabs settings
$tabs = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'tabs',
    'order' => 1,
    'settings' => [
        'style' => 'tabs',          // 'tabs', 'pills', or 'underline'
        'alignment' => 'start',     // 'start', 'center', 'end'
        'fill' => false,
        'fade' => true,
    ],
]);

// Tab 1: Personal Info
// ✅ V2: Tab title in settings, not as top-level 'label'
$tab1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $tabs->id,
    'element_type' => 'tab',  // ✅ V2: Must be 'tab', not 'container'
    'order' => 1,
    'settings' => [
        'tab_title' => 'Personal Info',  // ✅ V2: In settings
        'active' => true,                 // First tab is active
    ],
]);

// Row inside Tab 1
$tab1Row = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $tab1->id,
    'element_type' => 'row',
    'order' => 1,
    'settings' => ['gutter' => 'g-3'],
]);

$tab1Col1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $tab1Row->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $tab1Col1->id,
    'field_type' => 'text',
    'name' => 'first_name',
    'label' => 'First Name',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'max:50'],
    'order' => 1,
]);

$tab1Col2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $tab1Row->id,
    'element_type' => 'column',
    'order' => 2,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $tab1Col2->id,
    'field_type' => 'text',
    'name' => 'last_name',
    'label' => 'Last Name',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'max:50'],
    'order' => 2,
]);

// Email (full width)
$tab1Row2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $tab1->id,
    'element_type' => 'row',
    'order' => 2,
]);

$tab1Col3 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $tab1Row2->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '12'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $tab1Col3->id,
    'field_type' => 'email',
    'name' => 'email',
    'label' => 'Email Address',
    'is_required' => true,
    'validation_rules' => ['required', 'email', 'unique:users,email'],
    'order' => 3,
]);

// Phone
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $tab1Col3->id,
    'field_type' => 'phone',
    'name' => 'phone',
    'label' => 'Phone Number',
    'is_required' => false,
    'options' => [
        'default_country' => 'US',
        'show_country_selector' => true,
    ],
    'order' => 4,
]);

// Tab 2: Account Security
$tab2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $tabs->id,
    'element_type' => 'tab',
    'order' => 2,
    'settings' => [
        'tab_title' => 'Account Security',
        'active' => false,
    ],
]);

$tab2Row = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $tab2->id,
    'element_type' => 'row',
    'order' => 1,
]);

$tab2Col = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $tab2Row->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '12'],
]);

// Password with strength indicator (V2 feature)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $tab2Col->id,
    'field_type' => 'password',
    'name' => 'password',
    'label' => 'Password',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'min:8'],
    'help_text' => 'Must be at least 8 characters',
    'options' => [
        'show_toggle' => true,              // Show/hide password toggle
        'show_strength_indicator' => true,  // Visual strength meter
    ],
    'order' => 5,
]);

// Confirm Password
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $tab2Col->id,
    'field_type' => 'password',
    'name' => 'password_confirmation',
    'label' => 'Confirm Password',
    'is_required' => true,
    'validation_rules' => ['required', 'same:password'],
    'options' => [
        'show_toggle' => true,
    ],
    'order' => 6,
]);

// Tab 3: Preferences
$tab3 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $tabs->id,
    'element_type' => 'tab',
    'order' => 3,
    'settings' => [
        'tab_title' => 'Preferences',
        'active' => false,
    ],
]);

$tab3Row = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $tab3->id,
    'element_type' => 'row',
    'order' => 1,
]);

$tab3Col = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $tab3Row->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '12'],
]);

// Communication Preferences
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $tab3Col->id,
    'field_type' => 'checkbox',
    'name' => 'communication_preferences',
    'label' => 'I want to receive:',
    'is_required' => false,
    'options' => [
        'values' => [
            ['label' => 'Product Updates', 'value' => 'product_updates', 'default' => false],
            ['label' => 'Newsletter', 'value' => 'newsletter', 'default' => false],
            ['label' => 'Marketing Emails', 'value' => 'marketing', 'default' => false],
        ],
        'layout' => 'stacked',
    ],
    'order' => 7,
]);

// Terms Agreement (required)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $tab3Col->id,
    'field_type' => 'switch',
    'name' => 'agree_terms',
    'label' => 'I agree to the Terms of Service and Privacy Policy',
    'is_required' => true,
    'validation_rules' => ['required', 'accepted'],
    'options' => [
        'on_label' => 'Yes',
        'off_label' => 'No',
    ],
    'order' => 8,
]);
```

**Tab Configuration Options:**
- `style`: `'tabs'` (default), `'pills'`, or `'underline'`
- `alignment`: `'start'`, `'center'`, `'end'`, `'around'`, `'between'`, `'evenly'`
- `fill`: Boolean - tabs fill container width
- `fade`: Boolean - enable fade transition between tabs

---

## 4. Job Application Form

**Use Case:** Comprehensive job application with file uploads and conditional skill ratings.

**Key Features:**
- File upload for resume/CV
- Card layout for visual grouping
- Conditional fields based on experience level
- Star rating for skills
- Accordion for optional sections

**Code:**

```php
$form = CustomForm::create([
    'name' => 'Job Application',
    'description' => 'Apply for open positions',
    'is_active' => true,
    'settings' => [
        'submit_button_text' => 'Submit Application',
        'success_message' => 'Thank you! We\'ll review your application and be in touch soon.',
    ],
]);

$container = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'element_type' => 'container',
    'order' => 1,
    'settings' => ['fluid' => false],
]);

// Card 1: Personal Information
// ✅ V2: Card settings
$card1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'card',
    'order' => 1,
    'settings' => [
        'card_title' => 'Personal Information',
        'card_subtitle' => 'Tell us about yourself',
        'background_color' => '',      // '' or 'primary', 'secondary', etc.
        'border_color' => '',
        'show_shadow' => false,
    ],
]);

$card1Row = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $card1->id,
    'element_type' => 'row',
    'order' => 1,
    'settings' => ['gutter' => 'g-3'],
]);

// Name fields (2 columns)
$card1Col1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $card1Row->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $card1Col1->id,
    'field_type' => 'text',
    'name' => 'full_name',
    'label' => 'Full Name',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'max:100'],
    'order' => 1,
]);

$card1Col2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $card1Row->id,
    'element_type' => 'column',
    'order' => 2,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $card1Col2->id,
    'field_type' => 'email',
    'name' => 'email',
    'label' => 'Email Address',
    'is_required' => true,
    'validation_rules' => ['required', 'email'],
    'order' => 2,
]);

// Phone and LinkedIn
$card1Row2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $card1->id,
    'element_type' => 'row',
    'order' => 2,
    'settings' => ['gutter' => 'g-3'],
]);

$card1Col3 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $card1Row2->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $card1Col3->id,
    'field_type' => 'phone',
    'name' => 'phone',
    'label' => 'Phone Number',
    'is_required' => true,
    'validation_rules' => ['required'],
    'order' => 3,
]);

$card1Col4 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $card1Row2->id,
    'element_type' => 'column',
    'order' => 2,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $card1Col4->id,
    'field_type' => 'url',
    'name' => 'linkedin_url',
    'label' => 'LinkedIn Profile',
    'placeholder' => 'https://linkedin.com/in/yourname',
    'is_required' => false,
    'validation_rules' => ['url'],
    'options' => [
        'show_preview' => true,  // V2: Show preview button
    ],
    'order' => 4,
]);

// Card 2: Position Details
$card2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'card',
    'order' => 2,
    'settings' => [
        'card_title' => 'Position Details',
    ],
]);

$card2Row = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $card2->id,
    'element_type' => 'row',
    'order' => 1,
]);

$card2Col = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $card2Row->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '12'],
]);

// Position applying for
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $card2Col->id,
    'field_type' => 'select',
    'name' => 'position',
    'label' => 'Position Applying For',
    'is_required' => true,
    'validation_rules' => ['required'],
    'options' => [
        'values' => [
            ['label' => 'Frontend Developer', 'value' => 'frontend', 'default' => false],
            ['label' => 'Backend Developer', 'value' => 'backend', 'default' => false],
            ['label' => 'Full Stack Developer', 'value' => 'fullstack', 'default' => false],
            ['label' => 'DevOps Engineer', 'value' => 'devops', 'default' => false],
        ],
        'searchable' => false,
        'placeholder' => 'Select a position',
    ],
    'order' => 5,
]);

// Experience Level (triggers conditional fields)
$experienceField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $card2Col->id,
    'field_type' => 'radio',
    'name' => 'experience_level',
    'label' => 'Experience Level',
    'is_required' => true,
    'validation_rules' => ['required'],
    'options' => [
        'values' => [
            ['label' => 'Entry Level (0-2 years)', 'value' => 'entry', 'default' => false],
            ['label' => 'Mid Level (3-5 years)', 'value' => 'mid', 'default' => false],
            ['label' => 'Senior (6+ years)', 'value' => 'senior', 'default' => false],
        ],
        'layout' => 'stacked',
    ],
    'order' => 6,
]);

// Years of Experience (conditional - only for mid/senior)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $card2Col->id,
    'field_type' => 'number',
    'name' => 'years_experience',
    'label' => 'Years of Professional Experience',
    'is_required' => false,
    'validation_rules' => ['integer', 'min:0', 'max:50'],
    'conditional_logic' => [
        'action' => 'show',
        'match' => 'any',  // Show if mid OR senior
        'conditions' => [
            [
                'target_field_id' => $experienceField->id,
                'operator' => 'in',
                'value' => 'mid,senior',  // Comma-separated for 'in' operator
            ],
        ],
    ],
    'options' => [
        'min' => 0,
        'max' => 50,
        'step' => 1,
    ],
    'order' => 7,
]);

// Expected Salary Slider
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $card2Col->id,
    'field_type' => 'slider',
    'name' => 'expected_salary',
    'label' => 'Expected Salary (USD)',
    'is_required' => true,
    'validation_rules' => ['required', 'numeric', 'min:30000', 'max:200000'],
    'options' => [
        'min' => 30000,
        'max' => 200000,
        'step' => 5000,
        'show_value' => true,
    ],
    'order' => 8,
]);

// Resume Upload (V2 File Field)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $card2Col->id,
    'field_type' => 'file',
    'name' => 'resume',
    'label' => 'Resume / CV',
    'is_required' => true,
    'validation_rules' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
    'help_text' => 'PDF, DOC, or DOCX up to 5MB',
    'options' => [
        'max_size' => 5120,  // KB
        'allowed_types' => ['pdf', 'doc', 'docx'],
        'preview_enabled' => true,
    ],
    'order' => 9,
]);

// Accordion for Optional Sections
// ✅ V2: Accordion settings
$accordion = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'accordion',
    'order' => 3,
    'settings' => [
        'flush' => false,        // Flush style (no borders)
        'always_open' => false,  // Allow multiple items open
        'show_icons' => true,    // Show expand/collapse icons
    ],
]);

// Accordion Item 1: Skills Assessment
// ✅ V2: Accordion item element type
$accordionItem1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $accordion->id,
    'element_type' => 'accordion_item',  // ✅ V2: Must be 'accordion_item'
    'order' => 1,
    'settings' => [
        'accordion_title' => 'Skills Assessment',  // ✅ V2: In settings
        'open' => false,
    ],
]);

$accordionItem1Row = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $accordionItem1->id,
    'element_type' => 'row',
    'order' => 1,
]);

$accordionItem1Col = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $accordionItem1Row->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '12'],
]);

// Star Rating for Laravel Proficiency
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $accordionItem1Col->id,
    'field_type' => 'star_rating',
    'name' => 'laravel_proficiency',
    'label' => 'Laravel Proficiency',
    'is_required' => false,
    'options' => [
        'max_stars' => 5,
        'allow_half' => false,
    ],
    'order' => 10,
]);

// Star Rating for Vue.js Proficiency
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $accordionItem1Col->id,
    'field_type' => 'star_rating',
    'name' => 'vuejs_proficiency',
    'label' => 'Vue.js Proficiency',
    'is_required' => false,
    'options' => [
        'max_stars' => 5,
        'allow_half' => false,
    ],
    'order' => 11,
]);

// Accordion Item 2: Cover Letter
$accordionItem2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $accordion->id,
    'element_type' => 'accordion_item',
    'order' => 2,
    'settings' => [
        'accordion_title' => 'Cover Letter (Optional)',
        'open' => false,
    ],
]);

$accordionItem2Row = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $accordionItem2->id,
    'element_type' => 'row',
    'order' => 1,
]);

$accordionItem2Col = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $accordionItem2Row->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '12'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $accordionItem2Col->id,
    'field_type' => 'textarea',
    'name' => 'cover_letter',
    'label' => 'Cover Letter',
    'placeholder' => 'Tell us why you\'re a great fit for this position...',
    'is_required' => false,
    'options' => ['rows' => 8],
    'order' => 12,
]);

// Education Repeater with Row/Column Layout
$eduCard = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'card',
    'order' => 4,
    'settings' => ['title' => 'Education'],
]);

$eduRow = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $eduCard->id,
    'element_type' => 'row',
    'order' => 0,
]);

$eduCol = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $eduRow->id,
    'element_type' => 'column',
    'order' => 0,
    'settings' => ['column_width' => ['md' => 12]],
]);

$educationRepeater = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $eduCol->id,
    'field_type' => 'repeater',
    'name' => 'education',
    'label' => 'Education',
    'order' => 0,
    'options' => [
        'initial_instances' => 1,
        'add_button_text' => 'Add Education',
    ],
]);

// Create row inside repeater with 3 equal columns
$eduRepRow = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_field_id' => $educationRepeater->id,  // ✅ Note: parent_field_id, not parent_id
    'element_type' => 'row',
    'order' => 0,
]);

// Column 1: School Name
$eduRepCol1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $eduRepRow->id,
    'element_type' => 'column',
    'order' => 0,
    'settings' => ['column_width' => 'equal'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $eduRepCol1->id,
    'field_type' => 'text',
    'name' => 'school_name',
    'label' => 'School Name',
    'placeholder' => 'Your university or school',
    'order' => 0,
]);

// Column 2: Degree
$eduRepCol2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $eduRepRow->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['column_width' => 'equal'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $eduRepCol2->id,
    'field_type' => 'text',
    'name' => 'degree',
    'label' => 'Degree',
    'placeholder' => 'e.g., B.S., M.S.',
    'order' => 0,
]);

// Column 3: Graduation Year
$eduRepCol3 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $eduRepRow->id,
    'element_type' => 'column',
    'order' => 2,
    'settings' => ['column_width' => 'equal'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $eduRepCol3->id,
    'field_type' => 'number',
    'name' => 'graduation_year',
    'label' => 'Graduation Year',
    'placeholder' => 'e.g., 2024',
    'order' => 0,
]);

// Work Experience Repeater with Multiple Rows
$expCard = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'card',
    'order' => 5,
    'settings' => ['title' => 'Work Experience'],
]);

$expRow = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $expCard->id,
    'element_type' => 'row',
    'order' => 0,
]);

$expCol = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $expRow->id,
    'element_type' => 'column',
    'order' => 0,
    'settings' => ['column_width' => ['md' => 12]],
]);

$experienceRepeater = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $expCol->id,
    'field_type' => 'repeater',
    'name' => 'work_experience',
    'label' => 'Work Experience',
    'order' => 0,
    'options' => [
        'initial_instances' => 1,
        'add_button_text' => 'Add Experience',
    ],
]);

// Row 1 inside repeater: Company and Job Title
$expRepRow1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_field_id' => $experienceRepeater->id,
    'element_type' => 'row',
    'order' => 0,
]);

$expRepRow1Col1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $expRepRow1->id,
    'element_type' => 'column',
    'order' => 0,
    'settings' => ['column_width' => 'equal'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $expRepRow1Col1->id,
    'field_type' => 'text',
    'name' => 'company',
    'label' => 'Company',
    'placeholder' => 'e.g., Acme Inc.',
    'order' => 0,
]);

$expRepRow1Col2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $expRepRow1->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['column_width' => 'equal'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $expRepRow1Col2->id,
    'field_type' => 'text',
    'name' => 'job_title',
    'label' => 'Job Title',
    'placeholder' => 'e.g., Software Engineer',
    'order' => 0,
]);

// Row 2 inside repeater: Start and End Dates
$expRepRow2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_field_id' => $experienceRepeater->id,
    'element_type' => 'row',
    'order' => 1,
]);

$expRepRow2Col1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $expRepRow2->id,
    'element_type' => 'column',
    'order' => 0,
    'settings' => ['column_width' => 'equal'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $expRepRow2Col1->id,
    'field_type' => 'date',
    'name' => 'start_date',
    'label' => 'Start Date',
    'order' => 0,
]);

$expRepRow2Col2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $expRepRow2->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['column_width' => 'equal'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $expRepRow2Col2->id,
    'field_type' => 'date',
    'name' => 'end_date',
    'label' => 'End Date',
    'order' => 0,
]);

// Direct child field (not in row/column) - full width
CustomFormField::create([
    'slick_form_id' => $form->id,
    'parent_field_id' => $experienceRepeater->id,  // Direct child of repeater
    'field_type' => 'textarea',
    'name' => 'responsibilities',
    'label' => 'Responsibilities & Achievements',
    'placeholder' => 'Briefly describe your key responsibilities and achievements',
    'order' => 5,
    'options' => ['rows' => 3],
]);

// References Repeater
$refsCard = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'card',
    'order' => 6,
    'settings' => ['title' => 'References'],
]);

$refsRow = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $refsCard->id,
    'element_type' => 'row',
    'order' => 0,
]);

$refsCol = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $refsRow->id,
    'element_type' => 'column',
    'order' => 0,
    'settings' => ['column_width' => ['md' => 12]],
]);

$refsRepeater = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $refsCol->id,
    'field_type' => 'repeater',
    'name' => 'references',
    'label' => 'References',
    'order' => 0,
    'options' => [
        'initial_instances' => 1,
        'add_button_text' => 'Add Reference',
    ],
]);

// References row with 3 equal columns
$refsRepRow = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_field_id' => $refsRepeater->id,
    'element_type' => 'row',
    'order' => 0,
]);

$refsRepCol1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $refsRepRow->id,
    'element_type' => 'column',
    'order' => 0,
    'settings' => ['column_width' => 'equal'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $refsRepCol1->id,
    'field_type' => 'text',
    'name' => 'ref_name',
    'label' => 'Name',
    'placeholder' => 'Full name',
    'order' => 0,
]);

$refsRepCol2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $refsRepRow->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['column_width' => 'equal'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $refsRepCol2->id,
    'field_type' => 'text',
    'name' => 'ref_relationship',
    'label' => 'Relationship',
    'placeholder' => 'e.g., Manager',
    'order' => 0,
]);

$refsRepCol3 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $refsRepRow->id,
    'element_type' => 'column',
    'order' => 2,
    'settings' => ['column_width' => 'equal'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $refsRepCol3->id,
    'field_type' => 'phone',
    'name' => 'ref_phone',
    'label' => 'Phone',
    'placeholder' => '(555) 123-4567',
    'order' => 0,
    'options' => [
        'display_format' => 'us',
        'enable_input_mask' => true,
        'mask_type' => 'phone_us',
    ],
]);
```

**Key V2 Repeater with Row/Column Layout:**

Repeaters can contain layout elements (rows, columns, cards, etc.) for advanced layouts. Use `parent_field_id` to attach layout elements to a repeater:

```php
// Create repeater field
$repeater = CustomFormField::create([
    'field_type' => 'repeater',
    'name' => 'items',
    'options' => [
        'initial_instances' => 1,
        'add_button_text' => 'Add Item',
        'min_instances' => 1,
        'max_instances' => 10,
    ],
]);

// Create row INSIDE repeater using parent_field_id
$rowInRepeater = SlickFormLayoutElement::create([
    'parent_field_id' => $repeater->id,  // ✅ Links to repeater field
    'element_type' => 'row',
]);

// Create columns in the row
$col1 = SlickFormLayoutElement::create([
    'parent_id' => $rowInRepeater->id,  // ✅ Links to row element
    'element_type' => 'column',
    'settings' => ['column_width' => 'equal'],
]);

// Add field to column
CustomFormField::create([
    'slick_form_layout_element_id' => $col1->id,  // ✅ Links to column
    'field_type' => 'text',
    'name' => 'item_name',
]);

// You can also have direct field children (not in layout elements)
CustomFormField::create([
    'parent_field_id' => $repeater->id,  // ✅ Direct child of repeater
    'field_type' => 'textarea',
    'name' => 'item_description',
]);
```

**Key V2 Layout Elements:**

```php
// Card Structure
$card = SlickFormLayoutElement::create([
    'element_type' => 'card',
    'settings' => [
        'card_title' => 'Section Title',
        'card_subtitle' => 'Optional subtitle',
        'card_header' => 'Custom header HTML',  // Optional
        'card_footer' => 'Custom footer HTML',  // Optional
        'background_color' => '',  // '', 'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'
        'border_color' => '',      // Same color options
        'show_shadow' => false,
    ],
]);

// Accordion Structure
$accordion = SlickFormLayoutElement::create([
    'element_type' => 'accordion',
    'settings' => [
        'flush' => false,        // Remove borders for flush style
        'always_open' => false,  // Allow multiple items open simultaneously
        'show_icons' => true,    // Show expand/collapse icons
    ],
]);

// Accordion Item (child of accordion)
$item = SlickFormLayoutElement::create([
    'parent_id' => $accordion->id,
    'element_type' => 'accordion_item',
    'settings' => [
        'accordion_title' => 'Section Title',
        'open' => false,  // Initially expanded
    ],
]);
```

---

## 5. Event Registration with Pricing

**Use Case:** Event registration form with dynamic pricing based on selections.

**Key Features:**
- Date picker for event selection
- Calculation field for automatic price totals
- Checkbox for add-ons
- Number field for attendee count

**Code:**

```php
$form = CustomForm::create([
    'name' => 'Event Registration',
    'description' => 'Register for our upcoming conference',
    'is_active' => true,
    'settings' => [
        'submit_button_text' => 'Complete Registration',
        'success_message' => 'Registration confirmed! Check your email for details.',
    ],
]);

$container = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'element_type' => 'container',
    'order' => 1,
    'settings' => ['fluid' => false],
]);

// Header Field (V2 field type)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'header',
    'name' => 'header_attendee_info',
    'label' => 'Attendee Information',
    'show_label' => false,  // Header renders its own text
    'options' => [
        'tag' => 'h3',  // h1, h2, h3, h4, h5, h6
        'text_alignment' => 'start',  // start, center, end
    ],
    'order' => 1,
]);

// Row for name
$row1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'row',
    'order' => 1,
    'settings' => ['gutter' => 'g-3'],
]);

$col1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row1->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col1->id,
    'field_type' => 'text',
    'name' => 'attendee_name',
    'label' => 'Full Name',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'max:100'],
    'order' => 2,
]);

$col2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row1->id,
    'element_type' => 'column',
    'order' => 2,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col2->id,
    'field_type' => 'email',
    'name' => 'attendee_email',
    'label' => 'Email Address',
    'is_required' => true,
    'validation_rules' => ['required', 'email'],
    'order' => 3,
]);

// Event Selection
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'select',
    'name' => 'event_date',
    'label' => 'Select Event Date',
    'is_required' => true,
    'validation_rules' => ['required'],
    'options' => [
        'values' => [
            ['label' => 'March 15, 2025 - Early Bird ($299)', 'value' => '2025-03-15', 'default' => false],
            ['label' => 'April 20, 2025 - Regular ($399)', 'value' => '2025-04-20', 'default' => false],
            ['label' => 'May 25, 2025 - Regular ($399)', 'value' => '2025-05-25', 'default' => false],
        ],
        'placeholder' => 'Choose a date',
    ],
    'order' => 4,
]);

// Ticket Type
$ticketTypeField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'radio',
    'name' => 'ticket_type',
    'label' => 'Ticket Type',
    'is_required' => true,
    'validation_rules' => ['required'],
    'options' => [
        'values' => [
            ['label' => 'General Admission - $299', 'value' => '299', 'default' => false],
            ['label' => 'VIP Pass - $599', 'value' => '599', 'default' => false],
            ['label' => 'Student Discount - $149', 'value' => '149', 'default' => false],
        ],
        'layout' => 'stacked',
    ],
    'order' => 5,
]);

// Number of Attendees
$attendeeCountField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'number',
    'name' => 'attendee_count',
    'label' => 'Number of Attendees',
    'is_required' => true,
    'validation_rules' => ['required', 'integer', 'min:1', 'max:10'],
    'options' => [
        'min' => 1,
        'max' => 10,
        'step' => 1,
        'default_value' => 1,
    ],
    'order' => 6,
]);

// Add-ons (affects price calculation)
$addonsField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'checkbox',
    'name' => 'addons',
    'label' => 'Optional Add-ons',
    'is_required' => false,
    'options' => [
        'values' => [
            ['label' => 'Workshop Access (+$99)', 'value' => '99', 'default' => false],
            ['label' => 'Networking Dinner (+$75)', 'value' => '75', 'default' => false],
            ['label' => 'Conference Materials (+$25)', 'value' => '25', 'default' => false],
        ],
        'layout' => 'stacked',
    ],
    'order' => 7,
]);

// Total Price Calculation (V2 Calculation Field)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'calculation',
    'name' => 'total_price',
    'label' => 'Total Price',
    'show_label' => true,
    'is_required' => false,
    'options' => [
        // Formula: (ticket_type * attendee_count) + sum(addons)
        'formula' => '({ticket_type} * {attendee_count}) + SUM({addons})',
        'decimal_places' => 2,
        'prefix' => '$',
        'suffix' => ' USD',
    ],
    'order' => 8,
]);

// Dietary Restrictions (conditional on networking dinner)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'text',
    'name' => 'dietary_restrictions',
    'label' => 'Dietary Restrictions',
    'placeholder' => 'Vegetarian, vegan, gluten-free, etc.',
    'is_required' => false,
    'conditional_logic' => [
        'action' => 'show',
        'match' => 'all',
        'conditions' => [
            [
                'target_field_id' => $addonsField->id,
                'operator' => 'contains',  // For checkbox fields
                'value' => '75',  // Networking dinner value
            ],
        ],
    ],
    'order' => 9,
]);

// Special Requests
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'textarea',
    'name' => 'special_requests',
    'label' => 'Special Requests or Accommodations',
    'placeholder' => 'Any special needs or requests?',
    'is_required' => false,
    'options' => ['rows' => 4],
    'order' => 10,
]);
```

**V2 Calculation Field:**

The calculation field automatically computes values based on other fields using a formula:

```php
CustomFormField::create([
    'field_type' => 'calculation',
    'name' => 'total',
    'label' => 'Total',
    'options' => [
        'formula' => '({field1} + {field2}) * {field3}',
        'decimal_places' => 2,
        'prefix' => '$',
        'suffix' => ' USD',
    ],
]);
```

**Supported Formula Functions:**
- `SUM({field})` - Sum array values (for checkboxes)
- `AVG({field})` - Average of values
- `MIN({field1}, {field2})` - Minimum value
- `MAX({field1}, {field2})` - Maximum value
- `ROUND({field}, decimals)` - Round to decimals
- `IF(condition, true_value, false_value)` - Conditional logic

**Operators:**
- Arithmetic: `+`, `-`, `*`, `/`, `%`
- Comparison: `>`, `<`, `>=`, `<=`, `==`, `!=`
- Parentheses for grouping: `(` `)`

---

## 6. Customer Satisfaction Survey

**Use Case:** Customer feedback survey with rating scales and conditional follow-ups.

**Key Features:**
- Star ratings for satisfaction
- Slider for NPS score
- Conditional feedback textarea
- Tags field for categorization

**Code:**

```php
$form = CustomForm::create([
    'name' => 'Customer Satisfaction Survey',
    'description' => 'Help us improve our service',
    'is_active' => true,
    'settings' => [
        'submit_button_text' => 'Submit Feedback',
        'success_message' => 'Thank you for your valuable feedback!',
    ],
]);

$container = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'element_type' => 'container',
    'order' => 1,
    'settings' => ['fluid' => false],
]);

// Overall Satisfaction
$satisfactionField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'star_rating',
    'name' => 'overall_satisfaction',
    'label' => 'Overall Satisfaction with Our Service',
    'is_required' => true,
    'validation_rules' => ['required', 'integer', 'min:1', 'max:5'],
    'options' => [
        'max_stars' => 5,
        'allow_half' => false,
        'icon' => 'star',  // or 'heart'
    ],
    'order' => 1,
]);

// Product Quality Rating
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'star_rating',
    'name' => 'product_quality',
    'label' => 'Product Quality',
    'is_required' => true,
    'options' => [
        'max_stars' => 5,
        'allow_half' => false,
    ],
    'order' => 2,
]);

// Customer Support Rating
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'star_rating',
    'name' => 'customer_support',
    'label' => 'Customer Support',
    'is_required' => true,
    'options' => [
        'max_stars' => 5,
        'allow_half' => false,
    ],
    'order' => 3,
]);

// NPS Score Slider
$npsField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'slider',
    'name' => 'nps_score',
    'label' => 'How likely are you to recommend us to a friend? (0-10)',
    'is_required' => true,
    'validation_rules' => ['required', 'integer', 'min:0', 'max:10'],
    'help_text' => '0 = Not at all likely, 10 = Extremely likely',
    'options' => [
        'min' => 0,
        'max' => 10,
        'step' => 1,
        'show_value' => true,
    ],
    'order' => 4,
]);

// Feedback (conditional - only if low NPS score)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'textarea',
    'name' => 'improvement_feedback',
    'label' => 'What could we do to improve?',
    'placeholder' => 'Please share your thoughts...',
    'is_required' => false,
    'options' => ['rows' => 5],
    'conditional_logic' => [
        'action' => 'show',
        'match' => 'all',
        'conditions' => [
            [
                'target_field_id' => $npsField->id,
                'operator' => 'less_than',  // Show for detractors (0-6)
                'value' => '7',
            ],
        ],
        // Make required when shown
        'conditional_validation' => [
            [
                'rule' => 'required',
                'match' => 'all',
                'conditions' => [
                    [
                        'target_field_id' => $npsField->id,
                        'operator' => 'less_than',
                        'value' => '7',
                    ],
                ],
            ],
        ],
    ],
    'order' => 5,
]);

// Positive Feedback (conditional - only if high NPS score)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'textarea',
    'name' => 'positive_feedback',
    'label' => 'What do you love most about our service?',
    'placeholder' => 'We\'d love to hear what we\'re doing right!',
    'is_required' => false,
    'options' => ['rows' => 5],
    'conditional_logic' => [
        'action' => 'show',
        'match' => 'all',
        'conditions' => [
            [
                'target_field_id' => $npsField->id,
                'operator' => 'greater_than',  // Show for promoters (9-10)
                'value' => '8',
            ],
        ],
    ],
    'order' => 6,
]);

// Feature Requests (Tags Field - V2)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'tags',
    'name' => 'requested_features',
    'label' => 'What new features would you like to see?',
    'is_required' => false,
    'help_text' => 'Type and press Enter to add tags',
    'options' => [
        'max_tags' => 10,
        'allow_custom' => true,  // Allow user to create new tags
        'suggestions' => [       // Predefined suggestions
            'Dark Mode',
            'Mobile App',
            'API Access',
            'Advanced Analytics',
            'Team Collaboration',
        ],
    ],
    'order' => 7,
]);

// Would Recommend Switch
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'switch',
    'name' => 'would_recommend',
    'label' => 'I would recommend this product to others',
    'is_required' => false,
    'options' => [
        'on_label' => 'Yes',
        'off_label' => 'No',
    ],
    'order' => 8,
]);

// Email for Follow-up (optional)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'email',
    'name' => 'email',
    'label' => 'Email (optional - if you\'d like us to follow up)',
    'is_required' => false,
    'validation_rules' => ['email'],
    'order' => 9,
]);
```

**V2 Tags Field:**

The tags field allows users to add multiple keyword tags:

```php
CustomFormField::create([
    'field_type' => 'tags',
    'name' => 'keywords',
    'label' => 'Keywords',
    'options' => [
        'max_tags' => 10,              // Maximum number of tags
        'allow_custom' => true,        // Allow new tags
        'suggestions' => ['Tag 1', 'Tag 2'],  // Predefined options
        'min_length' => 2,             // Minimum tag length
        'max_length' => 50,            // Maximum tag length
    ],
]);
```

---

## 7. Product Order Form with Repeater

**Use Case:** Order form allowing customers to add multiple products.

**Key Features:**
- Repeater field for adding multiple line items
- Calculation for order subtotal
- Date range for delivery window
- Color picker for customization

**Code:**

```php
$form = CustomForm::create([
    'name' => 'Product Order Form',
    'description' => 'Place your custom order',
    'is_active' => true,
    'settings' => [
        'submit_button_text' => 'Place Order',
        'success_message' => 'Order received! We\'ll send a confirmation email shortly.',
    ],
]);

$container = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'element_type' => 'container',
    'order' => 1,
    'settings' => ['fluid' => false],
]);

// Customer Information
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'header',
    'name' => 'header_customer',
    'label' => 'Customer Information',
    'show_label' => false,
    'options' => ['tag' => 'h3'],
    'order' => 1,
]);

$row1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'row',
    'order' => 1,
    'settings' => ['gutter' => 'g-3'],
]);

$col1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row1->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col1->id,
    'field_type' => 'text',
    'name' => 'customer_name',
    'label' => 'Full Name',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'max:100'],
    'order' => 2,
]);

$col2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row1->id,
    'element_type' => 'column',
    'order' => 2,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col2->id,
    'field_type' => 'email',
    'name' => 'customer_email',
    'label' => 'Email Address',
    'is_required' => true,
    'validation_rules' => ['required', 'email'],
    'order' => 3,
]);

// Order Items Header
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'header',
    'name' => 'header_items',
    'label' => 'Order Items',
    'show_label' => false,
    'options' => ['tag' => 'h3'],
    'order' => 4,
]);

// Repeater Field for Line Items (V2)
// ✅ V2: Uses min_items/max_items, not min_rows/max_rows
$lineItemsField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'repeater',
    'name' => 'line_items',
    'label' => 'Products',
    'is_required' => true,
    'validation_rules' => ['required', 'array', 'min:1'],
    'options' => [
        'min_items' => 1,   // ✅ V2: Changed from min_rows
        'max_items' => 10,  // ✅ V2: Changed from max_rows
        'button_label' => 'Add Another Product',
    ],
    'order' => 5,
]);

// ✅ IMPORTANT: Repeater children are separate fields linked by parent_field_id
// Create each child field with parent_field_id = $lineItemsField->id

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'parent_field_id' => $lineItemsField->id,
    'field_type' => 'select',
    'name' => 'item_product',   // Use unique names per form
    'label' => 'Product',
    'is_required' => true,
    'options' => [
        'values' => [
            ['label' => 'T-Shirt', 'value' => 't-shirt'],
            ['label' => 'Hoodie', 'value' => 'hoodie'],
            ['label' => 'Hat', 'value' => 'hat'],
            ['label' => 'Mug', 'value' => 'mug'],
        ],
    ],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'parent_field_id' => $lineItemsField->id,
    'field_type' => 'select',
    'name' => 'item_size',
    'label' => 'Size',
    'is_required' => true,
    'options' => [
        'values' => [
            ['label' => 'Small', 'value' => 'S'],
            ['label' => 'Medium', 'value' => 'M'],
            ['label' => 'Large', 'value' => 'L'],
            ['label' => 'X-Large', 'value' => 'XL'],
        ],
    ],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'parent_field_id' => $lineItemsField->id,
    'field_type' => 'text',
    'name' => 'item_color',
    'label' => 'Color',
    'is_required' => true,
    'placeholder' => 'e.g., Blue, Red',
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'parent_field_id' => $lineItemsField->id,
    'field_type' => 'number',
    'name' => 'item_quantity',
    'label' => 'Quantity',
    'is_required' => true,
    'validation_rules' => ['required', 'integer', 'min:1', 'max:100'],
    'options' => [
        'min' => 1,
        'max' => 100,
        'step' => 1,
    ],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'parent_field_id' => $lineItemsField->id,
    'field_type' => 'number',
    'name' => 'item_unit_price',
    'label' => 'Unit Price ($)',
    'is_required' => true,
    'validation_rules' => ['required', 'numeric', 'min:0'],
    'options' => [
        'min' => 0,
        'step' => 0.01,
    ],
]);

// Custom Color Preference (Color Picker - V2)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'color_picker',
    'name' => 'custom_color',
    'label' => 'Custom Brand Color (optional)',
    'is_required' => false,
    'help_text' => 'Choose a color for custom branding',
    'options' => [
        'default_color' => '#000000',
        'allow_opacity' => false,
    ],
    'order' => 6,
]);

// Delivery Date Range (V2)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'date_range',
    'name' => 'delivery_window',
    'label' => 'Preferred Delivery Window',
    'is_required' => true,
    'validation_rules' => ['required'],
    'help_text' => 'Select the date range when you can receive the order',
    'options' => [
        'min_date' => 'today',
        'max_date' => '+90 days',
        'date_format' => 'Y-m-d',
    ],
    'order' => 7,
]);

// Shipping Address
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'textarea',
    'name' => 'shipping_address',
    'label' => 'Shipping Address',
    'is_required' => true,
    'validation_rules' => ['required', 'string'],
    'options' => ['rows' => 4],
    'order' => 8,
]);

// Special Instructions
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'textarea',
    'name' => 'special_instructions',
    'label' => 'Special Instructions',
    'placeholder' => 'Any special requests or notes?',
    'is_required' => false,
    'options' => ['rows' => 3],
    'order' => 9,
]);

// Order Total (Read-only calculation)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'paragraph',
    'name' => 'order_note',
    'show_label' => false,
    'options' => [
        'content' => '<p class="alert alert-info">Order total will be calculated and displayed on the next page.</p>',
    ],
    'order' => 10,
]);
```

**V2 Repeater Field:**

The repeater allows users to add multiple sets of fields dynamically. In V2, repeater children are separate fields linked via `parent_field_id`:

```php
// Create the repeater container field
$repeater = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'repeater',
    'name' => 'items',
    'label' => 'Items',
    'options' => [
        'min_items' => 1,      // ✅ Not min_rows
        'max_items' => 10,     // ✅ Not max_rows
        'button_label' => 'Add Item',
    ],
]);

// Then add child fields as separate records with parent_field_id
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'parent_field_id' => $repeater->id,
    'field_type' => 'text',   // or select, number, etc.
    'name' => 'item_field_name',  // Must be unique per form
    'label' => 'Field Label',
    'is_required' => true,
    'options' => [
        // For select/radio: 'values' => [['label' => 'Option 1', 'value' => 'opt1']]
        // For number: 'min' => 0, 'max' => 100, 'step' => 1
    ],
]);
```

---

## 8. Support Ticket Form

**Use Case:** Technical support request form with file attachments and priority selection.

**Key Features:**
- Issue type categorization
- Priority selection
- Multiple file uploads
- Conditional fields based on issue type
- Screenshot upload (V2 multi-file)

**Code:**

```php
$form = CustomForm::create([
    'name' => 'Support Ticket',
    'description' => 'Submit a support request',
    'is_active' => true,
    'settings' => [
        'submit_button_text' => 'Submit Ticket',
        'success_message' => 'Ticket created! Our support team will respond within 24 hours.',
    ],
]);

$container = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'element_type' => 'container',
    'order' => 1,
    'settings' => ['fluid' => false],
]);

// Contact Information
$row1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'row',
    'order' => 1,
    'settings' => ['gutter' => 'g-3'],
]);

$col1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row1->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col1->id,
    'field_type' => 'text',
    'name' => 'requester_name',
    'label' => 'Your Name',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'max:100'],
    'order' => 1,
]);

$col2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row1->id,
    'element_type' => 'column',
    'order' => 2,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col2->id,
    'field_type' => 'email',
    'name' => 'requester_email',
    'label' => 'Email Address',
    'is_required' => true,
    'validation_rules' => ['required', 'email'],
    'order' => 2,
]);

// Issue Type (triggers conditional fields)
$issueTypeField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'select',
    'name' => 'issue_type',
    'label' => 'Issue Type',
    'is_required' => true,
    'validation_rules' => ['required'],
    'options' => [
        'values' => [
            ['label' => 'Bug Report', 'value' => 'bug', 'default' => false],
            ['label' => 'Feature Request', 'value' => 'feature', 'default' => false],
            ['label' => 'Technical Question', 'value' => 'question', 'default' => false],
            ['label' => 'Account Issue', 'value' => 'account', 'default' => false],
            ['label' => 'Other', 'value' => 'other', 'default' => false],
        ],
        'placeholder' => 'Select issue type',
    ],
    'order' => 3,
]);

// Priority Level
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'radio',
    'name' => 'priority',
    'label' => 'Priority',
    'is_required' => true,
    'validation_rules' => ['required'],
    'options' => [
        'values' => [
            ['label' => 'Low - General inquiry', 'value' => 'low', 'default' => false],
            ['label' => 'Medium - Affecting work', 'value' => 'medium', 'default' => true],
            ['label' => 'High - Blocking progress', 'value' => 'high', 'default' => false],
            ['label' => 'Critical - System down', 'value' => 'critical', 'default' => false],
        ],
        'layout' => 'stacked',
    ],
    'order' => 4,
]);

// Subject
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'text',
    'name' => 'subject',
    'label' => 'Subject',
    'placeholder' => 'Brief description of the issue',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'max:200'],
    'order' => 5,
]);

// Description
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'textarea',
    'name' => 'description',
    'label' => 'Description',
    'placeholder' => 'Please provide as much detail as possible...',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'min:20'],
    'options' => ['rows' => 6],
    'order' => 6,
]);

// Steps to Reproduce (conditional - only for bug reports)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'textarea',
    'name' => 'steps_to_reproduce',
    'label' => 'Steps to Reproduce',
    'placeholder' => '1. Go to...\n2. Click on...\n3. See error...',
    'is_required' => false,
    'options' => ['rows' => 5],
    'conditional_logic' => [
        'action' => 'show',
        'match' => 'all',
        'conditions' => [
            [
                'target_field_id' => $issueTypeField->id,
                'operator' => 'equals',
                'value' => 'bug',
            ],
        ],
        'conditional_validation' => [
            [
                'rule' => 'required',
                'match' => 'all',
                'conditions' => [
                    [
                        'target_field_id' => $issueTypeField->id,
                        'operator' => 'equals',
                        'value' => 'bug',
                    ],
                ],
            ],
        ],
    ],
    'order' => 7,
]);

// Expected Behavior (conditional - only for bug reports)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'textarea',
    'name' => 'expected_behavior',
    'label' => 'Expected Behavior',
    'placeholder' => 'What should happen instead?',
    'is_required' => false,
    'options' => ['rows' => 3],
    'conditional_logic' => [
        'action' => 'show',
        'match' => 'all',
        'conditions' => [
            [
                'target_field_id' => $issueTypeField->id,
                'operator' => 'equals',
                'value' => 'bug',
            ],
        ],
    ],
    'order' => 8,
]);

// Screenshots (V2 Image Field with multiple uploads)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'image',
    'name' => 'screenshots',
    'label' => 'Screenshots',
    'is_required' => false,
    'help_text' => 'Upload images showing the issue (max 5 files, 5MB each)',
    'options' => [
        'max_size' => 5120,           // KB per file
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif'],
        'multiple' => true,           // ✅ V2: Allow multiple files
        'max_files' => 5,             // ✅ V2: Max number of files
        'preview_enabled' => true,
        'preview_width' => 150,
        'preview_height' => 150,
    ],
    'order' => 9,
]);

// System Information
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'code',
    'name' => 'system_info',
    'label' => 'System Information (optional)',
    'placeholder' => 'Browser, OS, version numbers, etc.',
    'is_required' => false,
    'options' => [
        'language' => 'plaintext',
        'line_numbers' => false,
        'rows' => 4,
    ],
    'order' => 10,
]);

// Request Phone Callback
$phoneCallbackField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'switch',
    'name' => 'request_callback',
    'label' => 'Request Phone Callback',
    'is_required' => false,
    'options' => [
        'on_label' => 'Yes',
        'off_label' => 'No',
    ],
    'order' => 11,
]);

// Phone Number (conditional - only if callback requested)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'phone',
    'name' => 'callback_phone',
    'label' => 'Phone Number for Callback',
    'is_required' => false,
    'conditional_logic' => [
        'action' => 'show',
        'match' => 'all',
        'conditions' => [
            [
                'target_field_id' => $phoneCallbackField->id,
                'operator' => 'checked',  // For switch fields
            ],
        ],
        'conditional_validation' => [
            [
                'rule' => 'required',
                'match' => 'all',
                'conditions' => [
                    [
                        'target_field_id' => $phoneCallbackField->id,
                        'operator' => 'checked',
                    ],
                ],
            ],
        ],
    ],
    'order' => 12,
]);
```

**V2 Multi-File Upload:**

Image, File, and Video fields support multiple uploads in V2:

```php
CustomFormField::create([
    'field_type' => 'image',  // or 'file' or 'video'
    'name' => 'attachments',
    'label' => 'Attachments',
    'options' => [
        'multiple' => true,           // ✅ V2: Enable multiple files
        'max_files' => 5,             // ✅ V2: Maximum number
        'max_size' => 5120,           // KB per file
        'allowed_types' => ['jpg', 'png', 'pdf'],
        'preview_enabled' => true,
    ],
]);
```

---

## 9. Newsletter Signup with Preferences

**Use Case:** Newsletter subscription form with preference selections.

**Key Features:**
- Email validation
- Multiple interest categories
- Frequency selection
- Conditional fields for business subscribers

**Code:**

```php
$form = CustomForm::create([
    'name' => 'Newsletter Signup',
    'description' => 'Subscribe to our newsletter',
    'is_active' => true,
    'settings' => [
        'submit_button_text' => 'Subscribe',
        'success_message' => 'Welcome! Please check your email to confirm your subscription.',
    ],
]);

$container = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'element_type' => 'container',
    'order' => 1,
    'settings' => ['fluid' => false],
]);

// Name
$row1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'row',
    'order' => 1,
    'settings' => ['gutter' => 'g-3'],
]);

$col1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row1->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col1->id,
    'field_type' => 'text',
    'name' => 'first_name',
    'label' => 'First Name',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'max:50'],
    'order' => 1,
]);

$col2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row1->id,
    'element_type' => 'column',
    'order' => 2,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col2->id,
    'field_type' => 'text',
    'name' => 'last_name',
    'label' => 'Last Name',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'max:50'],
    'order' => 2,
]);

// Email
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'email',
    'name' => 'email',
    'label' => 'Email Address',
    'is_required' => true,
    'validation_rules' => ['required', 'email', 'unique:newsletter_subscribers,email'],
    'order' => 3,
]);

// Subscriber Type
$subscriberTypeField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'radio',
    'name' => 'subscriber_type',
    'label' => 'I\'m subscribing as a:',
    'is_required' => true,
    'validation_rules' => ['required'],
    'options' => [
        'values' => [
            ['label' => 'Individual', 'value' => 'individual', 'default' => true],
            ['label' => 'Business', 'value' => 'business', 'default' => false],
        ],
        'layout' => 'inline',
    ],
    'order' => 4,
]);

// Company Name (conditional)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'text',
    'name' => 'company_name',
    'label' => 'Company Name',
    'is_required' => false,
    'validation_rules' => ['string', 'max:100'],
    'conditional_logic' => [
        'action' => 'show',
        'match' => 'all',
        'conditions' => [
            [
                'target_field_id' => $subscriberTypeField->id,
                'operator' => 'equals',
                'value' => 'business',
            ],
        ],
    ],
    'order' => 5,
]);

// Interest Categories
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'checkbox',
    'name' => 'interests',
    'label' => 'I\'m interested in:',
    'is_required' => true,
    'validation_rules' => ['required', 'array', 'min:1'],
    'help_text' => 'Select at least one topic',
    'options' => [
        'values' => [
            ['label' => 'Product Updates', 'value' => 'product_updates', 'default' => false],
            ['label' => 'Industry News', 'value' => 'industry_news', 'default' => false],
            ['label' => 'Tips & Tutorials', 'value' => 'tutorials', 'default' => false],
            ['label' => 'Special Offers', 'value' => 'offers', 'default' => false],
            ['label' => 'Event Invitations', 'value' => 'events', 'default' => false],
        ],
        'layout' => 'stacked',
    ],
    'order' => 6,
]);

// Email Frequency
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'select',
    'name' => 'email_frequency',
    'label' => 'How often would you like to receive emails?',
    'is_required' => true,
    'validation_rules' => ['required'],
    'options' => [
        'values' => [
            ['label' => 'Daily Digest', 'value' => 'daily', 'default' => false],
            ['label' => 'Weekly Summary', 'value' => 'weekly', 'default' => true],
            ['label' => 'Monthly Newsletter', 'value' => 'monthly', 'default' => false],
        ],
        'placeholder' => 'Select frequency',
    ],
    'order' => 7,
]);

// Content Format
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'radio',
    'name' => 'content_format',
    'label' => 'Preferred Email Format',
    'is_required' => true,
    'options' => [
        'values' => [
            ['label' => 'HTML (with images and formatting)', 'value' => 'html', 'default' => true],
            ['label' => 'Plain Text', 'value' => 'text', 'default' => false],
        ],
        'layout' => 'stacked',
    ],
    'order' => 8,
]);

// Privacy Consent
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'switch',
    'name' => 'privacy_consent',
    'label' => 'I agree to the Privacy Policy and Terms of Service',
    'is_required' => true,
    'validation_rules' => ['required', 'accepted'],
    'options' => [
        'on_label' => 'I Agree',
        'off_label' => 'No',
    ],
    'order' => 9,
]);

// Referral Source
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'select',
    'name' => 'referral_source',
    'label' => 'How did you hear about us?',
    'is_required' => false,
    'options' => [
        'values' => [
            ['label' => 'Search Engine', 'value' => 'search', 'default' => false],
            ['label' => 'Social Media', 'value' => 'social', 'default' => false],
            ['label' => 'Friend Referral', 'value' => 'friend', 'default' => false],
            ['label' => 'Blog or Article', 'value' => 'blog', 'default' => false],
            ['label' => 'Other', 'value' => 'other', 'default' => false],
        ],
        'placeholder' => 'Select one (optional)',
    ],
    'order' => 10,
]);
```

---

## 10. Booking Form with Date Range

**Use Case:** Hotel or service booking with date selection and special requests.

**Key Features:**
- Date range picker for check-in/check-out
- Number field for guests
- Conditional room type options
- Location picker for pickup (V2)
- Special requests textarea

**Code:**

```php
$form = CustomForm::create([
    'name' => 'Hotel Booking',
    'description' => 'Reserve your room',
    'is_active' => true,
    'settings' => [
        'submit_button_text' => 'Complete Booking',
        'success_message' => 'Booking confirmed! We\'ve sent a confirmation email.',
    ],
]);

$container = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'element_type' => 'container',
    'order' => 1,
    'settings' => ['fluid' => false],
]);

// Guest Information
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'header',
    'name' => 'header_guest',
    'label' => 'Guest Information',
    'show_label' => false,
    'options' => ['tag' => 'h3'],
    'order' => 1,
]);

$row1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $container->id,
    'element_type' => 'row',
    'order' => 1,
    'settings' => ['gutter' => 'g-3'],
]);

$col1 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row1->id,
    'element_type' => 'column',
    'order' => 1,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col1->id,
    'field_type' => 'text',
    'name' => 'guest_name',
    'label' => 'Full Name',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'max:100'],
    'order' => 2,
]);

$col2 = SlickFormLayoutElement::create([
    'slick_form_id' => $form->id,
    'parent_id' => $row1->id,
    'element_type' => 'column',
    'order' => 2,
    'settings' => ['md' => '6'],
]);

CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $col2->id,
    'field_type' => 'email',
    'name' => 'guest_email',
    'label' => 'Email Address',
    'is_required' => true,
    'validation_rules' => ['required', 'email'],
    'order' => 3,
]);

// Phone
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'phone',
    'name' => 'guest_phone',
    'label' => 'Phone Number',
    'is_required' => true,
    'validation_rules' => ['required'],
    'options' => [
        'default_country' => 'US',
        'show_country_selector' => true,
    ],
    'order' => 4,
]);

// Booking Details Header
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'header',
    'name' => 'header_booking',
    'label' => 'Booking Details',
    'show_label' => false,
    'options' => ['tag' => 'h3'],
    'order' => 5,
]);

// Date Range for Stay (V2)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'date_range',
    'name' => 'stay_dates',
    'label' => 'Check-in / Check-out',
    'is_required' => true,
    'validation_rules' => ['required'],
    'help_text' => 'Select your arrival and departure dates',
    'options' => [
        'min_date' => 'today',          // Don't allow past dates
        'max_date' => '+365 days',      // Up to 1 year ahead
        'date_format' => 'Y-m-d',
        'start_label' => 'Check-in',
        'end_label' => 'Check-out',
    ],
    'order' => 6,
]);

// Number of Guests
$guestsField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'number',
    'name' => 'number_of_guests',
    'label' => 'Number of Guests',
    'is_required' => true,
    'validation_rules' => ['required', 'integer', 'min:1', 'max:10'],
    'options' => [
        'min' => 1,
        'max' => 10,
        'step' => 1,
        'default_value' => 2,
    ],
    'order' => 7,
]);

// Room Type
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'select',
    'name' => 'room_type',
    'label' => 'Room Type',
    'is_required' => true,
    'validation_rules' => ['required'],
    'options' => [
        'values' => [
            ['label' => 'Standard Room - 1 Queen Bed ($149/night)', 'value' => 'standard_queen', 'default' => false],
            ['label' => 'Standard Room - 2 Double Beds ($169/night)', 'value' => 'standard_double', 'default' => false],
            ['label' => 'Deluxe Suite - 1 King Bed ($249/night)', 'value' => 'deluxe_king', 'default' => false],
            ['label' => 'Family Suite - 2 Bedrooms ($349/night)', 'value' => 'family_suite', 'default' => false],
        ],
        'placeholder' => 'Select room type',
    ],
    'order' => 8,
]);

// Extra Bed (conditional - only if 3+ guests)
$extraBedField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'switch',
    'name' => 'extra_bed',
    'label' => 'Add Extra Bed (+$30/night)',
    'is_required' => false,
    'options' => [
        'on_label' => 'Yes',
        'off_label' => 'No',
    ],
    'conditional_logic' => [
        'action' => 'show',
        'match' => 'all',
        'conditions' => [
            [
                'target_field_id' => $guestsField->id,
                'operator' => 'greater_than',
                'value' => '2',
            ],
        ],
    ],
    'order' => 9,
]);

// Special Requests
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'textarea',
    'name' => 'special_requests',
    'label' => 'Special Requests',
    'placeholder' => 'High floor, near elevator, quiet room, etc.',
    'is_required' => false,
    'options' => ['rows' => 4],
    'order' => 10,
]);

// Parking Required
$parkingField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'switch',
    'name' => 'parking_required',
    'label' => 'I need parking',
    'is_required' => false,
    'options' => [
        'on_label' => 'Yes',
        'off_label' => 'No',
    ],
    'order' => 11,
]);

// Airport Pickup (conditional)
$airportPickupField = CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'switch',
    'name' => 'airport_pickup',
    'label' => 'Airport Pickup Service (+$50)',
    'is_required' => false,
    'options' => [
        'on_label' => 'Yes',
        'off_label' => 'No',
    ],
    'order' => 12,
]);

// Flight Details (conditional - only if airport pickup)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'text',
    'name' => 'flight_details',
    'label' => 'Flight Number & Arrival Time',
    'placeholder' => 'e.g., UA123, 3:30 PM',
    'is_required' => false,
    'conditional_logic' => [
        'action' => 'show',
        'match' => 'all',
        'conditions' => [
            [
                'target_field_id' => $airportPickupField->id,
                'operator' => 'checked',
            ],
        ],
        'conditional_validation' => [
            [
                'rule' => 'required',
                'match' => 'all',
                'conditions' => [
                    [
                        'target_field_id' => $airportPickupField->id,
                        'operator' => 'checked',
                    ],
                ],
            ],
        ],
    ],
    'order' => 13,
});

// Pickup Location (V2 Location Picker)
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'location',
    'name' => 'pickup_location',
    'label' => 'Pickup Location',
    'is_required' => false,
    'help_text' => 'Click on the map or search for your pickup address',
    'options' => [
        'default_lat' => 37.7749,       // San Francisco
        'default_lng' => -122.4194,
        'default_zoom' => 13,
        'map_height' => 300,
        'enable_search' => true,        // Show search box
        'show_coordinates' => true,     // Display lat/lng
    ],
    'conditional_logic' => [
        'action' => 'show',
        'match' => 'all',
        'conditions' => [
            [
                'target_field_id' => $airportPickupField->id,
                'operator' => 'checked',
            ],
        ],
    ],
    'order' => 14,
]);

// Payment Information Note
CustomFormField::create([
    'slick_form_id' => $form->id,
    'slick_form_layout_element_id' => $container->id,
    'field_type' => 'paragraph',
    'name' => 'payment_note',
    'show_label' => false,
    'options' => [
        'content' => '<div class="alert alert-info"><strong>Payment:</strong> Payment will be collected upon check-in. A credit card is required to hold your reservation.</div>',
    ],
    'order' => 15,
]);
```

**V2 Location Picker Field:**

The location picker integrates OpenStreetMap with interactive map selection:

```php
CustomFormField::create([
    'field_type' => 'location',
    'name' => 'location',
    'label' => 'Location',
    'options' => [
        'default_lat' => 37.7749,       // Default center latitude
        'default_lng' => -122.4194,     // Default center longitude
        'default_zoom' => 13,           // Initial zoom level (1-18)
        'map_height' => 400,            // Map height in pixels
        'enable_search' => true,        // Show location search box
        'show_coordinates' => true,     // Display lat/lng below map
    ],
]);
```

**Stored Value:**
```json
{
    "lat": 37.7749,
    "lng": -122.4194,
    "address": "123 Main St, San Francisco, CA"
}
```

---

## Additional V2 Features Not Covered

### V2 New Field Types

#### Signature Pad Field
```php
CustomFormField::create([
    'field_type' => 'signature',
    'name' => 'signature',
    'label' => 'Please Sign',
    'is_required' => true,
    'options' => [
        'canvas_width' => '100%',       // Supports px, %, rem, em
        'canvas_height' => '200',
        'pen_color' => '#000000',
        'background_color' => '#ffffff',
    ],
]);
```

#### PDF Embed Field
```php
CustomFormField::create([
    'field_type' => 'pdf_embed',
    'name' => 'terms_pdf',
    'label' => 'Terms of Service',
    'options' => [
        'pdf_url' => 'https://example.com/terms.pdf',
        'width' => '100%',
        'height' => '500',
        'input_mode' => 'url',  // 'url' or 'upload'
    ],
]);
```

#### Rating Matrix Field
```php
CustomFormField::create([
    'field_type' => 'rating_matrix',
    'name' => 'feature_ratings',
    'label' => 'Rate Our Features',
    'options' => [
        'rows' => [
            ['label' => 'Ease of Use', 'value' => 'ease_of_use'],
            ['label' => 'Documentation', 'value' => 'documentation'],
            ['label' => 'Performance', 'value' => 'performance'],
        ],
        'columns' => [
            ['label' => 'Poor', 'value' => '1'],
            ['label' => 'Fair', 'value' => '2'],
            ['label' => 'Good', 'value' => '3'],
            ['label' => 'Excellent', 'value' => '4'],
        ],
        'input_type' => 'radio',  // 'radio' or 'select'
        'allow_na' => true,        // Allow N/A option
    ],
]);
```

### Carousel with Presets

See `ShowcaseFormSeeder.php` for a complete carousel example using the Album Gallery preset with 7 image slides.

### Table Layout

```php
$table = SlickFormLayoutElement::create([
    'element_type' => 'table',
    'settings' => [
        'striped' => true,
        'bordered' => true,
        'hoverable' => true,
        'responsive' => true,
    ],
]);

$header = SlickFormLayoutElement::create([
    'parent_id' => $table->id,
    'element_type' => 'table_header',
]);

$headerRow = SlickFormLayoutElement::create([
    'parent_id' => $header->id,
    'element_type' => 'table_row',
]);

$headerCell = SlickFormLayoutElement::create([
    'parent_id' => $headerRow->id,
    'element_type' => 'table_cell',
    'settings' => ['cell_type' => 'th'],
]);
```

---

## V2 Best Practices

1. **Always use `target_field_id`** with the database field ID, never field names
2. **Use `match` not `logic`** for conditional logic
3. **Layout elements use `settings`** not `options`
4. **Column widths** go in layout element `settings`, not field `options`
5. **Repeater uses `min_items/max_items`** not `min_rows/max_rows`
6. **Tabs and accordion items** must have correct `element_type` (`tab`, `accordion_item`)
7. **Add `default` property** to all select/radio/checkbox values
8. **Use conditional validation** to make fields required based on conditions
9. **Enable multiple file uploads** with `multiple: true` and `max_files` options

---

## Need Help?

Check the official documentation:
- [Conditional Logic Guide](CONDITIONAL_LOGIC.md)
- [Field Types Reference](FIELD_TYPES.md)
- [Layout Elements Guide](LAYOUT_ELEMENTS.md)
- [Validation Rules](VALIDATION.md)

All examples use V2-compatible syntax verified against `ShowcaseFormSeeder.php`.
