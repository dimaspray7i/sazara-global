<?php

namespace App\Providers;

use App\Services\DynamicTranslator;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\TranslationServiceProvider as BaseProvider;

/**
 * Override the Laravel TranslationServiceProvider to inject DynamicTranslator
 * so locales without static .php lang files (zh, ja, ko, ar, es, fr, ...)
 * use DB-backed translations instead of falling back to English.
 *
 * We implement DeferrableProvider to match the base and ensure this provider
 * controls the 'translator' and 'translation.loader' bindings.
 */
class TranslationServiceProvider extends BaseProvider implements DeferrableProvider
{
    /**
     * Re-register the translator as a DynamicTranslator singleton.
     * This runs AFTER the parent::register(), overwriting the base binding.
     */
    public function register(): void
    {
        // First let parent register translation.loader
        parent::register();

        // Then override the 'translator' singleton with DynamicTranslator
        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];
            $locale = $app->getLocale();

            $trans = new DynamicTranslator($loader, $locale);
            $trans->setFallback($app->getFallbackLocale());

            return $trans;
        });
    }
}
