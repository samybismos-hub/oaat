{{-- ═══════════════════════════════════════════════════════════════════
    _carte_projet.blade.php — Carte projet pour la grille des projets.
    Variables : $projet (Project avec domain, partners chargés), $locale, $t, $gradHsl
    --}}
@php
    $st = $projet->status->value;
    $esEnAttente = $st === 'awaiting_funding';
    $esEnCours   = $st === 'ongoing';
    $badgeLabel  = $esEnAttente
        ? $t('À financer', 'To fund')
        : ($esEnCours ? $t('En cours', 'Ongoing') : $t('Réalisé', 'Completed'));
    $badgeClass  = $esEnAttente ? 'bg-ochre text-deep' : ($esEnCours ? 'bg-lake text-white' : 'bg-moss text-white');
    $borderClass = $esEnAttente ? 'border-l-ochre' : 'border-l-moss';
    $slug        = $projet->domain->slug;
    $hsl         = $gradHsl($slug);
    $parts       = explode('|', $hsl);
    $h1          = $parts[0] ?? '0,45%,26%';
    $h2          = $parts[1] ?? '50,50%,44%';
    $rectSvg     = '%3Crect%20width%3D%22800%22%20height%3D%22500%22%20fill%3D%22url(%23g)%22%2F%3E';
    $domainName  = urlencode($projet->domain->getTranslation('name', $locale));
    $svghtml     = 'data:image/svg+xml;utf8,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 800 500%27%3E%3Cdefs%3E%3ClinearGradient id=%27g%27 x1=%270%27 y1=%270%27 x2=%271%27 y2=%271%27%3E%3Cstop offset=%270%27 stop-color=%27hsl('.$h1.')%27%2F%3E%3Cstop offset=%271%27 stop-color=%27hsl('.$h2.')%27%2F%3E%3C%2FlinearGradient%3E%3C%2Fdefs%3E'.$rectSvg.'%3Cpath d=%27M0 380c120-60 200 40 340-10s220-80 460 0v130H0z%27 fill=%27%23fff%27 opacity=%27.1%27%2F%3E%3Cpath d=%27M0 430c150-50 260 30 400-10s250-40 400 10v70H0z%27 fill=%27%23fff%27 opacity=%27.12%27%2F%3E%3Ctext x=%27400%27 y=%27245%27 fill=%27%23fff%27 fill-opacity=%27.85%27 font-family=%27sans-serif%27 font-size=%2724%27 text-anchor=%27middle%27%3E'.$domainName.'%3C%2Ftext%3E%3C%2Fsvg%3E';
    $photo       = $projet->getFirstMediaUrl('photo');
    $imgSrc      = $photo ?: $svghtml;
    $statutLabel = $t(match($projet->status->value) {
        'completed'        => 'Réalisé',
        'ongoing'          => 'En cours',
        'awaiting_funding' => 'En attente de financement',
        'planned'          => 'Planifié',
        default            => 'Inconnu',
    }, match($projet->status->value) {
        'completed'        => 'Completed',
        'ongoing'          => 'Ongoing',
        'awaiting_funding' => 'Awaiting funding',
        'planned'          => 'Planned',
        default            => 'Unknown',
    });
