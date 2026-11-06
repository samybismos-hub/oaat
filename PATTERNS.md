# PATTERNS — Conventions du projet OAAT

> **À consulter avant toute implémentation.** Ce fichier est la référence des
> conventions de code du site public. Tout écart doit être signalé et documenté.

---

## 1. Stack & outils

| Élément | Version / Choix | Notes |
|---|---|---|
| PHP | `^8.3` | `number_format` localisé, attributs PHP (`#[...]`), enums strictes |
| Laravel | `^13.17` | — |
| Filament | `^5.8` | fiche d'admin par modèle ; enums implémentant `HasLabel`/`HasColor` |
| spatie/laravel-translatable | `^6.14` | colonnes JSON `*_translatable` |
| spatie/laravel-medialibrary | `^11.23` | conversions `nonQueued()` |
| Front | Tailwind (maquette, sans framework JS) | rendu **100 % serveur** |
| Tests | PHPUnit `^12` (`php artisan test`) | env `testing` → SQLite |

**Dossiers de travail** : `app/Models`, `app/Http/Controllers/Public`,
`app/Enums`, `resources/views/pages`, `resources/views/partials`,
`config/oaat.php`, `database/seeders`, `database/migrations`, `tests/Feature`.

---

## 2. Architecture générale — le trinôme

**1. Contrôleur = préparateur de données.** Toute la logique métier vit dans le
contrôleur (ou le modèle) : filtres, tris, comptages, calculs de périodes,
préparation de tableaux prêts à afficher. La vue ne calcule rien.

**2. Vue `pages/*` = carte de visite.** Une page `pages/*.blade.php` est un
assemblage d'`@include('partials._xxx', [...])`, un bloc SEO, et rien de plus.
Exemple canonique : `pages/home.blade.php` (8 partials).

**3. Partials = briques réutilisables** :
`_hero_simple`, `_hero_carrousel`, `_chiffres`, `_about`, `_domaines`,
`_projets`, `_actualites`, `_partenaires`, `_carte_projet`, `_footer`,
`_header`, `_topbar`.

**Règle d'or** : si une vue dépasse ~80 lignes, c'est qu'elle contient de la
logique ou qu'il manque des partials. (Exception documentée :
`pages/domaines-index`, §10.)

---

## 3. Bilingualisme & routage

### Routeur — `routes/web.php`

```php
Route::group(
    [
        'prefix' => '{locale?}',
        'where' => ['locale' => '(fr|en)'],
        'middleware' => [SetLocale::class],
    ],
    function () { /* toutes les routes */ },
);
```

- Toutes les routes sont préfixées `{locale?}`. La racine `/` redirige vers
  `/fr/` via le middleware `SetLocale`.
- **Noms de routes** : `accueil`, `organisation`, `domaines.index`,
  `domaines.show`, `projets.index`, `projets.show`, `actualites.index`,
  `actualites.show`, `documents.index`, `galerie.index`, `galerie.show`,
  `partenaires`, `contact`, `contact.send`, `besoin`, `besoin.send`.

### Middleware — `app/Http/Middleware/SetLocale.php`

```php
$locale = Str::lower($request->route('locale') ?? 'fr');
if (! in_array($locale, ['fr', 'en'])) { $locale = 'fr'; }
App::setLocale($locale);
view()->share('locale', $locale);
```

