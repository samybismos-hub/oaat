# Implémentation `domaines-index` — statut & prochaines étapes

> Document de suivi (mis à jour). L'audit initial (vue maquette autonome, shadowing
> `$domaines`, hardcodé) a été **résolu** : la vue est intégrée au layout et pilotée
> par la base. Références : `docs/domaines-mapping.md` (conventions) et
> `Divisions_Etape2.md` (découpage en 6 étapes).

---

## ✅ Ce qui est terminé et vérifié

| Point | État | Preuve |
|---|---|---|
| Vue intégrée au layout | ✅ `@extends('layouts.app')`, topbar/header/footer du layout, `@push('scripts')` `oaat.js` | — |
| Shadowing `$domaines` supprimé | ✅ la Collection Eloquent du contrôleur est utilisée | `DomaineController@index` |
| Boucle domaines | ✅ `slug`, `getTranslation('name'\|'intro'\|'objectives', $locale)`, `interventions`/`formations` (colonnes JSON) | rendu `/fr/domaines` = 200 |
| Projets par domaine | ✅ `->with('projects', fn($q) => $q->published()->with('partners'))` ; tri réalisés/à financer, année via `end_date`/`start_date`, `statut_court` (d/w), `funderLabel()`/`partnersLabel()` | 38 projets publiés |
| Teintes + libellés rail + icônes | ✅ design figé dans `config/oaat.php` → `domaine_page.tints/rail/icons` | — |
| Compteurs héro dynamiques | ✅ `$domaines->count()` = 9, `$projetsCount` = 38, `$zonesCount` = 13 (Setting) | rendu (badges `data-n`) |
| Écart `wash` (name fr) | ✅ base = « Eau, hygiène et assainissement » (aligné maquette) | probe DB |
| Scripts de migration nettoyés | ✅ plus aucun `_migrate_view_part*.php` / helper à la racine | `git status` |

## ⚠️ Points restants — à aborder pour finaliser

### 1. `domaines-show.blade.php` absent alors que la route existe (casse réelle)
- La route `/domaines/{slug}` → `DomaineController@show` existe, et le partial
  `resources/views/partials/_domaines.blade.php` (inclus dans la **page d'accueil**
  `home.blade.php` l.48) y fait des liens `route('domaines.show', …)`.
  → **Clic depuis l'accueil = erreur 500** (`firstOrFail` OK mais vue manquante).
- Décision : créer la vue `domaines-show` **ou** retirer la route/le controller
  et repasser le partial `_domaines` sur des ancres `#slug` vers `domaines.index`.

### 2. Bilingue FR/EN incomplet (libellés statiques en dur)
- Rendu `/en/domaines` vérifié : **tous les libellés de structure restent en FR**
  alors que la méta et les données (noms, interventions…) basculent en EN.
- Éléments concernés : h1/sous-titre héro, fil d'Ariane
  (« Accueil », « Domaines d’intervention »), labels compteurs (« domaines
  d’intervention », « projets répertoriés », « zones en Sud et Nord-Kivu »),
  en-têtes colonnes (« Projets réalisés », « Projets à financer », « Période »,
  « Bailleurs et partenaires », « Nos interventions », « Formations reçues de nos
  partenaires », « Projets associés », « À financer », « Aucun projet… »), section
  Compétences transversales, CTA (« Nous contacter », « Voir les projets »).
- Action : passer ces chaînes dans `$t('…', '…')` (la closure existe déjà en tête de
  vue, utilisée aux seules métas).

### 3. Compétences transversales : dernier contenu éditorial en dur
- 5 cartes figées en HTML (`Gestion administrative et financière · PNUD COMREC Uvira
  2008`, `Gestion de projet · Pooled Fund 2009`, `GPS et smartphones`, `Renforcement
  des capacités OSC`, `Ateliers SAFE · Kigali/Nairobi`) — **aucune structure de données**.
- Décision de stockage à trancher :
  - (a) **Design assumé** → déplacer dans `config/oaat.php` (comme tints/rail) ;
  - (b) **Contenu éditorial** → colonne JSON `competences_transversales` (sur
    `domains` d'un domaine dédié, sur `settings`, ou table dédiée) + champ admin,
    avec FR/EN (la maquette ne fournit que la version FR).

### 4. Aribitrages de contenu de l'étape 3 (ouverts dans `docs/domaines-mapping.md` §6)
Impact direct sur ce qu'affiche la page (« Projets associés (n) », bailleurs) :
- bailleurs maquette absents du pivot (BCZS, COPA, Tearfund, AAP, P.F., SPES,
  BDOM, Louvain, USAID, PICAGEL…) — base : 25 partenaires / 33 pivots ;
- `ecole-karhale-kadutu` : statut `ongoing` en base vs « réalisé » maquette ;
- `jeunes-filles-meres-sake` : domaine `genre` (base) vs `protection` (maquette) ;
- `formation-informatique-beni` : projet 2010 maquette sans équivalent seeder ;
- compteur : 38 projets publiés affichés vs les 35 de la maquette (déjà assumé
  dynamiquement, mais l'alignement 35 vs 37/38 reste à valider).

### 5. Tests feature `DomaineIndexTest` (étape 6 du plan)
Aucun test dédié n'existe. À créer (`tests/Feature/DomaineIndexTest.php`) :
- `GET /fr/domaines` et `GET /en/domaines` → **200** ;
- compteurs dynamiques (domaines=9, projets = `Project::published()->count()`) ;
- rendu 100 % base : pas de références `$domaine[` / `$projet[` ; présence des
  traductions FR/EN selon la langue ;
- non-régression JS : présence de `data-r`, `data-n`, `#rl` (scrollspy `oaat.js`).
- Relancer la suite complète (les vues `projets-index`/`projets-show` partagent des
  adaptateurs `funderLabel`/`partnersLabel`).

---

## Ordre de priorité recommandé

1. **Réparer `domaines.show`** (casse depuis l'accueil — le plus urgent).
2. **`$t()` sur les libellés statiques** de `domaines-index` (parcours EN).
3. **Trancher + implémenter Compétences transversales**.
4. **Arbitrages de contenu étape 3** (bailleurs, statuts, domaines, 35 vs 38).
5. **`DomaineIndexTest` + passe complète de non-régression**.