<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'name_id', 'slug', 'description', 'description_id',
        'specification', 'specification_id', 'image', 'wa_template',
        'is_featured', 'is_active', 'sort_order',
    ];
    protected $casts = ['is_featured' => 'boolean', 'is_active' => 'boolean'];

    public function scopeActive($q)   { return $q->where('is_active', true); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }
    public function scopeOrdered($q)  { return $q->orderBy('sort_order'); }

    /* Ambil kolom sesuai bahasa aktif, fallback ke Inggris */
    public function tr(string $field): string
    {
        $column = app()->getLocale() === 'id' ? $field . '_id' : $field;
        return filled($this->$column) ? $this->$column : $this->$field;
    }

    public function imageUrl(): string
    {
        if (filled($this->image) && file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }
        if (file_exists(public_path('images/commodities/' . $this->slug . '.jpg'))) {
            return asset('images/commodities/' . $this->slug . '.jpg');
        }
        return asset('images/hero.jpg');
    }
}