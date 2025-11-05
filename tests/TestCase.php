<?php

namespace DigitalisStudios\SlickForms\Tests;

use DigitalisStudios\SlickForms\SlickFormsServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Load helper functions
        require_once __DIR__.'/../src/helpers.php';

        $this->loadMigrationsFrom(__DIR__.'/../src/database/migrations');

        // Create users table for testing (needed for dynamic options service and model binding)
        $this->app['db']->connection()->getSchemaBuilder()->create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // Nullable for model binding tests
            $table->string('remember_token', 100)->nullable();
            $table->json('settings')->nullable(); // For nested attribute tests
            $table->timestamps();
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            \Livewire\LivewireServiceProvider::class,
            SlickFormsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set encryption key for testing
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        // Set SlickForms configuration for testing
        $app['config']->set('slick-forms.urls.hashid_salt', 'test-salt-for-hashids');
        $app['config']->set('slick-forms.urls.hashid_min_length', 6);
        $app['config']->set('slick-forms.urls.signed_url_expiration', 24);
        $app['config']->set('slick-forms.load_routes', true);
        $app['config']->set('slick-forms.layout', 'slick-forms::layouts.standalone'); // Use standalone layout for tests
    }
}
