<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Fillable(['name', 'slug', 'objectives', 'activities', 'icon', 'position'])]
#[Translatable('name', 'objectives', 'activities')]
class Domain extends Model
{
    use HasFactory, HasTranslations;

    /**
     * Les attributs à convertir automatiquement en type PHP.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    /**
     * Un domaine regroupe plusieurs projets (relation 1 vers N).
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
