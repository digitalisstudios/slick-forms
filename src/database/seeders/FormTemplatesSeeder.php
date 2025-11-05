<?php

namespace DigitalisStudios\SlickForms\Database\Seeders;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use DigitalisStudios\SlickForms\Models\SlickFormPage;
use Illuminate\Database\Seeder;

class FormTemplatesSeeder extends Seeder
{
    /**
     * Seed the application's database with form templates.
     */
    public function run(): void
    {
        // Clear existing templates
        CustomForm::where('is_template', true)->delete();

        // Real-world examples from docs/EXAMPLES.md (10 templates)
        $this->createContactFormTemplate();                    // 1. Simple Contact Form
        $this->createLeadCaptureTemplate();                    // 2. Lead Capture with Conditional Logic
        $this->createMultiStepRegistrationTemplate();          // 3. Multi-Step Registration with Tabs
        $this->createJobApplicationTemplate();                 // 4. Job Application Form
        $this->createEventRegistrationTemplate();              // 5. Event Registration with Pricing
        $this->createCustomerSurveyTemplate();                 // 6. Customer Satisfaction Survey
        $this->createProductOrderTemplate();                   // 7. Product Order Form with Repeater
        $this->createSupportTicketTemplate();                  // 8. Support Ticket Form
        $this->createNewsletterSignupTemplate();               // 9. Newsletter Signup with Preferences
        $this->createBookingFormTemplate();                    // 10. Booking Form with Date Range
    }

