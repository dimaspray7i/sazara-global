<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $segment = strtolower((string) $request->segment(1));
        $supported = Language::getSupportedCodes();
        if (empty($supported)) {
            $supported = ['en', 'id'];
        }

        // Admin routes default to English and do not automatically inherit public session locale
        if ($request->is('admin*') || $segment === 'admin' || $segment === 'login-sazara') {
            $adminLocale = $request->query('admin_lang', session('admin_locale', 'en'));
            if (! in_array($adminLocale, $supported, true)) {
                $adminLocale = 'en';
            }
            if ($request->has('admin_lang')) {
                session(['admin_locale' => $adminLocale]);
            }
            app()->setLocale($adminLocale);
            return $next($request);
        }

        if (in_array($segment, $supported, true)) {
            $locale = $segment;
            session(['locale' => $locale]);
        } else {
            $locale = session('locale', 'en');
            if (! in_array($locale, $supported, true)) {
                $locale = 'en';
            }
        }

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}