<?php

namespace App\Http\Middleware;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Closure;
use Illuminate\Http\Request;
use App\Services\CountryCurrencyService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

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

        // If user has manually selected a currency, don't run auto-detection
        if (Session::has('preferred_currency') && Session::get('currency_initialized') === true) {
            return $next($request);
        }

        // Only set currency if user hasn't manually selected one and hasn't been initialized
        if (!Session::has('preferred_currency') && !Session::has('currency_initialized')) {
            // Detect country and currency from IP
            $ip = $request->ip();
            Log::info("CurrencyMiddleware: Detecting currency for IP: {$ip}");

            $detected = $this->currencyService->detectCountry();
            Log::info("CurrencyMiddleware: Detection result", ['detected' => $detected]);

            if ($detected && $detected['currency_code']) {
                Session::put('preferred_currency', $detected['currency_code']);
                Session::put('detected_country', $detected['country_name']);
                Session::put('detected_currency', $detected['currency_code']);
                Log::info("CurrencyMiddleware: Set currency to {$detected['currency_code']} for {$detected['country_name']}");
            } else {
                Log::warning("CurrencyMiddleware: Could not detect country/currency from IP: {$ip}");
            }

            // Mark as initialized to avoid repeated detection
            Session::put('currency_initialized', true);
        } else {
            // If user has manually set a currency, don't override it
            if (Session::has('preferred_currency') && Session::get('currency_initialized')) {
                Log::info("CurrencyMiddleware: User has manually set currency, not overriding", [
                    'preferred_currency' => Session::get('preferred_currency'),
                    'currency_initialized' => Session::get('currency_initialized')
                ]);
            } else {
                Log::info("CurrencyMiddleware: Currency already set or initialized", [
                    'preferred_currency' => Session::get('preferred_currency'),
                    'currency_initialized' => Session::has('currency_initialized')
                ]);
            }
        }

        return $next($request);
    }
}
