<?php

namespace DigitalisStudios\SlickForms\Console\Commands;

use DigitalisStudios\SlickForms\Services\FieldTypeRegistry;
use DigitalisStudios\SlickForms\Services\LayoutElementRegistry;
use Illuminate\Console\Command;

class ExportSchemasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slick-forms:export-schemas
                            {--output= : Output directory (default: package docs/schema)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all field type and layout element schemas to JSON files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Slick Forms Schema Export');
        $this->info('=========================');
        $this->newLine();

        // Determine output directory
        $outputDir = $this->option('output') ?? __DIR__.'/../../../docs/schema';
        $fieldsPath = $outputDir.'/fields';
        $elementsPath = $outputDir.'/elements';

        // Create directories if they don't exist
        if (! is_dir($fieldsPath)) {
            mkdir($fieldsPath, 0755, true);
            $this->info("Created directory: {$fieldsPath}");
        }
        if (! is_dir($elementsPath)) {
            mkdir($elementsPath, 0755, true);
            $this->info("Created directory: {$elementsPath}");
        }

        $this->newLine();

        // Export field type schemas
        $this->info('Exporting Field Type Schemas...');
        $this->info('-------------------------------');

        $fieldRegistry = app(FieldTypeRegistry::class);
        $fieldCount = 0;
        $fieldErrors = 0;

        foreach ($fieldRegistry->all() as $fieldName => $fieldType) {
            try {
                $schema = $fieldType->getFullSchema();
                $filename = $fieldsPath.'/'.$fieldName.'.json';

                file_put_contents($filename, $schema);

                $fieldCount++;
                $this->line("  <fg=green>✓</> {$fieldName}.json - {$fieldType->getLabel()}");
            } catch (\Exception $e) {
                $fieldErrors++;
                $this->error("  ✗ {$fieldName} - Error: {$e->getMessage()}");
            }
        }

        $this->newLine();

        // Export layout element type schemas
        $this->info('Exporting Layout Element Type Schemas...');
        $this->info('----------------------------------------');

        $elementRegistry = app(LayoutElementRegistry::class);
        $elementCount = 0;
        $elementErrors = 0;

        foreach ($elementRegistry->getAllInstances() as $elementName => $elementType) {
            try {
                $schema = $elementType->getFullSchema();
                $filename = $elementsPath.'/'.$elementName.'.json';

                file_put_contents($filename, $schema);

                $elementCount++;
                $this->line("  <fg=green>✓</> {$elementName}.json - {$elementType->getLabel()}");
            } catch (\Exception $e) {
                $elementErrors++;
                $this->error("  ✗ {$elementName} - Error: {$e->getMessage()}");
            }
        }

        $this->newLine();

        // Summary
        $this->info('Summary');
        $this->info('=======');
        $this->line("Field Types Exported: <fg=green>{$fieldCount}</>");
        if ($fieldErrors > 0) {
            $this->line("Field Type Errors: <fg=red>{$fieldErrors}</>");
        }
        $this->line("Layout Elements Exported: <fg=green>{$elementCount}</>");
        if ($elementErrors > 0) {
            $this->line("Layout Element Errors: <fg=red>{$elementErrors}</>");
        }
        $this->line('Total: <fg=green>'.($fieldCount + $elementCount).'</> schemas');

        $this->newLine();
        $this->info('Schemas exported to:');
        $this->line("  - {$fieldsPath}/");
        $this->line("  - {$elementsPath}/");

        $this->newLine();
        $this->info('✓ Export complete!');

        return Command::SUCCESS;
    }
}
