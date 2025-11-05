<?php

namespace DigitalisStudios\SlickForms\Services;

use DigitalisStudios\SlickForms\Events\DynamicOptionsFailed;
use DigitalisStudios\SlickForms\Events\DynamicOptionsLoaded;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\DynamicOptionsCache;
use Illuminate\Support\Facades\Http;

/**
 * Service for handling dynamic options loading from URLs and models
 *
 * Phase 3: Full implementation
 */
class DynamicOptionsService
{
    /**
     * Load options for a field from configured source
     *
     * @param  CustomFormField  $field  Field to load options for
     * @param  string|null  $parentValue  Value of parent field for cascading dropdowns
     * @return array Options array with 'value' and 'label' keys
     */
    public function loadOptions(CustomFormField $field, ?string $parentValue = null): array
    {
        $fieldOptions = $field->options ?? [];

        // Check if field has dynamic options configured
        if (empty($fieldOptions['dynamic_source'])) {
            return [];
        }

        $source = $fieldOptions['dynamic_source']; // 'url' or 'model'

        // Build cache key
        $cacheKey = $this->buildCacheKey($field, $parentValue);

        // Check cache if enabled
        if (config('slick-forms.dynamic_options.cache_enabled', true)) {
            $cached = $this->getCachedOptions($field, $cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        // Load options from source
        try {
            $options = match ($source) {
                'url' => $this->fetchFromUrl(
                    $fieldOptions['source_url'] ?? '',
                    $fieldOptions['headers'] ?? [],
                    $fieldOptions['value_key'] ?? 'value',
                    $fieldOptions['label_key'] ?? 'label',
                    $parentValue
                ),
                'model' => (function () use ($fieldOptions, $parentValue) {
                    $where = $fieldOptions['where'] ?? $fieldOptions['where_conditions'] ?? null;

                    // Decode JSON string if needed
                    if (is_string($where)) {
                        $where = json_decode($where, true);
                    }

                    return $this->fetchFromModel(
                        $fieldOptions['model_class'] ?? '',
                        $fieldOptions['value_column'] ?? 'id',
                        $fieldOptions['label_column'] ?? 'name',
                        $fieldOptions['scope'] ?? null,
                        $where,
                        $parentValue
                    );
                })(),
                default => []
            };

            // Cache the results
            if (config('slick-forms.dynamic_options.cache_enabled', true)) {
                $ttl = config('slick-forms.dynamic_options.cache_ttl', 300);
                $this->cacheOptions($field, $cacheKey, $options, $ttl);
            }

            // Dispatch success event
            event(new DynamicOptionsLoaded($field, $options));

            return $options;
        } catch (\Exception $e) {
            // Dispatch failure event
            event(new DynamicOptionsFailed($field, $e));

            // Return empty array on failure
            return [];
        }
    }

    /**
     * Fetch options from remote URL endpoint
     *
     * @param  string  $url  API endpoint URL
     * @param  array  $headers  HTTP headers to include in request
     * @param  string  $valueKey  JSON path to value field
     * @param  string  $labelKey  JSON path to label field
     * @param  string|null  $parentValue  Parent value for cascading
     * @return array Options array
     */
    public function fetchFromUrl(
        string $url,
        array $headers,
        string $valueKey,
        string $labelKey,
        ?string $parentValue = null
    ): array {
        // Replace placeholder in URL if parent value provided
        if ($parentValue !== null) {
            $url = str_replace('{parent}', urlencode($parentValue), $url);
        }

        $timeout = config('slick-forms.dynamic_options.timeout', 10);

        // Make HTTP request
        $response = Http::timeout($timeout)
            ->withHeaders($headers)
            ->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException("Failed to fetch options from URL: {$url}");
        }

        $data = $response->json();

        // If data is not an array, wrap it
        if (! is_array($data)) {
            return [];
        }

        // Extract options
        $options = [];

        // Always try to extract items from common API response patterns
        // This handles both direct arrays and wrapped responses like {data: [...]}
        $items = $this->extractItemsFromResponse($data);

        // Normalize to simple array if needed
        if (! is_array($items) || ! isset($items[0])) {
            $items = is_array($items) ? [$items] : [];
        }

        foreach ($items as $item) {
            $value = $this->resolveJsonPath($item, $valueKey);
            $label = $this->resolveJsonPath($item, $labelKey);

            if ($value !== null && $label !== null) {
                $options[] = [
                    'value' => $value,
                    'label' => $label,
                ];
            }
        }

        return $options;
    }

    /**
     * Fetch options from Eloquent model
     *
     * @param  string  $modelClass  Fully qualified model class name
     * @param  string  $valueColumn  Database column for option value
     * @param  string  $labelColumn  Database column for option label
     * @param  string|null  $scope  Optional query scope to apply
     * @param  array|null  $where  Optional where conditions
     * @param  string|null  $parentValue  Parent value for cascading
     * @return array Options array
     */
    public function fetchFromModel(
        string $modelClass,
        string $valueColumn,
        string $labelColumn,
        ?string $scope = null,
        ?array $where = null,
        ?string $parentValue = null
    ): array {
        // Validate model class exists
        if (! class_exists($modelClass)) {
            throw new \RuntimeException("Model class not found: {$modelClass}");
        }

        // Build query
        $query = $modelClass::query();

        // Apply scope if provided
        if ($scope !== null && method_exists($modelClass, 'scope'.ucfirst($scope))) {
            $query->{$scope}();
        }

        // Apply where conditions
        if ($where !== null && is_array($where)) {
            foreach ($where as $column => $value) {
                // Skip special keys like 'parent_column'
                if ($column === 'parent_column') {
                    continue;
                }
                $query->where($column, $value);
            }
        }

        // Apply parent value filter if cascading
        if ($parentValue !== null && isset($where['parent_column'])) {
            $query->where($where['parent_column'], $parentValue);
        }

        // Fetch records
        $records = $query->get([$valueColumn, $labelColumn]);

        // Transform to options array
        return $records->map(function ($record) use ($valueColumn, $labelColumn) {
            return [
                'value' => $record->{$valueColumn},
                'label' => $record->{$labelColumn},
            ];
        })->toArray();
    }

    /**
     * Get cached options if available
     *
     * @param  CustomFormField  $field  Field to get cache for
     * @param  string  $cacheKey  Unique cache key
     * @return array|null Cached options or null if not found/expired
     */
    public function getCachedOptions(CustomFormField $field, string $cacheKey): ?array
    {
        $cache = DynamicOptionsCache::where('field_id', $field->id)
            ->where('cache_key', $cacheKey)
            ->first();

        if ($cache === null) {
            return null;
        }

        // Check if expired
        if ($cache->isExpired()) {
            $cache->delete();

            return null;
        }

        return $cache->options;
    }

    /**
     * Cache options with TTL
     *
     * @param  CustomFormField  $field  Field to cache options for
     * @param  string  $cacheKey  Unique cache key
     * @param  array  $options  Options to cache
     * @param  int  $ttl  Time to live in seconds
     */
    public function cacheOptions(CustomFormField $field, string $cacheKey, array $options, int $ttl): void
    {
        DynamicOptionsCache::updateOrCreate(
            [
                'field_id' => $field->id,
                'cache_key' => $cacheKey,
            ],
            [
                'options' => $options,
                'cached_at' => now(),
                'ttl_seconds' => $ttl,
            ]
        );
    }

    /**
     * Invalidate cached options for a field
     *
     * @param  CustomFormField  $field  Field to invalidate cache for
     */
    public function invalidateCache(CustomFormField $field): void
    {
        DynamicOptionsCache::where('field_id', $field->id)->delete();
    }

    /**
     * Resolve JSON path in nested array
     *
     * @param  array  $data  Source data array
     * @param  string  $path  Dot-notation path (e.g., 'data.users.0.name')
     * @return mixed Resolved value
     */
    public function resolveJsonPath(array|object $data, string $path): mixed
    {
        // Handle empty path - return original data
        if (empty($path)) {
            return $data;
        }

        $keys = explode('.', $path);

        foreach ($keys as $key) {
            // Skip empty keys (from paths like 'a..b')
            if (empty($key)) {
                continue;
            }

            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } elseif (is_object($data) && property_exists($data, $key)) {
                $data = $data->{$key};
            } else {
                return null;
            }
        }

        return $data;
    }

    /**
     * Build unique cache key for field and parent value
     */
    protected function buildCacheKey(CustomFormField $field, ?string $parentValue = null): string
    {
        $fieldOptions = $field->options ?? [];

        $parts = [
            $field->id,
            $fieldOptions['dynamic_source'] ?? 'static',
            $fieldOptions['source_url'] ?? '',
            $fieldOptions['model_class'] ?? '',
            $parentValue ?? '',
        ];

        return md5(implode('|', $parts));
    }

    /**
     * Extract items array from API response
     *
     * Handles common API response patterns:
     * - Direct array: [{}, {}]
     * - Wrapped in 'data': {data: [{}, {}]}
     * - Nested wrapping: {data: {items: [{}, {}]}}
     * - Paginated: {data: [{}, {}], meta: {...}}
     */
    protected function extractItemsFromResponse(array $data): array
    {
        // Direct array
        if (isset($data[0])) {
            return $data;
        }

        // Try common wrapper keys in order
        $wrapperKeys = ['items', 'results', 'data'];

        foreach ($wrapperKeys as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                // Recursively extract if nested
                return $this->extractItemsFromResponse($data[$key]);
            }
        }

        // Single item object
        return [$data];
    }
}
