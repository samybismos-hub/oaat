{{--
    _galerie_media.blade.php — Une vignette <figure class="ph"> de la galerie.
    ────────────────────────────────────────────────────────────────────────
    Consommé par _galerie_grille (boucle $medias). Les data-* sont lus par
    oaat.js §23 (catalogue, filtres, tri, visionneuse) — contrat complet :
    Implémentation_Album.md §3.1. Variable : $m (une entrée du catalogue).
--}}
@php
    $id    = (int)    ($m['id'] ?? 0);
    $y     = (string)  ($m['y'] ?? '');
    $w     = (int)    ($m['w'] ?? 0);
    $h     = (int)    ($m['h'] ?? 0);
    $tf    = (string)  ($m['tf'] ?? '');
    $te    = (string)  ($m['te'] ?? $tf);
    $par   = (array)   ($m['par'] ?? []);
    $col   = (array)   ($m['col'] ?? []);
    $used  = (array)   ($m['use'] ?? []);
    $pairs = (array)   ($m['tags'] ?? []);              // [{fr,en}] ou ['fr~en', …]
    $tagsS = ($m['tagsRaw'] ?? null) ?: collect($pairs)
        ->map(fn ($x, $k) => (is_array($x) ? ($x['fr'] ?? $k) : $x) . '~' . (is_array($x) ? ($x['en'] ?? ($x['fr'] ?? $k)) : $x))
        ->implode('|');
    $tagsV = collect($pairs)->map(fn ($x, $k) => is_array($x) ? $t($x['fr'] ?? $k, $x['en'] ?? ($x['fr'] ?? $k)) : $x);
    $catFr = $m['catLabel'] ?? '';
    $catEn = $m['catLabelEn'] ?? $catFr;
    $lieu  = $m['lieuLabel'] ?? '';
@endphp
<figure id="g{{ $id }}" class="ph group/f mb-3 break-inside-avoid overflow-hidden bg-mist"
        data-id="{{ $id }}"
        @if ($y !== '') data-y="{{ $y }}" @endif
        data-w="{{ $w }}" data-h="{{ $h }}"
        data-cat="{{ $m['cat'] ?? '' }}" data-th="{{ $m['th'] ?? '' }}"
        data-lieu="{{ $m['lieu'] ?? '' }}" data-ori="{{ $m['ori'] ?? '' }}"
        data-par="{{ implode('|', $par) }}"
        data-col="{{ implode(' ', $col) }}"
        data-use="{{ implode(' ', $used) }}"
        data-tf="{{ $tf }}" data-te="{{ $te }}"
        data-pfr="{{ $m['pfr'] ?? '' }}" data-pen="{{ $m['pen'] ?? ($m['pfr'] ?? '') }}"
        data-src="{{ $m['src'] ?? '' }}" data-sfr="{{ $m['sfr'] ?? '' }}" data-sen="{{ $m['sen'] ?? ($m['sfr'] ?? '') }}"
        data-tags="{{ $tagsS }}"
        data-q="{{ $m['q'] ?? '' }}"
        data-dl="{{ $m['dl'] ?? '' }}">

    {{-- Zone cliquable : ouvre la visionneuse --}}
    <button type="button"
            class="open relative block h-full w-full shrink-0 overflow-hidden text-left group-data-[view=list]/g:h-auto group-data-[view=list]/g:w-48 group-data-[view=list]/g:aspect-[4/3]"
            aria-label="{{ $t('Ouvrir :', 'Open:') }} {{ $tf }}">
        <img src="{{ $m['card'] ?? '' }}" width="{{ $w }}" height="{{ $h }}"
             alt="{{ $tf }}" loading="lazy"
             class="block h-full w-full object-cover transition duration-700 group-hover/f:scale-105">
        {{-- Légende au survol (mode mosaïque) --}}
        <span class="cap pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink/90 via-ink/50 to-transparent p-3 pt-14 text-xs leading-snug text-white opacity-0 transition duration-300 group-hover/f:opacity-100 group-focus-within/f:opacity-100 group-data-[view=list]/g:hidden">
            <span class="line-clamp-2 font-medium">{{ $tf }}</span>
            @if ($y !== '')
                <span class="mt-1 block text-white/70">{{ $y }}</span>
            @endif
        </span>
    </button>

    {{-- Bouton de sélection (alimente le panier localStorage oaat-sel) --}}
    <button type="button"
            class="sel absolute right-2 top-2 grid h-8 w-8 place-items-center border border-white/70 bg-ink/40 text-white opacity-0 transition hover:bg-lake group-hover/f:opacity-100 group-focus-within/f:opacity-100 aria-pressed:opacity-100 aria-pressed:bg-lake group-data-[view=list]/g:static group-data-[view/list]/g:order-3 group-data-[view/list]/g:border-line group-data-[view/list]/g:bg-white group-data-[view/list]/g:text-ink/60 group-data-[view/list]/g:opacity-100"
            aria-pressed="false" aria-label="{{ $t('Sélectionner', 'Select') }}">
        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true">
            <path d="M5 12l5 5 9-10"/>
        </svg>
    </button>

    {{-- Métadonnées (visibles en vue « liste ») --}}
    <div class="meta hidden min-w-0 grow group-data-[view=list]/g:block">
        <p class="text-xs font-semibold uppercase tracking-wider text-lake">
            {{ $t($catFr, $catEn) }}
            @if ($y !== '')
                <span class="text-ink/40"> · {{ $y }}</span>
            @endif
        </p>
        <h3 class="mt-1 font-serif text-lg font-semibold leading-snug text-ink">{{ $tf }}</h3>
        <p class="mt-1 text-sm text-ink/65">
            <span>{{ $lieu }}</span>
            @if ($id)
                <span class="text-ink/40"> · OAAT-{{ str_pad($id, 4, '0', STR_PAD_LEFT) }}</span>
            @endif
        </p>
        @if ($tagsV->isNotEmpty())
            <p class="mt-3 flex flex-wrap gap-1.5">
                @foreach ($tagsV as $tag)
                    <span class="rounded-full border border-line px-2 py-0.5 text-xs text-ink/70">{{ $tag }}</span>
                @endforeach
            </p>
        @endif
    </div>
</figure>