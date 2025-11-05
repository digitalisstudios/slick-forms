<?php

namespace DigitalisStudios\SlickForms\Database\Seeders;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormFieldValue;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Models\SlickFormAnalyticsEvent;
use DigitalisStudios\SlickForms\Models\SlickFormAnalyticsSession;
use DigitalisStudios\SlickForms\Services\FormTemplateService;
use Illuminate\Database\Seeder;

class DemoFormsWithAnalyticsSeeder extends Seeder
{
    protected array $devices = ['desktop', 'mobile', 'tablet'];

    protected array $browsers = ['Chrome', 'Safari', 'Firefox', 'Edge'];

    protected array $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
    ];

    public function run(): void
    {
        $this->command->info('🗑️  Clearing existing data...');
        $this->clearExistingData();

        $this->command->info('📝 Creating template forms...');
        $this->call(FormTemplatesSeeder::class);

        // Only use the 10 real-world examples from docs/EXAMPLES.md
        $exampleTemplateNames = [
            'Simple Contact Form',
            'Lead Capture with Conditional Logic',
            'Multi-Step Registration with Tabs',
            'Job Application Form',
            'Event Registration with Pricing',
            'Customer Satisfaction Survey',
            'Product Order Form with Repeater',
            'Support Ticket Form',
            'Newsletter Signup with Preferences',
            'Booking Form with Date Range',
        ];

        $templates = CustomForm::where('is_template', true)
            ->whereIn('name', $exampleTemplateNames)
            ->get();

        $this->command->info('🔄 Creating working forms from templates...');
        $templateService = app(FormTemplateService::class);
        $forms = collect();

        foreach ($templates as $template) {
            $form = $templateService->createFromTemplate($template, $template->name);
            $forms->push($form);
            $this->command->info("  ✓ Created: {$form->name}");
        }

        foreach ($forms as $form) {
            $this->command->info("📊 Seeding analytics for: {$form->name}");

            // Create successful submissions with analytics (last 30 days)
            // Contact forms get more submissions than surveys
            $submissionCount = str_contains($form->name, 'Contact') || str_contains($form->name, 'Service') ? 25 : 18;
            $this->createSubmissionsWithAnalytics($form, $submissionCount, 30);

            // Create abandoned sessions (started but not completed)
            $abandonedCount = str_contains($form->name, 'Event') ? 12 : 8; // Event forms have higher abandonment
            $this->createAbandonedSessions($form, $abandonedCount, 30);

            // Create view-only sessions (no interaction)
            $viewCount = rand(5, 10);
            $this->createViewOnlySessions($form, $viewCount, 30);
        }

        $this->command->info('✅ Demo forms with analytics seeded successfully!');
    }

    protected function clearExistingData(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        SlickFormAnalyticsEvent::truncate();
        SlickFormAnalyticsSession::truncate();
        CustomFormFieldValue::truncate();
        CustomFormSubmission::truncate();
        \DigitalisStudios\SlickForms\Models\SlickFormLayoutElement::truncate();
        \DigitalisStudios\SlickForms\Models\CustomFormField::truncate();
        \DigitalisStudios\SlickForms\Models\SlickFormPage::truncate();
        CustomForm::truncate();

        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    protected function createSubmissionsWithAnalytics(CustomForm $form, int $count, int $daysBack): void
    {
        $fields = $form->fields()->whereNotIn('field_type', ['header', 'paragraph', 'code'])->get();

        for ($i = 0; $i < $count; $i++) {
            // Random timestamp within last X days
            $submittedAt = now()->subDays(rand(0, $daysBack))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $startedAt = (clone $submittedAt)->subMinutes(rand(2, 15)); // 2-15 minutes to complete
            $viewedAt = (clone $startedAt)->subSeconds(rand(5, 60)); // Viewed 5-60 seconds before starting

            $device = $this->devices[array_rand($this->devices)];
            $browser = $this->browsers[array_rand($this->browsers)];
            $userAgent = $this->userAgents[array_rand($this->userAgents)];

            // Create analytics session
            $session = SlickFormAnalyticsSession::create([
                'slick_form_id' => $form->id,
                'session_id' => 'session_'.uniqid(),
                'user_id' => null,
                'ip_address' => $this->randomIp(),
                'user_agent' => $userAgent,
                'device_type' => $device,
                'browser' => $browser,
                'current_page_index' => 0,
                'started_at' => $startedAt,
                'submitted_at' => $submittedAt,
                'time_spent_seconds' => $startedAt->diffInSeconds($submittedAt),
                'created_at' => $viewedAt,
                'updated_at' => $submittedAt,
            ]);

            // Create submission
            $submission = CustomFormSubmission::create([
                'slick_form_id' => $form->id,
                'user_id' => null,
                'ip_address' => $session->ip_address,
                'submitted_at' => $submittedAt,
                'created_at' => $submittedAt,
                'updated_at' => $submittedAt,
            ]);

            // Create field values and events
            foreach ($fields as $index => $field) {
                $value = $this->generateFieldValue($field);

                CustomFormFieldValue::create([
                    'slick_form_submission_id' => $submission->id,
                    'slick_form_field_id' => $field->id,
                    'value' => is_array($value) ? json_encode($value) : $value,
                ]);

                // Create field events (focus and change)
                $fieldInteractionTime = (clone $startedAt)->addSeconds($index * rand(5, 20));

                SlickFormAnalyticsEvent::create([
                    'slick_form_analytics_session_id' => $session->id,
                    'slick_form_field_id' => $field->id,
                    'event_type' => 'field_focus',
                    'created_at' => $fieldInteractionTime,
                ]);

                SlickFormAnalyticsEvent::create([
                    'slick_form_analytics_session_id' => $session->id,
                    'slick_form_field_id' => $field->id,
                    'event_type' => 'field_change',
                    'created_at' => (clone $fieldInteractionTime)->addSeconds(rand(3, 10)),
                ]);
            }
        }
    }

    protected function createAbandonedSessions(CustomForm $form, int $count, int $daysBack): void
    {
        $fields = $form->fields()->whereNotIn('field_type', ['header', 'paragraph', 'code'])->get();

        for ($i = 0; $i < $count; $i++) {
            $abandonedAt = now()->subDays(rand(0, $daysBack))->subHours(rand(0, 23));
            $startedAt = (clone $abandonedAt)->subMinutes(rand(1, 5));
            $viewedAt = (clone $startedAt)->subSeconds(rand(5, 60));

            $device = $this->devices[array_rand($this->devices)];
            $browser = $this->browsers[array_rand($this->browsers)];

            $session = SlickFormAnalyticsSession::create([
                'slick_form_id' => $form->id,
                'session_id' => 'session_'.uniqid(),
                'user_id' => null,
                'ip_address' => $this->randomIp(),
                'user_agent' => $this->userAgents[array_rand($this->userAgents)],
                'device_type' => $device,
                'browser' => $browser,
                'current_page_index' => 0,
                'started_at' => $startedAt,
                'abandoned_at' => $abandonedAt,
                'submitted_at' => null,
                'created_at' => $viewedAt,
                'updated_at' => $abandonedAt,
            ]);

            // User interacted with some fields before abandoning
            $fieldsToInteract = $fields->random(min(rand(1, 3), $fields->count()));

            foreach ($fieldsToInteract as $index => $field) {
                $fieldTime = (clone $startedAt)->addSeconds($index * rand(5, 15));

                SlickFormAnalyticsEvent::create([
                    'slick_form_analytics_session_id' => $session->id,
                    'slick_form_field_id' => $field->id,
                    'event_type' => 'field_focus',
                    'created_at' => $fieldTime,
                ]);

                // Maybe they changed the field, maybe not
                if (rand(0, 1)) {
                    SlickFormAnalyticsEvent::create([
                        'slick_form_analytics_session_id' => $session->id,
                        'slick_form_field_id' => $field->id,
                        'event_type' => 'field_change',
                        'created_at' => (clone $fieldTime)->addSeconds(rand(2, 8)),
                    ]);
                }
            }
        }
    }

    protected function createViewOnlySessions(CustomForm $form, int $count, int $daysBack): void
    {
        for ($i = 0; $i < $count; $i++) {
            $viewedAt = now()->subDays(rand(0, $daysBack))->subHours(rand(0, 23));

            $device = $this->devices[array_rand($this->devices)];
            $browser = $this->browsers[array_rand($this->browsers)];

            SlickFormAnalyticsSession::create([
                'slick_form_id' => $form->id,
                'session_id' => 'session_'.uniqid(),
                'user_id' => null,
                'ip_address' => $this->randomIp(),
                'user_agent' => $this->userAgents[array_rand($this->userAgents)],
                'device_type' => $device,
                'browser' => $browser,
                'current_page_index' => 0,
                'started_at' => null,
                'abandoned_at' => null,
                'submitted_at' => null,
                'created_at' => $viewedAt,
                'updated_at' => $viewedAt,
            ]);
        }
    }

    protected function generateFieldValue($field): mixed
    {
        return match ($field->field_type) {
            'text' => match ($field->name) {
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'company', 'company_name' => fake()->company(),
                'job_title' => fake()->jobTitle(),
                'referral_source', 'heard_about_us' => fake()->word(),
                default => fake()->words(rand(2, 4), true),
            },
            'email' => fake()->email(),
            'textarea' => match ($field->name) {
                'message', 'description', 'project_description' => fake()->paragraph(rand(2, 4)),
                'additional_comments', 'special_requirements', 'accessibility_needs' => fake()->sentence(rand(8, 15)),
                default => fake()->sentence(rand(5, 15)),
            },
            'number' => match ($field->name) {
                'number_of_tickets' => rand(2, 10),
                'nps_score' => rand(0, 10),
                default => rand(1, 100),
            },
            'phone' => fake()->phoneNumber(),
            'url' => fake()->url(),
            'select', 'radio' => $this->getRandomOptionValue($field),
            'checkbox', 'tags' => $this->getRandomOptionValues($field, rand(1, 3)),
            'date' => now()->subDays(rand(0, 365))->format('Y-m-d'),
            'time' => now()->format('H:i'),
            'star' => rand(3, 5), // Most ratings are positive (3-5 stars)
            'slider' => match ($field->name) {
                'nps_score' => rand(7, 10), // Most NPS scores are promoters (7-10)
                default => rand(50, 100),
            },
            'range' => [rand(20, 40), rand(60, 80)],
            'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
            'switch' => match ($field->name) {
                'newsletter_signup', 'gdpr_consent', 'terms_accepted', 'privacy_accepted' => true, // Most people accept
                'contact_permission', 'recommend' => (bool) rand(0, 1),
                default => (bool) rand(0, 1),
            },
            'file', 'image', 'video' => null, // Skip file uploads for demo data
            'hidden', 'calculation' => null, // Skip hidden and calculated fields
            default => null,
        };
    }

    protected function getRandomOptionValue($field): ?string
    {
        $options = $field->options['values'] ?? [];
        if (empty($options)) {
            return null;
        }

        $option = $options[array_rand($options)];

        return $option['value'] ?? null;
    }

    protected function getRandomOptionValues($field, int $count): array
    {
        $options = $field->options['values'] ?? [];
        if (empty($options)) {
            return [];
        }

        $selected = array_rand($options, min($count, count($options)));
        if (! is_array($selected)) {
            $selected = [$selected];
        }

        return array_map(fn ($index) => $options[$index]['value'], $selected);
    }

    protected function randomIp(): string
    {
        return implode('.', [
            rand(1, 255),
            rand(0, 255),
            rand(0, 255),
            rand(1, 255),
        ]);
    }
}
