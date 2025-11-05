<?php

namespace DigitalisStudios\SlickForms\Console\Commands;

use Illuminate\Console\Command;

class InstallSlickFormsCommand extends Command
{
    protected $signature = 'slick-forms:install {--force : Force installation}';

    protected $description = 'Install Slick Forms with interactive feature selection';

    protected array $featureMigrations = [
        'analytics' => '2024_06_01_000001_create_slick_forms_analytics_tables.php',
        'webhooks' => '2024_06_01_000002_create_slick_forms_webhook_tables.php',
        'spam_logs' => '2024_06_01_000003_create_slick_forms_spam_tables.php',
        'email_notifications' => '2024_06_01_000004_create_slick_forms_email_tables.php',
        'versioning' => '2024_06_01_000005_create_slick_forms_version_tables.php',
    ];

    public function handle(): int
    {
        $this->info('Slick Forms Installation');
        $this->warn('Interactive wizard not yet implemented.');
        $this->newLine();

        // For now, just show what would be installed
        $this->info('Core migration: 2024_01_01_000000_create_slick_forms_core_tables.php');
        $this->info('Feature migrations available:');
        foreach ($this->featureMigrations as $feature => $migrationFile) {
            $this->line("  - {$feature}: {$migrationFile}");
        }

        $this->newLine();
        $this->comment('Full installation wizard coming in Phase 7...');

        return self::SUCCESS;
    }

    /**
     * Get the migration file path for a feature
     */
    protected function getFeatureMigrationFile(string $feature): string
    {
        $filename = $this->featureMigrations[$feature] ?? null;

        if (! $filename) {
            throw new \InvalidArgumentException("Unknown feature: {$feature}");
        }

        return 'packages/digitalisstudios/slick-forms/src/database/migrations/'.$filename;
    }

    /**
     * Run migrations for selected features
     */
    protected function runMigrations(array $selectedFeatures): void
    {
        // Run core migration always
        $this->info('Installing core tables...');
        $this->call('migrate', [
            '--path' => 'packages/digitalisstudios/slick-forms/src/database/migrations/2024_01_01_000000_create_slick_forms_core_tables.php',
            '--force' => true,
        ]);

        // Run feature migrations
        foreach ($selectedFeatures as $feature => $enabled) {
            if ($enabled) {
                $migrationFile = $this->getFeatureMigrationFile($feature);
                $this->info("Installing {$feature} feature...");
                $this->call('migrate', [
                    '--path' => $migrationFile,
                    '--force' => true,
                ]);
            }
        }
    }
}
