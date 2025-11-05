<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * BaseLayoutElementType
 *
 * Abstract base class for all layout element types.
 * Provides the contract and shared functionality for layout elements.
 */
abstract class BaseLayoutElementType
{
    /**
     * Get the unique type identifier for this layout element
     *
     * @return string Type identifier (e.g., 'container', 'row', 'column')
     */
    abstract public function getType(): string;

    /**
     * Get the display label for this layout element type
     *
     * @return string Human-readable label (e.g., 'Container', 'Row', 'Column')
     */
    abstract public function getLabel(): string;

    /**
     * Get the Bootstrap icon class for this layout element type
     *
     * @return string Icon class (e.g., 'bi-box', 'bi-grid', 'bi-columns')
     */
    abstract public function getIcon(): string;

    /**
     * Render the layout element for display on the user-facing form
     *
     * @param  SlickFormLayoutElement  $element  The layout element model
     * @param  string  $childrenHtml  Rendered HTML of child elements/fields
     * @return string HTML markup
     */
    abstract public function render(SlickFormLayoutElement $element, string $childrenHtml): string;

    /**
     * Render the layout element preview in the form builder
     *
     * @param  SlickFormLayoutElement  $element  The layout element model
     * @param  string  $childrenHtml  Rendered HTML of child elements/fields
     * @return string HTML markup
     */
    abstract public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string;

    /**
     * Get configuration schema for this layout element type
     *
     * Defines the properties that can be configured in the properties panel.
     * Each field type can override this to add type-specific options.
     *
     * @return array Schema array
     */
    public function getConfigSchema(): array
    {
        return [
            'element_id' => [
                'type' => 'text',
                'label' => 'Element ID',
                'tab' => 'basic',
                'target' => 'column',
                'required' => true,
                'help' => 'Unique HTML id attribute (letters, numbers, hyphens, underscores)',
                'placeholder' => 'e.g., main-container, sidebar-column',
            ],
            'class' => [
                'type' => 'text',
                'label' => 'CSS Classes',
                'tab' => 'style',
                'target' => 'column',
                'help' => 'Space-separated CSS class names',
                'placeholder' => 'e.g., mt-5 shadow-lg custom-class',
            ],
            'style' => [
                'type' => 'textarea',
                'label' => 'Inline Styles',
                'tab' => 'style',
                'target' => 'column',
                'rows' => 3,
                'help' => 'Custom CSS styles for this element',
                'placeholder' => 'e.g., background-color: #f8f9fa; padding: 20px;',
            ],
            // Bootstrap Utilities - Spacing
            'spacing.margin_top' => [
                'type' => 'select',
                'label' => 'Margin Top',
                'tab' => 'style',
                'target' => 'settings',
                'options' => [
                    '' => 'None',
                    '0' => '0',
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    'auto' => 'Auto',
                ],
            ],
            'spacing.margin_bottom' => [
                'type' => 'select',
                'label' => 'Margin Bottom',
                'tab' => 'style',
                'target' => 'settings',
                'options' => [
                    '' => 'None',
                    '0' => '0',
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    'auto' => 'Auto',
                ],
            ],
            'spacing.padding_top' => [
                'type' => 'select',
                'label' => 'Padding Top',
                'tab' => 'style',
                'target' => 'settings',
                'options' => [
                    '' => 'None',
                    '0' => '0',
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ],
            ],
            'spacing.padding_bottom' => [
                'type' => 'select',
                'label' => 'Padding Bottom',
                'tab' => 'style',
                'target' => 'settings',
                'options' => [
                    '' => 'None',
                    '0' => '0',
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ],
            ],
            // Display Utilities
            'display.display' => [
                'type' => 'select',
                'label' => 'Display (XS)',
                'tab' => 'style',
                'target' => 'settings',
                'options' => [
                    '' => 'Default',
                    'none' => 'Hide',
                    'block' => 'Block',
                    'inline' => 'Inline',
                    'inline-block' => 'Inline Block',
                    'flex' => 'Flex',
                ],
            ],
            'display.display_sm' => [
                'type' => 'select',
                'label' => 'Display (SM)',
                'tab' => 'style',
                'target' => 'settings',
                'options' => [
                    '' => 'Default',
                    'none' => 'Hide',
                    'block' => 'Block',
                    'inline' => 'Inline',
                    'inline-block' => 'Inline Block',
                    'flex' => 'Flex',
                ],
            ],
            'display.display_md' => [
                'type' => 'select',
                'label' => 'Display (MD)',
                'tab' => 'style',
                'target' => 'settings',
                'options' => [
                    '' => 'Default',
                    'none' => 'Hide',
                    'block' => 'Block',
                    'inline' => 'Inline',
                    'inline-block' => 'Inline Block',
                    'flex' => 'Flex',
                ],
            ],
            // Text Alignment
            'text_alignment.align' => [
                'type' => 'select',
                'label' => 'Text Alignment',
                'tab' => 'style',
                'target' => 'settings',
                'options' => [
                    '' => 'Default',
                    'start' => 'Start (Left)',
                    'center' => 'Center',
                    'end' => 'End (Right)',
                ],
            ],
            // Note: Conditional logic is handled by the 'advanced' tab's custom view
            // See getPropertyTabs() method
        ];
    }

