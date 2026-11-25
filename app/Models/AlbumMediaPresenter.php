<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * AlbumMediaPresenter — transforme un Media Spatie en hash galerie.
 *
 * Contexte : la galerie (GalerieController, _galerie_media.blade.php)
 * attend pour chaque photo un tableau associatif structuré (un « hash $m »)
 * conforme au contrat DOM §3.1 de Implémentation_Album.md.
 *
 * Ce présentateur extrait la logique qui vivait dans GalerieController@buildMediaHash,
 * afin que le contrôleur ne soit qu'un orchestrateur, sans connaissance
 * du format interne de custom_properties (PATTERNS §2 — règle du métier).
 *
 * Appel :
 *     $hash = AlbumMediaPresenter::toHash($media, $album, $locale);
 */
class AlbumMediaPresenter
{
    /**
     * Construit le hash d'un média pour la vue galerie.
     *
     * @param  Media  $media  Instance Spatie Media (collection 'photos')
     * @param  Album  $album  Album parent (doit avoir ->project chargé)
     * @param  string $locale 'fr' ou 'en' (pour fallback titres)
     * @return array           Hash plat respectant le contrat §3.1
     */
    public static function toHash(Media $media, Album $album, string $locale): array
    {
        $props = $media->custom_properties ?? [];
        $project = $album->project;

        // ── Dimensions ────────────────────────────────────────────────────────
        $w   = (int) ($props['w'] ?? 800);
        $h   = (int) ($props['h'] ?? 600);
        $ori = $props['ori'] ?? ($w >= $h ? 'landscape' : 'portrait');

        // ── Titres bilingues (bruts ; la vue fait $t) ─────────────────────────
        $tf = (string) ($props['title_fr']
            ?? $album->getTranslation('title', 'fr')
            ?? $album->getTranslation('title', $locale)
            ?? '');
        $te = (string) ($props['title_en']
            ?? $album->getTranslation('title', 'en')
            ?? $tf);

        // ── Catégorie / thème / lieu (codes bruts) ────────────────────────────
        $cat  = (string) ($props['cat']  ?? $project?->domain?->slug ?? '');
        $th   = (string) ($props['th']   ?? $project?->domain?->slug ?? '');
        $lieu = (string) ($props['lieu'] ?? '');

        // ── Libellés de catégorie (la vue les utilise via $t) ─────────────────
        $catLabel = (string) ($props['cat_label_fr']
            ?? $project?->domain?->getTranslation('title', 'fr')
            ?? '');
        $catLabelEn = (string) ($props['cat_label_en']
            ?? $project?->domain?->getTranslation('title', 'en')
            ?? $catLabel);

        // ── Projet parent (noms bruts ; la vue fait $t) ───────────────────────
        $pfr = (string) ($props['project_fr']
            ?? $project?->getTranslation('title', 'fr')
            ?? $album->getTranslation('title', 'fr')
            ?? '');
        $pen = (string) ($props['project_en']
            ?? $project?->getTranslation('title', 'en')
            ?? $album->getTranslation('title', 'en')
            ?? $pfr);

        // ── Année ─────────────────────────────────────────────────────────────
        $y = (string) ($props['year'] ?? $project?->start_date?->format('Y') ?? '');

        // ── URLs des images ───────────────────────────────────────────────────
        $id    = (int) $media->id;
        $card  = $media->getUrl('card');
        $thumb = $media->getUrl('thumb');
        $dl    = $media->getUrl();

        // ── Source / crédit (bruts) ───────────────────────────────────────────
        $src = (string) ($props['src'] ?? '');
        $sfr = (string) ($props['source_fr'] ?? '');
        $sen = (string) ($props['source_en'] ?? $sfr);

        // ── Tableaux (par, col, use, tags) ────────────────────────────────────
        $par  = (array) ($props['par']  ?? []);
        $col  = (array) ($props['col']  ?? []);
        $use  = (array) ($props['use']  ?? []);
        $tags = (array) ($props['tags'] ?? []);

        // ── Référence unique OAAT-xxxx ────────────────────────────────────────
        $ref = 'OAAT-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT);

        // ── Chaîne de recherche normalisée (fulltext client-side) ─────────────
        $q = Str::of("{$tf} {$te} {$ref}")
            ->lower()
            ->replaceMatches('/[^a-z0-9\s\-]/', '')
            ->trim()
            ->toString();

        // ── Retour du hash plat (§3.1) ────────────────────────────────────────
        return [
            'id'         => $id,
            'ref'        => $ref,
            'y'          => $y,
            'w'          => $w,
            'h'          => $h,
            'ori'        => $ori,
            'cat'        => $cat,
            'th'         => $th,
            'lieu'       => $lieu,
            'tf'         => $tf,
            'te'         => $te,
            'pfr'        => $pfr,
            'pen'        => $pen,
            'src'        => $src,
            'sfr'        => $sfr,
            'sen'        => $sen,
            'par'        => $par,
            'col'        => $col,
            'use'        => $use,
            'tags'       => $tags,
            'catLabel'   => $catLabel,
            'catLabelEn' => $catLabelEn,
            'lieuLabel'  => $lieu,
            'q'          => $q,
            'card'       => $card,
            'thumb'      => $thumb,
            'dl'         => $dl,
        ];
    }
}