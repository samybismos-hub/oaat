{{-- pages/domaines-index.blade.php — « Domaines d’intervention ».
     Contenu rendu côté serveur (Blade), sans injection HTML ni traduction
     via JavaScript. Page unilingue française (maquette migrée). --}}
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Domaines d’intervention — OAAT</title>
    <meta name="description" content="Les neuf domaines d’intervention de l’OAAT : interventions, formations reçues et projets associés.">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,500;8..60,600;8..60,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#12263F', lake: '#17508F', deep: '#0B2545', ochre: '#E3A82B',
                        ochreDark: '#7A5200', moss: '#2F7D52', mist: '#F2F5F9', line: '#D6DEE8',
                    },
                    fontFamily: {
                        sans: ['Public Sans', 'system-ui', 'sans-serif'],
                        serif: ['Source Serif 4', 'Georgia', 'serif'],
                    },
                    keyframes: {
                        dash: { to: { strokeDashoffset: '0' } },
                    },
                    animation: {
                        dash: 'dash 5s ease-out forwards',
                    },
                },
            },
        };
    </script>
    <style>
        .animate-dash { animation: dash 5s ease-out forwards; }
        .motion-reduce .animate-dash { animation: none; }
    </style>
</head>
<body class="bg-white font-sans text-ink antialiased pt-[env(safe-area-inset-top,0px)] pb-[env(safe-area-inset-bottom,0px)] [&_*:focus-visible]:outline [&_*:focus-visible]:outline-2 [&_*:focus-visible]:outline-offset-2 [&_*:focus-visible]:outline-ochre">
@php
    // ────────────────────────────────────────────────────────────────────────
    //  Données de la page (contenu de la maquette, FR) — rendues côté serveur.
    //  Aucun contenu n'est injecté par JavaScript.
    // ────────────────────────────────────────────────────────────────────────
    $domaines = [
        'sante' => [
            'libelle'       => 'Santé',
            'teinte'        => 350,
            'icone'         => 'M12 8v8M8 12h8M12 3a9 9 0 100 18 9 9 0 000-18z',
            'descriptif'    => 'Réhabilitation de centres de santé, fourniture de matériel médical, formation du personnel soignant.',
            'interventions' => [
                'Réhabilitation d’infrastructures médicales et sanitaires',
                'Fourniture de matériels et d’intrants médicaux',
                'Formation des personnels soignants',
            ],
            'formations'    => [],
        ],
        'alim' => [
            'libelle'       => 'Sécurité alimentaire',
            'teinte'        => 95,
            'icone'         => 'M12 21V9M12 13c-4 0-6-2-6-6 4 0 6 2 6 6zM12 11c0-4 2-6 6-6 0 4-2 6-6 6z',
            'descriptif'    => 'Kits agricoles, drainage de marais, appui aux ménages déplacés et activités génératrices de revenus.',
            'interventions' => [
                'Renforcement technique et accompagnement des ménages agricoles',
                'Assistance alimentaire aux ménages déplacés, retournés et aux familles d’accueil',
                'Distribution de kits agricoles d’urgence (semences et outils aratoires)',
                'Vulgarisation des méthodes culturales et encadrement des agriculteurs',
                'Drainage de marais',
                'Gestion et accès à la terre justes et équitables',
                'Cohabitation pacifique dans nos rayons d’action',
                'Appui aux activités génératrices de revenus (AGR)',
            ],
            'formations'    => [
                'Collecte des données de prix des denrées alimentaires, avec le PAM et le ministère de l’Agriculture',
            ],
        ],
        'logi' => [
            'libelle'       => 'Logistique',
            'teinte'        => 30,
            'icone'         => 'M3 18h18M5 18v-6a7 7 0 0114 0v6M12 5v13',
            'descriptif'    => 'Routes de desserte agricole, ponts, dalots et infrastructures publiques et sociales.',
            'interventions' => [
                'Réhabilitation des infrastructures de base',
                'Aménagement des routes de desserte agricole : ponts, passages sous route, dalots',
                'Création et curage de canalisations et de saignées',
                'Construction et réhabilitation d’infrastructures publiques et sociales',
            ],
            'formations'    => [],
        ],
        'educ' => [
            'libelle'       => 'Éducation',
            'teinte'        => 215,
            'icone'         => 'M3 5h6a3 3 0 013 3v12a2 2 0 00-2-2H3zM21 5h-6a3 3 0 00-3 3v12a2 2 0 012-2h7z',
            'descriptif'    => 'Construction d’écoles, équipements, kits scolaires et formation des enseignants.',
            'interventions' => [
                'Construction et réhabilitation d’infrastructures scolaires',
                'Fourniture d’équipements et de kits scolaires',
                'Formation des enseignants',
            ],
            'formations'    => [
                'Normes INEE : éducation en situation d’urgence (atelier de formation)',
            ],
        ],
    
        'wash' => [
            'libelle'       => 'Eau, hygiène et assainissement',
            'teinte'        => 190,
            'icone'         => 'M12 3s6 6.5 6 11a6 6 0 01-12 0c0-4.5 6-11 6-11z',
            'descriptif'    => 'Adduction d’eau, captage de sources, forages, latrines et sensibilisation à l’hygiène.',
            'interventions' => [
                'Étude et conception de projets hydrauliques',
                'Adduction d’eau et captage de sources',
                'Forage de puits',
                'Construction de latrines publiques et familiales',
                'Sensibilisation à l’eau, à l’hygiène et à l’assainissement',
            ],
            'formations'    => [
                'Atelier de l’UNICEF sur les 5 engagements du cluster WASH pour la sécurité et la dignité des filles, garçons, femmes et hommes',
            ],
        ],
        'genre' => [
            'libelle'       => 'Genre',
            'teinte'        => 285,
            'icone'         => 'M12 14a5 5 0 100-10 5 5 0 000 10zM12 14v7M9 18h6',
            'descriptif'    => 'Équité, égalité de sexe et de chances pour les femmes, hommes, filles et garçons.',
            'interventions' => [
                'Sensibilisation à l’intégration du genre et au respect de l’équité',
                'Égalité de sexe et de chances pour hommes, femmes, filles et garçons dans tous les secteurs',
            ],
            'formations'    => [
                'Formation en genre par le HCR, à Bukavu',
                'Outils d’intégration du genre : analyse de genre, cadre ADAPT, AGIR et Gender Marker',
                'Premier atelier d’appui aux points focaux genre au Sud-Kivu, avec ICCO et OCHA',
            ],
        ],
        'prot' => [
            'libelle'       => 'Protection',
            'teinte'        => 330,
            'icone'         => 'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z',
            'descriptif'    => 'Droits de la femme et de l’enfant, lutte contre les violences basées sur le genre, réinsertion des personnes vulnérables.',
            'interventions' => [
                'Promotion des droits de la femme et de l’enfant',
                'Bonne gouvernance',
                'Lutte contre les viols et violences sexuelles et les violences basées sur le genre',
                'Lutte contre les IST et le VIH/SIDA',
                'Réinsertion socio-économique des personnes vulnérables, déplacées et retournées',
                'Réinsertion des personnes vivant avec un handicap',
            ],
            'formations'    => [
                'Management sensible aux conflits',
                'Aide humanitaire d’urgence et sensibilité aux conflits',
            ],
        ],
        'env' => [
            'libelle'       => 'Environnement',
            'teinte'        => 150,
            'icone'         => 'M5 19c0-9 5-14 15-14 0 10-5 15-14 15M5 19c2-5 5-8 9-10',
            'descriptif'    => 'Reboisement, énergies renouvelables, biodigesteurs, briquettes issues de déchets ménagers.',
            'interventions' => [
                'Sensibilisation au droit de l’environnement et à la résolution 1325 de l’ONU',
                'Protection de l’environnement, des forêts et de la biodiversité',
                'Lutte contre le changement climatique',
                'Promotion des énergies renouvelables et alternatives',
                'Reboisement',
                'Briquettes à partir de déchets ménagers et fabrication de pavés',
                'Biodigesteurs et production d’engrais biologique',
            ],
            'formations'    => [
                'Forum sur la séquestration du carbone et le changement climatique, à Kigali (Rwanda), 2016 — IPBS, NAMA Facility, UNFCCC',
            ],
        ],
        'form' => [
            'libelle'       => 'Formation professionnelle',
            'teinte'        => 45,
            'icone'         => 'M14 6l4 4M4 20l8-8M13 5l6 6-3 3-6-6z',
            'descriptif'    => 'Couture, maçonnerie, menuiserie, briqueterie, savonnerie et art culinaire.',
            'interventions' => [
                'Coupe-couture',
                'Maçonnerie',
                'Menuiserie',
                'Briqueterie',
                'Savonnerie',
                'Art culinaire',
            ],
            'formations'    => [],
        ],
    ];

    // Étiquettes courtes de la barre de navigation interne (#rl).
    $rail = [
        'sante' => 'Santé',
        'alim'  => 'Alimentation',
        'logi'  => 'Logistique',
        'educ'  => 'Éducation',
        'wash'  => 'Eau',
        'genre' => 'Genre',
        'prot'  => 'Protection',
        'env'   => 'Environnement',
        'form'  => 'Métiers',
    ];

    // Navigation principale (desktop, mobile et pied de page).
    $liensNav = [
        [route('accueil', ['locale' => $locale]),        'Accueil',               false],
        [route('organisation', ['locale' => $locale]),  'L’Organisation',        false],
        [route('domaines.index', ['locale' => $locale]), 'Domaines d’intervention', true],
        [route('projets.index', ['locale' => $locale]),  'Projets',               false],
        [route('actualites.index', ['locale' => $locale]), 'Actualités',          false],
        [route('documents.index', ['locale' => $locale]), 'Documents',            false],
        [route('galerie.index', ['locale' => $locale]),  'Galerie',               false],
        [route('partenaires', ['locale' => $locale]),    'Partenaires',           false],
        [route('contact', ['locale' => $locale]),       'Contact',               false],
    ];
