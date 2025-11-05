<?php

if (! function_exists('slick_forms_feature_enabled')) {
    /**
     * Check if a Slick Forms feature is enabled
     *
     * @param  string  $feature  Feature key (e.g., 'analytics', 'webhooks')
     */
    function slick_forms_feature_enabled(string $feature): bool
    {
        // Check if feature tracking table exists
        if (! \Illuminate\Support\Facades\Schema::hasTable('slick_form_features')) {
            // During migration, assume core features are enabled
            return in_array($feature, ['core']);
        }

        // Check cache first (1 hour TTL)
        $cacheKey = "slick_forms_features.{$feature}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($feature) {
            return \Illuminate\Support\Facades\DB::table('slick_form_features')
                ->where('feature_name', $feature)
                ->where('enabled', true)
                ->exists();
        });
    }
}

if (! function_exists('slick_forms_has_table')) {
    /**
     * Check if a Slick Forms table exists
     *
     * @param  string  $table  Table name (with or without slick_ prefix)
     */
    function slick_forms_has_table(string $table): bool
    {
        // Add slick_ prefix if not present
        if (! str_starts_with($table, 'slick_')) {
            $table = 'slick_'.$table;
        }

        // Check cache first (indefinite - only cleared on migrations)
        $cacheKey = "slick_forms_tables.{$table}";

        return \Illuminate\Support\Facades\Cache::rememberForever($cacheKey, function () use ($table) {
            return \Illuminate\Support\Facades\Schema::hasTable($table);
        });
    }
}

if (! function_exists('slick_forms_clear_cache')) {
    /**
     * Clear Slick Forms feature and table cache
     * Call this after running migrations or changing feature flags
     */
    function slick_forms_clear_cache(): void
    {
        \Illuminate\Support\Facades\Cache::forget('slick_forms_features.*');
        \Illuminate\Support\Facades\Cache::forget('slick_forms_tables.*');
    }
}

// Backward compatibility alias
if (! function_exists('slick_forms_has_feature')) {
    /**
     * Check if a Slick Forms feature is enabled (legacy alias)
     *
     * @param  string  $feature  The feature name (analytics, webhooks, spam_logs, etc.)
     *
     * @deprecated Use slick_forms_feature_enabled() instead
     */
    function slick_forms_has_feature(string $feature): bool
    {
        return slick_forms_feature_enabled($feature);
    }
}
