{{-- projets-index.blade.php — Page index des projets OAAT --}}
@extends('layouts.app')

@php
    // $t et $locale doivent être définis avant toute @section qui les utilise.
    // Le layout les définit aussi dans son @php, mais l'évaluation des sections
    // a lieu avant l'exécution du @php.
    $locale ??= 'fr';
    $t = fn($fr, $en) => $locale === 'en' ? $en : $fr;
@endphp

@section('meta_title', $t('Projets — OAAT', 'Projects — OAAT'))
@section('meta_description', $t(
    "Tous les projets de l'OAAT avec leurs détails : réalisations depuis 1994 et projets disponibles en attente de financement.",
    'All OAAT projects with details: achievements since 1994 and projects awaiting funding.',
))
@push('head')
    <style>
        .animate-dash { animation: dash 5s ease-out forwards; }
        .motion-reduce .animate-dash { animation: none; }
    </style>
@endpush

@php
    $t   = fn($fr, $en) => $locale === 'en' ? $en : $fr;
    $gMap = [
        'sante'                      => '0,45%,26%|50,50%,44%',
        'education'                  => '200,45%,26%|240,50%,44%',
        'eau-hygiene-assainissement' => '195,45%,26%|210,50%,44%',
        'alimentation'               => '95,45%,26%|135,50%,44%',
        'logistique'                 => '30,45%,26%|70,70%,44%',
        'genre'                      => '330,45%,26%|370,50%,44%',
        'protection'                 => '280,45%,26%|310,50%,44%',
        'formation-professionnelle'  => '0,45%,26%|20,50%,44%',
        'environnement'              => '160,45%,26%|185,50%,44%',
    ];
    $gradHsl = fn($slug) => $gMap[$slug] ?? '0,45%,26%|50,50%,44%';
    $qP = array_filter(['statut' => $statut, 'domaine' => $domaine, 'q' => $recherche]);
    $mk = fn($ov) => route('projets.index', ['locale' => $locale] + array_merge($qP, $ov));
    $enAtt = $projets->filter(fn($p) => $p->status->value === 'awaiting_funding');
    $realises = $projets->filter(fn($p) => in_array($p->status->value, ['completed', 'ongoing']));
@endphp
@section('content')
{{-- ═══════════════════════════════════════════════════════════════════
    H E R O
═══════════════════════════════════════════════════════════════════ --}}
@include('partials._hero', [
    'page' => $pageProjets,
    'locale' => $locale
    ])
