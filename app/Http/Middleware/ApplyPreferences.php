<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyPreferences
{
    public function handle(Request $request, Closure $next): Response
    {
        // Apply language from cookie
        if ($request->hasCookie('language')) {
            $language = $request->cookie('language');
            app()->setLocale($language);
        }
        
        // Apply theme to view
        $theme = $request->cookie('theme', 'light');
        view()->share('theme', $theme);
        
        // Apply timezone to user if authenticated
        if ($request->user() && $request->hasCookie('timezone')) {
            // You can set timezone for the user here
            // date_default_timezone_set($request->cookie('timezone'));
        }
        
        return $next($request);
    }
}