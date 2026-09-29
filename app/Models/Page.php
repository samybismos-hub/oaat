<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Fillable(['slug', 'title', 'body', 'meta_title', 'meta_description'])]
#[Translatable('title', 'body', 'meta_title', 'meta_description')]
class Page extends Model
{
    use HasFactory, HasTranslations;

    /**
     * Les pages dites "système" : leur adresse est utilisée par la navigation
     * du site public. On interdit donc leur suppression et la modification
     * de leur slug depuis l'administration.
     *
     * @var list<string>
     */
    public const SYSTEM_SLUGS = ['accueil', 'organisation'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Indique si la page fait partie des pages système (accueil, organisation).
     */
    public function isSystem(): bool
    {
        return in_array($this->slug, self::SYSTEM_SLUGS, true);
    }

    /**
     * Verrou de sécurité au niveau du modèle : même un appel direct
     * `$page->delete()` est annulé pour les pages système.
     * En Eloquent, un écouteur `deleting` qui renvoie `false` bloque la suppression.
     */
    protected static function booted(): void
    {
        static::deleting(fn (Page $page): bool => ! $page->isSystem());
    }

    /**
     * Titre à utiliser pour les moteurs de recherche :
     * le titre SEO s'il est renseigné, sinon le titre de la page.
     */
    public function seoTitle(string $locale): string
    {
        $metaTitle = $this->getTranslations('meta_title')[$locale] ?? null;

        if (is_string($metaTitle) && $metaTitle !== '') {
            return $metaTitle;
        }

        return $this->getTranslations('title')[$locale] ?? '';
    }

    /**
     * Description à utiliser pour les moteurs de recherche :
     * la description SEO si elle est renseignée, sinon un extrait du contenu
     * (avec le HTML retiré pour ne pas afficher de balises).
     */
    public function seoDescription(string $locale): string
    {
        $metaDescription = $this->getTranslations('meta_description')[$locale] ?? null;

        if (is_string($metaDescription) && $metaDescription !== '') {
            return $metaDescription;
        }

        $body = $this->getTranslations('body')[$locale] ?? '';

        return Str::limit(trim(strip_tags(is_string($body) ? $body : '')), 155);
    }
}
