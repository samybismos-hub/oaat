{{--
    _projets.blade.php — Section Projets (3 cartes + bannière phare).
    ────────────────────────────────────────────────────────────────
    Variables disponibles :
        $projets     (Collection) — projets à la une (max 3, is_featured)
        $projetPhare (Project|null) — projet phare (statut fundraising prioritaire)
        $locale      (string)
        $t           (helper)
--}}
@php
    $statusLabel = function (?string $s) use ($t): string {
        return match ($s) {
            'completed'  => $t('Réalisé', 'Completed'),
            'ongoing'    => $t('En cours', 'Ongoing'),
            'fundraising'=> $t('Recherche de financement', 'Seeking funding'),
            default      => $t('En cours', 'Ongoing'),
        };
    };
    $pingBadge = fn($t): string => '<span class="absolute left-5 top-5 z-10 flex items-center gap-2 bg-ochre px-3 py-1.5 text-xs font-bold text-deep">'
        . '<span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-deep opacity-60 motion-reduce:animate-none"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-deep"></span></span>'
        . $t('Recherche de financement', 'Seeking funding') . '</span>';
@endphp

<section id="projets"
         class="mx-auto max-w-7xl scroll-mt-20 px-5 py-24 lg:px-8">

    {{-- En-tête --}}
    <div class="flex flex-wrap items-end justify-between gap-4" data-r>
        <div>
            <h2 class="font-serif text-4xl font-semibold text-lake md:text-5xl">
                {{ $t('Projets', 'Projects') }}
            </h2>
            <p class="mt-3 max-w-xl text-ink/70">
                {{ $t("Des réalisations menées avec les agences des Nations Unies et des ONG internationales, et des projets prêts pour un financement.",
                      "Projects carried out with UN agencies and international NGOs, and projects ready for funding.") }}
            </p>
        </div>
        <a href="{{ route('projets.index', ['locale' => $locale]) }}"
           class="font-semibold text-lake underline-offset-4 hover:underline">
            {{ $t('Tous les projets →', 'All projects →') }}
        </a>
    </div>

    {{-- 3 cartes à la une --}}
    <div id="pj" class="mt-10 grid gap-6 md:grid-cols-3">
        @forelse($projets ?? [] as $projet)
            @php
                $cover   = $projet->getFirstMediaUrl('cover', 'card');
                $hasCover = filled($cover);
                $funder  = $projet->partners?->first()?->name;
            @endphp
            <article data-r
                     class="group overflow-hidden border border-t-4 border-line border-t-moss bg-white transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
                <div class="overflow-hidden">
                    @if ($hasCover)
                        <img src="{{ $cover }}" alt="{{ $projet->title }}"
                             class="h-52 w-full object-cover transition duration-700 group-hover:scale-105">
                    @else
                        <img data-ph="{{ $projet->domain?->name }}|160"
                             alt="{{ $projet->title }}"
                             class="h-52 w-full object-cover transition duration-700 group-hover:scale-105">
                    @endif
                </div>
                <div class="p-6">
                    <span class="inline-block bg-moss/10 px-3 py-1 text-xs font-semibold text-moss">
                        {{ $statusLabel($projet->status?->value ?? '') }}
                        @if ($projet->end_date)
                            <i class="font-normal not-italic"> · {{ $projet->end_date->format('Y') }}</i>
                        @endif
                    </span>
                    <h3 class="mt-4 font-serif text-lg font-semibold leading-snug text-lake">
                        {{ $projet->title }}
                    </h3>
                    <dl class="mt-4 grid grid-cols-3 gap-2 border-t border-line pt-4 text-xs">
                        @if ($funder)
                            <div>
                                <dt class="text-ink/60">{{ $t('Bailleur', 'Funder') }}</dt>
                                <dd class="mt-1 font-semibold">{{ $funder }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-ink/60">{{ $t('Bénéficiaires', 'Beneficiaries') }}</dt>
                            <dd class="mt-1 font-semibold">{{ $projet->beneficiaries }}</dd>
                        </div>
                        <div>
                            <dt class="text-ink/60">{{ $t('Budget', 'Budget') }}</dt>
                            <dd class="mt-1 font-semibold">
                                {{ number_format($projet->budget_amount, 0, ',', ' ') }}
                                {{ $projet->budget_currency }}
                            </dd>
                        </div>
                    </dl>
                    <a href="{{ route('projets.show', [$locale, $projet->slug]) }}"
                       class="mt-5 inline-flex gap-2 text-sm font-semibold text-lake">
                        {{ $t('En savoir plus', 'Learn more') }}
                        <span class="transition group-hover:translate-x-1">→</span>
                    </a>
                </div>
            </article>
        @empty
            <p class="col-span-3 text-center text-sm text-ink/40 italic">
                {{ $t('Aucun projet mis en avant pour le moment.', 'No featured projects yet.') }}
            </p>
        @endforelse
    </div>
{{-- Bannière projet phare --}}
    @if ($projetPhare)
        @php
            $phareCover = $projetPhare->getFirstMediaUrl('cover', 'hero');
            $hasPhare = filled($phareCover);
            $duree = $projetPhare->start_date && $projetPhare->end_date
                ? $projetPhare->start_date->diffInYears($projetPhare->end_date) : 0;
            $budgetRaw = intval($projetPhare->budget_amount ?? 0);
        @endphp
        <article data-r
                 class="group mt-8 grid overflow-hidden bg-deep text-white shadow-2xl lg:grid-cols-5">
            <div class="relative min-h-[280px] overflow-hidden lg:col-span-2">
                @if ($hasPhare)
                    <img src="{{ $phareCover }}" alt="{{ $projetPhare->title }}"
                         data-w
                         class="absolute inset-0 h-full w-full object-cover transition duration-1000 group-hover:scale-105">
                @else
                    <img data-w data-ph="Territoires|120"
                         alt="{{ $projetPhare->title }}"
                         class="absolute inset-0 h-full w-full object-cover transition duration-1000 group-hover:scale-105">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-deep/70 to-transparent"></div>
                @if (($projetPhare->status?->value ?? '') === 'fundraising')
                    {!! $pingBadge($t) !!}
                @endif
            </div>
            <div class="p-8 lg:col-span-3 lg:p-12">
                <p class="text-sm font-semibold uppercase tracking-wider text-ochre">
                    {{ $t('Projet phare', 'Flagship project') }}
                </p>
                <h3 class="mt-3 font-serif text-3xl font-semibold leading-tight md:text-4xl">
                    {{ $projetPhare->title }}
                </h3>
                <p class="mt-4 max-w-2xl text-white/75">
                    {{ $projetPhare->objectives }}
                </p>
                <dl class="mt-8 grid grid-cols-3 gap-4 border-y border-white/15 py-6">
                    <div>
                        <dt class="text-xs text-white/60">{{ $t('Budget', 'Budget') }}</dt>
                        <dd class="mt-1 whitespace-nowrap font-serif text-xl font-semibold sm:text-2xl md:text-3xl">
                            @if ($budgetRaw > 0)
                                <span data-n="{{ $budgetRaw }}">0</span>
                                {{ str_starts_with($projetPhare->budget_currency ?? 'USD', 'EUR') ? 'M€' : 'M' . ($projetPhare->budget_currency ?? 'USD') }}
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-white/60">{{ $t('Durée', 'Duration') }}</dt>
                        <dd class="mt-1 whitespace-nowrap font-serif text-xl font-semibold sm:text-2xl md:text-3xl">
                            <span data-n="{{ $duree }}">0</span>
                            {{ $t('ans', 'years') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-white/60">{{ $t('Bénéficiaires', 'Beneficiaries') }}</dt>
                        <dd class="mt-1 whitespace-nowrap font-serif text-xl font-semibold sm:text-2xl md:text-3xl">
                            <span data-n="{{ intval($projetPhare->beneficiaries_count ?? 0) }}">0</span>
                        </dd>
                    </div>
                </dl>
                <div class="mt-8 flex flex-wrap items-center gap-5">
                    @if (($projetPhare->status?->value ?? '') === 'fundraising')
                        <a href="#contact" data-pick="f"
                           class="relative overflow-hidden before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%] bg-ochre px-7 py-3.5 font-semibold text-deep transition hover:-translate-y-0.5 hover:shadow-xl">
                            {{ $t('Financer ce projet', 'Fund this project') }}
                        </a>
                    @endif
                    <a href="{{ route('projets.show', [$locale, $projetPhare->slug]) }}"
                       class="font-semibold underline-offset-4 hover:underline">
                        {{ $t('Consulter la fiche projet →', 'View project sheet →') }}
                    </a>
                </div>
            </div>
        </article>
    @endif
</section>