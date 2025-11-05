<?php

namespace DigitalisStudios\SlickForms\Console\Commands;

use Illuminate\Console\Command;

class InstallSlickFormsCommand extends Command
{
    protected $signature = 'slick-forms:install {--force : Force installation}';

    protected $description = 'Install Slick Forms with interactive feature selection';

    public function handle(): int
    {
        $this->info('Slick Forms Installation');
        $this->warn('Installation wizard not yet implemented.');

        return self::SUCCESS;
    }
}
