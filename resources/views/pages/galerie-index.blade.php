{{--
    pages/galerie-index.blade.php — Page « Galerie » (médiathèque).
    ──────────────────────────────────────────────────────────────────────────
    Assemblage des partials _galerie_* (cf. PATTERNS §2 : une page = une carte
    de visite). Les briques communes (topbar, header, footer, #pg, #up, #ts)
    viennent du layout `layouts.app`. Le JS (oaat.js §23) est poussé dans la
    stack `scripts`. Contrat DOM : Implémentation_Album.md §3.

    Variables (préparées par GalerieController@index, étape suivante) :
        $medias      : [{id,y,w,h,cat,th,lieu,ori,par[],col[],use[],tf,te,pfr,pen,
                        src,sfr,sen,tags[{fr,en}],q,thumb,card,dl}]
        $categories  : [{code,fr,en,count}]        // onglets .tb
        $collections : [{code,fr,en,count,hue}]    // bandeau collections (#cs)
        $facettes    : [{f,fr,en,open,scroll,values[{v,fr,en,count}]}]
        $annees      : {min,max,bars{y:hauteur%}}  // histogramme + curseurs
        $qs          : [{q,fr,en}]                 // recherches fréquentes
        $tags        : [{fr,en}]                   // vocabulaire .vtg (visionneuse)
        $stats       : [{valeur(int|string),fr,en}]// compteurs du héro
        $locale      : (string)
    Valeurs par défaut : gabarit rendable avant l'étape contrôleur (aucun
    calcul dans la vue, conformément à PATTERNS §2).
--}}
@php
    $locale ??= 'fr';
    $t = fn($fr, $en) => $locale === 'en' ? $en : $fr;

    // ── Données par défaut (le contrôleur fournira réellement ces valeurs) ──
    $medias      ??= collect();
    $categories  ??= collect();
    $collections ??= collect();
    $facettes    ??= collect();
    $annees      ??= ['min' => null, 'max' => null, 'bars' => []];
    $qs          ??= collect([
        ['q' => 'route',     'fr' => 'route',     'en' => 'road'],
        ['q' => 'pont',      'fr' => 'pont',      'en' => 'bridge'],
        ['q' => 'école',     'fr' => 'école',     'en' => 'school'],
        ['q' => 'riz',       'fr' => 'riz',       'en' => 'rice'],
        ['q' => 'formation', 'fr' => 'formation', 'en' => 'training'],
        ['q' => 'forum',     'fr' => 'forum',     'en' => 'forum'],
        ['q' => 'jeunes',    'fr' => 'jeunes',    'en' => 'youth'],
        ['q' => 'femmes',    'fr' => 'femmes',    'en' => 'women'],
    ]);
    $tags        ??= collect();
    $stats       ??= [
        ['valeur' => 0,    'fr' => 'médias',      'en' => 'media'],
        ['valeur' => 0,    'fr' => 'collections', 'en' => 'collections'],
        ['valeur' => '—',  'fr' => 'période',     'en' => 'period'],
    ];
@endphp

@extends('layouts.app')

@section('meta_title')
    {{ $t('Galerie — OAAT', 'Gallery — OAAT') }}
@endsection

@section('meta_description')
    {{ $t("Médiathèque de l'OAAT : toutes les images du site et celles que l'administration y ajoute, avec filtres, collections, sélection et visionneuse.",
          'OAAT media library: every image on the website, plus those added by the administration, with filters, collections, selection and a viewer.') }}
@endsection

@section('content')

    {{-- ═══════ HÉRO : titre, compteurs, actions, recherches fréquentes ═════ --}}
    @include('partials._galerie_hero', [
        'stats'  => $stats,
        'qs'     => $qs,
        'locale' => $locale,
    ])

    {{-- ═══════ Bandeau « Collections » ════════════════════════════════════ --}}
    @include('partials._galerie_collections', [
        'collections' => $collections,
        'locale'      => $locale,
    ])

    {{-- ═══════ Barre d'outils + onglets catégories ════════════════════════ --}}
    @include('partials._galerie_barre', [
        'categories' => $categories,
        'total'      => $medias->count(),
        'locale'     => $locale,
    ])

    {{-- ═══════ Corps : facettes (tiroir + colonne) ════════════════════════ --}}
    <div class="mx-auto grid max-w-7xl gap-10 px-5 py-10 lg:grid-cols-[17rem_minmax(0,1fr)] lg:px-8 lg:py-12">

        {{-- Facettes (aside #fb) --}}
        @include('partials._galerie_facettes', [
            'facettes' => $facettes,
            'annees'   => $annees,
            'locale'   => $locale,
        ])

        <div class="min-w-0">
            {{-- Chips des filtres actifs (#chips) --}}
            @include('partials._galerie_chips', [
                'categories' => $categories,
                'facettes'   => $facettes,
                'locale'     => $locale,
            ])

            {{-- Grille des médias + état vide + pagination --}}
            @include('partials._galerie_grille', [
                'medias' => $medias,
                'locale' => $locale,
            ])
        </div>
    </div>

    {{-- ═══════ Dock de sélection (bas d'écran, #tray) ═════════════════════ --}}
    @include('partials._galerie_tray', ['locale' => $locale])

    {{-- ═══════ Aide clavier (modal #help) ═════════════════════════════════ --}}
    @include('partials._galerie_aide', ['locale' => $locale])

    {{-- ═══════ Visionneuse plein écran (#vw) ══════════════════════════════ --}}
    @include('partials._galerie_visionneuse', [
        'medias' => $medias,
        'tags'   => $tags,
        'locale' => $locale,
    ])

    {{-- Champ invisible lu par le JS pour copier dans le presse-papiers (#cpi) --}}
    <input id="cpi" class="sr-only" tabindex="-1" aria-hidden="true" readonly>

@stop

@push('scripts')
    <script src="{{ asset('js/oaat.js') }}" defer></script>
@endpush