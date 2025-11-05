<?php

use DigitalisStudios\SlickForms\Http\Controllers\FormAnalyticsController;
use DigitalisStudios\SlickForms\Http\Controllers\FormBuilderController;
use DigitalisStudios\SlickForms\Http\Controllers\FormRendererController;
use DigitalisStudios\SlickForms\Http\Controllers\SubmissionExportController;
use DigitalisStudios\SlickForms\Http\Controllers\SubmissionViewerController;
use DigitalisStudios\SlickForms\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

if (! function_exists('resolveRouteUri')) {
    /**
     * Helper function to resolve route URI from config
     * Substitutes {segment_name} placeholders with actual segment values
     */
    function resolveRouteUri(string $uri): string
    {
        $segments = config('slick-forms.routes.segments', []);

        return preg_replace_callback('/\{([^}]+)\}/', function ($matches) use ($segments) {
            $key = $matches[1];
            // Don't replace route parameters (form, hash, data, template)
            if (in_array($key, ['form', 'hash', 'data', 'template'])) {
                return $matches[0];
            }

            return $segments[$key] ?? $matches[0];
        }, $uri);
    }
}

if (! function_exists('resolveMiddleware')) {
    /**
     * Helper function to resolve middleware from config
     * Converts middleware key references to actual middleware arrays
     *
     * @param  string|array  $middleware
     */
    function resolveMiddleware($middleware): array
    {
        if (is_array($middleware)) {
            return $middleware;
        }

        $middlewareGroups = config('slick-forms.routes.middleware', []);

        return $middlewareGroups[$middleware] ?? [$middleware];
    }
}

if (! function_exists('getRouteConfig')) {
    /**
     * Helper function to get route config (handles both string and array formats)
     * Returns array with 'uri' and 'middleware' keys
     *
     * @param  string  $section  Route section (e.g., 'builder', 'form', 'manage')
     * @param  string  $key  Route key (e.g., 'show', 'create', 'export_csv')
     */
    function getRouteConfig(string $section, string $key): array
    {
        $config = config("slick-forms.routes.{$section}.{$key}");

        if (is_string($config)) {
            // String format: uses group middleware
            return [
                'uri' => resolveRouteUri($config),
                'middleware' => resolveMiddleware(config("slick-forms.routes.{$section}.middleware")),
            ];
        }

        // Array format: custom override
        return [
            'uri' => resolveRouteUri($config['uri'] ?? ''),
            'middleware' => resolveMiddleware($config['middleware'] ?? []),
        ];
    }
}

Route::prefix(config('slick-forms.routes.prefix'))
    ->name(config('slick-forms.routes.name_prefix'))
    ->group(function () {

        // Form Builder Routes
        $config = getRouteConfig('builder', 'show');
        Route::get($config['uri'], [FormBuilderController::class, 'show'])
            ->middleware($config['middleware'])
            ->name('builder.show');

        // Form Display Routes (Public - Hashid only)
        $config = getRouteConfig('form', 'show');
        Route::get($config['uri'], [FormRendererController::class, 'showByHash'])
            ->middleware($config['middleware'])
            ->where('hash', '[a-zA-Z0-9]{6,}')
            ->name('form.show.hash');

        $config = getRouteConfig('form', 'show_prefilled');
        Route::get($config['uri'], [FormRendererController::class, 'showPrefilled'])
            ->middleware($config['middleware'])
            ->name('form.show.prefilled');

        // Submission Viewer Routes
        $config = getRouteConfig('submissions', 'show');
        Route::get($config['uri'], [SubmissionViewerController::class, 'show'])
            ->middleware($config['middleware'])
            ->name('submissions.show');

        // Submission Export Routes (Only if exports feature enabled)
        if (config('slick-forms.features.exports', true)) {
            $config = getRouteConfig('submissions', 'export_csv');
            Route::get($config['uri'], [SubmissionExportController::class, 'csv'])
                ->middleware($config['middleware'])
                ->name('submissions.export.csv');

            $config = getRouteConfig('submissions', 'export_excel');
            Route::get($config['uri'], [SubmissionExportController::class, 'excel'])
                ->middleware($config['middleware'])
                ->name('submissions.export.excel');

            $config = getRouteConfig('submissions', 'export_pdf');
            Route::get($config['uri'], [SubmissionExportController::class, 'pdf'])
                ->middleware($config['middleware'])
                ->name('submissions.export.pdf');
        }

        // Analytics Routes (Only if analytics feature enabled)
        if (config('slick-forms.features.analytics', true)) {
            $config = getRouteConfig('analytics', 'show');
            Route::get($config['uri'], [FormAnalyticsController::class, 'show'])
                ->middleware($config['middleware'])
                ->name('analytics.show');
        }

        // Form Management Routes (CRUD)
        $config = getRouteConfig('manage', 'index');
        Route::get($config['uri'], [FormBuilderController::class, 'index'])
            ->middleware($config['middleware'])
            ->name('manage.index');

        $config = getRouteConfig('manage', 'create');
        Route::get($config['uri'], [FormBuilderController::class, 'create'])
            ->middleware($config['middleware'])
            ->name('manage.create');

        $config = getRouteConfig('manage', 'store');
        Route::post($config['uri'], [FormBuilderController::class, 'store'])
            ->middleware($config['middleware'])
            ->name('manage.store');

        $config = getRouteConfig('manage', 'edit');
        Route::get($config['uri'], [FormBuilderController::class, 'edit'])
            ->middleware($config['middleware'])
            ->name('manage.edit');

        $config = getRouteConfig('manage', 'update');
        Route::put($config['uri'], [FormBuilderController::class, 'update'])
            ->middleware($config['middleware'])
            ->name('manage.update');

        $config = getRouteConfig('manage', 'destroy');
        Route::delete($config['uri'], [FormBuilderController::class, 'destroy'])
            ->middleware($config['middleware'])
            ->name('manage.destroy');

        // Form Operations Routes
        $config = getRouteConfig('forms', 'duplicate');
        Route::post($config['uri'], [FormBuilderController::class, 'duplicate'])
            ->middleware($config['middleware'])
            ->name('forms.duplicate');

        $config = getRouteConfig('forms', 'toggle_active');
        Route::post($config['uri'], [FormBuilderController::class, 'toggleActive'])
            ->middleware($config['middleware'])
            ->name('forms.toggle-active');

        // Template Routes
        $config = getRouteConfig('templates', 'use');
        Route::post($config['uri'], [TemplateController::class, 'use'])
            ->middleware($config['middleware'])
            ->name('templates.use');

        $config = getRouteConfig('templates', 'save_as_template');
        Route::post($config['uri'], [TemplateController::class, 'saveAsTemplate'])
            ->middleware($config['middleware'])
            ->name('forms.save-as-template');
    });
