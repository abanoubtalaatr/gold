<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LangController extends Controller
{
    /**
     * Available languages
     */
    private const AVAILABLE_LANGUAGES = ['en', 'ar'];

    /**
     * Change application language
     */
    public function change(Request $request): RedirectResponse
    {
        $language = $request->input('lang', 'en');
        
        // Validate language
        if (!in_array($language, self::AVAILABLE_LANGUAGES)) {
            $language = 'en'; // fallback to English
        }
        
        // Set application locale
        App::setLocale($language);
        
        // Store in session
        Session::put('locale', $language);
        
        return redirect()->back()->with('success', __('Language changed successfully'));
    }

    /**
     * Get available languages
     */
    public function getAvailableLanguages(): array
    {
        return self::AVAILABLE_LANGUAGES;
    }
}
