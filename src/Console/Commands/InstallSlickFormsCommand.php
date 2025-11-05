<?php

namespace DigitalisStudios\SlickForms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\warning;

class InstallSlickFormsCommand extends Command
{
    protected $signature = 'slick-forms:install {--force : Force installation}';

    protected $description = 'Install Slick Forms with interactive feature selection';

    protected array $featureMigrations = [
        'analytics' => [
            'path' => 'features/analytics/2024_06_01_000001_create_slick_forms_analytics_tables.php',
            'description' => 'Form analytics, tracking, and metrics',
            'tables' => 2,
        ],
        'webhooks' => [
            'path' => 'features/webhooks/2024_06_01_000002_create_slick_forms_webhook_tables.php',
            'description' => 'Webhook integrations and delivery logs',
            'tables' => 2,
        ],
        'spam_logs' => [
            'path' => 'features/spam/2024_06_01_000003_create_slick_forms_spam_tables.php',
            'description' => 'Spam protection and logging',
            'tables' => 1,
        ],
        'email_notifications' => [
            'path' => 'features/email/2024_06_01_000004_create_slick_forms_email_tables.php',
            'description' => 'Email templates and notification logs',
            'tables' => 2,
        ],
        'versioning' => [
            'path' => 'features/versioning/2024_06_01_000005_create_slick_forms_version_tables.php',
            'description' => 'Form version history and restoration',
            'tables' => 1,
        ],
    ];

    protected array $featureDependencies = [
        'exports' => [
            'packages' => [
                'maatwebsite/excel' => '^3.1',
                'barryvdh/laravel-dompdf' => '^3.0',
            ],
            'description' => 'Export submissions to CSV, Excel, and PDF',
        ],
        'qr_codes' => [
            'packages' => [
                'simplesoftwareio/simple-qrcode' => '^4.2',
            ],
            'description' => 'Generate QR codes for form URLs',
        ],
    ];

    public function handle(): int
    {
        $this->newLine();
        info('🚀 Slick Forms Installation Wizard');
        $this->newLine();

        // Step 1: Feature Selection
        $selectedFeatures = $this->selectFeatures();

        // Step 2: Dependency Check & Installation
        $this->handleDependencyInstallation($selectedFeatures);

        // Step 3: Publish Config (if not exists)
        $this->publishConfig();

        // Step 4: Run Migrations
        $this->runMigrations($selectedFeatures);

        // Step 5: Update Config with Selected Features
        $this->updateConfig($selectedFeatures);

        // Step 6: Display Summary
        $this->displaySummary($selectedFeatures);

        return self::SUCCESS;
    }

    /**
     * Sub-Phase 7.1: Interactive feature selection
     */
    protected function selectFeatures(): array
    {
        info('Select the features you want to install:');
        $this->newLine();

        // Build multiselect options
        $options = [];
        foreach ($this->featureMigrations as $feature => $config) {
            $options[$feature] = "{$feature} - {$config['description']}";
        }

        // Add dependency-based features
        foreach ($this->featureDependencies as $feature => $config) {
            $options[$feature] = "{$feature} - {$config['description']}";
        }

        // Get already enabled features to pre-select them
        $enabledFeatures = $this->getEnabledFeatures();

        // Interactive multiselect
        $selected = multiselect(
            label: 'Features (use space to select, enter to confirm):',
            options: $options,
            default: $enabledFeatures,
            hint: 'Core tables will always be installed. Select additional features.'
        );

        // Convert to associative array
        $selectedFeatures = [];
        foreach (array_merge(array_keys($this->featureMigrations), array_keys($this->featureDependencies)) as $feature) {
            $selectedFeatures[$feature] = in_array($feature, $selected);
        }

        return $selectedFeatures;
    }

    /**
     * Get features that are already enabled in the database
     */
    protected function getEnabledFeatures(): array
    {
        // Check if feature tracking table exists
        if (! Schema::hasTable('slick_form_features')) {
            return [];
        }

        // Get features that are currently enabled
        $enabled = DB::table('slick_form_features')
            ->where('enabled', true)
            ->pluck('feature_name')
            ->toArray();

        return $enabled;
    }

    /**
     * Sub-Phase 7.2: Check and install missing dependencies
     */
    protected function handleDependencyInstallation(array $selectedFeatures): void
    {
        $missingDependencies = $this->checkDependencies($selectedFeatures);

        if (empty($missingDependencies)) {
            return;
        }

        $this->newLine();
        warning('Some features require additional Composer packages:');
        $this->newLine();

        foreach ($missingDependencies as $feature => $packages) {
            $this->line("  <comment>{$feature}</comment> requires:");
            foreach ($packages as $package => $version) {
                $this->line("    - {$package}: {$version}");
            }
        }

        $this->newLine();

        if (confirm('Would you like to install these dependencies automatically?', default: true)) {
            $this->installDependencies($missingDependencies);
        } else {
            warning('Installation will continue, but features requiring missing packages will not work.');
            warning('You can install them manually later with: composer require <package>');
        }
    }

    /**
     * Check for missing Composer dependencies
     */
    protected function checkDependencies(array $selectedFeatures): array
    {
        $missing = [];

        foreach ($selectedFeatures as $feature => $enabled) {
            if (! $enabled || ! isset($this->featureDependencies[$feature])) {
                continue;
            }

            $packages = $this->featureDependencies[$feature]['packages'];

            foreach ($packages as $package => $version) {
                // Check if package exists in vendor directory
                $packagePath = base_path('vendor/'.str_replace('/', DIRECTORY_SEPARATOR, $package));

                if (! File::exists($packagePath)) {
                    if (! isset($missing[$feature])) {
                        $missing[$feature] = [];
                    }
                    $missing[$feature][$package] = $version;
                }
            }
        }

        return $missing;
    }

