<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['filename', 'original_name', 'mime_type', 'size', 'path', 'alt'];

    /** Full public URL for the image */
    public function url(): string
    {
        return asset('storage/' . $this->path);
    }

    /** Human-readable file size */
    public function humanSize(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    /** Check if this media is referenced by any page_sections, products, or articles */
    public function isInUse(): bool
    {
        if (PageSection::where('media_id', $this->id)->exists()) return true;
        if (Product::where('image', $this->path)->exists()) return true;
        if (Article::where('thumbnail', $this->path)->exists()) return true;
        return false;
    }

    /** Relationships */
    public function pageSections()
    {
        return $this->hasMany(PageSection::class);
    }
}