Il **partage** `$locale` avec toutes les vues. Les vues le re-définissent
quand même en tête (ordre d'évaluation des sections Blade).

### Signature d'un contrôleur public

Le contrôleur reçoit **toujours** `string $locale` en premier paramètre :

```php
public function index(string $locale): mixed
```

---

## 4. Pattern de la vue — en-tête & assemblage

### Les 4 lignes obligatoires en tête de vue

```blade
{{-- pages/xxx.blade.php — description courte + partial(s) utilisé(s) + variables
     Disponibles (depuis XxxController) : $page, $stats, $locale … --}}
@php
    $locale ??= 'fr';
    $t = fn($fr, $en) => $locale === 'en' ? $en : $fr;
@endphp
@extends('layouts.app')
```

Le commentaire d'en-tête documente le partial consommé et les variables reçues
(pattern visible dans `home`, `_carte_projet`, etc.).

### SEO — sections du layout

```blade
@section('meta_title')
    {{ $page?->seoTitle($locale) ?: 'Fallback' }}
@endsection

@section('meta_description')
    {{ $page?->seoDescription($locale) }}
@endsection
```

Puis `@section('content') … @stop`, et éventuellement `@push('head')` /
`@push('scripts')` pour les ajouts spécifiques.

### Assemblage de partials

```blade
@include('partials._hero_simple', [
    'page'   => $pageProjets,
    'stats'  => [
        ['valeur' => $compteurs['realises'] ?? 0, 'fr' => '…', 'en' => '…'],
    ],
    'locale' => $locale,
])
```

---

## 5. `$t` et traduction inline

Partout dans les vues : `$t('Français', 'English')`. Pour le texte éditorial
stocké en base (traduit), utiliser le modèle avec **repli sur le français**
quand la clé de la langue est vide :

```php
$value = $model->getTranslation('name', $locale) ?: $model->getTranslation('name', 'fr') ?: '';
```

---

## 6. Modèles — conventions

### Attributs PHP

```php
#[Fillable(['slug', 'title', 'body', …'])]
#[Translatable('title', 'body', 'meta_title', '…')]
class Page extends Model implements HasMedia
{
    use HasTranslations /*, HasFactory, InteractsWithMedia */;

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,   // enum
            'is_featured' => 'boolean',
            'start_date' => 'date',
            'budget_amount' => 'decimal:2',
        ];
    }
}
```

### `getRouteKeyName()`

Toujours `'slug'` pour les entités publiques (Domain, Page, Project…).

### Scope `published()`

```php
public function scopePublished(Builder $query): Builder
{
    return $query->whereNotNull('published_at')->where('published_at', '<=', now());
}
```

### Media (Spatie)

```php
public function registerMediaCollections(): void
{
    $this->addMediaCollection('hero')->singleFile()
         ->useDisk('public')->storeConversionsOnDisk('public');
}
public function registerMediaConversions(?Media $media = null): void
{
    $this->addMediaConversion('banner')->width(1920)->height(800)->nonQueued();
}
```

**Toujours `useDisk('public')`** : le disk par défaut `local` casse l'affichage
public (cf. commentaire dans `Page.php`).

### Médias galerie (Album)

`Album` expose la collection **`photos`** (disque `public`) avec deux
conversions `nonQueued()` : `thumb` (200×150) et `card` (800×600). C'est le
contrat d'image de la page galerie (à venir) — les vues n'utiliseront que ces
deux conversions. Relation `belongsTo(Project)`.

---

## 7. Les libellés d'affichage vivent dans le modèle

**Jamais de `match` / condition de statut dans la vue.** Ce sont des méthodes
de modèle (ex. `app/Models/Project.php`) :

- `year(): ?int` — année d'affichage (`start_date` sinon `end_date`)
- `statutCourt` (`getStatutCourtAttribute`) — `d` = `completed`/`ongoing`,
  `w` = `awaiting_funding`/`planned` (familles de la maquette)
- `statusLabel(?string $locale)` — FR/EN
- `durationLabel(?string $locale)`, `durationParts(): ?array`
- `budgetLabel(?string $locale)`, `beneficiariesLabel(?string $locale)`
- `partnersLabel(?string $locale)`, `funderLabel(?string $locale)`,
  `implementationPartnersLabel(?string $locale)`
- `relatedTo(...)` — statique, paliers de pertinence

Les **bailleurs / partenaires** s'affichent **sans filtre « OU »** : les deux
groupes (`funder` et `implementer`) apparaissent chacun dans leur case. Le rôle
est stocké dans le **pivot** `project_partner.role` (enum `PartnerRole`).

---

## 8. Enums

Implémentent **les deux** contrats Filament (`HasLabel`, `HasColor`) :

```php
enum ProjectStatus: string implements HasColor, HasLabel
{
    case Planned = 'planned';
    // …

    public function getLabel(): string { return match ($this) { … }; }
    public function getColor(): string { return match ($this) { … }; }
}
```

Les rôles partenaires (`funder`, `implementer`) sont typés **dans la classe de
Pivot** :

```php
class ProjectPartner extends Pivot
{
    protected function casts(): array { return ['role' => PartnerRole::class]; }
}

// Relation dans Project :
public function partners(): BelongsToMany
{
    return $this->belongsToMany(Partner::class, 'project_partner')
        ->using(ProjectPartner::class)
        ->withTimestamps()
        ->withPivot('role');
}
```

---

## 9. Les compteurs — toujours en SQL dans le contrôleur

Jamais `count()` dans la vue. Le contrôleur fournit un tableau `$compteurs` :

```php
$compteurs = [
    'domaines' => Domain::query()->count(),
    'projets'  => Project::query()->published()->count(),
    'zones'    => Project::query()->published()
                    ->whereNotNull('city')->where('city', '!=', '')
                    ->distinct()->count('city'),
];
```

Exceptions héritées de la maquette : `$yearsOfActivity` (calculé depuis la
date de fondation `config('oaat.founded_at')`) et `stat_zones` du `Setting` —
à éviter pour du neuf.

---

## 10. Héro et partials partagés

### `_hero_simple` (pages internes)

Variables : `$page` (Page CMS), `$crumb` (libellé du fil d'Ariane), `$stats`
(`[['valeur' => int, 'fr' => ..., 'en' => ...], ...]`), `$locale`, `$t`.
Le fond = image `hero[0]` (conversion `banner`), le titre = `title`, le
sous-titre = extrait du `body`. **100 % CMS.** Utilisé par `projets-index`,
`organisation` et toutes les pages internes.

### `_hero_carrousel` (uniquement l'accueil)

3 diapos, page `accueil`.

### `_carte_projet` (la carte projet canonique)

Carte **100 % textuelle** (plus aucune image ni SVG — décision du 03/11/2026) :
le badge de statut (« À financer / En cours / Réalisé ») vit dans la ligne meta
(année + domaine), en tête de contenu.

Variables documentées en tête : `$projet` (avec `domain` et `partners`
chargés), `$locale`, `$t`. 6 cellules fixes (`Budget`, `Durée`,
`Bénéficiaires`, `Bailleurs`, `Partenaires de mise en œuvre`, `Statut`) —
chacune = un **label de modèle**, valeur « — » si absente. Bouton →
`projets.show`.

### `pages/domaines-index` — la page hub (référence)

Page longue **assumée** (9 sections de domaine assemblées inline) : toute la
logique vit dans `DomaineController@index` (structure `$sections` prête à
afficher, compteurs en SQL — §9). La vue ne garde que des helpers de
présentation lisant la config :

```php
$railLabel = fn($d) => config('oaat.domaine_page.rail.'.$d->slug) ?: …;
$icone     = fn($d) => (string) config('oaat.domaine_page.icons.'.$d->slug);
$teinte    = fn($d) => (int) config('oaat.domaine_page.tints.'.$d->slug);
```

Conventions posées par cette page (à réutiliser) :

- **Illustration de section = fond CSS + vrai HTML, jamais de texte dans un
  SVG.** Le bandeau est un `div.relative` avec `background-image:
  linear-gradient(to bottom right, hsl(teinte 45% 26%), hsl(teinte 50% 44%))`
  (les 2 teintes de la config) ; l'objectif est un `<p>` HTML par-dessus
  (`relative max-w-*`) → retour à la ligne automatique à chaque breakpoint.
  Décoration éventuelle = SVG `aria-hidden` + `pointer-events-none`, sans texte.
- **Champ éventuellement vide** : `@if ($sec->objectives !== '')` (le contrôleur
  renvoie `''` si absent) — repli possible sur `$sec->intro`.
- **Lien hub → fiche** : CTA dans la colonne gauche **sticky**
  (`lg:sticky lg:top-40`), en bas du résumé →
  `route('domaines.show', [$locale, $sec->domaine->slug])`. Bouton `bg-lake`
  pleine largeur, `aria-label` incluant `$sec->nom` (9 liens visibles
  identiques → nom accessible distinct).

---

## 11. Page CMS & SEO — `app/Models/Page.php`

- `SYSTEM_SLUGS = ['accueil', 'organisation', 'domaines']` → suppression
  interdite (`isSystem()` + écouteur `deleting` dans `booted()`).
- `seoTitle(string $locale)`, `seoDescription(string $locale)` — replis sur
  `title` / extrait du `body` (HTML strippé, limité à 155).
- Helpers par bloc éditorial (`timelineFor`, `recognitionsFor`, `zonesFor`,
  `competencesFor`) : **repli FR** + retour d'un tableau typé :

```php
$values = $this->getTranslation('competences', $locale) ?? [];
if ($values === [] || ! is_array($values)) {
    $values = $this->getTranslation('competences', 'fr') ?? [];
}
return array_values(array_filter(is_array($values) ? $values : [],
    fn ($item) => is_array($item)));
```

- Colonnes éditoriales : `title`, `body`, `meta_title`, `meta_description`,
  `timeline`, `recognitions`, `zones`, `competences` (Repeaters Filament).
- Les contrôleurs récupèrent la page par slug :
  `Page::query()->where('slug', 'domaines')->first()`.

---

## 12. Config `config/oaat.php`

Ce qui est **figé par le design** va dans la config ; ce qui est **éditorial**
va en base. Exemples présents :

```php
'projects' => [
    'experience_count' => 4,   // blocs « Expérience comparable »
    'related_count'    => 3,   // et « Autres projets » de la fiche projet
],
'domaine_page' => [
    'tints' => [/* teinte HSL par slug de domaine */],
    'rail'  => [/* libellés courts de la barre de navigation interne */],
    'icons' => [/* SVG 24×24 de chaque domaine */],
],
```

Décision du 31/10/2026 (Option B) : **teintes, icônes et libellés courts de la
page domaines = données de config, pas de base.**

---

## 13. Seeders — ordre et dépendances

`database/seeders/DatabaseSeeder.php` exécute dans l'ordre des dépendances :
settings → pages → membres → **partenaires → domaines → projets** (pivot
`project_partner`) → actualités → documents → albums → admin. C'est un jeu de
démo **remis en état à chaque `migrate:fresh --seed`** — jamais de donnée
éditoriale « à la main » en base de production.

`AlbumSeeder` est **idempotent** : il ne crée rien si un album existe déjà
(slug unique `album-de-demonstration`).

---

## 14. Tests — patterns

### Réaliste (page HTTP) — `tests/Feature/ContactPageTest.php`

```php
$this->get('/fr/contact?objet=f')->assertOk()->getContent();
$this->assertStringContainsString('value="f" selected', $fr);
```

### Sans vue (mock du contrat `View`) — `tests/Feature/ProjetIndexTest.php`

Quand on teste la **logique du contrôleur** sans que la vue n'existe (ou sans
l'afficher), on mocke `Illuminate\Contracts\View\Factory` pour capturer les
données passées au rendu :

```php
$captured = null;
$this->mock(ViewFactory::class, function ($mock) use (&$captured) {
    $mock->shouldReceive('make')
        ->with('pages.projets-index', Mockery::capture($captured), Mockery::any())
        ->andReturn($this->fakeView());
    $mock->shouldReceive('share')->andReturnNull();   // SetLocale
});
$this->get('/fr/projets')->assertOk();
```

Fichiers types : `tests/Feature/*.php`, un test par page / par ressource
Filament (`*ResourceTest`). Seuil : chaque nouvelle page a son test.

---

## 15. Contrôleurs — squelette type

```php
namespace App\Http\Controllers\Public;

class XxxController
{
    public function index(string $locale): mixed
    {
        // 1. Données de base (Settings, Page CMS, collections)
        $settings = Setting::current();
        $page = Page::query()->where('slug', 'xxx')->first();

        // 2. Logique (filtres, tris, comptages, tableaux annotés)
        //    — jamais dans la vue —

        // 3. Rendu avec toutes les variables prêtes
        return view('pages.xxx', compact('…', 'locale'));
    }
}
```

`Setting::current()` = premier enregistrement (pattern singleton).

---

## 16. Dettes & pièges connus

- **`pages/domaines-show` n'existe pas — saut volontaire** (décision du
  03/11/2026, la galerie passe devant). La page `domaines-index` y pointe
  pourtant (9 CTA « Voir la page du domaine ») ainsi que les 9 cartes de
  l'accueil (`_domaines.blade.php:48`) → **clic = 500**. Dette assumée : créer
  la fiche ou neutraliser les liens.
- `pages/partenaires` **n'existe pas** (route câblée, item du menu header) →
  clic = **500**. À créer ou désactiver.
- **Galerie : vues absentes.** Routes (`galerie.index`, `galerie.show`),
  `GalerieController`, modèle `Album` (médias OK) et `AlbumResource` (Filament)
  existent, mais `pages/galerie-index` / `pages/galerie-show` manquent →
  clic = **500**. Prochaine étape d'implémentation.
- Commentaire d'en-tête de `_carte_projet` obsolète : mentionne encore
  `$gradHsl` (supprimé avec la zone image) → à nettoyer.
- L'ordre de lecture : **toujours lire ce fichier + le commentaire d'en-tête
  du partial ciblé + le contrôleur qui le nourrit** avant d'écrire du code.