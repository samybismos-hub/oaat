<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale ?? 'fr') }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    {{-- ─── SEO : chaque page peut surcharger ces valeurs ─── --}}
    <title>@yield('meta_title', "OAAT — Organisation Africaine pour l'Am\u00E9nagement des Territoires")</title>
    <meta name="description" content="@yield('meta_description', 'ONG interafricaine active \u00E0 l\u2019Est de la RD Congo depuis 1995 : sant\u00E9, eau, \u00E9ducation, s\u00E9curit\u00E9 alimentaire, infrastructures, protection et environnement.')">

    {{-- ─── Google Fonts : Public Sans + Source Serif 4 ─── --}}
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,500;8..60,600;8..60,700&display=swap" rel="stylesheet">

    {{-- ─── Tailwind CSS v4 (CDN) ─────────────────────────── --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- ─── Configuration Tailwind : couleurs, polices, animations ─── --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink:  '#12263F',
                        lake: '#17508F',
                        deep: '#0B2545',
                        ochre: '#E3A82B',
                        moss: '#2F7D52',
                        mist: '#F2F5F9',
                        line: '#D6DEE8',
                    },
                    fontFamily: {
                        sans:  ['Public Sans', 'system-ui', 'sans-serif'],
                        serif: ['Source Serif 4', 'Georgia', 'serif'],
                    },
                    keyframes: {
                        marq: {
                            from: { transform: 'translateX(0)' },
                            to:   { transform: 'translateX(-50%)' },
                        },
                        marqr: {
                            from: { transform: 'translateX(-50%)' },
                            to:   { transform: 'translateX(0)' },
                        },
                        bar: {
                            from: { width: '0%' },
                            to:   { width: '100%' },
                        },
                        dash: {
                            to: { strokeDashoffset: '0' },
                        },
                    },
                    animation: {
                        marq:  'marq 50s linear infinite',
                        marqr: 'marqr 55s linear infinite',
                        bar:   'bar 7s linear forwards',
                        dash:  'dash 5s ease-out forwards',
                    },
                },
            },
        };
    </script>

    {{-- ─── Permet aux pages filles d'injecter du contenu dans <head> ─── --}}
    @stack('head')
</head>
<body class="bg-white font-sans text-ink antialiased pt-[env(safe-area-inset-top,0px)] pb-[env(safe-area-inset-bottom,0px)] [&_*:focus-visible]:outline [&_*:focus-visible]:outline-2 [&_*:focus-visible]:outline-offset-2 [&_*:focus-visible]:outline-ochre">

    {{-- ─── Helper de traduction statique ─────────────────────── --}}
    @php
        $locale ??= 'fr';
        $t = fn($fr, $en) => $locale === 'en' ? $en : $fr;
    @endphp

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  Barre de progression (scroll)                            --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div id="pg" class="fixed left-0 top-0 z-[70] h-[3px] w-0 bg-ochre"></div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  Top bar (téléphone, email, sélecteur de langue)          --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @include('partials._topbar', ['settings' => $settings ?? null, 'locale' => $locale])

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  En-tête (logo + navigation)                               --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @include('partials._header', ['locale' => $locale])

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  Contenu spécifique à chaque page                           --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <main class="transition-opacity duration-300">
        @yield('content')
    </main>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  Pied de page                                              --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @include('partials._footer', ['settings' => $settings ?? null, 'locale' => $locale])

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  Toast de notification (affiché via JavaScript)            --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div id="ts"
         class="fixed bottom-4 left-4 z-50 translate-y-20 opacity-0 rounded-xl bg-ochre px-6 py-4 font-semibold text-deep shadow-2xl transition duration-500 motion-reduce:transition-none">
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  Scripts JavaScript spécifiques à chaque page              --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @stack('scripts')
</body>
</html>