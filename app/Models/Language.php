<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Language extends Model
{
    protected $fillable = [
        'code', 'name', 'native_name', 'flag', 'direction',
        'is_active', 'is_default', 'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function isRtl(): bool
    {
        return strtolower($this->direction) === 'rtl';
    }

    /**
     * Get all active languages ordered.
     */
    public static function getActive()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get array of all supported active language codes.
     */
    public static function getSupportedCodes(): array
    {
        return Cache::remember('sazara_supported_lang_codes', 3600, function () {
            return static::where('is_active', true)->pluck('code')->toArray();
        });
    }

    /**
     * Get language model by code.
     */
    public static function findByCode(string $code): ?self
    {
        return static::where('is_active', true)->where('code', strtolower($code))->first();
    }


    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('sazara_active_languages');
        });
        static::deleted(function () {
            Cache::forget('sazara_active_languages');
        });
    }
}
