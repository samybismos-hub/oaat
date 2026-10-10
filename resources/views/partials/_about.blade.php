{{--
    _about.blade.php — Section Mission / Organisation (accueil).
    ────────────────────────────────────────────────────────────────
    Variables disponibles :
        $organisation      (Page|null) — page slug='accueil'
        $pageOrganisation  (Page|null) — page slug='organisation' (body translatable)
        $timeline          (array) — jalons historiques extraits du body
        $recognitions      (array) — reconnaissances extraites du body
        $founder           (TeamMember|null) — membre is_founder (portrait, bio, citation)
        $locale            (string)
        $t                 (helper)
--}}
@php
    // Onglet actif par défaut : index 2 (2006-2009) quand la timeline a ≥ 3
    // jalons, sinon le dernier disponible. Évite un état « aucun actif ».
    $tlActive = min(2, count($timeline ?? []) - 1);

    // Données du fondateur (étape 12) : plus aucun texte en dur, tout vient
    // du TeamMember marqué is_founder (géré dans l'admin Filament).
    $founderQuote = $founder?->getTranslation('quote', $locale) ?? '';
    $founderBio   = $founder?->getTranslation('bio', $locale) ?? '';
    $founderRole  = $founder?->getTranslation('role', $locale);
    $founderName  = $founder?->name ?? $t('Ir Roger Manema Cirhahingirwa', 'Ir Roger Manema Cirhahingirwa');
    $founderPhoto = $founder?->getFirstMediaUrl('photo', 'card');
@endphp
<section id="organisation"
         class="mx-auto max-w-7xl scroll-mt-20 px-5 py-24 lg:px-8">

    <div class="grid items-center gap-14 lg:grid-cols-2">

        {{-- Colonne texte --}}
        <div data-r>
            <h2 class="font-serif text-4xl font-semibold leading-tight text-lake md:text-5xl">
                {{ $t("Une ONG interafricaine au service des territoires",
                      "A pan-African NGO serving territories") }}
            </h2>
            <p class="mt-5 max-w-xl text-lg leading-relaxed text-ink/75">
                {{ $t("Apolitique et non confessionnelle, l'OAAT renoue le lien entre développement technologique et besoins des populations les plus défavorisées de la RD Congo, en milieu urbain comme rural.",
                      "Apolitical and non-denominational, OAAT reconnects technological development with the needs of the most disadvantaged populations in DR Congo, in both urban and rural areas.") }}
            </p>

            {{-- Timeline historique --}}
            <div class="mt-8 border border-line bg-mist p-6">
                <div id="tabs" class="flex flex-wrap gap-2" role="tablist">
                    @foreach ($timeline as $index => $h)
                        <button type="button"
                                role="tab"
                                data-tab="{{ $index }}"
                                aria-selected="{{ $index === $tlActive ? 'true' : 'false' }}"
                                class="rounded-full px-5 py-2 text-sm font-semibold transition
                                       {{ $index === $tlActive ? 'bg-lake text-white shadow' : 'bg-white text-ink/70 hover:bg-white hover:text-lake ring-1 ring-line' }}">
                            {{ $h[0] }}
                        </button>
                    @endforeach
                </div>
                <div id="tp" class="mt-5 min-h-[110px] transition duration-300">
                    @foreach ($timeline as $index => $h)
                        <div class="tl-panel {{ $index !== $tlActive ? 'hidden' : '' }}" data-panel="{{ $index }}">
                            <h3 class="font-serif text-xl font-semibold text-lake">{{ $h[1] }}</h3>
                            <p class="mt-2 leading-relaxed text-ink/75">{{ $h[2] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('organisation', $locale) }}"
               class="group/l mt-8 inline-flex items-center gap-2 font-semibold text-lake">
                {{ $t("Découvrir toute l'organisation", 'Discover the full organisation') }}
                <span class="transition group-hover/l:translate-x-1">→</span>
            </a>
        </div>

        {{-- Colonne portrait fondateur (données réelles du TeamMember) --}}
        <figure class="relative" data-r>
            <div class="overflow-hidden shadow-xl">
                @if ($founderPhoto)
                    <img src="{{ $founderPhoto }}"
                         alt="{{ $founderName }} — {{ $t('fondateur de l\'OAAT', 'founder of OAAT') }}"
                         data-w
                         class="aspect-[4/5] w-full object-cover">
                @else
                    <img data-w
                         data-ph="Portrait du fondateur|205"
                         alt="{{ $t('Portrait de Roger Manema Cirhahingirwa, fondateur de l\'OAAT',
                                     'Portrait of Roger Manema Cirhahingirwa, founder of OAAT') }}"
                         class="aspect-[4/5] w-full object-cover">
                @endif
            </div>
            <figcaption class="absolute -bottom-8 left-4 right-4 border-l-4 border-ochre bg-deep p-6 text-white shadow-2xl md:left-auto md:right-[-1rem] md:max-w-sm lg:right-[-1.5rem]">
                <p class="font-serif text-lg leading-snug">
                    « {{ $founderQuote ?: $t("Pour que le développement soit local, il faut que la force de développement soit aussi locale.",
                            "For development to be local, the driving force of development must also be local.") }} »
                </p>
                @if ($founderBio)
                    <p class="mt-3 text-sm leading-snug text-white/75 line-clamp-4">
                        {{ $founderBio }}
                    </p>
                @endif
                <p class="mt-3 text-sm text-white/80">
                    {{ $founderName }}@if ($founderRole)<span class="text-white/60"> — {{ $founderRole }}</span>@endif
                </p>
            </figcaption>
        </figure>
    </div>

    {{-- ─── Reconnaissances officielles ─────────────────────────── --}}
    <div class="mt-28 grid gap-4 md:grid-cols-4" id="reco">
        @forelse($recognitions ?? [] as $r)
            <div data-r
                 class="flex gap-4 border border-line p-5 transition duration-300 hover:-translate-y-1 hover:border-ochre hover:shadow-lg">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-moss/10 text-moss">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12l5 5 9-10"/>
                    </svg>
                </span>
                <div>
                    <b class="text-sm text-lake">{{ $r[0] }}</b>
                    <p class="mt-1 text-sm text-ink/65">{{ $r[1] }}</p>
                </div>
            </div>
        @empty
            <p class="col-span-4 text-center text-sm text-ink/40 italic">
                {{ $t('Aucune reconnaissance pour le moment.', 'No recognitions yet.') }}
            </p>
        @endforelse
    </div>
</section>
