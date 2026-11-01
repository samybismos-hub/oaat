# Mapping « Domaines » — maquette ↔ base de données (étape 1)

> Conventions figées le 31/10/2026. Ce fichier est la **référence unique** pour les
> étapes 2 (colonnes `interventions`/`formations`) et 3 (rattachement des 35 projets
> de la maquette à la table `projects`).

## Décisions d'étape 1

| Sujet | Décision | Emplacement |
|---|---|---|
| 1. Teinte HSL des domaines | **Option B** (design figé) | `config/oaat.php` → `domaine_page.tints` |
| 2. Libellés courts du rail | **Option B** (design figé) | `config/oaat.php` → `domaine_page.rail` |
| 3. Interventions + Formations | **Option A** (contenu éditorial) | colonnes JSON sur `domains` (étape 2) |

**Règle de base** : les slugs de la table `domains` font foi (consommés par
`projets-index`, la route `domaines.show` et le `DomainSeeder`). La clé courte
de la maquette n'est **qu'un identifiant de travail** pour la migration.

## Table de correspondance

| Clé maquette | Slug base (`domains.slug`) | position | Libellé FR maquette | Libellé FR base (seeder) | Teinte | Rail |
|---|---|---|---|---|---|---|
| `sante` | `sante` | 1 | Santé | Santé | 350 | Santé |
| `alim` | `securite-alimentaire` | 2 | Sécurité alimentaire | Sécurité alimentaire | 95 | Alimentation |
| `logi` | `logistique` | 3 | Logistique | Logistique | 30 | Logistique |
| `educ` | `education` | 4 | Éducation | Éducation | 215 | Éducation |
| `wash` | `wash` | 5 | Eau, hygiène et assainissement | **Hygiène et Assainissement** ⚠️ | 190 | Eau |
| `genre` | `genre` | 6 | Genre | Genre | 285 | Genre |
| `prot` | `protection` | 7 | Protection | Protection | 330 | Protection |
| `env` | `environnement` | 8 | Environnement | Environnement | 150 | Environnement |
| `form` | `formation-professionnelle` | 9 | Formation professionnelle | Formation professionnelle | 45 | Métiers |

## Écarts à corriger à l'étape 2 (alignement du seeder sur la maquette)

- **`wash`** : le libellé FR du seeder (« Hygiène et Assainissement ») diffère de la
  maquette (« Eau, hygiène et assainissement »). → Aligner `name.fr` sur la maquette.
- **`education`** : `activities.en` du seeder se termine sans point (cohérence mineure,
  à normaliser au passage).
- **`icon` (nom)** : la base stocke des noms d'icônes (`heart-pulse`, `wheat-awn`…),
  la maquette des chemins SVG. Le rendu final de la page n'expose aucune icône de ce
  niveau → aucun alignement requis pour le point 2 (hors scope).
- **`descriptif`** : la maquette a un champ `descriptif` ; la base a `objectives`
  (formulation différente). Voir étape 2 (soit colonne dédiée, soit mapping sur une
  phrase courte dérivée — à trancher sur pièces).

## Analyse comparative — maquette (35 lignes) ↔ seeder (37 projets) — étape 3

Source : `domaines-index.blade.php` (bloc `$projets`) vs `ProjectSeeder.php`.

### 1. Correspondances sûres (le slug seeder a un équivalent maquette **explicite**)

