<?php

namespace DigitalisStudios\SlickForms\Services;

/**
 * TabRegistry Service
 *
 * Manages property panel tabs for field types and layout element types.
 * Allows types to register custom tabs dynamically.
 */
class TabRegistry
{
    /**
     * Default tabs for field types
     */
    protected array $defaultFieldTabs = [
        'basic' => [
            'label' => 'Basic',
            'icon' => 'bi-info-circle',
            'order' => 10,
        ],
        'options' => [
            'label' => 'Options',
            'icon' => 'bi-sliders',
            'order' => 20,
        ],
        'validation' => [
            'label' => 'Validation',
            'icon' => 'bi-check-circle',
            'order' => 30,
            'view' => 'slick-forms::livewire.partials.properties-panel.tabs.validation',
        ],
        'style' => [
            'label' => 'Style',
            'icon' => 'bi-palette',
            'order' => 40,
            'view' => 'slick-forms::livewire.partials.properties-panel.tabs.style',
        ],
        'advanced' => [
            'label' => 'Visibility',
            'icon' => 'bi-eye',
            'order' => 50,
            'view' => 'slick-forms::livewire.partials.properties-panel.tabs.advanced',
        ],
    ];

    /**
     * Default tabs for layout element types
     */
    protected array $defaultElementTabs = [
        'basic' => [
            'label' => 'Basic',
            'icon' => 'bi-info-circle',
            'order' => 10,
        ],
        'settings' => [
            'label' => 'Settings',
            'icon' => 'bi-sliders',
            'order' => 20,
        ],
        'style' => [
            'label' => 'Style',
            'icon' => 'bi-palette',
            'order' => 30,
        ],
        'children' => [
            'label' => 'Children',
            'icon' => 'bi-diagram-3',
            'order' => 35,
            'view' => 'slick-forms::livewire.partials.tabs.children-tab',
        ],
        'advanced' => [
            'label' => 'Advanced',
            'icon' => 'bi-gear',
            'order' => 40,
        ],
    ];

    /**
     * Default tabs for form-level settings
     * These tabs appear when the form itself is selected (not a field or element)
     */
    protected array $defaultFormTabs = [
        'basic' => [
            'label' => 'Basic',
            'icon' => 'bi-info-circle',
            'order' => 10,
        ],
        'email' => [
            'label' => 'Email',
            'icon' => 'bi-envelope',
            'order' => 20,
            'view' => 'slick-forms::livewire.partials.settings.email-settings',
        ],
        'spam' => [
            'label' => 'Spam',
            'icon' => 'bi-shield-check',
            'order' => 30,
            'view' => 'slick-forms::livewire.partials.settings.spam-settings',
        ],
        'model' => [
            'label' => 'Model',
            'icon' => 'bi-database',
            'order' => 35,
            'view' => 'slick-forms::livewire.partials.settings.model-settings',
        ],
        'success' => [
            'label' => 'Success',
            'icon' => 'bi-check-circle',
            'order' => 37,
            'view' => 'slick-forms::livewire.partials.settings.success-settings',
        ],
        'urls' => [
            'label' => 'URLs',
            'icon' => 'bi-link-45deg',
            'order' => 38,
            'view' => 'slick-forms::livewire.partials.settings.url-settings',
        ],
        'webhooks' => [
            'label' => 'Webhooks',
            'icon' => 'bi-broadcast',
            'order' => 39,
            'view' => 'slick-forms::livewire.partials.settings.webhook-settings',
        ],
        'advanced' => [
            'label' => 'Advanced',
            'icon' => 'bi-gear',
            'order' => 40,
        ],
    ];

    /**
     * Get tabs for a field type
     *
     * @param  object  $fieldType  Instance of field type (must have getPropertyTabs() method)
     * @return array Sorted tabs array
     */
    public function getFieldTabs(object $fieldType): array
    {
        // Get tabs from field type if method exists
        if (method_exists($fieldType, 'getPropertyTabs')) {
            $customTabs = $fieldType->getPropertyTabs();

            // Merge with defaults, allowing override
            $tabs = array_merge($this->defaultFieldTabs, $customTabs);

            // Filter out tabs that are explicitly set to false
            $tabs = array_filter($tabs, function ($tab) {
                return $tab !== false;
            });
        } else {
            // Fall back to defaults
            $tabs = $this->defaultFieldTabs;
        }

        // Sort by order
        uasort($tabs, function ($a, $b) {
            return ($a['order'] ?? 999) <=> ($b['order'] ?? 999);
        });

        return $tabs;
    }

    /**
     * Get tabs for a layout element type
     *
     * @param  object  $elementType  Instance of layout element type (must have getPropertyTabs() method)
     * @return array Sorted tabs array
     */
    public function getElementTabs(object $elementType): array
    {
        // Get tabs from element type if method exists
        if (method_exists($elementType, 'getPropertyTabs')) {
            $customTabs = $elementType->getPropertyTabs();

            // Merge with defaults, allowing override
            $tabs = array_merge($this->defaultElementTabs, $customTabs);
        } else {
            // Fall back to defaults
            $tabs = $this->defaultElementTabs;
        }

        // Sort by order
        uasort($tabs, function ($a, $b) {
            return ($a['order'] ?? 999) <=> ($b['order'] ?? 999);
        });

        return $tabs;
    }

    /**
     * Get default field tabs
     */
    public function getDefaultFieldTabs(): array
    {
        return $this->defaultFieldTabs;
    }

    /**
     * Get default element tabs
     */
    public function getDefaultElementTabs(): array
    {
        return $this->defaultElementTabs;
    }

    /**
     * Get tabs for form-level settings
     *
     * @return array Sorted tabs array
     */
    public function getFormTabs(): array
    {
        // Sort by order
        $tabs = $this->defaultFormTabs;

        uasort($tabs, function ($a, $b) {
            return ($a['order'] ?? 999) <=> ($b['order'] ?? 999);
        });

        return $tabs;
    }

    /**
     * Get default form tabs
     */
    public function getDefaultFormTabs(): array
    {
        return $this->defaultFormTabs;
    }

    /**
     * Check if a tab exists in the given tabs array
     *
     * @param  array  $tabs  Tabs array
     * @param  string  $tabKey  Tab key to check
     */
    public function hasTab(array $tabs, string $tabKey): bool
    {
        return isset($tabs[$tabKey]);
    }

    /**
     * Get a specific tab configuration
     *
     * @param  array  $tabs  Tabs array
     * @param  string  $tabKey  Tab key
     * @return array|null Tab configuration or null if not found
     */
    public function getTab(array $tabs, string $tabKey): ?array
    {
        return $tabs[$tabKey] ?? null;
    }

    /**
     * Merge multiple tab configurations
     *
     * Useful for combining tabs from multiple sources (base class, trait, etc.)
     *
     * @param  array  ...$tabArrays  Multiple tab configuration arrays
     * @return array Merged and sorted tabs
     */
    public function mergeTabs(array ...$tabArrays): array
    {
        $merged = [];

        foreach ($tabArrays as $tabs) {
            $merged = array_merge($merged, $tabs);
        }

        // Sort by order
        uasort($merged, function ($a, $b) {
            return ($a['order'] ?? 999) <=> ($b['order'] ?? 999);
        });

        return $merged;
    }
}
