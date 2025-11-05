<?php

namespace DigitalisStudios\SlickForms;

use DigitalisStudios\SlickForms\Http\Middleware\CheckFormSchedule;
use DigitalisStudios\SlickForms\Http\Middleware\CheckIpRestrictions;
use DigitalisStudios\SlickForms\Http\Middleware\CheckSubmissionLimits;
use DigitalisStudios\SlickForms\Http\Middleware\VerifyFormPassword;
use DigitalisStudios\SlickForms\Livewire\EmailLogsViewer;
use DigitalisStudios\SlickForms\Livewire\FormAnalytics;
use DigitalisStudios\SlickForms\Livewire\FormBuilder;
use DigitalisStudios\SlickForms\Livewire\FormRenderer;
use DigitalisStudios\SlickForms\Livewire\FormTemplates;
use DigitalisStudios\SlickForms\Livewire\Manage;
use DigitalisStudios\SlickForms\Livewire\ManageStats;
use DigitalisStudios\SlickForms\Livewire\SpamLogsViewer;
use DigitalisStudios\SlickForms\Livewire\SubmissionViewer;
use DigitalisStudios\SlickForms\Services\CarouselPresetService;
use DigitalisStudios\SlickForms\Services\DynamicOptionsService;
use DigitalisStudios\SlickForms\Services\EmailNotificationService;
use DigitalisStudios\SlickForms\Services\FieldTypeRegistry;
use DigitalisStudios\SlickForms\Services\FormLayoutService;
use DigitalisStudios\SlickForms\Services\FormulaEvaluator;
use DigitalisStudios\SlickForms\Services\FormVersionService;
use DigitalisStudios\SlickForms\Services\LayoutElementRegistry;
use DigitalisStudios\SlickForms\Services\ModelBindingService;
use DigitalisStudios\SlickForms\Services\SchemaRenderer;
use DigitalisStudios\SlickForms\Services\SpamProtectionService;
use DigitalisStudios\SlickForms\Services\TabRegistry;
use DigitalisStudios\SlickForms\Services\UrlObfuscationService;
use DigitalisStudios\SlickForms\Services\WebhookService;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class SlickFormsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/slick-forms.php',
            'slick-forms'
        );

        $this->app->singleton(FieldTypeRegistry::class, function ($app) {
            return new FieldTypeRegistry;
        });

        $this->app->singleton(FormLayoutService::class, function ($app) {
            return new FormLayoutService;
        });

        $this->app->singleton(FormulaEvaluator::class, function ($app) {
            return new FormulaEvaluator;
        });

        $this->app->singleton(SchemaRenderer::class, function ($app) {
            return new SchemaRenderer;
        });

        $this->app->singleton(TabRegistry::class, function ($app) {
            return new TabRegistry;
        });

        $this->app->singleton(LayoutElementRegistry::class, function ($app) {
            return new LayoutElementRegistry;
        });

        // Core services (always available)
        $this->app->singleton(SpamProtectionService::class, function ($app) {
            return new SpamProtectionService;
        });

        $this->app->singleton(DynamicOptionsService::class, function ($app) {
            return new DynamicOptionsService;
        });

        $this->app->singleton(ModelBindingService::class, function ($app) {
            return new ModelBindingService;
        });

        $this->app->singleton(UrlObfuscationService::class, function ($app) {
            return new UrlObfuscationService;
        });

        $this->app->singleton(CarouselPresetService::class, function ($app) {
            return new CarouselPresetService;
        });

        // Feature-specific services (conditional registration)
        if (config('slick-forms.features.email_notifications', true)) {
            $this->app->singleton(EmailNotificationService::class, function ($app) {
                return new EmailNotificationService;
            });
        }

        if (config('slick-forms.features.webhooks', true)) {
            $this->app->singleton(WebhookService::class, function ($app) {
                return new WebhookService;
            });
        }

        if (config('slick-forms.features.versioning', true)) {
            $this->app->singleton(FormVersionService::class, function ($app) {
                return new FormVersionService;
            });
        }
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->loadViewsFrom(__DIR__.'/resources/views', 'slick-forms');

        if (config('slick-forms.load_routes', true)) {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        }

        // Auto-publish assets on package installation (required for form builder UI)
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/resources/css' => public_path('vendor/slick-forms/css'),
                __DIR__.'/resources/js' => public_path('vendor/slick-forms/js'),
            ], ['slick-forms-assets', 'laravel-assets']);
        }

        // Optional publishable resources
        $this->publishes([
            __DIR__.'/config/slick-forms.php' => config_path('slick-forms.php'),
        ], 'slick-forms-config');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/slick-forms'),
        ], 'slick-forms-views');

        $this->registerFieldTypes();
        $this->registerLayoutElementTypes();
        $this->registerMiddleware();
        $this->registerRouteBindings();
        $this->registerCommands();

        if ($this->app->bound('livewire')) {
            // Core components (always available)
            Livewire::component('slick-forms::form-builder', FormBuilder::class);
            Livewire::component('slick-forms::form-renderer', FormRenderer::class);
            Livewire::component('slick-forms::submission-viewer', SubmissionViewer::class);
            Livewire::component('slick-forms::manage', Manage::class);
            Livewire::component('slick-forms::manage-stats', ManageStats::class);
            Livewire::component('slick-forms::form-templates', FormTemplates::class);

            // Feature-specific components (conditional registration)
            if (config('slick-forms.features.analytics', true)) {
                Livewire::component('slick-forms::form-analytics', FormAnalytics::class);
            }

            if (config('slick-forms.features.email_notifications', true)) {
                Livewire::component('slick-forms::email-logs-viewer', EmailLogsViewer::class);
            }

            if (config('slick-forms.features.spam_logs', true)) {
                Livewire::component('slick-forms::spam-logs-viewer', SpamLogsViewer::class);
            }

            if (config('slick-forms.features.webhooks', true)) {
                Livewire::component('slick-forms::webhook-logs-viewer', \DigitalisStudios\SlickForms\Livewire\WebhookLogsViewer::class);
            }
        }
    }

    protected function registerFieldTypes(): void
    {
        $registry = $this->app->make(FieldTypeRegistry::class);

        foreach (config('slick-forms.field_types', []) as $name => $class) {
            $registry->register($name, $class);
        }
    }

    protected function registerLayoutElementTypes(): void
    {
        $registry = $this->app->make(LayoutElementRegistry::class);

        foreach (config('slick-forms.layout_element_types', []) as $type => $class) {
            $registry->register($type, $class);
        }
    }

    /**
     * Register V2 middleware aliases
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('slick-forms.password', VerifyFormPassword::class);
        $router->aliasMiddleware('slick-forms.schedule', CheckFormSchedule::class);
        $router->aliasMiddleware('slick-forms.limits', CheckSubmissionLimits::class);
        $router->aliasMiddleware('slick-forms.ip', CheckIpRestrictions::class);
    }

    /**
     * Register hashid route model bindings
     */
    protected function registerRouteBindings(): void
    {
        $router = $this->app->make(Router::class);

        // Bind {form} parameter to hashid resolution for all admin routes
        $router->bind('form', function ($value) {
            $urlService = $this->app->make(UrlObfuscationService::class);
            $id = $urlService->decodeId($value);

            if (! $id) {
                abort(404, 'Invalid form identifier.');
            }

            return \DigitalisStudios\SlickForms\Models\CustomForm::findOrFail($id);
        });

        // Bind {template} parameter to hashid resolution for template routes
        $router->bind('template', function ($value) {
            $urlService = $this->app->make(UrlObfuscationService::class);
            $id = $urlService->decodeId($value);

            if (! $id) {
                abort(404, 'Invalid template identifier.');
            }

            return \DigitalisStudios\SlickForms\Models\CustomForm::where('is_template', true)->findOrFail($id);
        });
    }

    /**
     * Register Artisan commands
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \DigitalisStudios\SlickForms\Console\Commands\ExportSchemasCommand::class,
                \DigitalisStudios\SlickForms\Console\Commands\InstallSlickFormsCommand::class,
            ]);
        }
    }
}