| Année maq | Domaine maq | Titre maquette | Slug seeder | Écarts |
|---|---|---|---|---|
| 1994 | prot | Sensibilisation des femmes burundaises à la cohabitation | `cohabitation-femmes-burundaises` | ✅ (CECI+PNUD funder) |
| 1995 | form | Atelier couture orphelins/veuves, Burundi | `atelier-couture-buyezi` | AVEB implementer |
| 1995 | educ | École primaire enfants orphelins, Bujumbura | `ecole-orphelins-bujumbura` | ✅ |
| 1996 | wash | Assainissement d'Uvira | `assainissement-site-uvira` | bailleur PAM absent du seed |
| 1997 | form | Tuiles, briques, foyers Ruzizi | `tuiles-briques-ruzizi` | PAM present, USAID absent |
| 1998 | logi | Route Kamanyola–Uvira | `route-kamanyola-uvira` | bailleurs PAM/USAID absents |
| 1999 | sante | Centres santé Mazigiro et Ibambiro | `centres-sante-mazigiro-ibambiro` | BCZS absent (PAM/USAID présents) |
| 2000 | sante | Centres santé Walungu | `centres-sante-walungu` | BDOM/Louvain absents |
| 2000 | logi | Escalier 5 400 m Bukavu | `escalier-bukavu` | ✅ (MONUC) |
| 2005 | alim | Petit bétail Lemera | `petits-betails-lemera` | ✅ (BDOM+Louvain) |
| 2006 | sante | Maternité Kasenga | `maternite-kasenga` | SPES/UNHCR absents |
| 2006 | educ | 50 classes à Fizi | `ecoles-fizi-50-classes` | SPES/HCR présents, maq dit AVSI/ECHO ⚠️ |
| 2006 | educ | 4 écoles Ruzizi | `ecoles-latrines-citernes-ruzizi` | AVSI+UNICEF présents (+ECHO) |
| 2007 | logi | 2 ponts Kasandjala | `ponts-kasandjala-i-et-ii` | ✅ (PNUD) |
| 2008 | wash | Eau/hygiène Sebele (8 puits) | `eau-potable-sebele-fizi` | ✅ (Pooled Fund) |
| 2009 | logi | Route Sebele–Kazimia | `route-sebele-kazimia` | ✅ (PNUD) |
| 2010 | wash | 346 latrines familiales | `latrines-familiales-sud-kivu` | Tearfund absent (BETING implementer) |
| 2013 | logi | Route 38 km Numbi–Ziralo | `route-numbi-ziralo` | AAP/P.F. absents (BETING implementer) |
| 2014 | form | Autonomisation jeune Goma | `autonomisation-jeunes-goma` | BETING absent (UNICEF present) ⚠️ |
| 2014 | educ | 12 classes EP Bakhita | `rehabilitation-ep-bakhita` | COPA absent (EDAP+PICAG présents) |
| 2015 | form | Entrepreneuriat Masisi | `entreprenariat-jeunes-masisi` | ✅ |
| 2017 | wash | Citernes EP Camp Saio | `citernes-ep-camp-saio` | EPSP/UNICEF absents |
| 2019 | form | Jeunes Nyiragongo | `formation-jeunes-nyiragongo` | BETING absent |
| 2021 | educ | EP EDAP/ISP | `construction-rehabilitation-ep-edap-isp` | EDAP absent du seed |
| 2022 | alim | Entrepôt riz Nyamubanda | `entrepot-riz-nyamubanda` | PICAGEL absent |
| 2023 | alim | Tilapia Karhongo | `pisciculture-tilapia-karhongo` | ✅ (FAO) |
| 2024 | educ | École Karhale | `ecole-karhale-kadutu` | status seed = **ongoing** (maq = réalisé) ⚠️ |
| 2024 | prot | Foyer jeunes filles mères Saké | `jeunes-filles-meres-sake` | **domaine seed = genre ≠ maq prot** ⚠️ |

### 2. Projets « à financer » (annee = 0) — tous présents en seeder ✅

| Titre maquette | Slug seeder | Statut seed |
|---|---|---|
| Aménagement des huit territoires | `amenagement-des-8-territoires` | awaiting_funding ✅ |
| Drainage marais Nyamubanda | `drainage-marais-nyamubanda` | awaiting_funding ✅ |
| Relance production agricole Fizi | `relance-production-agricole-fizi` | awaiting_funding ✅ |
| Relance production riz Fizi | `securite-alimentaire-riz-fizi` | awaiting_funding ✅ |
| Intégration genre Sud-Kivu | `integration-genre-femmes-sud-kivu` | awaiting_funding ✅ |
| Prévention Ebola/COVID/mpox écoles | `prevention-ebola-covid-mpox-ecoles` | awaiting_funding ✅ |

### 3. Projets seeder **sans équivalent maquette** (surnuméraires, à trancher)

- `forages-latrines-fizi` (wash, 2009, PNUD funder) → pourrait absorber la mention « huit puits » de la ligne 2008 (fusion possible).
- `sensibilisation-ebola-covid19` (sante, completed 2018→2022, EPSP+UNICEF funder) → absent de la liste maquette.

### 4. Ligne maquette **sans équivalent seeder explicite**

- **« Formation en informatique de jeunes à Beni »** (2010, form, BETING). Le seul projet « Beni » du seed est `encadrement-jeunes-beni` (**protection**, BARAKA, « Appui à l'encadrement ») : projet différent. À créer (`formation-informatique-beni`) ou à fusionner selon ta décision.

### 5. Bilan — 37 projets seed pour 35 lignes maquette

- Divergences **éditoriales** à arbitrer : bailleurs (10+ cas), domaine `jeunes-filles-meres-sake` (genre vs prot), statut `ecole-karhale-kadutu` (ongoing vs réalisé).
- Note technique : le compteur `data-n=35` du héro et les compteurs « Projets associés (n) » devront être **calculés dynamiquement** depuis la DB (plus jamais en dur) — conséquence prévue pour l'étape 5.

### 6. Décisions à prendre avant alignement (étape 3)

1. **Source de vérité des bailleurs** : la maquette (texte figé) ou le seeder (pivot déjà structuré) ?
   Recommandation : seeder reste la référence pivot `project_partner` ; on **ajoute** les bailleurs maquette manquants
   (BCZS, COPA, Tearfund, AAP, P.F., SPES, BDOM, Louvain, USAID, PICAGEL…) au `PartnerSeeder` puis au pivot.
2. **`ecole-karhale-kadutu`** : on passe en `completed` (aligné maquette) ou on garde `ongoing` ?
3. **`jeunes-filles-meres-sake`** : domaine `genre` (seed) ou `protection` (maquette) ?
4. **`formation-informatique-beni`** : créer le projet, ou considérer `encadrement-jeunes-beni` comme équivalent ?
5. **35 vs 37** : (a) supprimer/déconsidérer les 2 surnuméraires, ou (b) garder 37 projets publiés et laisser les compteurs dynamiques le refléter ?

