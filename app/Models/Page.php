<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Fillable(['slug', 'title', 'body'])]
#[Translatable('title', 'body')]
class Page extends Model
{
    use HasFactory, HasTranslations;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}