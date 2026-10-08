{{--
    _header.blade.php — En-tête avec logo OAAT, navigation, CTA « Devenir partenaire », menu mobile.
    ────────────────────────────────────────────────────────────────────────────────
    Variables disponibles :
        $locale   (string) — 'fr' ou 'en'
        $t        (helper) — traduction statique : $t('fr', 'en')
--}}
<header id="hd" class="sticky top-0 z-50 border-b border-line bg-white/90 backdrop-blur transition-shadow duration-300">
    <div id="hi" class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-3 transition-[padding] duration-300 lg:px-8">

        {{-- ═══ Logo OAAT ═══════════════════════════════════════ --}}
        <a href="{{ route('accueil', ['locale' => $locale]) }}" class="flex items-center gap-3" aria-label="OAAT, {{ $t('accueil', 'home') }}">
            <svg viewBox="0 0 40 40" class="h-11 w-11" aria-hidden="true">
                <rect width="40" height="40" rx="9" fill="#17508F"/>
                <path d="M5 27c6-9 11 3 17-6s8-5 13-9M5 21c5-8 10 2 15-5s9-4 15-8M5 33c7-8 12 3 18-5s7-4 12-7"
                      fill="none" stroke="#E3A82B" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span class="leading-tight">
                <b class="block font-serif text-xl text-lake">OAAT</b>
                <span class="block max-w-[200px] text-[11px] text-ink/60">
                    {{ $t("Organisation Africaine pour l'Aménagement des Territoires", 'African Organisation for Territorial Planning') }}
                </span>
            </span>
        </a>

        {{-- ═══ Navigation desktop ═══════════════════════════════ --}}
        @php $navItems = [
            [route('accueil', ['locale' => $locale]),              $t('Accueil',                       'Home')],
            [route('organisation', ['locale' => $locale]),         $t("L'Organisation",               'Organisation')],
            [route('domaines.index', ['locale' => $locale]),       $t("Domaines d'intervention",       'Areas of intervention')],
            [route('projets.index', ['locale' => $locale]),        $t('Projets',                       'Projects')],
            [route('actualites.index', ['locale' => $locale]),     $t('Actualités',                    'News')],
            [route('partenaires', ['locale' => $locale]),          $t('Partenaires',                   'Partners')],
            [route('contact', ['locale' => $locale]),              $t('Contact',                       'Contact')],
        ]; @endphp

        <nav id="nav" class="hidden items-center xl:flex"
             aria-label="{{ $t('Navigation principale', 'Main navigation') }}">
            @foreach($navItems as $item)
                <a href="{{ $item[0] }}"
                   class="nl relative px-2.5 py-2 text-[13px] font-medium text-ink/75 transition hover:text-lake after:absolute after:inset-x-2.5 after:-bottom-0.5 after:h-0.5 after:origin-left after:scale-x-0 after:bg-ochre after:transition after:duration-300 hover:after:scale-x-100">
                    {{ $item[1] }}
                </a>
            @endforeach
        </nav>

        {{-- ═══ CTA « Devenir partenaire » ═══════════════════════ --}}
        <a href="{{ route('contact', ['locale' => $locale]) }}"
           class="hidden rounded-full bg-lake px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-deep hover:shadow-lg 2xl:block relative overflow-hidden
                  before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%]">
            {{ $t('Devenir partenaire', 'Become a partner') }}
        </a>

        {{-- ═══ Bouton menu mobile (hamburger) ═══════════════════ --}}
        <button id="bt"
                aria-label="{{ $t('Ouvrir le menu', 'Open menu') }}"
                aria-expanded="false"
                class="grid h-11 w-11 place-items-center rounded-full border border-line transition hover:bg-mist xl:hidden">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 7h16M4 12h16M4 17h16"/>
            </svg>
        </button>
    </div>

    {{-- ═══ Menu mobile (coulissant) ════════════════════════════ --}}
    <div id="mn" class="grid grid-rows-[0fr] bg-white transition-[grid-template-rows] duration-300 xl:hidden">
        <div class="overflow-hidden">
            <nav id="mnav" class="flex flex-col border-t border-line p-4"
                 aria-label="{{ $t('Navigation mobile', 'Mobile navigation') }}">
                @foreach($navItems as $item)
                    <a href="{{ $item[0] }}"
                       class="px-3 py-3 font-medium transition hover:bg-mist hover:text-lake">
                        {{ $item[1] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>
</header>