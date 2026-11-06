{{--
    _galerie_chips.blade.php — Chips des filtres actifs.
    ────────────────────────────────────────────────────────────────────────
    Conteneur #chips, masqué/affiché et décoré par §23.4. Une .chip par valeur
    de facette possible (+ catégories en data-f="cat"), chips dynamiques
    « années » (#chy) et « recherche » (#chq), puis « Tout effacer » (#cclr).
    Variables : $categories, $facettes, $locale, $t.
--}}
<div id="chips" class="mb-6 hidden flex-wrap items-center gap-2">
    <span class="mr-1 text-sm text-ink/55">{{ $t('Filtres actifs', 'Active filters') }} :</span>

    {{-- Chip par catégorie (onglets) --}}
    @foreach ($categories as $cat)
        <button type="button" data-f="cat" data-v="{{ $cat['code'] }}"
                class="chip hidden items-center gap-2 border border-lake/30 bg-lake/5 px-3 py-1.5 text-sm text-lake transition hover:bg-lake hover:text-white">
            {{ $t($cat['fr'] ?? '', $cat['en'] ?? '') }}<span aria-hidden="true">✕</span>
        </button>
    @endforeach

    {{-- Chip par valeur de facette --}}
    @foreach ($facettes as $g)
        @foreach ($g['values'] ?? [] as $v)
            <button type="button" data-f="{{ $g['f'] }}" data-v="{{ $v['v'] }}"
                    class="chip hidden items-center gap-2 border border-lake/30 bg-lake/5 px-3 py-1.5 text-sm text-lake transition hover:bg-lake hover:text-white">
                {{ $t($v['fr'] ?? '', $v['en'] ?? '') }}<span aria-hidden="true">✕</span>
            </button>
        @endforeach
    @endforeach

    {{-- Chips dynamiques : libellé rempli par le JS (années, recherche) --}}
    <button type="button" data-f="y"
            class="chip hidden items-center gap-2 border border-lake/30 bg-lake/5 px-3 py-1.5 text-sm text-lake transition hover:bg-lake hover:text-white">
        <span id="chy"></span><span aria-hidden="true">✕</span>
    </button>
    <button type="button" data-f="q"
            class="chip hidden items-center gap-2 border border-lake/30 bg-lake/5 px-3 py-1.5 text-sm text-lake transition hover:bg-lake hover:text-white">
        <span id="chq"></span><span aria-hidden="true">✕</span>
    </button>

    {{-- Tout effacer --}}
    <button type="button" id="cclr" class="chip-clear ml-1 text-sm font-semibold text-ink/60 underline-offset-4 hover:text-lake hover:underline">
        {{ $t('Tout effacer', 'Clear all') }}
    </button>
</div>