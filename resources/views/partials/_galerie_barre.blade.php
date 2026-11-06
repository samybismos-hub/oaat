{{--
    _galerie_barre.blade.php — Onglets catégories + barre d'outils de la galerie.
    ────────────────────────────────────────────────────────────────────────
    Bloc collant (lg:sticky, cf. maquette l.87-164) : onglets #tabs (.tb),
    bouton mobile #fbtn (badge #fn), recherche #q, compteur #rc, densité
    #dens, tri #so, sélecteur de vue .vb[data-v].
    Variables : $categories (Tout + catégories), $total, $locale, $t.
--}}
<div class="z-30 border-b border-line bg-white/95 backdrop-blur lg:sticky lg:top-[60px]">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">

        {{-- Onglets catégories : « Tout » (data-c="") est l'état actif initial --}}
        <div id="tabs" class="flex overflow-x-auto" role="tablist" aria-label="{{ $t('Catégories', 'Categories') }}">
            <button type="button" data-c=""
                    class="tb shrink-0 whitespace-nowrap border-b-2 border-lake px-4 py-3.5 text-sm font-medium text-lake transition">
                <span>{{ $t('Tout', 'All') }}</span>
                <span class="cnt text-xs text-ink/50">{{ $total }}</span>
            </button>
            @foreach ($categories as $cat)
                <button type="button" data-c="{{ $cat['code'] }}"
                        class="tb shrink-0 whitespace-nowrap border-b-2 border-transparent px-4 py-3.5 text-sm font-medium text-ink/70 transition hover:text-lake">
                    <span>{{ $t($cat['fr'] ?? '', $cat['en'] ?? '') }}</span>
                    <span class="cnt text-xs text-ink/50">{{ $cat['count'] ?? 0 }}</span>
                </button>
            @endforeach
        </div>

        {{-- Barre d'outils --}}
        <div class="flex flex-wrap items-center gap-3 border-t border-line py-2.5">
            {{-- Ouverture du tiroir de facettes (mobile) + badge de filtres actifs (#fn) --}}
            <button type="button" id="fbtn" class="inline-flex h-10 items-center gap-2 border border-lake px-4 text-sm font-semibold text-lake lg:hidden">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M4 6h16M7 12h10M10 18h4"/>
                </svg>
                {{ $t('Filtres', 'Filters') }}
                <span id="fn" class="hidden bg-lake px-1.5 text-xs text-white"></span>
            </button>

            {{-- Recherche (reliée à §23 via #q) --}}
            <label class="relative block min-w-[12rem] grow lg:max-w-md">
                <svg viewBox="0 0 24 24" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink/50" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="M21 21l-4-4"/>
                </svg>
                <input id="q" type="search" aria-label="{{ $t('Rechercher', 'Search') }}"
                       placeholder="{{ $t('Rechercher une image, un lieu, un partenaire…', 'Search an image, a place, a partner…') }}"
                       class="h-10 w-full border border-line pl-9 pr-3 text-sm outline-none transition focus:border-lake focus:ring-4 focus:ring-lake/15">
            </label>

            {{-- Compteur d'éléments visibles (rempli par le JS §23.6) --}}
            <p id="rc" class="hidden whitespace-nowrap text-sm text-ink/60 xl:block" aria-live="polite"></p>

            <div class="ml-auto flex flex-wrap items-center gap-3">
                {{-- Densité de la mosaïque (persistée en localStorage) --}}
                <label class="hidden items-center gap-2 text-sm text-ink/60 xl:flex">
                    {{ $t('Taille', 'Size') }}
                    <input id="dens" type="range" min="2" max="6" value="4" class="w-24 accent-[#17508F]">
                </label>

                {{-- Tri : rec (défaut) / old / az / rnd --}}
                <select id="so" aria-label="{{ $t('Tri', 'Sort') }}" class="h-10 border border-line bg-white px-3 text-sm outline-none transition focus:border-lake focus:ring-4 focus:ring-lake/15">
                    <option value="rec">{{ $t('Plus récentes', 'Newest') }}</option>
                    <option value="old">{{ $t('Plus anciennes', 'Oldest') }}</option>
                    <option value="az">{{ $t('Titre A–Z', 'Title A–Z') }}</option>
                    <option value="rnd">{{ $t('Aléatoire', 'Random') }}</option>
                </select>

                {{-- Vues : mosaïque / grille / liste --}}
                <div id="vm" class="flex border border-line">
                    <button type="button" data-v="masonry"
                            class="vb grid h-10 w-10 place-items-center border-r border-line text-ink/60 transition last:border-r-0 hover:text-lake"
                            aria-label="{{ $t('Mosaïque', 'Masonry') }}" title="{{ $t('Mosaïque', 'Masonry') }}">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h7v9H4zM13 4h7v5h-7zM13 11h7v9h-7zM4 15h7v5H4z"/></svg>
                    </button>
                    <button type="button" data-v="grid"
                            class="vb grid h-10 w-10 place-items-center border-r border-line text-ink/60 transition last:border-r-0 hover:text-lake"
                            aria-label="{{ $t('Grille', 'Grid') }}" title="{{ $t('Grille', 'Grid') }}">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"/></svg>
                    </button>
                    <button type="button" data-v="list"
                            class="vb grid h-10 w-10 place-items-center border-r border-line text-ink/60 transition last:border-r-0 hover:text-lake"
                            aria-label="{{ $t('Liste', 'List') }}" title="{{ $t('Liste', 'List') }}">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>