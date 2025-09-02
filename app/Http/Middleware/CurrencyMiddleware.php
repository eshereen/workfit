<?php

namespace App\Http\Middleware;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Closure;
use Illuminate\Http\Request;
use App\Services\CountryCurrencyService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Exception;

class CurrencyMiddleware
{
    protected $currencyService;

    public function __construct(CountryCurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request):((Response|RedirectResponse)) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip currency detection for Livewire internal update requests to avoid interfering
        // with Livewire's AJAX lifecycle and prevent transient errors during rapid updates
        if ($request->is('livewire/*') || $request->headers->has('x-livewire')) {
            return $next($request);
        }

        // Skip for static assets and API requests
        if ($request->is('*.css', '*.js', '*.png', '*.jpg', '*.jpeg', '*.gif', '*.svg', '*.ico', '*.woff', '*.woff2', 'api/*')) {
            return $next($request);
        }

        // If user has manually selected a currency, don't run auto-detection
        if (Session::has('preferred_currency') && Session::get('currency_initialized') === true) {
            return $next($request);
        }

        // Only set currency if user hasn't manually selected one and hasn't been initialized
        if (!Session::has('preferred_currency') && !Session::has('currency_initialized')) {
            // Skip detection for localhost/development
            $ip = $request->ip();
            if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
                Session::put('preferred_currency', 'USD');
                Session::put('detected_country', 'United States');
                Session::put('detected_currency', 'USD');
                Session::put('currency_initialized', true);
                return $next($request);
            }

            // Detect country and currency from IP (with timeout protection)
            Log::info("CurrencyMiddleware: Detecting currency for IP: {$ip}");

            try {
                $detected = $this->currencyService->detectCountry();
                Log::info("CurrencyMiddleware: Detection result", ['detected' => $detected]);

                if ($detected && $detected['currency_code']) {
                    Session::put('preferred_currency', $detected['currency_code']);
                    Session::put('detected_country', $detected['country_name']);
                    Session::put('detected_currency', $detected['currency_code']);
                    Log::info("CurrencyMiddleware: Set currency to {$detected['currency_code']} for {$detected['country_name']}");
                } else {
                    // Fallback to USD if detection fails
                    Session::put('preferred_currency', 'USD');
                    Session::put('detected_country', 'United States');
                    Session::put('detected_currency', 'USD');
                    Log::warning("CurrencyMiddleware: Could not detect country/currency from IP: {$ip}, using USD fallback");
                }
            } catch (Exception $e) {
                // Fallback to USD if detection throws an exception
                Session::put('preferred_currency', 'USD');
                Session::put('detected_country', 'United States');
                Session::put('detected_currency', 'USD');
                Log::error("CurrencyMiddleware: Detection failed with exception: " . $e->getMessage());
            }

            // Mark as initialized to avoid repeated detection
            Session::put('currency_initialized', true);
        }

        return $next($request);
    }
}
