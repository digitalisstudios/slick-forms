<?php

namespace DigitalisStudios\SlickForms\Services;

use DigitalisStudios\SlickForms\Events\ModelBound;
use DigitalisStudios\SlickForms\Events\ModelSaved;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\FormModelBinding;
use Illuminate\Database\Eloquent\Model;

/**
 * Service for binding forms to Eloquent models
 *
 * Phase 3: Full implementation
 */
class ModelBindingService
{
    /**
     * Bind a model to a form and create/update binding configuration
     *
     * @param  array  $config  Binding configuration
     * @return array Field mapping configuration
     */
    public function bindModel(CustomForm $form, array $config): array
    {
        // Create or update binding configuration
        $binding = FormModelBinding::updateOrCreate(
            ['form_id' => $form->id],
            [
                'model_class' => $config['model_class'],
                'route_parameter' => $config['route_parameter'] ?? 'model',
                'route_key' => $config['route_key'] ?? 'id',
                'field_mappings' => $config['field_mappings'] ?? [],
                'relationship_mappings' => $config['relationship_mappings'] ?? [],
                'allow_create' => $config['allow_create'] ?? true,
                'allow_update' => $config['allow_update'] ?? true,
                'custom_population_logic' => $config['custom_population_logic'] ?? null,
                'custom_save_logic' => $config['custom_save_logic'] ?? null,
            ]
        );

        // Dispatch event
        event(new ModelBound($form, null, $binding));

        return [
            'model_class' => $binding->model_class,
            'field_mappings' => $binding->field_mappings ?? [],
            'relationship_mappings' => $binding->relationship_mappings ?? [],
        ];
    }

    /**
     * Populate form data from a model instance
     *
     * @param  Model  $model  Model to read data from
     * @return array Form data array keyed by field names
     */
    public function populateFormData(CustomForm $form, $model): array
    {
        // Get binding configuration
        $binding = FormModelBinding::where('form_id', $form->id)->first();

        if (! $binding) {
            return [];
        }

        $formData = [];

        // Apply field mappings
        foreach ($binding->field_mappings ?? [] as $fieldName => $modelAttribute) {
            // Support dot notation for nested attributes
            $value = $this->resolveNestedAttribute($model, $modelAttribute);

            // Apply custom population logic if defined
            if (! empty($binding->custom_population_logic)) {
                $value = $this->applyTransformer($value, $binding->custom_population_logic);
            }

            $formData[$fieldName] = $value;
        }

        // Apply relationship mappings
        foreach ($binding->relationship_mappings ?? [] as $fieldName => $relationshipPath) {
            $value = $this->resolveNestedAttribute($model, $relationshipPath);

            $formData[$fieldName] = $value;
        }

        return $formData;
    }

    /**
     * Save form data to a model instance
     *
     * @param  array  $formData  Form submission data
     * @param  \DigitalisStudios\SlickForms\Models\CustomFormSubmission  $submission  Form submission record
     * @param  Model|null  $model  Existing model or null to create new
     * @return Model Saved model instance
     */
    public function saveModel(CustomForm $form, array $formData, $submission, $model = null)
    {
        // Get binding configuration
        $binding = FormModelBinding::where('form_id', $form->id)->first();

        if (! $binding) {
            throw new \RuntimeException('No model binding configured for this form');
        }

        // Check permissions
        if ($model === null && ! $binding->allowsCreate()) {
            throw new \RuntimeException('Model creation is not allowed for this form');
        }

        if ($model !== null && ! $binding->allowsUpdate()) {
            throw new \RuntimeException('Model updates are not allowed for this form');
        }

        // Create new model instance if needed
        if ($model === null) {
            $modelClass = $binding->model_class;

            if (! class_exists($modelClass)) {
                throw new \RuntimeException("Model class not found: {$modelClass}");
            }

            $model = new $modelClass;
        }

        // Apply field mappings
        foreach ($binding->field_mappings ?? [] as $fieldName => $modelAttribute) {
            if (! array_key_exists($fieldName, $formData)) {
                continue; // Skip if field not in form data
            }

            $value = $formData[$fieldName];

            // Apply custom save logic if defined
            if (! empty($binding->custom_save_logic)) {
                $value = $this->applyTransformer($value, $binding->custom_save_logic);
            }

            // Set attribute (supports dot notation for relationships)
            $this->setNestedAttribute($model, $modelAttribute, $value);
        }

        // Save the model
        $model->save();

        // Handle relationship mappings (many-to-many, etc.)
        foreach ($binding->relationship_mappings ?? [] as $fieldName => $relationshipPath) {
            if (! array_key_exists($fieldName, $formData)) {
                continue;
            }

            $value = $formData[$fieldName];

            // Extract relationship name (first part before dot)
            $parts = explode('.', $relationshipPath);
            $relationshipName = $parts[0];

            // Handle many-to-many relationships with sync()
            if (method_exists($model, $relationshipName)) {
                $relation = $model->{$relationshipName}();

                // Check if it's a BelongsToMany relationship
                if (method_exists($relation, 'sync')) {
                    $relation->sync($value);
                }
            }
        }

        // Dispatch event
        event(new ModelSaved($form, $model, $binding));

        return $model;
    }

