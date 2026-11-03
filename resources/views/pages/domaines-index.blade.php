{{-- pages/domaines-index.blade.php — « Domaines d'intervention ».
     Données préparées par DomaineController@index : $sections (une structure par
     domaine : nom, intro, interventions, formations, realises, aFinancer, periode,
     bailleursLabel, implementeursLabel, parcours), $domaines (rail), $compteurs,
     $competences (CMS), $pageDomaines (SEO/héro), $locale, $teinte, $icone, $railLabel . --}}
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
    @foreach ($sections as $sec)

        <section id="{{ $sec->domaine->slug }}" class="scroll-mt-32 {{ $loop->odd ? 'bg-mist' : 'bg-white' }} py-20">
            <div class="mx-auto grid max-w-7xl gap-12 px-5 lg:grid-cols-12 lg:px-8">

                {{-- ▸ Colonne gauche : résumé sticky --}}
                <div class="lg:col-span-4" data-r>
                    <div class="lg:sticky lg:top-40">
                        <span class="grid h-16 w-16 place-items-center bg-lake text-white">
                            <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="{{ $icone($sec->domaine) }}"/>
                            </svg>
                        </span>

                        <h2 class="mt-6 font-serif text-3xl font-semibold text-lake md:text-4xl">{{ $sec->nom }}</h2>

                        <dl class="mt-6 grid grid-cols-2 gap-px border border-line bg-line">
                            <div class="bg-white p-4">
                                <dt class="text-xs text-ink/60">{{ $t('Projets réalisés', 'Completed projects') }}</dt>
                                <dd class="mt-1 font-serif text-3xl font-semibold text-lake">{{ $sec->realises->count() }}</dd>
                            </div>
                            <div class="bg-white p-4">
                                <dt class="text-xs text-ink/60">{{ $t('Projets à financer', 'Projects seeking funding') }}</dt>
                                <dd class="mt-1 font-serif text-3xl font-semibold text-lake">{{ $sec->aFinancer->count() }}</dd>
                            </div>
                        </dl>

                        @if ($sec->periode !== '')
                            <p class="mt-4 text-sm text-ink/70"><b>{{ $t('Période', 'Period') }}</b> : {{ $sec->periode }}</p>
                        @endif

                        {{-- S2 — les deux groupes, sans « ou » --}}
                        @if ($sec->bailleursLabel !== '')
                            <p class="mt-2 text-sm text-ink/70"><b>{{ $t('Bailleurs', 'Funders') }}</b> : {{ $sec->bailleursLabel }}</p>
                        @endif
                        @if ($sec->implementeursLabel !== '')
                            <p class="mt-2 text-sm text-ink/70"><b>{{ $t('Partenaires de mise en œuvre', 'Implementing Partners') }}</b> : {{ $sec->implementeursLabel }}</p>
                        @endif
                        {{-- ▸ Lien vers la fiche du domaine --}}
                        <a href="{{ route('domaines.show', [$locale, $sec->domaine->slug]) }}"
                        class="mt-6 inline-flex w-full items-center justify-center gap-2 bg-lake px-6 py-3.5 font-semibold text-white transition duration-300 hover:-translate-y-0.5 hover:bg-ochre hover:shadow-xl"
                        aria-label="{{ $t('Voir la page du domaine : ', 'View the domain page: ') }}{{ $sec->nom }}">
                            {{ $t('Voir la page du domaine', 'View the domain page') }} <span aria-hidden="true">→</span>
                        </a>

                    </div>
                </div>

                {{-- ▸ Colonne droite : illustration, interventions, formations, projets --}}
                <div class="lg:col-span-8" data-r>




                {{-- ▸ Bandeau : objectif du domaine sur fond dégradé --}}
                <div class="relative flex min-h-56 items-center justify-center overflow-hidden md:min-h-72" data-r>
                    <div class="absolute inset-0" aria-hidden="true"
                        style="background-image: linear-gradient(to bottom right, hsl({{ $teinte($sec->domaine) }} 45% 26%), hsl({{ $teinte($sec->domaine) }} 50% 44%));">
                    </div>
                    {{-- Ligne décoratives --}}
                    <svg class="pointer-events-none absolute inset-0 h-full w-full" viewBox="0 0 800 300"
                        preserveAspectRatio="none" aria-hidden="true" focusable="false">
                        <path d="M0 220c120-60 200 40 340-10s220-80 460 0v90H0z" fill="#fff" opacity=".08"/>
                        <path d="M0 260c150-50 260 30 400-10s250-40 400 10v40H0z" fill="#fff" opacity=".1"/>
                    </svg>
                    @if ($sec->objectives !== '')
                        <p class="relative max-w-3xl p-8 font-serif text-xl leading-relaxed text-white md:p-10 md:text-2xl text-center">
                            {{ $sec->objectives }}
                        </p>
                    @endif

                </div>


                    @if ($sec->interventions !== [])
                        <h3 class="mt-10 text-sm font-semibold uppercase tracking-wider text-moss">{{ $t('Nos interventions', 'Our interventions') }}</h3>
                        <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                            @foreach ($sec->interventions as $intervention)
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

                    @if ($sec->formations !== [])
                        <h3 class="mt-10 text-sm font-semibold uppercase tracking-wider text-moss">{{ $t('Formations reçues de nos partenaires', 'Training received from our partners') }}</h3>
                        <ul class="mt-4 space-y-2">
                            @foreach ($sec->formations as $formation)
                                <li class="border-l-4 border-ochre bg-white px-4 py-3 text-sm leading-relaxed">{{ $formation }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <h3 class="mt-10 text-sm font-semibold uppercase tracking-wider text-moss">{{ $t('Projets associés', 'Related projects') }} ({{ $sec->realises->count() + $sec->aFinancer->count() }})</h3>
                    @if ($sec->parcours->isNotEmpty())
                        <ul class="mt-4 divide-y divide-line border border-line bg-white">
                            @foreach ($sec->parcours as $row)
                                <li class="flex items-center gap-4 p-4">
                                    <span class="w-20 shrink-0 text-sm font-bold {{ $row->afinancer ? 'text-ochreDark' : 'text-ink/60' }}">
                                        {{ $row->afinancer ? $t('À financer', 'Seeking funding') : $row->annee }}
                                    </span>
                                    <span class="grow">
                                        {{-- S3 — lien vers la fiche projet --}}
                                        <a href="{{ $row->url }}" class="block transition hover:opacity-75">
                                            <b class="block text-lake">{{ $row->titre }}</b>
                                        </a>
                                        @if ($row->funderLabel !== '—' || $row->implLabel !== '—')
                                            <span class="mt-0.5 block text-xs text-ink/60">
                                                @if ($row->funderLabel !== '—')
                                                    {{ $t('Bailleurs', 'Funders') }} : {{ $row->funderLabel }}
                                                @endif
                                                @if ($row->funderLabel !== '—' && $row->implLabel !== '—')
                                                    ·
                                                @endif
                                                @if ($row->implLabel !== '—')
                                                    {{ $t('Mise en œuvre', 'Implementation') }} : {{ $row->implLabel }}
                                                @endif
                                            </span>
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