<!--section id="top" class="relative overflow-hidden bg-gradient-to-br from-deep via-deep via-[#3B5D8A] to-[#1a4d7a] text-white">
    <svg class="pointer-events-none absolute inset-0 z-0 h-full w-full opacity-10" viewBox="0 0 1440 800"
         preserveAspectRatio="none" fill="none" stroke="#E3A82B" stroke-width="1.5" aria-hidden="true">
        <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"
              class="animate-dash motion-reduce:animate-none"
              d="M-20 600C220 520 380 690 640 640S1060 580 1460 560"/>
        <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"
              class="animate-dash motion-reduce:animate-none" style="animation-delay:.5s"
              d="M-20 650C240 580 400 730 680 650S1080 540 1460 620"/>
        <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"
              class="animate-dash motion-reduce:animate-none" style="animation-delay:1s"
              d="M-20 700C260 640 420 770 700 700S1100 600 1460 660"/>
        <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"
              class="animate-dash motion-reduce:animate-none" style="animation-delay:1.5s"
              d="M-20 550C200 470 360 640 620 550S1040 430 1460 510"/>
    </svg>
    <div class="relative z-10 mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-24">

        <h1 class="mt-6 font-serif text-5xl font-semibold leading-tight md:text-6xl" data-r>
            {{ $t('Projets', 'Projects') }}
        </h1>
        <p class="mt-5 max-w-2xl text-lg text-white/75" data-r>
            {!! Str::markdown($pageProjets?->getTranslation('body', $locale) ?? $t(
                "Trente ans de réalisations aux côtés des communautés, avec les agences des Nations Unies et des ONG internationales, et des projets prêts à être financés.",
                "Thirty years of achievements alongside communities, with UN agencies and international NGOs, and projects ready for funding."
            )) !!}
        </p>
        <dl class="mt-12 grid max-w-4xl grid-cols-2 gap-8 border-t border-white/15 pt-8 md:grid-cols-4">
            <div data-r>
                <dd class="font-serif text-4xl font-semibold">
                    <span data-n="{{ $compteurs['realises'] ?? 0 }}">{{ $compteurs['realises'] ?? 0 }}</span>
                </dd>
                <dt class="mt-1 text-sm text-white/65">{{ $t('projets réalisés', 'projects completed') }}</dt>
            </div>
            <div data-r>
                <dd class="font-serif text-4xl font-semibold">
                    <span data-n="{{ $compteurs['en_attente'] ?? 0 }}">{{ $compteurs['en_attente'] ?? 0 }}</span>
                </dd>
                <dt class="mt-1 text-sm text-white/65">{{ $t('projets prêts à financer', 'projects ready to fund') }}</dt>
            </div>
            <div data-r>
                <dd class="font-serif text-4xl font-semibold">
                    <span data-n="{{ $compteurs['domaines'] ?? 0 }}">{{ $compteurs['domaines'] ?? 0 }}</span>
                </dd>
                <dt class="mt-1 text-sm text-white/65">{{ $t('domaines d\'intervention', 'areas of intervention') }}</dt>
            </div>
            <div data-r>
                <dd class="font-serif text-4xl font-semibold">
                    <span data-n="{{ $compteurs['zones'] ?? 0 }}">{{ $compteurs['zones'] ?? 0 }}</span>
                </dd>
                <dt class="mt-1 text-sm text-white/65">{{ $t('zones couvertes', 'zones covered') }}</dt>
            </div>
        </dl>
    </div>
