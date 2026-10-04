<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Fillable(['title', 'slug', 'body', 'published_at', 'is_featured'])]
#[Translatable('title', 'body')]
class Actuality extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)->height(150)->nonQueued();

        $this->addMediaConversion('card')
            ->width(600)->height(400)->nonQueued();

        $this->addMediaConversion('hero')
            ->width(1200)->height(630)->nonQueued();
    }

    /**
     * Extrait le résumé d'une actualité dans la langue demandée.
     *
     * Prend les 200 premiers caractères du corps de l'actualité (HTML retiré)
     * et tronque proprement sur un mot pour éviter de couper en milieu de mot.
     * Utilisé sur la page d'accueil et les listes d'actualités.
     */
    public function excerpt(string $locale = 'fr'): string
    {
        $body = $this->getTranslation('body', $locale) ?? '';

        return Str::limit(trim(strip_tags($body)), 200);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
