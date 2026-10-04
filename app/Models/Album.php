<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Fillable(['title', 'project_id'])]
#[Translatable('title')]
class Album extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)->height(150)->nonQueued();

        $this->addMediaConversion('card')
            ->width(800)->height(600)->nonQueued();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
