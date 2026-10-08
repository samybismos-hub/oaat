{{--
    _topbar.blade.php — Barre supérieure (adresse, téléphone, email, sélecteur de langue)
    ────────────────────────────────────────────────────────────────────────────────
    Variables disponibles :
        $settings   (Setting|null) — passé par le contrôleur ou null
        $locale     (string)       — 'fr' ou 'en'
        $t          (helper)       — traduction statique : $t('fr', 'en')
--}}
<div class="bg-deep text-[13px] text-white/80">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-1.5 lg:px-8">

        {{-- ─── Adresse ─────────────────────────────────────────── --}}
        <p class="hidden lg:block">
            {{ $t('Bukavu · Sud-Kivu · République démocratique du Congo', 'Bukavu · South Kivu · Democratic Republic of the Congo') }}
        </p>

        {{-- ─── Téléphone & Email ───────────────────────────────── --}}
        <p class="flex gap-5">
            @if (isset($settings) && $settings !== null)
                @if (count($settings->phones) > 0)
                    <a class="transition hover:text-ochre" href="tel:{{ $settings->phones[0] }}">
                        {{ $settings->phones[0] }}
                    </a>
                @endif
                @if ($settings->email)
                    <a class="hidden transition hover:text-ochre md:inline" href="mailto:{{ $settings->email }}">
                        {{ $settings->email }}
                    </a>
                @endif
            @endif
        </p>

        {{-- ─── Sélecteur de langue ─────────────────────────────── --}}
        <div role="group"
             aria-label="{{ $t('Langue', 'Language') }}"
             class="ml-auto flex divide-x divide-white/25 border border-white/25">

            <a href="{{ route('accueil', ['locale' => 'fr']) }}"
               class="px-3 py-1 transition hover:text-ochre{{ $locale === 'fr' ? ' font-bold' : '' }}"
               aria-pressed="{{ $locale === 'fr' ? 'true' : 'false' }}">
                Français
            </a>

            <a href="{{ route('accueil', ['locale' => 'en']) }}"
               class="px-3 py-1 transition hover:text-ochre{{ $locale === 'en' ? ' font-bold' : '' }}"
               aria-pressed="{{ $locale === 'en' ? 'true' : 'false' }}">
                English
            </a>
        </div>

    </div>
</div>