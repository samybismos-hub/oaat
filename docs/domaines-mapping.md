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

## Références projets (pour l'étape 3)

Les 35 lignes de la maquette utilisent la **clé maquette** dans la colonne `domaine`.
L'étape 3 les rattachera à `domain_id` via la table ci-dessus. Statuts :

- `annee > 0` → projet `completed` (année d'achèvement = `end_date`),
- `annee = 0` → projet « À financer » (à rattacher à un statut planifié/en attente,
  à confirmer avec l'enum `ProjectStatus`).
