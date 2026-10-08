{{--
    _partenaires.blade.php — Section Partenaires (marquee infini).
    ────────────────────────────────────────────────────────────────
    Variables disponibles :
        $partenaires  (Collection) — tous les partenaires triés par nom
        $locale       (string)
        $t            (helper)
--}}
@php
    $monogram = function (string $name): string {
        $hash = crc32($name);
        $hue  = abs($hash) % 360;
        if (str_contains($name, ' ')) {
            $parts = explode(' ', $name);
            $initials = '';
            foreach ($parts as $p) {
                if (!empty($p)) $initials .= mb_strtoupper(mb_substr($p, 0, 1));
            }
        } else {
            $initials = mb_substr($name, 0, 4);
        }
        return 'data:image/svg+xml;utf8,' . rawurlencode(
            "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'>"
            . "<rect width='64' height='64' fill='hsl({$hue},40%,32%)'/>"
            . "<text x='32' y='38' fill='#fff' font-family='sans-serif' font-size='17' font-weight='700' text-anchor='middle'>{$initials}</text>"
            . '</svg>'
        );
    };
    $img = fn($p) => $p->getFirstMediaUrl('logo', 'thumb') ?: $monogram($p->name);
    $fbk = fn($p) => filled($p->getFirstMediaUrl('logo', 'thumb')) ? $monogram($p->name) : $monogram($p->name);
    $mid = (int) ceil(count($partenaires ?? []) / 2);
    $row1 = $partenaires->slice(0, $mid)->values();
    $row2 = $partenaires->slice($mid)->values();
@endphp

<section id="partenaires"
         class="scroll-mt-20 border-y border-line bg-white py-20">

    <div class="mx-auto max-w-7xl px-5 lg:px-8" data-r>
        <h2 class="font-serif text-4xl font-semibold text-lake md:text-5xl">
            {{ $t('Ils nous font confiance', 'They trust us') }}
        </h2>
        <p class="mt-3 max-w-2xl text-ink/70">
            {{ $t("Agences des Nations Unies, bailleurs et ONG internationales avec lesquels l'OAAT a mené des projets depuis 1994.",
                  "UN agencies, donors and international NGOs OAAT has delivered projects with since 1994.") }}
        </p>
    </div>

    <div class="group mt-12 space-y-4 overflow-hidden [mask-image:linear-gradient(90deg,transparent,#000_10%,#000_90%,transparent)]">

        {{-- Rangée 1 (vers la gauche) --}}
        <div id="m1" class="flex w-max animate-marq motion-reduce:animate-none group-hover:[animation-play-state:paused]">
            @foreach ($row1 as $p)
                <span class="mx-2 flex h-16 min-w-[190px] items-center justify-center gap-3 border border-line bg-white px-5 font-serif text-lg font-semibold text-ink/60 grayscale transition duration-300 hover:-translate-y-0.5 hover:border-ochre hover:text-lake hover:shadow-lg hover:grayscale-0">
                    <img src="{{ $img($p) }}" data-f="{{ $fbk($p) }}"
                         alt="{{ $p->name }}" class="h-9 w-9 object-contain"
                         loading="lazy" onerror="this.onerror=null;this.src=this.dataset.f">{{ $p->name }}
                </span>
            @endforeach
            @foreach ($row1 as $p)
                <span class="mx-2 flex h-16 min-w-[190px] items-center justify-center gap-3 border border-line bg-white px-5 font-serif text-lg font-semibold text-ink/60 grayscale transition duration-300 hover:-translate-y-0.5 hover:border-ochre hover:text-lake hover:shadow-lg hover:grayscale-0">
                    <img src="{{ $img($p) }}" data-f="{{ $fbk($p) }}"
                         alt="{{ $p->name }}" class="h-9 w-9 object-contain"
                         loading="lazy" onerror="this.onerror=null;this.src=this.dataset.f">{{ $p->name }}
                </span>
            @endforeach
        </div>
{{-- Rangée 2 (vers la droite) --}}
        <div id="m2" class="flex w-max animate-marqr motion-reduce:animate-none group-hover:[animation-play-state:paused]">
            @foreach ($row2 as $p)
                <span class="mx-2 flex h-16 min-w-[190px] items-center justify-center gap-3 border border-line bg-white px-5 font-serif text-lg font-semibold text-ink/60 grayscale transition duration-300 hover:-translate-y-0.5 hover:border-ochre hover:text-lake hover:shadow-lg hover:grayscale-0">
                    <img src="{{ $img($p) }}" data-f="{{ $fbk($p) }}"
                         alt="{{ $p->name }}" class="h-9 w-9 object-contain"
                         loading="lazy" onerror="this.onerror=null;this.src=this.dataset.f">{{ $p->name }}
                </span>
            @endforeach
            @foreach ($row2 as $p)
                <span class="mx-2 flex h-16 min-w-[190px] items-center justify-center gap-3 border border-line bg-white px-5 font-serif text-lg font-semibold text-ink/60 grayscale transition duration-300 hover:-translate-y-0.5 hover:border-ochre hover:text-lake hover:shadow-lg hover:grayscale-0">
                    <img src="{{ $img($p) }}" data-f="{{ $fbk($p) }}"
                         alt="{{ $p->name }}" class="h-9 w-9 object-contain"
                         loading="lazy" onerror="this.onerror=null;this.src=this.dataset.f">{{ $p->name }}
                </span>
            @endforeach
        </div>

    </div>
</section>