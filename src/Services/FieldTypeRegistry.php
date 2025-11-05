<?php

namespace DigitalisStudios\SlickForms\Services;

use DigitalisStudios\SlickForms\FieldTypes\BaseFieldType;
use InvalidArgumentException;

class FieldTypeRegistry
{
    protected array $fieldTypes = [];

    public function register(string $name, string $class): void
    {
        if (! is_subclass_of($class, BaseFieldType::class)) {
            throw new InvalidArgumentException(
                "Field type {$class} must extend ".BaseFieldType::class
            );
        }

        $this->fieldTypes[$name] = $class;
    }

    public function get(string $name): BaseFieldType
    {
        if (! isset($this->fieldTypes[$name])) {
            throw new InvalidArgumentException("Field type {$name} is not registered");
        }

        return app($this->fieldTypes[$name]);
    }

    public function has(string $name): bool
    {
        return isset($this->fieldTypes[$name]);
    }

    public function all(): array
    {
        return collect($this->fieldTypes)
            ->map(fn ($class) => app($class))
            ->toArray();
    }

    public function getNames(): array
    {
        return array_keys($this->fieldTypes);
    }
}
