J'ai maintenant tout le contexte nécessaire pour une feuille de route précise et vérifiée (j'ai re-contrôlé le modèle `Project`, le pivot `ProjectPartner`, le seeder, le layout Tailwind CDN, le formulaire Filament v5 et le namespace `Repeater`). Voici __ce que je vais implémenter__, dans l'ordre.

---

# 🗺️ Feuille de route d'implémentation — Fiche projet

## Rappel des principes validés

- Aucune régression de rendu : les textes actuellement affichés restent __identiques__, mais lus depuis la base au lieu du template.
- Chaque phase est livrable et testable indépendamment. La suite de tests est relancée après chaque phase.
- Le pivot bailleurs (`withPivot('role')` + enum `PartnerRole` + `->value`) est __déjà correct__ : les bailleurs s'affichent bien, pas de correctif nécessaire (point 1.2 de l'audit résolu).

---

## 📘 Phase 1 — Sécurité & robustesse *(aucune migration)*

### T1.1 — Bloquer l'accès public aux brouillons

__Fichier__ : `app/Http/Controllers/Public/ProjetController.php` (l.177-180)

```php
$projet = Project::query()
    ->where('slug', $slug)
    ->with(['domain', 'partners', 'albums.media'])
    ->published()          // ← ajout : non publié → 404
    ->firstOrFail();
```

__Test__ : `test_show_renvoie_404_sur_brouillon` dans `ProjetShowTest` (projet sans `published_at` → 404). Les projets de test existants doivent publier leurs fixtures (à vérifier dans les factories/tests).

### T1.2 — Retirer l'ID interne du hero

__Fichier__ : `resources/views/pages/projets-show.blade.php` (l.138) `Fiche projet n° {{ $projet->id }}` expose l'ID de la base. → suppression de la mention, ou remplacement par un éventuel champ métier « référence » (voir __Décision 1__).

### T1.3 — Compteurs configurables + « Expérience » incluant « En cours »

__Fichier__ : `config/oaat.php` (ajout) + `ProjetController.php` (l.184-200)

```php
// config/oaat.php
'projects' => [
    'experience_count' => 4,
    'related_count'    => 3,
],
```

```php
// contrôleur
->whereIn('status', [
    ProjectStatus::Completed->value,
    ProjectStatus::Ongoing->value,      // ← ajout
])
->take(config('oaat.projects.experience_count')),
```

*(voir __Décision 3__ pour inclure ou non `ongoing`)*

### T1.4 — i18n durée & bénéficiaires

__Fichier__ : `app/Models/Project.php`

- `durationLabel()` : quand les deux dates sont renseignées mais que la durée fait 0 mois → `—` (et non « En cours ») ;
- `beneficiariesLabel()` : nombre formaté selon la locale (`1 234` en FR, `1,234` en EN).

### T1.5 — Logo bailleur en vignette allégée

__Fichier__ : `projets-show.blade.php` (l.445-446) `getFirstMediaUrl('logo','thumb')` (conversion `thumb` déjà déclarée côté `Partner` à vérifier).

---

## 📗 Phase 2 — Contenu éditorial pilotable *(migration — le gros chantier)*

> Objectif : faire disparaître __tous les textes figés__ des sections Contexte / Objectif général / Composantes multiplié par le slug magique `amenagement-des-8-territoires`, et les rendre administrables.

### T2.1 — Migration

__Fichier__ : `database/migrations/2026_10_26_000001_add_editorial_content_to_projects_table.php`

```php
$table->json('contexte')->nullable();         // {'fr':'…','en':'…'}  — 1 à 2 paragraphes
$table->json('objectif_general')->nullable(); // {'fr':'…','en':'…'}
$table->json('composantes')->nullable();      // liste d'objets (voir T2.3)
```

Structure des composantes (clés plates, comme les traductions existantes) :

```json
[{ "nom_fr":"…","nom_en":"…","prevoit_fr":"…","prevoit_en":"…","resultat_fr":"…","resultat_en":"…" }]
```

### T2.2 — Modèle `Project`

- Ajouter `contexte`, `objectif_general`, `composantes` au `#[Fillable]` ;
- `contexte` + `objectif_general` dans `#[Translatable]` ; `composantes` en cast `array` ;
- Nouvelle méthode `hasEditorialContent(): bool` (non vide sur __au moins une langue__) qui __remplace le test du slug__.

### T2.3 — Formulaire Filament (`ProjectForm.php`)

- Ongllets FR / EN existants : ajout d'un `Textarea` « Contexte » et « Objectif général » dans chacun ;
- Nouvelle `Section::make('Composantes')` avec `Filament\Forms\Components\Repeater::make('composantes')` → 6 sous-champs bilingues, `->reorderable()->collapsible()->defaultItems(0)->addActionLabel('Ajouter une composante')`. (Namespace `Repeater` confirmé dans `vendor/filament/forms/src/Components/Repeater.php`.)

