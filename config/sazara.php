<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sazara Global Centralized Settings
    |--------------------------------------------------------------------------
    |
    | Central configuration for WhatsApp integration, company contact details,
    | and site-wide defaults. These serve as default fallbacks when values
    | have not been customized in the Admin Settings panel.
    |
    */

    'whatsapp_number'     => env('WHATSAPP_NUMBER', '6281260407208'),
    'whatsapp_display'    => env('WHATSAPP_DISPLAY', '+62 812-6040-7208'),
    'whatsapp_message'    => env('WHATSAPP_DEFAULT_MESSAGE', 'Halo Sazara Global, saya ingin mengetahui lebih lanjut tentang layanan ekspor komoditas Anda.'),
    'whatsapp_message_en' => env('WHATSAPP_DEFAULT_MESSAGE_EN', 'Hello Sazara Global, I would like to know more about your commodity export services.'),
    'contact_person'      => env('WHATSAPP_CONTACT_NAME', 'Afriansyah Munar'),

    'email'   => env('SITE_EMAIL', 'contact@sazaraglobal.com'),
    'phone'   => env('SITE_PHONE', '+62 812-6040-7208'),
    'address' => env('SITE_ADDRESS', 'Medan, North Sumatra, Indonesia'),
    'website' => env('SITE_WEBSITE', 'www.sazaraglobal.com'),
];
