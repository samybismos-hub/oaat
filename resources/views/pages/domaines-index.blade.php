{{-- pages/domaines-index.blade.php — « Domaines d'intervention ».
     Vue intégrée au layout commun : topbar, en-tête et pied de page proviennent
     de layouts/app.blade.php. Contenu rendu côté serveur (Blade), sans injection
     HTML ni traduction par JavaScript. --}}
@extends('layouts.app')

@php
    // $t et $locale doivent être définis avant toute @section qui les utilise (cf. projets-index).
    $locale ??= 'fr';
    $t = fn($fr, $en) => $locale === 'en' ? $en : $fr;
@endphp


{{-- ─── SEO : titre & description dynamiques ─────────────────── --}}
@section('meta_title')
    {{ $pageDomaines?->seoTitle($locale) ?: $t('Domaines d’intervention — OAAT', 'Areas of intervention — OAAT') }}
@endsection

@section('meta_description')
    {{ $pageDomaines?->seoDescription($locale) ?: $t(
    "Les neuf domaines d'intervention de l'OAAT : santé, sécurité alimentaire, logistique, éducation, eau et assainissement, genre, protection, environnement et formation aux métiers.",
    'The nine areas of intervention of OAAT: health, food security, logistics, education, water and sanitation, gender, protection, environment and vocational training.',
    )
}}
@endsection

@push('head')
    <style>
        .animate-dash { animation: dash 5s ease-out forwards; }
        .motion-reduce .animate-dash { animation: none; }
    </style>
@endpush

@php

    // Année d'un projet (réalisation), sinon null.
    $anneeProjet = static fn ($p) => $p->end_date?->year ?? $p->start_date?->year;

    // Libellé court du rail pour un domaine (fallback = nom traduit, puis slug).
    $railLabel = static function ($d) use ($locale) {
        $label = config('oaat.domaine_page.rail.'.$d->slug);

        return $label ?: (string) ($d->getTranslation('name', $locale) ?: $d->slug);
    };

    // Pictogramme SVG (design figé dans config), chaîne vide si jamais absent.
    $icone = static fn ($d) => (string) (config('oaat.domaine_page.icons.'.$d->slug) ?? '');

    // Teinte HSL (design figé), défaut 200 si jamais absent.
    $teinte = static fn ($d) => (int) (config('oaat.domaine_page.tints.'.$d->slug) ?? 200);

    // Libellé des bailleurs d'un projet (funder puis, à défaut, tous les
    // partenaires) ; chaîne vide si aucun partenaire.
    $bailleurProjet = static function ($p) use ($locale) {
        $label = trim((string) $p->funderLabel($locale));

        if ($label === '' || $label === '—') {
            $label = trim((string) $p->partnersLabel($locale));
        }

        return $label === '—' ? '' : $label;
    };

    // ─── Contenu CMS de la page (slug 'domaines') : héro, compétences, CTA ───
    $pg = $pageDomaines ?? null;


    $competences = $pg ? (array) ($pg->getTranslation('competences', $locale) ?? []) : [];
    if ($competences === [] && $pg) {
        $competences = (array) ($pg->getTranslation('competences', 'fr') ?? []);
    }

@endphp

