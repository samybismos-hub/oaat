{{--
    _hero_simple.blade.php — Bandeau interne (pages autres que l'accueil).
    ────────────────────────────────────────────────────────────────
    Consommateurs : pages/projets-index.blade.php, pages/organisation.blade.php
    et toutes les futures pages internes (option A).
    Variables disponibles :
        $page        (Page|null) — la page courante, porte image hero[0]
        $crumb       (string)    — libellé du fil d'Ariane (ex. « Projets »)
        $stats       (array)     — compteurs [['valeur'=>int,'fr'=>..,'en'=>..], ...]
        $locale      (string)
        $t           (helper)
    100 % CMS : fond = hero[0], titre = title, sous-titre = extrait du body.
--}}
@php
    $heroBg = (string) ($page?->getMedia('hero')->first()?->getUrl('banner') ?? '');
    $heroTitle = trim((string) ($page?->getTranslation('title', $locale) ?? ''));
    $heroIntro = \Illuminate\Support\Str::limit(trim(strip_tags((string) ($page?->getTranslation('body', $locale) ?? ''))), 220);
@endphp
<section id="top" class="relative overflow-hidden bg-gradient-to-br from-deep via-deep to-[#123d6e] text-white">
    @if($heroBg !== '')
        <img src="{{ $heroBg }}" alt="" aria-hidden="true"
             class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-25">
    @endif
    <svg class="pointer-events-none absolute inset-0 z-0 h-full w-full opacity-20" viewBox="0 0 1440 800"
         preserveAspectRatio="none" fill="none" stroke="#E3A82B" stroke-width="1.3" aria-hidden="true">
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
    <div class="relative z-10 mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-24">

        <h1 class="mt-6 font-serif text-5xl font-semibold leading-tight md:text-6xl" data-r>
            @if($heroTitle !== ''){{ $heroTitle }}@else{{ $crumb }}@endif
        </h1>
        @if($heroIntro !== '')
            <p class="mt-5 max-w-2xl text-lg text-white/75" data-r>{{ $heroIntro }}</p>
        @endif
        @if(! empty($stats ?? []))
            <dl class="mt-12 grid max-w-4xl grid-cols-2 gap-8 border-t border-white/15 pt-8 md:grid-cols-4">
                @foreach($stats as $stat)
                    <div data-r>
                        <dd class="font-serif text-4xl font-semibold"><span data-n="{{ $stat['valeur'] }}">0</span></dd>
                        <dt class="mt-1 text-sm text-white/65">{{ $t($stat['fr'], $stat['en']) }}</dt>
                    </div>
                @endforeach
            </dl>
        @endif
    </div>
</section>
