<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Fillable(['domain_id', 'title', 'slug', 'status', 'country', 'city', 'start_date', 'end_date', 'objectives', 'beneficiaries', 'results', 'budget_amount', 'budget_currency', 'beneficiaries_count', 'beneficiaries_unit', 'is_featured', 'published_at'])]
#[Translatable('title', 'objectives', 'beneficiaries', 'results')]
class Project extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'budget_amount' => 'decimal:2',       // ← sort toujours avec 2 décimales
            'beneficiaries_count' => 'integer',    // ← force le type entier
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('photos');
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function partners(): BelongsToMany
    {
        return $this->belongsToMany(Partner::class, 'project_partner')->withTimestamps()->withPivot('role');
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
