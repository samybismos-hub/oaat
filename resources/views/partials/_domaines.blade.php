{{--
    _domaines.blade.php — Section Domaines d'intervention (9 cartes).
    ────────────────────────────────────────────────────────────────
    Variables disponibles :
        $domaines  (Collection) — Domaines triés par name
        $locale    (string)
        $t         (helper)
--}}
@php
    // Mapping icône → SVG path (référence OAAT_pageAccueil.html)
    $iconPaths = [
        'heart-pulse'        => 'M12 8v8M8 12h8M12 3a9 9 0 100 18 9 9 0 000-18z',
        'wheat-awn'          => 'M12 21V9M12 13c-4 0-6-2-6-6 4 0 6 2 6 6zM12 11c0-4 2-6 6-6 0 4-2 6-6 6z',
        'truck'              => 'M3 18h18M5 18v-6a7 7 0 0114 0v6M12 5v13',
        'book'               => 'M3 5h6a3 3 0 013 3v12a2 2 0 00-2-2H3zM21 5h-6a3 3 0 00-3 3v12a2 2 0 012-2h7z',
        'droplets'           => 'M12 3s6 6.5 6 11a6 6 0 01-12 0c0-4.5 6-11 6-11z',
        'venus'              => 'M12 14a5 5 0 100-10 5 5 0 000 10zM12 14v7M9 18h6',
        'shield-halved'      => 'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z',
        'leaf'               => 'M5 19c0-9 5-14 15-14 0 10-5 15-14 15M5 19c2-5 5-8 9-10',
        'screwdriver-wrench' => 'M14 6l4 4M4 20l8-8M13 5l6 6-3 3-6-6z',
    ];
    $defaultPath = 'M12 2v20M17 6H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6';
@endphp

<section id="domaines"
         class="scroll-mt-20 bg-mist py-24">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">

        {{-- ─── En-tête ───────────────────────────────────────── --}}
        <div class="flex flex-wrap items-end justify-between gap-4" data-r>
            <h2 class="max-w-2xl font-serif text-4xl font-semibold text-lake md:text-5xl">
                {{ $t('Neuf domaines d\'intervention', 'Nine areas of intervention') }}
            </h2>
            <a href="{{ route('domaines.index', ['locale' => $locale]) }}"
               class="font-semibold text-lake underline-offset-4 hover:underline">
                {{ $t('Tous les domaines →', 'All areas →') }}
            </a>
        </div>

        {{-- ─── Grille des 9 cartes ───────────────────────────── --}}
        <div id="dm" class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($domaines ?? [] as $domaine)
                @php
                    // Traductions déjà appliquées par le middleware SetLocale
                    $nom   = $domaine->getTranslation('name', $locale) ?? $domaine->name;
                    $act   = $domaine->getTranslation('activities', $locale) ?? $domaine->activities;
                @endphp
                <a href="{{ route('domaines.show', [$locale, $domaine->slug]) }}"
                   data-r
                   class="sp group relative overflow-hidden border border-line bg-white p-7 pb-12 transition duration-300
                          before:pointer-events-none before:absolute before:inset-0 before:opacity-0 before:transition before:duration-300
                          before:bg-[radial-gradient(260px_circle_at_var(--x,50%)_var(--y,50%),rgba(255,255,255,.18),transparent_70%)]
                          hover:-translate-y-1 hover:border-transparent hover:bg-lake hover:text-white hover:shadow-2xl hover:before:opacity-100">

                    {{-- Icône SVG --}}
                    <span class="relative grid h-12 w-12 place-items-center bg-lake/10 text-lake transition group-hover:bg-ochre group-hover:text-deep">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="{{ $iconPaths[$domaine->icon] ?? $defaultPath }}"/>
                        </svg>
                    </span>

                    {{-- Titre --}}
                    <h3 class="relative mt-5 font-serif text-xl font-semibold">
                        {{ $nom }}
                    </h3>

                    {{-- Description (activités principales) --}}
                    <p class="relative mt-2 text-sm leading-relaxed text-ink/65 transition group-hover:text-white/80">
                        {{ $act }}
                    </p>

                    {{-- Flèche --}}
                    <span class="absolute bottom-5 right-6 translate-x-2 text-xl text-ochre opacity-0 transition duration-300 group-hover:translate-x-0 group-hover:opacity-100">→</span>
                </a>
            @empty
                <p class="col-span-3 text-center text-sm text-ink/40 italic">
                    {{ $t('Aucun domaine renseigné pour le moment.', 'No areas listed yet.') }}
                </p>
            @endforelse
        </div>

    </div>
</section>