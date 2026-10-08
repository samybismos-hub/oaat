{{--
    _footer.blade.php — Pied de page complet.
    ────────────────────────────────────────────────────────────────
    Variables disponibles :
        $settings  (Setting|null) — email, phones, addresses, socials, logo
        $locale    (string)
        $t         (helper)
--}}
@php
    $navLinks = [
        ['#accueil',      $t('Accueil',        'Home')],
        ['#organisation', $t("L'Organisation", 'Organisation')],
        ['#domaines',     $t("Domaines d'intervention", 'Areas of intervention')],
        ['#projets',      $t('Projets',        'Projects')],
        ['#actualites',   $t('Actualités',     'News')],
        ['#contact',      $t('Contact',        'Contact')],
    ];

    $socialIcons = [
        'facebook' => ['f',  'Facebook'],
        'linkedin' => ['in', 'LinkedIn'],
        'twitter'  => ['X',  'X'],
        'youtube'  => ['▶',  'YouTube'],
    ];

    $logoUrl  = $settings?->getFirstMediaUrl('logo') ?? '';
    $toastMsg = $t('Lien du réseau social à renseigner.', 'Social link to be added.');
    $socials  = $settings?->socials ?? [];
@endphp

<footer class="bg-deep text-sm text-white/70">

    <div class="mx-auto grid max-w-7xl gap-12 px-5 py-16 md:grid-cols-2 lg:grid-cols-12 lg:px-8">

        {{-- ═══ Colonne 1 : Logo + description + réseaux sociaux ── --}}
        <div class="lg:col-span-5">

            <a href="#accueil" class="flex items-center gap-3 text-white">
                @if (filled($logoUrl))
                    <img src="{{ $logoUrl }}" alt="OAAT"
                         class="h-12 w-12 rounded-lg object-contain">
                @else
                    <svg viewBox="0 0 40 40" class="h-12 w-12" aria-hidden="true">
                        <rect width="40" height="40" rx="9" fill="#17508F"/>
                        <path d="M5 27c6-9 11 3 17-6s8-5 13-9M5 21c5-8 10 2 15-5s9-4 15-8M5 33c7-8 12 3 18-5s7-4 12-7"
                              fill="none" stroke="#E3A82B" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                @endif
                <span class="leading-tight">
                    <b class="block font-serif text-2xl">OAAT</b>
                    <span class="block text-xs text-white/60">
                        {{ $settings?->organisation_name
                            ?? $t("Organisation Africaine pour l'Aménagement des Territoires",
                                  'African Organisation for Land Planning') }}
                    </span>
                </span>
            </a>

            <p class="mt-5 max-w-md leading-relaxed">
                {{ $t("Aménagement des territoires et développement durable des communautés à l'Est de la RD Congo, depuis 1995.",
                      "Territorial planning and sustainable community development in eastern DR Congo, since 1995.") }}
            </p>

            {{-- Réseaux sociaux --}}
            <p class="mb-3 mt-7 font-semibold text-white">
                {{ $t('Réseaux sociaux', 'Follow us') }}
            </p>
            <div id="so" class="flex gap-2">
                @foreach ($socialIcons as $key => $icon)
                    @if (blank($socials[$key] ?? null))
                        <a href="#"
                           aria-label="{{ $icon[1] }}"
                           onclick="event.preventDefault();window.dispatchEvent(new CustomEvent('toast',{detail:'{{ $toastMsg }}'}))"
                           class="sc grid h-10 w-10 place-items-center border border-white/25 font-bold text-white transition hover:-translate-y-0.5 hover:border-ochre hover:bg-ochre hover:text-deep">
                            {{ $icon[0] }}
                        </a>
                    @else
                        <a href="{{ $socials[$key] }}" aria-label="{{ $icon[1] }}"
                           target="_blank" rel="noopener noreferrer"
                           class="sc grid h-10 w-10 place-items-center border border-white/25 font-bold text-white transition hover:-translate-y-0.5 hover:border-ochre hover:bg-ochre hover:text-deep">
                            {{ $icon[0] }}
                        </a>
                    @endif
                @endforeach
            </div>       {{-- fin #so --}}
        </div>           {{-- fin colonne 1 --}}

        {{-- ═══ Colonne 2 : Liens rapides ─────────────────────── --}}
        <div class="lg:col-span-3">
            <b class="text-white">{{ $t('Liens rapides', 'Quick links') }}</b>
            <ul id="fn" class="mt-4 space-y-2.5">
                @foreach ($navLinks as $link)
                    <li>
                        <a href="{{ $link[0] }}" class="transition hover:text-ochre">
                            {{ $link[1] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- ═══ Colonne 3 : Coordonnées ───────────────────────── --}}
        <div id="coordonnees" class="scroll-mt-24 lg:col-span-4">
            <b class="text-white">{{ $t('Coordonnées', 'Contact details') }}</b>
            <ul class="mt-4 space-y-4 leading-relaxed">

                {{-- E-mail --}}
                <li>
                    <span class="block text-white">{{ $t('E-mail', 'Email') }}</span>
                    <a class="transition hover:text-ochre"
                       href="mailto:{{ $settings?->email ?? 'oaatrdc2000@gmail.com' }}">
                        {{ $settings?->email ?? 'oaatrdc2000@gmail.com' }}
                    </a>
                </li>

                {{-- Téléphone --}}
                <li>
                    <span class="block text-white">{{ $t('Téléphone', 'Phone') }}</span>
                    @php $phones = $settings?->phones ?? ['+243 993 537 325', '+243 896 127 195']; @endphp
                    @foreach ($phones as $phone)
                        <a class="transition hover:text-ochre"
                           href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">
                            {{ $phone }}
                        </a>@if (!$loop->last) · @endif
                    @endforeach
                </li>

                {{-- Adresses (translatables) --}}
                @php
                    $addresses = is_array($settings?->getTranslation('addresses', $locale))
                        ? $settings->getTranslation('addresses', $locale)
                        : [];
                @endphp
                @if (count($addresses) > 0)
                    @foreach ($addresses as $addr)
                        @php
                            $parts = explode(' : ', $addr, 2);
                            $label = $parts[0];
                            $value = $parts[1] ?? $addr;
                        @endphp
                        <li>
                            <span class="block text-white">{{ $label }}</span>
                            {{ $value }}
                        </li>
                    @endforeach
                @else
                    {{-- Fallback si aucune adresse en base --}}
                    <li>
                        <span class="block text-white">{{ $t('Bureau de représentation', 'Representation office') }}</span>
                        {{ $t("N° 007, Avenue Fizi II/Nyawera, Quartier Ndendere, Commune d'Ibanda, Bukavu, Sud-Kivu",
                              "No. 007, Fizi II/Nyawera Avenue, Ndendere Quarter, Ibanda Commune, Bukavu, South Kivu") }}
                    </li>
                    <li>
                        <span class="block text-white">{{ $t('Siège social', 'Head office') }}</span>
                        {{ $t('Nyangezi, Groupement de Karhongo, Territoire de Walungu',
                              'Nyangezi, Karhongo Grouping, Walungu Territory') }}
                    </li>
                @endif

            </ul>
        </div>

    </div>

    {{-- ═══ Barre copyright ───────────────────────────────────── --}}
    <div class="border-t border-white/10 py-5 text-center text-xs">
        &copy; {{ date('Y') }} OAAT. {{ $t('Tous droits réservés.', 'All rights reserved.') }}
    </div>

</footer>