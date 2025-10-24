<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from various sources in order of priority
        $locale = $this->getLocale($request);
        
        // Set the application locale
        App::setLocale($locale);
        
        return $next($request);
    }

    /**
     * Determine the locale to use
     */
    private function getLocale(Request $request): string
    {
        // 1. Check if user has a preferred language set
        if (auth()->check() && auth()->user()->preferred_language) {
            return auth()->user()->preferred_language;
        }

        // 2. Check session for stored locale
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if ($this->isValidLocale($sessionLocale)) {
                return $sessionLocale;
            }
        }

        // 3. Check URL parameter
        if ($request->has('lang')) {
            $urlLocale = $request->get('lang');
            if ($this->isValidLocale($urlLocale)) {
                // Store in session for future requests
                Session::put('locale', $urlLocale);
                return $urlLocale;
            }
        }

        // 4. Check Accept-Language header
        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            $browserLocale = $this->parseAcceptLanguage($acceptLanguage);
            if ($browserLocale && $this->isValidLocale($browserLocale)) {
                return $browserLocale;
            }
        }

        // 5. Default to Portuguese (Brazil)
        return 'pt_BR';
    }

    /**
     * Check if locale is valid
     */
    private function isValidLocale(string $locale): bool
    {
        return in_array($locale, ['pt_BR', 'en', 'es']);
    }

    /**
     * Parse Accept-Language header to get preferred locale
     */
    private function parseAcceptLanguage(string $acceptLanguage): ?string
    {
        $languages = explode(',', $acceptLanguage);
        
        foreach ($languages as $language) {
            $locale = trim(explode(';', $language)[0]);
            
            // Map common browser locales to our supported locales
            $localeMap = [
                'pt' => 'pt_BR',
                'pt-BR' => 'pt_BR',
                'pt_BR' => 'pt_BR',
                'en' => 'en',
                'en-US' => 'en',
                'en-GB' => 'en',
                'es' => 'es',
                'es-ES' => 'es',
                'es-MX' => 'es',
                'es-AR' => 'es',
            ];

            if (isset($localeMap[$locale])) {
                return $localeMap[$locale];
            }
        }

        return null;
    }
}