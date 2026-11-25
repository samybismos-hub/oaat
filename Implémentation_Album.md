# Implémentation — Page Galerie / Médiathèque (« Album »)

> Mémoire de travail pour **toute** l'implémentation de la page Album (menu « Galerie »).
> À lire avec `PATTERNS.md` avant toute écriture.

## 0. État d'avancement

| Étape | Contenu | Statut |
|---|---|---|
| **1. Migration JS** | `Galerie.html` → `public/js/oaat.js` §23 | ✅ fait |
| 2. PHP | `GalerieController@index()` via `AlbumMediaPresenter` | ✅ fait (14/12/2026) |
| 3. Vue `pages/galerie-index` | DOM conforme au **§3** | ⏳ à faire |
| 4. Tests | Données `data-*`, hooks JS | ⏳ à faire |

## 1. Stratégie globale

1. **Le JS ne crée aucun HTML** : il filtre/trie/positionne les `<figure class="ph">` servies par PHP.
2. **Tout calculable est rendu en PHP** : `data-*`, facettes, onglets, chips, `$t()`.
3. **Langue NON gérée en JS** : routes `/fr` ↔ `/en` + `$t()`. Ne pas migrer `tr()`, `setL()`, `TT`.
4. Tout hook DOM ajouté côté PHP doit être documenté au **§3**.

## 2. Inventaire JS maquette (l.36-245)
:
### 2.1 Déjà dans `oaat.js`

| Fonction | Maquette | oaat.js |
|---|---|---|
| `$` / `$$` | 36 | en-tête |
| `toast` | 40 | §2 |
| Menu mobile | 42-43 | §3 |
| Méga menu | 44-46 | §16 |
| Compteurs/révélations | 48-53 | §5 |
| Défilement + scrollspy | 54-55 | §6 |

### 2.2 Migrées dans §23 de `oaat.js`

| Fonction | Maquette | §23 |
|---|---|---|
| Normalisation `nz` | 61 | 23.1 |
| Référence `OAAT-xxxx` | 62 | 23.1 |
| Catalogue `IT` depuis data-* | 72 | 23.1 |
| État `S`, facettes `SETS` | 75-77 | 23.1 |
| Matcher `m(it,skip)` | 78-83 | 23.1 |
| Tris + `setSort` | 86-87 | 23.2 |
| Layout + ResizeObserver | 89-104 | 23.3 |
| `updFacets` | 106-119 | 23.4 |
| Sélection + `#tray` | 122-125 | 23.5 |
| `syncUrl` URL partageable | 127, 234-238 | 23.6 |
| `refresh` / `clearAll` | 128-137 | 23.6 |
| Commandes facettes | 139-153 | 23.7 |
| Tiroir mobile | 154-155 | 23.7 |
| **Visionneuse** `#vw` | 166-213 | 23.9 |
| Clavier global | 215-232 | 23.10 |
| Lien direct `#gN` | 240, 245 | 23.11 |

## 3. Contrat DOM

### 3.1 Figure du catalogue

```html
<figure id="g1" class="ph" data-id="1" data-y="2024" data-w="900" data-h="600"
        data-cat="projets" data-th="educ" data-lieu="sk" data-ori="l"
        data-par="beting" data-col="phares" data-use="projets accueil"
        data-tf="Titre FR" data-te="Title EN"
        data-pfr="Lieu FR" data-pen="Place EN"
        data-tags="ecole~school" data-dl="OAAT-0001.jpg"
        data-q="mots normalises sans accents oaat-0001">
```

### 3.2 IDs requis

| Groupe | Éléments |
|---|---|
| Barre d'outils | `#fbtn`, `#q`, `#dens`, `#so`, `.vb[data-v]`, `#rc` |
| Facettes + années | `#fb`, `#y0`, `#y1`, `#yr` |
| Onglets | `#tabs` + `.tb[data-c]` |
| Grille | `#gr` + `figure.ph` |
| Sélection | `#tray`, `#treq` |
| Visionneuse | `#vw`, `#vfl` + `.fs[data-id]` |

## 4. Notes de migration

- **Langue** : pas de bascule client (routes + `$t()`).
- **`data-id` stable** : clé du lien direct `#gN` et du panier `oaat-sel`.
- **localStorage** : clés `oaat-sel`, `oaat-view`, `oaat-dens`.
- **Isolation** : §23 s'active si `#gr` + `figure.ph` existent.

---

## 5. Étape 3 — Logique métier

### 5.1 État des lieux fonctionnel

| Pièce | Statut |
|---|---|
| `Album` (modèle) | ✅ title traduisible, collection photos |
| `Media` (vendor) | ✅ `custom_properties`, conversions thumb/card |
| `GalerieController@index()` | ✅ Refactorisé (14/12/2026) |
| `pages/galerie-index.blade.php` | ✅ Plus de `$qs` |
| `_galerie_hero.blade.php` | ✅ Nettoyé (plus de recherches fréquentes) |
| `AlbumMediaPresenter` | ✅ Nouveau (extrait buildMediaHash) |
| `config('oaat.galerie.collection_palette')` | ✅ Nouveau |
| `Route GET /galerie` | ✅ Pointe sur GalerieController |
| `oaat.js` §23 | ✅ Filtre, trie, visionneuse |
| `AlbumSeeder` | ⚠️ 1 album vide → à enrichir |
| `AlbumResource` (Filament) | ✅ Upload photos, sans métadonnées |

### 5.2 Dettes

- **D1** : `custom_properties` vides → `<figure>` sans `data-*`. Emplacement retenu : colonne JSON `media.custom_properties`.
- **D2** : Pas d'UI Filament pour les métadonnées (hors périmètre).
- **D3** : Refactorisé : `AlbumMediaPresenter::toHash()` extrait du contrôleur.
- **D4** : `Setting::current()` transmis comme les autres contrôleurs publics.
- **D5** : Seeder sans photo → impossible de démontrer facettes/visionneuse.

### 5.3 Architecture (après refactoring 14/12/2026)

```
GalerieController@index
  → Setting::current()
  → Album::with(['media', 'project.domain'])->get()
  → flatMap AlbumMediaPresenter::toHash($media, $album, $locale)
  → groupBy('cat')          → $categories
  → buildFacetteGroup(x3)   → $facettes
  → buildCollections()      → $collections (palette config)
  → buildAnnees()           → $annees (histogramme)
  → compteurs bruts         → $stats
  → unique(tags)            → $tags
  → view('pages.galerie-index', compact(...))
```

**Nouveaux fichiers :**
- `app/Models/AlbumMediaPresenter.php` — hash média (24 clés)
- `config/oaat.php` section `galerie.collection_palette`

**Décisions d'architecture :**

| Question | Décision |
|---|---|
| Transformation hash média | `AlbumMediaPresenter::toHash()` |
| Palette collections | `config('oaat.galerie.collection_palette')` |
| Bilinguisme | Uniquement dans les vues via `$t()` |
| Labels facettes | Slugs bruts (`fr`/`en` = code) |
| Recherches fréquentes | **Supprimées** |
| Cache | Pas pour le moment |
| Métadonnées photo | `custom_properties` (JSON) |
| Admin métadonnées | Hors périmètre |
| `$settings` | Transmis |
| URL images | `getUrl('thumb')`, `getUrl('card')`, `getUrl()` |
| Référence | `OAAT-` + `media.id` sur 4 chiffres |

### 5.4 Programme restant

1. **`AlbumSeeder`** — photos + `custom_properties`
2. **`GalerieIndexTest`** — validation des données
3. **`PATTERNS.md` §16** — dettes D1→D5
