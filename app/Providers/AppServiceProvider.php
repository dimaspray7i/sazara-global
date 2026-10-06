<?php

namespace App\Providers;

use App\Models\PageSection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::defaults(['locale' => app()->getLocale()]);

        View::composer('*', function ($view) {

            $isId = app()->getLocale() === 'id';
            $fallbackMsg = $isId
                ? 'Halo Sazara Global, saya ingin mengetahui lebih lanjut tentang layanan ekspor komoditas Anda.'
                : 'Hello Sazara Global, I would like to know more about your commodity export services.';

            try {
                if (Schema::hasTable('page_sections')) {
                    $cleanWa = PageSection::getCleanWhatsApp();
                    $displayWa = PageSection::getSetting('whatsapp_number', config('sazara.whatsapp_display', '+62 812-6040-7208'));
                    $contactPerson = PageSection::getSetting('contact_person', config('sazara.contact_person', 'Afriansyah Munar'));
                    $dbMsg = PageSection::getSetting('whatsapp_message');

                    // Determine message
                    if ($dbMsg && $dbMsg !== 'Custom Inquiry') {
                        // If locale is English and DB still has default Indonesian, provide English default
                        if (! $isId && $dbMsg === 'Halo Sazara Global, saya ingin mengetahui lebih lanjut tentang layanan ekspor komoditas Anda.') {
                            $defaultMsg = config('sazara.whatsapp_message_en', 'Hello Sazara Global, I would like to know more about your commodity export services.');
                        } else {
                            $defaultMsg = $dbMsg;
                        }
                    } else {
                        $defaultMsg = $fallbackMsg;
                    }

                    $email = PageSection::getSetting('email', config('sazara.email', 'contact@sazaraglobal.com'));
                    $phone = PageSection::getSetting('phone', config('sazara.phone', '+62 812-6040-7208'));
                    $address = PageSection::getSetting('address', config('sazara.address', 'Medan, North Sumatra, Indonesia'));
                } else {
                    $cleanWa = '6281260407208';
                    $displayWa = '+62 812-6040-7208';
                    $contactPerson = 'Afriansyah Munar';
                    $defaultMsg = $fallbackMsg;
                    $email = 'contact@sazaraglobal.com';
                    $phone = '+62 812-6040-7208';
                    $address = 'Medan, North Sumatra, Indonesia';
                }
            } catch (\Throwable $e) {
                $cleanWa = '6281260407208';
                $displayWa = '+62 812-6040-7208';
                $contactPerson = 'Afriansyah Munar';
                $defaultMsg = $fallbackMsg;
                $email = 'contact@sazaraglobal.com';
                $phone = '+62 812-6040-7208';
                $address = 'Medan, North Sumatra, Indonesia';
            }

            $currentLocale = app()->getLocale();
            $activeLanguages = \App\Models\Language::getActive();
            $currentLang = \App\Models\Language::findByCode($currentLocale) ?? \App\Models\Language::findByCode('en');

            $view->with([
                'globalWaNumber'      => $cleanWa,
                'globalWaDisplay'     => $displayWa,
                'globalWaDefault'     => $defaultMsg,
                'globalContactPerson' => $contactPerson,
                'globalWaUrl'         => "https://wa.me/{$cleanWa}?text=" . rawurlencode($defaultMsg),
                'globalEmail'         => $email,
                'globalPhone'         => $phone,
                'globalAddress'       => $address,
                'activeLanguages'     => $activeLanguages,
                'currentLanguage'     => $currentLang,
            ]);
        });
    }
}

