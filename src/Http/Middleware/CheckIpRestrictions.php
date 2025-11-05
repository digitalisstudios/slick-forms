<?php

namespace DigitalisStudios\SlickForms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware to check IP address restrictions
 *
 * Phase 1: Skeleton class with method signature
 * Phase 2: Full implementation scheduled
 */
class CheckIpRestrictions
{
    /**
     * Handle an incoming request
     *
     * Checks if user's IP address is allowed to access form:
     * - Checks IP blacklist (if matched, deny access)
     * - Checks IP whitelist (if set and not matched, deny access)
     * - Supports CIDR notation for IP ranges
     * Returns 403 response if access denied.
     *
     *
     * @throws \RuntimeException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        throw new \RuntimeException('Method not yet implemented - Phase 2');
    }

    /**
     * Check if IP address is in list
     *
     *
     * @throws \RuntimeException
     */
    protected function ipInList(string $ip, array $list): bool
    {
        throw new \RuntimeException('Method not yet implemented - Phase 2');
    }

    /**
     * Check if IP address is in CIDR range
     *
     *
     * @throws \RuntimeException
     */
    protected function ipInCidr(string $ip, string $cidr): bool
    {
        throw new \RuntimeException('Method not yet implemented - Phase 2');
    }
}
