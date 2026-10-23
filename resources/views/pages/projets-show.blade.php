{{--
    pages/projets-show.blade.php — Fiche détaillée d'un projet.
    ────────────────────────────────────────────────────────────────────────────
    Hérite de layouts.app : la topbar, le header, le footer, le bouton « retour
    en haut », la barre de progression et le toast sont fournis par le layout.

    Variables disponibles (depuis ProjetController@show) :
        $projet      (Project)           — projet affiché (relations domain,
                                           partners et albums.media chargées)
        $experiences (Collection)       — 4 réalisations récentes (section
                                           « Expérience comparable »)
        $connexes_awaiting (Collection) — 3 projets en attente de financement
                                           (section « Autres projets disponibles »)
        $settings    (Setting|null)
        $locale      (string)           — 'fr' | 'en'

    Sections de la page (ancres de la sous-navigation #sn) :
        #apercu  #contexte  #composantes  #zone  #financement
        #experience  #galerie  #soutenir

    Interactions : le JavaScript est centralisé dans public/js/oaat.js
    (galerie #lb, copie #cpi, impression #pr, sous-nav #sn, barre mobile #mb).
--}}
@extends('layouts.app')

@php
    $locale ??= 'fr';
    $t = fn ($fr, $en) => $locale === 'en' ? $en : $fr;

    // ─────────────────────────────────────────────────────────────────────
    //  1. Données locales du projet (bilingue, prêtes à afficher)
    // ─────────────────────────────────────────────────────────────────────
    $titre     = trim((string) $projet->getTranslation('title', $locale));
    $domaine   = trim((string) ($projet->domain?->getTranslation('name', $locale) ?? ''));
    $objectifs = trim((string) $projet->getTranslation('objectives', $locale));
    $resultats = trim((string) $projet->getTranslation('results', $locale));
    $zone      = trim(implode(' · ', array_values(array_filter([$projet->city, $projet->country]))));

    // Famille visuelle du statut : « w » = à financer (pastille ochre),
    // « d » = réalisé / en cours (pastille verte).
    $aFinancer = $projet->statut_court === 'w';
    $statut    = $projet->statusLabel($locale);

    $beneficiaires = $projet->beneficiariesLabel($locale);

    // Bailleurs (rôle « funder ») et mise en œuvre (rôle « implementer »)
    // issus de la table pivot project_partner.
    $funders = $projet->relationLoaded('partners')
        ? $projet->partners->filter(fn ($p) => ($p->pivot?->role?->value ?? null) === 'funder')->values()
        : collect();

    $bailleurs    = $funders->isNotEmpty() ? $funders->pluck('name')->join(', ') : '—';
    $miseEnOeuvre = $projet->implementationPartnersLabel($locale);
    if ($miseEnOeuvre === '—') {
        $miseEnOeuvre = 'OAAT';
    }

    // ─────────────────────────────────────────────────────────────────────
    //  2. Visuels : couverture (collection « cover ») + galerie des albums
    // ─────────────────────────────────────────────────────────────────────
    $heroUrl = '';
    if ($couverture = $projet->getFirstMedia('cover')) {
        $heroUrl = $couverture->hasGeneratedConversion('hero')
            ? $couverture->getUrl('hero')
            : $couverture->getUrl();
    }

    $photos = collect();
    foreach ($projet->albums as $album) {
        foreach ($album->getMedia('photos') as $photo) {
            $photos->push([
                'thumb' => $photo->hasGeneratedConversion('card') ? $photo->getUrl('card') : $photo->getUrl(),
                'full'  => $photo->getUrl(),
                'title' => trim((string) $album->getTranslation('title', $locale)) ?: $titre,
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    //  3. E-mails automatiques (« Demander un document », « Soutenir »)
    // ─────────────────────────────────────────────────────────────────────
    $email = $settings?->email ?? 'oaatrdc2000@gmail.com';

    $mailto = fn (string $sujetFr, string $sujetEn, string $corpsFr, string $corpsEn): array => [
        'fr' => 'mailto:' . $email . '?subject=' . rawurlencode($sujetFr) . '&body=' . rawurlencode($corpsFr),
        'en' => 'mailto:' . $email . '?subject=' . rawurlencode($sujetEn) . '&body=' . rawurlencode($corpsEn),
    ];

    // ─────────────────────────────────────────────────────────────────────
    //  4. Sélecteurs de sections (affichées seulement si un contenu existe)
    // ─────────────────────────────────────────────────────────────────────
    // Sections éditoriales « Contexte » / « Composantes » : réservées au projet
    // emblématique de la maquette (ces champs n'existent pas encore en base).
    $editorial = $projet->slug === 'amenagement-des-8-territoires';

    $aApercu = $objectifs !== '' || $resultats !== '';
    $aZone   = $zone !== '' || $beneficiaires !== '—';
    $aExp    = $experiences->isNotEmpty();
@endphp

{{-- ─── SEO : titre & description dynamiques ───────────────────────────── --}}
@section('meta_title')
    {{ trim($titre . ' — ' . $t('Projet', 'Project') . ' — OAAT') }}
@endsection

@section('meta_description')
    {{ \Illuminate\Support\Str::limit(strip_tags($objectifs), 155) }}
@endsection

@section('content')

    {{-- ═══════════════════════════════════════════════════════════════════
         FIL D'ARIANE
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="border-b border-line bg-mist print:hidden">
        <nav aria-label="{{ $t('Fil d’Ariane', 'Breadcrumb') }}"
             class="mx-auto flex max-w-7xl flex-wrap items-center gap-2 px-5 py-3 text-sm text-ink/60 lg:px-8">
            <a href="{{ route('accueil', ['locale' => $locale]) }}" class="transition hover:text-lake">
                {{ $t('Accueil', 'Home') }}
            </a>
            <span aria-hidden="true">›</span>
            <a href="{{ route('projets.index', ['locale' => $locale]) }}" class="transition hover:text-lake">
                {{ $t('Projets', 'Projects') }}
            </a>
            <span aria-hidden="true">›</span>
            <span class="text-ink">{{ $titre }}</span>
        </nav>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         HERO DU PROJET (#top)
    ═══════════════════════════════════════════════════════════════════ --}}
    <section id="top" class="bg-white">
        <div class="mx-auto max-w-7xl px-5 pb-10 pt-10 lg:px-8 lg:pt-14">

            {{-- ▸ Identification : n° de fiche, domaine, statut --}}
            <p class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-semibold uppercase tracking-wider" data-r>
                <span class="text-lake">{{ $t('Fiche projet n°', 'Project sheet no.') }} {{ $projet->id }}</span>
                <span class="text-ink/25" aria-hidden="true">|</span>
                @if ($domaine !== '')
                    <span class="text-ink/70">{{ $domaine }}</span>
                    <span class="text-ink/25" aria-hidden="true">|</span>
                @endif
                <span class="inline-flex items-center gap-2 {{ $aFinancer ? 'text-[#7A5200]' : 'text-moss' }}">
                    <i class="h-2 w-2 {{ $aFinancer ? 'bg-ochre' : 'bg-moss' }}"></i>
                    <span>{{ $statut }}</span>
                </span>
            </p>

            {{-- ▸ Titre --}}
            <h1 class="mt-5 max-w-4xl font-serif text-4xl font-semibold leading-tight text-ink md:text-5xl" data-r>
                {{ $titre }}
            </h1>

            {{-- ▸ Synthèse — reprise des objectifs --}}
            @if ($objectifs !== '')
                <p class="mt-6 max-w-3xl border-l-4 border-ochre pl-5 text-lg leading-relaxed text-ink/70" data-r>
                    {{ $objectifs }}
                </p>
            @endif
        </div>

        {{-- ▸ Visuel de couverture (21:9), si une image est publiée --}}
        @if ($heroUrl !== '')
            <figure class="mx-auto max-w-7xl px-5 pb-12 lg:px-8">
                <img src="{{ $heroUrl }}" alt="{{ $titre }}" class="aspect-[21/9] w-full object-cover">
                <figcaption class="mt-2 flex flex-wrap justify-between gap-2 text-xs text-ink/60">
                    <span>{{ $titre }}</span>
                    <span>Crédit : OAAT</span>
                </figcaption>
            </figure>
        @endif
    </section>

    {{-- ═══════════════════════════════════════════════════════════════════
         SOUS-NAVIGATION (#sn) — ancres vers les sections de la page
         Surlignage piloté par oaat.js §11 (scrollspy : border-lake + text-lake)
    ═══════════════════════════════════════════════════════════════════ --}}
    <div id="sn" class="sticky top-[60px] z-30 border-y border-line bg-white/95 backdrop-blur print:hidden">
        <nav class="mx-auto flex max-w-7xl overflow-x-auto px-5 lg:px-8" aria-label="{{ $t('Sections', 'Sections') }}">
            @php $snClass = 'shrink-0 whitespace-nowrap border-b-2 px-4 py-3.5 text-sm font-medium transition border-transparent text-ink/70 hover:text-lake'; @endphp

            @if ($aApercu)
                <a href="#apercu" class="shrink-0 whitespace-nowrap border-b-2 border-lake px-4 py-3.5 text-sm font-medium transition text-lake">{{ $t('Aperçu', 'Overview') }}</a>
            @endif
            @if ($editorial)
                <a href="#contexte" class="{{ $snClass }}">{{ $t('Contexte', 'Context') }}</a>
                <a href="#composantes" class="{{ $snClass }}">{{ $t('Composantes', 'Components') }}</a>
            @endif
            @if ($aZone)
                <a href="#zone" class="{{ $snClass }}">{{ $t('Zone', 'Area') }}</a>
            @endif
            <a href="#financement" class="{{ $snClass }}">{{ $t('Financement', 'Funding') }}</a>
            @if ($aExp)
                <a href="#experience" class="{{ $snClass }}">{{ $t('Expérience', 'Experience') }}</a>
            @endif
            @if ($photos->isNotEmpty())
                <a href="#galerie" class="{{ $snClass }}">{{ $t('Galerie', 'Gallery') }}</a>
            @endif
            <a href="#soutenir" class="{{ $snClass }}">{{ $t('Soutenir', 'Support') }}</a>
        </nav>
    </div>


    {{-- ═══════════════════════════════════════════════════════════════════
         GRILLE PRINCIPALE — FICHE (aside) + CONTENU (colonne éditoriale)
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="mx-auto grid max-w-7xl gap-12 px-5 py-14 lg:grid-cols-[minmax(0,1fr)_21rem] lg:gap-16 lg:px-8">

        {{-- ▸▸ FICHE DU PROJET (colonne de droite, collante) ▸▸ --}}
        <aside class="lg:col-start-2 lg:row-start-1">
            <div class="lg:sticky lg:top-[130px]">
                <div class="border border-line bg-white">

                    <div class="border-b border-line bg-mist px-6 py-4">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-lake">{{ $t('Fiche du projet', 'Project fact sheet') }}</h2>
                    </div>

                    {{-- ═══ Métadonnées ═══ --}}
                    <dl class="divide-y divide-line px-6">
                        <div class="py-3">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-ink/55">{{ $t('Statut', 'Status') }}</dt>
                            <dd class="mt-1 font-semibold text-ink">{{ $statut }}</dd>
                        </div>
                        <div class="py-3">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-ink/55">{{ $t('Domaine', 'Area') }}</dt>
                            <dd class="mt-1 font-semibold text-ink">{{ $domaine !== '' ? $domaine : '—' }}</dd>
                        </div>
                        <div class="py-3">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-ink/55">{{ $t('Budget global', 'Total budget') }}</dt>
                            <dd class="mt-1 font-semibold text-ink">{{ $projet->budgetLabel($locale) }}</dd>
                        </div>
                        <div class="py-3">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-ink/55">{{ $t('Durée', 'Duration') }}</dt>
                            <dd class="mt-1 font-semibold text-ink">{{ $projet->durationLabel($locale) }}</dd>
                        </div>
                        <div class="py-3">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-ink/55">{{ $t('Bénéficiaires', 'Beneficiaries') }}</dt>
                            <dd class="mt-1 font-semibold text-ink">{{ $beneficiaires }}</dd>
                        </div>
                        <div class="py-3">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-ink/55">{{ $t("Zone d'intervention", 'Area of intervention') }}</dt>
                            <dd class="mt-1 font-semibold text-ink">{{ $zone !== '' ? $zone : '—' }}</dd>
                        </div>
                        <div class="py-3">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-ink/55">{{ $t('Bailleurs identifiés', 'Identified donors') }}</dt>
                            <dd class="mt-1 font-semibold text-ink">{{ $bailleurs }}</dd>
                        </div>
                        <div class="py-3">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-ink/55">{{ $t('Mise en œuvre', 'Implementation') }}</dt>
                            <dd class="mt-1 font-semibold text-ink">{{ $miseEnOeuvre }}</dd>
                        </div>
                    </dl>


                    {{-- ═══ Actions ═══ --}}
                    <div class="space-y-3 border-t border-line p-6">
                        <a href="#soutenir"
                           class="block bg-lake px-6 py-3.5 text-center font-semibold text-white transition hover:bg-deep">{{ $t('Soutenir ce projet', 'Support this project') }}</a>

                        </div>

                    {{-- ═══ Outils : copier, imprimer, partager ═══ --}}
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-line px-6 py-4 text-sm text-ink/60 print:hidden">
                        <button type="button" class="cp inline-flex items-center gap-2 transition hover:text-lake">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M10 13a5 5 0 007.07 0l3-3a5 5 0 00-7.07-7.07l-1.5 1.5M14 11a5 5 0 00-7.07 0l-3 3a5 5 0 007.07 7.07l1.5-1.5"/>
                            </svg>
                            <span>{{ $t('Copier le lien', 'Copy link') }}</span>
                        </button>
                        <button type="button" id="pr" class="inline-flex items-center gap-2 transition hover:text-lake">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M6 9V3h12v6M6 18H4a1 1 0 01-1-1v-6a2 2 0 012-2h14a2 2 0 012 2v6a1 1 0 01-1 1h-2M7 14h10v7H7z"/>
                            </svg>
                            <span>{{ $t('Imprimer', 'Print') }}</span>
                        </button>
                        <a class="sh inline-flex items-center gap-2 transition hover:text-lake" href="mailto:">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 6h18v12H3zM3 7l9 6 9-6"/>
                            </svg>
                            <span>{{ $t('Partager', 'Share') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ▸▸ CONTENU ÉDITORIAL (colonne principale) ▸▸ --}}
        <div class="min-w-0 lg:col-start-1 lg:row-start-1">


            {{-- ═══ APERÇU DU PROJET (#apercu) ═══ --}}
            @if ($aApercu)
                <section id="apercu" class="scroll-mt-40">
                    <h2 class="font-serif text-3xl font-semibold text-ink">{{ $t('Aperçu du projet', 'Project overview') }}</h2>

                    @if ($objectifs !== '')
                        <p class="mt-5 max-w-[68ch] text-[17px] leading-relaxed text-ink/80">{{ $objectifs }}</p>
                    @endif

                    @if ($resultats !== '')
                        <h3 class="mt-8 font-serif text-xl font-semibold text-lake">{{ $t('Résultats', 'Results') }}</h3>
                        <p class="mt-2 max-w-[68ch] text-[17px] leading-relaxed text-ink/80">{{ $resultats }}</p>
                    @endif
                </section>
            @endif

            {{-- ═══ CONTEXTE ET ENJEUX (#contexte) — projet emblématique ═══ --}}
            @if ($editorial)
                <section id="contexte" class="scroll-mt-40 mt-14 border-t border-line pt-10">
                    <h2 class="font-serif text-3xl font-semibold text-ink">{{ $t('Contexte et enjeux', 'Context and challenges') }}</h2>

                    <p class="mt-5 max-w-[68ch] text-[17px] leading-relaxed text-ink/80">
                        {{ $t('Après une dizaine d’années de conflits armés, la région a sombré dans l’instabilité socio-économique et culturelle, avec de lourdes conséquences pour les communautés et leurs infrastructures.',
                              'After about ten years of armed conflict, the region sank into socio-economic and cultural instability, with severe consequences for communities and their infrastructure.') }}
                    </p>
                    <p class="mt-5 max-w-[68ch] text-[17px] leading-relaxed text-ink/80">
                        {{ $t('La question qui guide l’action de l’OAAT est celle d’une approche pour un processus de paix inclusif, qui améliore les infrastructures et facilite la mobilité des personnes et de leurs biens. Ce projet y répond à l’échelle de huit territoires, en combinant routes, énergie renouvelable et formation aux métiers.',
                              'The question guiding OAAT\'s work is how to build an inclusive peace process that improves infrastructure and eases the movement of people and goods. This project answers it across eight territories, combining roads, renewable energy and vocational training.') }}
                    </p>
                </section>
            @endif


            {{-- ═══ OBJECTIFS ET COMPOSANTES (#composantes) — projet emblématique ═══ --}}
            @if ($editorial)
                @php
                    $composantes = [
                        [
                            'nomFr' => 'Routes de desserte agricole',        'nomEn' => 'Farm-access roads',
                            'favFr' => 'Réhabilitation des routes de desserte agricole dans les huit territoires.',
                            'favEn' => 'Rehabilitation of farm-access roads in the eight territories.',
                            'resFr' => 'Des déplacements facilités pour les personnes et leurs biens.',
                            'resEn' => 'Easier movement of people and goods.',
                        ],
                        [
                            'nomFr' => 'Barrage hydro-électrique et centrale photovoltaïque', 'nomEn' => 'Hydroelectric dam and photovoltaic plant',
                            'favFr' => 'Construction d’un barrage hydro-électrique et d’une centrale photovoltaïque.',
                            'favEn' => 'Construction of a hydroelectric dam and a photovoltaic power plant.',
                            'resFr' => 'Une production d’énergie renouvelable au service des territoires.',
                            'resEn' => 'Renewable energy generation serving the territories.',
                        ],
                        [
                            'nomFr' => 'Micro-centrales hydro-électriques', 'nomEn' => 'Micro hydropower plants',
                            'favFr' => 'Construction de micro-centrales hydro-électriques dans les huit territoires.',
                            'favEn' => 'Construction of micro hydropower plants in the eight territories.',
                            'resFr' => 'Une énergie disponible au plus près des communautés.',
                            'resEn' => 'Energy available close to communities.',
                        ],
                        [
                            'nomFr' => 'Centres d’apprentissage de métiers', 'nomEn' => 'Trade-training centres',
                            'favFr' => 'Construction de centres d’apprentissage de métiers dans les huit territoires.',
                            'favEn' => 'Construction of trade-training centres in the eight territories.',
                            'resFr' => 'Des compétences professionnelles développées localement.',
                            'resEn' => 'Professional skills developed locally.',
                        ],
                    ];
                @endphp
                <section id="composantes" class="scroll-mt-40 mt-14 border-t border-line pt-10">
                    <h2 class="font-serif text-3xl font-semibold text-ink">{{ $t('Objectifs et composantes', 'Objectives and components') }}</h2>

                    <p class="mt-5 max-w-[68ch] text-[17px] leading-relaxed text-ink/80">
                        {{ $t('Objectif général : faciliter la mobilité des personnes et de leurs biens et renforcer la résilience des communautés de huit territoires, par des infrastructures de base, une énergie renouvelable locale et des centres d’apprentissage de métiers.',
                              'General objective: ease the movement of people and goods and strengthen the resilience of communities in eight territories, through basic infrastructure, local renewable energy and trade-training centres.') }}
                    </p>

                    <div class="mt-6 overflow-x-auto border border-line">
                        <table class="w-full min-w-[620px] border-collapse text-left text-sm">
                            <thead class="bg-mist text-xs uppercase tracking-wider text-ink/60">
                                <tr>
                                    <th class="px-4 py-3 font-semibold" scope="col">{{ $t('Composante', 'Component') }}</th>
                                    <th class="px-4 py-3 font-semibold" scope="col">{{ $t('Ce que prévoit le projet', 'Planned works') }}</th>
                                    <th class="px-4 py-3 font-semibold" scope="col">{{ $t('Résultat attendu', 'Expected result') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line bg-white">
                                @foreach ($composantes as $c)
                                    <tr>
                                        <th class="w-1/4 px-4 py-4 align-top font-semibold text-ink" scope="row">
                                            {{ $t($c['nomFr'], $c['nomEn']) }}
                                        </th>
                                        <td class="px-4 py-4 align-top text-ink/75">{{ $t($c['favFr'], $c['favEn']) }}</td>
                                        <td class="px-4 py-4 align-top text-ink/75">{{ $t($c['resFr'], $c['resEn']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif


            {{-- ═══ ZONE D'INTERVENTION (#zone) ═══ --}}
            @if ($aZone)
                <section id="zone" class="scroll-mt-40 mt-14 border-t border-line pt-10">
                    <h2 class="font-serif text-3xl font-semibold text-ink">{{ $t('Bénéficiaires et zone d’intervention', 'Beneficiaries and area of intervention') }}</h2>

                    <p class="mt-5 max-w-[68ch] text-[17px] leading-relaxed text-ink/80">
                        @if ($zone !== '' && $beneficiaires !== '—')
                            {{ $t("Le projet est mené à {$zone} et s'adresse à {$beneficiaires}.",
                                  "The project is carried out in {$zone} and targets {$beneficiaires}.") }}
                        @elseif ($zone !== '')
                            {{ $t("Le projet est mené à {$zone}.", "The project is carried out in {$zone}.") }}
                        @else
                            {{ $t("Le projet s'adresse à {$beneficiaires}.", "The project targets {$beneficiaires}.") }}
                        @endif
                    </p>
                </section>
            @endif

            {{-- ═══ FINANCEMENT ET PARTENAIRES (#financement) ═══ --}}
            <section id="financement" class="scroll-mt-40 mt-14 border-t border-line pt-10">
                <h2 class="font-serif text-3xl font-semibold text-ink">{{ $t('Financement et partenaires', 'Funding and partners') }}</h2>

                {{-- Données financières --}}
                <dl class="mt-6 divide-y divide-line border-y border-line">
                    <div class="grid gap-1 py-3 sm:grid-cols-[13rem_1fr] sm:gap-6">
                        <dt class="text-sm text-ink/60">{{ $t('Statut du financement', 'Funding status') }}</dt>
                        <dd class="font-semibold text-ink">{{ $statut }}</dd>
                    </div>
                    <div class="grid gap-1 py-3 sm:grid-cols-[13rem_1fr] sm:gap-6">
                        <dt class="text-sm text-ink/60">{{ $t('Montant à financer', 'Amount to fund') }}</dt>
                        <dd class="font-semibold text-ink">
                            @if ($projet->budget_amount !== null)
                                {{ $projet->budgetLabel($locale) }}
                                <span class="font-normal text-ink/60">({{ $t('budget global', 'total budget') }})</span>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div class="grid gap-1 py-3 sm:grid-cols-[13rem_1fr] sm:gap-6">
                        <dt class="text-sm text-ink/60">{{ $t('Détail budgétaire', 'Budget breakdown') }}</dt>
                        <dd class="font-semibold text-ink">{{ $t('Communiqué aux partenaires sur demande', 'Provided to partners on request') }}</dd>
                    </div>
                </dl>

                {{-- Partenaires identifiés (bailleurs) --}}
                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    @forelse ($funders as $partenaire)
                        <div class="flex items-center gap-4 border border-line bg-white p-5">
                            @if ($logoPartenaire = $partenaire->getFirstMediaUrl('logo'))
                                <img src="{{ $logoPartenaire }}" alt="" class="h-12 w-12 shrink-0 object-contain">
                            @else
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded bg-lake/10 font-serif text-lg font-semibold text-lake">
                                    {{ mb_substr((string) $partenaire->name, 0, 1) }}
                                </span>
                            @endif
                            <div>
                                <p class="font-serif text-lg font-semibold text-ink">{{ $partenaire->name }}</p>
                                <p class="text-sm text-ink/65">{{ $t('Bailleur identifié', 'Identified donor') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="border border-dashed border-line bg-white p-5">
                            <p class="text-sm text-ink/65">{{ $t('Aucun bailleur identifié pour le moment.', 'No donor identified yet.') }}</p>
                        </div>
                    @endforelse


                    {{-- OAAT : mise en œuvre --}}
                    <div class="flex items-center gap-4 border border-line bg-white p-5">
                        <svg viewBox="0 0 40 40" class="h-12 w-12 shrink-0" aria-hidden="true">
                            <rect width="40" height="40" rx="9" fill="#17508F"/>
                            <path d="M5 27c6-9 11 3 17-6s8-5 13-9M5 21c5-8 10 2 15-5s9-4 15-8M5 33c7-8 12 3 18-5s7-4 12-7"
                                  fill="none" stroke="#E3A82B" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <div>
                            <p class="font-serif text-lg font-semibold text-ink">OAAT</p>
                            <p class="text-sm text-ink/65">{{ $t('Organisation de mise en œuvre', 'Implementing organisation') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Rejoindre le partenariat --}}
                @php
                    $u = $mailto(
                        "Partenariat — {$titre}",
                        "Partnership — {$titre}",
                        "Bonjour,\n\nNotre organisation souhaite étudier un partenariat sur le projet « {$titre} ».\n\nOrganisation :\nContact :\n\nCordialement,",
                        "Hello,\n\nOur organisation would like to explore a partnership on the project \"{$titre}\".\n\nOrganisation:\nContact:\n\nKind regards,",
                    );
                @endphp
                <p class="mt-6 text-sm text-ink/70">
                    {{ $t('Vous souhaitez rejoindre ce partenariat ?', 'Would you like to join this partnership?') }}
                    <a href="{{ $locale === 'en' ? $u['en'] : $u['fr'] }}"
                       class="font-semibold text-lake underline-offset-4 hover:underline">
                        {{ $t('Écrire à l’OAAT', 'Write to OAAT') }}
                    </a>
                </p>
            </section>


            {{-- ═══ EXPÉRIENCE COMPARABLE (#experience) ═══ --}}
            @if ($aExp)
                <section id="experience" class="scroll-mt-40 mt-14 border-t border-line pt-10">
                    <h2 class="font-serif text-3xl font-semibold text-ink">{{ $t('Expérience comparable', 'Comparable experience') }}</h2>

                    <p class="mt-5 max-w-[68ch] text-[17px] leading-relaxed text-ink/80">
                        {{ $t('Depuis 1995, l’OAAT réalise des routes, des ponts et des ouvrages publics avec des agences des Nations Unies et d’autres bailleurs. Quelques réalisations comparables :',
                              'Since 1995, OAAT has built roads, bridges and public works with UN agencies and other donors. A few comparable achievements:') }}
                    </p>

                    <div class="mt-6 overflow-x-auto border border-line">
                        <table class="w-full min-w-[620px] border-collapse text-left text-sm">
                            <thead class="bg-mist text-xs uppercase tracking-wider text-ink/60">
                                <tr>
                                    <th class="px-4 py-3 font-semibold" scope="col">{{ $t('Année', 'Year') }}</th>
                                    <th class="px-4 py-3 font-semibold" scope="col">{{ $t('Projet', 'Project') }}</th>
                                    <th class="px-4 py-3 font-semibold" scope="col">{{ $t('Bailleur', 'Donor') }}</th>
                                    <th class="px-4 py-3 font-semibold" scope="col">{{ $t('Bénéficiaires', 'Beneficiaries') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line bg-white">
                                @foreach ($experiences as $experience)
                                    @php
                                        $expFunder = $experience->funderLabel($locale);
                                        if ($expFunder === '—') {
                                            $expFunder = $experience->partnersLabel($locale);
                                        }
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-4 font-semibold text-ink">{{ $experience->year() ?? '—' }}</td>
                                        <td class="px-4 py-4">
                                            <a href="{{ route('projets.show', ['locale' => $locale, 'slug' => $experience->slug]) }}"
                                               class="font-semibold text-lake underline-offset-4 hover:underline">
                                                {{ $experience->getTranslation('title', $locale) }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-4 text-ink/75">{{ $expFunder }}</td>
                                        <td class="px-4 py-4 text-ink/75">{{ $experience->beneficiariesLabel($locale) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p class="mt-5 text-sm">
                        <a href="{{ route('organisation', ['locale' => $locale]) }}#reconnaissance"
                           class="font-semibold text-lake underline-offset-4 hover:underline">
                            {{ $t('Reconnaissance officielle et enregistrement de l’organisation →', 'Official recognition and registration of the organisation →') }}
                        </a>
                    </p>
                </section>
            @endif


            </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         GALERIE PHOTO (#galerie) — figures .gl pilotées par oaat.js §20
    ═══════════════════════════════════════════════════════════════════ --}}
    @if ($photos->isNotEmpty())
        <section id="galerie" class="scroll-mt-32 border-t border-line bg-mist py-16">
            <div class="mx-auto max-w-7xl px-5 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <h2 class="font-serif text-3xl font-semibold text-ink">{{ $t('Galerie photo', 'Photo gallery') }}</h2>
                    <p class="text-sm text-ink/60">{{ $photos->count() }} {{ $t('photos', 'photos') }}</p>
                </div>

                <div class="mt-8 grid gap-x-6 gap-y-9 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($photos as $i => $photo)
                        <figure>
                            <button type="button"
                                    class="gl block w-full overflow-hidden bg-mist"
                                    data-full="{{ $photo['full'] }}"
                                    aria-label="{{ $t('Agrandir la photo', 'Enlarge photo') }} {{ $i + 1 }}">
                                <img src="{{ $photo['thumb'] }}" alt="{{ $photo['title'] }}" loading="lazy"
                                     class="aspect-[4/3] w-full object-cover transition duration-500 hover:scale-105">
                            </button>
                            <figcaption class="mt-3 text-sm leading-snug text-ink/80">
                                <span>{{ $photo['title'] }}</span>
                                <span class="mt-1 block text-xs text-ink/50">Crédit : OAAT</span>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif



    <!-- Appel à soutien -->
    <section id="soutenir" class="scroll-mt-32 bg-deep py-20 text-white">
      <div class="mx-auto max-w-7xl px-5 lg:px-8">
        <div data-r class="max-w-2xl">
          <h2 class="font-serif text-4xl font-semibold">{{ $t('Soutenir ce projet', 'Support this project') }}</h2>
          <p class="mt-4 text-lg text-white/75">
            {{ $t('Trois façons de contribuer au soutien de ce projet.', 'Three ways to contribute to the support of this project.') }}
          </p>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-3">
          <div data-r class="sp group relative flex flex-col overflow-hidden border border-white/15 bg-white/5 p-7 transition duration-300 before:pointer-events-none before:absolute before:inset-0 before:opacity-0 before:transition before:duration-300 before:bg-[radial-gradient(260px_circle_at_var(--x,50%)_var(--y,50%),rgba(255,255,255,.12),transparent_70%)] hover:-translate-y-1 hover:border-ochre hover:before:opacity-100">
            <h3 class="relative font-serif text-2xl font-semibold">{{ $t('Financer', 'Fund') }}</h3>
            <p class="relative mt-3 grow text-sm leading-relaxed text-white/75">
              {{ $t('Contribution financière ou cofinancement d’une ou plusieurs composantes du projet.', 'Financial contribution or co-funding of one or more components of the project.') }}
            </p>
            <a class="relative mt-6 inline-flex items-center gap-2 font-semibold text-ochre"
               href="{{ route('contact', ['locale' => $locale, 'objet' => 'f']) }}">
              {{ $t('Écrire à l’OAAT', 'Write to OAAT') }}<span class="transition group-hover:translate-x-1" aria-hidden="true">→</span>
            </a>
          </div>
          <div data-r class="sp group relative flex flex-col overflow-hidden border border-white/15 bg-white/5 p-7 transition duration-300 before:pointer-events-none before:absolute before:inset-0 before:opacity-0 before:transition before:duration-300 before:bg-[radial-gradient(260px_circle_at_var(--x,50%)_var(--y,50%),rgba(255,255,255,.12),transparent_70%)] hover:-translate-y-1 hover:border-ochre hover:before:opacity-100">
            <h3 class="relative font-serif text-2xl font-semibold">{{ $t('Partenariat technique', 'Technical partnership') }}</h3>
            <p class="relative mt-3 grow text-sm leading-relaxed text-white/75">
              {{ $t('Expertise, équipements ou appui à la mise en œuvre des ouvrages et des formations.', 'Expertise, equipment or support for the delivery of the works and training.') }}
            </p>
            <a class="relative mt-6 inline-flex items-center gap-2 font-semibold text-ochre"
               href="{{ route('contact', ['locale' => $locale, 'objet' => 'p']) }}">
              {{ $t('Écrire à l’OAAT', 'Write to OAAT') }}<span class="transition group-hover:translate-x-1" aria-hidden="true">→</span>
            </a>
          </div>
          <div data-r class="sp group relative flex flex-col overflow-hidden border border-white/15 bg-white/5 p-7 transition duration-300 before:pointer-events-none before:absolute before:inset-0 before:opacity-0 before:transition before:duration-300 before:bg-[radial-gradient(260px_circle_at_var(--x,50%)_var(--y,50%),rgba(255,255,255,.12),transparent_70%)] hover:-translate-y-1 hover:border-ochre hover:before:opacity-100">
            <h3 class="relative font-serif text-2xl font-semibold">{{ $t('Relayer', 'Spread the word') }}</h3>
            <p class="relative mt-3 grow text-sm leading-relaxed text-white/75">
              {{ $t('Mise en relation avec d’autres bailleurs, institutions et réseaux susceptibles de soutenir le projet.', 'Introductions to other donors, institutions and networks that may support the project.') }}
            </p>
            <a class="relative mt-6 inline-flex items-center gap-2 font-semibold text-ochre"
               href="{{ route('contact', ['locale' => $locale, 'objet' => 'i']) }}">
              {{ $t('Écrire à l’OAAT', 'Write to OAAT') }}<span class="transition group-hover:translate-x-1" aria-hidden="true">→</span>
            </a>
          </div>
        </div>
        <p data-r class="mt-10 border-t border-white/15 pt-6 text-sm text-white/70">
          {{ $t('Interlocuteur : Ir Roger Manema Cirhahingirwa, Représentant national — ', 'Contact: Ir Roger Manema Cirhahingirwa, National Representative — ') }}
          <a class="font-semibold text-ochre underline-offset-4 hover:underline" href="mailto:{{ $email }}">{{ $email }}</a>
        </p>
      </div>
    </section>


    <!-- Autres projets disponibles -->
    @if ($connexes_awaiting->isNotEmpty())
        <section class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4" data-r>
                <h2 class="font-serif text-3xl font-semibold text-lake md:text-4xl">
                    {{ $t('Autres projets disponibles', 'Other projects available') }}
                </h2>
                <a href="{{ route('projets.index', ['locale' => $locale]) }}"
                   class="font-semibold text-lake underline-offset-4 hover:underline">
                    {{ $t('Tous les projets', 'All projects') }} →
                </a>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach ($connexes_awaiting as $connexe)
                    <a data-r href="{{ route('projets.show', ['locale' => $locale, 'slug' => $connexe->slug]) }}"
                       class="group flex flex-col border border-line border-t-4 border-t-ochre bg-white p-6 transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#7A5200]">
                            {{ $connexe->statutCourt === 'w' ? $t('À financer', 'To fund') : $connexe->statusLabel($locale) }}
                        </p>
                        <h3 class="mt-3 grow font-serif text-lg font-semibold leading-snug text-lake">
                            {{ $connexe->getTranslation('title', $locale) }}
                        </h3>
                        <p class="mt-4 border-t border-line pt-4 text-sm text-ink/70">
                            <span>{{ $connexe->budgetLabel($locale) }}</span> · <span>{{ $connexe->durationLabel($locale) }}</span> ·
                            <span>{{ $connexe->beneficiariesLabel($locale) }}</span>
                        </p>
                        <span class="mt-4 inline-flex gap-2 text-sm font-semibold text-lake">
                            {{ $t('Voir la fiche', 'View the sheet') }} <span class="transition group-hover:translate-x-1" aria-hidden="true">→</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
    {{-- ═══════════════════════════════════════════════════════════════════
         BARRE CTA MOBILE (#mb) — affichée par oaat.js §17 sous 1024 px
    ═══════════════════════════════════════════════════════════════════ --}}
    <div id="mb"
         class="fixed inset-x-0 bottom-0 z-40 translate-y-full border-t border-line bg-white/95 p-3 pr-20 backdrop-blur transition duration-300 lg:hidden print:hidden">
        <a href="#soutenir"
           class="block bg-lake px-6 py-3.5 text-center font-semibold text-white transition hover:bg-deep">{{ $t('Soutenir ce projet', 'Support this project') }}</a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         COULEUR DE COPIER LE LIEN — cible de oaat.js §18
    ═══════════════════════════════════════════════════════════════════ --}}
    <input id="cpi" class="sr-only" tabindex="-1" aria-hidden="true" readonly>

    {{-- ═══════════════════════════════════════════════════════════════════
         VISIONNEUSE PHOTOS (#lb) — pilotée par oaat.js §20
    ═══════════════════════════════════════════════════════════════════ --}}
    <div id="lb"
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-ink/95 p-4 print:hidden"
         role="dialog" aria-modal="true" aria-label="{{ $t('Galerie photo', 'Photo gallery') }}">
        <button type="button" id="lx" aria-label="{{ $t('Fermer', 'Close') }}"
                class="absolute right-4 top-4 h-11 w-11 border border-white/30 text-white transition hover:bg-white hover:text-ink">✕</button>
        <button type="button" id="lp" aria-label="{{ $t('Photo précédente', 'Previous photo') }}"
                class="absolute left-3 h-11 w-11 border border-white/30 text-white transition hover:bg-white hover:text-ink">‹</button>

        <figure class="w-full max-w-5xl">
            <img id="li" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                 alt="" class="max-h-[78vh] w-full object-contain">
            <figcaption class="mt-3 flex justify-between gap-4 text-sm text-white/80">
                <span id="lc"></span>
                <span id="ln2" class="shrink-0"></span>
            </figcaption>
        </figure>

        <button type="button" id="ln" aria-label="{{ $t('Photo suivante', 'Next photo') }}"
                class="absolute right-3 h-11 w-11 border border-white/30 text-white transition hover:bg-white hover:text-ink">›</button>
    </div>

@endsection

{{-- ─── Scripts : oaat.js gère l'ensemble des interactions de la page ─── --}}
@push('scripts')
    <script src="{{ asset('js/oaat.js') }}"></script>
@endpush