@section('content')
    {{-- ═══════════════════════════════════════════════════════════════════
    H E R O
═══════════════════════════════════════════════════════════════════ --}}
@include('partials._hero_simple', [
    'page'   => $pageDomaines,
    'stats'  => [
        ['valeur' => $compteurs['domaines'] ?? 0,   'fr' => 'domaines d’intervention',          'en' => 'areas of intervention'],
        ['valeur' => $compteurs['projets'] ?? 0, 'fr' => 'projets répertoriés',  'en' => 'listed projects'],
        ['valeur' => $compteurs['zones'] ?? 0,      'fr' => 'zones en Sud et Nord-Kivu',           'en' => 'zones in South and North Kivu'],

    ],
    'locale' => $locale,
])

    {{-- ═══ Barre de navigation interne (ancres par domaine) ══════════════ --}}
    <div class="sticky top-[60px] z-30 border-b border-line bg-white/95 backdrop-blur">
        <nav id="rl" class="mx-auto flex max-w-7xl overflow-x-auto px-5 lg:px-8" aria-label="Domaines">
            @foreach ($domaines as $domaine)
                <a href="#{{ $domaine->slug }}"
                   class="flex shrink-0 items-center gap-2 border-b-2 px-3 py-3.5 text-sm font-medium transition {{ $loop->first ? 'border-ochre text-lake' : 'border-transparent text-ink/70' }}">
                    <span class="[&_svg]:h-5 [&_svg]:w-5">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="{{ $icone($domaine) }}"/>
                        </svg>
                    </span>
                    <span class="hidden lg:inline">{{ $railLabel($domaine) }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    {{-- ═══ Sections par domaine ═════════════════════════════════════════ --}}
    @foreach ($domaines as $domaine)
        @php
            $projetsDuDomaine = $domaine->projects; // publiés + partenaires (chargés par le contrôleur)

            $realises  = $projetsDuDomaine->filter(fn ($p) => $p->statut_court === 'd')
                ->sortByDesc(fn ($p) => $anneeProjet($p) ?? 0);
            $aFinancer = $projetsDuDomaine->filter(fn ($p) => $p->statut_court === 'w')->values();
            $parcours  = $realises->concat($aFinancer);

            $annees    = $realises->map(fn ($p) => $anneeProjet($p))->filter()->values();
            $periode   = $annees->isNotEmpty() ? $annees->min() . '–' . $annees->max() : '';

            $bailleurs = collect($projetsDuDomaine)
                ->flatMap(fn ($p) => preg_split('~[\/,]~', $bailleurProjet($p)))
                ->map(fn ($seg) => trim($seg))
                ->filter(fn ($seg) => $seg !== '' && $seg !== 'P.F.')
                ->unique()
                ->values()
                ->join(', ');

            $nom           = (string) ($domaine->getTranslation('name', $locale) ?: $domaine->slug);
            $intro         = (string) ($domaine->getTranslation('intro', $locale)
                ?: $domaine->getTranslation('objectives', $locale)
                ?: '');
            $interventions = (array) ($domaine->getTranslation('interventions', $locale) ?? []);
            $formations    = (array) ($domaine->getTranslation('formations', $locale) ?? []);
        @endphp

        <section id="{{ $domaine->slug }}" class="scroll-mt-32 {{ $loop->odd ? 'bg-mist' : 'bg-white' }} py-20">
            <div class="mx-auto grid max-w-7xl gap-12 px-5 lg:grid-cols-12 lg:px-8">

                {{-- ▸ Colonne gauche : résumé sticky --}}
                <div class="lg:col-span-4" data-r>
                    <div class="lg:sticky lg:top-40">
                        <span class="grid h-16 w-16 place-items-center bg-lake text-white">
                            <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="{{ $icone($domaine) }}"/>
                            </svg>
                        </span>

                        <h2 class="mt-6 font-serif text-3xl font-semibold text-lake md:text-4xl">{{ $nom }}</h2>

                        @if ($intro !== '')
                            <p class="mt-3 leading-relaxed text-ink/70">{{ $intro }}</p>
                        @endif

                        <dl class="mt-6 grid grid-cols-2 gap-px border border-line bg-line">
                            <div class="bg-white p-4">
                                <dt class="text-xs text-ink/60">{{ $t('Projets réalisés', 'Completed projects') }}</dt>
                                <dd class="mt-1 font-serif text-3xl font-semibold text-lake">{{ $realises->count() }}</dd>
                            </div>
                            <div class="bg-white p-4">
                                <dt class="text-xs text-ink/60">{{ $t('Projets à financer', 'Projects seeking funding') }}</dt>
                                <dd class="mt-1 font-serif text-3xl font-semibold text-lake">{{ $aFinancer->count() }}</dd>
                            </div>
                        </dl>

                        @if ($periode !== '')
                            <p class="mt-4 text-sm text-ink/70"><b>{{ $t('Période', 'Period') }}</b> : {{ $periode }}</p>
                        @endif
                        @if ($bailleurs !== '')
                            <p class="mt-2 text-sm text-ink/70"><b>{{ $t('Bailleurs et partenaires', 'Funders and partners') }}</b> : {{ $bailleurs }}</p>
                        @endif
                    </div>
                </div>

                {{-- ▸ Colonne droite : illustration, interventions, formations, projets --}}
                <div class="lg:col-span-8" data-r>
                    {{-- Illustration (dégradé + libellé, rendu côté serveur) --}}
                    <figure class="overflow-hidden" data-r>
                        <svg viewBox="0 0 800 300" preserveAspectRatio="xMidYMid slice" class="h-56 w-full md:h-72"
                             role="img" aria-label="{{ $nom }}">
                            <defs>
                                <linearGradient id="degrade-{{ $domaine->slug }}" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0" stop-color="hsl({{ $teinte($domaine) }} 45% 26%)"/>
                                    <stop offset="1" stop-color="hsl({{ $teinte($domaine) }} 50% 44%)"/>
                                </linearGradient>
                            </defs>
                            <rect width="800" height="300" fill="url(#degrade-{{ $domaine->slug }})"/>
                            <text x="400" y="156" fill="#fff" fill-opacity=".85" font-family="sans-serif" font-size="24" text-anchor="middle">{{ $nom }}</text>
                            <text x="400" y="182" fill="#fff" fill-opacity=".6" font-family="sans-serif" font-size="15" text-anchor="middle">Image d’illustration</text>
                        </svg>
                    </figure>

                    @if ($interventions !== [])
                        <h3 class="mt-10 text-sm font-semibold uppercase tracking-wider text-moss">{{ $t('Nos interventions', 'Our interventions') }}</h3>
                        <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                            @foreach ($interventions as $intervention)
                                <li class="flex gap-3 border border-line bg-white p-4 text-sm leading-relaxed">
                                    <span class="mt-0.5 shrink-0 text-moss">
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M5 12l5 5 9-10"/>
                                        </svg>
                                    </span>
                                    <span>{{ $intervention }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if ($formations !== [])
                        <h3 class="mt-10 text-sm font-semibold uppercase tracking-wider text-moss">{{ $t('Formations reçues de nos partenaires', 'Training received from our partners') }}</h3>
                        <ul class="mt-4 space-y-2">
                            @foreach ($formations as $formation)
                                <li class="border-l-4 border-ochre bg-white px-4 py-3 text-sm leading-relaxed">{{ $formation }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <h3 class="mt-10 text-sm font-semibold uppercase tracking-wider text-moss">{{ $t('Projets associés', 'Related projects') }} ({{ $projetsDuDomaine->count() }})</h3>
                    @if ($projetsDuDomaine->isNotEmpty())
                        <ul class="mt-4 divide-y divide-line border border-line bg-white">
                            @foreach ($parcours as $projet)
                                @php
                                    $anneeP    = (string) $anneeProjet($projet);
                                    $aFinP     = $projet->statut_court === 'w';
                                    $titreP    = trim((string) $projet->getTranslation('title', $locale));
                                    $bailleurP = $bailleurProjet($projet);
                                @endphp
                                <li class="flex items-center gap-4 p-4">
                                    <span class="w-20 shrink-0 text-sm font-bold {{ $aFinP ? 'text-ochreDark' : 'text-ink/60' }}">
                                        {{ $aFinP ? $t('À financer', 'Seeking funding') : $anneeP }}
                                    </span>
                                    <span class="grow">
                                        <b class="block text-lake">{{ $titreP }}</b>
                                        @if ($bailleurP !== '')
                                            <span class="text-xs text-ink/60">{{ $bailleurP }}</span>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-4 border border-dashed border-line bg-white p-5 text-sm text-ink/65">
                            {{ $t('Aucun projet de ce domaine n’est répertorié à ce jour dans le portefeuille présenté.', 'No project in this area is currently listed in the presented portfolio.') }}
                        </p>
                    @endif
                </div>

            </div>
        </section>
    @endforeach

    {{-- ═══ Compétences transversales ═════════════════════════════════════ --}}
    <section id="transversal" class="scroll-mt-32 bg-deep py-20 text-white">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <div data-r>
                <h2 class="font-serif text-4xl font-semibold">{{ $t('Compétences transversales', 'Cross-cutting skills') }}</h2>
                    <p class="mt-4 max-w-2xl text-white/70">{{ $t('Des formations reçues de nos partenaires et bailleurs, utiles à tous les domaines.', 'Training received from our partners and funders, useful across all areas.') }}</p>
            </div>

            <ul class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($competences as $item)
                    <li data-r class="border border-white/15 bg-white/5 p-6 transition duration-300 hover:border-ochre hover:bg-white/10">
                        <h3 class="font-serif text-lg font-semibold">{{ $item['titre'] ?? '' }}</h3>
                        @if (! empty($item['detail'] ?? ''))
                            <p class="mt-2 text-sm text-white/65">{{ $item['detail'] }}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ═══ Bandeau d'appel à l'action ═════════════════════════════════════ --}}
    <section class="bg-lake text-white">
        <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 px-5 py-16 lg:flex-row lg:items-center lg:px-8">
            <div data-r>
                <h2 class="font-serif text-3xl font-semibold md:text-4xl">{{ $t('Un besoin dans l’un de ces domaines ?', 'Need support in one of these areas?') }}</h2>
                    <p class="mt-3 max-w-xl text-white/80">{{ $t('Décrivez-nous votre projet, parlons-en.', 'Tell us about your project — let’s talk.') }}</p>
            </div>
            <div class="flex flex-wrap gap-3" data-r>
                <a href="{{ route('contact', ['locale' => $locale]) }}"
                   class="relative overflow-hidden before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%] bg-ochre px-7 py-3.5 font-semibold text-deep transition hover:-translate-y-0.5 hover:shadow-xl">
                    {{ $t('Nous contacter', 'Contact us') }}
                </a>
                <a href="{{ route('projets.index', ['locale' => $locale]) }}"
                   class="border border-white/40 px-7 py-3.5 font-semibold transition hover:bg-white hover:text-deep">
                    {{ $t('Voir les projets', 'See the projects') }}
                </a>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script src="{{ asset('js/oaat.js') }}" defer></script>
@endpush
