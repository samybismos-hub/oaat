<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Fillable(['name', 'role', 'bio', 'quote', 'position', 'is_founder'])]
#[Translatable('role', 'bio', 'quote')]
class TeamMember extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'is_founder' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)->height(150)->nonQueued();

        $this->addMediaConversion('card')
            ->width(400)->height(400)->nonQueued();
    }
}
