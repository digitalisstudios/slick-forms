<?php

if (! function_exists('slick_forms_has_feature')) {
    /**
     * Check if a Slick Forms feature is enabled
     *
     * @param  string  $feature  The feature name (analytics, webhooks, spam_logs, etc.)
     */
    function slick_forms_has_feature(string $feature): bool
    {
        return config("slick-forms.features.{$feature}", false);
    }
}
