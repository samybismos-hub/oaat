J'ai terminé l'analyse chirurgicale. Voici le diagnostic complet, les solutions que vous pouvez implémenter seul, et le point précis sur ce qui reste vraiment.

1. État des lieux de domaines-index.blade.php
La vue fait 295 lignes et enchaîne trois niveaux de logique :

en-tête : $t/$locale (pattern correct), SEO depuis la page CMS (correct), CSS d'animation (pattern correct) ;
bloc @php principal (l.34-73) : 5 closures maison — $anneeProjet, $railLabel, $icone, $teinte, $bailleurProjet + lecture des compétences ;
boucle par domaine (l.107-248) avec un @php interne (l.109-134) qui recalcule réalisés / à financer / période / bailleurs et un @php par projet (l.220-225) ;
compétences + bandeau CTA (l.250-289).
2. Les problèmes, par gravité
P1 — Le pattern n°1 du projet est rompu : tout le métier est dans la vue
Le pattern confirmé partout (ProjetController, OrganisationController, AccueilController) : le contrôleur prépare les données, la vue affiche ; les vues pages/* sont minces et délèguent à des partials/.

Ici, DomaineController::index() ne fait que charger les modèles : tous les calculs sont dans le template — tris, filtres par statut, période min/max, agrégation des bailleurs. Pire : la vue duplique des méthodes qui existent déjà sur Project :

Duplication dans la vue	Méthode existante
$anneeProjet(...) (l.37)	Project::year() (Project.php, l.289 → ?int)
statut_court === 'd'/'w'	Project::$statutCourt (l.405)
$bailleurProjet($projet) (l.54)	funderLabel() (l.355) + implementationPartnersLabel() (l.338) + partnersLabel() (l.323)
P2 — Votre désaccord sur bailleurProjet est pleinement fondé
Sémantique : $bailleurProjet renvoie les bailleurs OU, si la liste est vide, tous les partenaires. Un projet qui a un bailleur → ses partenaires de mise en œuvre disparaissent. Les partenaires et bailleurs ne sont jamais tous affichés.
Fragilité : dans le résumé par domaine, $bailleurs (l.120-126) fait preg_split('~[\/,]~', $bailleurProjet($p)) : on concatène les noms en chaîne, puis on re-découpe sur / et ,. Un nom contenant / (ex. « UNFPA/Sud-Kivu », « Baraka/… ») est cassé en deux fragments, un filtre en dur retire « P.F. », puis on rejoint avec , . C'est du chaîne→liste→chaîne qui perd les entités (pas de logo, pas de lien vers /partenaires, pas de déduplication par id).
Perte du rôle : le site étiquette pourtant les rôles (cellules « Bailleurs » et « Partenaires de mise en œuvre » dans _carte_projet, libellés dans PartnerRole::getLabel()). Tout est fondu dans un suffixe.
P3 — Réinvention d'une mini-carte projet au lieu de _carte_projet
Le bloc « Projets associés » (l.216-238) réimplémente une carte maison (année / « À financer » + titre + bailleur). La carte canonique existe : partials/_carte_projet (réutilisée dans projets-index et projets-show). Trouvez aussi : dans cette version actuelle, aucun lien vers la fiche projet (projets.show) dans la liste — or _carte_projet en fournit un.

P4 — Fake placeholder « Image d'illustration »
L'illustration de chaque domaine (l.177-190) est un SVG généré portant un texte visible « Image d'illustration ». Le pattern du site pour une image absente est le dégradé propre de _carte_projet ($svghtml : dégradé + nom du domaine, sans légende « placeholder »).

P5 — Incohérence des compteurs
$compteurs['zones'] vient de Setting::stat_zones (saisie manuelle) alors que ProjetController calcule distinct count(city) des projets publiés → deux sources pour « zones », valeurs divergentes.
projetsCount et zonesCount sont passés à la vue en plus de $compteurs → variables mortes.
P6 — La page reste un monolithe (~300 lignes), pas un assemblage de partials
Le pattern home/organisation est : page mince + @include('partials._xxx', dados). Ici, une seule vue spaghetti.

P7 — Aucun test pour la page
Il existe ProjetIndexTest, ContactPageTest… mais rien pour /fr/domaines. La non-régression n'est pas verrouillée.

3. Les patterns du projet à respecter (rappel validé à la lecture du code)
Contrôleur = préparation, vue = affichage (leitmotiv des 4 contrôleurs publics).
Pages minces qui assemblent des partials (home en enchaîne 7).
Cartes projets = _carte_projet, labels portés par le modèle (year(), funderLabel(), implementationPartnersLabel(), statusLabel()).
Éditorial = base ; design figé = config (domaine_page.tints/rail/icons → Option B déjà en place).
$t/$locale re-définis en tête de vue (le layout les définit après l'évaluation des sections) — pattern conservé, ne pas le toucher.
Partenaires = entités (logo, pivot role, enum PartnerRole), jamais des chaînes concaténées.
4. Solutions proposées (par ordre de mise en œuvre)
S1 — Déplacer toute la préparation dans DomaineController::index()
Faire précéder le return view(...) par une passe qui prépare, pour chaque domaine, une structure prête à afficher (sur le modèle des $timeline préparées par OrganisationController) :


$sections = $domaines->map(function (Domain $d) use ($locale) {
    $projets   = $d->projects;                                  // déjà published + partners
    $realises  = $projets->filter(fn ($p) => $p->statut_court === 'd')
                         ->sortByDesc(fn ($p) => $p->year() ?? 0)->values();
    $aFinancer = $projets->filter(fn ($p) => $p->statut_court === 'w')->values();

    $annees    = $realises->map(fn ($p) => $p->year())->filter()->values();

    // Entités partenaires dédupliquées par ID (jamais par split de chaîne)
    $bailleurs     = $projets->flatMap(fn ($p) => $p->partners)
                             ->unique('id')
                             ->values();
    $implementeurs = /* idem, filtré sur pivot.role->value === 'implementer' */;

    return (object) [
        'domaine'      => $d,
        'nom'          => $d->getTranslation('name', $locale) ?: $d->slug,
        'intro'        => $d->getTranslation('intro', $locale)
                          ?: $d->getTranslation('objectives', $locale) ?: '',
        'interventions'=> (array) $d->getTranslation('interventions', $locale) ?? [],
        'formations'   => (array) $d->getTranslation('formations', $locale) ?? [],
        'realises'     => $realises,
        'aFinancer'    => $aFinancer,
        'periode'      => $annees->isEmpty() ? '' : $annees->min().'–'.$annees->max(),
        'bailleurs'    => $bailleurs,
        'implementeurs'=> $implementeurs,
    ];
})->values();
→ Dans la vue, plus aucun @php interne : on boucle sur $sections et on affiche $sec->realises, $sec->bailleurs, etc. Le rail, le hero et les compteurs utilisent les données brutes ($domaines, $compteurs) comme aujourd'hui.