@endphp
@php
    // Portefeuille de projets (année 0 = projet « à financer »).
    $projets = [
        ['annee' => 1994, 'domaine' => 'prot',  'titre' => 'Sensibilisation des femmes burundaises à la cohabitation', 'bailleur' => 'CECI'],
        ['annee' => 1995, 'domaine' => 'form',  'titre' => 'Atelier de couture pour orphelins et veuves, Burundi', 'bailleur' => 'PNUD'],
        ['annee' => 1995, 'domaine' => 'educ',  'titre' => 'École primaire pour enfants orphelins, Bujumbura', 'bailleur' => ''],
        ['annee' => 1996, 'domaine' => 'wash',  'titre' => 'Assainissement d’Uvira et entretien des avenues', 'bailleur' => 'PAM'],
        ['annee' => 1997, 'domaine' => 'form',  'titre' => 'Tuiles, briques et foyers améliorés, plaine de la Ruzizi', 'bailleur' => 'PAM, USAID'],
        ['annee' => 1998, 'domaine' => 'logi',  'titre' => 'Réhabilitation de la route Kamanyola–Uvira', 'bailleur' => 'PAM, USAID'],
        ['annee' => 1999, 'domaine' => 'sante', 'titre' => 'Centres de santé de Mazigiro et Ibambiro', 'bailleur' => 'BCZS'],
        ['annee' => 2000, 'domaine' => 'sante', 'titre' => 'Centres de santé, latrines et douches, Walungu', 'bailleur' => 'BDOM / Louvain Développement'],
        ['annee' => 2000, 'domaine' => 'logi',  'titre' => 'Escalier de 5 400 mètres à Bukavu', 'bailleur' => 'MONUC'],
        ['annee' => 2005, 'domaine' => 'alim',  'titre' => 'Petit bétail pour les femmes retournées, Lemera', 'bailleur' => 'BDOM / Louvain Développement'],
        ['annee' => 2006, 'domaine' => 'sante', 'titre' => 'Réhabilitation de la maternité de Kasenga', 'bailleur' => 'SPES / UNHCR'],
        ['annee' => 2006, 'domaine' => 'educ',  'titre' => 'Réhabilitation de 50 classes à Fizi', 'bailleur' => 'AVSI / ECHO'],
        ['annee' => 2006, 'domaine' => 'educ',  'titre' => 'Quatre écoles, 18 latrines et 19 citernes, plaine de la Ruzizi', 'bailleur' => 'AVSI / UNICEF'],
        ['annee' => 2007, 'domaine' => 'logi',  'titre' => 'Deux ponts Kasandjala I et II, territoire de Fizi', 'bailleur' => 'PNUD / COMREC'],
        ['annee' => 2008, 'domaine' => 'wash',  'titre' => 'Eau potable et hygiène à Sebele, huit puits à Fizi', 'bailleur' => 'Pooled Fund'],
        ['annee' => 2009, 'domaine' => 'logi',  'titre' => 'Réhabilitation de la route Sebele–Kazimia', 'bailleur' => 'PNUD / COMREC'],
        ['annee' => 2010, 'domaine' => 'form',  'titre' => 'Formation en informatique de jeunes à Beni', 'bailleur' => 'BETING'],
        ['annee' => 2010, 'domaine' => 'wash',  'titre' => '346 latrines familiales', 'bailleur' => 'Tearfund'],
        ['annee' => 2013, 'domaine' => 'logi',  'titre' => 'Route de 38 km Numbi–Ziralo, territoire de Kalehe', 'bailleur' => 'AAP / P.F.'],
        ['annee' => 2014, 'domaine' => 'form',  'titre' => 'Autonomisation économique de jeunes à Goma', 'bailleur' => 'BETING'],
        ['annee' => 2014, 'domaine' => 'educ',  'titre' => 'Douze classes et latrines à l’EP Bakhita', 'bailleur' => 'COPA'],
        ['annee' => 2015, 'domaine' => 'form',  'titre' => 'Formation à l’entrepreneuriat de jeunes, Masisi', 'bailleur' => 'BETING'],
        ['annee' => 2017, 'domaine' => 'wash',  'titre' => 'Citernes d’eau à l’EP Camp Saio', 'bailleur' => 'EPSP / UNICEF'],
        ['annee' => 2019, 'domaine' => 'form',  'titre' => 'Formation de jeunes contre la pauvreté, Nyiragongo', 'bailleur' => 'BETING'],
        ['annee' => 2021, 'domaine' => 'educ',  'titre' => 'Construction et réhabilitation de l’EP EDAP/ISP', 'bailleur' => 'EDAP'],
        ['annee' => 2022, 'domaine' => 'alim',  'titre' => 'Entrepôt de riz, marais de Nyamubanda et Hogola', 'bailleur' => 'PICAGEL'],
        ['annee' => 2023, 'domaine' => 'alim',  'titre' => 'Production de tilapia en étangs piscicoles, Karhongo', 'bailleur' => 'FAO'],
        ['annee' => 2024, 'domaine' => 'educ',  'titre' => 'École primaire et secondaire de Karhale, Bukavu', 'bailleur' => 'BETING'],
        ['annee' => 2024, 'domaine' => 'prot',  'titre' => 'Foyer social pour jeunes filles mères, Saké', 'bailleur' => 'BETING'],
        ['annee' => 0,    'domaine' => 'logi',  'titre' => 'Aménagement des huit territoires', 'bailleur' => 'IPBS, NAMA Facility'],
        ['annee' => 0,    'domaine' => 'alim',  'titre' => 'Drainage du marais de Nyamubanda, Walungu', 'bailleur' => ''],
        ['annee' => 0,    'domaine' => 'alim',  'titre' => 'Relance de la production agricole à Fizi', 'bailleur' => ''],
        ['annee' => 0,    'domaine' => 'alim',  'titre' => 'Relance de la production de riz à Fizi', 'bailleur' => ''],
        ['annee' => 0,    'domaine' => 'genre', 'titre' => 'Intégration du genre dans le Sud-Kivu', 'bailleur' => ''],
        ['annee' => 0,    'domaine' => 'wash',  'titre' => 'Prévention d’Ebola, COVID-19 et mpox en milieu scolaire', 'bailleur' => ''],
    ];