@endphp
<article id="p{{ $projet->id }}" data-r data-i="{{ $projet->sort_order ?? ($loop->iteration ?? 1) }}"
         data-st="{{ $projet->statutCourt }}"
         data-dom="{{ $slug }}"
         data-y="{{ $projet->year() }}"
         data-b="{{ $projet->budget }}"
         data-q="{{ Str::slug($projet->getTranslation('title', 'fr').' '.$projet->city.' '.$projet->country) }}"
         class="group grid scroll-mt-32 overflow-hidden border border-l-4 border-line {{ $borderClass }} bg-white transition duration-300 hover:shadow-xl md:grid-cols-[13rem_1fr]">

    {{-- ═══ Image ═══ --}}
    <div class="relative overflow-hidden">
        <img src="{{ $imgSrc }}" alt="" loading="lazy"
             class="h-44 w-full object-cover transition duration-700 group-hover:scale-105 md:h-full">
        <span class="absolute left-3 top-3 {{ $badgeClass }} px-2.5 py-1 text-xs font-bold">
            <span data-en="{{ $badgeLabel }}">{{ $badgeLabel }}</span>
        </span>
    </div>

    {{-- ═══ Contenu texte ═══ --}}
    <div class="p-6">
{{-- ▸ Année + Domaine --}}
        <div class="flex flex-wrap items-center gap-3">
            @if (!$esEnAttente)
                <span class="bg-mist px-2 py-0.5 text-xs font-semibold text-ink/70">{{ $projet->year() }}</span>
            @endif
            <span class="text-xs font-semibold uppercase tracking-wider {{ $esEnAttente ? 'text-[#7A5200]' : 'text-moss' }}">
                {{ $projet->domain->getTranslation('name', $locale) }}
            </span>
        </div>

        {{-- ▸ Titre --}}
        <h3 class="mt-3 font-serif text-xl font-semibold leading-snug text-lake">
            {{ $projet->getTranslation('title', $locale) }}
        </h3>

        {{-- ▸ Objectifs (description) --}}
        <p class="mt-3 leading-relaxed text-ink/75">
            {{ Str::limit($projet->getTranslation('objectives', $locale), 180) }}
        </p>

        {{-- ▸ Métadonnées : 6 cellules fixes — Budget, Durée, Bénéficiaires,
             Partenaires, Mise en œuvre (ONG), Statut — valeur « — » si absente. --}}
        <dl class="mt-5 grid grid-cols-2 gap-px border border-line bg-line text-sm sm:grid-cols-3">
            {{-- 1. Budget total --}}
            <div class="bg-white p-3">
                <dt class="text-xs text-ink/60" data-en="Total budget">{{ $t('Budget total', 'Total budget') }}</dt>
                <dd class="mt-1 font-semibold">{{ $projet->budgetLabel($locale) }}</dd>
            </div>
            {{-- 2. Durée --}}
            <div class="bg-white p-3">
                <dt class="text-xs text-ink/60" data-en="Duration">{{ $t('Durée', 'Duration') }}</dt>
                <dd class="mt-1 font-semibold">{{ $projet->durationLabel($locale) }}</dd>
            </div>
            {{-- 3. Bénéficiaires (nombre) --}}
            <div class="bg-white p-3">
                <dt class="text-xs text-ink/60" data-en="Beneficiaries">{{ $t('Bénéficiaires', 'Beneficiaries') }}</dt>
                <dd class="mt-1 font-semibold">{{ $projet->beneficiariesLabel($locale) }}</dd>
            </div>
            {{-- 4. Partenaires --}}
            <div class="bg-white p-3">
                <dt class="text-xs text-ink/60" data-en="Partners">{{ $t('Partenaires', 'Partners') }}</dt>
                <dd class="mt-1 font-semibold">{{ $projet->partnersLabel($locale) }}</dd>
            </div>
            {{-- 5. ONG de mise en œuvre --}}
            <div class="bg-white p-3">
                <dt class="text-xs text-ink/60" data-en="Implementing">{{ $t('Mise en œuvre', 'Implementing') }}</dt>
                <dd class="mt-1 font-semibold">{{ $projet->implementationPartnersLabel($locale) }}</dd>
            </div>
            {{-- 6. Statut --}}
            <div class="bg-white p-3">
                <dt class="text-xs text-ink/60" data-en="Status">{{ $t('Statut', 'Status') }}</dt>
                <dd class="mt-1 font-semibold">{{ $statutLabel }}</dd>
            </div>
        </dl>

        {{-- ▸ Bouton --}}
        <a href="{{ route('projets.show', ['locale' => $locale, 'slug' => $projet->slug]) }}"
           class="relative overflow-hidden before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%] mt-5 inline-block {{ $esEnAttente ? 'bg-ochre text-deep' : 'bg-lake text-white' }} px-6 py-3 text-sm font-semibold transition hover:-translate-y-0.5 hover:shadow-xl"
           data-en="{{ $esEnAttente ? 'Support this project' : 'Learn more' }}">
            {{ $esEnAttente ? $t('Soutenir ce projet', 'Support this project') : $t('En savoir plus', 'Learn more') }}
        </a>
    </div>
</article>
