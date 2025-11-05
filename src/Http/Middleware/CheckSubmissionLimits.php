<?php

namespace DigitalisStudios\SlickForms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware to check submission limits
 *
 * Phase 1: Skeleton class with method signature
 * Phase 2: Full implementation scheduled
 */
class CheckSubmissionLimits
{
    /**
     * Handle an incoming request
     *
     * Checks if form has reached submission limits:
     * - Total submission limit across all users
     * - Per-user submission limit (by IP address)
     * Returns 403 response if limits reached.
     *
     *
     * @throws \RuntimeException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        throw new \RuntimeException('Method not yet implemented - Phase 2');
    }
}