    /**
     * FORM 8: Support Ticket Form
     */
    protected function createSupportTicketTemplate(): void
    {
        $form = CustomForm::create([
            'name' => 'Support Ticket Form',
            'description' => 'Submit a support ticket with category and priority.',
            'is_active' => false,
            'is_template' => true,
            'template_category' => 'other',
            'template_description' => 'Subject, category, priority, and issue description with contact info.',
        ]);

        $container = $form->layoutElements()->create([
            'element_type' => 'container',
            'element_id' => 'support_container',
            'order' => 0,
            // class set after create to avoid mass-assignment edge cases
        ]);
        $container->class = 'pt-4 pb-5';
        $container->save();

        // Visual header to enhance appearance
        $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'image',
            'name' => 'support_header_image',
            'element_id' => 'support_header_image',
            'label' => 'Header',
            'show_label' => false,
            'order' => -1,
            'options' => [
                'input_mode' => 'url',
                'image_url' => 'https://picsum.photos/seed/slick-forms-support/1200/220',
                'alt_text' => 'Support Banner',
                'object_fit' => 'cover',
            ],
        ]);

        // Row 1: Subject + Email
        $row1 = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'support_row_1',
            'order' => 0,
            'settings' => ['gutter' => 'g-3'],
        ]);

        $col1 = $row1->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'support_subject_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $col1->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'subject',
            'element_id' => 'subject',
            'label' => 'Subject',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'max:150'],
            'options' => [
                'floating_label' => true,
                'placeholder' => 'Brief summary of the issue',
            ],
        ]);

        $col2 = $row1->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'support_email_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $col2->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'email',
            'name' => 'email',
            'element_id' => 'email',
            'label' => 'Contact Email',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'email'],
            'options' => ['floating_label' => true, 'placeholder' => 'you@example.com'],
        ]);

        // Row 2: Category + Priority
        $row2 = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'support_row_2',
            'order' => 1,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $catCol = $row2->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'support_category_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $catCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'select',
            'name' => 'category',
            'element_id' => 'category',
            'label' => 'Category',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required'],
            'options' => [
                'searchable' => true,
                'placeholder' => 'Choose a category...',
                'values' => [
                    ['label' => 'Bug', 'value' => 'bug', 'default' => true],
                    ['label' => 'Feature Request', 'value' => 'feature'],
                    ['label' => 'Question', 'value' => 'question'],
                    ['label' => 'Other', 'value' => 'other'],
                ],
            ],
        ]);
        $priCol = $row2->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'support_priority_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $priCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'select',
            'name' => 'priority',
            'element_id' => 'priority',
            'label' => 'Priority',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required'],
            'options' => [
                'searchable' => true,
                'values' => [
                    ['label' => 'Low', 'value' => 'low'],
                    ['label' => 'Medium', 'value' => 'medium', 'default' => true],
                    ['label' => 'High', 'value' => 'high'],
                    ['label' => 'Critical', 'value' => 'critical'],
                ],
            ],
        ]);

        // Row 3: Description (full width)
        $row3 = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'support_row_3',
            'order' => 2,
        ]);
        $descCol = $row3->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'support_desc_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 12]],
        ]);
        $descCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'textarea',
            'name' => 'issue_description',
            'element_id' => 'issue_description',
            'label' => 'Issue Description',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'min:10', 'max:5000'],
            'options' => ['rows' => 6, 'floating_label' => true, 'placeholder' => 'Describe the problem and steps to reproduce...'],
        ]);
    }

    /**
     * FORM 1: Professional Contact Form
     * Simple, conversion-optimized contact form for business websites
     */
    protected function createContactFormTemplate(): void
    {
        $form = CustomForm::create([
            'name' => 'Simple Contact Form',
            'description' => 'A clean, conversion-optimized contact form perfect for business websites. Includes all essential fields with smart validation.',
            'is_active' => false,
            'is_template' => true,
            'template_category' => 'contact',
            'template_description' => 'Collect inquiries from website visitors with this professional contact form. Features subject categorization, optional phone field with country selector, and a clear message area with newsletter opt-in.',
        ]);

        // Create container
        $container = $form->layoutElements()->create([
            'element_type' => 'container',
            'element_id' => 'contact_container',
            'order' => 0,
            // class set after create to avoid mass-assignment edge cases
        ]);
        $container->class = 'pt-4 pb-5';
        $container->save();

        // Header image sits at top; intro moved into card below

        // Subtle header image to add visual polish (placed first for hierarchy)
        $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'image',
            'name' => 'contact_header_image',
            'element_id' => 'contact_header_image',
            'label' => 'Header',
            'show_label' => false,
            'order' => -1,
            'options' => [
                'input_mode' => 'url',
                'image_url' => 'https://picsum.photos/seed/slick-forms-contact/1800/240',
                'alt_text' => 'Contact Banner',
                'object_fit' => 'cover',
            ],
        ]);

        // Contact Information Card
        $card = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'contact_info_card',
            'order' => 1,
            'settings' => [
                'title' => 'Contact Information',
            ],
        ]);
        $card->class = 'shadow-sm rounded-3';
        $card->save();

        // Intro row (inside card)
        $introRow = $card->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'contact_intro_row',
            'order' => 0,
        ]);
        $introCol = $introRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'contact_intro_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 12]],
        ]);
        $introCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'paragraph',
            'name' => 'intro_text',
            'element_id' => 'intro_text',
            'label' => 'Thank you for your interest',
            'order' => 0,
            'options' => [
                'content' => '<p>Thank you for your interest! Please fill out the form below and we\'ll get back to you as soon as possible.</p>',
            ],
        ]);

        // Name row (2 columns)
        $nameRow = $card->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'name_row',
            'order' => 1,
        ]);

        $firstNameCol = $nameRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'first_name_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $firstNameCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'first_name',
            'element_id' => 'first_name',
            'label' => 'First Name',
            'placeholder' => 'First name',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'min:2', 'max:50'],
            'options' => ['floating_label' => true],
        ]);

        $lastNameCol = $nameRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'last_name_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $lastNameCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'last_name',
            'element_id' => 'last_name',
            'label' => 'Last Name',
            'placeholder' => 'Last name',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'min:2', 'max:50'],
            'options' => ['floating_label' => true],
        ]);

        // Row 2 (card): Email + Phone + Opt-In (3 equal)
        $contactRow2 = $card->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'contact_row_2',
            'order' => 2,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $emailCol2 = $contactRow2->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'email_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 'equal']],
        ]);
        $emailCol2->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'email',
            'name' => 'email',
            'element_id' => 'email',
            'label' => 'Email Address',
            'placeholder' => 'john@example.com',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'email', 'max:255'],
            'options' => ['floating_label' => true],
        ]);
        $phoneCol2 = $contactRow2->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'phone_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 'equal']],
        ]);
        $phoneCol2->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'phone',
            'name' => 'phone',
            'element_id' => 'phone',
            'label' => 'Phone Number',
            'help_text' => 'Optional - We\'ll only call if necessary',
            'placeholder' => '(555) 123-4567',
            'is_required' => false,
            'order' => 0,
            'validation_rules' => ['max:20'],
            'options' => [
                'format' => 'us',
                'mask' => ['enabled' => true, 'type' => 'phone_us'],
                'floating_label' => true,
            ],
        ]);

        // Third column with inline newsletter opt-in
        $optinCol = $contactRow2->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'optin_col',
            'order' => 2,
            'settings' => ['width' => ['md' => 'equal']],
        ]);
        $optinCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'switch',
            'name' => 'opt_in_newsletter',
            'element_id' => 'opt_in_newsletter',
            'label' => 'Subscribe to our newsletter for updates and tips',
            'is_required' => false,
            'order' => 0,
            'options' => [
                'on_label' => 'Yes, keep me updated',
                'off_label' => 'No thanks',
                'show_labels' => true,
            ],
        ]);

        if (property_exists($this, 'command') && $this->command) {
            $this->command->info('  • Contact: intro row + name_row + contact_row_2 (3 equal cols with opt-in)');
        }

        // Inquiry type select
        $card->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'select',
            'name' => 'inquiry_type',
            'element_id' => 'inquiry_type',
            'label' => 'How can we help you?',
            'is_required' => true,
            'order' => 3,
            'validation_rules' => ['required'],
            'options' => [
                'searchable' => true,
                'placeholder' => 'Select a topic...',
                'values' => [
                    ['label' => 'General Question', 'value' => 'general', 'default' => true],
                    ['label' => 'Sales Inquiry', 'value' => 'sales', 'default' => false],
                    ['label' => 'Technical Support', 'value' => 'support', 'default' => false],
                    ['label' => 'Partnership Opportunity', 'value' => 'partnership', 'default' => false],
                    ['label' => 'Feedback', 'value' => 'feedback', 'default' => false],
                ],
            ],
        ]);

        // Message textarea
        $card->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'textarea',
            'name' => 'message',
            'element_id' => 'message',
            'label' => 'Message',
            'placeholder' => 'Please tell us how we can help you...',
            'is_required' => true,
            'order' => 4,
            'validation_rules' => ['required', 'min:10', 'max:2000'],
            'options' => [
                'rows' => 6,
                'floating_label' => true,
            ],
        ]);

        // Opt-in moved into contact_row_2
    }

    /**
     * FORM 2: Newsletter & Lead Magnet Signup
     * High-converting lead capture form with optional lead magnet delivery
     */
    protected function createNewsletterSignupTemplate(): void
    {
        $form = CustomForm::create([
            'name' => 'Newsletter Signup with Preferences',
            'description' => 'High-converting lead capture form with optional lead magnet delivery. Perfect for growing your email list.',
            'is_active' => false,
            'is_template' => true,
            'template_category' => 'survey',
            'template_description' => 'Capture email subscribers with this optimized signup form. Includes interest selection, lead magnet options, and GDPR-compliant consent with conditional field display.',
        ]);

        $container = $form->layoutElements()->create([
            'element_type' => 'container',
            'element_id' => 'newsletter_container',
            'order' => 0,
            // class set after create to avoid mass-assignment edge cases
        ]);
        $container->class = 'pt-4 pb-5';
        $container->save();

        // Header
        $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'header',
            'name' => 'header',
            'element_id' => 'header',
            'label' => 'Join Our Community',
            'order' => 0,
            'options' => [
                'heading_level' => 'h2',
                'content' => 'Join Our Community',
                'text_alignment' => ['align' => 'center'],
            ],
        ]);

        // Intro paragraph
        $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'paragraph',
            'name' => 'intro',
            'element_id' => 'intro',
            'label' => 'Introduction',
            'order' => 1,
            'options' => [
                'content' => '<p class="text-center">Get exclusive tips, resources, and updates delivered straight to your inbox. Plus, choose a free welcome gift!</p>',
            ],
        ]);

        // Signup card
        $card = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'signup_card',
            'order' => 2,
            'settings' => [
                'title' => 'Sign Up Now',
            ],
        ]);
        $card->class = 'shadow-sm rounded-3';
        $card->save();

        // Name row
        $nameRow = $card->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'name_row',
            'order' => 0,
        ]);

        $firstNameCol = $nameRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'first_name_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $firstNameCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'first_name',
            'element_id' => 'first_name',
            'label' => 'First Name',
            'placeholder' => 'First name',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'min:2', 'max:50'],
            'options' => ['floating_label' => true],
        ]);

        $lastNameCol = $nameRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'last_name_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $lastNameCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'last_name',
            'element_id' => 'last_name',
            'label' => 'Last Name',
            'placeholder' => 'Last name',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'min:2', 'max:50'],
            'options' => ['floating_label' => true],
        ]);

        // Email + Lead magnet side by side (card-level to preserve order during cloning)
        $nlRow = $card->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'newsletter_row_email_leadmagnet',
            'order' => 1,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $nlEmailCol = $nlRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'newsletter_email_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $nlEmailCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'email',
            'name' => 'email',
            'element_id' => 'email',
            'label' => 'Email Address',
            'placeholder' => 'your@email.com',
            'help_text' => 'We\'ll never share your email with anyone else',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'email', 'max:255'],
            'options' => ['floating_label' => true],
        ]);
        $nlLeadCol = $nlRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'newsletter_leadmagnet_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $leadMagnetField = $nlLeadCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'select',
            'name' => 'lead_magnet',
            'element_id' => 'lead_magnet',
            'label' => 'Choose Your Free Resource',
            'help_text' => 'Select the resource you\'d like to receive',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required'],
            'options' => [
                'searchable' => true,
                'values' => [
                    ['label' => 'Free Ebook: Getting Started Guide', 'value' => 'ebook', 'default' => true],
                    ['label' => 'Video Course: Fundamentals', 'value' => 'video_course', 'default' => false],
                    ['label' => 'Downloadable Checklist', 'value' => 'checklist', 'default' => false],
                    ['label' => 'Template Pack', 'value' => 'templates', 'default' => false],
                    ['label' => 'Case Study Collection', 'value' => 'case_studies', 'default' => false],
                ],
            ],
        ]);

        if (property_exists($this, 'command') && $this->command) {
            $this->command->info('  • Newsletter: added newsletter_row_email_leadmagnet (md-6/md-6)');
        }

        // Interest checkboxes
        $card->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'checkbox',
            'name' => 'interests',
            'element_id' => 'interests',
            'label' => 'What topics interest you?',
            'help_text' => 'Select all that apply',
            'is_required' => false,
            'order' => 3,
            'options' => [
                'layout' => 'horizontal',
                'values' => [
                    ['label' => 'Tips & Tricks', 'value' => 'tips', 'default' => false],
                    ['label' => 'Product Updates', 'value' => 'updates', 'default' => false],
                    ['label' => 'Industry News', 'value' => 'news', 'default' => false],
                    ['label' => 'Special Offers', 'value' => 'offers', 'default' => false],
                ],
            ],
        ]);

        // Email frequency
        $card->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'radio',
            'name' => 'frequency',
            'element_id' => 'frequency',
            'label' => 'How often would you like to hear from us?',
            'is_required' => true,
            'order' => 4,
            'validation_rules' => ['required'],
            'options' => [
                'layout' => 'horizontal',
                'values' => [
                    ['label' => 'Daily Digest', 'value' => 'daily', 'default' => false],
                    ['label' => 'Weekly', 'value' => 'weekly', 'default' => true],
                    ['label' => 'Monthly', 'value' => 'monthly', 'default' => false],
                ],
            ],
        ]);

        // GDPR consent
        $card->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'switch',
            'name' => 'consent_marketing',
            'element_id' => 'consent_marketing',
            'label' => 'I agree to receive marketing emails and understand I can unsubscribe at any time',
            'is_required' => true,
            'order' => 5,
            'validation_rules' => ['required', 'accepted'],
        ]);
    }

    /**
     * FORM 3: Service Request / Quote Form
     * Comprehensive form for service inquiries with budget selection and project details
     */
    protected function createServiceRequestTemplate(): void
    {
        $form = CustomForm::create([
            'name' => 'Service Request & Quote Form',
            'description' => 'Comprehensive form for service inquiries with budget selection and project details. Ideal for agencies and service providers.',
            'is_active' => false,
            'is_template' => true,
            'template_category' => 'contact',
            'template_description' => 'Qualify leads and gather project requirements with this detailed quote request form. Includes service selection, budget ranges, timeline, file uploads, and conditional referral field.',
        ]);

        $container = $form->layoutElements()->create([
            'element_type' => 'container',
            'element_id' => 'quote_container',
            'order' => 0,
        ]);

        // Header
        $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'header',
            'name' => 'header',
            'element_id' => 'header',
            'label' => 'Request a Quote',
            'order' => 0,
            'options' => [
                'heading_level' => 'h2',
                'content' => 'Request a Quote',
            ],
        ]);

        // Contact Information Card
        $contactCard = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'contact_card',
            'order' => 1,
            'settings' => [
                'title' => 'Contact Information',
            ],
        ]);

        // Company/Name row
        $row1 = $contactCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'company_row',
            'order' => 0,
        ]);

        $col1 = $row1->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'company_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $col1->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'company_name',
            'element_id' => 'company_name',
            'label' => 'Company Name',
            'placeholder' => 'Acme Corporation',
            'is_required' => false,
            'order' => 0,
            'validation_rules' => ['max:100'],
        ]);

        $col2 = $row1->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'name_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $col2->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'contact_name',
            'element_id' => 'contact_name',
            'label' => 'Your Name',
            'placeholder' => 'John Doe',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'min:2', 'max:100'],
        ]);

        // Email/Phone row
        $row2 = $contactCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'contact_row',
            'order' => 1,
        ]);

        $emailCol = $row2->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'email_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $emailCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'email',
            'name' => 'email',
            'element_id' => 'email',
            'label' => 'Email Address',
            'placeholder' => 'john@example.com',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'email', 'max:255'],
        ]);

        $phoneCol = $row2->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'phone_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $phoneCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'phone',
            'name' => 'phone',
            'element_id' => 'phone',
            'label' => 'Phone Number',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'max:20'],
            'options' => [
                'show_country_selector' => true,
                'default_country' => 'US',
                'format' => 'us',
                'mask' => [
                    'enabled' => true,
                    'type' => 'phone_us',
                ],
            ],
        ]);

        // Project Details Card
        $projectCard = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'project_card',
            'order' => 2,
            'settings' => [
                'card_title' => 'Project Details',
            ],
        ]);

        // Services needed
        $projectCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'checkbox',
            'name' => 'services_needed',
            'element_id' => 'services_needed',
            'label' => 'Which services are you interested in?',
            'help_text' => 'Select all services you\'re interested in',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'array', 'min:1'],
            'options' => [
                'values' => [
                    ['label' => 'Web Design', 'value' => 'web_design', 'default' => false],
                    ['label' => 'Web Development', 'value' => 'web_dev', 'default' => false],
                    ['label' => 'SEO Optimization', 'value' => 'seo', 'default' => false],
                    ['label' => 'Content Marketing', 'value' => 'content', 'default' => false],
                    ['label' => 'Branding', 'value' => 'branding', 'default' => false],
                    ['label' => 'Consulting', 'value' => 'consulting', 'default' => false],
                ],
            ],
        ]);

        // Budget range
        $projectCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'select',
            'name' => 'budget_range',
            'element_id' => 'budget_range',
            'label' => 'Project Budget Range',
            'help_text' => 'Your estimated project budget',
            'is_required' => true,
            'order' => 1,
            'validation_rules' => ['required'],
            'options' => [
                'values' => [
                    ['label' => 'Less than $5,000', 'value' => 'under_5k', 'default' => false],
                    ['label' => '$5,000 - $10,000', 'value' => '5k_10k', 'default' => false],
                    ['label' => '$10,000 - $25,000', 'value' => '10k_25k', 'default' => true],
                    ['label' => '$25,000 - $50,000', 'value' => '25k_50k', 'default' => false],
                    ['label' => '$50,000 - $100,000', 'value' => '50k_100k', 'default' => false],
                    ['label' => 'More than $100,000', 'value' => 'over_100k', 'default' => false],
                ],
            ],
        ]);

        // Timeline
        $projectCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'select',
            'name' => 'timeline',
            'element_id' => 'timeline',
            'label' => 'Project Timeline',
            'help_text' => 'When do you need this completed?',
            'is_required' => true,
            'order' => 2,
            'validation_rules' => ['required'],
            'options' => [
                'values' => [
                    ['label' => 'ASAP (within 2 weeks)', 'value' => 'asap', 'default' => false],
                    ['label' => '1-3 months', 'value' => '1_3_months', 'default' => true],
                    ['label' => '3-6 months', 'value' => '3_6_months', 'default' => false],
                    ['label' => '6+ months', 'value' => 'over_6_months', 'default' => false],
                    ['label' => 'Flexible', 'value' => 'flexible', 'default' => false],
                ],
            ],
        ]);

        // Project description
        $projectCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'textarea',
            'name' => 'project_description',
            'element_id' => 'project_description',
            'label' => 'Project Description',
            'placeholder' => 'Please describe your project, goals, and any specific requirements...',
            'is_required' => true,
            'order' => 3,
            'validation_rules' => ['required', 'min:50', 'max:5000'],
            'options' => [
                'rows' => 6,
            ],
        ]);

        // File upload
        $projectCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'file',
            'name' => 'attachment',
            'element_id' => 'attachment',
            'label' => 'Supporting Documents',
            'help_text' => 'Upload any relevant documents (specs, mockups, etc.)',
            'is_required' => false,
            'order' => 4,
            'options' => [
                'multiple' => true,
                'accepted_types' => '.pdf,.doc,.docx,.xls,.xlsx,.zip,.jpg,.png',
                'max_size' => 10240,
            ],
        ]);

        // Additional Information Card
        $additionalCard = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'additional_card',
            'order' => 3,
            'settings' => [
                'card_title' => 'Additional Information',
            ],
        ]);

        // Website URL
        $additionalCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'url',
            'name' => 'website_url',
            'element_id' => 'website_url',
            'label' => 'Current Website (if applicable)',
            'placeholder' => 'https://www.example.com',
            'is_required' => false,
            'order' => 0,
            'validation_rules' => ['url'],
            'options' => [
                'show_preview' => true,
            ],
        ]);

        // How did you hear about us
        $heardAboutField = $additionalCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'radio',
            'name' => 'heard_about_us',
            'element_id' => 'heard_about_us',
            'label' => 'How did you hear about us?',
            'is_required' => true,
            'order' => 1,
            'validation_rules' => ['required'],
            'options' => [
                'values' => [
                    ['label' => 'Google Search', 'value' => 'google', 'default' => false],
                    ['label' => 'Social Media', 'value' => 'social', 'default' => false],
                    ['label' => 'Referral', 'value' => 'referral', 'default' => false],
                    ['label' => 'Advertisement', 'value' => 'ad', 'default' => false],
                    ['label' => 'Other', 'value' => 'other', 'default' => false],
                ],
            ],
        ]);

        // Conditional referral source field
        $additionalCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'referral_source',
            'element_id' => 'referral_source',
            'label' => 'Please specify',
            'placeholder' => 'Please tell us more...',
            'is_required' => false,
            'order' => 2,
            'validation_rules' => ['max:255'],
            'conditional_logic' => [
                'action' => 'show',
                'match' => 'any',
                'conditions' => [
                    [
                        'target_field_id' => $heardAboutField->id,
                        'operator' => 'equals',
                        'value' => 'referral',
                    ],
                    [
                        'target_field_id' => $heardAboutField->id,
                        'operator' => 'equals',
                        'value' => 'other',
                    ],
                ],
            ],
        ]);
    }

    /**
     * FORM 4: Customer Satisfaction Survey
     * Comprehensive feedback form with star ratings, NPS scoring, and detailed feedback
     */
    protected function createCustomerSurveyTemplate(): void
    {
        $form = CustomForm::create([
            'name' => 'Customer Satisfaction Survey',
            'description' => 'Comprehensive feedback form with star ratings, NPS scoring, and open-ended responses. Perfect for measuring customer satisfaction.',
            'is_active' => false,
            'is_template' => true,
            'template_category' => 'survey',
            'template_description' => 'Measure customer satisfaction with this well-structured survey. Features multiple star ratings, Net Promoter Score slider, category-specific feedback, and conditional follow-up email.',
        ]);

        $container = $form->layoutElements()->create([
            'element_type' => 'container',
            'element_id' => 'survey_container',
            'order' => 0,
        ]);
        $container->class = 'pt-4 pb-5';
        $container->save();

        // Header
        $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'header',
            'name' => 'header',
            'element_id' => 'header',
            'label' => 'We Value Your Feedback',
            'order' => 0,
            'options' => [
                'heading_level' => 'h2',
                'content' => 'We Value Your Feedback',
                'text_alignment' => ['align' => 'center'],
            ],
        ]);

        // Intro
        $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'paragraph',
            'name' => 'intro',
            'element_id' => 'intro',
            'label' => 'Introduction',
            'order' => 1,
            'options' => [
                'content' => '<p class="text-center">Thank you for taking a few minutes to complete this survey. Your feedback helps us improve our products and services.</p>',
            ],
        ]);

        // Overall Experience Card
        $overallCard = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'overall_card',
            'order' => 2,
            'settings' => [
                'title' => 'Overall Experience',
            ],
        ]);
        $overallCard->class = 'shadow-sm rounded-3';
        $overallCard->save();

        // Overall satisfaction
        $overallSatisfaction = $overallCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'star_rating',
            'name' => 'overall_satisfaction',
            'element_id' => 'overall_satisfaction',
            'label' => 'Overall Satisfaction',
            'help_text' => 'Rate your overall experience with our product/service',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'integer', 'min:1', 'max:5'],
            'options' => [
                'max_stars' => 5,
            ],
        ]);

        // NPS score
        $overallCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'slider',
            'name' => 'nps_score',
            'element_id' => 'nps_score',
            'label' => 'How likely are you to recommend us to a friend or colleague?',
            'help_text' => '0 = Not at all likely | 10 = Extremely likely',
            'is_required' => true,
            'order' => 1,
            'validation_rules' => ['required', 'integer', 'min:0', 'max:10'],
            'options' => [
                'min' => 0,
                'max' => 10,
                'step' => 1,
                'show_value' => true,
            ],
        ]);

        // Detailed Ratings Card
        $detailedCard = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'detailed_card',
            'order' => 3,
            'settings' => [
                'title' => 'Detailed Ratings',
            ],
        ]);
        $detailedCard->class = 'shadow-sm rounded-3';
        $detailedCard->save();

        // Rating categories row 1
        $row1 = $detailedCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'ratings_row_1',
            'order' => 0,
        ]);

        $col1 = $row1->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'quality_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $productQuality = $col1->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'star_rating',
            'name' => 'product_quality',
            'element_id' => 'product_quality',
            'label' => 'Product Quality',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'integer', 'min:1', 'max:5'],
            'options' => [
                'max_stars' => 5,
            ],
        ]);

        $col2 = $row1->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'service_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $customerService = $col2->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'star_rating',
            'name' => 'customer_service',
            'element_id' => 'customer_service',
            'label' => 'Customer Service',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'integer', 'min:1', 'max:5'],
            'options' => [
                'max_stars' => 5,
            ],
        ]);

        // Rating categories row 2
        $row2 = $detailedCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'ratings_row_2',
            'order' => 1,
        ]);

        $col3 = $row2->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'value_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $valueForMoney = $col3->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'star_rating',
            'name' => 'value_for_money',
            'element_id' => 'value_for_money',
            'label' => 'Value for Money',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'integer', 'min:1', 'max:5'],
            'options' => [
                'max_stars' => 5,
            ],
        ]);

        $col4 = $row2->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'ease_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $easeOfUse = $col4->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'star_rating',
            'name' => 'ease_of_use',
            'element_id' => 'ease_of_use',
            'label' => 'Ease of Use',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'integer', 'min:1', 'max:5'],
            'options' => [
                'max_stars' => 5,
            ],
        ]);

        // Open Feedback Card
        $feedbackCard = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'feedback_card',
            'order' => 4,
            'settings' => [
                'title' => 'Your Feedback',
            ],
        ]);
        $feedbackCard->class = 'shadow-sm rounded-3';
        $feedbackCard->save();

        $feedbackCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'textarea',
            'name' => 'what_we_did_well',
            'element_id' => 'what_we_did_well',
            'label' => 'What did we do well?',
            'placeholder' => 'What aspects of our service did you appreciate?',
            'is_required' => false,
            'order' => 0,
            'validation_rules' => ['max:2000'],
            'options' => [
                'rows' => 4,
            ],
        ]);

        $feedbackCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'textarea',
            'name' => 'what_needs_improvement',
            'element_id' => 'what_needs_improvement',
            'label' => 'What could we improve?',
            'placeholder' => 'What could we do better?',
            'is_required' => false,
            'order' => 1,
            'validation_rules' => ['max:2000'],
            'options' => [
                'rows' => 4,
            ],
        ]);

        $feedbackCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'textarea',
            'name' => 'additional_comments',
            'element_id' => 'additional_comments',
            'label' => 'Additional Comments',
            'placeholder' => 'Any other feedback you\'d like to share?',
            'is_required' => false,
            'order' => 2,
            'validation_rules' => ['max:2000'],
            'options' => [
                'rows' => 4,
            ],
        ]);

        // Follow-up Card
        $followupCard = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'followup_card',
            'order' => 5,
            'settings' => [
                'title' => 'Follow-up',
            ],
        ]);
        $followupCard->class = 'shadow-sm rounded-3';
        $followupCard->save();

        $mayContactSwitch = $followupCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'switch',
            'name' => 'may_we_contact',
            'element_id' => 'may_we_contact',
            'label' => 'May we contact you to discuss your feedback in more detail?',
            'is_required' => false,
            'order' => 0,
            'options' => [
                'on_label' => 'Yes, please contact me',
                'off_label' => 'No thanks',
                'show_labels' => true,
            ],
        ]);

        $followupCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'email',
            'name' => 'follow_up_email',
            'element_id' => 'follow_up_email',
            'label' => 'Email Address',
            'placeholder' => 'your@email.com',
            'is_required' => false,
            'order' => 1,
            'validation_rules' => ['email', 'max:255'],
            'options' => ['floating_label' => true],
            'conditional_logic' => [
                'action' => 'show',
                'match' => 'all',
                'conditions' => [
                    [
                        'target_field_id' => $mayContactSwitch->id,
                        'operator' => 'checked',
                        'value' => true,
                    ],
                ],
            ],
        ]);

        // Hidden calculation field - average satisfaction score
        $followupCard->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'calculation',
            'name' => 'satisfaction_score',
            'element_id' => 'satisfaction_score',
            'label' => 'Average Satisfaction Score',
            'is_required' => false,
            'order' => 2,
            'options' => [
                'formula' => "({{$overallSatisfaction->name}} + {{$productQuality->name}} + {{$customerService->name}} + {{$valueForMoney->name}} + {{$easeOfUse->name}}) / 5",
                'display_format' => 'number',
                'decimal_places' => 2,
                'display_mode' => 'visible',
            ],
        ]);
    }

    /**
     * FORM 5: Event Registration & Booking (MULTI-PAGE)
     * Professional multi-page event registration with ticket selection and attendee information
     */
    protected function createEventRegistrationTemplate(): void
    {
        $form = CustomForm::create([
            'name' => 'Event Registration with Pricing',
            'description' => 'Professional multi-page event registration form with ticket selection, attendee information, and special requirements. Perfect for conferences, workshops, and events.',
            'is_active' => false,
            'is_template' => true,
            'template_category' => 'contact',
            'template_description' => 'Streamline event registrations with this comprehensive multi-page form. Features progress steps, ticket type selection, repeater fields for group registration, dietary preferences, and payment acceptance.',
            'settings' => [
                'multi_page_enabled' => true,
                'progress_style' => 'steps',
                'allow_back_navigation' => true,
            ],
        ]);

        // Page 1: Event & Ticket Selection
        $page1 = SlickFormPage::create([
            'slick_form_id' => $form->id,
            'title' => 'Event Selection',
            'description' => 'Choose your event session and ticket type',
            'order' => 0,
            'icon' => 'bi-calendar-event',
            'show_in_progress' => true,
        ]);

        $container1 = $form->layoutElements()->create([
            'slick_form_page_id' => $page1->id,
            'element_type' => 'container',
            'element_id' => 'event_selection_container',
            'order' => 0,
        ]);
        $container1->class = 'pt-4 pb-5';
        $container1->save();

        $container1->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page1->id,
            'field_type' => 'header',
            'name' => 'event_header',
            'element_id' => 'event_header',
            'label' => 'Event Registration',
            'order' => 0,
            'options' => [
                'heading_level' => 'h2',
                'content' => 'Event Registration',
            ],
        ]);

        $container1->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page1->id,
            'field_type' => 'paragraph',
            'name' => 'event_intro',
            'element_id' => 'event_intro',
            'label' => 'Introduction',
            'order' => 1,
            'options' => [
                'content' => '<p>Register for our upcoming event. Select your preferred session time and ticket type below.</p>',
            ],
        ]);

        $card1 = $container1->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page1->id,
            'element_type' => 'card',
            'element_id' => 'event_card',
            'order' => 2,
            'settings' => [
                'title' => 'Event Details',
            ],
        ]);
        $card1->class = 'shadow-sm rounded-3';
        $card1->save();

        $card1->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page1->id,
            'field_type' => 'select',
            'name' => 'event_session',
            'element_id' => 'event_session',
            'label' => 'Preferred Session',
            'help_text' => 'Choose your preferred session time',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required'],
            'options' => [
                'values' => [
                    ['label' => 'Morning Session (9AM-12PM)', 'value' => 'morning', 'default' => false],
                    ['label' => 'Afternoon Session (1PM-4PM)', 'value' => 'afternoon', 'default' => false],
                    ['label' => 'Full Day (9AM-4PM)', 'value' => 'full_day', 'default' => true],
                    ['label' => 'Evening Networking (5PM-8PM)', 'value' => 'evening', 'default' => false],
                ],
            ],
        ]);

        $ticketTypeField = $card1->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page1->id,
            'field_type' => 'radio',
            'name' => 'ticket_type',
            'element_id' => 'ticket_type',
            'label' => 'Ticket Type',
            'help_text' => 'Select your ticket type',
            'is_required' => true,
            'order' => 1,
            'validation_rules' => ['required'],
            'options' => [
                'values' => [
                    ['label' => 'Early Bird - $75', 'value' => 'early_bird', 'default' => false],
                    ['label' => 'Standard - $99', 'value' => 'standard', 'default' => true],
                    ['label' => 'VIP - $149', 'value' => 'vip', 'default' => false],
                    ['label' => 'Group (5+) - $85/person', 'value' => 'group', 'default' => false],
                ],
            ],
        ]);

        $numTicketsField = $card1->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page1->id,
            'field_type' => 'number',
            'name' => 'number_of_tickets',
            'element_id' => 'number_of_tickets',
            'label' => 'Number of Tickets',
            'help_text' => 'Minimum 5 tickets for group rate',
            'is_required' => false,
            'order' => 2,
            'validation_rules' => ['integer', 'min:5', 'max:50'],
            'options' => [
                'min' => 5,
                'max' => 50,
                'step' => 1,
            ],
            'conditional_logic' => [
                'action' => 'show',
                'match' => 'all',
                'conditions' => [
                    [
                        'target_field_id' => $ticketTypeField->id,
                        'operator' => 'equals',
                        'value' => 'group',
                    ],
                ],
            ],
        ]);

        // Page 2: Primary Attendee Information
        $page2 = SlickFormPage::create([
            'slick_form_id' => $form->id,
            'title' => 'Your Information',
            'description' => 'Tell us about yourself',
            'order' => 1,
            'icon' => 'bi-person',
            'show_in_progress' => true,
        ]);

        $container2 = $form->layoutElements()->create([
            'slick_form_page_id' => $page2->id,
            'element_type' => 'container',
            'element_id' => 'attendee_container',
            'order' => 0,
        ]);
        $container2->class = 'pt-4 pb-5';
        $container2->save();

        $card2 = $container2->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'element_type' => 'card',
            'element_id' => 'primary_attendee_card',
            'order' => 0,
            'settings' => [
                'title' => 'Primary Attendee Information',
            ],
        ]);
        $card2->class = 'shadow-sm rounded-3';
        $card2->save();

        $nameRow = $card2->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'element_type' => 'row',
            'element_id' => 'name_row',
            'order' => 0,
        ]);

        $firstCol = $nameRow->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'element_type' => 'column',
            'element_id' => 'first_name_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $firstCol->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'field_type' => 'text',
            'name' => 'first_name',
            'element_id' => 'first_name',
            'label' => 'First Name',
            'placeholder' => 'First name',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'min:2', 'max:50'],
            'options' => ['floating_label' => true],
        ]);

        $lastCol = $nameRow->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'element_type' => 'column',
            'element_id' => 'last_name_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $lastCol->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'field_type' => 'text',
            'name' => 'last_name',
            'element_id' => 'last_name',
            'label' => 'Last Name',
            'placeholder' => 'Last name',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'min:2', 'max:50'],
            'options' => ['floating_label' => true],
        ]);

        $contactRow = $card2->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'element_type' => 'row',
            'element_id' => 'contact_row',
            'order' => 1,
        ]);

        $emailCol = $contactRow->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'element_type' => 'column',
            'element_id' => 'email_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $emailCol->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'field_type' => 'email',
            'name' => 'email',
            'element_id' => 'email',
            'label' => 'Email Address',
            'placeholder' => 'john@example.com',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'email', 'max:255'],
            'options' => ['floating_label' => true],
        ]);

        $phoneCol = $contactRow->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'element_type' => 'column',
            'element_id' => 'phone_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);

        $phoneCol->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'field_type' => 'phone',
            'name' => 'phone',
            'element_id' => 'phone',
            'label' => 'Phone Number',
            'placeholder' => '(555) 123-4567',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'max:20'],
            'options' => [
                'format' => 'us',
                'mask' => ['enabled' => true, 'type' => 'phone_us'],
                'floating_label' => true,
            ],
        ]);

        $companyRow = $card2->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'element_type' => 'row',
            'element_id' => 'company_row',
            'order' => 2,
        ]);

        $companyCol = $companyRow->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'element_type' => 'column',
            'element_id' => 'company_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 8]],
        ]);

        $companyCol->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'field_type' => 'text',
            'name' => 'company_organization',
            'element_id' => 'company_organization',
            'label' => 'Company/Organization',
            'placeholder' => 'Acme Corporation',
            'is_required' => false,
            'order' => 0,
            'validation_rules' => ['max:100'],
        ]);

        $jobCol = $companyRow->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'element_type' => 'column',
            'element_id' => 'job_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 4]],
        ]);

        $jobCol->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'field_type' => 'text',
            'name' => 'job_title',
            'element_id' => 'job_title',
            'label' => 'Job Title',
            'placeholder' => 'Manager',
            'is_required' => false,
            'order' => 0,
            'validation_rules' => ['max:100'],
        ]);

        // Page 3: Preferences & Requirements
        $page3 = SlickFormPage::create([
            'slick_form_id' => $form->id,
            'title' => 'Preferences',
            'description' => 'Workshop selection and special requirements',
            'order' => 2,
            'icon' => 'bi-gear',
            'show_in_progress' => true,
        ]);

        $container3 = $form->layoutElements()->create([
            'slick_form_page_id' => $page3->id,
            'element_type' => 'container',
            'element_id' => 'preferences_container',
            'order' => 0,
        ]);

        $workshopCard = $container3->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page3->id,
            'element_type' => 'card',
            'element_id' => 'workshop_card',
            'order' => 0,
            'settings' => [
                'title' => 'Workshop Selection',
            ],
        ]);
        $workshopCard->class = 'shadow-sm rounded-3';
        $workshopCard->save();

        $workshopCard->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page3->id,
            'field_type' => 'checkbox',
            'name' => 'workshop_interests',
            'element_id' => 'workshop_interests',
            'label' => 'Which workshops would you like to attend?',
            'help_text' => 'Select all that interest you (space permitting)',
            'is_required' => false,
            'order' => 0,
            'options' => [
                'values' => [
                    ['label' => 'Introduction to Topic A', 'value' => 'intro_a', 'default' => false],
                    ['label' => 'Advanced Topic B', 'value' => 'advanced_b', 'default' => false],
                    ['label' => 'Hands-on Workshop C', 'value' => 'workshop_c', 'default' => false],
                    ['label' => 'Panel Discussion D', 'value' => 'panel_d', 'default' => false],
                    ['label' => 'Networking Lunch', 'value' => 'networking', 'default' => false],
                ],
            ],
        ]);

        $dietaryCard = $container3->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page3->id,
            'element_type' => 'card',
            'element_id' => 'dietary_card',
            'order' => 1,
            'settings' => [
                'title' => 'Dietary & Accessibility',
            ],
        ]);
        $dietaryCard->class = 'shadow-sm rounded-3';
        $dietaryCard->save();

        $dietaryField = $dietaryCard->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page3->id,
            'field_type' => 'checkbox',
            'name' => 'dietary_requirements',
            'element_id' => 'dietary_requirements',
            'label' => 'Dietary Requirements',
            'help_text' => 'Select all that apply',
            'is_required' => false,
            'order' => 0,
            'options' => [
                'layout' => 'horizontal',
                'values' => [
                    ['label' => 'Vegetarian', 'value' => 'vegetarian', 'default' => false],
                    ['label' => 'Vegan', 'value' => 'vegan', 'default' => false],
                    ['label' => 'Gluten-Free', 'value' => 'gluten_free', 'default' => false],
                    ['label' => 'Dairy-Free', 'value' => 'dairy_free', 'default' => false],
                    ['label' => 'Nut Allergy', 'value' => 'nut_allergy', 'default' => false],
                    ['label' => 'Halal', 'value' => 'halal', 'default' => false],
                    ['label' => 'Kosher', 'value' => 'kosher', 'default' => false],
                ],
            ],
        ]);

        $dietaryCard->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page3->id,
            'field_type' => 'textarea',
            'name' => 'special_requirements',
            'element_id' => 'special_requirements',
            'label' => 'Special Requirements',
            'placeholder' => 'Any special requests or requirements?',
            'is_required' => false,
            'order' => 1,
            'validation_rules' => ['max:1000'],
            'options' => [
                'rows' => 3,
            ],
        ]);

        $dietaryCard->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page3->id,
            'field_type' => 'textarea',
            'name' => 'accessibility_needs',
            'element_id' => 'accessibility_needs',
            'label' => 'Accessibility Needs',
            'placeholder' => 'Do you need any accessibility accommodations?',
            'is_required' => false,
            'order' => 2,
            'validation_rules' => ['max:1000'],
            'options' => [
                'rows' => 3,
            ],
        ]);

        // Page 4: Confirmation
        $page4 = SlickFormPage::create([
            'slick_form_id' => $form->id,
            'title' => 'Confirmation',
            'description' => 'Review and confirm your registration',
            'order' => 3,
            'icon' => 'bi-check-circle',
            'show_in_progress' => true,
        ]);

        $container4 = $form->layoutElements()->create([
            'slick_form_page_id' => $page4->id,
            'element_type' => 'container',
            'element_id' => 'confirmation_container',
            'order' => 0,
        ]);
        $container4->class = 'pt-4 pb-5';
        $container4->save();

        $summaryCard = $container4->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page4->id,
            'element_type' => 'card',
            'element_id' => 'summary_card',
            'order' => 0,
            'settings' => [
                'title' => 'Registration Summary',
            ],
        ]);
        $summaryCard->class = 'shadow-sm rounded-3';
        $summaryCard->save();

        $summaryCard->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page4->id,
            'field_type' => 'paragraph',
            'name' => 'summary_text',
            'element_id' => 'summary_text',
            'label' => 'Summary',
            'order' => 0,
            'options' => [
                'content' => '<p><strong>Almost done!</strong> Please review your information and accept our policies below to complete your registration.</p>',
            ],
        ]);

        $policyCard = $container4->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page4->id,
            'element_type' => 'card',
            'element_id' => 'policy_card',
            'order' => 1,
            'settings' => [
                'title' => 'Terms & Policies',
            ],
        ]);
        $policyCard->class = 'shadow-sm rounded-3';
        $policyCard->save();

        $policyCard->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page4->id,
            'field_type' => 'switch',
            'name' => 'agree_to_terms',
            'element_id' => 'agree_to_terms',
            'label' => 'I agree to the event terms and conditions',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'accepted'],
        ]);

        $policyCard->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page4->id,
            'field_type' => 'switch',
            'name' => 'agree_to_cancellation_policy',
            'element_id' => 'agree_to_cancellation_policy',
            'label' => 'I understand the cancellation policy (No refunds within 7 days of event)',
            'is_required' => true,
            'order' => 1,
            'validation_rules' => ['required', 'accepted'],
        ]);

        $policyCard->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page4->id,
            'field_type' => 'paragraph',
            'name' => 'payment_note',
            'element_id' => 'payment_note',
            'label' => 'Payment Information',
            'order' => 2,
            'options' => [
                'content' => '<p class="text-muted"><small>Payment information will be collected after form submission via secure payment link sent to your email.</small></p>',
            ],
        ]);
    }

    /**
     * FORM 2: Lead Capture with Conditional Logic
     */
    protected function createLeadCaptureTemplate(): void
    {
        $form = CustomForm::create([
            'name' => 'Lead Capture with Conditional Logic',
            'description' => 'Marketing lead form that shows company fields for business accounts.',
            'is_active' => false,
            'is_template' => true,
            'template_category' => 'lead',
            'template_description' => 'Collect leads with conditional company fields, budget slider, and interests.',
        ]);

        $container = $form->layoutElements()->create([
            'element_type' => 'container',
            'element_id' => 'lead_container',
            'order' => 0,
            // class set after create to avoid mass-assignment edge cases
        ]);
        $container->class = 'pt-4 pb-5';
        $container->save();

        // Visual header to elevate look
        $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'image',
            'name' => 'lead_header_image',
            'element_id' => 'lead_header_image',
            'label' => 'Header',
            'show_label' => false,
            'order' => -1,
            'options' => [
                'input_mode' => 'url',
                'image_url' => 'https://picsum.photos/seed/slick-forms-lead/1200/260',
                'alt_text' => 'Lead Banner',
                'object_fit' => 'cover',
            ],
        ]);

        $card = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'lead_card',
            'order' => 0,
            'settings' => ['title' => 'Tell us about you'],
        ]);
        $card->class = 'shadow-sm rounded-3';
        $card->save();

        // Account type
        $accountType = $card->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'radio',
            'name' => 'account_type',
            'element_id' => 'account_type',
            'label' => 'Account Type',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required'],
            'options' => [
                'values' => [
                    ['label' => 'Business', 'value' => 'business', 'default' => true],
                    ['label' => 'Personal', 'value' => 'personal'],
                ],
            ],
        ]);

        // Row: Company Name + Industry (conditional for business accounts) — card-level for reliable cloning
        $leadCompanyRow = $card->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'lead_row_company_industry',
            'order' => 1,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $leadCompanyCol = $leadCompanyRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'lead_company_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $leadCompanyCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'company_name',
            'element_id' => 'company_name',
            'label' => 'Company Name',
            'placeholder' => 'Acme Inc.',
            'is_required' => false,
            'order' => 0,
            'options' => ['floating_label' => true],
            'conditional_logic' => [
                'action' => 'show',
                'match' => 'all',
                'conditions' => [[
                    'target_field_id' => $accountType->id,
                    'operator' => 'equals',
                    'value' => 'business',
                ]],
            ],
        ]);

        $leadIndustryCol = $leadCompanyRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'lead_industry_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $leadIndustryCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'select',
            'name' => 'industry',
            'element_id' => 'industry',
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
                'conditions' => [[
                    'target_field_id' => $accountType->id,
                    'operator' => 'equals',
                    'value' => 'business',
                ]],
            ],
        ]);

        // Row: Contact — card-level for reliable cloning
        $leadContactRow = $card->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'lead_row_contact',
            'order' => 2,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $leadEmailCol = $leadContactRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'lead_email_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $leadEmailCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'email',
            'name' => 'email',
            'element_id' => 'email',
            'label' => 'Work Email',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'email'],
            'options' => [
                'floating_label' => true,
                'placeholder' => 'you@company.com',
            ],
        ]);

        if (property_exists($this, 'command') && $this->command) {
            $this->command->info('  • Lead: added lead_row_company_industry + lead_row_contact');
        }

        // Budget slider
        $card->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'slider',
            'name' => 'budget',
            'element_id' => 'budget',
            'label' => 'Approximate Budget ($)',
            'is_required' => false,
            'order' => 3,
            'options' => ['min' => 0, 'max' => 100000, 'step' => 1000, 'show_value' => true],
        ]);
    }

    /**
     * FORM 3: Multi-Step Registration with Tabs
     */
    protected function createMultiStepRegistrationTemplate(): void
    {
        $form = CustomForm::create([
            'name' => 'Multi-Step Registration with Tabs',
            'description' => 'Registration form split across multiple steps with a progress indicator.',
            'is_active' => false,
            'is_template' => true,
            'template_category' => 'registration',
            'template_description' => 'Collect account details, profile info, and preferences across multiple steps.',
            'settings' => [
                'multi_page_enabled' => true,
                'progress_style' => 'steps',
                'allow_back_navigation' => true,
            ],
        ]);

        // Page 1: Account
        $page1 = SlickFormPage::create([
            'slick_form_id' => $form->id,
            'title' => 'Account',
            'order' => 0,
            'icon' => 'bi-person-circle',
            'show_in_progress' => true,
        ]);

        $cont1 = $form->layoutElements()->create([
            'slick_form_page_id' => $page1->id,
            'element_type' => 'container',
            'element_id' => 'account_container',
            'order' => 0,
        ]);
        $cont1->class = 'pt-4 pb-5';
        $cont1->save();

        // Row: Email + Password
        $regRow = $cont1->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page1->id,
            'element_type' => 'row',
            'element_id' => 'reg_row_account',
            'order' => 0,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $regEmailCol = $regRow->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page1->id,
            'element_type' => 'column',
            'element_id' => 'reg_email_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $regEmailCol->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page1->id,
            'field_type' => 'email',
            'name' => 'email',
            'element_id' => 'reg_email',
            'label' => 'Email',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'email'],
            'options' => ['floating_label' => true, 'placeholder' => 'you@example.com'],
        ]);
        $regPassCol = $regRow->children()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page1->id,
            'element_type' => 'column',
            'element_id' => 'reg_password_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $regPassCol->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page1->id,
            'field_type' => 'password',
            'name' => 'password',
            'element_id' => 'reg_password',
            'label' => 'Password',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'min:8'],
            'options' => ['floating_label' => true, 'placeholder' => 'At least 8 characters'],
        ]);

        if (property_exists($this, 'command') && $this->command) {
            $this->command->info('  • Registration: added reg_row_account (email/password)');
        }

        // Page 2: Profile
        $page2 = SlickFormPage::create([
            'slick_form_id' => $form->id,
            'title' => 'Profile',
            'order' => 1,
            'icon' => 'bi-card-text',
            'show_in_progress' => true,
        ]);

        $cont2 = $form->layoutElements()->create([
            'slick_form_page_id' => $page2->id,
            'element_type' => 'container',
            'element_id' => 'profile_container',
            'order' => 0,
        ]);
        $cont2->class = 'pt-4 pb-5';
        $cont2->save();

        $cont2->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page2->id,
            'field_type' => 'text',
            'name' => 'full_name',
            'element_id' => 'full_name',
            'label' => 'Full Name',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'min:2'],
            'options' => ['floating_label' => true, 'placeholder' => 'Your full name'],
        ]);

        // Page 3: Preferences
        $page3 = SlickFormPage::create([
            'slick_form_id' => $form->id,
            'title' => 'Preferences',
            'order' => 2,
            'icon' => 'bi-sliders',
            'show_in_progress' => true,
        ]);

        $cont3 = $form->layoutElements()->create([
            'slick_form_page_id' => $page3->id,
            'element_type' => 'container',
            'element_id' => 'pref_container',
            'order' => 0,
        ]);

        $cont3->fields()->create([
            'slick_form_id' => $form->id,
            'slick_form_page_id' => $page3->id,
            'field_type' => 'checkbox',
            'name' => 'interests',
            'element_id' => 'interests',
            'label' => 'Interests',
            'is_required' => false,
            'order' => 0,
            'options' => [
                'values' => [
                    ['label' => 'News', 'value' => 'news'],
                    ['label' => 'Offers', 'value' => 'offers'],
                    ['label' => 'Events', 'value' => 'events'],
                ],
            ],
        ]);
    }

    /**
     * FORM 4: Job Application Form
     */
    protected function createJobApplicationTemplate(): void
    {
        $form = CustomForm::create([
            'name' => 'Job Application Form',
            'description' => 'Collect applicant details and resume uploads.',
            'is_active' => false,
            'is_template' => true,
            'template_category' => 'application',
            'template_description' => 'Name, contact info, resume upload, and cover letter.',
        ]);

        $container = $form->layoutElements()->create([
            'element_type' => 'container',
            'element_id' => 'job_container',
            'order' => 0,
        ]);
        $container->class = 'pt-4 pb-5';
        $container->save();

        // Visual header image for hierarchy and polish
        $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'image',
            'name' => 'job_header_image',
            'element_id' => 'job_header_image',
            'label' => 'Header',
            'show_label' => false,
            'order' => -1,
            'options' => [
                'input_mode' => 'url',
                'image_url' => 'https://picsum.photos/seed/slick-forms-job/1800/240',
                'alt_text' => 'Job Application Banner',
                'object_fit' => 'cover',
            ],
        ]);

        // Primary Application Card
        $basicCard = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'job_basic_card',
            'order' => 0,
            'settings' => ['title' => 'Application Details'],
        ]);
        $basicCard->class = 'shadow-sm rounded-3';
        $basicCard->save();

        // Row 1: Full Name + Email
        $row1 = $basicCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_row_1',
            'order' => 0,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $nameCol = $row1->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_name_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $nameCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'full_name',
            'element_id' => 'full_name',
            'label' => 'Full Name',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'min:2'],
            'options' => ['floating_label' => true, 'placeholder' => 'Your full name'],
        ]);
        $emailCol = $row1->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_email_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $emailCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'email',
            'name' => 'email',
            'element_id' => 'email',
            'label' => 'Email',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'email'],
            'options' => ['floating_label' => true, 'placeholder' => 'you@example.com'],
        ]);

        // Row 2: Resume (full width)
        $row2 = $basicCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_row_2',
            'order' => 1,
        ]);
        $resumeCol = $row2->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_resume_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 12]],
        ]);
        $resumeCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'file',
            'name' => 'resume',
            'element_id' => 'resume',
            'label' => 'Resume (PDF/DOC)',
            'is_required' => true,
            'order' => 0,
            'options' => [
                'accepted_types' => '.pdf,.doc,.docx',
                'max_size' => 5120,
                'floating_label' => false,
            ],
            'help_text' => 'Upload your resume (PDF or DOC, max 5MB)',
        ]);

        // Row 3: Cover letter (full width)
        $row3 = $basicCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_row_3',
            'order' => 2,
        ]);
        $coverCol = $row3->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_cover_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 12]],
        ]);
        $coverCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'textarea',
            'name' => 'cover_letter',
            'element_id' => 'cover_letter',
            'label' => 'Cover Letter',
            'is_required' => false,
            'order' => 0,
            'options' => ['rows' => 6, 'floating_label' => true, 'placeholder' => 'Paste your cover letter here'],
        ]);

        // Additional Details Card (rows/columns for polish and cloning reliability)
        $detailsCard = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'job_details_card',
            'order' => 3,
            'settings' => ['title' => 'Additional Details'],
        ]);
        $detailsCard->class = 'shadow-sm rounded-3';
        $detailsCard->save();

        // Row 4: Contact & Links (3 equal)
        $contactLinksRow = $detailsCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_contact_links_row',
            'order' => 0,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $phoneCol = $contactLinksRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_phone_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 'equal']],
        ]);
        $phoneCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'phone',
            'name' => 'phone',
            'element_id' => 'job_phone',
            'label' => 'Phone Number',
            'is_required' => false,
            'order' => 0,
            'options' => ['floating_label' => true, 'format' => 'us', 'mask' => ['enabled' => true, 'type' => 'phone_us']],
            'validation_rules' => ['max:20'],
            'placeholder' => '(555) 123-4567',
        ]);
        $linkedInCol = $contactLinksRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_linkedin_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 'equal']],
        ]);
        $linkedInCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'url',
            'name' => 'linkedin_url',
            'element_id' => 'linkedin_url',
            'label' => 'LinkedIn Profile',
            'is_required' => false,
            'order' => 0,
            'options' => ['floating_label' => true],
            'placeholder' => 'https://www.linkedin.com/in/username',
            'validation_rules' => ['url', 'max:255'],
        ]);
        $portfolioCol = $contactLinksRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_portfolio_col',
            'order' => 2,
            'settings' => ['width' => ['md' => 'equal']],
        ]);
        $portfolioCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'url',
            'name' => 'portfolio_url',
            'element_id' => 'portfolio_url',
            'label' => 'Portfolio Website',
            'is_required' => false,
            'order' => 0,
            'options' => ['floating_label' => true],
            'placeholder' => 'https://example.com',
            'validation_rules' => ['url', 'max:255'],
        ]);

        // Row 5: Address
        $addressRow = $detailsCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_address_row',
            'order' => 1,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $addr1Col = $addressRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_address1_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 8]],
        ]);
        $addr1Col->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'address_line1',
            'element_id' => 'address_line1',
            'label' => 'Address Line 1',
            'options' => ['floating_label' => true],
            'order' => 0,
            'placeholder' => 'Street address, P.O. box',
        ]);
        $addr2Col = $addressRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_address2_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 4]],
        ]);
        $addr2Col->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'address_line2',
            'element_id' => 'address_line2',
            'label' => 'Address Line 2',
            'options' => ['floating_label' => true],
            'order' => 0,
            'placeholder' => 'Apt, suite, unit',
        ]);
        $cityRow = $detailsCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_city_state_row',
            'order' => 2,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $cityCol = $cityRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_city_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 4]],
        ]);
        $cityCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'city',
            'element_id' => 'city',
            'label' => 'City',
            'options' => ['floating_label' => true],
            'order' => 0,
            'placeholder' => 'City',
        ]);
        $stateCol = $cityRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_state_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 4]],
        ]);
        $stateCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'select',
            'name' => 'state',
            'element_id' => 'state',
            'label' => 'State/Region',
            'order' => 0,
            'options' => [
                'searchable' => false,
                'floating_label' => true,
                'placeholder' => 'Select state',
                'values' => [
                    ['label' => 'Alabama', 'value' => 'AL'],
                    ['label' => 'Alaska', 'value' => 'AK'],
                    ['label' => 'Arizona', 'value' => 'AZ'],
                    ['label' => 'Arkansas', 'value' => 'AR'],
                    ['label' => 'California', 'value' => 'CA'],
                    // ... truncated list for brevity
                ],
            ],
        ]);
        $zipCol = $cityRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_postal_code_col',
            'order' => 2,
            'settings' => ['width' => ['md' => 4]],
        ]);
        $zipCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'postal_code',
            'element_id' => 'postal_code',
            'label' => 'Postal Code',
            'options' => ['floating_label' => true],
            'order' => 0,
            'placeholder' => 'ZIP / Postal code',
        ]);

        // Row 6: Position Details
        $positionRow = $detailsCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_position_row',
            'order' => 3,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $roleCol = $positionRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_role_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $roleCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'desired_role',
            'element_id' => 'desired_role',
            'label' => 'Desired Position',
            'options' => ['floating_label' => true],
            'order' => 0,
            'placeholder' => 'Position you are applying for',
        ]);
        $empTypeCol = $positionRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_employment_type_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $empTypeCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'select',
            'name' => 'employment_type',
            'element_id' => 'employment_type',
            'label' => 'Employment Type',
            'order' => 0,
            'options' => [
                'placeholder' => 'Select type',
                'values' => [
                    ['label' => 'Full-time', 'value' => 'full_time'],
                    ['label' => 'Part-time', 'value' => 'part_time'],
                    ['label' => 'Contract', 'value' => 'contract'],
                    ['label' => 'Internship', 'value' => 'internship'],
                ],
                'searchable' => false,
                'floating_label' => true,
            ],
        ]);
        $availabilityRow = $detailsCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_availability_row',
            'order' => 4,
            'settings' => ['g-3'],
        ]);
        $availCol = $availabilityRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_availability_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $availCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'date',
            'name' => 'availability_date',
            'element_id' => 'availability_date',
            'label' => 'Available From',
            'order' => 0,
            'options' => ['floating_label' => true, 'enable_flatpickr' => false],
        ]);
        $salaryCol = $availabilityRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_salary_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $salaryCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'expected_salary',
            'element_id' => 'expected_salary',
            'label' => 'Expected Salary',
            'order' => 0,
            'options' => ['floating_label' => true],
            'placeholder' => '$80,000 per year',
        ]);

        // Row 7: Authorization & Relocation
        $authRow = $detailsCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_auth_row',
            'order' => 5,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $authCol = $authRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_work_auth_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $authCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'switch',
            'name' => 'work_authorization',
            'element_id' => 'work_authorization',
            'label' => 'Legally authorized to work',
            'order' => 0,
            'options' => ['on_label' => 'Yes', 'off_label' => 'No', 'show_labels' => true],
        ]);
        $relocateCol = $authRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_relocate_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $relocateCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'switch',
            'name' => 'willing_to_relocate',
            'element_id' => 'willing_to_relocate',
            'label' => 'Willing to relocate',
            'order' => 0,
        ]);

        // Row 8: Skills (tags)
        $skillsRow = $detailsCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_skills_row',
            'order' => 6,
        ]);
        $skillsCol = $skillsRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_skills_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 12]],
        ]);
        $skillsCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'tags',
            'name' => 'skills',
            'element_id' => 'skills',
            'label' => 'Key Skills',
            'order' => 0,
            'options' => ['floating_label' => true, 'field_size' => 'lg'],
            'placeholder' => 'e.g., Laravel, Livewire, MySQL, AWS',
        ]);

        // Education Card
        $eduCard = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'job_education_card',
            'order' => 4,
            'settings' => [],
        ]);
        $eduCard->class = 'shadow-sm rounded-3';
        $eduCard->save();

        // Row 9: Education (repeater)
        $educationRow = $eduCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_education_row',
            'order' => 7,
        ]);
        $educationCol = $educationRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_education_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 12]],
        ]);
        $educationRepeater = $educationCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'repeater',
            'name' => 'education',
            'element_id' => 'education',
            'label' => 'Education',
            'order' => 0,
            'options' => ['initial_instances' => 1, 'add_button_text' => 'Add Education'],
        ]);

        // Education repeater row with 3 equal columns
        $eduRepRow = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_field_id' => $educationRepeater->id,
            'element_type' => 'row',
            'element_id' => 'edu_rep_row',
            'order' => 0,
        ]);

        // Column 1: School Name
        $eduCol1 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $eduRepRow->id,
            'element_type' => 'column',
            'element_id' => 'edu_col_1',
            'order' => 0,
            'settings' => ['column_width' => 'equal'],
        ]);
        $eduCol1->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'school_name',
            'element_id' => 'school_name',
            'label' => 'School Name',
            'order' => 0,
            'options' => ['floating_label' => true],
            'placeholder' => 'Your university or school',
        ]);

        // Column 2: Degree
        $eduCol2 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $eduRepRow->id,
            'element_type' => 'column',
            'element_id' => 'edu_col_2',
            'order' => 1,
            'settings' => ['column_width' => 'equal'],
        ]);
        $eduCol2->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'degree',
            'element_id' => 'degree',
            'label' => 'Degree',
            'order' => 0,
            'options' => ['floating_label' => true],
            'placeholder' => 'e.g., B.S., M.S.',
        ]);

        // Column 3: Graduation Year
        $eduCol3 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $eduRepRow->id,
            'element_type' => 'column',
            'element_id' => 'edu_col_3',
            'order' => 2,
            'settings' => ['column_width' => 'equal'],
        ]);
        $eduCol3->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'number',
            'name' => 'graduation_year',
            'element_id' => 'graduation_year',
            'label' => 'Graduation Year',
            'order' => 0,
            'options' => ['floating_label' => true],
            'placeholder' => 'e.g., 2024',
        ]);

        // Experience Card
        $expCard = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'job_experience_card',
            'order' => 5,
            'settings' => [],
        ]);
        $expCard->class = 'shadow-sm rounded-3';
        $expCard->save();

        // Row 10: Experience (repeater)
        $experienceRow = $expCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_experience_row',
            'order' => 8,
        ]);
        $experienceCol = $experienceRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_experience_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 12]],
        ]);
        $experienceRepeater = $experienceCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'repeater',
            'name' => 'work_experience',
            'element_id' => 'work_experience',
            'label' => 'Work Experience',
            'order' => 0,
            'options' => ['initial_instances' => 1, 'add_button_text' => 'Add Experience'],
        ]);

        // Row 1: Company and Job Title (2 equal columns)
        $expRepRow1 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_field_id' => $experienceRepeater->id,
            'element_type' => 'row',
            'element_id' => 'exp_rep_row_1',
            'order' => 0,
        ]);
        $expRepRow1Col1 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $expRepRow1->id,
            'element_type' => 'column',
            'element_id' => 'exp_rep_row1_col_1',
            'order' => 0,
            'settings' => ['column_width' => 'equal'],
        ]);
        $expRepRow1Col1->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'company',
            'element_id' => 'company',
            'label' => 'Company',
            'order' => 0,
            'options' => ['floating_label' => true],
            'placeholder' => 'e.g., Acme Inc.',
        ]);
        $expRepRow1Col2 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $expRepRow1->id,
            'element_type' => 'column',
            'element_id' => 'exp_rep_row1_col_2',
            'order' => 1,
            'settings' => ['column_width' => 'equal'],
        ]);
        $expRepRow1Col2->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'job_title',
            'element_id' => 'job_title',
            'label' => 'Job Title',
            'order' => 0,
            'options' => ['floating_label' => true],
            'placeholder' => 'e.g., Software Engineer',
        ]);

        // Row 2: Start and End Date (2 equal columns)
        $expRepRow2 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_field_id' => $experienceRepeater->id,
            'element_type' => 'row',
            'element_id' => 'exp_rep_row_2',
            'order' => 1,
        ]);
        $expRepRow2Col1 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $expRepRow2->id,
            'element_type' => 'column',
            'element_id' => 'exp_rep_row2_col_1',
            'order' => 0,
            'settings' => ['column_width' => 'equal'],
        ]);
        $expRepRow2Col1->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'date',
            'name' => 'start_date',
            'element_id' => 'start_date',
            'label' => 'Start Date',
            'order' => 0,
            'options' => ['floating_label' => true, 'enable_flatpickr' => false],
        ]);
        $expRepRow2Col2 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $expRepRow2->id,
            'element_type' => 'column',
            'element_id' => 'exp_rep_row2_col_2',
            'order' => 1,
            'settings' => ['column_width' => 'equal'],
        ]);
        $expRepRow2Col2->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'date',
            'name' => 'end_date',
            'element_id' => 'end_date',
            'label' => 'End Date',
            'order' => 0,
            'options' => ['floating_label' => true, 'enable_flatpickr' => false],
        ]);

        // Responsibilities as direct child (not in row/column)
        $form->fields()->create([
            'slick_form_id' => $form->id,
            'parent_field_id' => $experienceRepeater->id,
            'field_type' => 'textarea',
            'name' => 'responsibilities',
            'element_id' => 'responsibilities',
            'label' => 'Responsibilities & Achievements',
            'order' => 5,
            'options' => ['rows' => 3, 'floating_label' => true],
            'placeholder' => 'Briefly describe your key responsibilities and achievements',
        ]);

        // References Card
        $refsCard = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'job_references_card',
            'order' => 6,
            'settings' => [],
        ]);
        $refsCard->class = 'shadow-sm rounded-3';
        $refsCard->save();

        // Row 11: References (repeater)
        $refsRow = $refsCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_references_row',
            'order' => 9,
        ]);
        $refsCol = $refsRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_references_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 12]],
        ]);
        $refsRepeater = $refsCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'repeater',
            'name' => 'references',
            'element_id' => 'references',
            'label' => 'References',
            'order' => 0,
            'options' => ['initial_instances' => 1, 'add_button_text' => 'Add Reference'],
        ]);

        // References row with 3 equal columns
        $refsRepRow = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_field_id' => $refsRepeater->id,
            'element_type' => 'row',
            'element_id' => 'refs_rep_row',
            'order' => 0,
        ]);
        $refsCol1 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $refsRepRow->id,
            'element_type' => 'column',
            'element_id' => 'refs_col_1',
            'order' => 0,
            'settings' => ['column_width' => 'equal'],
        ]);
        $refsCol1->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'ref_name',
            'element_id' => 'ref_name',
            'label' => 'Name',
            'order' => 0,
            'options' => ['floating_label' => true],
            'placeholder' => 'Full name',
        ]);
        $refsCol2 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $refsRepRow->id,
            'element_type' => 'column',
            'element_id' => 'refs_col_2',
            'order' => 1,
            'settings' => ['column_width' => 'equal'],
        ]);
        $refsCol2->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'ref_relationship',
            'element_id' => 'ref_relationship',
            'label' => 'Relationship',
            'order' => 0,
            'options' => ['floating_label' => true],
            'placeholder' => 'e.g., Manager',
        ]);
        $refsCol3 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $refsRepRow->id,
            'element_type' => 'column',
            'element_id' => 'refs_col_3',
            'order' => 2,
            'settings' => ['column_width' => 'equal'],
        ]);
        $refsCol3->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'phone',
            'name' => 'ref_phone',
            'element_id' => 'ref_phone',
            'label' => 'Phone',
            'order' => 0,
            'options' => [
                'floating_label' => true,
                'display_format' => 'us',
                'enable_input_mask' => true,
                'mask_type' => 'phone_us',
            ],
            'placeholder' => '(555) 123-4567',
        ]);

        // Certification & Signature Card
        $signCard = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'card',
            'element_id' => 'job_signature_card',
            'order' => 7,
            'settings' => ['title' => 'Certification & Signature'],
        ]);
        $signCard->class = 'shadow-sm rounded-3';
        $signCard->save();

        // Row 12: Certification & Signature
        $consentRow = $signCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_consent_row',
            'order' => 10,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $certCol = $consentRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_certify_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 12]],
        ]);
        $certCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'switch',
            'name' => 'certify_truthful',
            'element_id' => 'certify_truthful',
            'label' => 'I certify that the information provided is true and complete to the best of my knowledge',
            'order' => 0,
            'validation_rules' => ['accepted'],
        ]);
        $sigRow = $signCard->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'job_signature_row',
            'order' => 11,
        ]);
        $sigCol = $sigRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_signature_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $sigCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'signature',
            'name' => 'signature',
            'element_id' => 'signature',
            'label' => 'Signature',
            'order' => 0,
        ]);
        $sigDateCol = $sigRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'job_signature_date_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $sigDateCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'date',
            'name' => 'signature_date',
            'element_id' => 'signature_date',
            'label' => 'Date',
            'order' => 0,
            'options' => ['floating_label' => true, 'enable_flatpickr' => false],
        ]);
    }

    /**
     * FORM 7: Product Order Form with Repeater
     */
    protected function createProductOrderTemplate(): void
    {
        $form = CustomForm::create([
            'name' => 'Product Order Form with Repeater',
            'description' => 'Order multiple products with quantities using a repeater.',
            'is_active' => false,
            'is_template' => true,
            'template_category' => 'order',
            'template_description' => 'Add one or more products and quantities, then provide contact info.',
        ]);

        $container = $form->layoutElements()->create([
            'element_type' => 'container',
            'element_id' => 'order_container',
            'order' => 0,
            // class set after create to avoid mass-assignment edge cases
        ]);
        $container->class = 'pt-4 pb-5';
        $container->save();

        // Visual header image (hero/banner)
        $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'image',
            'name' => 'order_header_image',
            'element_id' => 'order_header_image',
            'label' => 'Header',
            'show_label' => false,
            'order' => -2,
            'options' => [
                'input_mode' => 'url',
                'image_url' => 'https://picsum.photos/seed/slick-forms-order/1200/320',
                'alt_text' => 'Order Banner',
                'object_fit' => 'cover',
            ],
        ]);

        // Note: no carousel here to keep this template focused and clean.

        // Repeater field
        $repeater = $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'repeater',
            'name' => 'items',
            'element_id' => 'items',
            'label' => 'Order Items',
            'is_required' => true,
            'order' => 0,
            'options' => [
                'initial_instances' => 1,
                'min_instances' => 1,
                'max_instances' => 10,
                'button_label' => 'Add Item',
                'layout_style' => 'card',
                'show_instance_number' => true,
                'allow_reorder' => true,
            ],
            'validation_rules' => ['required', 'array', 'min:1'],
        ]);
        $repeater->class = 'mb-4';
        $repeater->save();

        // Child fields within repeater (use unique names within the form)
        $repeater->children()->create([
            'slick_form_id' => $form->id,
            'parent_field_id' => $repeater->id,
            'field_type' => 'select',
            'name' => 'item_product',
            'element_id' => 'item_product',
            'label' => 'Product',
            'is_required' => true,
            'order' => 0,
            'options' => [
                'searchable' => true,
                'placeholder' => 'Choose a product...',
                'values' => [
                    ['label' => 'Standard Widget', 'value' => 'std_widget', 'default' => true],
                    ['label' => 'Pro Widget', 'value' => 'pro_widget'],
                    ['label' => 'Deluxe Widget', 'value' => 'deluxe_widget'],
                ],
            ],
        ]);

        $repeater->children()->create([
            'slick_form_id' => $form->id,
            'parent_field_id' => $repeater->id,
            'field_type' => 'number',
            'name' => 'item_quantity',
            'element_id' => 'item_quantity',
            'label' => 'Quantity',
            'is_required' => true,
            'order' => 1,
            'validation_rules' => ['required', 'integer', 'min:1', 'max:100'],
            'options' => [
                'min' => 1,
                'max' => 100,
                'step' => 1,
                'floating_label' => true,
            ],
        ]);

        // Optional: size select for apparel or variants
        $repeater->children()->create([
            'slick_form_id' => $form->id,
            'parent_field_id' => $repeater->id,
            'field_type' => 'select',
            'name' => 'item_size',
            'element_id' => 'item_size',
            'label' => 'Size',
            'is_required' => false,
            'order' => 2,
            'options' => [
                'searchable' => true,
                'values' => [
                    ['label' => 'Small', 'value' => 'S'],
                    ['label' => 'Medium', 'value' => 'M'],
                    ['label' => 'Large', 'value' => 'L'],
                    ['label' => 'X-Large', 'value' => 'XL'],
                ],
            ],
        ]);

        // Color text input
        $repeater->children()->create([
            'slick_form_id' => $form->id,
            'parent_field_id' => $repeater->id,
            'field_type' => 'text',
            'name' => 'item_color',
            'element_id' => 'item_color',
            'label' => 'Color',
            'placeholder' => 'e.g., Blue, Red',
            'is_required' => false,
            'order' => 3,
            'options' => [
                'floating_label' => true,
            ],
        ]);

        // Unit price (for reference/quote)
        $repeater->children()->create([
            'slick_form_id' => $form->id,
            'parent_field_id' => $repeater->id,
            'field_type' => 'number',
            'name' => 'item_unit_price',
            'element_id' => 'item_unit_price',
            'label' => 'Unit Price ($)',
            'is_required' => false,
            'order' => 4,
            'validation_rules' => ['numeric', 'min:0'],
            'options' => [
                'min' => 0,
                'step' => 0.01,
                'floating_label' => true,
            ],
        ]);

        // Contact info row (2 columns)
        $contactRow = $container->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'row',
            'element_id' => 'order_contact_row',
            'order' => 2,
            'settings' => ['gutter' => 'g-3'],
        ]);
        $nameCol = $contactRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'order_name_col',
            'order' => 0,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $nameCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'customer_name',
            'element_id' => 'customer_name',
            'label' => 'Your Name',
            'placeholder' => 'Full Name',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required'],
            'options' => [
                'floating_label' => true,
            ],
        ]);
        $emailCol = $contactRow->children()->create([
            'slick_form_id' => $form->id,
            'element_type' => 'column',
            'element_id' => 'order_email_col',
            'order' => 1,
            'settings' => ['width' => ['md' => 6]],
        ]);
        $emailCol->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'email',
            'name' => 'customer_email',
            'element_id' => 'customer_email',
            'label' => 'Email',
            'placeholder' => 'you@example.com',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required', 'email'],
            'options' => [
                'floating_label' => true,
            ],
        ]);
    }

    /**
     * FORM 10: Booking Form with Date Range
     */
    protected function createBookingFormTemplate(): void
    {
        $form = CustomForm::create([
            'name' => 'Booking Form with Date Range',
            'description' => 'Book a time window with start and end dates.',
            'is_active' => false,
            'is_template' => true,
            'template_category' => 'order',
            'template_description' => 'Collect booking date range and contact info.',
        ]);

        $container = $form->layoutElements()->create([
            'element_type' => 'container',
            'element_id' => 'booking_container',
            'order' => 0,
            // class set after create to avoid mass-assignment edge cases
        ]);
        $container->class = 'pt-4 pb-5';
        $container->save();

        $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'date_range',
            'name' => 'date_range',
            'element_id' => 'date_range',
            'label' => 'Requested Dates',
            'is_required' => true,
            'order' => 0,
            'validation_rules' => ['required'],
        ]);

        $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'name',
            'element_id' => 'name',
            'label' => 'Full Name',
            'is_required' => true,
            'order' => 1,
            'validation_rules' => ['required'],
        ]);

        $container->fields()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'email',
            'name' => 'email',
            'element_id' => 'email',
            'label' => 'Email',
            'is_required' => true,
            'order' => 2,
            'validation_rules' => ['required', 'email'],
        ]);
    }
}
