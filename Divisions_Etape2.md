J'ai maintenant une vision complète de l'écart maquette ↔ base. Voici mon analyse et le découpage proposé pour le point 2 (données en dur → base de données).

🗺️ État réel constaté (ce que le point 2 doit résoudre)
La vue domaines-index est intégrée au layout (point ✓) mais affiche encore tout en dur dans deux gros @php :

Bloc en dur (lignes)	Données	Existe en base ?
$domaines (l.30-172)	libellé, teinte (hue), chemin SVG, descriptif, interventions[], formations[]	✅ table domains, mais seulement name, objectives, activities (phrases, icon = nom, pas de hue ni de path ni de listes)
$rail (l.175-185)	étiquettes courtes (Alimentation, Eau, Métiers…)	❌ nulle part
$projets (l.189-225)	35 projets année / titre / bailleur + statut « À financer »	✅ table projects, mais structurée différemment (status enum, partners N-N, pas d'année directe)
Écarts critiques identifiés :

Slugs non alignés — la vue utilise alim/logi/educ/prot/env/form, la base utilise securite-alimentaire/logistique/education/protection/environnement/formation-professionnelle. Les slugs DB sont déjà consommés par projets-index (table $gMap) et par la route domaines.show → ce sont eux qu'il faut conserver, pas renverser.
$bailleur (texte libre) ≠ relation DB partners (rôle funder) : il faut brancher sur funderLabel() / relation, et gérer les multi-bailleurs (SPES / UNHCR, PAM, USAID).
annee=0 = statut « À financer » → correspond à status='awaiting_funding' côté DB (l'enum existe) ; annee>0 → completed + end_date pour l'année.
Compteurs du héro data-n="9" / "35" / "13" et les 5 compétences transversales en dur → soient de vraies données, soit préciser qu'ils restent du design statique.
domaines-show.blade.php n'existe pas alors que la route/le controller l'exposent → flag hors-scope pour le point 2 (index), à traiter plus tard.
📋 Découpage proposé — 6 étapes vérifiables

Étape 1 — Mapping maquette ↔ base & conventions (aucune écriture de prod)
Rédiger une table de correspondance (commentée dans le code) : clé maquette → slug DB → hue / path icône / libellé court rail. Décisions prises par défaut, à valider :

Slugs existants conservés (compatibilité projets-index + routes) ; la vue les parcourt, pas d'alias.
teinte (hue) et étiquettes courtes $rail = données de présentation (design), → déplacées dans config/oaat.php ou en attribut accesseur, selon votre convention "les valeurs statiques n'ont pas leur place en BD" (déjà énoncée dans le fichier).
interventions / formations (listes) → colonnes JSON à ajouter sur domains (c'est du contenu éditorial).
✅ Critère : fichier de mapping figé, plus aucune divergence avec les slugs réels.

Étape 2 — Migration + modèle Domain enrichi
Migration additive : interventions (JSON), formations (JSON), et conserver icon/position existants. Aucune modification destructive des colonnes objectives/activities (utilisées par domaines.show/Filament).
DomainSeeder : injecter les 9 domaines de la maquette avec EN + FR dans name/objectives/activities/interventions/formations ; PositionSeeder inchangé.
Attributs accesseurs côté modèle si besoin (interventionsList() etc.) pour normaliser le rendu liste.
✅ Critère : php artisan migrate:fresh --seed OK ; Domain::with('projects')->get() retourne les 9 domaines ordonnés.

Étape 3 — Brancher les projets de la maquette sur la base
Faire coïncider les 35 lignes de la maquette avec les 38 projets du seeder : protéger les existants, créer les manquants (statut, domain_id par slug, end_date = année, published_at).
Bailleurs : PartenaireSeeder enrichi au besoin + attachement via pivot ('funder'), en gérant la séparation / et ,.
✅ Critère : chaque projet maquette retrouve exactement 1 domaine, 1 statut, 0-N bailleurs ; compteur data-n=35 cohérent avec le seed.

Étape 4 — Controller DomaineController::index
Récupérer domaines (orderBy('position'), withCount('projects'), projets published()), et supprimer du rendu les gros @php en dur (si on les retire après coup, faire d'abord la vue en Étape 5 sans casser le rendu, idéalement séquence : controller-vue ensemble).
Passer les compteurs calculés (domaines->count(), projets->count()) et la config rail/hue.
✅ Critère : la page /{locale}/domaines rend sans erreur, sans aucune référence à $projets hardcodés.

Étape 5 — Vue domaines-index
Boucles sur Eloquent ($domaine->name, $domaine->getTranslation(...), $domaine->projects), adaptateurs : $projet->title, funderLabel(), statut (badge année via end_date, getStatutCourtAttribute() pour d/w), $domaine->interventions/formations json_castées.
Traductions : libellés statiques passés dans $t(...) ; en conservant la structure FR/EN déjà en place.
Retrait définitif des @php du fichier.
✅ Critère : /fr/domaines et /en/domaines identiques à la maquette (structure/rendu) et issus 100 % de la base.

Étape 6 — Tests + validation complète
Nouveau test feature DomaineIndexTest : 200 la page, compteurs dynamiques calculés, pas de $liensNav/doublons de layout, vérification FR et EN sur données DB (type /fr/domaines).
Vérifier la non-régression : data-r/data-n/#rl scrollspy & §22 oaat.js fonctionnels (relancer la suite existante, 110 tests).
✅ Critère : suite complète verte + snapshot rendu/screenshot de contrôle.
Ordre obligatoire : 1 → 2 → 3 → (4+5 ensemble) → 6. Les étapes 2 et 3 sont indépendantes en lecture mais 3 dépend des slugs posés en 1. Aucune étape ne casse le site entre deux jalons (approches additives).

Veux-tu que je commence par l'étape 1 (le mapping + conventions à valider avec toi) — c'est le seul point qui engage des choix (hue/rail en config, icônes, statut des compétences transversales) — ou préfères-tu que je rédige directement la proposition de mapping comme première livraison ?
