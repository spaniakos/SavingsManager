<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $locale = $request->input('locale', 'en');
        
        if (in_array($locale, ['en', 'el'])) {
            Cookie::queue('locale', $locale, 60 * 24 * 365); // 1 year
            app()->setLocale($locale);
        }
        
        return Redirect::back();
    }
}

