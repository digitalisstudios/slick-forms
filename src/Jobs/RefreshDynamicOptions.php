<?php

namespace DigitalisStudios\SlickForms\Jobs;

use DigitalisStudios\SlickForms\Models\FormField;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job to refresh dynamic options cache for a field
 *
 * Phase 1: Skeleton class
 * Phase 2: Full implementation scheduled
 */
class RefreshDynamicOptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 2;

    /**
     * The number of seconds to wait before retrying the job
     */
    public int $backoff = 30;

    /**
     * Create a new job instance
     */
    public function __construct(
        public FormField $field
    ) {}

    /**
     * Execute the job
     */
    public function handle(): void
    {
        throw new \RuntimeException('Method not yet implemented - Phase 2');
    }
}
