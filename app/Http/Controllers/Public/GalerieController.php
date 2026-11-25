<?php

namespace App\Http\Controllers\Public;

use App\Models\Album;
use App\Models\AlbumMediaPresenter;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class GalerieController
{
    /**
     * Affiche la galerie complA?te.
     */
    public function index(string $locale): View
    {
        $settings = Setting::current();

        $albums = Album::query()
            ->with(['media', 'project.domain'])
            ->latest('created_at')
            ->get();

        $medias = collect();
        foreach ($albums as $album) {
            foreach ($album->getMedia('photos') as $media) {
                $medias->push(AlbumMediaPresenter::toHash($media, $album, $locale));
            }
        }

        $categories = $medias
            ->groupBy('cat')
            ->map(fn (Collection $items, string $cat): array => [
                'code'  => $cat,
                'fr'    => $items->first()['catLabel'] ?? '',
                'en'    => $items->first()['catLabelEn'] ?? ($items->first()['catLabel'] ?? ''),
                'count' => $items->count(),
            ])
            ->values()
            ->toArray();

        $facettes = array_values(array_filter([
            self::buildFacetteGroup('th',   $medias),
            self::buildFacetteGroup('lieu', $medias),
            self::buildFacetteGroup('ori',  $medias, scroll: false),
        ]));

        $collections = self::buildCollections($medias);
        $annees = self::buildAnnees($medias);

        $photosCount = $medias->count();
        $albumsCount = $albums->count();
        $yearsRaw    = $medias->pluck('y')->filter()->unique()->sort()->values();
        $stats = [
            ['valeur' => $photosCount, 'fr' => 'Photos', 'en' => 'Photos'],
            ['valeur' => $albumsCount, 'fr' => 'Albums', 'en' => 'Albums'],
            ['valeur' => $yearsRaw->isNotEmpty()
                ? $yearsRaw->first() . "\xe2\x80\x93" . $yearsRaw->last()
                : date('Y'),
             'fr' => "Ann\xc3\xa9es d'activit\xc3\xa9", 'en' => 'Years of activity'],
        ];

        $tags = $medias
            ->pluck('tags')
            ->flatten(1)
            ->unique(fn ($t) => is_array($t) ? ($t['fr'] ?? serialize($t)) : $t)
            ->values()
            ->toArray();

        return view('pages.galerie-index', compact(
            'medias', 'categories', 'facettes', 'collections',
            'annees', 'stats', 'tags', 'settings', 'locale'
        ));
    }

    private static function buildFacetteGroup(string $field, Collection $medias, bool $scroll = true): ?array
    {
        $values = $medias
            ->groupBy($field)
            ->filter(fn (Collection $items, string $v) => $v !== '')
            ->map(fn (Collection $items, string $v): array => [
                'v'     => $v,
                'fr'    => $v,
                'en'    => $v,
                'count' => $items->count(),
            ])
            ->values()
            ->toArray();

        if (empty($values)) {
            return null;
        }

        return [
            'f'      => $field,
            'fr'     => $field,
            'en'     => $field,
            'open'   => false,
            'scroll' => $scroll,
            'values' => $values,
        ];
    }

    private static function buildCollections(Collection $medias): array
    {
        $allCols = $medias
            ->pluck('col')
            ->flatten()
            ->filter(fn (string $c) => $c !== '')
            ->countBy()
            ->sortDesc();

        if ($allCols->isEmpty()) {
            return [];
        }

        $palette = config('oaat.galerie.collection_palette', [205, 350, 95, 30, 215, 190, 285, 330, 150, 45]);

        return $allCols
            ->map(function (int $count, string $code) use ($palette): array {
                $index = abs(crc32($code)) % count($palette);
                return [
                    'code'  => $code,
                    'fr'    => $code,
                    'en'    => $code,
                    'count' => $count,
                    'hue'   => $palette[$index],
                ];
            })
            ->values()
            ->toArray();
    }

    private static function buildAnnees(Collection $medias): array
    {
        $years = $medias
            ->pluck('y')
            ->filter(fn (string $y) => $y !== '' && is_numeric($y))
            ->map(fn (string $y) => (int) $y);

        if ($years->isEmpty()) {
            return ['min' => null, 'max' => null, 'bars' => []];
        }

        $min      = $years->min();
        $max      = $years->max();
        $counts   = $years->countBy()->toArray();
        $maxCount = max($counts);

        $bars = [];
        for ($y = $min; $y <= $max; $y++) {
            $c            = $counts[$y] ?? 0;
            $bars[$y]     = $maxCount > 0 ? (int) round($c / $maxCount * 100) : 0;
        }

        return [
            'min'  => $min,
            'max'  => $max,
            'bars' => $bars,
        ];
    }
}