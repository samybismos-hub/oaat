<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Fillable(['organisation_name', 'email', 'phones', 'addresses', 'socials', 'stat_projects', 'stat_beneficiaries', 'stat_zones', 'stat_years'])]
#[Translatable('organisation_name', 'addresses')]
class Setting extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    /**
     * Les attributs à convertir automatiquement en type PHP.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'phones' => 'array',
            'socials' => 'array',
        ];
    }

    /**
     * Nombre d'années d'activité, calculé depuis la fondation (jamais saisi à la main).
     */
    protected function yearsOfActivity(): Attribute
    {
        return Attribute::get(fn (): int => (int) now()->diffInYears(config('oaat.founded_at'), true));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public static function current(): ?self
    {
        return static::query()->first();
    }
}
