{{--
    _galerie_hero.blade.php — Héro de la galerie (page pages/galerie-index).
    ────────────────────────────────────────────────────────────────────────
    Bloc clair (cf. maquette Galerie.html l.26-29) : fil d'Ariane, grand titre,
    compteurs, boutons « Lancer le diaporama » (#sl0) et « Raccourcis clavier »
    (#hlp), puis recherches fréquentes ([data-qs]).
    Variables : $stats, $qs, $locale, $t. Hooks : Implémentation_Album.md §3.2.
--}}
<section id="top" class="relative overflow-hidden border-b border-line bg-white">
    {{-- Liseré pointillé supérieur (décor maquette) --}}
    <div class="h-2 bg-[repeating-linear-gradient(90deg,#B9C5D3_0_1px,transparent_1px_16px)] opacity-70" aria-hidden="true"></div>

    {{-- Trame décorative (chemins animés hérités de la maquette) --}}
    <svg class="pointer-events-none absolute inset-0 z-0 h-full w-full opacity-20" viewBox="0 0 1440 800"
         preserveAspectRatio="none" fill="none" stroke="#E3A82B" stroke-width="1.3" aria-hidden="true">
        <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"
              class="animate-dash motion-reduce:animate-none" d="M-20 600C220 520 380 690 640 600S1060 470 1460 560"/>
        <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"
              class="animate-dash motion-reduce:animate-none" style="animation-delay:.5s" d="M-20 650C240 580 400 730 680 650S1080 530 1460 610"/>
        <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"
              class="animate-dash motion-reduce:animate-none" style="animation-delay:1s" d="M-20 700C260 640 420 770 700 700S1100 590 1460 660"/>
        <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"
              class="animate-dash motion-reduce:animate-none" style="animation-delay:1.5s" d="M-20 550C200 470 360 640 620 550S1040 420 1460 510"/>
    </svg>

    <div class="relative z-10 mx-auto max-w-7xl px-5 pb-12 pt-8 lg:px-8 lg:pb-16">

        {{-- Fil d'Ariane --}}
        <nav aria-label="{{ $t('Fil d’Ariane', 'Breadcrumb') }}" class="flex flex-wrap items-center gap-2 text-sm text-ink/60">
            <a href="{{ route('accueil', ['locale' => $locale]) }}" class="transition hover:text-lake">{{ $t('Accueil', 'Home') }}</a>
            <span aria-hidden="true">›</span>
            <span class="text-ink">{{ $t('Galerie', 'Gallery') }}</span>
        </nav>

        <div class="mt-10 grid gap-10 lg:grid-cols-12 lg:items-end">
            {{-- Titre + introduction --}}
            <div class="lg:col-span-7">
                <p class="text-xs font-semibold uppercase tracking-[.18em] text-lake">{{ $t('Médiathèque', 'Media library') }}</p>
                <h1 class="mt-4 font-serif text-[length:clamp(3.25rem,9vw,7.5rem)] font-semibold leading-[.95] tracking-tight text-ink">
                    {{ $t('Galerie', 'Gallery') }}
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-ink/70">
                    {{ $t("Toutes les images du site de l'OAAT, et celles que l'administration y ajoute : explorez, filtrez, sélectionnez et téléchargez.",
                          'Every image on OAAT website, plus those added by the administration: browse, filter, select and download.') }}
                </p>
            </div>

            {{-- Compteurs (les chiffres [data-n] sont comptés par §5) + boutons --}}
            <div class="lg:col-span-5">
                <dl class="grid grid-cols-3 border-y border-line py-5 text-center">
                    @foreach ($stats as $stat)
                        <div @if ($loop->iteration === 2) class="border-x border-line" @endif>
                            @if (is_numeric($stat['valeur'] ?? null))
                                <dd class="font-serif text-4xl font-semibold text-lake"><span data-n="{{ $stat['valeur'] }}">{{ $stat['valeur'] }}</span></dd>
                            @else
                                <dd class="whitespace-nowrap font-serif text-2xl font-semibold text-lake sm:text-4xl">{{ $stat['valeur'] }}</dd>
                            @endif
                            <dt class="mt-1 text-xs uppercase tracking-wider text-ink/55">{{ $t($stat['fr'] ?? '', $stat['en'] ?? '') }}</dt>
                        </div>
                    @endforeach
                </dl>

                <div class="mt-5 flex flex-wrap items-center gap-3">
                    {{-- Lance le diaporama depuis la première image (#sl0) --}}
                    <button type="button" id="sl0"
                            class="relative overflow-hidden bg-lake px-6 py-3.5 font-semibold text-white transition before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:bg-deep hover:before:left-[150%]">
                        {{ $t('Lancer le diaporama', 'Start slideshow') }}
                    </button>
                    {{-- Ouvre la modal des raccourcis clavier (#hlp) --}}
                    <button type="button" id="hlp" class="border border-line px-4 py-3.5 text-sm font-medium text-ink/70 transition hover:border-lake hover:text-lake">
                        {{ $t('Raccourcis clavier', 'Keyboard shortcuts') }}
                        <kbd class="ml-1 border border-line px-1.5 text-xs">?</kbd>
                    </button>
                </div>
            </div>
        </div>

        {{-- Recherches fréquentes : une puce déclenche la recherche (§23.7) --}}
        @if ($qs->isNotEmpty())
            <div class="mt-8 flex flex-wrap items-center gap-2">
                <span class="mr-1 text-sm text-ink/55">{{ $t('Recherches fréquentes', 'Popular searches') }} :</span>
                @foreach ($qs as $s)
                    <button type="button" data-qs="{{ $s['q'] ?? ($s['fr'] ?? '') }}"
                            class="border border-line bg-white px-3.5 py-1.5 text-sm text-ink/70 transition hover:border-lake hover:bg-lake hover:text-white">
                        {{ $s['fr'] ?? $s['q'] ?? '' }}
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</section>