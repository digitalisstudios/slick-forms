<?php

namespace DigitalisStudios\SlickForms\Livewire;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormFieldValue;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Models\SlickFormAnalyticsEvent;
use DigitalisStudios\SlickForms\Models\SlickFormAnalyticsSession;
use DigitalisStudios\SlickForms\Services\ConditionalLogicEvaluator;
use DigitalisStudios\SlickForms\Services\FieldTypeRegistry;
use DigitalisStudios\SlickForms\Services\FormLayoutService;
use DigitalisStudios\SlickForms\Services\FormulaEvaluator;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormRenderer extends Component
{
    use WithFileUploads;

    public CustomForm $form;

    #[Locked]
    public $model = null; // Eloquent model instance for binding

    public array $formData = [];

    public bool $submitted = false;

    public $lastSubmission = null; // Store last submission for success screen

    public array $formStructure = [];

    public array $visibleFieldIds = [];

    public array $repeaterInstances = []; // Track instance counts for each repeater

    public ?int $currentPageId = null;

    public array $pages = [];

    public int $currentPageIndex = 0;

    public ?int $analyticsSessionId = null;

    public bool $analyticsStarted = false;

    public function mount(int $formId, $model = null, ?array $prefillData = null): void
    {
        $this->form = CustomForm::with(['fields.children', 'layoutElements', 'pages', 'modelBinding'])->findOrFail($formId);
        $this->model = $model;

        // Initialize analytics session if feature enabled
        if (slick_forms_feature_enabled('analytics')) {
            $this->initializeAnalyticsSession();
        }

        if (! $this->form->is_active) {
            abort(404, 'This form is not currently active.');
        }

        if ($this->form->isMultiPage()) {
            $this->pages = $this->form->pages()->orderBy('order')->get()->toArray();
            if (count($this->pages) > 0) {
                $this->currentPageId = $this->pages[0]['id'];
                $this->currentPageIndex = 0;
            }
        }

        // Load form structure (filtered by current page if multi-page)
        $layoutService = app(FormLayoutService::class);
        $this->formStructure = $layoutService->getFormStructure($this->form, $this->currentPageId);

        // Initialize form data for all non-repeater fields
        foreach ($this->form->fields->where('parent_field_id', null) as $field) {
            if ($field->field_type === 'repeater') {
                // Initialize repeater
                $this->initializeRepeater($field);
            } else {
                $this->formData['field_'.$field->id] = $this->getFieldDefaultValue($field);
            }
        }

        // Pre-fill form data from model if model binding is enabled and model exists
        if ($this->model && $this->form->modelBinding) {
            $this->populateFromModel();
        }

        // Pre-fill form data from URL prefill data (takes precedence over model data)
        if ($prefillData) {
            $this->populateFromPrefillData($prefillData);
        }

        // Initialize spam protection fields
        $this->initializeSpamProtection();

        // Initialize calculations and visible fields
        $this->recalculateFields();
        $this->updateVisibleFields();
    }

    /**
     * Get the default value for a field by delegating to the field type class
     */
    protected function getFieldDefaultValue($field): mixed
    {
        $registry = app(\DigitalisStudios\SlickForms\Services\FieldTypeRegistry::class);
        $fieldTypeInstance = $registry->get($field->field_type);

        if ($fieldTypeInstance) {
            return $fieldTypeInstance->getDefaultValue($field);
        }

        return null;
    }

    /**
     * Populate form data from bound model
     */
    protected function populateFromModel(): void
    {
        $modelBindingService = app(\DigitalisStudios\SlickForms\Services\ModelBindingService::class);

        // Get pre-filled data from model
        $populatedData = $modelBindingService->populateFormData($this->form, $this->model);

        // Merge populated data into formData
        foreach ($populatedData as $fieldName => $value) {
            // Find field by name
            $field = $this->form->fields->where('name', $fieldName)->first();

            if ($field) {
                $this->formData['field_'.$field->id] = $value;
            }
        }
    }

    /**
     * Populate form data from URL prefill data
     */
    protected function populateFromPrefillData(array $prefillData): void
    {
        // Merge prefill data into formData
        foreach ($prefillData as $fieldName => $value) {
            // Find field by name
            $field = $this->form->fields->where('name', $fieldName)->first();

            if ($field) {
                $this->formData['field_'.$field->id] = $value;
            }
        }
    }

    /**
     * Initialize spam protection fields (honeypot, CAPTCHA tokens)
     */
    protected function initializeSpamProtection(): void
    {
        $spamSettings = $this->form->settings['spam'] ?? [];

        if (! ($spamSettings['enabled'] ?? false)) {
            return;
        }

        // Initialize honeypot field
        if ($spamSettings['honeypot']['enabled'] ?? false) {
            $honeypotFieldName = $spamSettings['honeypot']['field_name'] ?? 'website';
            $this->formData[$honeypotFieldName] = '';
            $this->formData['_honeypot_time'] = now()->timestamp;
        }

        // Initialize CAPTCHA response fields
        $captchaType = $spamSettings['captcha']['type'] ?? 'none';
        if ($captchaType === 'recaptcha') {
            $this->formData['g-recaptcha-response'] = '';
        } elseif ($captchaType === 'hcaptcha') {
            $this->formData['h-captcha-response'] = '';
        }
    }

    protected function initializeRepeater($repeaterField): void
    {
        $initialInstances = $repeaterField->options['initial_instances'] ?? 1;
        $this->repeaterInstances[$repeaterField->id] = $initialInstances;

        // Initialize formData structure for repeater
        $this->formData['field_'.$repeaterField->id] = [];

        for ($i = 0; $i < $initialInstances; $i++) {
            $this->formData['field_'.$repeaterField->id][$i] = [];

            // Initialize child fields with default values
            foreach ($repeaterField->children as $child) {
                $this->formData['field_'.$repeaterField->id][$i]['field_'.$child->id] = $this->getFieldDefaultValue($child);
            }
        }
    }

    public function addInstance(int $repeaterId): void
    {
        $repeater = $this->form->fields->find($repeaterId);
        $maxInstances = $repeater->options['max_instances'] ?? 10;

        if ($this->repeaterInstances[$repeaterId] < $maxInstances) {
            $this->repeaterInstances[$repeaterId]++;
            $instanceIndex = count($this->formData['field_'.$repeaterId]);

            // Initialize new instance data with default values
            $this->formData['field_'.$repeaterId][$instanceIndex] = [];
            foreach ($repeater->children as $child) {
                $this->formData['field_'.$repeaterId][$instanceIndex]['field_'.$child->id] = $this->getFieldDefaultValue($child);
            }
        }
    }

    public function removeInstance(int $repeaterId, int $instanceIndex): void
    {
        $repeater = $this->form->fields->find($repeaterId);
        $minInstances = $repeater->options['min_instances'] ?? 1;

        if ($this->repeaterInstances[$repeaterId] > $minInstances) {
            // Remove instance
            array_splice($this->formData['field_'.$repeaterId], $instanceIndex, 1);
            $this->repeaterInstances[$repeaterId]--;

            // Re-index array to maintain sequential keys
            $this->formData['field_'.$repeaterId] = array_values($this->formData['field_'.$repeaterId]);
        }
    }

    public function reorderInstances(int $repeaterId, array $newOrder): void
    {
        $reordered = [];
        foreach ($newOrder as $oldIndex) {
            $reordered[] = $this->formData['field_'.$repeaterId][$oldIndex];
        }
        $this->formData['field_'.$repeaterId] = $reordered;
    }

    public function updated($propertyName): void
    {
        // When any formData changes, re-evaluate conditional logic and calculations
        if (str_starts_with($propertyName, 'formData.')) {
            $this->recalculateFields();
            $this->updateVisibleFields();
        }
    }

    protected function recalculateFields(): void
    {
        $formulaEvaluator = app(FormulaEvaluator::class);

        // Convert formData from field_<id> to field_name format for evaluation
        // Build this ONCE so calculated values can be used in subsequent calculations
        $formDataByName = [];
        foreach ($this->form->fields as $sourceField) {
            if ($sourceField->name) {
                $value = $this->formData['field_'.$sourceField->id] ?? null;

                // For calculation fields, try to extract numeric value from formatted string
                if ($sourceField->field_type === 'calculation' && is_string($value)) {
                    // Remove common formatting (currency symbols, commas, percent signs, etc.)
                    $numericValue = preg_replace('/[^0-9.-]/', '', $value);
                    $formDataByName[$sourceField->name] = is_numeric($numericValue) ? (float) $numericValue : $value;
                } else {
                    $formDataByName[$sourceField->name] = $value;
                }
            }
        }

        // Get all calculation fields
        $calculationFields = $this->form->fields->where('field_type', 'calculation');

        foreach ($calculationFields as $field) {
            $formula = $field->options['formula'] ?? '';

            if ($formula) {
                // Evaluate formula
                $decimalPlaces = $field->options['decimal_places'] ?? 2;
                $result = $formulaEvaluator->evaluate($formula, $formDataByName, $decimalPlaces);

                // Store raw numeric result (for use in other calculations)
                if ($result !== null) {
                    // Store the raw number for use in other formulas (CRITICAL for chained calculations)
                    $formDataByName[$field->name] = $result;

                    // Format for display
                    $displayAs = $field->options['display_as'] ?? 'number';
                    $prefix = $field->options['prefix'] ?? '';
                    $suffix = $field->options['suffix'] ?? '';

                    $formatted = $formulaEvaluator->formatValue($result, $displayAs, $decimalPlaces, $prefix, $suffix);

                    // Store formatted value for display in the field
                    $this->formData['field_'.$field->id] = $formatted;
                }
            }
        }
    }

    protected function updateVisibleFields(): void
    {
        $evaluator = app(ConditionalLogicEvaluator::class);

        // Convert formData from field_<id> format to element_id format for evaluation
        $formDataByElementId = [];
        foreach ($this->form->fields as $field) {
            if ($field->element_id) {
                $formDataByElementId[$field->element_id] = $this->formData['field_'.$field->id] ?? null;
            }
        }

        // Evaluate each field's visibility
        $this->visibleFieldIds = [];
        foreach ($this->form->fields as $field) {
            if ($evaluator->shouldShowField($field, $formDataByElementId, $this->form->fields)) {
                $this->visibleFieldIds[] = $field->id;
            }
        }
    }

    /**
     * Public hook to force re-evaluation of conditional visibility from the UI
     */
    public function refreshVisibility(): void
    {
        $this->recalculateFields();
        $this->updateVisibleFields();
    }

    public function rules(): array
    {
        $rules = [];
        $registry = app(FieldTypeRegistry::class);
        $evaluator = app(ConditionalLogicEvaluator::class);

        // Convert formData to element_id format for evaluation
        $formDataByElementId = [];
        foreach ($this->form->fields as $field) {
            if ($field->element_id) {
                $formDataByElementId[$field->element_id] = $this->formData['field_'.$field->id] ?? null;
            }
        }

        // Only validate visible fields
        foreach ($this->form->fields as $field) {
            if (! in_array($field->id, $this->visibleFieldIds)) {
                continue;
            }

            $fieldType = $registry->get($field->field_type);

            // Handle repeater fields specially
            if ($field->field_type === 'repeater') {
                // Validate repeater array structure
                $fieldRules = $fieldType->validate($field, $this->formData['field_'.$field->id] ?? null);
                if (! empty($fieldRules)) {
                    $rules['formData.field_'.$field->id] = $fieldRules;
                }

                // Validate each child field in each instance
                foreach ($field->children as $child) {
                    $childFieldType = $registry->get($child->field_type);
                    $childRules = $childFieldType->validate($child, null);

                    if (! empty($childRules)) {
                        $rules['formData.field_'.$field->id.'.*.'.'field_'.$child->id] = $childRules;
                    }
                }
            } else {
                // Regular field validation
                $fieldRules = $fieldType->validate($field, $this->formData['field_'.$field->id] ?? null);

                // Get conditional validation rules
                $conditionalRules = $evaluator->getConditionalValidationRules($field, $formDataByElementId, $this->form->fields);

                // Merge base rules with conditional rules
                if (! empty($conditionalRules)) {
                    $fieldRules = array_merge($fieldRules, $conditionalRules);
                }

                if (! empty($fieldRules)) {
                    $rules['formData.field_'.$field->id] = $fieldRules;
                }
            }
        }

        return $rules;
    }

    public function validationAttributes(): array
    {
        $attributes = [];

        // Map field paths to their labels
        foreach ($this->form->fields as $field) {
            // Regular fields
            $attributes['formData.field_'.$field->id] = $field->label;

            // Repeater child fields
            if ($field->field_type === 'repeater') {
                foreach ($field->children as $child) {
                    $attributes['formData.field_'.$field->id.'.*.'.'field_'.$child->id] = $child->label;
                }
            }
        }

        return $attributes;
    }

    public function submit(): void
    {
        // Spam protection validation BEFORE normal validation
        if ($this->form->settings['spam']['enabled'] ?? false) {
            $spamService = app(\DigitalisStudios\SlickForms\Services\SpamProtectionService::class);

            if (! $spamService->validateSubmission($this->form, $this->formData, request()->ip())) {
                $this->addError('spam', 'Your submission was flagged as spam. Please try again later.');

                return;
            }
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger('Validation failed:', [
                'errors' => $e->errors(),
                'rules' => $this->rules(),
            ]);
            throw $e;
        }

        // Save to model if model binding is enabled
        if ($this->form->modelBinding) {
            $modelBindingService = app(\DigitalisStudios\SlickForms\Services\ModelBindingService::class);

            // Convert formData keys from 'field_123' to field names
            $cleanFormData = [];
            foreach ($this->formData as $key => $value) {
                if (str_starts_with($key, 'field_')) {
                    $fieldId = (int) str_replace('field_', '', $key);
                    $field = $this->form->fields->where('id', $fieldId)->first();

                    if ($field) {
                        $cleanFormData[$field->name] = $value;
                    }
                }
            }

            // Save model (create or update)
            $this->model = $modelBindingService->saveModel($this->form, $cleanFormData, $this->model);
        }

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $this->form->id,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'submitted_at' => now(),
            'model_type' => $this->model ? get_class($this->model) : null,
            'model_id' => $this->model?->id,
        ]);

        $registry = app(FieldTypeRegistry::class);

        // Only save visible fields
        foreach ($this->form->fields as $field) {
            if (! in_array($field->id, $this->visibleFieldIds)) {
                continue;
            }

            $fieldType = $registry->get($field->field_type);
            $value = $this->formData['field_'.$field->id] ?? null;
            $processedValue = $fieldType->processValue($value);

            CustomFormFieldValue::create([
                'slick_form_submission_id' => $submission->id,
                'slick_form_field_id' => $field->id,
                'value' => is_array($processedValue) ? json_encode($processedValue) : $processedValue,
            ]);
        }

        // Track analytics - form submitted
        if ($this->analyticsSessionId) {
            $session = SlickFormAnalyticsSession::find($this->analyticsSessionId);
            if ($session) {
                $timeSpent = $session->started_at ? now()->diffInSeconds($session->started_at) : null;

                $session->update([
                    'submitted_at' => now(),
                    'time_spent_seconds' => $timeSpent,
                ]);
            }
        }

        // Store submission for success screen
        $this->lastSubmission = $submission;

        // Check for redirect
        $successAction = $this->determineSuccessAction($submission);

        if ($successAction['type'] === 'redirect') {
            $this->redirect($successAction['url'], navigate: true);

            return;
        }

        $this->submitted = true;
        $this->reset('formData');
    }

    // ==================== Multi-Page Navigation ====================

    public function nextPage(): void
    {
        if (! $this->form->isMultiPage() || $this->isLastPage()) {
            return;
        }

        // Validate current page before proceeding
        $this->validateCurrentPage();

        // Move to next page
        $this->currentPageIndex++;
        $this->currentPageId = $this->pages[$this->currentPageIndex]['id'];

        // Reload form structure for new page
        $layoutService = app(FormLayoutService::class);
        $this->formStructure = $layoutService->getFormStructure($this->form, $this->currentPageId);

        // Update visible fields
        $this->updateVisibleFields();
    }

    public function previousPage(): void
    {
        if (! $this->form->isMultiPage() || $this->isFirstPage()) {
            return;
        }

        // Move to previous page (no validation needed for back)
        $this->currentPageIndex--;
        $this->currentPageId = $this->pages[$this->currentPageIndex]['id'];

        // Reload form structure for new page
        $layoutService = app(FormLayoutService::class);
        $this->formStructure = $layoutService->getFormStructure($this->form, $this->currentPageId);

        // Update visible fields
        $this->updateVisibleFields();
    }

    public function isFirstPage(): bool
    {
        return $this->currentPageIndex === 0;
    }

    public function isLastPage(): bool
    {
        return $this->currentPageIndex === count($this->pages) - 1;
    }

    public function getCurrentPage(): ?array
    {
        return $this->pages[$this->currentPageIndex] ?? null;
    }

    protected function validateCurrentPage(): void
    {
        // Get all fields on current page
        $currentPageFields = $this->form->fields()
            ->where('slick_form_page_id', $this->currentPageId)
            ->get();

        // Build validation rules for current page only
        $rules = [];
        $registry = app(FieldTypeRegistry::class);

        foreach ($currentPageFields as $field) {
            // Skip hidden fields
            if (! in_array($field->id, $this->visibleFieldIds)) {
                continue;
            }

            $fieldType = $registry->get($field->field_type);
            $fieldRules = $fieldType->validate($field, $this->formData['field_'.$field->id] ?? null);

            if (! empty($fieldRules)) {
                $rules['formData.field_'.$field->id] = $fieldRules;
            }

            // Handle repeater children if this is a repeater
            if ($field->field_type === 'repeater') {
                $rules['formData.field_'.$field->id] = ['required', 'array'];

                foreach ($field->children as $child) {
                    $childFieldType = $registry->get($child->field_type);
                    $childRules = $childFieldType->validate($child, null);

                    if (! empty($childRules)) {
                        $rules['formData.field_'.$field->id.'.*.'.'field_'.$child->id] = $childRules;
                    }
                }
            }
        }

        // Validate
        $this->validate($rules);
    }

    // ==================== End Multi-Page Navigation Methods ====================

    // ==================== Analytics Tracking Methods ====================

    protected function initializeAnalyticsSession(): void
    {
        if (! slick_forms_feature_enabled('analytics')) {
            return;
        }

        $session = SlickFormAnalyticsSession::create([
            'slick_form_id' => $this->form->id,
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_type' => $this->detectDeviceType(),
            'browser' => $this->detectBrowser(),
            'current_page_index' => $this->currentPageIndex,
        ]);

        $this->analyticsSessionId = $session->id;
    }

    public function trackFormStart(): void
    {
        if (! slick_forms_feature_enabled('analytics')) {
            return;
        }

        if ($this->analyticsSessionId && ! $this->analyticsStarted) {
            SlickFormAnalyticsSession::where('id', $this->analyticsSessionId)
                ->update(['started_at' => now()]);

            $this->analyticsStarted = true;
        }
    }

    public function trackFieldEvent(int $fieldId, string $eventType, ?string $eventData = null): void
    {
        if (! slick_forms_feature_enabled('analytics')) {
            return;
        }

        if (! $this->analyticsSessionId) {
            return;
        }

        // Automatically start session on first field interaction
        if (! $this->analyticsStarted) {
            $this->trackFormStart();
        }

        SlickFormAnalyticsEvent::create([
            'slick_form_analytics_session_id' => $this->analyticsSessionId,
            'slick_form_field_id' => $fieldId,
            'event_type' => $eventType,
            'event_data' => $eventData,
        ]);
    }

    public function trackValidationError(int $fieldId, string $errorMessage): void
    {
        if (! slick_forms_feature_enabled('analytics')) {
            return;
        }

        $this->trackFieldEvent($fieldId, 'validation_error', $errorMessage);
    }

    protected function detectDeviceType(): string
    {
        $userAgent = request()->userAgent();

        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent)) {
            if (preg_match('/iPad/i', $userAgent)) {
                return 'tablet';
            }

            return 'mobile';
        }

        return 'desktop';
    }

    protected function detectBrowser(): ?string
    {
        $userAgent = request()->userAgent();

        if (preg_match('/Edge/i', $userAgent)) {
            return 'Edge';
        }
        if (preg_match('/Chrome/i', $userAgent)) {
            return 'Chrome';
        }
        if (preg_match('/Safari/i', $userAgent)) {
            return 'Safari';
        }
        if (preg_match('/Firefox/i', $userAgent)) {
            return 'Firefox';
        }
        if (preg_match('/MSIE|Trident/i', $userAgent)) {
            return 'Internet Explorer';
        }

        return null;
    }

    // ==================== End Analytics Tracking Methods ====================

    // ==================== Success Screen Methods ====================

    /**
     * Determine the success action (message or redirect)
     */
    public function determineSuccessAction(CustomFormSubmission $submission): array
    {
        $settings = $this->form->settings['success_screen'] ?? [];

        // Check conditional redirects first (highest priority)
        $conditionalRedirects = $settings['conditional_redirects'] ?? [];
        usort($conditionalRedirects, function ($a, $b) {
            return ($b['priority'] ?? 0) <=> ($a['priority'] ?? 0);
        });

        foreach ($conditionalRedirects as $rule) {
            if ($this->evaluateRedirectConditions($rule['conditions'] ?? [], $submission)) {
                return [
                    'type' => 'redirect',
                    'url' => $this->parseRedirectUrl($rule['url'] ?? '', $submission),
                ];
            }
        }

        // Check default redirect or message_then_redirect
        $actionType = $settings['action_type'] ?? 'message';

        if ($actionType === 'message_then_redirect') {
            return [
                'type' => 'message_then_redirect',
                'url' => $this->parseRedirectUrl($settings['redirect_url'] ?? '', $submission),
            ];
        }

        if ($actionType === 'redirect' || ($settings['redirect_enabled'] ?? false)) {
            return [
                'type' => 'redirect',
                'url' => $this->parseRedirectUrl($settings['redirect_url'] ?? '', $submission),
            ];
        }

        // Show success message
        return ['type' => 'message'];
    }

    /**
     * Evaluate conditional redirect conditions
     */
    protected function evaluateRedirectConditions(array $conditions, CustomFormSubmission $submission): bool
    {
        if (empty($conditions)) {
            return false;
        }

        $evaluator = app(ConditionalLogicEvaluator::class);

        // Build form data from submission values (use field names as keys)
        $formDataForEval = [];
        foreach ($submission->fieldValues as $value) {
            $field = $this->form->fields->firstWhere('id', $value->slick_form_field_id);
            if ($field) {
                $formDataForEval[$field->name] = $value->value;
            }
        }

        // Evaluate each condition with AND logic
        foreach ($conditions as $condition) {
            $targetField = $condition['target_field'] ?? null;
            $operator = $condition['operator'] ?? 'equals';
            $expectedValue = $condition['value'] ?? null;

            if (! $targetField || ! isset($formDataForEval[$targetField])) {
                return false;
            }

            $actualValue = $formDataForEval[$targetField];

            // Evaluate based on operator
            $matches = match ($operator) {
                'equals', '==' => $actualValue == $expectedValue,
                'not_equals', '!=' => $actualValue != $expectedValue,
                'contains' => str_contains((string) $actualValue, (string) $expectedValue),
                'not_contains' => ! str_contains((string) $actualValue, (string) $expectedValue),
                'greater_than', '>' => $actualValue > $expectedValue,
                'less_than', '<' => $actualValue < $expectedValue,
                'greater_than_or_equal', '>=' => $actualValue >= $expectedValue,
                'less_than_or_equal', '<=' => $actualValue <= $expectedValue,
                default => false,
            };

            if (! $matches) {
                return false;
            }
        }

        return true;
    }

    /**
     * Parse redirect URL with submission data variables
     */
    protected function parseRedirectUrl(string $url, ?CustomFormSubmission $submission = null): string
    {
        // If no submission provided, return URL as-is
        if (! $submission) {
            return $url;
        }

        // Replace {{submission.id}} with actual submission ID
        $url = str_replace('{{submission.id}}', $submission->id, $url);
        $url = str_replace('{{ submission.id }}', $submission->id, $url);

        // Replace field values like {{field.email}}
        foreach ($submission->fieldValues as $value) {
            $fieldName = $value->field->name;
            $url = str_replace('{{'.$fieldName.'}}', $value->value, $url);
            $url = str_replace('{{ '.$fieldName.' }}', $value->value, $url);
        }

        // Add submission ID as query parameter if enabled
        $settings = $this->form->settings['success_screen'] ?? [];
        if ($settings['redirect_pass_submission_id'] ?? $settings['pass_submission_id'] ?? false) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= $separator.'submission_id='.$submission->id;
        }

        return $url;
    }

    /**
     * Render success message with Blade template support
     */
    public function renderSuccessMessage(): string
    {
        $settings = $this->form->settings['success_screen'] ?? [];
        $body = $settings['message_body'] ?? '<p>Your submission has been received. We\'ll be in touch soon.</p>';

        if (! $this->lastSubmission) {
            return $body;
        }

        // Replace submission variables
        $body = str_replace('{{submission.id}}', $this->lastSubmission->id, $body);
        $body = str_replace('{{ submission.id }}', $this->lastSubmission->id, $body);
        $body = str_replace('{{submission.created_at}}', $this->lastSubmission->created_at->format('Y-m-d H:i:s'), $body);
        $body = str_replace('{{ submission.created_at }}', $this->lastSubmission->created_at->format('Y-m-d H:i:s'), $body);

        // Replace field values
        foreach ($this->lastSubmission->fieldValues as $value) {
            $fieldName = $value->field->name;
            $body = str_replace('{{'.$fieldName.'}}', $value->value, $body);
            $body = str_replace('{{ '.$fieldName.' }}', $value->value, $body);
        }

        return $body;
    }

    /**
     * Get success screen settings
     */
    public function getSuccessSettings(): array
    {
        return $this->form->settings['success_screen'] ?? [];
    }

    // ==================== End Success Screen Methods ====================

    public function render()
    {
        return view('slick-forms::livewire.form-renderer', [
            'registry' => app(FieldTypeRegistry::class),
            'visibleFieldIds' => $this->visibleFieldIds,
            'repeaterInstances' => $this->repeaterInstances,
        ]);
    }

    /**
     * Public method to validate a single field (called from wire:blur)
     */
    public function validateField(string $fieldPath): void
    {
        $this->validateOnly($fieldPath);
    }
}
