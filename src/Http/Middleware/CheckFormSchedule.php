<?php

namespace DigitalisStudios\SlickForms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware to check form schedule availability
 *
 * Phase 1: Skeleton class with method signature
 * Phase 2: Full implementation scheduled
 */
class CheckFormSchedule
{
    /**
     * Handle an incoming request
     *
     * Checks if form is available based on schedule settings (available_from, available_until).
     * Returns 403 response if form is not currently available.
     *
     *
     * @throws \RuntimeException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        throw new \RuntimeException('Method not yet implemented - Phase 2');
    }
}
