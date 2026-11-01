# Implémentation `domaines-index` — statut & prochaines étapes

> Document de suivi. L'audit initial (vue maquette autonome, shadowing `$domaines`,
> hardcodé) est **résolu** : la vue est intégrée au layout, bilingue FR/EN, et
> **100 % pilotée par la base et les Pages du site** (Filament). Références :
> `docs/domaines-mapping.md` (conventions) et `Divisions_Etape2.md` (découpage en 6 étapes).

---

## ✅ Terminé et vérifié

| Point | État | Preuve |
|---|---|---|
| Vue intégrée au layout | ✅ `@extends('layouts.app')`, topbar/header/footer du layout, `@push('scripts')` `oaat.js` | — |
| Shadowing `$domaines` supprimé | ✅ la Collection Eloquent du contrôleur est utilisée | `DomaineController@index` |
| Boucle domaines | ✅ `slug`, `getTranslation('name'\|'intro'\|'objectives', $locale)`, `interventions`/`formations` (colonnes JSON) | rendu `/fr/domaines` = 200 |
| Projets par domaine | ✅ `->with('projects', fn($q) => $q->published()->with('partners'))` ; tri réalisés/à financer, année via `end_date`/`start_date`, `statut_court` (d/w), `funderLabel()`/`partnersLabel()` | 38 projets publiés |
| Teintes + libellés rail + icônes | ✅ design figé dans `config/oaat.php` → `domaine_page.tints/rail/icons` | — |
| Compteurs héro dynamiques | ✅ `$domaines->count()` = 9, `$projetsCount` = 38, `$zonesCount` = 13 (Setting) | rendu (badges `data-n`) |
| Écart `wash` (name fr) | ✅ base = « Eau, hygiène et assainissement » (aligné maquette) | probe DB |
| **Bilingue FR/EN des libellés de structure** | ✅ tous les libellés statiques (fil d'Ariane, compteurs, colonnes, interventions/formations, projets associés, badge « À financer », message vide, CTA boutons) passés dans `$t(...)` | rendu `/en/domaines` vérifié |
| **Page CMS « domaines » (Filament)** | ✅ Page slug `domaines` seedée : titre, body (héro), `meta_title`/`meta_description`, `competences_titre`/`intro`/`competences` (JSON FR/EN), `cta_titre`/`intro` | `PageSeeder` + colonnes migrées (migration `2026_11_01_000001_add_domaines_index_blocks_to_pages_table`) |
| **SEO piloté par la base** | ✅ `<title>`/`meta description` lus depuis la page CMS avec repli `$t()` | rendu des deux langues |
| **Compétences transversales en base** | ✅ 5 items {titre, detail} FR/EN depuis la page CMS, boucle dynamique dans la vue | rendu FR/EN |
| **CTA piloté par la base** | ✅ titre + intro du bandeau depuis la page CMS (boutons en `$t()`) | rendu FR/EN |
| Admin Filament | ✅ `PageForm` : nouvelle section « page Domaines uniquement » (Repeater compétences + CTA, onglets FR/EN), page ajoutée aux pages système (slug verrouillé, suppression bloquée) | `php -l` + structure vérifiée |
| Non-régression | ✅ `ContactPageTest` 3 passed, `ProjetIndexTest` 7 passed (70 assertions) ; rendu kernel `/fr|/en/domaines` HTTP 200 | suite feature |
| Scripts temporaires nettoyés | ✅ plus aucun helper `_*` à la racine | `git status` |

## ⚠️ Points restants

### 1. `domaines-show.blade.php` absent alors que la route existe (casse réelle)
- La route `/domaines/{slug}` → `DomaineController@show` existe, et le partial
  `resources/views/partials/_domaines.blade.php` (inclus dans la **page d'accueil**
  `home.blade.php`) y fait des liens `route('domaines.show', …)`.
  → **Clic depuis l'accueil = erreur 500** (`firstOrFail` OK mais vue manquante).
- Décision : créer la vue `domaines-show` (une fois la maquette disponible)
  **ou** retirer la route/le controller et repasser le partial `_domaines`
  sur des ancres `#slug` vers `domaines.index`.

### 2. Arbitrages de contenu de l'étape 3 (ouverts dans `docs/domaines-mapping.md` §6)
Impact direct sur ce qu'affiche la page (« Projets associés (n) », bailleurs) :
- bailleurs maquette absents du pivot (BCZS, COPA, Tearfund, AAP, P.F., SPES,
  BDOM, Louvain, USAID, PICAGEL…) — base : 25 partenaires / 33 pivots ;
- `ecole-karhale-kadutu` : statut `ongoing` en base vs « réalisé » maquette ;
- `jeunes-filles-meres-sake` : domaine `genre` (base) vs `protection` (maquette) ;
- `formation-informatique-beni` : projet 2010 maquette sans équivalent seeder ;
- compteur : 38 projets publiés affichés vs les 35 de la maquette (déjà assumé
  dynamiquement, mais l'alignement 35 vs 37/38 reste à valider).

### 3. Test feature `DomaineIndexTest` (étape 6 du plan)
À créer (`tests/Feature/DomaineIndexTest.php`) :
- `GET /fr/domaines` et `GET /en/domaines` → **200** ;
- compteurs dynamiques (domaines=9, projets = `Project::published()->count()`) ;
- rendu 100 % base : pas de références `$domaine[` / `$projet[` ; présence des
  traductions FR/EN selon la langue ;
- non-régression JS : présence de `data-r`, `data-n`, `#rl` (scrollspy `oaat.js`).

---

## Ordre de priorité restant

1. **Réparer `domaines.show`** (casse depuis l'accueil) — en attente de la maquette,
   sinon désactiver la route et rattacher le partial aux ancres `#slug`.
2. **Arbitrages de contenu étape 3** (bailleurs, statuts, domaines, 35 vs 38).
3. **`DomaineIndexTest` + passe complète de non-régression**.