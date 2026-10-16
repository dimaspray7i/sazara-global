<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryCategory extends Model
{
    protected $fillable = ['slug', 'name', 'name_id', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }

    /** Multilingual label */
    public function label(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'id' && filled($this->name_id)) return $this->name_id;
        return $this->name;
    }

    /** Get all active categories as slug => label array (for views) */
    public static function activeMap(): array
    {
        return static::active()->get()->mapWithKeys(fn ($c) => [$c->slug => $c->label()])->toArray();
    }

    /** Get all active categories as slug => name_en array (for seeding, etc.) */
    public static function allActive(): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()->get();
    }
}
