{{--
    _actualites.blade.php — Section Actualités (3 dernières).
    ────────────────────────────────────────────────────────────────
    Variables disponibles :
        $actualites  (Collection) — 3 dernières actualités publiées
        $locale      (string)
        $t           (helper)
--}}

<section id="actualites"
         class="mx-auto max-w-7xl scroll-mt-20 px-5 py-24 lg:px-8">

    {{-- ─── En-tête ───────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-2" data-r>
        <h2 class="font-serif text-4xl font-semibold text-lake md:text-5xl">
            {{ $t('Actualités', 'News') }}
        </h2>
        <a href="{{ route('actualites.index', ['locale' => $locale]) }}"
           class="font-semibold text-lake underline-offset-4 hover:underline">
            {{ $t('Toutes les actualités →', 'All news →') }}
        </a>
    </div>

    {{-- ─── Grille des 3 actualités ──────────────────────────── --}}
    <div id="nw" class="mt-10 grid gap-6 md:grid-cols-3">
        @forelse($actualites ?? [] as $actu)
            @php
                $coverUrl = $actu->getFirstMediaUrl('cover', 'card');
                $hasCover = filled($coverUrl);
                $excerpt  = $actu->excerpt($locale);
                $date     = $actu->published_at
                    ? ucfirst($actu->published_at->isoFormat($locale === 'fr' ? 'D MMMM YYYY' : 'MMMM D, YYYY'))
                    : null;
            @endphp
            <article data-r class="group">
                <a href="{{ route('actualites.show', [$locale, $actu->slug]) }}" class="block">
                    {{-- Image --}}
                    <div class="overflow-hidden">
                        @if ($hasCover)
                            <img src="{{ $coverUrl }}"
                                 alt="{{ $actu->title }}"
                                 class="aspect-[16/10] w-full object-cover transition duration-700 group-hover:scale-105">
                        @else
                            <img data-ph="Actu {{ $loop->iteration }}|175"
                                 alt="{{ $actu->title }}"
                                 class="aspect-[16/10] w-full object-cover transition duration-700 group-hover:scale-105">
                        @endif
                    </div>

                    {{-- Métadonnées --}}
                    <p class="mt-4 text-sm font-semibold text-moss">
                        @if ($actu->is_featured)
                            {{ $t('À la une', 'Featured') }}
                        @endif
                        @if ($date)
                            @if ($actu->is_featured)
                                <i class="font-normal not-italic"> · </i>
                            @endif
                            <i class="font-normal not-italic">{{ $date }}</i>
                        @endif
                    </p>

                    {{-- Titre --}}
                    <h3 class="mt-1 font-serif text-xl font-semibold leading-snug text-lake transition group-hover:text-ochre">
                        {{ $actu->title }}
                    </h3>

                    {{-- Résumé --}}
                    <p class="mt-2 text-sm text-ink/65">
                        {{ $excerpt }}
                    </p>
                </a>
            </article>
        @empty
            <p class="col-span-3 text-center text-sm text-ink/40 italic">
                {{ $t('Aucune actualité publiée pour le moment.', 'No news published yet.') }}
            </p>
        @endforelse
    </div>

</section>