    /**
     * Resolve nested attribute value using dot notation
     *
     * @param  Model  $model
     * @param  string  $path  Dot-notation path (e.g., 'profile.address.city')
     * @return mixed Attribute value
     */
    public function resolveNestedAttribute($model, string $path): mixed
    {
        $keys = explode('.', $path);
        $value = $model;

        foreach ($keys as $key) {
            if ($value === null) {
                return null;
            }

            // Check if it's a relationship
            if ($value instanceof Model && method_exists($value, $key)) {
                $value = $value->{$key};
            } elseif ($value instanceof Model) {
                // Direct attribute access
                $value = $value->getAttribute($key);
            } elseif (is_array($value)) {
                // Array access
                $value = $value[$key] ?? null;
            } elseif (is_object($value)) {
                // Object property access
                $value = $value->{$key} ?? null;
            } else {
                return null;
            }
        }

        return $value;
    }

    /**
     * Set nested attribute value using dot notation
     *
     * @param  Model  $model
     * @param  string  $path  Dot-notation path (e.g., 'profile.address.city')
     * @param  mixed  $value  Value to set
     */
    public function setNestedAttribute($model, string $path, $value): void
    {
        $keys = explode('.', $path);

        // If simple attribute (no dots), set directly
        if (count($keys) === 1) {
            $model->{$keys[0]} = $value;

            return;
        }

        // Check if first key is a JSON/array cast attribute
        $firstKey = $keys[0];
        $casts = $model->getCasts();

        // Check if the attribute is cast as array/json or if it's a fillable JSON column
        $isJsonAttribute = false;
        if (isset($casts[$firstKey])) {
            $castType = $casts[$firstKey];
            // Handle both string casts ('array', 'json') and class-based casts
            $isJsonAttribute = in_array($castType, ['array', 'json', 'object', 'collection'])
                || str_contains($castType, 'AsArrayObject')
                || str_contains($castType, 'AsCollection');
        }

        // Also check if the current value is an array (already cast)
        if (! $isJsonAttribute) {
            try {
                $currentValue = $model->{$firstKey};
                $isJsonAttribute = is_array($currentValue) || is_object($currentValue);
            } catch (\Exception $e) {
                // Attribute doesn't exist or can't be accessed
            }
        }

        if ($isJsonAttribute) {
            // Handle JSON attribute - update nested value within the JSON
            $data = $model->{$firstKey} ?? [];
            if (! is_array($data)) {
                $data = (array) $data;
            }

            // Navigate to nested position and set value
            $current = &$data;
            $remaining = array_slice($keys, 1);
            $lastKey = array_pop($remaining);

            foreach ($remaining as $key) {
                if (! isset($current[$key]) || ! is_array($current[$key])) {
                    $current[$key] = [];
                }
                $current = &$current[$key];
            }

            $current[$lastKey] = $value;
            $model->{$firstKey} = $data;

            return;
        }

        // Handle nested relationships
        $current = $model;
        $lastKey = array_pop($keys);

        foreach ($keys as $key) {
            // Navigate through relationships
            if (method_exists($current, $key)) {
                $relation = $current->{$key};

                // If relation doesn't exist, create it
                if ($relation === null) {
                    $relationMethod = $current->{$key}();
                    $relatedClass = get_class($relationMethod->getRelated());
                    $relation = new $relatedClass;
                    $current->{$key}()->save($relation);
                }

                $current = $relation;
            } else {
                // Can't navigate further
                throw new \RuntimeException("Cannot navigate to nested attribute: {$path}");
            }
        }

        // Set the final attribute
        $current->{$lastKey} = $value;

        // Save if it's a separate model
        if ($current !== $model && $current instanceof Model) {
            $current->save();
        }
    }

    /**
     * Apply custom transformer code to a value
     *
     * @param  mixed  $value  Input value
     * @param  string  $transformerCode  PHP code to execute
     * @return mixed Transformed value
     */
    public function applyTransformer($value, string $transformerCode): mixed
    {
        // If it's a class name, instantiate and call transform method
        if (class_exists($transformerCode)) {
            $transformer = new $transformerCode;

            if (method_exists($transformer, 'transform')) {
                return $transformer->transform($value);
            }

            throw new \RuntimeException('Transformer class must have a transform() method');
        }

        // Otherwise, treat as PHP code to evaluate
        // WARNING: This is potentially dangerous - only use with trusted code
        try {
            // Wrap code in a closure to provide $value in scope
            $code = trim($transformerCode);

            // Check if code already starts with "return", if not add it
            if (! str_starts_with($code, 'return ')) {
                $code = "return {$code}";
            }

            // Remove trailing semicolon if present
            $code = rtrim($code, ';');

            // Create a closure that has $value in scope
            $closure = eval("return function(\$value) { {$code}; };");

            // If eval returns false, it means there was a parse error
            if ($closure === false && error_get_last() !== null) {
                return $value;
            }

            // Execute the closure with the value
            return $closure($value);
        } catch (\Throwable $e) {
            // Return original value on error instead of throwing exception
            return $value;
        }
    }

    /**
     * Get model from route parameters
     *
     * @param  FormModelBinding  $binding  Model binding configuration
     * @param  array  $routeParams  Route parameters array
     * @return Model|null Model instance or null if not found
     */
    public function getModelFromRoute(FormModelBinding $binding, array $routeParams): ?Model
    {
        $modelClass = $binding->model_class;

        if (! class_exists($modelClass)) {
            return null;
        }

        $routeParameter = $binding->route_parameter ?? 'model';
        $routeKey = $binding->route_key ?? 'id';

        // Get parameter value from route params
        if (! isset($routeParams[$routeParameter])) {
            return null;
        }

        // Query the model
        return $modelClass::where($routeKey, $routeParams[$routeParameter])->first();
    }
}
