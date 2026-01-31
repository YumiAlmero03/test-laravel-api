<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    /** @use HasFactory<\Database\Factories\TranslationFactory> */
    use HasFactory;

    protected $fillable = ['locale_id', 'key', 'value'];

    public function scopeWithTags($query, array $tags)
    {
        if (empty($tags)) {
            return $query; // 👈 no filter → return all translations
        }

        return $query->whereHas('tags', fn ($q) => $q->whereIn('name', $tags)
        );
    }

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_translations');
    }
}