    /**
     * Install missing dependencies via Composer
     */
    protected function installDependencies(array $missingDependencies): void
    {
        $this->newLine();
        info('Installing dependencies...');

        $packages = [];
        foreach ($missingDependencies as $feature => $deps) {
            foreach ($deps as $package => $version) {
                $packages[] = "{$package}:{$version}";
            }
        }

        $command = 'composer require '.implode(' ', $packages);

        $this->line("  Running: <comment>{$command}</comment>");
        $this->newLine();

        exec($command, $output, $exitCode);

        if ($exitCode === 0) {
            info('✓ Dependencies installed successfully');
        } else {
            warning('⚠ Some dependencies failed to install. Check output above.');
        }
    }

    /**
     * Publish config file if it doesn't exist
     */
    protected function publishConfig(): void
    {
        $configPath = config_path('slick-forms.php');

        if (! File::exists($configPath)) {
            info('Publishing config file...');
            $this->call('vendor:publish', [
                '--tag' => 'slick-forms-config',
                '--force' => $this->option('force'),
            ]);
        }
    }

    /**
     * Sub-Phase 7.3: Update config file with selected features
     */
    protected function updateConfig(array $selectedFeatures): void
    {
        $configPath = config_path('slick-forms.php');

        if (! File::exists($configPath)) {
            warning('Config file not found. Run: php artisan vendor:publish --tag=slick-forms-config');

            return;
        }

        info('Updating config file with selected features...');

        $config = File::get($configPath);

        // Build new features array string
        $featuresArray = "[\n";
        foreach ($selectedFeatures as $feature => $enabled) {
            $status = $enabled ? 'true' : 'false';
            $featuresArray .= "        '{$feature}' => {$status},\n";
        }
        $featuresArray .= '    ]';

        // Replace features array in config file
        $pattern = "/'features'\s*=>\s*\[.*?\]/s";
        $replacement = "'features' => {$featuresArray}";
        $config = preg_replace($pattern, $replacement, $config);

        File::put($configPath, $config);

        info('✓ Config file updated');
    }

    /**
     * Sub-Phase 7.4: Display installation summary
     */
    protected function displaySummary(array $selectedFeatures): void
    {
        $this->newLine();
        $this->newLine();
        info('🎉 Slick Forms installed successfully!');
        $this->newLine();

        // Count enabled features and tables
        $enabledMigrationFeatures = array_filter(array_intersect_key($selectedFeatures, $this->featureMigrations));
        $enabledDependencyFeatures = array_filter(array_intersect_key($selectedFeatures, $this->featureDependencies));

        $totalTables = 9; // Core tables including slick_form_features
        foreach ($enabledMigrationFeatures as $feature => $enabled) {
            if ($enabled) {
                $totalTables += $this->featureMigrations[$feature]['tables'];
            }
        }

        // Display installed features
        $this->line('<comment>Installed Features:</comment>');
        $this->line('  Core (9 tables including feature tracking)');

        foreach ($this->featureMigrations as $feature => $config) {
            if ($selectedFeatures[$feature]) {
                $this->line("  ✓ {$feature} ({$config['tables']} tables)");
            }
        }

        foreach ($this->featureDependencies as $feature => $config) {
            if ($selectedFeatures[$feature]) {
                $this->line("  ✓ {$feature} (Composer packages)");
            }
        }

        $this->newLine();
        $this->line("<info>Total database tables:</info> {$totalTables}");
        $this->newLine();

        // Next steps
        $this->line('<comment>Next Steps:</comment>');
        $this->line('  1. Visit /slick-forms/manage to create your first form');
        $this->line('  2. Use the drag-and-drop builder at /slick-forms/builder/{form}');
        $this->line('  3. Share your form at /slick-forms/form/{form}');
        $this->line('  4. View submissions at /slick-forms/submissions/{form}');

        if ($selectedFeatures['analytics'] ?? false) {
            $this->line('  5. Check analytics at /slick-forms/analytics/{form}');
        }

        $this->newLine();
        $this->line('📖 Documentation: https://github.com/digitalisstudios/slick-forms');
        $this->newLine();
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
        $this->newLine();
        info('Installing database tables...');
        $this->newLine();

        // Run core migration always
        $this->line('  Installing core tables (9 tables)...');
        $this->call('migrate', [
            '--path' => 'packages/digitalisstudios/slick-forms/src/database/migrations/2024_01_01_000000_create_slick_forms_core_tables.php',
            '--force' => true,
        ]);
        $this->line('  ✓ Core tables installed');
        $this->newLine();

        // Run feature migrations with tracking (only those with migrations)
        foreach ($selectedFeatures as $feature => $enabled) {
            if ($enabled && isset($this->featureMigrations[$feature])) {
                try {
                    // 1. Enable feature BEFORE migration
                    $this->enableFeature($feature);

                    // 2. Run migration
                    $migrationPath = $this->getFeatureMigrationPath($feature);
                    $config = $this->featureMigrations[$feature];
                    $this->line("  Installing {$feature} ({$config['tables']} tables)...");
                    $this->call('migrate', [
                        '--path' => $migrationPath,
                        '--force' => true,
                    ]);

                    // 3. Mark as installed AFTER success
                    $this->markFeatureInstalled($feature);
                    $this->line("  ✓ {$feature} installed");
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
