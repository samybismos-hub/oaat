<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Fillable(['organisation_name', 'email', 'phone', 'address', 'facebook_url', 'linkedin_url', 'x_url', 'instagram_url', 'youtube_url', 'stat_projects', 'stat_beneficiaries', 'stat_zones', 'stat_years'])]
#[Translatable('organisation_name', 'address')]
class Setting extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public static function current(): ?self
    {
        return static::query()->first();
    }
}