<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LivewireCSRFMiddleware extends VerifyCsrfToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next): Response
    {
        // Add debugging for live server
        if (app()->environment('production')) {
            Log::info('LivewireCSRFMiddleware: Processing request', [
                'url' => $request->url(),
                'method' => $request->method(),
                'is_livewire' => $request->hasHeader('X-Livewire'),
                'has_csrf_token' => $request->hasHeader('X-CSRF-TOKEN') || $request->hasHeader('X-XSRF-TOKEN'),
                'session_token' => $request->session()->token(),
                'csrf_token' => csrf_token(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->header('referer')
            ]);
        }

        // For Livewire requests, ensure proper token handling
        if ($this->isLivewireRequest($request)) {
            // Check for XSRF token in header (common in SPA setups)
            if ($request->hasHeader('X-XSRF-TOKEN')) {
                $token = $request->header('X-XSRF-TOKEN');
                $request->headers->set('X-CSRF-TOKEN', $token);
            }

            // Regenerate CSRF token if it's expired or invalid
            if (!$this->tokensMatch($request)) {
                if (app()->environment('production')) {
                    Log::warning('LivewireCSRFMiddleware: CSRF token mismatch detected', [
                        'session_token' => $request->session()->token(),
                        'request_token' => $this->getTokenFromRequest($request),
                        'session_id' => $request->session()->getId()
                    ]);
                }

                // Regenerate session and CSRF token
                $request->session()->regenerateToken();

                // Return a JSON response for Livewire with new token
                return response()->json([
                    'error' => 'Session expired. Please refresh the page.',
                    'csrf_token' => csrf_token(),
                    'redirect' => $request->url()
                ], 419);
            }
        }

        return parent::handle($request, $next);
    }

    /**
     * Determine if the request is a Livewire request.
     */
    protected function isLivewireRequest(Request $request): bool
    {
        return $request->hasHeader('X-Livewire') ||
               $request->hasHeader('X-Livewire-Request') ||
               str_contains($request->url(), '/livewire/');
    }

    /**
     * Get the CSRF token from the request.
     */
    protected function getTokenFromRequest($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (!$token && $header = $request->header('X-XSRF-TOKEN')) {
            $token = $this->encrypter->decrypt($header, static::serialized());
        }

        return $token;
    }

}
