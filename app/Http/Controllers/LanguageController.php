<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    /**
     * Available languages
     */
    private $languages = [
        'pt_BR' => [
            'name' => 'Português (Brasil)',
            'flag' => '🇧🇷',
            'code' => 'pt_BR'
        ],
        'en' => [
            'name' => 'English',
            'flag' => '🇺🇸',
            'code' => 'en'
        ],
        'es' => [
            'name' => 'Español',
            'flag' => '🇪🇸',
            'code' => 'es'
        ]
    ];

    /**
     * Show language selection page
     */
    public function index()
    {
        $currentLocale = App::getLocale();
        
        return view('language.index', [
            'languages' => $this->languages,
            'currentLocale' => $currentLocale
        ]);
    }

    /**
     * Change application language
     */
    public function change(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:pt_BR,en,es'
        ]);

        $locale = $request->locale;

        // Store in session
        Session::put('locale', $locale);

        // Update user preference if authenticated
        if (Auth::check()) {
            Auth::user()->update(['preferred_language' => $locale]);
        }

        // Set application locale
        App::setLocale($locale);

        // Redirect back with success message
        return redirect()->back()->with('success', __('messages.common.success') . ' ' . __('messages.language.changed'));
    }

    /**
     * Get current language info
     */
    public function current()
    {
        $currentLocale = App::getLocale();
        
        return response()->json([
            'locale' => $currentLocale,
            'language' => $this->languages[$currentLocale] ?? null
        ]);
    }

    /**
     * Get all available languages
     */
    public function available()
    {
        return response()->json([
            'languages' => $this->languages,
            'current' => App::getLocale()
        ]);
    }

    /**
     * Detect language from browser
     */
    public function detect(Request $request)
    {
        $acceptLanguage = $request->header('Accept-Language');
        $detectedLocale = $this->parseAcceptLanguage($acceptLanguage);
        
        if ($detectedLocale && isset($this->languages[$detectedLocale])) {
            return response()->json([
                'detected' => $detectedLocale,
                'language' => $this->languages[$detectedLocale]
            ]);
        }

        return response()->json([
            'detected' => null,
            'message' => 'No supported language detected'
        ]);
    }

    /**
     * Parse Accept-Language header
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