</!--section-->
{{-- ═══════════════════════════════════════════════════════════════════
    F I L T R E S   +   G R I L L E
═══════════════════════════════════════════════════════════════════ --}}
<section class="mx-auto max-w-7xl px-5 py-14 lg:px-8">
    <div class="grid gap-10 lg:grid-cols-[17rem_1fr]">

        {{-- ═══ SIDEBAR FILTRES ═══ --}}
        <aside class="lg:sticky lg:top-[84px] lg:self-start">
            <button id="ftg" aria-expanded="false" class="flex w-full items-center justify-between border border-line bg-white px-4 py-3 font-semibold text-lake lg:hidden">
                <span>{{ $t('Filtres et recherche', 'Filters and search') }}</span>
                <span aria-hidden="true">▾</span>
            </button>
            <div id="fb"
                 class="mt-4 hidden space-y-7 border border-line bg-white p-5 lg:mt-0 lg:block lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto">

                {{-- 1. Recherche enrobé dans un form GET pour soumettre ?q= --}}
                <form method="GET" action="{{ route('projets.index', ['locale' => $locale]) }}">
                    <input type="hidden" name="statut" value="{{ $statut }}">
                    <input type="hidden" name="domaine" value="{{ $domaine }}">
                    <label class="relative block">
                        <svg viewBox="0 0 24 24" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink/50" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="M21 21l-4-4"/>
                        </svg>
                        <input id="q" type="search" wire:model.live="search" name="q" aria-label="Rechercher"
                               placeholder="{{ $t('Rechercher un projet', 'Search a project') }}"
                               value="{{ $recherche }}"
                               class="w-full border border-line py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-lake focus:ring-4 focus:ring-lake/15">
                    </label>
                </form>

                {{-- 2. Statuts --}}
                @php
                    $stLabels = [
                        'all'              => $t('Tous les projets', 'All projects'),
                        'completed'        => $t('Réalisés', 'Completed'),
                        'awaiting_funding' => $t('À financer', 'To fund'),
                        'ongoing'          => $t('En cours', 'Ongoing'),
                    ];
                @endphp
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-ink/60" data-en="Status">
                        {{ $t('Statut', 'Status') }}
                    </h3>
                    <div id="fst" class="mt-3 flex flex-col gap-1">
                        @foreach ($statuts as $st)
                            <a href="{{ $mk(['statut' => $st['cle'] === 'all' ? '' : $st['cle']]) }}"
                               class="flex items-center border-l-2 px-3 py-2 text-left text-sm transition
                                      {{ ($st['cle'] === 'all' && blank($statut)) || $statut === $st['cle'] ? 'border-ochre bg-mist font-semibold text-lake' : 'border-transparent text-ink/75 hover:bg-mist' }}">
                                <span>{{ $stLabels[$st['cle']] ?? $st['cle'] }}</span>
                                @if ($st['cle'] !== 'all' || $compteurs['total'] > 0)
                                    <span class="ml-auto text-xs text-ink/50">({{ $st['count'] }})</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Domaines --}}
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-ink/60" data-en="Area">
                        {{ $t('Domaine', 'Area') }}
                    </h3>
                    <div id="fdm" class="mt-2 space-y-1">
                        <a href="{{ $mk(['domaine' => '']) }}"
                           class="block py-1.5 text-sm transition
                                  {{ blank($domaine) ? 'font-semibold text-lake' : 'text-ink/75 hover:text-lake' }}">
                            <span data-en="All areas">{{ $t('Tous', 'All') }}</span>
                        </a>
                        @foreach ($domaines as $d)
                            <a href="{{ $mk(['domaine' => $d->slug]) }}"
                           data-d="{{ $d->slug }}"
                           class="block w-full py-1.5 text-left text-sm transition
                                  {{ $domaine === $d->slug ? 'font-semibold text-lake' : 'text-ink/75 hover:text-lake' }}">
                                <span class="flex justify-between gap-2">
                                    <span>{{ $d->getTranslation('name', $locale) }}</span>
                                    <span class="ml-auto text-xs text-ink/50">{{ $d->projects_count }}</span>
                                </span>
                                <i class="mt-1 block h-1 bg-mist"><b class="block h-full @php echo $d->barWidth >= 100 ? 'bg-ochre' : 'bg-lake/50'; @endphp" style="width: {{ $d->barWidth }}%"></b></i>
                            </a>
                        @endforeach
                    </div>
                </div>
{{-- 4. Périodes --}}
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-ink/60" data-en="Period">
                        {{ $t('Période', 'Period') }}
                    </h3>
                    <div id="fpe" class="mt-3 flex flex-wrap gap-2">
                        @foreach ($periodes as $i => $p)
                            <a href="{{ $mk(['periode' => $i]) }}"
                           class="border px-3 py-1.5 text-xs font-semibold transition
                                  {{ $periode == (string) $i ? 'border-lake text-lake' : 'border-line text-ink/70 hover:border-lake hover:text-lake' }}">
                                {{ $p['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- 5. Reset --}}
                <button id="frs"
                        onclick="window.location.href='{{ route('projets.index', ['locale' => $locale]) }}'"
                        class="text-sm font-semibold text-lake underline-offset-4 hover:underline"
                        data-en="Reset filters">
                    {{ $t('Réinitialiser les filtres', 'Reset filters') }}
                </button>
            </div>
        </aside>

        {{-- ═══ CONTENU PRINCIPAL ═══ --}}
        <div>

            {{-- Barre outils : compteur + tri --}}
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <p id="rc" class="text-sm text-ink/65" aria-live="polite">
                    <span data-n="{{ $projets->count() }}">{{ $projets->count() }}</span>
                    {{ $t('projet·s', 'project·s') }}
                </p>
                <label class="flex items-center gap-2 text-sm text-ink/70">
                    <span data-en="Sort by">{{ $t('Trier par', 'Sort by') }}</span>
                    <select id="fs" aria-label="Tri"
                            class="border border-line bg-white px-3 py-2 text-sm outline-none transition focus:border-lake focus:ring-4 focus:ring-lake/15">
                        <option value="rec" data-en="Recommended">{{ $t('Recommandé', 'Recommended') }}</option>
                        <option value="new" data-en="Newest">{{ $t('Plus récents', 'Newest') }}</option>
                        <option value="old" data-en="Oldest">{{ $t('Plus anciens', 'Oldest') }}</option>
                        <option value="bud" data-en="Highest budget">{{ $t('Budget décroissant', 'Highest budget') }}</option>
                    </select>
                </label>
            </div>
{{-- ═══ GRILLE ═══ --}}
            <div id="gr" class="flex flex-col gap-6">

                {{-- ▸ Section En attente de financement --}}
                @if ($enAtt->isNotEmpty())
                    <h2 id="gw" class="flex items-baseline gap-3 border-b border-line pb-3 pt-4 font-serif text-2xl font-semibold text-lake">
                        {{ $t('En attente de financement', 'Awaiting funding') }}
                        <span class="font-sans text-base font-normal text-ink/50">({{ $enAtt->count() }})</span>
                    </h2>
                    @foreach ($enAtt as $projet)
                        @include('partials._carte_projet', ['projet' => $projet])
                    @endforeach
                @endif

                {{-- ▸ Section Réalisations --}}
                @if ($realises->isNotEmpty())
                    <h2 id="gd" class="flex items-baseline gap-3 border-b border-line pb-3 pt-4 font-serif text-2xl font-semibold text-lake">
                        {{ $t('Nos réalisations', 'Our achievements') }}
                        <span class="font-sans text-base font-normal text-ink/50">({{ $realises->count() }})</span>
                    </h2>
                    @foreach ($realises as $projet)
                        @include('partials._carte_projet', ['projet' => $projet])
                    @endforeach
                @endif

                {{-- ▸ État vide --}}
                @if ($projets->isEmpty())
                    <p id="em" class="border border-dashed border-line p-10 text-center text-ink/65">
                        <span data-en="No project matches your search.">
                            {{ $t("Aucun projet ne correspond à votre recherche.", "No project matches your search.") }}
                        </span>
                        <a href="{{ route('projets.index', ['locale' => $locale]) }}"
                           class="font-semibold text-lake underline" data-en="Reset">
                            {{ $t('Réinitialiser', 'Reset') }}
                        </a>
                    </p>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
    C T A   B A N D E A U
═══════════════════════════════════════════════════════════════════ --}}
<section class="bg-lake text-white">
    <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 px-5 py-16 lg:flex-row lg:items-center lg:px-8">
        <div data-r>
            <h2 class="font-serif text-3xl font-semibold md:text-4xl"
                data-en="Fund or co-design a project">
                {{ $t('Financer ou co-construire un projet', 'Fund or co-design a project') }}
            </h2>
            <p class="mt-3 max-w-xl text-white/80"
               data-en="Six projects are available and awaiting funding. Let's talk.">
                {{ $t('Six projets sont disponibles et attendent un financement. Parlons-en.', 'Six projects are available and awaiting funding. Let\'s talk.') }}
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('contact', ['locale' => $locale]) }}"
               class="relative overflow-hidden before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%] bg-ochre px-7 py-3.5 font-semibold text-deep transition hover:-translate-y-0.5 hover:shadow-xl"
               data-en="Contact us">
                {{ $t('Nous contacter', 'Contact us') }}
            </a>
            <a href="{{ route('organisation', ['locale' => $locale]) }}"
               class="border border-white/40 px-7 py-3.5 font-semibold transition hover:bg-white hover:text-deep"
               data-en="Discover the organisation">
                {{ $t('Découvrir l\'organisation', 'Discover the organisation') }}
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
    {{-- asset() (et non secure_asset) : secure_asset force https, absent en local --}}
    <script src="{{ asset('js/oaat.js') }}" defer></script>
@endpush
