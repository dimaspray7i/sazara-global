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

    public function waUrl(): string
    {
        $cleanWa = PageSection::getCleanWhatsApp();
        $isId = app()->getLocale() === 'id';

        if (filled($this->wa_template)) {
            $msg = $this->wa_template;
        } elseif ($this->slug === 'other-commodities') {
            $msg = $isId
                ? 'Halo Sazara Global, saya memiliki permintaan khusus (Custom Inquiry). Mohon informasi ketersediaan dan penawaran terbaik.'
                : 'Hello Sazara Global, I have a custom commodity inquiry. Please let me know the availability and best offer.';
        } else {
            $name = $this->tr('name');
            $msg = $isId
                ? "Halo Sazara Global, saya tertarik dengan {$name}. Mohon kirimkan penawaran terbaik."
                : "Hello Sazara Global, I am interested in {$name}. Please send me your best offer.";
        }

        return "https://wa.me/{$cleanWa}?text=" . rawurlencode($msg);
    }
}