    /**
     * Get property panel tabs for this layout element type
     *
     * Returns array of tabs to display in the properties panel.
     * Element types can override this to add custom tabs or modify existing ones.
     *
     * @return array Array of tab configurations keyed by tab key
     */
    public function getPropertyTabs(): array
    {
        return [
            'basic' => [
                'label' => 'Basic',
                'icon' => 'bi-info-circle',
                'order' => 10,
                'view' => null, // null = auto-generate from schema
            ],
            'settings' => [
                'label' => 'Settings',
                'icon' => 'bi-sliders',
                'order' => 20,
                'view' => null, // null = auto-generate from schema
            ],
            'style' => [
                'label' => 'Style',
                'icon' => 'bi-palette',
                'order' => 30,
                'view' => null, // null = auto-generate from schema
            ],
            'advanced' => [
                'label' => 'Visibility',
                'icon' => 'bi-eye',
                'order' => 40,
                'view' => 'slick-forms::livewire.partials.properties-panel.tabs.advanced', // Use same conditional logic UI as fields
            ],
        ];
    }

    /**
     * Determine if this layout element can contain another element type
     *
     * @param  string  $elementType  The type to check (e.g., 'row', 'field', 'column')
     * @return bool True if can contain, false otherwise
     */
    public function canContain(string $elementType): bool
    {
        // Default: allow all children (override in subclasses for restrictions)
        return true;
    }

    /**
     * Get allowed child element types
     *
     * @return array Array of allowed element type strings, or ['*'] for all
     */
    public function getAllowedChildren(): array
    {
        // Default: allow all types
        return ['*'];
    }

    /**
     * Get helper methods for building utility classes
     * Similar to BaseFieldType utility methods
     */

    /**
     * Build utility classes from element settings
     */
    protected function buildUtilityClasses(SlickFormLayoutElement $element): string
    {
        $classes = [];

        $settings = $element->settings ?? [];

        // Spacing utilities
        $classes[] = $this->buildSpacingClasses($settings);

        // Display utilities
        $classes[] = $this->buildDisplayClasses($settings);

        // Text alignment utilities
        $classes[] = $this->buildTextAlignmentClasses($settings);

        return implode(' ', array_filter($classes));
    }

    /**
     * Build spacing utility classes
     */
    protected function buildSpacingClasses(array $settings): string
    {
        $classes = [];
        $spacing = $settings['spacing'] ?? [];

        if (! empty($spacing['margin_top'])) {
            $classes[] = 'mt-'.$spacing['margin_top'];
        }
        if (! empty($spacing['margin_bottom'])) {
            $classes[] = 'mb-'.$spacing['margin_bottom'];
        }
        if (! empty($spacing['padding_top'])) {
            $classes[] = 'pt-'.$spacing['padding_top'];
        }
        if (! empty($spacing['padding_bottom'])) {
            $classes[] = 'pb-'.$spacing['padding_bottom'];
        }

        return implode(' ', $classes);
    }