@endphp

{{-- ═══ Barre de progression (défilement) ═══════════════════════════════ --}}
<div id="pg" class="fixed left-0 top-0 z-[70] h-[3px] w-0 bg-ochre"></div>

{{-- ═══ Barre supérieure (adresse, téléphone, e-mail) ═══════════════════ --}}
<div class="bg-deep text-[13px] text-white/80">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-1.5 lg:px-8">
        <p class="hidden lg:block">Bukavu · Sud-Kivu · République démocratique du Congo</p>
        <p class="flex gap-5">
            <a class="transition hover:text-ochre" href="tel:+243993537325">+243 993 537 325</a>
            <a class="hidden transition hover:text-ochre md:inline" href="mailto:oaatrdc2000@gmail.com">oaatrdc2000@gmail.com</a>
        </p>
    </div>
</div>

{{-- ═══ En-tête (logo, navigation, CTA, menu mobile) ════════════════════ --}}
<header id="hd" class="sticky top-0 z-50 border-b border-line bg-white/90 backdrop-blur transition-shadow duration-300">
    <div id="hi" class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-3 transition-[padding] duration-300 lg:px-8">

        {{-- ▸ Logo OAAT --}}
        <a href="{{ route('accueil', ['locale' => $locale]) }}" class="flex items-center gap-3" aria-label="OAAT, accueil">
            <svg viewBox="0 0 40 40" class="h-11 w-11" aria-hidden="true">
                <rect width="40" height="40" rx="9" fill="#17508F"/>
                <path d="M5 27c6-9 11 3 17-6s8-5 13-9M5 21c5-8 10 2 15-5s9-4 15-8M5 33c7-8 12 3 18-5s7-4 12-7" fill="none" stroke="#E3A82B" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span class="leading-tight">
                <b class="block font-serif text-xl text-lake">OAAT</b>
                <span class="block max-w-[200px] text-[11px] text-ink/60">Organisation Africaine pour l’Aménagement des Territoires</span>
            </span>
        </a>

        {{-- ▸ Navigation desktop --}}
        <nav id="nav" class="hidden items-center xl:flex" aria-label="Navigation principale">
            @foreach ($liensNav as $lien)
                <a href="{{ $lien[0] }}"
                   class="nl relative px-2.5 py-2 text-[13px] font-medium transition hover:text-lake
                          after:absolute after:inset-x-2.5 after:-bottom-0.5 after:h-0.5 after:origin-left after:scale-x-0 after:bg-ochre after:transition after:duration-300 hover:after:scale-x-100
                          {{ $lien[2] ? 'text-lake after:scale-x-100' : 'text-ink/75' }}">
                    {{ $lien[1] }}
                    @if ($lien[2])<i class="ml-1 text-[10px] not-italic opacity-60">▾</i>@endif
                </a>
            @endforeach
        </nav>

        {{-- ▸ CTA « Devenir partenaire » --}}
        <a href="{{ route('contact', ['locale' => $locale]) }}"
           class="hidden rounded-full bg-lake px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-deep hover:shadow-lg 2xl:block relative overflow-hidden
                  before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%]">
            Devenir partenaire
        </a>

        {{-- ▸ Bouton menu mobile --}}
        <button id="bt" aria-label="Ouvrir le menu" aria-expanded="false"
                class="grid h-11 w-11 place-items-center rounded-full border border-line transition hover:bg-mist xl:hidden">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 7h16M4 12h16M4 17h16"/>
            </svg>
        </button>
    </div>

    {{-- ▸ Méga menu « Domaines » (sous le lien actif) --}}
    <div id="mg" class="invisible absolute inset-x-0 top-full -translate-y-2 border-b border-line bg-white opacity-0 shadow-2xl transition duration-300">
        <div id="mgi" class="mx-auto grid max-w-7xl gap-1 px-5 py-6 sm:grid-cols-2 lg:grid-cols-3 lg:px-8">
            @foreach ($domaines as $cle => $domaine)
                <a href="#{{ $cle }}" class="group flex items-center gap-3 p-3 transition hover:bg-mist">
                    <span class="grid h-10 w-10 shrink-0 place-items-center bg-lake/10 text-lake transition group-hover:bg-lake group-hover:text-white">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="{{ $domaine['icone'] }}"/>
                        </svg>
                    </span>
                    <span class="text-sm font-medium transition group-hover:text-lake">{{ $domaine['libelle'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ▸ Menu mobile (coulissant) --}}
    <div id="mn" class="grid grid-rows-[0fr] bg-white transition-[grid-template-rows] duration-300 xl:hidden">
        <div class="overflow-hidden">
            <nav id="mnav" class="flex flex-col border-t border-line p-4" aria-label="Navigation mobile">
                @foreach ($liensNav as $lien)
                    <a href="{{ $lien[0] }}" class="px-3 py-3 font-medium transition hover:bg-mist hover:text-lake">
                        {{ $lien[1] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>
</header>

<main class="transition-opacity duration-300">

    {{-- ═══ Héro ═════════════════════════════════════════════════════════ --}}
    <section id="top" class="relative overflow-hidden bg-gradient-to-br from-deep via-deep to-[#123d6e] text-white">
        <svg class="pointer-events-none absolute inset-0 z-0 h-full w-full opacity-20" viewBox="0 0 1440 800"
             preserveAspectRatio="none" fill="none" stroke="#E3A82B" stroke-width="1.3" aria-hidden="true">
            <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1" class="animate-dash motion-reduce:animate-none" d="M-20 600C220 520 380 690 640 600S1060 470 1460 560"/>
            <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1" class="animate-dash motion-reduce:animate-none" style="animation-delay:.5s" d="M-20 650C240 580 400 730 680 650S1080 530 1460 610"/>
            <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1" class="animate-dash motion-reduce:animate-none" style="animation-delay:1s" d="M-20 700C260 640 420 770 700 700S1100 590 1460 660"/>
            <path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1" class="animate-dash motion-reduce:animate-none" style="animation-delay:1.5s" d="M-20 550C200 470 360 640 620 550S1040 420 1460 510"/>
        </svg>

        <div class="relative z-10 mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-24">
            <nav aria-label="Fil d’Ariane" class="text-sm text-white/60" data-r>
                <a href="{{ route('accueil', ['locale' => $locale]) }}" class="transition hover:text-ochre">Accueil</a>
                <span class="mx-2">›</span>
                <span class="text-white">Domaines d’intervention</span>
            </nav>

            <h1 class="mt-6 font-serif text-5xl font-semibold leading-tight md:text-6xl" data-r>Domaines d’intervention</h1>

            <p class="mt-5 max-w-2xl text-lg text-white/75" data-r>
                Neuf domaines d’action pour répondre aux besoins des populations de l’Est de la RD Congo, de la santé à la formation aux métiers.
            </p>

            <dl class="mt-12 grid max-w-3xl grid-cols-3 gap-4 border-t border-white/15 pt-8 sm:gap-8">
                <div data-r>
                    <dd class="font-serif text-4xl font-semibold"><span data-n="9">0</span></dd>
                    <dt class="mt-1 text-sm text-white/65">domaines d’intervention</dt>
                </div>
                <div data-r>
                    <dd class="font-serif text-4xl font-semibold"><span data-n="35">0</span></dd>
                    <dt class="mt-1 text-sm text-white/65">projets répertoriés</dt>
                </div>
                <div data-r>
                    <dd class="font-serif text-4xl font-semibold"><span data-n="13">0</span></dd>
                    <dt class="mt-1 text-sm text-white/65">zones en Sud et Nord-Kivu</dt>
                </div>
            </dl>
        </div>
    </section>

    {{-- ═══ Barre de navigation interne (ancres par domaine) ══════════════ --}}
    <div class="sticky top-[60px] z-30 border-b border-line bg-white/95 backdrop-blur">
        <nav id="rl" class="mx-auto flex max-w-7xl overflow-x-auto px-5 lg:px-8" aria-label="Domaines">
            @foreach ($rail as $cle => $libelle)
                <a href="#{{ $cle }}"
                   class="flex shrink-0 items-center gap-2 border-b-2 px-3 py-3.5 text-sm font-medium transition {{ $loop->first ? 'border-ochre text-lake' : 'border-transparent text-ink/70' }}">
                    <span class="[&_svg]:h-5 [&_svg]:w-5">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="{{ $domaines[$cle]['icone'] }}"/>
                        </svg>
                    </span>
                    <span class="hidden lg:inline">{{ $libelle }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    {{-- ═══ Sections par domaine ═════════════════════════════════════════ --}}
    @foreach ($domaines as $cle => $domaine)
        @php
            $projetsDuDomaine = collect($projets)->where('domaine', $cle);
            $realises         = $projetsDuDomaine->where('annee', '>', 0)->sortByDesc('annee');
            $aFinancer        = $projetsDuDomaine->where('annee', '<=', 0)->values();
            $parcours         = $realises->concat($aFinancer);

            $annees           = $realises->pluck('annee');
            $periode          = $annees->isNotEmpty() ? $annees->min() . '–' . $annees->max() : '';

            $bailleurs        = collect();
            foreach ($projetsDuDomaine as $projet) {
                foreach (preg_split('~[\/,]~', $projet['bailleur']) as $segment) {
                    $seg = trim($segment);
                    if ($seg !== '' && $seg !== 'P.F.') {
                        $bailleurs->push($seg);
                    }
                }
            }
            $bailleurs = $bailleurs->unique()->values()->join(', ');
        @endphp

        <section id="{{ $cle }}" class="scroll-mt-32 {{ $loop->odd ? 'bg-mist' : 'bg-white' }} py-20">
            <div class="mx-auto grid max-w-7xl gap-12 px-5 lg:grid-cols-12 lg:px-8">

                {{-- ▸ Colonne gauche : résumé sticky --}}
                <div class="lg:col-span-4" data-r>
                    <div class="lg:sticky lg:top-40">
                        <span class="grid h-16 w-16 place-items-center bg-lake text-white">
                            <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="{{ $domaine['icone'] }}"/>
                            </svg>
                        </span>

                        <h2 class="mt-6 font-serif text-3xl font-semibold text-lake md:text-4xl">{{ $domaine['libelle'] }}</h2>

                        <p class="mt-3 leading-relaxed text-ink/70">{{ $domaine['descriptif'] }}</p>

                        <dl class="mt-6 grid grid-cols-2 gap-px border border-line bg-line">
                            <div class="bg-white p-4">
                                <dt class="text-xs text-ink/60">Projets réalisés</dt>
                                <dd class="mt-1 font-serif text-3xl font-semibold text-lake">{{ $realises->count() }}</dd>
                            </div>
                            <div class="bg-white p-4">
                                <dt class="text-xs text-ink/60">Projets à financer</dt>
                                <dd class="mt-1 font-serif text-3xl font-semibold text-lake">{{ $aFinancer->count() }}</dd>
                            </div>
                        </dl>

                        @if ($periode !== '')
                            <p class="mt-4 text-sm text-ink/70"><b>Période</b> : {{ $periode }}</p>
                        @endif
                        @if ($bailleurs !== '')
                            <p class="mt-2 text-sm text-ink/70"><b>Bailleurs et partenaires</b> : {{ $bailleurs }}</p>
                        @endif
                    </div>
                </div>

                {{-- ▸ Colonne droite : illustration, interventions, formations, projets --}}
                <div class="lg:col-span-8" data-r>
                    {{-- Illustration (dégradé + libellé, rendu côté serveur) --}}
                    <figure class="overflow-hidden" data-w>
                        <svg viewBox="0 0 800 300" preserveAspectRatio="xMidYMid slice" class="h-56 w-full md:h-72"
                             role="img" aria-label="{{ $domaine['libelle'] }}">
                            <defs>
                                <linearGradient id="degrade-{{ $cle }}" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0" stop-color="hsl({{ $domaine['teinte'] }} 45% 26%)"/>
                                    <stop offset="1" stop-color="hsl({{ $domaine['teinte'] }} 50% 44%)"/>
                                </linearGradient>
                            </defs>
                            <rect width="800" height="300" fill="url(#degrade-{{ $cle }})"/>
                            <text x="400" y="156" fill="#fff" fill-opacity=".85" font-family="sans-serif" font-size="24" text-anchor="middle">{{ $domaine['libelle'] }}</text>
                            <text x="400" y="182" fill="#fff" fill-opacity=".6" font-family="sans-serif" font-size="15" text-anchor="middle">Image d’illustration</text>
                        </svg>
                    </figure>

                    <h3 class="mt-10 text-sm font-semibold uppercase tracking-wider text-moss">Nos interventions</h3>
                    <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($domaine['interventions'] as $intervention)
                            <li class="flex gap-3 border border-line bg-white p-4 text-sm leading-relaxed">
                                <span class="mt-0.5 shrink-0 text-moss">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M5 12l5 5 9-10"/>
                                    </svg>
                                </span>
                                <span>{{ $intervention }}</span>
                            </li>
                        @endforeach
                    </ul>

                    @if ($domaine['formations'] !== [])
                        <h3 class="mt-10 text-sm font-semibold uppercase tracking-wider text-moss">Formations reçues de nos partenaires</h3>
                        <ul class="mt-4 space-y-2">
                            @foreach ($domaine['formations'] as $formation)
                                <li class="border-l-4 border-ochre bg-white px-4 py-3 text-sm leading-relaxed">{{ $formation }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <h3 class="mt-10 text-sm font-semibold uppercase tracking-wider text-moss">Projets associés ({{ $projetsDuDomaine->count() }})</h3>
                    @if ($projetsDuDomaine->isNotEmpty())
                        <ul class="mt-4 divide-y divide-line border border-line bg-white">
                            @foreach ($parcours as $projet)
                                <li class="flex items-center gap-4 p-4">
                                    <span class="w-20 shrink-0 text-sm font-bold {{ $projet['annee'] ? 'text-ink/60' : 'text-ochreDark' }}">
                                        {{ $projet['annee'] ?: 'À financer' }}
                                    </span>
                                    <span class="grow">
                                        <b class="block text-lake">{{ $projet['titre'] }}</b>
                                        @if ($projet['bailleur'] !== '')
                                            <span class="text-xs text-ink/60">{{ $projet['bailleur'] }}</span>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-4 border border-dashed border-line bg-white p-5 text-sm text-ink/65">
                            Aucun projet de ce domaine n’est répertorié à ce jour dans le portefeuille présenté.
                        </p>
                    @endif
                </div>

            </div>
        </section>
    @endforeach

    {{-- ═══ Compétences transversales ═════════════════════════════════════ --}}
    <section id="transversal" class="scroll-mt-32 bg-deep py-20 text-white">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <div data-r>
                <h2 class="font-serif text-4xl font-semibold">Compétences transversales</h2>
                <p class="mt-4 max-w-2xl text-white/70">Des formations reçues de nos partenaires et bailleurs, utiles à tous les domaines.</p>
            </div>

            <ul class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <li data-r class="border border-white/15 bg-white/5 p-6 transition duration-300 hover:border-ochre hover:bg-white/10">
                    <h3 class="font-serif text-lg font-semibold">Gestion administrative et financière</h3>
                    <p class="mt-2 text-sm text-white/65">PNUD COMREC, Uvira · 2008</p>
                </li>
                <li data-r class="border border-white/15 bg-white/5 p-6 transition duration-300 hover:border-ochre hover:bg-white/10">
                    <h3 class="font-serif text-lg font-semibold">Gestion de projet</h3>
                    <p class="mt-2 text-sm text-white/65">Pooled Fund Kinshasa, Uvira · 2009</p>
                </li>
                <li data-r class="border border-white/15 bg-white/5 p-6 transition duration-300 hover:border-ochre hover:bg-white/10">
                    <h3 class="font-serif text-lg font-semibold">GPS et smartphones pour la collecte de données</h3>
                    <p class="mt-2 text-sm text-white/65">Session de formation</p>
                </li>
                <li data-r class="border border-white/15 bg-white/5 p-6 transition duration-300 hover:border-ochre hover:bg-white/10">
                    <h3 class="font-serif text-lg font-semibold">Renforcement des capacités des organisations de la société civile</h3>
                    <p class="mt-2 text-sm text-white/65">Sensibilisation et formation</p>
                </li>
                <li data-r class="border border-white/15 bg-white/5 p-6 transition duration-300 hover:border-ochre hover:bg-white/10">
                    <h3 class="font-serif text-lg font-semibold">Ateliers SAFE</h3>
                    <p class="mt-2 text-sm text-white/65">Kigali (Rwanda) · 2016 — Nairobi (Kenya) · 2017</p>
                </li>
            </ul>
        </div>
    </section>

    {{-- ═══ Bandeau d'appel à l'action ═════════════════════════════════════ --}}
    <section class="bg-lake text-white">
        <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 px-5 py-16 lg:flex-row lg:items-center lg:px-8">
            <div data-r>
                <h2 class="font-serif text-3xl font-semibold md:text-4xl">Un besoin dans l’un de ces domaines ?</h2>
                <p class="mt-3 max-w-xl text-white/80">Décrivez-nous votre projet, parlons-en.</p>
            </div>
            <div class="flex flex-wrap gap-3" data-r>
                <a href="{{ route('contact', ['locale' => $locale]) }}"
                   class="relative overflow-hidden before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%] bg-ochre px-7 py-3.5 font-semibold text-deep transition hover:-translate-y-0.5 hover:shadow-xl">
                    Nous contacter
                </a>
                <a href="{{ route('projets.index', ['locale' => $locale]) }}"
                   class="border border-white/40 px-7 py-3.5 font-semibold transition hover:bg-white hover:text-deep">
                    Voir les projets
                </a>
            </div>
        </div>
    </section>
</main>

{{-- ═══ Pied de page ════════════════════════════════════════════════════ --}}
<footer class="bg-deep text-sm text-white/70">
    <div class="mx-auto grid max-w-7xl gap-12 px-5 py-16 md:grid-cols-2 lg:grid-cols-12 lg:px-8">

        {{-- ▸ Colonne 1 : logo, description, réseaux sociaux --}}
        <div class="lg:col-span-5">
            <a href="{{ route('accueil', ['locale' => $locale]) }}" class="flex items-center gap-3 text-white">
                <svg viewBox="0 0 40 40" class="h-12 w-12" aria-hidden="true">
                    <rect width="40" height="40" rx="9" fill="#17508F"/>
                    <path d="M5 27c6-9 11 3 17-6s8-5 13-9M5 21c5-8 10 2 15-5s9-4 15-8M5 33c7-8 12 3 18-5s7-4 12-7" fill="none" stroke="#E3A82B" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span class="leading-tight">
                    <b class="block font-serif text-2xl">OAAT</b>
                    <span class="block text-xs text-white/60">Organisation Africaine pour l’Aménagement des Territoires</span>
                </span>
            </a>

            <p class="mt-5 max-w-md leading-relaxed">
                Aménagement des territoires et développement durable des communautés à l’Est de la RD Congo, depuis 1995.
            </p>

            <p class="mb-3 mt-7 font-semibold text-white">Réseaux sociaux</p>
            <div id="so" class="flex gap-2">
                <a href="#" aria-label="Facebook" class="sc grid h-10 w-10 place-items-center border border-white/25 font-bold text-white transition hover:-translate-y-0.5 hover:border-ochre hover:bg-ochre hover:text-deep">f</a>
                <a href="#" aria-label="LinkedIn" class="sc grid h-10 w-10 place-items-center border border-white/25 font-bold text-white transition hover:-translate-y-0.5 hover:border-ochre hover:bg-ochre hover:text-deep">in</a>
                <a href="#" aria-label="X" class="sc grid h-10 w-10 place-items-center border border-white/25 font-bold text-white transition hover:-translate-y-0.5 hover:border-ochre hover:bg-ochre hover:text-deep">X</a>
                <a href="#" aria-label="YouTube" class="sc grid h-10 w-10 place-items-center border border-white/25 font-bold text-white transition hover:-translate-y-0.5 hover:border-ochre hover:bg-ochre hover:text-deep">▶</a>
            </div>
        </div>

        {{-- ▸ Colonne 2 : liens rapides --}}
        <div class="lg:col-span-3">
            <b class="text-white">Liens rapides</b>
            <ul id="fn" class="mt-4 space-y-2">
                @foreach ($liensNav as $lien)
                    <li>
                        <a href="{{ $lien[0] }}" class="transition hover:text-ochre">{{ $lien[1] }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- ▸ Colonne 3 : coordonnées --}}
        <div id="coordonnees" class="scroll-mt-24 lg:col-span-4">
            <b class="text-white">Coordonnées</b>
            <ul class="mt-4 space-y-4 leading-relaxed">
                <li>
                    <span class="block text-white">E-mail</span>
                    <a class="transition hover:text-ochre" href="mailto:oaatrdc2000@gmail.com">oaatrdc2000@gmail.com</a>
                </li>
                <li>
                    <span class="block text-white">Téléphone</span>
                    +243 993 537 325 · +243 896 127 195
                </li>
                <li>
                    <span class="block text-white">Bureau de représentation</span>
                    N° 007, Avenue Fizi II/Nyawera, Quartier Ndendere, Commune d’Ibanda, Bukavu, Sud-Kivu
                </li>
                <li>
                    <span class="block text-white">Siège social</span>
                    Nyangezi, Groupement de Karhongo, Territoire de Walungu
                </li>
            </ul>
        </div>

    </div>

    <div class="border-t border-white/10 py-5 text-center text-xs">
        &copy; 2026 OAAT. Tous droits réservés.
    </div>
</footer>

{{-- ═══ Bouton retour en haut (anneau de progression) ════════════════════ --}}
<button id="up"
        aria-label="Retour en haut"
        class="pointer-events-none fixed bottom-5 right-5 z-40 grid h-12 w-12 translate-y-4 place-items-center rounded-full bg-deep text-white opacity-0 shadow-xl transition duration-300 hover:bg-lake">
    <svg viewBox="0 0 48 48" class="absolute inset-0 -rotate-90" aria-hidden="true">
        <circle cx="24" cy="24" r="22" fill="none" stroke="#fff" stroke-opacity=".2" stroke-width="2"/>
        <circle id="rg" cx="24" cy="24" r="22" fill="none" stroke="#E3A82B" stroke-width="2"
                pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"/>
    </svg>
    <span class="relative">↑</span>
</button>

<script src="{{ asset('js/oaat.js') }}" defer></script>
</body>
</html>
