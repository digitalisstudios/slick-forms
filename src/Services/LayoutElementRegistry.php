<?php

namespace DigitalisStudios\SlickForms\Services;

use DigitalisStudios\SlickForms\LayoutElementTypes\BaseLayoutElementType;

/**
 * LayoutElementRegistry
 *
 * Service for registering and managing layout element types.
 * Similar to FieldTypeRegistry but for layout elements.
 */
class LayoutElementRegistry
{
    /**
     * Registered layout element types
     *
     * @var array<string, string> Array of element type => class name
     */
    protected array $types = [];

    /**
     * Cached instances of layout element types
     *
     * @var array<string, BaseLayoutElementType>
     */
    protected array $instances = [];

    /**
     * Register a layout element type
     *
     * @param  string  $type  Element type identifier (e.g., 'container', 'row', 'column')
     * @param  string  $class  Fully qualified class name
     *
     * @throws \Exception If type is already registered
     */
    public function register(string $type, string $class): void
    {
        if (isset($this->types[$type])) {
            throw new \Exception("Layout element type '{$type}' is already registered.");
        }

        $this->types[$type] = $class;
    }

    /**
     * Get an instance of a layout element type
     *
     * @param  string  $type  Element type identifier
     *
     * @throws \Exception If type is not registered
     */
    public function get(string $type): BaseLayoutElementType
    {
        if (! isset($this->types[$type])) {
            throw new \Exception("Layout element type '{$type}' is not registered.");
        }

        // Return cached instance if available
        if (isset($this->instances[$type])) {
            return $this->instances[$type];
        }

        // Create and cache instance
        $class = $this->types[$type];
        $instance = new $class;

        if (! $instance instanceof BaseLayoutElementType) {
            throw new \Exception("Layout element type '{$type}' must extend BaseLayoutElementType.");
        }

        $this->instances[$type] = $instance;

        return $instance;
    }

    /**
     * Get all registered element types
     *
     * @return array<string, string> Array of type => class name
     */
    public function all(): array
    {
        return $this->types;
    }

    /**
     * Get all registered element type instances
     *
     * @return array<string, BaseLayoutElementType>
     */
    public function getAllInstances(): array
    {
        $instances = [];

        foreach ($this->types as $type => $class) {
            $instances[$type] = $this->get($type);
        }

        return $instances;
    }

    /**
     * Check if a layout element type is registered
     *
     * @param  string  $type  Element type identifier
     */
    public function has(string $type): bool
    {
        return isset($this->types[$type]);
    }

    /**
     * Get configuration schema for a layout element type
     *
     * @param  string  $type  Element type identifier
     */
    public function getSchema(string $type): array
    {
        return $this->get($type)->getConfigSchema();
    }

    /**
     * Get property panel tabs for a layout element type
     *
     * @param  string  $type  Element type identifier
     */
    public function getTabs(string $type): array
    {
        return $this->get($type)->getPropertyTabs();
    }

    /**
     * Get the label for a layout element type
     *
     * @param  string  $type  Element type identifier
     */
    public function getLabel(string $type): string
    {
        return $this->get($type)->getLabel();
    }

    /**
     * Get the icon for a layout element type
     *
     * @param  string  $type  Element type identifier
     */
    public function getIcon(string $type): string
    {
        return $this->get($type)->getIcon();
    }

    /**
     * Check if an element type can contain another element type
     *
     * @param  string  $parentType  Parent element type
     * @param  string  $childType  Child element type
     */
    public function canContain(string $parentType, string $childType): bool
    {
        return $this->get($parentType)->canContain($childType);
    }

    /**
     * Get allowed child types for an element type
     *
     * @param  string  $type  Element type identifier
     */
    public function getAllowedChildren(string $type): array
    {
        return $this->get($type)->getAllowedChildren();
    }
}
