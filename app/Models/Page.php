<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Fillable(['slug', 'title', 'body', 'meta_title', 'meta_description', 'timeline', 'recognitions', 'zones'])]
#[Translatable('title', 'body', 'meta_title', 'meta_description', 'timeline', 'recognitions', 'zones')]
class Page extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

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

    public function registerMediaCollections(): void
    {
        // Le disk « public » est indispensable : c'est lui qui est servi par
        // le symlink public/storage. Sans cette ligne, le composant Filament
        // utilise le disk par défaut de l'app (local → storage/app/private),
        // les fichiers ne sont pas exposés publiquement, et les images ne
        // s'affichent pas (on retombe sur les fallbacks fictifs).
        $this->addMediaCollection('hero')
            ->useDisk('public')
            ->storeConversionsOnDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('banner')
            ->width(1920)->height(800)->nonQueued();
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
    public function timelineFor(string $locale): array
    {
        $values = $this->getTranslation('timeline', $locale) ?? [];
        if ($values === [] || ! is_array($values)) {
            $values = $this->getTranslation('timeline', 'fr') ?? [];
        }
        return is_array($values) ? $values : [];
    }

    public function recognitionsFor(string $locale): array
    {
        $values = $this->getTranslation('recognitions', $locale) ?? [];
        if ($values === [] || ! is_array($values)) {
            $values = $this->getTranslation('recognitions', 'fr') ?? [];
        }
        return is_array($values) ? $values : [];
    }

    public function zonesFor(string $locale): array
    {
        $values = $this->getTranslation('zones', $locale) ?? [];
        if ($values === [] || ! is_array($values)) {
            $values = $this->getTranslation('zones', 'fr') ?? [];
        }
        return is_array($values) ? $values : [];
    }

}
