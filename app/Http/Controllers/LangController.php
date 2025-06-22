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
        $user = request()->user();
        $user->prefer_language = $language;
        $user->save();
        // Set application locale immediately
        App::setLocale($language);
        
        // Store in session for persistence
        Session::put('locale', $language);
        
        return back()->with('success', __('Language changed successfully'));
    }

    /**
     * Get available languages
     */
    public function getAvailableLanguages(): array
    {
        return self::AVAILABLE_LANGUAGES;
    }
}
