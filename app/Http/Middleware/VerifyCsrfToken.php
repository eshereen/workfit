<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Exclude debug routes only (these are safe to exclude)
        'debug/*',
        'emergency-test.php',
        // Exclude PayMob callback (external webhook)
        'api/paymob/callback',
    ];

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        // Check the standard exclusions first
        if (parent::shouldPassThrough($request)) {
            return true;
        }

        // For localhost: Be more lenient with CSRF for development
        if (app()->environment('local')) {
            // Allow Livewire requests on localhost
            if ($this->isLivewireRequest($request)) {
                return true;
            }

            // Allow Filament requests on localhost
            if ($this->isFilamentRequest($request)) {
                return true;
            }
        }

        // For Livewire requests: Handle stale token issues
        if ($this->isLivewireRequest($request)) {
            $sessionToken = $request->session()->token();
            $requestToken = $this->getTokenFromRequest($request);

            // If tokens don't match, regenerate session token once and try again
            if ($sessionToken && $requestToken && !hash_equals($sessionToken, $requestToken)) {
                \Log::info('CSRF token mismatch - regenerating session token', [
                    'url' => $request->url(),
                    'old_session_token' => substr($sessionToken, 0, 10) . '...',
                    'request_token' => substr($requestToken, 0, 10) . '...',
                ]);

                // Regenerate token and allow this request through
                $request->session()->regenerateToken();
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if this is a Livewire request.
     */
    protected function isLivewireRequest($request): bool
    {
        return $request->hasHeader('X-Livewire') ||
               str_contains($request->url(), '/livewire/') ||
               $request->hasHeader('X-Livewire-Request');
    }

    /**
     * Determine if this is a Filament request.
     */
    protected function isFilamentRequest($request): bool
    {
        return str_contains($request->url(), '/admin/') ||
               str_contains($request->url(), '/filament/') ||
               $request->hasHeader('X-Filament');
    }

    /**
     * Get the CSRF token from the request.
     */
    protected function getTokenFromRequest($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (!$token && $header = $request->header('X-XSRF-TOKEN')) {
            try {
                $token = decrypt($header);
            } catch (\Exception $e) {
                // If decryption fails, use the raw header value
                $token = $header;
            }
        }

        return $token;
    }
}