    /**
     * Build display utility classes
     */
    protected function buildDisplayClasses(array $settings): string
    {
        $classes = [];
        $display = $settings['display'] ?? [];

        foreach (['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $breakpoint) {
            $key = $breakpoint === 'xs' ? 'display' : 'display_'.$breakpoint;
            if (! empty($display[$key])) {
                $prefix = $breakpoint === 'xs' ? 'd' : 'd-'.$breakpoint;
                $classes[] = $prefix.'-'.$display[$key];
            }
        }

        return implode(' ', $classes);
    }

    /**
     * Build text alignment utility classes
     */
    protected function buildTextAlignmentClasses(array $settings): string
    {
        $classes = [];
        $alignment = $settings['text_alignment'] ?? [];

        if (! empty($alignment['align'])) {
            $classes[] = 'text-'.$alignment['align'];
        }

        return implode(' ', $classes);
    }

    /**
     * Get all classes for the element (custom + utility)
     */
    protected function getElementClasses(SlickFormLayoutElement $element, string $baseClasses = ''): string
    {
        $classes = [];

        if ($baseClasses) {
            $classes[] = $baseClasses;
        }

        if ($element->class) {
            $classes[] = $element->class;
        }

        $utilityClasses = $this->buildUtilityClasses($element);
        if ($utilityClasses) {
            $classes[] = $utilityClasses;
        }

        return implode(' ', array_filter($classes));
    }

    /**
     * Get complete schema documentation for this layout element type
     *
     * Dynamically generates JSON schema by reading from:
     * - getConfigSchema() for properties
     * - getPropertyTabs() for tab structure
     * - getAllowedChildren() for nesting rules
     * - SlickFormLayoutElement $fillable for available columns
     *
     * @return string JSON schema documentation
     */
    public function getFullSchema(): string
    {
        // Get configuration schemas
        $baseSchema = (new \ReflectionClass(self::class))->getMethod('getConfigSchema')->invoke($this);
        $configSchema = $this->getConfigSchema();
        $mergedSchema = array_merge($baseSchema, $configSchema);

        // Get tabs
        $tabs = $this->getPropertyTabs();

        // Get allowed children
        $allowedChildren = $this->getAllowedChildren();

        // Build the full schema
        $schema = [
            'metadata' => [
                'type' => $this->getType(),
                'label' => $this->getLabel(),
                'icon' => $this->getIcon(),
                'description' => $this->getDescription(),
            ],
            'usage' => $this->buildUsageExample(),
            'properties' => $this->buildPropertiesFromSchema($mergedSchema),
            'allowed_children' => $allowedChildren,
            'tabs' => $this->formatTabs($tabs),
        ];

        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Get description for this layout element type
     * Override in child classes to provide specific descriptions
     */
    protected function getDescription(): string
    {
        return '';
    }

    /**
     * Build usage example showing how to create this layout element type
     */
    protected function buildUsageExample(): array
    {
        $example = [
            'slick_form_id' => 1,
            'element_type' => $this->getType(),
            'element_id' => $this->getExampleElementId(),
            'order' => 0,
        ];

        // Add parent_id for nested elements
        if ($this->requiresParent()) {
            $example['parent_id'] = $this->getExampleParentId();
        }

        // Add element-specific settings
        $exampleSettings = $this->getExampleSettings();
        if (! empty($exampleSettings)) {
            $example['settings'] = $exampleSettings;
        }

        return [
            'model' => 'SlickFormLayoutElement',
            'method' => 'create',
            'example' => $example,
        ];
    }

    /**
     * Get example element ID
     */
    protected function getExampleElementId(): string
    {
        return match ($this->getType()) {
            'container' => 'main-container',
            'row' => 'header-row',
            'column' => 'left-column',
            'card' => 'info-card',
            'tabs' => 'profile-tabs',
            'tab' => 'general-tab',
            'accordion' => 'faq-accordion',
            'accordion_item' => 'question-1',
            'carousel' => 'hero-carousel',
            'carousel_slide' => 'slide-1',
            'table' => 'data-table',
            default => $this->getType().'-element',
        };
    }

    /**
     * Get example parent ID
     */
    protected function getExampleParentId(): ?int
    {
        // Elements that require parents get an example parent ID
        return match ($this->getType()) {
            'column' => 1, // parent: row
            'tab' => 2, // parent: tabs
            'accordion_item' => 3, // parent: accordion
            'carousel_slide' => 4, // parent: carousel
            'table_row' => 5, // parent: table section
            'table_cell' => 6, // parent: table row
            default => null,
        };
    }

    /**
     * Check if this element type requires a parent
     */
    protected function requiresParent(): bool
    {
        return in_array($this->getType(), [
            'column',
            'tab',
            'accordion_item',
            'carousel_slide',
            'table_row',
            'table_cell',
        ]);
    }

    /**
     * Get example settings for this element type
     */
    protected function getExampleSettings(): array
    {
        return match ($this->getType()) {
            'container' => [
                'fluid' => false,
                'breakpoint' => 'lg',
            ],
            'row' => [
                'gutter' => 'g-4',
                'horizontal_alignment' => 'justify-content-center',
                'vertical_alignment' => 'align-items-center',
            ],
            'column' => [
                'width' => [
                    'xs' => '12',
                    'md' => '6',
                    'lg' => '4',
                ],
                'offset' => [
                    'lg' => '1',
                ],
            ],
            'card' => [
                'card_header_show' => true,
                'card_header_text' => 'Card Title',
                'card_title' => 'Featured Content',
                'card_subtitle' => 'Subtitle text here',
                'card_background' => 'light',
                'card_shadow' => 'shadow',
            ],
            'tabs' => [
                'tab_style' => 'nav-tabs',
                'tab_alignment' => 'justify-content-center',
                'tab_fill' => false,
                'default_active_tab' => 0,
                'fade_animation' => true,
            ],
            'tab' => [
                'tab_label' => 'General',
                'tab_icon' => 'bi-info-circle',
            ],
            'accordion' => [
                'accordion_flush' => false,
                'always_open' => false,
                'default_open_item' => 0,
            ],
            'accordion_item' => [
                'accordion_item_label' => 'Frequently Asked Question',
                'accordion_item_icon' => 'bi-question-circle',
                'initially_open' => false,
            ],
            'carousel' => [
                'direction' => 'horizontal',
                'speed' => 300,
                'loop' => true,
                'slides_per_view' => 1,
                'space_between' => 30,
                'autoplay' => [
                    'enabled' => true,
                    'delay' => 3000,
                ],
                'navigation' => [
                    'enabled' => true,
                ],
                'pagination' => [
                    'enabled' => true,
                    'type' => 'bullets',
                    'clickable' => true,
                ],
            ],
            'carousel_slide' => [
                'container_label' => 'Hero Image',
            ],
            'table' => [
                'bordered' => true,
                'striped' => true,
                'hover' => true,
                'responsive' => true,
            ],
            default => [],
        };
    }

    /**
     * Build properties documentation from config schema
     *
     * @param  array  $schema  Merged config schema from parent and child
     * @return array Properties documentation
     */
    protected function buildPropertiesFromSchema(array $schema): array
    {
        $properties = [];

        // Add model fillable columns first
        $model = new \DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
        foreach ($model->getFillable() as $column) {
            // Skip if already in schema (will be added below with more detail)
            if (isset($schema[$column])) {
                continue;
            }

            // Add basic documentation for fillable columns not in schema
            $properties[$column] = $this->getColumnDocumentation($column);
        }

        // Add schema-defined properties
        foreach ($schema as $key => $config) {
            $property = [
                'type' => $config['type'] ?? 'string',
                'required' => $config['required'] ?? false,
                'storage' => $this->determineStorage($config['target'] ?? 'settings'),
                'default' => $config['default'] ?? null,
                'description' => $config['help'] ?? $config['label'] ?? '',
            ];

            // Extract options if defined
            if (isset($config['options']) && is_array($config['options'])) {
                $property['options'] = array_values($config['options']);
                $property['options_labels'] = $config['options'];
            }

            // Add tab information
            if (isset($config['tab'])) {
                $property['tab'] = $config['tab'];
            }

            $properties[$key] = $property;
        }

        return $properties;
    }

    /**
     * Get documentation for a database column
     */
    protected function getColumnDocumentation(string $column): array
    {
        $docs = [
            'slick_form_id' => [
                'type' => 'integer',
                'required' => true,
                'storage' => 'column',
                'description' => 'The form this element belongs to',
            ],
            'slick_form_page_id' => [
                'type' => 'integer',
                'required' => false,
                'storage' => 'column',
                'description' => 'The page this element belongs to (for multi-page forms)',
            ],
            'parent_id' => [
                'type' => 'integer',
                'required' => false,
                'storage' => 'column',
                'description' => 'Parent element ID for nesting (null = top-level)',
            ],
            'element_type' => [
                'type' => 'string',
                'required' => true,
                'storage' => 'column',
                'value' => $this->getType(),
                'description' => "Must be '{$this->getType()}' for this element type",
            ],
            'order' => [
                'type' => 'integer',
                'required' => true,
                'storage' => 'column',
                'description' => 'Display order among siblings',
            ],
            'conditional_logic' => [
                'type' => 'object',
                'required' => false,
                'storage' => 'json_column',
                'description' => 'Visibility rules (same structure as field conditional logic)',
            ],
        ];

        return $docs[$column] ?? [
            'type' => 'mixed',
            'storage' => 'column',
            'description' => '',
        ];
    }

    /**
     * Determine storage location from schema target
     */
    protected function determineStorage(string $target): string
    {
        return match ($target) {
            'column' => 'column',
            'settings' => 'json_column',
            'conditional_logic' => 'json_column',
            default => 'json_column',
        };
    }

    /**
     * Format tabs documentation
     */
    protected function formatTabs(array $tabs): array
    {
        $formatted = [];

        foreach ($tabs as $key => $config) {
            // Skip disabled tabs (false values)
            if ($config === false) {
                continue;
            }

            // Handle array config
            if (is_array($config)) {
                $formatted[$key] = [
                    'label' => $config['label'] ?? $key,
                    'icon' => $config['icon'] ?? null,
                    'order' => $config['order'] ?? 0,
                    'view' => $config['view'] ?? 'auto-generated from schema',
                ];
            }
        }

        return $formatted;
    }
}