✅ **Fait — 02/11/2026.** S1 implémenté : la préparation est déplacée dans `DomaineController::index()` (structure `$sections`). La vue boucle sur `$sections` — plus aucun `@php` interne (filtres, période, agrégation). Au passage : `parcours` (lignes prêtes du bloc « Projets associés », avec `url`), `bailleursLabel` / `implementeursLabel`, et `$competences` via `Page::competencesFor($locale)`.
S2 — Répondre à votre exigence : tous les partenaires et bailleurs affichés
Le modèle a ce qu'il faut — supprimez la closure $bailleurProjet :

Par projet (ligne compacte) : afficher les deux groupes, sans « ou » :

Bailleurs : PNUD, UNICEF   ·   Mise en œuvre : BETING
c.-à-d. $projet->funderLabel($locale) et $projet->implementationPartnersLabel($locale) (chacun masqué seulement s'il vaut '—').
Par domaine (colonne gauche) : remplacer le bloc $bailleurs splité par deux lignes étiquetées construites sur les entités (déduplication unique('id'), libellés de PartnerRole::getLabel()) :

Bailleurs : PNUD, UNICEF
Partenaires de mise en œuvre : BETING, AVEB
(Un même partenaire peut légitimement apparaître dans les deux lignes s'il a les deux rôles sur des projets de ce domaine — c'est exact, pas un bug.)
✅ **Fait — 02/11/2026.** S2 implémenté : suppression de la closure `$bailleurProjet` et du `preg_split` (plus de « chaîne → liste → chaîne »). Dans le contrôleur, entités partenaires dédupliquées par `id` et filtrées sur `pivot.role` (`bailleurs` / `implementeurs`). Colonne gauche : deux lignes étiquetées « Bailleurs » et « Partenaires de mise en œuvre » (masquées si vides) ; ligne par projet : `Bailleurs : …` et `Mise en œuvre : …` affichées ensemble, chacune masquée seulement si « — ».
S3 — Réutiliser _carte_projet (ou extraire partials/_projet_liste.blade.php)
Si « Projets associés » peut prendre la carte standard → @include('partials._carte_projet', ['projet' => $projet]) (avec $gradHsl en variante fournie par le contrôleur, cf. projets-index). Bonus : lien vers projets.show récupéré.
Si le design maquette impose une liste compacte → extraire un partial _projet_liste.blade.php avec en-tête documenté (comme _carte_projet), qui n'appelle que les méthodes du modèle (S2).
✅ **Fait — 02/11/2026.** S3 implémenté en version « liens seulement » (pas de partial pour l'instant) : chaque titre du bloc « Projets associés » pointe vers `projets.show` (`route('projets.show', ['locale' => $locale, 'slug' => $p->slug])`, propriété `url` de `$sec->parcours`).
S4 — Remplacer le placeholder par l'illustration pattern
Réutiliser le dégradé $teinte($domaine) déjà en config pour le linearGradient, et supprimer les deux <text> dont « Image d'illustration » — ou réutiliser un générateur data-uri identique à $svghtml de _carte_projet.

S5 — Uniformiser les compteurs
zones : soit distinct count(city) comme ProjetController, soit assumer stat_zones en le documentant — mais une seule source.
Supprimer projetsCount/zonesCount de la vue (masse morte) ; ne passer que $compteurs.
S6 — (Dernier) Découper en partials, pattern home/organisation
pages/domaines-index = _hero_simple + _domaines_index_rail + _domaines_index_section (un bloc par domaine, appelé dans la boucle) + sections compétences/CTA (soit en dur soigneusement, soit 2 micro-partials). La page finale tombe à ~80 lignes sans aucune logique.

**Statut (02/11/2026) : S1 ✅ · S2 ✅ · S3 ✅ — reste S4 → S5 → S6.** Prévoir : php artisan test --filter='ProjetIndexTest|ContactPageTest' + php artisan test sur les tests feature, + rendu /fr/domaines et /en/domaines (200, contenus FR/EN, aucun résidu d'anciennes colonnes).

**État d'avancement (02/11/2026)** — points 1 à 3 de la section 4 traités :

- ✅ **S1** — toute la logique de préparation vit dans `DomaineController::index()` ; la vue ne calcule plus rien.
- ✅ **S2** — les deux groupes (bailleurs et mise en œuvre) sont affichés, sans « ou », par domaine et par projet ; entités dédupliquées par `id`.
- ✅ **S3** — liens vers `projets.show` ajoutés sur les titres du bloc « Projets associés » (pas de partial, liens seulement).
- ⏳ **S4** (illustration placeholder) · **S5** (compteurs) · **S6** (partials) restent à faire.

Vérification effectuée : rendu `/fr/domaines` et `/en/domaines` → 200 (liens vers les fiches projets et libellés bailleurs/mise en œuvre présents), `ProjetIndexTest` + `ContactPageTest` au vert (10 tests).
5. Ce qui reste réellement pour finir (y compris hors de cette page)
#	Sujet	Constat	Action
1)	Logique de la vue	Objet de cette analyse (P1-P6)	À traiter (S1→S6) ; S1 ✅ S2 ✅ S3 ✅ faits le 02/11/2026 — reste S4 → S6 (voir bloc « État d'avancement »)
2)	/domaines/{slug} (domaines.show)	Route câblée (web.php:51) et liée par les 9 cartes de l'accueil (_domaines.blade.php:48) mais la vue pages/domaines-show n'existe pas (jamais créée dans l'historique) → clic = 500	Créer la fiche domaine (hero + objectifs + projets du domaine en _carte_projet + partenaire list) — ou masquer la route et les liens si hors périmètre
3)	/partenaires	Item du menu header (_header.blade.php:37, « Partenaires ») mais vue pages/partenaires inexistante → clic = 500 (préexistant, jamais dans l'historique)	Créer une page « Partenaires » (logos de tous les partenaires — en phase avec votre exigence « tous les partenaires affichés ») ou retirer l'item du menu
4)	Arbitrages de données (étape 3 du mapping)	Non réalisés : 10 bailleurs manquants au pivot (BCZS, Tearfund, AAP, P.F., PICAGEL…), wash toujours « Hygiène et Assainissement » (maquette : « Eau, hygiène et assainissement »), ecole-karhale-kadutu ongoing (maquette réalisé), jeunes-filles-meres-sake genre (maquette protection), 2 projets surnuméraires	Décisions éditoriales à arbitrer (le doc docs/domaines-mapping.md §6 liste les 5 questions) avant de « finir » la page
5)	Test de non-régression	Aucun DomainesIndexTest (rendu FR/EN validé ponctuellement le 02/11/2026)	Créer le test (200 FR/EN, présence titre/intro/compétences, absence des anciennes colonnes) pour verrouiller
6)	Compteurs déjà dynamiques	✅ Fait : $compteurs calculés en SQL dans le contrôleur (prévu « étape 5 » du mapping)	Rien à faire
Ce qui est déjà bon (ne pas casser)
Rendu 100 % serveur bilingue via $t()/$locale ; héro + SEO pilotés par la page CMS domaines.
Compétences transversales en base (Repeater FR/EN, repli FR) — propre depuis ma dernière passe.
Design figé (tints/rail/icons) en config/oaat.php (Option B) ; partenaires eager-loaded ; SYSTEM_SLUGS protège la page.
Filament PageForm conforme (seulement competences éditable).
Voulez-vous que j'implémente S1→S6 (et éventuellement #2 et #3 du tableau) pour vous, ou préférez-vous suivre ces étapes vous-même ? Si je dois le faire, passez simplement en mode Act et je le réalise dans cet ordre avec validation à chaque étape.
