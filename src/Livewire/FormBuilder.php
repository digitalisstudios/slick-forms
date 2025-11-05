<?php

namespace DigitalisStudios\SlickForms\Livewire;

use DigitalisStudios\SlickForms\Livewire\Concerns\ManagesSchemaProperties;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use DigitalisStudios\SlickForms\Services\FieldTypeRegistry;
use DigitalisStudios\SlickForms\Services\FormLayoutService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormBuilder extends Component
{
    use ManagesSchemaProperties;
    use WithFileUploads;

    public CustomForm $form;

    public string $formName = '';

    public string $formDescription = '';

    public bool $formIsActive = true;

    public bool $formIsPublic = true;

    public ?string $formExpiresAt = null;

    public int $formTimeLimit = 0;

    public ?CustomFormField $selectedField = null;

    public ?SlickFormLayoutElement $selectedElement = null;

    public array $availableFieldTypes = [];

    public array $availableContentTypes = [];

    public array $availableFormFieldTypes = [];

    public array $availableLayoutTypes = [];

    public bool $showFieldEditor = false;

    public bool $showElementEditor = false;

    public string $activePropertiesTab = 'basic';

    public array $formStructure = [];

    public bool $previewMode = false;

    public string $viewportMode = 'desktop'; // desktop, tablet, mobile

    public bool $showDeleteConfirmation = false;

    public ?int $fieldToDelete = null;

    public array $deletionDependencies = [];

    public bool $pickerMode = false;

    public ?string $pickerTarget = null;

    public ?string $errorMessage = null;

    // Validation & Conditional Logic Properties (not schema-driven due to complexity)
    public bool $fieldIsRequired = false;

    public array $fieldValidationOptions = [];

    public array $fieldConditionalLogic = [];

    // Multi-Page Form Properties
    public ?int $currentPageId = null;

    public array $pages = [];

    public bool $showPageEditor = false;

    public ?int $selectedPageId = null;

    public $pageTitle = '';

    public $pageDescription = '';

    public $pageIcon = '';

    public $pageShowInProgress = true;

    // Version Management Properties
    public bool $showVersionHistory = false;

    public array $versions = [];

    public ?int $selectedVersionId = null;

    public bool $showVersionComparison = false;

    public ?int $compareVersionId1 = null;

    public ?int $compareVersionId2 = null;

    public array $versionDifferences = [];

    // Table Cell Editor Properties
    public ?int $selectedTableElementId = null;

    public ?string $selectedTableCellId = null;

    public int $selectedTableCellColspan = 1;

    public int $selectedTableCellRowspan = 1;

    public string $selectedTableCellAlign = 'left';

    public string $selectedTableCellValign = 'middle';

    public string $selectedTableCellWidth = '';

    public string $selectedTableCellClasses = '';

    public string $selectedTableCellStyle = '';

    public string $selectedTableCellBackground = '';

    public string $selectedTableCellTextContent = '';

    public string $selectedTableCellContentType = 'text';

    public ?int $selectedTableCellFieldId = null;

    public ?int $selectedTableCellElementId = null;

    // Email Notification Properties
    public bool $emailEnabled = false;

    public array $adminEmailTemplates = [];

    public bool $userConfirmationEnabled = false;

    public ?int $userEmailFieldId = null;

    public string $userConfirmationSubject = 'Thank you for your submission';

    public array $userConfirmationTemplate = [];

    // Form-Level Settings UI State
    public bool $showFormEditor = false;

    // Single property to prevent flash when switching panels - updated by methods
    public bool $anyPanelOpen = false;

    /**
     * Update anyPanelOpen based on current panel states
     */
    protected function updateAnyPanelOpen(): void
    {
        $this->anyPanelOpen = $this->showFieldEditor || $this->showElementEditor || $this->showPageEditor || $this->showFormEditor || $this->selectedTableCellId !== null;
    }

    public bool $showEmailLogsModal = false;

    public bool $showEmailTemplateModal = false;

    public ?int $editingTemplateIndex = null;

    // Share Panel Properties
    public bool $showSharePanel = false;

    public array $prefillData = [];

    public ?string $generatedPrefillUrl = null;

    // Email Template Editor Properties
    public array $editingTemplate = [];

    public string $templateRecipients = '';

    public string $templateSubject = '';

    public int $templatePriority = 1;

    public bool $templateAttachPdf = false;

    public bool $templateEnabled = true;

    public array $templateConditionalRules = [];

    // Spam Protection Properties
    public bool $spamProtectionEnabled = false;

    public bool $honeypotEnabled = false;

    public string $honeypotFieldName = 'website';

    public int $honeypotTimeThreshold = 3;

    public string $captchaType = 'none';

    public string $recaptchaSiteKey = '';

    public string $recaptchaSecretKey = '';

    public float $recaptchaScoreThreshold = 0.5;

    public string $hcaptchaSiteKey = '';

    public string $hcaptchaSecretKey = '';

    public bool $rateLimitEnabled = false;

    public int $rateLimitMaxAttempts = 5;

    public int $rateLimitDecayMinutes = 60;

    // Spam UI State
    public bool $showSpamLogsModal = false;

    // Model Binding Properties
    public bool $modelBindingEnabled = false;

    public string $modelClass = '';

    public string $routeParameter = 'model';

    public string $routeKey = 'id';

    public bool $allowCreate = true;

    public bool $allowUpdate = true;

    public array $fieldMappings = [];

    public array $relationshipMappings = [];

    // Success Screen Properties
    public string $successActionType = 'message';

    public string $messageTitle = 'Thank you!';

    public string $messageBody = '<p>Your submission has been received. We\'ll be in touch soon.</p>';

    public bool $showSubmissionData = false;

    public array $hiddenFields = [];

    public string $redirectUrl = '';

    public int $redirectDelay = 3;

    public bool $passSubmissionId = false;

    public array $conditionalRedirects = [];

    public bool $enablePdfDownload = false;

    public string $pdfButtonText = 'Download PDF';

    public bool $enableCsvDownload = false;

    public string $csvButtonText = 'Download CSV';

    public bool $enableEditLink = false;

    public string $editLinkText = 'Edit Your Submission';

    public int $editLinkExpiration = 24;

    // Webhook Properties
    public array $webhooks = [];

    public bool $showWebhookEditor = false;

    public ?int $editingWebhookId = null;

    public string $webhookName = '';

    public string $webhookUrl = '';

    public string $webhookMethod = 'POST';

    public array $webhookHeaders = [];

    public string $webhookFormat = 'json';

    public array $webhookTriggerConditions = [];

    public bool $webhookEnabled = true;

    public int $webhookMaxRetries = 3;

    public int $webhookRetryDelay = 60;

    public ?array $webhookTestResult = null;

    // Settings-only mode (hide canvas/sidebar, show Form Settings panel only)
    public bool $settingsOnly = false;

    public function mount(int $formId, bool $openFormSettings = false, bool $settingsOnly = false): void
    {
        $this->form = CustomForm::with(['layoutElements', 'fields', 'pages'])->findOrFail($formId);
        $this->formName = $this->form->name;
        $this->formDescription = $this->form->description ?? '';
        $this->formIsActive = $this->form->is_active;
        $this->formIsPublic = $this->form->is_public ?? true;
        $this->formExpiresAt = $this->form->expires_at?->format('Y-m-d\TH:i');
        $this->formTimeLimit = $this->form->time_limited ? (int) ceil($this->form->time_limited / 60) : 0;
        $this->loadAvailableFieldTypes();
        $this->loadAvailableLayoutTypes();
        $this->loadPages();
        $this->loadFormStructure();
        $this->loadEmailSettings();
        $this->loadSpamSettings();
        $this->loadModelBinding();
        $this->loadSuccessSettings();
        $this->loadWebhookSettings();

        // Optionally open Form Settings on load (used by manage/edit view)
        if ($openFormSettings) {
            $this->showFormSettings();
        }

        // Apply settings-only mode
        $this->settingsOnly = $settingsOnly;
        if ($this->settingsOnly && ! $this->showFormEditor) {
            $this->showFormSettings();
        }
    }

    public function loadAvailableFieldTypes(): void
    {
        $registry = app(FieldTypeRegistry::class);
        $contentFieldNames = ['header', 'paragraph', 'image', 'video', 'pdf_embed', 'code'];

        $allFields = collect($registry->all())
            ->map(fn ($fieldType) => [
                'name' => $fieldType->getName(),
                'label' => $fieldType->getLabel(),
                'icon' => $fieldType->getIcon(),
            ]);

        // Sort content types in the specified order
        $contentFields = [];
        foreach ($contentFieldNames as $name) {
            $field = $allFields->firstWhere('name', $name);
            if ($field) {
                $contentFields[] = $field;
            }
        }
        $this->availableContentTypes = $contentFields;

        $this->availableFormFieldTypes = $allFields
            ->filter(fn ($field) => ! in_array($field['name'], $contentFieldNames))
            ->values()
            ->toArray();

        $this->availableFieldTypes = $allFields->toArray();
    }

    public function loadAvailableLayoutTypes(): void
    {
        $this->availableLayoutTypes = [
            [
                'name' => 'container',
                'label' => 'Container',
                'icon' => 'bi bi-box',
            ],
            [
                'name' => 'row',
                'label' => 'Row',
                'icon' => 'bi bi-distribute-vertical',
            ],
            [
                'name' => 'column',
                'label' => 'Column',
                'icon' => 'bi bi-distribute-horizontal',
            ],
            [
                'name' => 'card',
                'label' => 'Card',
                'icon' => 'bi bi-card-text',
            ],
            [
                'name' => 'accordion',
                'label' => 'Accordion',
                'icon' => 'bi bi-list-nested',
            ],
            [
                'name' => 'accordion_item',
                'label' => 'Accordion Item',
                'icon' => 'bi bi-caret-right-square',
            ],
            [
                'name' => 'tabs',
                'label' => 'Tabs',
                'icon' => 'bi bi-segmented-nav',
            ],
            [
                'name' => 'tab',
                'label' => 'Tab',
                'icon' => 'bi bi-file-earmark',
            ],
            [
                'name' => 'carousel',
                'label' => 'Carousel',
                'icon' => 'bi bi-collection-play',
            ],
            [
                'name' => 'table',
                'label' => 'Table',
                'icon' => 'bi bi-table',
            ],
        ];
    }

    public function loadFormStructure(): void
    {
        $layoutService = app(FormLayoutService::class);
        $this->formStructure = $layoutService->getFormStructure($this->form, $this->currentPageId);
    }

    public function selectElement(?int $elementId): void
    {
        $this->selectedElement = $elementId ? SlickFormLayoutElement::find($elementId) : null;
        $this->closeFieldEditor();
    }

    public function editElement(int $elementId): void
    {
        try {
            $element = SlickFormLayoutElement::findOrFail($elementId);

            // If clicking a container while properties panel is open, just close the panel
            if ($element->element_type === 'container' && ($this->showFieldEditor || $this->showElementEditor)) {
                $this->closeFieldEditor();
                $this->closeElementEditor();

                return;
            }

            $this->editElementV3($elementId);
            $this->showElementEditor = true;
            $this->anyPanelOpen = true;
            $this->closeFieldEditor();
            $this->closeFormSettings();
            $this->dispatch('element-editor-opened');
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to load element: '.$e->getMessage();
            logger()->error('Edit element error', ['elementId' => $elementId, 'error' => $e->getMessage()]);
        }
    }

    public function saveElement(): void
    {
        if (! $this->selectedElement) {
            return;
        }

        $elementId = $this->selectedElement->id;
        $elementType = $this->selectedElement->element_type;

        $this->saveElementV3();
        $this->loadFormStructure();

        // Dispatch event to notify carousel to re-initialize
        $this->dispatch('element-saved', elementId: $elementId, elementType: $elementType);
    }

    public function dismissError(): void
    {
        $this->errorMessage = null;
    }

    public function closeElementEditor(): void
    {
        $this->showElementEditor = false;
        $this->selectedElement = null;
        $this->dispatch('element-editor-closed');
    }

    public function addField(string $fieldType, ?int $parentElementIdOrFieldId = null, bool $isParentField = false, ?int $atIndex = null): void
    {
        $parentElementId = null;
        $parentFieldId = null;

        // Determine if the parent is a field (repeater) or layout element
        if ($isParentField) {
            $parentFieldId = $parentElementIdOrFieldId;
        } else {
            $parentElementId = $parentElementIdOrFieldId;
            // Use selected element as parent if no parent specified
            if ($parentElementId === null && $this->selectedElement) {
                $parentElementId = $this->selectedElement->id;
            }
        }

        // Determine order for the new field
        if ($atIndex !== null) {
            // Insert at specific position - shift existing items down
            if ($parentFieldId) {
                CustomFormField::where('slick_form_id', $this->form->id)
                    ->where('parent_field_id', $parentFieldId)
                    ->where('order', '>=', $atIndex)
                    ->increment('order');
            } else {
                CustomFormField::where('slick_form_id', $this->form->id)
                    ->where('slick_form_layout_element_id', $parentElementId)
                    ->whereNull('parent_field_id')
                    ->where('order', '>=', $atIndex)
                    ->increment('order');
            }
            $order = $atIndex;
        } else {
            // Calculate max order based on parent type (add at the end)
            if ($parentFieldId) {
                $maxOrder = CustomFormField::where('slick_form_id', $this->form->id)
                    ->where('parent_field_id', $parentFieldId)
                    ->max('order') ?? -1;
            } else {
                $maxOrder = CustomFormField::where('slick_form_id', $this->form->id)
                    ->where('slick_form_layout_element_id', $parentElementId)
                    ->whereNull('parent_field_id')
                    ->max('order') ?? -1;
            }
            $order = $maxOrder + 1;
        }

        // Auto-generate unique field name
        $baseName = $fieldType.'_field';
        $name = $baseName;
        $counter = 1;

        while (CustomFormField::where('slick_form_id', $this->form->id)
            ->where('name', $name)
            ->exists()) {
            $name = $baseName.'_'.$counter;
            $counter++;
        }

        // Auto-generate unique element_id
        $elementIdBase = $fieldType.'_'.uniqid();
        $elementId = $elementIdBase;
        $elementIdCounter = 1;

        while (CustomFormField::where('slick_form_id', $this->form->id)
            ->where('element_id', $elementId)
            ->exists()) {
            $elementId = $elementIdBase.'_'.$elementIdCounter;
            $elementIdCounter++;
        }

        $registry = app(FieldTypeRegistry::class);
        $fieldTypeInstance = $registry->get($fieldType);
        $fieldTypeLabel = $fieldTypeInstance->getLabel();

        // Content field types should have show_label = false by default
        $contentFieldTypes = ['image', 'video', 'pdf_embed', 'paragraph', 'header', 'code'];
        $showLabel = ! in_array($fieldType, $contentFieldTypes);

        // Apply default options from field type's config schema
        $defaultOptions = [];
        $configSchema = $fieldTypeInstance->getConfigSchema();
        foreach ($configSchema as $key => $config) {
            if (isset($config['default'])) {
                $target = $config['target'] ?? 'options';
                // Only add to defaultOptions if target is 'options'
                if ($target === 'options') {
                    $defaultOptions[$key] = $config['default'];
                }
            }
        }

        $field = CustomFormField::create([
            'slick_form_id' => $this->form->id,
            'slick_form_layout_element_id' => $parentElementId,
            'parent_field_id' => $parentFieldId,
            'field_type' => $fieldType,
            'name' => $name,
            'element_id' => $elementId,
            'label' => 'New '.$fieldTypeLabel,
            'order' => $order,
            'is_required' => false,
            'show_label' => $showLabel,
            'options' => $defaultOptions,
        ]);

        $this->loadFormStructure();
        $this->editField($field->id);
        $this->dispatch('scroll-to-field', fieldId: $field->id);
    }

    public function addLayoutElement(string $elementType, ?int $parentElementId = null, array $customSettings = [], ?int $atIndex = null): void
    {
        // Use selected element as parent if no parent specified
        if ($parentElementId === null && $this->selectedElement) {
            $parentElementId = $this->selectedElement->id;
        }

        $layoutService = app(FormLayoutService::class);
        $parent = $parentElementId ? SlickFormLayoutElement::find($parentElementId) : null;

        // Validate that this element can be added to this parent
        if (! $layoutService->canAddChild($parent, $elementType)) {
            $this->dispatch('error', message: 'This layout element cannot be added here.');

            return;
        }

        // Determine order for the new element
        if ($atIndex !== null) {
            // Insert at specific position - shift existing items down
            SlickFormLayoutElement::where('slick_form_id', $this->form->id)
                ->where('parent_id', $parentElementId)
                ->where('order', '>=', $atIndex)
                ->increment('order');
            $order = $atIndex;
        } else {
            // Add at the end
            $maxOrder = SlickFormLayoutElement::where('slick_form_id', $this->form->id)
                ->where('parent_id', $parentElementId)
                ->max('order') ?? -1;
            $order = $maxOrder + 1;
        }

        // Get default settings from element type class if available
        $registry = app(\DigitalisStudios\SlickForms\Services\LayoutElementRegistry::class);
        $defaultSettings = [];

        if ($registry->has($elementType)) {
            $elementTypeInstance = $registry->get($elementType);
            if (method_exists($elementTypeInstance, 'getDefaultSettings')) {
                $defaultSettings = $elementTypeInstance->getDefaultSettings();
            }
        }

        // Merge defaults with custom settings (custom settings take precedence)
        $settings = array_merge($defaultSettings, $customSettings);

        // Special handling for column width if not provided
        if ($elementType === 'column' && empty($settings['column_width'])) {
            $settings['column_width'] = 'equal';
        }

        $element = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'parent_id' => $parentElementId,
            'element_type' => $elementType,
            'element_id' => $this->generateUniqueElementId($elementType),
            'order' => $order,
            'settings' => $settings,
        ]);

        // If it's a row, automatically add 2 equal-width columns
        if ($elementType === 'row') {
            SlickFormLayoutElement::create([
                'slick_form_id' => $this->form->id,
                'parent_id' => $element->id,
                'element_type' => 'column',
                'element_id' => $this->generateUniqueElementId('column'),
                'order' => 0,
                'settings' => ['column_width' => 'equal'],
            ]);

            SlickFormLayoutElement::create([
                'slick_form_id' => $this->form->id,
                'parent_id' => $element->id,
                'element_type' => 'column',
                'element_id' => $this->generateUniqueElementId('column'),
                'order' => 1,
                'settings' => ['column_width' => 'equal'],
            ]);
        }

        // If it's tabs, automatically add 3 tab elements
        if ($elementType === 'tabs') {
            SlickFormLayoutElement::create([
                'slick_form_id' => $this->form->id,
                'parent_id' => $element->id,
                'element_type' => 'tab',
                'element_id' => $this->generateUniqueElementId('tab'),
                'order' => 0,
                'settings' => ['tab_label' => 'Tab 1'],
            ]);

            SlickFormLayoutElement::create([
                'slick_form_id' => $this->form->id,
                'parent_id' => $element->id,
                'element_type' => 'tab',
                'element_id' => $this->generateUniqueElementId('tab'),
                'order' => 1,
                'settings' => ['tab_label' => 'Tab 2'],
            ]);

            SlickFormLayoutElement::create([
                'slick_form_id' => $this->form->id,
                'parent_id' => $element->id,
                'element_type' => 'tab',
                'element_id' => $this->generateUniqueElementId('tab'),
                'order' => 2,
                'settings' => ['tab_label' => 'Tab 3'],
            ]);
        }

        // If it's a table, initialize with nested element structure (header + 3x3 body)
        if ($elementType === 'table') {
            app(FormLayoutService::class)->createDefaultTableStructure($element);
        }

        // Check if element type has a default preset to apply
        if ($registry->has($elementType)) {
            $elementTypeInstance = $registry->get($elementType);
            if (method_exists($elementTypeInstance, 'getDefaultPreset')) {
                $presetKey = $elementTypeInstance->getDefaultPreset();
                if ($presetKey && method_exists($this, 'apply'.ucfirst($elementType).'Preset')) {
                    $this->{'apply'.ucfirst($elementType).'Preset'}($element->id, $presetKey);
                }
            }
        }

        // Auto-select the newly created element
        $this->selectedElement = $element;

        $this->loadFormStructure();

        // Check if element type should auto-open properties panel
        if ($registry->has($elementType)) {
            $elementTypeInstance = $registry->get($elementType);
            if (method_exists($elementTypeInstance, 'shouldAutoOpenProperties') && $elementTypeInstance->shouldAutoOpenProperties()) {
                $this->editElementV3($element->id);
                $this->showElementEditor = true;
                $this->anyPanelOpen = true;
                $this->dispatch('element-editor-opened');
            }
        }

        $this->dispatch('scroll-to-element', elementId: $element->id);
    }

    /**
     * Add a layout element under a repeater field (parent_field_id).
     */
    public function addLayoutElementToField(string $elementType, int $parentFieldId, array $customSettings = [], ?int $atIndex = null): void
    {
        $layoutService = app(FormLayoutService::class);
        if (! $layoutService->canAddChildToField($elementType)) {
            $this->dispatch('error', message: 'This layout element cannot be added inside a repeater.');

            return;
        }

        // Determine order for the new element within the repeater
        if ($atIndex !== null) {
            SlickFormLayoutElement::where('slick_form_id', $this->form->id)
                ->where('parent_field_id', $parentFieldId)
                ->where('order', '>=', $atIndex)
                ->increment('order');
            $order = $atIndex;
        } else {
            $maxOrder = SlickFormLayoutElement::where('slick_form_id', $this->form->id)
                ->where('parent_field_id', $parentFieldId)
                ->max('order') ?? -1;
            $order = $maxOrder + 1;
        }

        $registry = app(\DigitalisStudios\SlickForms\Services\LayoutElementRegistry::class);
        $defaultSettings = [];
        if ($registry->has($elementType)) {
            $elementTypeInstance = $registry->get($elementType);
            if (method_exists($elementTypeInstance, 'getDefaultSettings')) {
                $defaultSettings = $elementTypeInstance->getDefaultSettings();
            }
        }
        $settings = array_merge($defaultSettings, $customSettings);

        $element = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'parent_id' => null,
            'parent_field_id' => $parentFieldId,
            'element_type' => $elementType,
            'element_id' => $this->generateUniqueElementId($elementType),
            'order' => $order,
            'settings' => $settings,
        ]);

        // If it's a row, add default equal columns (like elsewhere)
        if ($elementType === 'row') {
            SlickFormLayoutElement::create([
                'slick_form_id' => $this->form->id,
                'parent_id' => $element->id,
                'element_type' => 'column',
                'element_id' => $this->generateUniqueElementId('column'),
                'order' => 0,
                'settings' => ['column_width' => 'equal'],
            ]);
            SlickFormLayoutElement::create([
                'slick_form_id' => $this->form->id,
                'parent_id' => $element->id,
                'element_type' => 'column',
                'element_id' => $this->generateUniqueElementId('column'),
                'order' => 1,
                'settings' => ['column_width' => 'equal'],
            ]);
        }

        $this->loadFormStructure();
        $this->selectedElement = $element;
        $this->dispatch('scroll-to-element', elementId: $element->id);
    }

    /** Move an existing layout element into a repeater */
    public function moveElementToRepeater(int $elementId, int $repeaterFieldId): void
    {
        $element = SlickFormLayoutElement::find($elementId);
        if (! $element) {
            return;
        }

        $element->parent_field_id = $repeaterFieldId;
        $element->parent_id = null; // detach from previous element parent if any
        $maxOrder = SlickFormLayoutElement::where('parent_field_id', $repeaterFieldId)->max('order') ?? 0;
        $element->order = $maxOrder + 1;
        $element->save();

        $this->loadFormStructure();
    }

    /** Update mixed order (fields + elements) under a repeater */
    public function updateChildrenOrderInRepeater(int $repeaterFieldId, array $orderedItems): void
    {
        foreach ($orderedItems as $index => $item) {
            if ($item['type'] === 'field') {
                CustomFormField::where('id', $item['id'])
                    ->where('parent_field_id', $repeaterFieldId)
                    ->update(['order' => $index]);
            } elseif ($item['type'] === 'element') {
                SlickFormLayoutElement::where('id', $item['id'])
                    ->where('parent_field_id', $repeaterFieldId)
                    ->update(['order' => $index]);
            }
        }

        $this->loadFormStructure();
    }

    public function editField(int $fieldId): void
    {
        try {
            // Reset special properties to ensure clean state
            $this->fieldIsRequired = false;
            $this->fieldValidationOptions = [];
            $this->fieldConditionalLogic = [];

            $this->editFieldV3($fieldId);
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to load field: '.$e->getMessage();
            logger()->error('Edit field error', ['fieldId' => $fieldId, 'error' => $e->getMessage()]);
        }
    }

    public function updatedProperties($value, $key)
    {
        // Handle image upload for Image field
        if ($key === 'image_upload' && $value) {
            $path = $value->store('form-builder-images', 'public');
            $this->properties['image_url'] = asset('storage/'.$path);
            $this->properties['image_upload'] = null; // Clear the upload field
        }

        // Handle PDF upload for PDF Embed field
        if ($key === 'pdf_upload' && $value) {
            $path = $value->store('form-builder-docs', 'public');
            $this->properties['pdf_url'] = asset('storage/'.$path);
            $this->properties['pdf_upload'] = null;
        }
    }

    public function saveField(): void
    {
        $this->saveFieldV3();
    }

    public function deleteField(int $fieldId): void
    {
        // Check if any fields depend on this field via conditional logic
        $dependencies = $this->findFieldDependencies($fieldId);

        if (! empty($dependencies)) {
            // Show confirmation dialog with dependencies
            $this->fieldToDelete = $fieldId;
            $this->deletionDependencies = $dependencies;
            $this->showDeleteConfirmation = true;
        } else {
            // No dependencies, safe to delete immediately
            $this->executeFieldDeletion($fieldId);
        }
    }

    public function confirmDeleteField(): void
    {
        if ($this->fieldToDelete) {
            // Clean up broken references in dependent fields
            $this->cleanupBrokenReferences($this->fieldToDelete);

            // Delete the field
            $this->executeFieldDeletion($this->fieldToDelete);

            // Reset confirmation state
            $this->cancelDeleteField();
        }
    }

    public function cancelDeleteField(): void
    {
        $this->showDeleteConfirmation = false;
        $this->fieldToDelete = null;
        $this->deletionDependencies = [];
    }

    protected function executeFieldDeletion(int $fieldId): void
    {
        // Close properties panel if this field is selected
        if ($this->selectedField && $this->selectedField->id === $fieldId) {
            $this->closeFieldEditor();
        }

        CustomFormField::destroy($fieldId);
        $this->loadFormStructure();
    }

    protected function findFieldDependencies(int $fieldId): array
    {
        $dependencies = [];
        $allFields = CustomFormField::where('slick_form_id', $this->form->id)->get();

        foreach ($allFields as $field) {
            if (! $field->conditional_logic || $field->id === $fieldId) {
                continue;
            }

            $logic = $field->conditional_logic;
            $hasDependency = false;

            // Check visibility conditions
            if (isset($logic['conditions']) && is_array($logic['conditions'])) {
                foreach ($logic['conditions'] as $condition) {
                    if (isset($condition['target_field_id']) && $condition['target_field_id'] == $fieldId) {
                        $hasDependency = true;
                        break;
                    }
                }
            }

            // Check conditional validation
            if (! $hasDependency && isset($logic['conditional_validation']) && is_array($logic['conditional_validation'])) {
                foreach ($logic['conditional_validation'] as $validationRule) {
                    if (isset($validationRule['conditions']) && is_array($validationRule['conditions'])) {
                        foreach ($validationRule['conditions'] as $condition) {
                            if (isset($condition['target_field_id']) && $condition['target_field_id'] == $fieldId) {
                                $hasDependency = true;
                                break 2;
                            }
                        }
                    }
                }
            }

            if ($hasDependency) {
                // Count total conditions referencing this field
                $conditionCount = 0;

                // Count visibility conditions
                if (isset($logic['conditions']) && is_array($logic['conditions'])) {
                    foreach ($logic['conditions'] as $condition) {
                        if (isset($condition['target_field_id']) && $condition['target_field_id'] == $fieldId) {
                            $conditionCount++;
                        }
                    }
                }

                // Count conditional validation conditions
                if (isset($logic['conditional_validation']) && is_array($logic['conditional_validation'])) {
                    foreach ($logic['conditional_validation'] as $validationRule) {
                        if (isset($validationRule['conditions']) && is_array($validationRule['conditions'])) {
                            foreach ($validationRule['conditions'] as $condition) {
                                if (isset($condition['target_field_id']) && $condition['target_field_id'] == $fieldId) {
                                    $conditionCount++;
                                }
                            }
                        }
                    }
                }

                $dependencies[] = [
                    'field_id' => $field->id,
                    'field_label' => $field->label,
                    'field_element_id' => $field->element_id,
                    'condition_count' => $conditionCount,
                ];
            }
        }

        return $dependencies;
    }

    protected function cleanupBrokenReferences(int $deletedFieldId): void
    {
        $affectedFields = CustomFormField::where('slick_form_id', $this->form->id)
            ->whereNotNull('conditional_logic')
            ->get();

        foreach ($affectedFields as $field) {
            $logic = $field->conditional_logic;
            $hasChanges = false;

            // Clean up visibility conditions
            if (isset($logic['conditions']) && is_array($logic['conditions'])) {
                $originalCount = count($logic['conditions']);
                $logic['conditions'] = array_filter($logic['conditions'], function ($condition) use ($deletedFieldId) {
                    return ! isset($condition['target_field_id']) || $condition['target_field_id'] != $deletedFieldId;
                });
                $logic['conditions'] = array_values($logic['conditions']);

                if (count($logic['conditions']) !== $originalCount) {
                    $hasChanges = true;
                }
            }

            // Clean up conditional validation
            if (isset($logic['conditional_validation']) && is_array($logic['conditional_validation'])) {
                foreach ($logic['conditional_validation'] as $valIndex => $validationRule) {
                    if (isset($validationRule['conditions']) && is_array($validationRule['conditions'])) {
                        $originalCount = count($validationRule['conditions']);
                        $logic['conditional_validation'][$valIndex]['conditions'] = array_filter(
                            $validationRule['conditions'],
                            function ($condition) use ($deletedFieldId) {
                                return ! isset($condition['target_field_id']) || $condition['target_field_id'] != $deletedFieldId;
                            }
                        );
                        $logic['conditional_validation'][$valIndex]['conditions'] = array_values(
                            $logic['conditional_validation'][$valIndex]['conditions']
                        );

                        if (count($logic['conditional_validation'][$valIndex]['conditions']) !== $originalCount) {
                            $hasChanges = true;
                        }

                        // Remove validation rule if it has no conditions left
                        if (empty($logic['conditional_validation'][$valIndex]['conditions'])) {
                            unset($logic['conditional_validation'][$valIndex]);
                            $hasChanges = true;
                        }
                    }
                }

                $logic['conditional_validation'] = array_values($logic['conditional_validation']);
            }

            // Update field if changes were made
            if ($hasChanges) {
                $field->update(['conditional_logic' => $logic]);
            }
        }
    }

    /**
     * Check if an element or any of its descendants is currently selected
     */
    protected function isElementOrDescendantSelected(SlickFormLayoutElement $element): bool
    {
        // Check if this element is selected
        if ($this->selectedElement && $this->selectedElement->id === $element->id) {
            return true;
        }

        // Recursively check all child elements
        foreach ($element->children as $child) {
            if ($this->isElementOrDescendantSelected($child)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a field is within (belongs to) a given element or its descendants
     */
    protected function isFieldWithinElement(int $fieldId, int $elementId): bool
    {
        $element = SlickFormLayoutElement::find($elementId);

        if (! $element) {
            return false;
        }

        // Check if field is directly attached to this element
        if ($element->fields()->where('id', $fieldId)->exists()) {
            return true;
        }

        // Recursively check all child elements
        foreach ($element->children as $child) {
            if ($this->isFieldWithinElement($fieldId, $child->id)) {
                return true;
            }
        }

        return false;
    }

    public function deleteLayoutElement(int $elementId): void
    {
        $element = SlickFormLayoutElement::find($elementId);

        if ($element) {
            // Close properties panel if this element or any descendant is selected
            if ($this->isElementOrDescendantSelected($element)) {
                $this->closeElementEditor();
            }

            // Close field editor if any field within this element is selected
            if ($this->selectedField && $this->isFieldWithinElement($this->selectedField->id, $elementId)) {
                $this->closeFieldEditor();
            }
        }

        SlickFormLayoutElement::destroy($elementId);
        $this->loadFormStructure();
    }

    // =========================================================================
    // TABLE OPERATIONS (Refactored for nested element structure)
    // =========================================================================

    /**
     * Add a row to a table section (header, body, or footer)
     */
    public function addRowToSection(int $sectionId): void
    {
        $section = SlickFormLayoutElement::findOrFail($sectionId);

        // Get the table to determine column count
        $table = $section->parent;
        $columnCount = app(FormLayoutService::class)->getTableColumnCount($table) ?? 3;

        $maxOrder = SlickFormLayoutElement::where('parent_id', $sectionId)
            ->where('element_type', 'table_row')
            ->max('order') ?? -1;

        $cellType = $section->element_type === 'table_header' ? 'th' : 'td';
        app(FormLayoutService::class)->createTableRow($section, $maxOrder + 1, $columnCount, $cellType);

        $this->loadFormStructure();
    }

    /**
     * Delete a table row
     */
    public function deleteTableRow(int $rowId): void
    {
        $row = SlickFormLayoutElement::findOrFail($rowId);
        $row->delete(); // Cascade deletes cells and their contents
        $this->loadFormStructure();
    }

    /**
     * Move table row up
     */
    public function moveTableRowUp(int $rowId): void
    {
        $row = SlickFormLayoutElement::findOrFail($rowId);
        $previousRow = SlickFormLayoutElement::where('parent_id', $row->parent_id)
            ->where('element_type', 'table_row')
            ->where('order', '<', $row->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousRow) {
            $tempOrder = $row->order;
            $row->order = $previousRow->order;
            $previousRow->order = $tempOrder;
            $row->save();
            $previousRow->save();
            $this->loadFormStructure();
        }
    }

    /**
     * Move table row down
     */
    public function moveTableRowDown(int $rowId): void
    {
        $row = SlickFormLayoutElement::findOrFail($rowId);
        $nextRow = SlickFormLayoutElement::where('parent_id', $row->parent_id)
            ->where('element_type', 'table_row')
            ->where('order', '>', $row->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextRow) {
            $tempOrder = $row->order;
            $row->order = $nextRow->order;
            $nextRow->order = $tempOrder;
            $row->save();
            $nextRow->save();
            $this->loadFormStructure();
        }
    }

    /**
     * Add column to all rows in table
     */
    public function addColumnToTable(int $tableId): void
    {
        $table = SlickFormLayoutElement::findOrFail($tableId);
        app(FormLayoutService::class)->addColumnToTable($table);
        $this->loadFormStructure();
        $this->dispatch('table-updated');
    }

    /**
     * Delete column from all rows in table
     */
    public function deleteColumnFromTable(int $tableId, int $columnIndex): void
    {
        $table = SlickFormLayoutElement::findOrFail($tableId);
        app(FormLayoutService::class)->deleteColumnFromTable($table, $columnIndex);
        $this->loadFormStructure();
        $this->dispatch('table-updated');
    }

    /**
     * Update a single setting for an element (generic method for any element type)
     */
    public function updateElementSetting(int $elementId, string $key, mixed $value): void
    {
        $element = SlickFormLayoutElement::findOrFail($elementId);
        $settings = $element->settings ?? [];
        $settings[$key] = $value;
        $element->settings = $settings;
        $element->save();

        $this->loadFormStructure();
        $this->dispatch('element-setting-updated', elementId: $elementId, key: $key);
    }

    /**
     * Add a slide to a carousel (convenience wrapper around addLayoutElement)
     */
    public function addSlideToCarousel(int $carouselId): void
    {
        // Get existing slide count to generate proper title
        $carousel = SlickFormLayoutElement::find($carouselId);
        $slideCount = $carousel ? $carousel->children()->count() : 0;

        $this->addLayoutElement('carousel_slide', $carouselId, [
            'slide_title' => 'Slide '.($slideCount + 1),
            'slide_icon' => '',
            'background_color' => '',
            'text_alignment' => '',
        ]);
    }

    /**
     * Apply a carousel preset to an existing carousel element.
     * Replaces all current settings and slides with the preset configuration.
     */
    public function applyCarouselPreset(int $elementId, string $presetKey): void
    {
        if (empty($presetKey)) {
            return;
        }

        $presetService = app(\DigitalisStudios\SlickForms\Services\CarouselPresetService::class);
        $preset = $presetService->getPreset($presetKey);

        if (! $preset) {
            session()->flash('error', 'Preset not found');

            return;
        }

        $element = SlickFormLayoutElement::findOrFail($elementId);

        // Clear existing slides (children)
        $element->children()->delete();

        // Apply preset settings
        $element->settings = array_merge($element->settings ?? [], $preset['settings']);
        $element->save();

        // Create slides based on preset template
        $slideTemplate = $preset['slideTemplate'];
        $slideTemplates = $presetService->getSlideTemplates();

        if (! isset($slideTemplates[$slideTemplate])) {
            session()->flash('error', 'Slide template not found');

            return;
        }

        $fieldTypeRegistry = app(FieldTypeRegistry::class);
        $templateConfig = $slideTemplates[$slideTemplate];

        // Support both old array format (just fields) and new object format (with background image)
        $templateFields = $templateConfig['fields'] ?? $templateConfig;
        $hasBackgroundImage = isset($templateConfig['background_image']);

        for ($i = 0; $i < $preset['slideCount']; $i++) {
            // Build slide settings
            $slideSettings = [
                'slide_title' => 'Slide '.($i + 1),
                'slide_icon' => '',
                'background_color' => '',
                'text_alignment' => $templateConfig['text_alignment'] ?? '',
                'vertical_alignment' => $templateConfig['vertical_alignment'] ?? '',
                'padding' => $templateConfig['padding'] ?? '',
                'min_height' => $templateConfig['min_height'] ?? '',
            ];

            // Add background image if template has one
            if ($hasBackgroundImage) {
                $backgroundUrl = $templateConfig['background_image'];

                // For Picsum Photos, append slide number to seed for unique images per slide
                if (str_contains($backgroundUrl, 'picsum.photos/seed/')) {
                    $backgroundUrl = preg_replace(
                        '/(picsum\.photos\/seed\/)([^\/]+)(\/.+)/',
                        '$1$2-slide-'.$i.'$3',
                        $backgroundUrl
                    );
                }

                $slideSettings['background_image_mode'] = 'url';
                $slideSettings['background_image_url'] = $backgroundUrl;
            }

            // Create carousel slide element
            $slide = SlickFormLayoutElement::create([
                'slick_form_id' => $this->form->id,
                'parent_id' => $element->id,
                'element_type' => 'carousel_slide',
                'order' => $i,
                'settings' => $slideSettings,
            ]);

            // Add template fields to slide
            foreach ($templateFields as $index => $fieldTemplate) {
                if (! $fieldTypeRegistry->has($fieldTemplate['field_type'])) {
                    continue;
                }

                $fieldType = $fieldTypeRegistry->get($fieldTemplate['field_type']);

                $field = CustomFormField::create([
                    'slick_form_id' => $this->form->id,
                    'slick_form_layout_element_id' => $slide->id,
                    'field_type' => $fieldTemplate['field_type'],
                    'name' => 'slide_'.$i.'_'.Str::snake($fieldTemplate['label']).'_'.Str::random(6),
                    'label' => $fieldTemplate['label'],
                    'placeholder' => $fieldTemplate['placeholder'] ?? '',
                    'validation_rules' => $fieldTemplate['required'] ? ['required'] : [],
                    'order' => $index,
                    'show_label' => $fieldTemplate['show_label'] ?? true,
                ]);

                // Set placeholder URL for image fields
                if ($fieldTemplate['field_type'] === 'image' && isset($fieldTemplate['placeholder_url'])) {
                    $imageUrl = $fieldTemplate['placeholder_url'];

                    // For Picsum Photos, append slide number to seed for unique images per slide
                    if (str_contains($imageUrl, 'picsum.photos/seed/')) {
                        $imageUrl = preg_replace(
                            '/(picsum\.photos\/seed\/)([^\/]+)(\/.+)/',
                            '$1$2-slide-'.$i.'$3',
                            $imageUrl
                        );
                    }

                    $field->options = array_merge($field->options ?? [], [
                        'image_url' => $imageUrl,
                        'alt_text' => $fieldTemplate['label'],
                    ]);
                    $field->save();
                }

                // Set default content for paragraph fields
                if ($fieldTemplate['field_type'] === 'paragraph' && isset($fieldTemplate['default_content'])) {
                    $field->options = array_merge($field->options ?? [], [
                        'content' => $fieldTemplate['default_content'],
                    ]);
                    $field->save();
                }
            }
        }

        // Refresh form structure
        $this->loadFormStructure();

        // Clear preset selection
        $this->updateElementSetting($elementId, 'preset', '');

        // Dispatch event to reinitialize carousel preview
        $this->dispatch('element-saved', elementId: $elementId, elementType: 'carousel');

        session()->flash('message', "Applied preset: {$preset['label']} ({$preset['slideCount']} slides created)");
    }

    public function updateFieldOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            CustomFormField::where('id', $id)->update(['order' => $index]);
        }

        $this->loadFormStructure();
    }

    public function updateElementOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            SlickFormLayoutElement::where('id', $id)->update(['order' => $index]);
        }

        $this->loadFormStructure();
    }

    /**
     * Update the order of both fields and layout elements within a parent.
     * This method processes fields and elements together to maintain their interleaved order.
     *
     * @param  int|null  $parentElementId  The parent element ID (null for root level)
     * @param  array  $orderedItems  Array of items with 'type' and 'id' keys
     *                               Example: [['type' => 'field', 'id' => 1], ['type' => 'element', 'id' => 2]]
     */
    public function updateChildrenOrderInParent(?int $parentElementId, array $orderedItems): void
    {
        $layoutService = app(FormLayoutService::class);
        $parent = $parentElementId ? SlickFormLayoutElement::find($parentElementId) : null;

        foreach ($orderedItems as $index => $item) {
            if ($item['type'] === 'field') {
                // Fields can generally be added anywhere except specific restrictions
                CustomFormField::where('id', $item['id'])->update([
                    'slick_form_layout_element_id' => $parentElementId,
                    'order' => $index,
                ]);
            } elseif ($item['type'] === 'element') {
                $element = SlickFormLayoutElement::find($item['id']);
                if ($element) {
                    // Validate that this element can be a child of this parent
                    if (! $layoutService->canAddChild($parent, $element->element_type)) {
                        // Invalid drop - set error message and skip this update
                        $parentName = $parent ? $parent->element_type : 'root level';
                        $this->errorMessage = "Cannot place a '{$element->element_type}' element into a '{$parentName}' container. Please check the nesting rules.";

                        continue;
                    }

                    $element->update([
                        'parent_id' => $parentElementId,
                        'order' => $index,
                    ]);
                }
            }
        }

        $this->loadFormStructure();
    }

    public function closeFieldEditor(): void
    {
        $this->showFieldEditor = false;
        $this->selectedField = null;
        $this->properties = [];
        $this->activePropertiesTab = 'basic';

        // Reset special properties
        $this->fieldIsRequired = false;
        $this->fieldValidationOptions = [];
        $this->fieldConditionalLogic = [];
    }

    /**
     * Activate field picker mode for a specific condition
     */
    public function activatePicker(string $target): void
    {
        $this->pickerMode = true;
        $this->pickerTarget = $target;
    }

    /**
     * Cancel field picker mode
     */
    public function cancelPicker(): void
    {
        $this->pickerMode = false;
        $this->pickerTarget = null;
    }

    /**
     * Handle field selection from canvas picker
     */
    public function pickField(int $fieldId): void
    {
        if (! $this->pickerMode || ! $this->pickerTarget) {
            return;
        }

        // Parse the target to determine where to set the field
        // Format examples: "condition_0", "validation_0_condition_1"
        $parts = explode('_', $this->pickerTarget);

        if ($parts[0] === 'condition') {
            // Visibility condition
            $index = (int) $parts[1];
            if (isset($this->properties['conditional_logic']['conditions'][$index])) {
                $this->properties['conditional_logic']['conditions'][$index]['target_field_id'] = $fieldId;
            }
        } elseif ($parts[0] === 'validation') {
            // Conditional validation condition
            $valIndex = (int) $parts[1];
            $condIndex = (int) $parts[3]; // validation_0_condition_1

            if (isset($this->properties['conditional_logic']['conditional_validation'][$valIndex]['conditions'][$condIndex])) {
                $this->properties['conditional_logic']['conditional_validation'][$valIndex]['conditions'][$condIndex]['target_field_id'] = $fieldId;
            }
        }

        // Exit picker mode
        $this->cancelPicker();
    }

    /**
     * Get available operators for a given field type
     * Uses ConditionalLogicEvaluator service for centralized operator logic
     */
    public function getOperatorsForFieldType(string $fieldType): array
    {
        $evaluator = app(\DigitalisStudios\SlickForms\Services\ConditionalLogicEvaluator::class);
        $operators = $evaluator->getOperatorsForFieldType($fieldType);

        $labels = $evaluator->getOperatorLabels();

        // Return operators with labels
        $result = [];
        foreach ($operators as $operator) {
            $result[$operator] = $labels[$operator] ?? ucwords(str_replace('_', ' ', $operator));
        }

        return $result;
    }

    /**
     * Get operators for a specific target field ID
     */
    public function getOperatorsForTargetField(mixed $targetFieldId): array
    {
        // Cast to int (wire:model sometimes passes strings)
        $targetFieldId = $targetFieldId ? (int) $targetFieldId : null;

        if (! $targetFieldId) {
            // Return all operators if no field selected
            return [
                'equals' => 'Equals',
                'not_equals' => 'Not Equals',
                'contains' => 'Contains',
                'not_contains' => 'Not Contains',
                'greater_than' => 'Greater Than',
                'less_than' => 'Less Than',
                'greater_than_or_equal' => 'Greater Than or Equal',
                'less_than_or_equal' => 'Less Than or Equal',
                'is_empty' => 'Is Empty',
                'is_not_empty' => 'Is Not Empty',
                'in' => 'In (comma-separated)',
                'not_in' => 'Not In (comma-separated)',
            ];
        }

        $field = CustomFormField::find($targetFieldId);

        if (! $field) {
            return $this->getOperatorsForFieldType('text');
        }

        return $this->getOperatorsForFieldType($field->field_type);
    }

    /**
     * Get the available options for a target field
     */
    public function getTargetFieldOptions(mixed $targetFieldId): ?array
    {
        // Cast to int (wire:model sometimes passes strings)
        $targetFieldId = $targetFieldId ? (int) $targetFieldId : null;

        if (! $targetFieldId) {
            return null;
        }

        $field = CustomFormField::find($targetFieldId);

        if (! $field) {
            return null;
        }

        // Check if field has options
        if (! isset($field->options['values']) || ! is_array($field->options['values'])) {
            return null;
        }

        return $field->options['values'];
    }

    /**
     * Check if target field has predefined options
     */
    public function targetFieldHasOptions(mixed $targetFieldId): bool
    {
        return $this->getTargetFieldOptions($targetFieldId) !== null;
    }

    /**
     * Get all fields grouped by their parent container for use in dropdowns
     */
    public function getFieldsGroupedByContainer()
    {
        $allFields = CustomFormField::where('slick_form_id', $this->form->id)->get();
        $grouped = [];

        foreach ($allFields as $field) {
            $container = $this->findParentContainer($field);
            $containerLabel = $container ? $container->getContainerLabel() : 'Ungrouped Fields';

            if (! isset($grouped[$containerLabel])) {
                $grouped[$containerLabel] = [];
            }

            $grouped[$containerLabel][] = $field;
        }

        return $grouped;
    }

    /**
     * Find the topmost container for a field by traversing up the hierarchy
     */
    public function findParentContainer(CustomFormField $field): ?SlickFormLayoutElement
    {
        if (! $field->slick_form_layout_element_id) {
            return null;
        }

        $element = SlickFormLayoutElement::find($field->slick_form_layout_element_id);

        if (! $element) {
            return null;
        }

        // If this is already a container, return it
        if ($element->element_type === 'container') {
            return $element;
        }

        // Otherwise, traverse up to find the parent container
        while ($element && $element->parent_id) {
            $element = $element->parent;

            if ($element && $element->element_type === 'container') {
                return $element;
            }
        }

        return null;
    }

    private function generateUniqueElementId(string $elementType): string
    {
        $elementIdBase = $elementType.'_'.uniqid();
        $elementId = $elementIdBase;
        $elementIdCounter = 1;

        while (SlickFormLayoutElement::where('slick_form_id', $this->form->id)
            ->where('element_id', $elementId)
            ->exists()) {
            $elementId = $elementIdBase.'_'.$elementIdCounter;
            $elementIdCounter++;
        }

        return $elementId;
    }

    public function moveFieldToRepeater(int $fieldId, int $repeaterFieldId): void
    {
        $field = CustomFormField::find($fieldId);
        if (! $field) {
            return;
        }

        // Update the field to belong to the repeater
        $field->parent_field_id = $repeaterFieldId;
        $field->slick_form_layout_element_id = null;

        // Calculate new order
        $maxOrder = CustomFormField::where('parent_field_id', $repeaterFieldId)
            ->max('order') ?? 0;
        $field->order = $maxOrder + 1;

        $field->save();

        $this->loadFormStructure();
    }

    public function reorderRepeaterChildren(int $repeaterFieldId, array $fieldIds): void
    {
        foreach ($fieldIds as $index => $fieldId) {
            CustomFormField::where('id', $fieldId)
                ->where('parent_field_id', $repeaterFieldId)
                ->update(['order' => $index + 1]);
        }

        $this->loadFormStructure();
    }

    // ==================== Multi-Page Form Methods ====================

    public function loadPages(): void
    {
        $this->pages = $this->form->pages()->orderBy('order')->get()->toArray();

        // If multi-page mode is enabled and we have pages, set current page to first page
        if ($this->form->isMultiPage() && count($this->pages) > 0) {
            $this->currentPageId = $this->currentPageId ?? $this->pages[0]['id'];
        }
    }

    public function selectPage(int $pageId): void
    {
        $this->currentPageId = $pageId;
        $this->loadFormStructure();
        $this->closeFieldEditor();
        $this->closeElementEditor();
        $this->closePageEditor();
    }

    public function addPage(): void
    {
        $maxOrder = $this->form->pages()->max('order') ?? 0;

        $page = \DigitalisStudios\SlickForms\Models\SlickFormPage::create([
            'slick_form_id' => $this->form->id,
            'title' => 'New Page',
            'description' => '',
            'order' => $maxOrder + 1,
            'icon' => 'bi-file-text',
            'show_in_progress' => true,
        ]);

        $this->loadPages();
        $this->currentPageId = $page->id;
        $this->loadFormStructure();
        $this->editPage($page->id);
    }

    public function editPage(int $pageId): void
    {
        $page = \DigitalisStudios\SlickForms\Models\SlickFormPage::find($pageId);
        if (! $page) {
            return;
        }

        $this->selectedPageId = $pageId;
        $this->pageTitle = $page->title;
        $this->pageDescription = $page->description ?? '';
        $this->pageIcon = $page->icon ?? '';
        $this->pageShowInProgress = $page->show_in_progress;

        $this->showPageEditor = true;
        $this->anyPanelOpen = true;
        $this->closeFieldEditor();
        $this->closeElementEditor();
    }

    public function savePage(): void
    {
        $this->validate([
            'pageTitle' => 'required|string|max:255',
        ]);

        $page = \DigitalisStudios\SlickForms\Models\SlickFormPage::find($this->selectedPageId);
        if (! $page) {
            return;
        }

        $page->update([
            'title' => $this->pageTitle,
            'description' => $this->pageDescription,
            'icon' => $this->pageIcon,
            'show_in_progress' => $this->pageShowInProgress,
        ]);

        $this->loadPages();
        $this->closePageEditor();
    }

    public function closePageEditor(): void
    {
        $this->showPageEditor = false;
        $this->selectedPageId = null;
        $this->reset(['pageTitle', 'pageDescription', 'pageIcon', 'pageShowInProgress']);
    }

    public function deletePage(int $pageId): void
    {
        $page = \DigitalisStudios\SlickForms\Models\SlickFormPage::find($pageId);
        if (! $page) {
            return;
        }

        // Don't delete if it's the only page in multi-page mode
        if ($this->form->isMultiPage() && count($this->pages) <= 1) {
            $this->errorMessage = 'Cannot delete the only page. Disable multi-page mode first.';

            return;
        }

        // Move fields and elements to null (single-page) or first page
        $targetPageId = null;
        if (count($this->pages) > 1) {
            $targetPageId = $this->pages[0]['id'] == $pageId ? $this->pages[1]['id'] : $this->pages[0]['id'];
        }

        $page->fields()->update(['slick_form_page_id' => $targetPageId]);
        $page->layoutElements()->update(['slick_form_page_id' => $targetPageId]);

        $page->delete();

        // Update current page if we deleted it
        if ($this->currentPageId == $pageId) {
            $this->currentPageId = $targetPageId;
        }

        $this->loadPages();
        $this->loadFormStructure();
    }

    public function reorderPages(array $pageIds): void
    {
        foreach ($pageIds as $index => $pageId) {
            \DigitalisStudios\SlickForms\Models\SlickFormPage::where('id', $pageId)
                ->update(['order' => $index + 1]);
        }

        $this->loadPages();
    }

    public function toggleMultiPageMode(): void
    {
        $settings = $this->form->settings ?? [];
        $currentlyEnabled = $settings['multi_page_enabled'] ?? false;

        if ($currentlyEnabled) {
            // Disabling: move all fields/elements to null page_id
            $this->form->fields()->update(['slick_form_page_id' => null]);
            $this->form->layoutElements()->update(['slick_form_page_id' => null]);
            $settings['multi_page_enabled'] = false;
            $this->currentPageId = null;
        } else {
            // Enabling: create first page if none exist
            if ($this->form->pages()->count() == 0) {
                $page = \DigitalisStudios\SlickForms\Models\SlickFormPage::create([
                    'slick_form_id' => $this->form->id,
                    'title' => 'Page 1',
                    'description' => '',
                    'order' => 1,
                    'icon' => 'bi-file-text',
                    'show_in_progress' => true,
                ]);
            } else {
                // Use existing first page
                $page = $this->form->pages()->orderBy('order')->first();
            }

            $this->currentPageId = $page->id;

            // Move all existing fields/elements to first page (handles both new and existing pages)
            $this->form->fields()->whereNull('slick_form_page_id')->update(['slick_form_page_id' => $page->id]);
            $this->form->layoutElements()->whereNull('slick_form_page_id')->update(['slick_form_page_id' => $page->id]);

            $settings['multi_page_enabled'] = true;
        }

        $this->form->update(['settings' => $settings]);
        $this->form->refresh();
        $this->loadPages();
        $this->loadFormStructure();
    }

    // ==================== End Multi-Page Form Methods ====================

    public function updatedFormName(): void
    {
        // If empty, set to "Untitled"
        if (empty(trim($this->formName))) {
            $this->formName = 'Untitled';
        }

        $this->validate([
            'formName' => 'required|string|max:255',
        ]);

        $this->form->update(['name' => $this->formName]);
    }

    /**
     * Add a new option to a field's values array
     */
    public function addOption(string $path, string $label, string $value, bool $default = false): void
    {
        // Parse the path to get the property path (e.g., "properties.values")
        $keys = explode('.', $path);

        // Navigate to the array
        $data = &$this->{$keys[0]};
        for ($i = 1; $i < count($keys); $i++) {
            if (! isset($data[$keys[$i]])) {
                $data[$keys[$i]] = [];
            }
            $data = &$data[$keys[$i]];
        }

        // Add the new option
        if (! is_array($data)) {
            $data = [];
        }

        $data[] = [
            'label' => $label,
            'value' => $value,
            'default' => $default,
        ];
    }

    /**
     * Remove an option from a field's values array
     */
    public function removeOption(string $path, int $index): void
    {
        // Parse the path to get the property path
        $keys = explode('.', $path);

        // Navigate to the array
        $data = &$this->{$keys[0]};
        for ($i = 1; $i < count($keys); $i++) {
            if (! isset($data[$keys[$i]])) {
                return;
            }
            $data = &$data[$keys[$i]];
        }

        // Remove the option
        if (is_array($data) && isset($data[$index])) {
            array_splice($data, $index, 1);
        }
    }

    /**
     * Move an option up or down in a field's values array
     */
    public function moveOption(string $path, int $index, string $direction): void
    {
        // Parse the path to get the property path
        $keys = explode('.', $path);

        // Navigate to the array
        $data = &$this->{$keys[0]};
        for ($i = 1; $i < count($keys); $i++) {
            if (! isset($data[$keys[$i]])) {
                return;
            }
            $data = &$data[$keys[$i]];
        }

        if (! is_array($data) || ! isset($data[$index])) {
            return;
        }

        $targetIndex = $direction === 'up' ? $index - 1 : $index + 1;

        if ($targetIndex < 0 || $targetIndex >= count($data)) {
            return;
        }

        // Swap the elements
        $temp = $data[$index];
        $data[$index] = $data[$targetIndex];
        $data[$targetIndex] = $temp;
    }

    // ============================================
    // Email Notification Methods
    // ============================================

    /**
     * Load email settings from form
     */
    public function loadEmailSettings(): void
    {
        $settings = $this->form->settings['email'] ?? [];

        $this->emailEnabled = $settings['enabled'] ?? false;
        $this->adminEmailTemplates = $settings['admin_templates'] ?? [];
        $this->userConfirmationEnabled = $settings['user_confirmation']['enabled'] ?? false;
        $this->userEmailFieldId = $settings['user_confirmation']['email_field_id'] ?? null;
        $this->userConfirmationSubject = $settings['user_confirmation']['subject'] ?? 'Thank you for your submission';
        $this->userConfirmationTemplate = $settings['user_confirmation'] ?? [];
    }

    /**
     * Save email settings to form
     */
    public function saveEmailSettings(): void
    {
        $settings = $this->form->settings ?? [];
        $settings['email'] = [
            'enabled' => $this->emailEnabled,
            'admin_templates' => $this->adminEmailTemplates,
            'user_confirmation' => [
                'enabled' => $this->userConfirmationEnabled,
                'email_field_id' => $this->userEmailFieldId,
                'subject' => $this->userConfirmationSubject,
                'body_template' => $this->userConfirmationTemplate['body_template'] ?? 'user-confirmation',
                'attach_pdf' => $this->userConfirmationTemplate['attach_pdf'] ?? false,
            ],
        ];

        $this->form->update(['settings' => $settings]);

        session()->flash('message', 'Email settings saved successfully.');
    }

    /**
     * Add new admin email template
     */
    public function addAdminTemplate(): void
    {
        $newTemplate = [
            'enabled' => true,
            'recipients' => [],
            'subject' => 'New {{form.name}} submission',
            'body_template' => 'admin-notification',
            'attach_pdf' => false,
            'conditional_rules' => [],
            'priority' => count($this->adminEmailTemplates) + 1,
        ];

        $this->adminEmailTemplates[] = $newTemplate;
        $this->editingTemplateIndex = count($this->adminEmailTemplates) - 1;

        // Load template into editing properties
        $this->loadTemplateForEditing($this->editingTemplateIndex);

        $this->showEmailTemplateModal = true;
    }

    /**
     * Edit admin email template
     */
    public function editAdminTemplate(int $index): void
    {
        if (! isset($this->adminEmailTemplates[$index])) {
            return;
        }

        $this->editingTemplateIndex = $index;

        // Load template into editing properties
        $this->loadTemplateForEditing($index);

        $this->showEmailTemplateModal = true;
    }

    /**
     * Load template data into editing properties
     */
    protected function loadTemplateForEditing(int $index): void
    {
        if (! isset($this->adminEmailTemplates[$index])) {
            return;
        }

        $template = $this->adminEmailTemplates[$index];

        $this->templateEnabled = $template['enabled'] ?? true;
        $this->templateRecipients = is_array($template['recipients'] ?? null)
            ? implode(', ', $template['recipients'])
            : ($template['recipients'] ?? '');
        $this->templateSubject = $template['subject'] ?? 'New {{form.name}} submission';
        $this->templatePriority = $template['priority'] ?? 1;
        $this->templateAttachPdf = $template['attach_pdf'] ?? false;
        $this->templateConditionalRules = $template['conditional_rules'] ?? [];
    }

    /**
     * Save template from editing properties back to array
     */
    public function saveTemplate(): void
    {
        if ($this->editingTemplateIndex === null) {
            return;
        }

        // Parse recipients (comma-separated)
        $recipients = array_filter(
            array_map('trim', explode(',', $this->templateRecipients)),
            fn ($email) => ! empty($email)
        );

        $this->adminEmailTemplates[$this->editingTemplateIndex] = [
            'enabled' => $this->templateEnabled,
            'recipients' => $recipients,
            'subject' => $this->templateSubject,
            'body_template' => 'admin-notification',
            'attach_pdf' => $this->templateAttachPdf,
            'conditional_rules' => $this->templateConditionalRules,
            'priority' => $this->templatePriority,
        ];

        $this->saveEmailSettings();

        session()->flash('message', 'Email template saved successfully.');

        $this->closeEmailTemplateModal();
    }

    /**
     * Delete admin email template
     */
    public function deleteAdminTemplate(int $index): void
    {
        if (! isset($this->adminEmailTemplates[$index])) {
            return;
        }

        array_splice($this->adminEmailTemplates, $index, 1);
        $this->saveEmailSettings();

        session()->flash('message', 'Email template deleted successfully.');
    }

    /**
     * Update admin email template
     */
    public function updateAdminTemplate(int $index, array $data): void
    {
        if (! isset($this->adminEmailTemplates[$index])) {
            return;
        }

        $this->adminEmailTemplates[$index] = array_merge(
            $this->adminEmailTemplates[$index],
            $data
        );

        $this->saveEmailSettings();
    }

    /**
     * Edit user confirmation template
     */
    public function editUserConfirmationTemplate(): void
    {
        // For now, we'll handle this via inline editing
        // In the future, this could open a modal similar to admin templates
    }

    /**
     * Show email logs modal
     */
    public function showEmailLogs(): void
    {
        $this->showEmailLogsModal = true;
    }

    /**
     * Close email logs modal
     */
    public function closeEmailLogsModal(): void
    {
        $this->showEmailLogsModal = false;
    }

    /**
     * Close email template editor modal
     */
    public function closeEmailTemplateModal(): void
    {
        $this->showEmailTemplateModal = false;
        $this->editingTemplateIndex = null;
    }

    /**
     * Get all email fields from the form
     */
    public function getEmailFields()
    {
        return $this->form->fields()
            ->where('field_type', 'email')
            ->orderBy('order')
            ->get();
    }

    // ==========================================
    // Spam Protection Methods
    // ==========================================

    /**
     * Load spam protection settings from form
     */
    public function loadSpamSettings(): void
    {
        $settings = $this->form->settings['spam'] ?? [];

        $this->spamProtectionEnabled = $settings['enabled'] ?? false;

        // Honeypot settings
        $this->honeypotEnabled = $settings['honeypot']['enabled'] ?? false;
        $this->honeypotFieldName = $settings['honeypot']['field_name'] ?? 'website';
        $this->honeypotTimeThreshold = $settings['honeypot']['time_threshold'] ?? 3;

        // CAPTCHA settings
        $this->captchaType = $settings['captcha']['type'] ?? 'none';
        $this->recaptchaSiteKey = $settings['captcha']['recaptcha_site_key'] ?? '';
        $this->recaptchaSecretKey = $settings['captcha']['recaptcha_secret_key'] ?? '';
        $this->recaptchaScoreThreshold = $settings['captcha']['recaptcha_score_threshold'] ?? 0.5;
        $this->hcaptchaSiteKey = $settings['captcha']['hcaptcha_site_key'] ?? '';
        $this->hcaptchaSecretKey = $settings['captcha']['hcaptcha_secret_key'] ?? '';

        // Rate limiting settings
        $this->rateLimitEnabled = $settings['rate_limit']['enabled'] ?? false;
        $this->rateLimitMaxAttempts = $settings['rate_limit']['max_attempts'] ?? 5;
        $this->rateLimitDecayMinutes = $settings['rate_limit']['decay_minutes'] ?? 60;
    }

    /**
     * Save spam protection settings to form
     */
    public function saveSpamSettings(): void
    {
        $settings = $this->form->settings ?? [];
        $settings['spam'] = [
            'enabled' => $this->spamProtectionEnabled,
            'honeypot' => [
                'enabled' => $this->honeypotEnabled,
                'field_name' => $this->honeypotFieldName,
                'time_threshold' => $this->honeypotTimeThreshold,
            ],
            'captcha' => [
                'type' => $this->captchaType,
                'recaptcha_site_key' => $this->recaptchaSiteKey,
                'recaptcha_secret_key' => $this->recaptchaSecretKey,
                'recaptcha_score_threshold' => $this->recaptchaScoreThreshold,
                'hcaptcha_site_key' => $this->hcaptchaSiteKey,
                'hcaptcha_secret_key' => $this->hcaptchaSecretKey,
            ],
            'rate_limit' => [
                'enabled' => $this->rateLimitEnabled,
                'max_attempts' => $this->rateLimitMaxAttempts,
                'decay_minutes' => $this->rateLimitDecayMinutes,
            ],
        ];

        $this->form->update(['settings' => $settings]);

        session()->flash('message', 'Spam protection settings saved successfully.');
    }

    /**
     * Show spam logs modal
     */
    public function showSpamLogs(): void
    {
        $this->showSpamLogsModal = true;
    }

    /**
     * Close spam logs modal
     */
    public function closeSpamLogsModal(): void
    {
        $this->showSpamLogsModal = false;
    }

    /**
     * Auto-save spam settings when spamProtectionEnabled changes
     */
    public function updatedSpamProtectionEnabled(): void
    {
        $this->saveSpamSettings();
    }

    /**
     * Auto-save spam settings when honeypotEnabled changes
     */
    public function updatedHoneypotEnabled(): void
    {
        $this->saveSpamSettings();
    }

    /**
     * Auto-save spam settings when captchaType changes
     */
    public function updatedCaptchaType(): void
    {
        $this->saveSpamSettings();
    }

    /**
     * Auto-save spam settings when rateLimitEnabled changes
     */
    public function updatedRateLimitEnabled(): void
    {
        $this->saveSpamSettings();
    }

    // ============================================================================
    // Model Binding Methods
    // ============================================================================

    /**
     * Load model binding settings from database
     */
    public function loadModelBinding(): void
    {
        $binding = $this->form->modelBinding;

        if ($binding) {
            $this->modelBindingEnabled = true;
            $this->modelClass = $binding->model_class ?? '';
            $this->routeParameter = $binding->route_parameter ?? 'model';
            $this->routeKey = $binding->route_key ?? 'id';
            $this->allowCreate = $binding->allow_create ?? true;
            $this->allowUpdate = $binding->allow_update ?? true;

            // Load field mappings
            $fieldMappings = $binding->field_mappings ?? [];
            $this->fieldMappings = [];
            foreach ($fieldMappings as $formField => $modelAttribute) {
                $this->fieldMappings[] = [
                    'form_field' => $formField,
                    'model_attribute' => $modelAttribute,
                ];
            }

            // Load relationship mappings
            $relationshipMappings = $binding->relationship_mappings ?? [];
            $this->relationshipMappings = [];
            foreach ($relationshipMappings as $formField => $relationshipName) {
                $this->relationshipMappings[] = [
                    'form_field' => $formField,
                    'relationship_name' => $relationshipName,
                ];
            }
        } else {
            $this->modelBindingEnabled = false;
        }
    }

    /**
     * Save model binding configuration to database
     */
    public function saveModelBinding(): void
    {
        // Validate model class if binding is enabled
        if ($this->modelBindingEnabled && empty($this->modelClass)) {
            session()->flash('error', 'Model class is required when model binding is enabled.');

            return;
        }

        // Convert field mappings array to associative array
        $fieldMappingsData = [];
        foreach ($this->fieldMappings as $mapping) {
            if (! empty($mapping['form_field']) && ! empty($mapping['model_attribute'])) {
                $fieldMappingsData[$mapping['form_field']] = $mapping['model_attribute'];
            }
        }

        // Convert relationship mappings array to associative array
        $relationshipMappingsData = [];
        foreach ($this->relationshipMappings as $mapping) {
            if (! empty($mapping['form_field']) && ! empty($mapping['relationship_name'])) {
                $relationshipMappingsData[$mapping['form_field']] = $mapping['relationship_name'];
            }
        }

        if ($this->modelBindingEnabled) {
            // Create or update model binding
            $this->form->modelBinding()->updateOrCreate(
                ['form_id' => $this->form->id],
                [
                    'model_class' => $this->modelClass,
                    'route_parameter' => $this->routeParameter,
                    'route_key' => $this->routeKey,
                    'field_mappings' => $fieldMappingsData,
                    'relationship_mappings' => $relationshipMappingsData,
                    'allow_create' => $this->allowCreate,
                    'allow_update' => $this->allowUpdate,
                ]
            );

            session()->flash('success', 'Model binding configuration saved successfully.');
        } else {
            // Delete model binding if disabled
            $this->form->modelBinding()->delete();

            session()->flash('success', 'Model binding disabled.');
        }

        // Reload binding
        $this->form->refresh();
        $this->loadModelBinding();
    }

    /**
     * Add a new field mapping row
     */
    public function addFieldMapping(): void
    {
        $this->fieldMappings[] = [
            'form_field' => '',
            'model_attribute' => '',
        ];
    }

    /**
     * Remove a field mapping row
     */
    public function removeFieldMapping(int $index): void
    {
        unset($this->fieldMappings[$index]);
        $this->fieldMappings = array_values($this->fieldMappings);
    }

    /**
     * Add a new relationship mapping row
     */
    public function addRelationshipMapping(): void
    {
        $this->relationshipMappings[] = [
            'form_field' => '',
            'relationship_name' => '',
        ];
    }

    /**
     * Remove a relationship mapping row
     */
    public function removeRelationshipMapping(int $index): void
    {
        unset($this->relationshipMappings[$index]);
        $this->relationshipMappings = array_values($this->relationshipMappings);
    }

    /**
     * Get all form fields for mapping dropdowns
     */
    public function getFormFields()
    {
        return $this->form->fields()->orderBy('name')->get();
    }

    /**
     * Discover available Eloquent models in the host application
     * and return a sorted list of fully qualified class names.
     */
    public function getAvailableModels(): array
    {
        $models = [];

        try {
            $paths = [];
            $appModels = app_path('Models');
            $appRoot = app_path();

            if (is_dir($appModels)) {
                $paths[] = [$appModels, 'App\\Models'];
            }

            // Also scan app/ for models that may not be under app/Models
            if (is_dir($appRoot)) {
                $paths[] = [$appRoot, 'App'];
            }

            foreach ($paths as [$basePath, $baseNamespace]) {
                foreach (File::allFiles($basePath) as $file) {
                    if ($file->getExtension() !== 'php') {
                        continue;
                    }

                    $relative = trim(str_replace([$basePath, '.php'], '', $file->getPathname()), DIRECTORY_SEPARATOR);
                    if ($relative === '' || str_contains($relative, DIRECTORY_SEPARATOR.'Console'.DIRECTORY_SEPARATOR)) {
                        continue;
                    }

                    $class = $baseNamespace.'\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $relative);

                    if (class_exists($class) && is_subclass_of($class, \Illuminate\Database\Eloquent\Model::class)) {
                        $models[] = $class;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fail silently and return what we have
        }

        $models = array_values(array_unique($models));
        sort($models);

        return $models;
    }

    /**
     * Livewire hook: when modelClass changes, auto-suggest mappings if none exist.
     */
    public function updatedModelClass($value): void
    {
        if (! $this->modelBindingEnabled) {
            return;
        }

        if (! $value || ! class_exists($value)) {
            return;
        }

        // Only suggest when no mappings configured yet
        if (! empty($this->fieldMappings)) {
            return;
        }

        $suggestions = $this->suggestFieldMappings($value);
        if (! empty($suggestions)) {
            $this->fieldMappings = $suggestions;
        }
    }

    /**
     * Called explicitly from the Tom Select change handler to ensure
     * suggestions are generated immediately on selection.
     */
    public function onModelSelected(?string $class): void
    {
        $this->modelClass = $class ?? '';

        if (! $this->modelBindingEnabled || empty($this->modelClass)) {
            return;
        }

        if (empty($this->fieldMappings)) {
            $suggestions = $this->suggestFieldMappings($this->modelClass);
            if (! empty($suggestions)) {
                $this->fieldMappings = $suggestions;
            }
        }
    }

    /**
     * Suggest field mappings based on form fields and model attributes.
     */
    protected function suggestFieldMappings(string $modelClass): array
    {
        try {
            /** @var \Illuminate\Database\Eloquent\Model $model */
            $model = new $modelClass;
        } catch (\Throwable $e) {
            return [];
        }

        // Collect available attributes from fillable or DB schema
        $attributes = method_exists($model, 'getFillable') ? $model->getFillable() : [];
        if (empty($attributes) && method_exists($model, 'getTable')) {
            try {
                $attributes = Schema::getColumnListing($model->getTable());
            } catch (\Throwable $e) {
                // ignore schema errors
            }
        }

        $attributes = array_values(array_unique($attributes));
        $attrLookup = collect($attributes)->mapWithKeys(function ($attr) {
            return [strtolower($attr) => $attr];
        })->all();

        $mappings = [];

        foreach ($this->getFormFields() as $field) {
            $attr = $this->guessAttributeForField($field, $attrLookup);
            if ($attr) {
                $mappings[] = [
                    'form_field' => $field->name,
                    'model_attribute' => $attr,
                ];
            }
        }

        return $mappings;
    }

    /**
     * Guess the best model attribute for a given form field.
     */
    protected function guessAttributeForField($field, array $attrLookup): ?string
    {
        $fieldName = strtolower($field->name ?? '');
        $label = strtolower(($field->label ?? ''));
        $type = strtolower($field->field_type ?? '');

        // Helper to test candidates in order
        $pick = function (array $candidates) use ($attrLookup) {
            foreach ($candidates as $c) {
                if (! $c) {
                    continue;
                }
                $key = strtolower($c);
                if (isset($attrLookup[$key])) {
                    return $attrLookup[$key];
                }
            }

            return null;
        };

        // 1) Exact name match
        if ($attr = $pick([$fieldName])) {
            return $attr;
        }

        // 2) Common type-based mappings
        if (str_contains($type, 'email') || $fieldName === 'email' || str_contains($label, 'email')) {
            if ($attr = $pick(['email'])) {
                return $attr;
            }
        }
        if (str_contains($type, 'phone') || str_contains($fieldName, 'phone') || str_contains($label, 'phone')) {
            if ($attr = $pick(['phone', 'phone_number', 'mobile', 'mobile_phone'])) {
                return $attr;
            }
        }
        if ($fieldName === 'name' || str_contains($label, 'name')) {
            if ($attr = $pick(['name', 'full_name', 'username'])) {
                return $attr;
            }
            // Fall back to first_name if present
            if ($attr = $pick(['first_name'])) {
                return $attr;
            }
        }
        if (str_contains($fieldName, 'first') && str_contains($label, 'first')) {
            if ($attr = $pick(['first_name', 'firstname'])) {
                return $attr;
            }
        }
        if (str_contains($fieldName, 'last') && str_contains($label, 'last')) {
            if ($attr = $pick(['last_name', 'lastname', 'surname'])) {
                return $attr;
            }
        }
        if (str_contains($type, 'address') || str_contains($fieldName, 'address')) {
            if ($attr = $pick(['address', 'address1', 'street'])) {
                return $attr;
            }
        }
        if (str_contains($fieldName, 'city')) {
            if ($attr = $pick(['city', 'town'])) {
                return $attr;
            }
        }
        if (str_contains($fieldName, 'state')) {
            if ($attr = $pick(['state', 'province', 'region'])) {
                return $attr;
            }
        }
        if (str_contains($fieldName, 'zip') || str_contains($fieldName, 'postal')) {
            if ($attr = $pick(['zip', 'postal_code', 'postcode'])) {
                return $attr;
            }
        }

        // 3) Slug/normalized label match
        $candidates = [];
        if ($label) {
            $candidates[] = Str::snake($label);
        }
        $candidates[] = Str::snake($fieldName);
        if ($attr = $pick($candidates)) {
            return $attr;
        }

        return null;
    }

    // ============================================================================
    // Success Screen Methods
    // ============================================================================

    /**
     * Load success screen settings from form settings JSON
     */
    public function loadSuccessSettings(): void
    {
        $settings = $this->form->settings['success_screen'] ?? [];

        $this->successActionType = $settings['type'] ?? 'message';
        $this->messageTitle = $settings['message_title'] ?? 'Thank you!';
        $this->messageBody = $settings['message_body'] ?? '<p>Your submission has been received. We\'ll be in touch soon.</p>';
        $this->showSubmissionData = $settings['message_show_data'] ?? false;
        $this->hiddenFields = $settings['message_hidden_fields'] ?? [];
        $this->redirectUrl = $settings['redirect_url'] ?? '';
        $this->redirectDelay = $settings['redirect_delay_seconds'] ?? 3;
        $this->passSubmissionId = $settings['redirect_pass_submission_id'] ?? false;
        $this->conditionalRedirects = $settings['conditional_redirects'] ?? [];
        $this->enablePdfDownload = $settings['download_pdf_enabled'] ?? false;
        $this->pdfButtonText = $settings['download_pdf_button_text'] ?? 'Download PDF';
        $this->enableCsvDownload = $settings['download_csv_enabled'] ?? false;
        $this->csvButtonText = $settings['download_csv_button_text'] ?? 'Download CSV';
        $this->enableEditLink = $settings['edit_link_enabled'] ?? false;
        $this->editLinkText = $settings['edit_link_text'] ?? 'Edit Your Submission';
        $this->editLinkExpiration = $settings['edit_link_expiration_hours'] ?? 24;
    }

    /**
     * Save success screen settings to form settings JSON
     */
    public function saveSuccessSettings(): void
    {
        $currentSettings = $this->form->settings;

        $currentSettings['success_screen'] = [
            'type' => $this->successActionType,
            'message_enabled' => in_array($this->successActionType, ['message', 'message_then_redirect']),
            'message_title' => $this->messageTitle,
            'message_body' => $this->messageBody,
            'message_show_data' => $this->showSubmissionData,
            'message_hidden_fields' => $this->hiddenFields,
            'redirect_enabled' => in_array($this->successActionType, ['redirect', 'message_then_redirect']),
            'redirect_url' => $this->redirectUrl,
            'redirect_delay_seconds' => $this->redirectDelay,
            'redirect_pass_submission_id' => $this->passSubmissionId,
            'conditional_redirects' => $this->conditionalRedirects,
            'download_pdf_enabled' => $this->enablePdfDownload,
            'download_pdf_button_text' => $this->pdfButtonText,
            'download_csv_enabled' => $this->enableCsvDownload,
            'download_csv_button_text' => $this->csvButtonText,
            'edit_link_enabled' => $this->enableEditLink,
            'edit_link_text' => $this->editLinkText,
            'edit_link_expiration_hours' => $this->editLinkExpiration,
        ];

        $this->form->update(['settings' => $currentSettings]);

        session()->flash('success', 'Success screen settings saved successfully.');
    }

    /**
     * Add a new conditional redirect rule
     */
    public function addConditionalRedirect(): void
    {
        $this->conditionalRedirects[] = [
            'url' => '',
            'priority' => 1,
            'conditions' => [],
        ];
    }

    /**
     * Remove a conditional redirect rule
     */
    public function removeConditionalRedirect(int $index): void
    {
        unset($this->conditionalRedirects[$index]);
        $this->conditionalRedirects = array_values($this->conditionalRedirects);
    }

    /**
     * Add a new conditional validation rule
     */
    public function addConditionalValidation(): void
    {
        if (! isset($this->fieldConditionalLogic['conditional_validation'])) {
            $this->fieldConditionalLogic['conditional_validation'] = [];
        }

        $this->fieldConditionalLogic['conditional_validation'][] = [
            'rule' => 'required',
            'match' => 'all',
            'conditions' => [
                [
                    'target_field_id' => '',
                    'operator' => 'equals',
                    'value' => '',
                ],
            ],
        ];
    }

    /**
     * Remove a conditional validation rule
     */
    public function removeConditionalValidation(int $index): void
    {
        if (isset($this->fieldConditionalLogic['conditional_validation'][$index])) {
            unset($this->fieldConditionalLogic['conditional_validation'][$index]);
            // Re-index the array
            $this->fieldConditionalLogic['conditional_validation'] = array_values($this->fieldConditionalLogic['conditional_validation']);
        }
    }

    /**
     * Add a condition to a conditional validation rule
     */
    public function addConditionalValidationCondition(int $validationIndex): void
    {
        if (isset($this->fieldConditionalLogic['conditional_validation'][$validationIndex])) {
            $this->fieldConditionalLogic['conditional_validation'][$validationIndex]['conditions'][] = [
                'target_field_id' => '',
                'operator' => 'equals',
                'value' => '',
            ];
        }
    }

    /**
     * Remove a condition from a conditional validation rule
     */
    public function removeConditionalValidationCondition(int $validationIndex, int $conditionIndex): void
    {
        if (isset($this->fieldConditionalLogic['conditional_validation'][$validationIndex]['conditions'][$conditionIndex])) {
            unset($this->fieldConditionalLogic['conditional_validation'][$validationIndex]['conditions'][$conditionIndex]);
            // Re-index the array
            $this->fieldConditionalLogic['conditional_validation'][$validationIndex]['conditions'] =
                array_values($this->fieldConditionalLogic['conditional_validation'][$validationIndex]['conditions']);
        }
    }

    /**
     * Add a simple visibility condition
     */
    public function addCondition(): void
    {
        if (! isset($this->fieldConditionalLogic['conditions'])) {
            $this->fieldConditionalLogic['conditions'] = [];
        }

        $this->fieldConditionalLogic['conditions'][] = [
            'target_field_id' => '',
            'operator' => 'equals',
            'value' => '',
        ];
    }

    /**
     * Remove a simple visibility condition
     */
    public function removeCondition(int $index): void
    {
        if (isset($this->fieldConditionalLogic['conditions'][$index])) {
            unset($this->fieldConditionalLogic['conditions'][$index]);
            // Re-index the array
            $this->fieldConditionalLogic['conditions'] = array_values($this->fieldConditionalLogic['conditions']);
        }
    }

    /**
     * Switch from simple conditions to advanced rule groups mode
     */
    public function switchToAdvancedMode(): void
    {
        // Convert simple conditions to rule groups
        if (isset($this->fieldConditionalLogic['conditions']) && count($this->fieldConditionalLogic['conditions']) > 0) {
            $this->fieldConditionalLogic['rule_groups'] = [
                [
                    'match' => $this->fieldConditionalLogic['match'] ?? 'all',
                    'conditions' => $this->fieldConditionalLogic['conditions'],
                ],
            ];
            $this->fieldConditionalLogic['groups_match'] = 'all';
        }
    }

    /**
     * Switch from advanced rule groups mode to simple conditions
     */
    public function switchToSimpleMode(): void
    {
        // Convert first rule group back to simple conditions
        if (isset($this->fieldConditionalLogic['rule_groups'][0])) {
            $this->fieldConditionalLogic['conditions'] = $this->fieldConditionalLogic['rule_groups'][0]['conditions'];
            $this->fieldConditionalLogic['match'] = $this->fieldConditionalLogic['rule_groups'][0]['match'];
            unset($this->fieldConditionalLogic['rule_groups']);
            unset($this->fieldConditionalLogic['groups_match']);
        }
    }

    /**
     * Add a new rule group for advanced conditional logic
     */
    public function addRuleGroup(): void
    {
        if (! isset($this->fieldConditionalLogic['rule_groups'])) {
            $this->fieldConditionalLogic['rule_groups'] = [];
        }

        $this->fieldConditionalLogic['rule_groups'][] = [
            'match' => 'all',
            'conditions' => [
                [
                    'target_field_id' => '',
                    'operator' => 'equals',
                    'value' => '',
                ],
            ],
        ];
    }

    /**
     * Remove a rule group
     */
    public function removeRuleGroup(int $groupIndex): void
    {
        if (isset($this->fieldConditionalLogic['rule_groups'][$groupIndex])) {
            unset($this->fieldConditionalLogic['rule_groups'][$groupIndex]);
            $this->fieldConditionalLogic['rule_groups'] = array_values($this->fieldConditionalLogic['rule_groups']);
        }
    }

    /**
     * Add a condition to a specific rule group
     */
    public function addConditionToGroup(int $groupIndex): void
    {
        if (isset($this->fieldConditionalLogic['rule_groups'][$groupIndex])) {
            $this->fieldConditionalLogic['rule_groups'][$groupIndex]['conditions'][] = [
                'target_field_id' => '',
                'operator' => 'equals',
                'value' => '',
            ];
        }
    }

    /**
     * Remove a condition from a specific rule group
     */
    public function removeConditionFromGroup(int $groupIndex, int $conditionIndex): void
    {
        if (isset($this->fieldConditionalLogic['rule_groups'][$groupIndex]['conditions'][$conditionIndex])) {
            unset($this->fieldConditionalLogic['rule_groups'][$groupIndex]['conditions'][$conditionIndex]);
            $this->fieldConditionalLogic['rule_groups'][$groupIndex]['conditions'] =
                array_values($this->fieldConditionalLogic['rule_groups'][$groupIndex]['conditions']);
        }
    }

    /**
     * Show form-level settings editor
     */
    public function showFormSettings(): void
    {
        // Close all other editors
        $this->showFieldEditor = false;
        $this->showElementEditor = false;
        $this->showPageEditor = false;
        $this->selectedField = null;
        $this->selectedElement = null;
        $this->selectedPageId = null;

        // Open form settings editor
        $this->showFormEditor = true;
        $this->anyPanelOpen = true;
        $this->activePropertiesTab = 'basic';
    }

    /**
     * Close form-level settings editor
     */
    public function closeFormSettings(): void
    {
        $this->showFormEditor = false;
        $this->activePropertiesTab = 'basic';
    }

    /**
     * Save form-level settings
     */
    public function saveFormSettings(): void
    {
        // Validate form data
        $this->validate([
            'formName' => 'required|string|max:255',
            'formDescription' => 'nullable|string',
            'formExpiresAt' => 'nullable|date',
            'formTimeLimit' => 'nullable|integer|min:0',
        ]);

        // Update form basic properties
        $this->form->update([
            'name' => $this->formName,
            'description' => $this->formDescription,
            'is_active' => $this->formIsActive,
            'is_public' => $this->formIsPublic,
            'expires_at' => $this->formExpiresAt ? \Carbon\Carbon::parse($this->formExpiresAt) : null,
            'time_limited' => $this->formTimeLimit > 0 ? $this->formTimeLimit * 60 : 0,
        ]);

        // Save email settings
        $this->saveEmailSettings();

        session()->flash('message', 'Form settings saved successfully.');
    }

    /**
     * Get form URL for preview/sharing (always uses hashid strategy)
     */
    public function getFormUrl(): string
    {
        $urlService = app(\DigitalisStudios\SlickForms\Services\UrlObfuscationService::class);

        // Always use hashid strategy for preview URLs (short, non-sequential, shareable)
        return route('slick-forms.form.show.hash', ['hash' => $urlService->encodeId($this->form->id)]);
    }

    /**
     * Get QR code SVG for form URL
     */
    public function getQrCode(): string
    {
        $urlService = app(\DigitalisStudios\SlickForms\Services\UrlObfuscationService::class);
        $formUrl = $this->getFormUrl();

        return $urlService->generateQrCode($formUrl);
    }

    /**
     * Open share panel modal
     */
    #[\Livewire\Attributes\On('openSharePanel')]
    public function openSharePanel(): void
    {
        $this->showSharePanel = true;
        $this->prefillData = [];
        $this->generatedPrefillUrl = null;
    }

    /**
     * Close share panel modal
     */
    public function closeSharePanel(): void
    {
        $this->showSharePanel = false;
        $this->prefillData = [];
        $this->generatedPrefillUrl = null;
    }

    /**
     * Generate pre-fill URL with encrypted data
     */
    public function generatePrefillUrl(): void
    {
        // Remove empty values
        $cleanedData = array_filter($this->prefillData, function ($value) {
            return $value !== '' && $value !== null;
        });

        if (empty($cleanedData)) {
            session()->flash('error', 'Please fill in at least one field to generate a pre-fill URL.');

            return;
        }

        $urlService = app(\DigitalisStudios\SlickForms\Services\UrlObfuscationService::class);

        $this->generatedPrefillUrl = $urlService->generatePrefillUrl(
            $this->form,
            $cleanedData,
            $this->form->settings['url_security']['prefill_expiration_hours'] ?? 24
        );
    }

    // ============================================================================
    // Webhook Methods
    // ============================================================================

    /**
     * Load webhooks from database
     */
    public function loadWebhookSettings(): void
    {
        $this->webhooks = $this->form->webhooks()
            ->orderBy('created_at')
            ->get()
            ->toArray();
    }

    /**
     * Open webhook editor to add new webhook
     */
    public function addWebhook(): void
    {
        $this->resetWebhookForm();
        $this->showWebhookEditor = true;
    }

    /**
     * Open webhook editor to edit existing webhook
     */
    public function editWebhook(int $webhookId): void
    {
        $webhook = $this->form->webhooks()->findOrFail($webhookId);

        $this->editingWebhookId = $webhook->id;
        $this->webhookName = $webhook->name;
        $this->webhookUrl = $webhook->url;
        $this->webhookMethod = $webhook->method;
        $this->webhookHeaders = $webhook->headers ?? [];
        $this->webhookFormat = $webhook->format;
        $this->webhookTriggerConditions = $webhook->trigger_conditions ?? [];
        $this->webhookEnabled = $webhook->enabled;
        $this->webhookMaxRetries = $webhook->max_retries;
        $this->webhookRetryDelay = $webhook->retry_delay_seconds;

        $this->showWebhookEditor = true;
    }

    /**
     * Save webhook (create or update)
     */
    public function saveWebhook(): void
    {
        $this->validate([
            'webhookName' => 'required|string|max:255',
            'webhookUrl' => 'required|url|max:500',
            'webhookMethod' => 'required|in:GET,POST,PUT,PATCH,DELETE',
            'webhookFormat' => 'required|in:json,form_data,xml',
            'webhookMaxRetries' => 'required|integer|min:0|max:10',
            'webhookRetryDelay' => 'required|integer|min:1|max:3600',
        ]);

        $data = [
            'name' => $this->webhookName,
            'url' => $this->webhookUrl,
            'method' => $this->webhookMethod,
            'headers' => $this->webhookHeaders,
            'format' => $this->webhookFormat,
            'trigger_conditions' => $this->webhookTriggerConditions,
            'enabled' => $this->webhookEnabled,
            'max_retries' => $this->webhookMaxRetries,
            'retry_delay_seconds' => $this->webhookRetryDelay,
        ];

        if ($this->editingWebhookId) {
            // Update existing webhook
            $webhook = $this->form->webhooks()->findOrFail($this->editingWebhookId);
            $webhook->update($data);
            session()->flash('success', 'Webhook updated successfully.');
        } else {
            // Create new webhook
            $this->form->webhooks()->create($data);
            session()->flash('success', 'Webhook created successfully.');
        }

        $this->loadWebhookSettings();
        $this->closeWebhookEditor();
    }

    /**
     * Delete webhook
     */
    public function deleteWebhook(int $webhookId): void
    {
        $webhook = $this->form->webhooks()->findOrFail($webhookId);
        $webhook->delete();

        $this->loadWebhookSettings();
        session()->flash('success', 'Webhook deleted successfully.');
    }

    /**
     * Test webhook with sample data
     */
    public function testWebhook(int $webhookId): void
    {
        $webhook = $this->form->webhooks()->findOrFail($webhookId);
        $webhookService = app(\DigitalisStudios\SlickForms\Services\WebhookService::class);

        $result = $webhookService->testWebhook($webhook);
        $result['webhook_id'] = $webhookId;

        $this->webhookTestResult = $result;
    }

    /**
     * Close webhook editor modal
     */
    public function closeWebhookEditor(): void
    {
        $this->showWebhookEditor = false;
        $this->resetWebhookForm();
    }

    /**
     * Reset webhook form fields
     */
    protected function resetWebhookForm(): void
    {
        $this->editingWebhookId = null;
        $this->webhookName = '';
        $this->webhookUrl = '';
        $this->webhookMethod = 'POST';
        $this->webhookHeaders = [];
        $this->webhookFormat = 'json';
        $this->webhookTriggerConditions = [];
        $this->webhookEnabled = true;
        $this->webhookMaxRetries = 3;
        $this->webhookRetryDelay = 60;
        $this->webhookTestResult = null;
    }

    /**
     * Add HTTP header to webhook
     */
    public function addWebhookHeader(): void
    {
        $this->webhookHeaders[] = ['key' => '', 'value' => ''];
    }

    /**
     * Remove HTTP header from webhook
     */
    public function removeWebhookHeader(int $index): void
    {
        unset($this->webhookHeaders[$index]);
        $this->webhookHeaders = array_values($this->webhookHeaders);
    }

    // ==========================================
    // Version Management Methods
    // ==========================================

    /**
     * Toggle version history modal
     */
    public function toggleVersionHistory(): void
    {
        $this->showVersionHistory = ! $this->showVersionHistory;

        if ($this->showVersionHistory) {
            $this->loadVersionHistory();
        }
    }

    /**
     * Load version history for the form
     */
    public function loadVersionHistory(): void
    {
        $versionService = app(\DigitalisStudios\SlickForms\Services\FormVersionService::class);
        $this->versions = $versionService->getVersionHistory($this->form)->map(function ($version) {
            return [
                'id' => $version->id,
                'version_number' => $version->version_number,
                'version_name' => $version->version_name,
                'change_summary' => $version->change_summary,
                'published_at' => $version->published_at->format('M d, Y g:i A'),
                'published_by' => $version->publisher ? $version->publisher->name : 'System',
                'submission_count' => $version->submissions()->count(),
            ];
        })->toArray();
    }

    /**
     * Create a new version snapshot
     */
    public function createVersion(?string $versionName = null, ?string $changeSummary = null): void
    {
        $versionService = app(\DigitalisStudios\SlickForms\Services\FormVersionService::class);

        try {
            $version = $versionService->createVersion(
                $this->form,
                auth()->id(),
                $versionName,
                $changeSummary
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Version '.$version->version_number.' created successfully!',
            ]);

            $this->loadVersionHistory();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to create version: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Restore form to a specific version
     */
    public function restoreVersion(int $versionId)
    {
        $versionService = app(\DigitalisStudios\SlickForms\Services\FormVersionService::class);
        $version = \DigitalisStudios\SlickForms\Models\FormVersion::findOrFail($versionId);

        try {
            $versionService->restoreVersion($this->form, $version);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Form restored to version '.$version->version_number.' successfully!',
            ]);

            // Reload form structure
            $this->form->refresh();
            $this->loadFormStructure();
            $this->loadVersionHistory();
            $this->showVersionHistory = false;

            // Redirect to refresh the builder
            return redirect()->route('slick-forms.builder.show', $this->form->id);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to restore version: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Delete a specific version
     */
    public function deleteVersion(int $versionId): void
    {
        $versionService = app(\DigitalisStudios\SlickForms\Services\FormVersionService::class);
        $version = \DigitalisStudios\SlickForms\Models\FormVersion::findOrFail($versionId);

        try {
            $versionService->deleteVersion($version);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Version deleted successfully!',
            ]);

            $this->loadVersionHistory();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to delete version: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Show version comparison modal
     */
    public function showVersionComparison(int $versionId1, int $versionId2): void
    {
        $versionService = app(\DigitalisStudios\SlickForms\Services\FormVersionService::class);
        $version1 = \DigitalisStudios\SlickForms\Models\FormVersion::findOrFail($versionId1);
        $version2 = \DigitalisStudios\SlickForms\Models\FormVersion::findOrFail($versionId2);

        $this->compareVersionId1 = $versionId1;
        $this->compareVersionId2 = $versionId2;
        $this->versionDifferences = $versionService->compareVersions($version1, $version2);
        $this->showVersionComparison = true;
    }

    /**
     * Close version comparison modal
     */
    public function closeVersionComparison(): void
    {
        $this->showVersionComparison = false;
        $this->compareVersionId1 = null;
        $this->compareVersionId2 = null;
        $this->versionDifferences = [];
    }

    /**
     * Check if form has unsaved changes
     */
    public function hasUnsavedChanges(): bool
    {
        $versionService = app(\DigitalisStudios\SlickForms\Services\FormVersionService::class);

        return $versionService->hasChanges($this->form);
    }

    public function render()
    {
        $allFields = $this->form->fields()->orderBy('order')->get();
        $fieldsGroupedByContainer = $this->getFieldsGroupedByContainer();

        return view('slick-forms::livewire.form-builder', [
            'registry' => app(FieldTypeRegistry::class),
            'allFields' => $allFields,
            'fieldsGroupedByContainer' => $fieldsGroupedByContainer,
        ]);
    }
}
