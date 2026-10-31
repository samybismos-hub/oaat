Analyse terminée. Voici l'audit chirurgical complet.

---

# 🔬 Audit — `domaines-index.blade.php` (672 lignes)

## 1. Problème structurel n°1 : cette vue n'est pas intégrée au projet

C'est une **maquette autonome** (doctype + `<head>` + topbar + header + footer inline, l.4-40, 257-344, 586-656), alors que toutes les autres pages (`projets-index`, `organisation`, `contact`…) héritent de `layouts/app.blade.php` et des partials `_topbar`, `_header`, `_footer`. Elle **réinvente** topbar/header/footer en code dupliqué.

## 2. Shadowing fatal : le `$domaines` du contrôleur est écrasé

Le contrôleur charge `Domain::withCount('projects')` et passe `$domaines` (Collection Eloquent). Or la vue définit l.46-188 :
```php
$domaines = [ 'sante' => ['libelle'=…, 'icone'=…, 'interventions'=…], … ];
```
**Elle écrase la Collection** — le modèle, les relations et les traductions ne servent à rien ici.

---

## 3. Inventaire du hardcodé (réel, par bloc)

### Bloc A — Données métier en dur (l.41-254)
| Élément | Lignes | Ce qui existe déjà en base |
|---|---|---|
| 9 domaines : `libelle`, `descriptif`, `interventions` | 46-188 | Modèle `Domain` : `name`, `objectives`, `activities` (translatables FR/EN) ✓ |
| `icone` (path SVG de 9 icônes) | 46-188 | Colonne `icon` (clé texte, ex. `heart-pulse`) ✓ mais pas de path |
| `teinte` (hue dégradé 350/95/30…) | 46-188 | ❌ n'existe nulle part (convention visuelle de la maquette) |
| `formations` (listes par domaine) | 46-188 | ❌ aucune colonne/table (ex. « Normes INEE », « Ateliers SAFE »…) |
| `$rail` (9 étiquettes courtes) | 190-201 | Duplique le nom du domaine (peut se dériver) |
| **35 projets du portefeuille** (année/domaine/titre/bailleur) | 218-254 | ✅ **37 projets réels en base** (`ProjectSeeder`) avec `status`, `domain_id`, partenaires pivot |

### Bloc B — Contenus texte en dur
| Élément | Lignes | Source réelle |
|---|---|---|
| Topbar : adresse, tel `+243 993 537 325`, email | 263-266 | `$settings->addresses/phones/email` (le partial `_topbar` le fait déjà) |
| Logo SVG + nom complet | 277-283 | Partial `_logo_oaat` (existe) + `$settings->organisation_name` |
| Héro : titre, sous-titre, compteurs **9 / 35 / 13** | 359-383 | `Domain::count()`, `Project::count()`, compteur zones réel |
| Libellés sections (« Nos interventions », « Projets associés »…) | 482-504 | Textes statiques `$t()` légitimes |

### Bloc C — Compétences transversales (l.532-562)
5 cartes (Gestion admin/fin, Gestion projet, GPS, Renforcement OSC, Ateliers SAFE) **figées en dur**. Ce sont les « formations reçues » transversales du PDF ≠ formations par domaine. **Aucune structure de données.**

### Bloc D — Footer inline dupliqué
Coordonnées, réseaux sociaux (`href="#"` vides), copyright en dur (l.587-656) → tout ça est dans `_footer` avec `$settings`.

---

## 4. Ce qu'il faut pour finaliser

### a) Rendre la page cohérente avec le reste du site
1. **Convertir** la vue en `@extends('layouts.app')` + `@section('content')` + `@push('scripts')` pour `oaat.js`.
2. **Supprimer** le header/topbar/footer inline (l.4-40, 257-344, 586-656) → apportés par le layout/partials.
3. **Supprimer** `$liensNav` (l.204-214) et le `@php` des 35 projets.
4. **Supprimer le shadowing** : ne plus redéfinir `$domaines` ; utiliser la Collection du contrôleur.

### b) Brancher les données réelles
5. **Domaines** : `@foreach($domaines as $domaine)` → `$domaine->slug`, `getTranslation('name', $locale)`, `activities`, `objectives`.
6. **Icônes** : soit publication d'un `@php` map `slug => path SVG` (comme sur l'accueil `_domaines`), soit upload admin.
7. **`teinte`** : assumer.csses (map slug→hue dans le code, style) — ou ajouter une colonne.
8. **Projets par domaine** : `$domaine->projects` (relation `hasMany` existante) → boucle avec `year`, `status`, `partners`.
9. **Compteurs héro** : `$domaines->count()`, `Project::published()->count()` (≈35-37), `$zonesCount`.
10. **Topbar/footer résaux** : `$settings->socials` (remplace `href="#"`).

### c) Nouvelles structures de données à décider
11. **Formations par domaine + compétences transversales** : aucun stockage n'existe. Deux options :
    - colonne JSON `formations` sur `Domain` (+ champs admin), et table `competences_transversales` (ou JSON dans Settings),
    - ou les assumer comme contenu éditorial d'une section `Page` (`body`) avec un format dédié.
12. **Optimisation contrôleur** : charger `with(['projects' => …published…])` pour éviter N+1.

### d) Statut FR/EN
13. La vue est **unilingue FR**. Si la page doit être bilingue comme les autres, il faut `$t()` partout + traductions réelles des données. À arbitrer avec toi (ce domaine de la maquette était FR-only, les données `Domain` sont elles bilingues).

---

## 5. Verdict / priorisation

Pour « finaliser » proprement, l'ordre que je recommande :
1. **Intégrer la vue au layout** (tuer le doublon header/topbar/footer + le shadowing) — c'est le plus gros gain de qualité, en une passe.
2. **Brancher les domaines sur la base** (le contrôleur fournit déjà tout).
3. **Brancher les projets sur `domain->projects`** (les 37 projets existants remplacent les 35 codés).
4. **Décider** du stockage des `formations`/compétences transversales + icônes/teinte.
5. Ajouter `$t()` si on active le bilingue.

Souhaites-tu que je te détaille l'un de ces points (par ex. le schéma exact de la migration `formations` + `teinte`, ou le mapping icônes), ou qu'on attaque l'étape 1 — l'intégration au layout ?