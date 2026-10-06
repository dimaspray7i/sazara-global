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