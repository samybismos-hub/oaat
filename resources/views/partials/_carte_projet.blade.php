{{-- ═══════════════════════════════════════════════════════════════════
    _carte_projet.blade.php — Carte projet pour la grille des projets.
    Variables : $projet (Project avec domain, partners chargés), $locale, $t,
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


@endphp
<article id="p{{ $projet->id }}" data-r data-i="{{ $projet->sort_order ?? ($loop->iteration ?? 1) }}"
         data-st="{{ $projet->statutCourt }}"

         data-y="{{ $projet->year() }}"
         data-b="{{ $projet->budget }}"
         data-q="{{ Str::slug($projet->getTranslation('title', 'fr').' '.$projet->city.' '.$projet->country) }}"
         class="scroll-mt-32 border border-l-4 border-line {{ $borderClass }} bg-white transition duration-300 hover:shadow-xl"
>


    {{-- ═══ Contenu texte ═══ --}}
    <div class="p-6">
{{-- ▸ Année + Domaine --}}

        <div class="flex flex-wrap items-center gap-3">
            <span class="{{ $badgeClass }} px-2.5 py-1 text-xs font-bold">{{ $badgeLabel }}</span>
            @if (!$esEnAttente)
                <span class="bg-mist px-2 py-0.5 text-xs font-semibold text-ink/70">{{ $projet->year() }}</span>
            @endif
            <span class="text-xs font-semibold uppercase tracking-wider {{ $esEnAttente ? 'text-ochre-dark' : 'text-moss' }}">
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
                <dt class="text-xs text-ink/60">{{ $t('Budget total', 'Total budget') }}</dt>
                <dd class="mt-1 font-semibold">{{ $projet->budgetLabel($locale) }}</dd>
            </div>
            {{-- 2. Durée --}}
            <div class="bg-white p-3">
                <dt class="text-xs text-ink/60">{{ $t('Durée', 'Duration') }}</dt>
                <dd class="mt-1 font-semibold">{{ $projet->durationLabel($locale) }}</dd>
            </div>
            {{-- 3. Bénéficiaires (nombre) --}}
            <div class="bg-white p-3">
                <dt class="text-xs text-ink/60">{{ $t('Bénéficiaires', 'Beneficiaries') }}</dt>
                <dd class="mt-1 font-semibold">{{ $projet->beneficiariesLabel($locale) }}</dd>
            </div>
            {{-- 4. Partenaires --}}
            <div class="bg-white p-3">
                <dt class="text-xs text-ink/60">{{ $t('Bailleurs', 'Funders') }}</dt>
                <dd class="mt-1 font-semibold">{{ $projet->funderLabel($locale) }}</dd>
            </div>
            {{-- 5. ONG de mise en œuvre --}}
            <div class="bg-white p-3">
                <dt class="text-xs text-ink/60">{{ $t('Partenaires de mise en œuvre', 'Implementing Partners') }}</dt>
                <dd class="mt-1 font-semibold">{{ $projet->implementationPartnersLabel($locale) }}</dd>
            </div>
            {{-- 6. Statut --}}
            <div class="bg-white p-3">
                <dt class="text-xs text-ink/60">{{ $t('Statut', 'Status') }}</dt>
                <dd class="mt-1 font-semibold">{{ $projet->statusLabel($locale) }}</dd>
            </div>
        </dl>

        {{-- ▸ Bouton --}}
        <a href="{{ route('projets.show', ['locale' => $locale, 'slug' => $projet->slug]) }}"
           class="relative overflow-hidden before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%] mt-5 inline-block {{ $esEnAttente ? 'bg-ochre text-deep' : 'bg-lake text-white' }} px-6 py-3 text-sm font-semibold transition hover:-translate-y-0.5 hover:shadow-xl">
            {{ $esEnAttente ? $t('Soutenir ce projet', 'Support this project') : $t('En savoir plus', 'Learn more') }}
        </a>
    </div>
</article>
