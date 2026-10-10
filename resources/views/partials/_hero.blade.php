{{--
    _hero.blade.php — Section héro (carrousel 3 diapos).
    ────────────────────────────────────────────────────────────────
    Variables disponibles :
        $locale   (string)
        $t        (helper)
--}}
<section id="accueil"
         class="group relative h-[80vh] min-h-[560px] overflow-hidden bg-deep text-white"
         aria-roledescription="carrousel"
         aria-label="{{ $t('À la une', 'Highlights') }}">

    {{-- ─── Conteneur des diapos (#sl) ─────────────────────────── --}}
    <div id="sl">

        {{-- ═══ Diapositive 1 — active par défaut ═══════════════ --}}
        <div class="absolute inset-0 opacity-100 z-10 transition-opacity duration-1000">
            <img data-ph="Hero 1|195" alt=""
                 class="absolute inset-0 h-full w-full scale-100 object-cover transition-transform duration-[8000ms] ease-out">
            <div class="absolute inset-0 bg-gradient-to-r from-deep via-deep/75 to-deep/10"></div>
            <div class="relative mx-auto flex h-full max-w-7xl items-center px-5 lg:px-8">
                <div class="t max-w-2xl pb-16">
                    <h2 class="font-serif text-4xl font-semibold leading-[1.1] md:text-6xl transition duration-1000 delay-300">
                        {{ $t("Aménager les territoires, bâtir des communautés résilientes",
                              "Planning territories, building resilient communities") }}
                    </h2>
                    <p class="mt-5 max-w-xl text-lg text-white/80 transition duration-1000 delay-500">
                        {{ $t("ONG interafricaine active à l'Est de la RD Congo depuis 1995, aux côtés des populations les plus vulnérables.",
                              "Pan-African NGO active in Eastern DRC since 1995, working alongside the most vulnerable populations.") }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3 transition duration-1000 delay-700">
                        <a href="{{ route('organisation', ['locale' => $locale]) }}"
                           class="rounded-full bg-ochre px-7 py-3.5 font-semibold text-deep transition hover:-translate-y-0.5 hover:shadow-xl relative overflow-hidden before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%]">
                            {{ $t("Découvrir l'OAAT", 'Discover OAAT') }}
                        </a>
                        <a href="{{ route('contact', ['locale' => $locale]) }}"
                           class="rounded-full border border-white/40 px-7 py-3.5 font-semibold transition hover:bg-white hover:text-deep">
                            {{ $t('Nous contacter', 'Contact us') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ Diapositive 2 : Projets ═════════════════════════ --}}
        <div class="absolute inset-0 opacity-0 transition-opacity duration-1000">
            <img data-ph="Hero 2|165" alt=""
                 class="absolute inset-0 h-full w-full scale-110 object-cover transition-transform duration-[8000ms] ease-out">
            <div class="absolute inset-0 bg-gradient-to-r from-deep via-deep/75 to-deep/10"></div>
            <div class="relative mx-auto flex h-full max-w-7xl items-center px-5 lg:px-8">
                <div class="t max-w-2xl pb-16">
                    <h2 class="translate-y-8 opacity-0 font-serif text-4xl font-semibold leading-[1.1] md:text-6xl transition duration-1000 delay-300">
                        {{ $t("Eau, santé, éducation : des infrastructures qui durent",
                              "Water, health, education: lasting infrastructure") }}
                    </h2>
                    <p class="mt-5 max-w-xl translate-y-8 opacity-0 text-lg text-white/80 transition duration-1000 delay-500">
                        {{ $t("Écoles, centres de santé, ponts et routes de desserte agricole réalisés avec les agences des Nations Unies et nos partenaires.",
                              "Schools, health centers, bridges and farm-to-market roads built with UN agencies and our partners.") }}
                    </p>
                    <div class="mt-8 flex translate-y-8 opacity-0 flex-wrap gap-3 transition duration-1000 delay-700">
                        <a href="{{ route('projets.index', ['locale' => $locale]) }}"
                           class="rounded-full bg-ochre px-7 py-3.5 font-semibold text-deep transition hover:-translate-y-0.5 hover:shadow-xl relative overflow-hidden before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%]">
                            {{ $t('Voir les projets', 'See projects') }}
                        </a>
                        <a href="{{ route('contact', ['locale' => $locale]) }}"
                           class="rounded-full border border-white/40 px-7 py-3.5 font-semibold transition hover:bg-white hover:text-deep">
                            {{ $t('Nous contacter', 'Contact us') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ Diapositive 3 : Domaines ════════════════════════ --}}
        <div class="absolute inset-0 opacity-0 transition-opacity duration-1000">
            <img data-ph="Hero 3|40" alt=""
                 class="absolute inset-0 h-full w-full scale-110 object-cover transition-transform duration-[8000ms] ease-out">
            <div class="absolute inset-0 bg-gradient-to-r from-deep via-deep/75 to-deep/10"></div>
            <div class="relative mx-auto flex h-full max-w-7xl items-center px-5 lg:px-8">
                <div class="t max-w-2xl pb-16">
                    <h2 class="translate-y-8 opacity-0 font-serif text-4xl font-semibold leading-[1.1] md:text-6xl transition duration-1000 delay-300">
                        {{ $t("Sécurité alimentaire et paix durable dans la région des Grands Lacs",
                              "Food security and lasting peace in the Great Lakes region") }}
                    </h2>
                    <p class="mt-5 max-w-xl translate-y-8 opacity-0 text-lg text-white/80 transition duration-1000 delay-500">
                        {{ $t("Accompagner les ménages agricoles, déplacés et retournés vers l'autonomie et la cohabitation pacifique.",
                              "Supporting farming households, displaced persons and returnees towards autonomy and peaceful coexistence.") }}
                    </p>
                    <div class="mt-8 flex translate-y-8 opacity-0 flex-wrap gap-3 transition duration-1000 delay-700">
                        <a href="{{ route('domaines.index', ['locale' => $locale]) }}"
                           class="rounded-full bg-ochre px-7 py-3.5 font-semibold text-deep transition hover:-translate-y-0.5 hover:shadow-xl relative overflow-hidden before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%]">
                            {{ $t("Nos domaines d'intervention", 'Our areas of intervention') }}
                        </a>
                        <a href="{{ route('contact', ['locale' => $locale]) }}"
                           class="rounded-full border border-white/40 px-7 py-3.5 font-semibold transition hover:bg-white hover:text-deep">
                            {{ $t('Nous contacter', 'Contact us') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
</div>
{{-- ─── Lignes décoratives animées (dash) ────────────────── --}}
    <svg class="pointer-events-none absolute inset-0 z-[15] h-full w-full opacity-30"
         viewBox="0 0 1440 800"
         preserveAspectRatio="none"
         fill="none" stroke="#E3A82B" stroke-width="1.3" aria-hidden="true">
        <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"
              class="animate-dash motion-reduce:animate-none"
              d="M-20 600C220 520 380 690 640 600S1060 470 1460 560"/>
        <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"
              class="animate-dash motion-reduce:animate-none" style="animation-delay:.5s"
              d="M-20 650C240 580 400 730 680 650S1080 530 1460 610"/>
        <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"
              class="animate-dash motion-reduce:animate-none" style="animation-delay:1s"
              d="M-20 700C260 640 420 770 700 700S1100 590 1460 660"/>
        <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"
              class="animate-dash motion-reduce:animate-none" style="animation-delay:1.5s"
              d="M-20 550C200 470 360 640 620 550S1040 420 1460 510"/>
    </svg>

    {{-- ─── Barre de contrôle (compteur, dots, prev/next) ───── --}}
    <div class="absolute inset-x-0 bottom-0 z-20 pb-24 md:pb-28">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 lg:px-8">
            <div class="flex items-center gap-5">
                <!--span id="cn" class="whitespace-nowrap font-serif text-lg tabular-nums text-white/80">
                    01 / 03
                </span-->
                <!--div id="dots" class="flex gap-2">
                    <button aria-label="{{ $t('Diapositive 1', 'Slide 1') }}"
                            class="h-1.5 w-12 overflow-hidden rounded bg-white/30">
                        <i class="block h-full w-0 bg-ochre group-hover:[animation-play-state:paused]
                                  motion-reduce:[animation-play-state:paused]"></i>
                    </button>
                    <button aria-label="{{ $t('Diapositive 2', 'Slide 2') }}"
                            class="h-1.5 w-12 overflow-hidden rounded bg-white/30">
                        <i class="block h-full w-0 bg-ochre group-hover:[animation-play-state:paused]
                                  motion-reduce:[animation-play-state:paused]"></i>
                    </button>
                    <button aria-label="{{ $t('Diapositive 3', 'Slide 3') }}"
                            class="h-1.5 w-12 overflow-hidden rounded bg-white/30">
                        <i class="block h-full w-0 bg-ochre group-hover:[animation-play-state:paused]
                                  motion-reduce:[animation-play-state:paused]"></i>
                    </button>
                </div-->
            </div>
            <div class="flex gap-2">
                <button id="pv"
                        aria-label="{{ $t('Précédent', 'Previous') }}"
                        class="grid h-11 w-11 place-items-center rounded-full border border-white/30 transition hover:bg-white hover:text-deep">
                    &lsaquo;
                </button>
                <button id="nx"
                        aria-label="{{ $t('Suivant', 'Next') }}"
                        class="grid h-11 w-11 place-items-center rounded-full border border-white/30 transition hover:bg-white hover:text-deep">
                    &rsaquo;
                </button>
            </div>
        </div>
    </div>
</section>