### T2.4 — Seeder

L'entrée `amenagement-des-8-territoires` du `ProjectSeeder` (l.360) reçoit les 2 paragraphes de contexte, l'objectif général et les 4 composantes __tels qu'affichés aujourd'hui__ → rendu strictement identique après refactor, en dev comme en recette. *(En production : une saisie via Filament, pas de script.)*

### T2.5 — Vue

- l.94 : `$editorial = $projet->hasEditorialContent();`
- `#contexte` (l.311-325) : boucle sur les paragraphes issus de `contexte` ;
- `#composantes` (l.328-393) : boucle sur `composantes`, objectif général issu de la base ; __structure HTML conservée telle quelle__.

### T2.6 — Tests

- `test_sections_editoriales_rendues_depuis_la_base` (projet avec composantes → tableau FR/EN rendu) ;
- Le test existant « sections masquées pour les autres projets » __doit continuer à passer__ (aucun éditorial en base pour eux).

---

## 📙 Phase 3 — Dés-hardcodage des textes & Settings *(migration)*

### T3.1/T3.2 — Interlocuteur + crédit photo paramétrables

__Migration__ `settings` : `contact_person_fr`, `contact_person_en`, `contact_role_fr`, `contact_role_en`, `photo_credit` (JSON, nullable). __Fichiers__ : `Setting.php` (Fillable/casts) + __`SettingForm.php` existant__ (nouvelle section « Affichage des fiches projet »). __Vue__ : interlocuteur `#soutenir` (l.630) affiché __seulement si renseigné__ ; figcaptions hero (l.169) et galerie (~l.577) : `$settings?->photo_credit` avec fallback traduit « Crédit : OAAT ».

### T3.3 — « Rejoindre le partenariat » : du mailto vers le formulaire

__Fichier__ : l.479-490 → lien `route('contact')` avec `?objet=p` pré-sélectionné cohérent avec les 3 cartes « Soutenir » (voir __Décision 4__).

### T3.4 — Email de secours dans la config

__Fichiers__ : `config/oaat.php` (`'contact_email' => 'oaatrdc2000@gmail.com'`) + vue (l.82) `config('oaat.contact_email')`.

### T3.5 — Token couleur `ochre-dark`

__Fichier__ : `layouts/app.blade.php` (l.26, config Tailwind CDN) : `ochreDark: '#7A5200'` → remplacement de `text-[#7A5200]` (l.144 et 654) par `text-ochre-dark`.

### T3.6 — Factorisation du logo SVG OAAT

__Fichier__ : nouveau `partials/_svg_oaat.blade.php` (rect `#17508F` + path ochre), puis remplacement des __3 occurrences__ : `projets-show` l.466-469, `_header` l.20-21, `_footer` l.44-45.

### T3.7 — Harmonisation du libellé de statut

`statusLabel()` (« Recherche de financement ») vs carte de l'index (« En attente de financement ») : aligner les deux sur __un seul wording__ (voir __Décision 5__).

### T3.8 — Tests

ContactPageTest inchangé + test « interlocuteur masqué si vide ».

---

## 📕 Phase 4 — Nettoyage transverse *(bonus, sans migration)*

- __T4.1__ — `public/js/oaat.js` : retirer la branche morte `[data-href-fr]` (§22) ;
- __T4.2__ — Traiter les `data-en` __restants__ (`projets-index`, `_carte_projet`) : les traductions EN de l'index sont actuellement cassées — basculer sur `$t()` comme fait pour la fiche ;
- __T4.3__ — SEO : `<link rel="canonical">` + OG de base + fallback de `meta_description` (reprendre `$resultats` puis le titre) ;
- __T4.4__ — Harmoniser l'offset de scroll via une variable `--scroll-offset` (optionnel).

---

## 🚦 Critère d'acceptation global

`php artisan test` vert (dont `ProjetShowTest`), rendu FR/EN manuel sur Laragon conforme à la maquette pour le projet « 8 territoires », et fiches « non emblématiques » sans sections vides.

## ⚖️ Décisions à trancher

1. __N° de fiche__ (T1.2) : retirer la mention (simple) *ou* créer une vraie référence métier ?
2. __Stockage éditorial__ (T2.1) : colonnes JSON sur `projects` — ma recommandation, cohérent avec l'existant (`title`, `objectives`…) *vs* table `project_sections` (plus évolutive mais plus lourde) ?
3. __« Expérience comparable »__ (T1.3) : inclure les projets __En cours__ en plus des Réalisés ?
4. __« Rejoindre le partenariat »__ (T3.3) : basculer sur `contact?objet=p` (traçable, cohérent) *vs* conserver le `mailto:` ?
5. __Wording statut__ (T3.7) : « Recherche de financement » *ou* « En attente de financement » partout ?
