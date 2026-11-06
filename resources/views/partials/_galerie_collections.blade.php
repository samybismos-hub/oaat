{{--
    _galerie_collections.blade.php — Bandeau « Collections » de la galerie.
    ────────────────────────────────────────────────────────────────────────
    Carrousel à défilement (#cs) de boutons .cc[data-col] : cliquer applique
    la collection comme filtre (JS §23.7). L'illustration est un dégradé dont
    la teinte ('hue') est fournie avec la collection.
    Variables : $collections, $locale, $t.
--}}
@php
    $collections = collect($collections ?? []);
@endphp
<section id="collections" class="border-b border-line bg-mist py-10">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">
        <div class="flex items-end justify-between gap-4">
            <h2 class="font-serif text-2xl font-semibold text-ink">{{ $t('Collections', 'Collections') }}</h2>
            <p class="text-sm text-ink/55">{{ $t('Albums sélectionnés par l’administration', 'Albums curated by the administration') }}</p>
        </div>

        @if ($collections->isNotEmpty())
            <div id="cs" class="mt-5 flex snap-x gap-4 overflow-x-auto pb-3">
                @foreach ($collections as $c)
                    @php $hue = (int) ($c['hue'] ?? 205); @endphp
                    <button type="button" data-col="{{ $c['code'] }}"
                            class="cc group/c relative block w-64 shrink-0 snap-start overflow-hidden bg-ink text-left text-white sm:w-72">
                        {{-- Illustration (dégradé) + zoom au survol --}}
                        <span class="absolute inset-x-0 top-0 h-44 transition duration-700 group-hover/c:scale-105"
                              style="background:linear-gradient(135deg, hsl({{ $hue }},45%,26%), hsl({{ $hue + 40 }},50%,44%))"
                              aria-hidden="true"></span>
                        <span class="absolute inset-0 bg-gradient-to-t from-ink/90 via-ink/30 to-transparent" aria-hidden="true"></span>
                        <span class="absolute inset-x-0 bottom-0 p-4">
                            <span class="block font-serif text-lg font-semibold leading-snug">{{ $t($c['fr'] ?? '', $c['en'] ?? '') }}</span>
                            <span class="mt-1 block text-xs text-white/70">{{ $c['count'] ?? 0 }} {{ $t('médias', 'media') }}</span>
                        </span>
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</section>