<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title', 'title_id', 'slug', 'excerpt', 'excerpt_id',
        'body', 'body_id', 'thumbnail', 'status', 'published_at',
    ];
    protected $casts = ['published_at' => 'date'];

    public function scopePublished($q) { return $q->where('status', 'published'); }

    public function tr(string $field): string
    {
        $column = app()->getLocale() === 'id' ? $field . '_id' : $field;
        return filled($this->$column) ? $this->$column : $this->$field;
    }

    public function imageUrl(): string
    {
        if (filled($this->thumbnail) && file_exists(public_path('storage/' . $this->thumbnail))) {
            return asset('storage/' . $this->thumbnail);
        }
        if (file_exists(public_path('images/articles/' . $this->slug . '.jpg'))) {
            return asset('images/articles/' . $this->slug . '.jpg');
        }
        return asset('images/commodities/other-commodities.jpg');
    }
}