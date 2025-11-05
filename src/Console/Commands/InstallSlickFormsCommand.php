<?php

namespace DigitalisStudios\SlickForms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstallSlickFormsCommand extends Command
{
    protected $signature = 'slick-forms:install {--force : Force installation}';

    protected $description = 'Install Slick Forms with interactive feature selection';

    protected array $featureMigrations = [
        'analytics' => [
            'path' => 'features/analytics/2024_06_01_000001_create_slick_forms_analytics_tables.php',
            'description' => 'Form analytics, tracking, and metrics',
        ],
        'webhooks' => [
            'path' => 'features/webhooks/2024_06_01_000002_create_slick_forms_webhook_tables.php',
            'description' => 'Webhook integrations and delivery logs',
        ],
        'spam_logs' => [
            'path' => 'features/spam/2024_06_01_000003_create_slick_forms_spam_tables.php',
            'description' => 'Spam protection and logging',
        ],
        'email_notifications' => [
            'path' => 'features/email/2024_06_01_000004_create_slick_forms_email_tables.php',
            'description' => 'Email templates and notification logs',
        ],
        'versioning' => [
            'path' => 'features/versioning/2024_06_01_000005_create_slick_forms_version_tables.php',
            'description' => 'Form version history and restoration',
        ],
    ];

    public function handle(): int
    {
        $this->info('Slick Forms Installation');
        $this->warn('Interactive wizard not yet implemented.');
        $this->newLine();

        // For now, just show what would be installed
        $this->info('Core migration: 2024_01_01_000000_create_slick_forms_core_tables.php');
        $this->info('Feature migrations available:');
        foreach ($this->featureMigrations as $feature => $config) {
            $this->line("  - {$feature}: {$config['description']}");
        }

        $this->newLine();
        $this->comment('Full installation wizard coming in Phase 7...');

        return self::SUCCESS;
    }

    /**
     * Get the migration file path for a feature
     */
    protected function getFeatureMigrationPath(string $feature): string
    {
        $config = $this->featureMigrations[$feature] ?? null;

        if (! $config) {
            throw new \InvalidArgumentException("Unknown feature: {$feature}");
        }

        return 'packages/digitalisstudios/slick-forms/src/database/migrations/'.$config['path'];
    }

    /**
     * Track feature as enabled BEFORE running migration
     */
    protected function enableFeature(string $feature): void
    {
        // Check if feature tracking table exists
        if (! Schema::hasTable('slick_form_features')) {
            $this->warn('Feature tracking table does not exist. Run core migration first.');

            return;
        }

        $config = $this->featureMigrations[$feature] ?? null;
        if (! $config) {
            return;
        }

        // Insert or update feature record
        DB::table('slick_form_features')->updateOrInsert(
            ['feature_name' => $feature],
            [
                'enabled' => true,
                'enabled_at' => now(),
                'migration_path' => $config['path'],
                'updated_at' => now(),
            ]
        );

        $this->info("✓ Feature '{$feature}' enabled");
    }

    /**
     * Mark feature as installed AFTER migration succeeds
     */
    protected function markFeatureInstalled(string $feature): void
    {
        if (! Schema::hasTable('slick_form_features')) {
            return;
        }

        DB::table('slick_form_features')
            ->where('feature_name', $feature)
            ->update([
                'installed' => true,
                'installed_at' => now(),
                'updated_at' => now(),
            ]);

        $this->info("✓ Feature '{$feature}' installed successfully");
    }

    /**
     * Disable feature if migration fails
     */
    protected function disableFeatureOnFailure(string $feature, \Exception $e): void
    {
        if (! Schema::hasTable('slick_form_features')) {
            return;
        }

        DB::table('slick_form_features')
            ->where('feature_name', $feature)
            ->update([
                'enabled' => false,
                'installed' => false,
                'enabled_at' => null,
                'installed_at' => null,
                'updated_at' => now(),
            ]);

        $this->error("✗ Feature '{$feature}' installation failed: {$e->getMessage()}");
    }

    /**
     * Run migrations for selected features with tracking
     */
    protected function runMigrations(array $selectedFeatures): void
    {
        // Run core migration always
        $this->info('Installing core tables...');
        $this->call('migrate', [
            '--path' => 'packages/digitalisstudios/slick-forms/src/database/migrations/2024_01_01_000000_create_slick_forms_core_tables.php',
            '--force' => true,
        ]);

        // Run feature migrations with tracking
        foreach ($selectedFeatures as $feature => $enabled) {
            if ($enabled) {
                try {
                    // 1. Enable feature BEFORE migration
                    $this->enableFeature($feature);

                    // 2. Run migration
                    $migrationPath = $this->getFeatureMigrationPath($feature);
                    $this->info("Installing {$feature} feature...");
                    $this->call('migrate', [
                        '--path' => $migrationPath,
                        '--force' => true,
                    ]);

                    // 3. Mark as installed AFTER success
                    $this->markFeatureInstalled($feature);
                } catch (\Exception $e) {
                    // 4. Handle failure gracefully
                    $this->disableFeatureOnFailure($feature, $e);
                }
            }
        }

        // Clear feature cache after installation
        if (function_exists('slick_forms_clear_cache')) {
            slick_forms_clear_cache();
        }
    }
}
