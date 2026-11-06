{{--
    _galerie_facettes.blade.php — Panneau de filtres (colonne desktop / tiroir mobile).
    ────────────────────────────────────────────────────────────────────────
    aside #fb (tiroir mobile/colonne desktop via classes lg:*) + voile #fbk.
    Groupes <details> servis par $facettes, filtre années dédié (#y0/#y1,
    #y0t/#y1t/#yr, .yb, .dec), bouton #fclr. Les compteurs .cnt sont mis à
    jour par §23.4. Variables : $facettes, $annees (min/max/bars), $locale, $t.
--}}
@php
    $groups = collect($facettes ?? []);
    $ymin   = ! empty($annees['min']) ? (int) $annees['min'] : 1994;
    $ymax   = ! empty($annees['max']) ? (int) $annees['max'] : 2024;
    $bars   = collect($annees['bars'] ?? []);
    // Classe commune des deux curseurs (thumb WebKit + Firefox, héritée de la maquette).
    $range  = 'pointer-events-none absolute inset-x-0 top-0 h-5 w-full appearance-none bg-transparent [&::-webkit-slider-runnable-track]:bg-transparent [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:h-5 [&::-webkit-slider-thumb]:w-5 [&::-webkit-slider-thumb]:cursor-pointer [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:border-2 [&::-webkit-slider-thumb]:border-white [&::-webkit-slider-thumb]:bg-lake [&::-webkit-slider-thumb]:shadow [&::-moz-range-thumb]:pointer-events-auto [&::-moz-range-thumb]:h-5 [&::-moz-range-thumb]:w-5 [&::-moz-range-thumb]:cursor-pointer [&::-moz-range-thumb]:border-2 [&::-moz-range-thumb]:border-white [&::-moz-range-thumb]:bg-lake';
@endphp

<aside id="fb"
       class="fixed inset-y-0 left-0 z-[70] w-[88%] max-w-sm -translate-x-full overflow-y-auto bg-white px-6 pb-10 transition-transform duration-300 lg:sticky lg:top-[132px] lg:z-auto lg:max-h-[calc(100vh-9rem)] lg:w-auto lg:max-w-none lg:translate-x-0 lg:self-start lg:px-0 lg:pb-6"
       aria-label="{{ $t('Filtres', 'Filters') }}">

    {{-- Tête du tiroir (mobile uniquement) --}}
    <div class="flex items-center justify-between border-b border-line py-4 lg:hidden">
        <p class="font-serif text-xl font-semibold">{{ $t('Filtres', 'Filters') }}</p>
        <button type="button" id="fclose" class="h-10 w-10 border border-line" aria-label="{{ $t('Fermer', 'Close') }}">✕</button>
    </div>

    {{-- Groupes de facettes (cases à cocher, une par valeur) --}}
    @foreach ($groups as $g)
        <details class="border-b border-line py-4" @if ($g['open'] ?? false) open @endif>
            <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold uppercase tracking-wider text-ink">
                {{ $t($g['fr'] ?? '', $g['en'] ?? '') }}
                <span class="text-ink/40" aria-hidden="true">+</span>
            </summary>
            <div class="mt-3 {{ ($g['scroll'] ?? false) ? 'max-h-52 overflow-y-auto pr-1' : '' }}">
                @foreach ($g['values'] ?? [] as $v)
                    <label class="flex cursor-pointer items-center gap-3 py-1.5 text-sm text-ink/80 transition hover:text-lake">
                        <input type="checkbox" data-f="{{ $g['f'] }}" value="{{ $v['v'] }}" class="h-4 w-4 shrink-0 accent-[#17508F]">
                        <span class="grow">{{ $t($v['fr'] ?? '', $v['en'] ?? '') }}</span>
                        <span class="cnt text-xs tabular-nums text-ink/45">{{ $v['count'] ?? 0 }}</span>
                    </label>
                @endforeach
            </div>
        </details>
    @endforeach

    {{-- Filtre années --}}
    <details class="border-b border-line py-4" open>
        <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold uppercase tracking-wider text-ink">
            {{ $t('Année', 'Year') }}
            <span class="text-ink/40" aria-hidden="true">+</span>
        </summary>
        <div class="mt-4">
            {{-- Histogramme des années (.yb), hauteurs fournies par le contrôleur --}}
            @if ($bars->isNotEmpty())
                <div class="flex h-12 items-end gap-px" aria-hidden="true">
                    @foreach ($bars as $annee => $hauteur)
                        <i class="yb block w-full bg-line transition-colors" data-y="{{ $annee }}" style="height:{{ $hauteur }}%"></i>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-ink/60">{{ $t('Aucune année renseignée.', 'No year recorded.') }}</p>
            @endif

            {{-- Double curseur sur le rail (#yr colore la période active) --}}
            <div class="relative mt-1 h-5">
                <div class="absolute inset-x-0 top-2 h-1 bg-line" aria-hidden="true"></div>
                <div id="yr" class="absolute top-2 h-1 bg-lake" aria-hidden="true"></div>
                <input id="y0" type="range" min="{{ $ymin }}" max="{{ $ymax }}" value="{{ $ymin }}"
                       aria-label="{{ $t('Année de début', 'Start year') }}" class="{{ $range }}">
                <input id="y1" type="range" min="{{ $ymin }}" max="{{ $ymax }}" value="{{ $ymax }}"
                       aria-label="{{ $t('Année de fin', 'End year') }}" class="{{ $range }}">
            </div>
            <p class="mt-2 flex justify-between text-sm font-semibold text-ink">
                <span id="y0t">{{ $ymin }}</span>
                <span id="y1t">{{ $ymax }}</span>
            </p>

            {{-- Raccourcis par décennie --}}
            <div class="mt-3 flex flex-wrap gap-2">
                <button type="button" class="dec border border-line px-2.5 py-1 text-xs font-medium text-ink/70 transition hover:border-lake hover:text-lake" data-y0="1994" data-y1="1999">90s</button>
                <button type="button" class="dec border border-line px-2.5 py-1 text-xs font-medium text-ink/70 transition hover:border-lake hover:text-lake" data-y0="2000" data-y1="2009">2000s</button>
                <button type="button" class="dec border border-line px-2.5 py-1 text-xs font-medium text-ink/70 transition hover:border-lake hover:text-lake" data-y0="2010" data-y1="2019">2010s</button>
                <button type="button" class="dec border border-line px-2.5 py-1 text-xs font-medium text-ink/70 transition hover:border-lake hover:text-lake" data-y0="2020" data-y1="2029">2020s</button>
            </div>

            <p class="mt-3 text-xs text-ink/50">{{ $t('Les images sans date restent visibles tant que toute la période est sélectionnée.', 'Undated images stay visible while the whole period is selected.') }}</p>
        </div>
    </details>

    {{-- Réinitialiser tous les filtres --}}
    <button type="button" id="fclr" class="mt-6 w-full border border-lake px-4 py-3 text-sm font-semibold text-lake transition hover:bg-lake hover:text-white">
        {{ $t('Réinitialiser les filtres', 'Reset filters') }}
    </button>
</aside>

{{-- Voile au-dessus du contenu quand le tiroir mobile est ouvert --}}
<div id="fbk" class="fixed inset-0 z-[65] hidden bg-ink/60 lg:hidden"></div>