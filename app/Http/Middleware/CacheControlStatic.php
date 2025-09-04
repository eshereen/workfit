<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheControlStatic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $path = $request->path();

        // File extensions to cache
        $staticExtensions = [
            'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp',
            'woff', 'woff2', 'ttf', 'otf', 'eot',
        ];

        // If request ends with one of the static extensions → cache it
        if (preg_match('/\.(' . implode('|', $staticExtensions) . ')$/i', $path)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        } else {
            // Dynamic pages → no caching
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
