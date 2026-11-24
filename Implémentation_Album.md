# Implémentation — Page Galerie / Médiathèque (« Album »)

> Mémoire de travail pour **toute** l'implémentation de la page Album (menu « Galerie »).
> Maquette : `Galerie.html` à la racine du projet — JS énorme (lignes 36-245).
> À lire avec `PATTERNS.md` avant toute écriture.

## 0. État d'avancement

| Étape | Contenu | Statut |
|---|---|---|
| **1. Migration JS** | Analyse chirurgicale du JS de `Galerie.html` + migration dans `public/js/oaat.js` (**§23**), sans duplication avec l'existant | ✅ fait |
| 2. PHP | `GalerieController@index()` : catalogue, facettes, compteurs, libellés i18n, références | ⏳ à faire |
| 3. Vue `pages/galerie-index` | Produire le DOM conforme au **§3** de ce fichier | ⏳ à faire |
| 4. Tests | Test de rendu : hooks JS présents (`#gr`, `figure.ph`, `#vw`…), données `data-*` | ⏳ à faire |

Les étapes 2-4 doivent **rester en cohérence avec `oaat.js` §23** : ni duplication PHP↔JS, ni hook DOM non documenté.

## 1. Stratégie globale (règles posées par la maquette)

1. **Le JS ne crée aucun HTML** (commentaire maquette l.57-58) : il filtre, trie et positionne des `<figure class="ph">` **déjà servies** par le rendu PHP. Pour ajouter une image : dupliquer une figure (et sa miniature `.fs`) avec ses `data-*`.
2. **Tout ce qui est calculable est rendu en PHP** : figures + `data-*`, facettes (`input[data-f]` + compteurs `.cnt`), onglets `.tb`, chips `.chip`, libellés localisés (`$t()`), email (`data-mailto` sur `<body>`).
3. **Langue NON gérée en JS** : la maquette bascule sa langue toute seule (`data-en`, localStorage `oaat-lang`) ; dans l'app c'est fait par les routes `/fr` ↔ `/en` et `$t()`. **Ne pas migrer** `tr()`, `setL()`, `TT` de la maquette.
4. Tout hook DOM ajouté côté PHP doit être documenté au **§3** pour ne pas casser §23.

## 2. Inventaire des fonctionnalités du JS de la maquette (l.36-245)

### 2.1 Déjà dans `oaat.js` → réutilisées (aucune duplication)

| Fonction maquette | Lignes | oaat.js | Note |
|---|---|---|---|
| `$` / `$$` | 36 | en-tête | identiques |
| `rm` (reduced-motion) | 37 | en-tête | identique |
| `toast` | 40 | §2 | idem (+ garde null) |
| `L()` traduction | 39 | §2 helper | oaat.js lit `<html lang>` (mieux que localStorage) |
| Menu mobile `#bt/#mn/#mnav` | 42-43 | §3 | identique |
| Méga menu `#mg` + `#nav a:nth-child(3)` | 44-46 | §16 | identique |
| Liens sociaux `.sc` → toast | 47 | §21 | délégation (plus robuste) |
| Compteurs `[data-n]`, révélations `[data-r]/[data-w]`, barres `h2` | 48-53 | §5 | mêmes attributs → **ne pas ré-implémenter** |
| Défilement `#pg/#hd/#hi/#rg/#up` + scrollspy | 54-55 | §6 | identique |

### 2.2 Migrées dans `oaat.js` — section 23 « Page galerie — Médiathèque »

| Fonction | Maquette | §23 | Notes |
|---|---|---|---|
| Normalisation recherche `nz` | 61 | 23.1 | minuscules + NFD sans diacritiques |
| Référence `refc` → `OAAT-0001` | 62 | 23.1 | `'OAAT-'+padStart(4)` |
| Catalogue `IT` depuis `figure.ph`/`data-*` | 72 | 23.1 | mapping d'attributs | §3 |
| État `S`, facettes `SETS`, `facetPick` | 75-77 | 23.1 | localStorage : `oaat-sel`, `oaat-view`, `oaat-dens` |
| Matcher `m(it,skip)` | 78-83 | 23.1 | mots = tous présents (ET), années |
| Tris `rec/old/az/rnd` + `setSort` | 86-87 | 23.2 | `az` localisé |
| Layout mosaïque/grille/liste + ResizeObserver | 89-104 | 23.3 | hauteur grille calculée |
| `updFacets` (cases, onglets, années, chips, `#fn`) | 106-119 | 23.4 | compteurs = JS |
| `updCtl` (`#so`, `#dens`, `.vb`) | 120 | 23.4 | |
| Sélection `sel` persistée, `#tray`, `#tall/#tinv/#tclr/#tcp`, `#treq` | 122-125 | 23.5 | email = `data-mailto` |
| `syncUrl` URL partageable + init URL | 127, 234-238 | 23.6, 23.11 | `q,c,th,lieu,par,col,ori,use,y,s,v` + ancre `#gN` |
| `refresh` / `clearAll` (`#em`, `#rc`, `#more`, `#pg2`) | 128-137 | 23.6 | |
| Commandes facettes/recherche/tri/vues/densité/`#snt` | 139-153 | 23.7 | |
| Tiroir mobile facettes (`#fbtn,#fb,#fbk,#fclose`) | 154-155 | 23.7 | `fDrawer` |
| Clic grille `.sel` / `.open` | 156 | 23.7 | |
| Aide clavier `#help` (`#hlp, #hx`) | 163-164 | 23.8 | |
| **Visionneuse** `#vw` (zoom, pan, pincement, diaporama, plein écran, partage, similarité, tags, filmstrip, piège Tab) | 166-213 | 23.9 | cf. §3.3 |
| Clavier global (`/`, `?`, `g`, `f`, `Échap`, `+/-/0`, espace, `i/s/c`, flèches) | 215-232 | 23.10 | |
| Lien direct `#gN` (hash history) | 240, 245 | 23.11 | |

Raccourcis : `/` recherche, `?` aide, `G` vue, `F` filtres, `Échap` ferme, `+/−/0` zoom, espace diaporama, `i` infos, `s` sélection, `c` copier lien, `f` plein écran, `←/→` photo. **Désactivés quand on tape** (`INPUT`≠range, `TEXTAREA`, `SELECT`).

### 2.3 À faire en PHP (server-side) — le JS ne s'en occupe PAS

| Donnée | Source / contenu | À produire dans |
|---|---|---|
| Liste des médias | Modèle `Album` + collection `photos` (conversions `thumb`/`card`, cf. PATTERNS §6) | vue `pages/galerie-index` |
| `data-*` par figure (§3.1) | données média + traductions `$t()` | vue |
| Facettes `input[data-f]` + libellé `span.grow` + compteur `.cnt` initial | facettes déduites du catalogue | vue |
| Onglets `.tb` (Tout + catégories) + compteurs initiaux | catégories présentes dans le catalogue | vue |
| Années `#y0/#y1` (range), `#y0t/#y1t`, rail `#yr`, décennies `.yb` | min/max des années des médias | vue |
| Chips `.chip[data-f][data-v]` + `#cclr` | valeurs possibles par facette | vue |
| `[data-qs]` recherches fréquentes | statique / config | vue |
| Références `OAAT-00xx` | `data-id` 1..N servis par le serveur (le JS fait `refc`) | vue |
| Email de contact | `<body data-mailto="{settings.email}">` | Layout ou vue |
| Libellés visionneuse `#vm1-10`, `.vtg`, `#vsrc/#vsrt/#vd/#vq` | données + traductions | vue |
| `data-src`/`data-sfr`/`data-sen` (lien source de l'image) | page/ressource liée | vue |
| `data-dl` (nom de fichier au téléchargement) | nom média ex. `OAAT-0001.jpg` (défaut JS : `.svg`, hérité de la maquette) | vue |
| `#cpi` `<input class="sr-only" readonly tabindex="-1">` | toujours présent en fin de page (presse-papiers) | vue |

## 3. Contrat DOM (ce que la vue doit produire pour oaat.js §23)

### 3.1 Figure du catalogue (une par média, dans `#gr`)

```html
<figure id="g1" class="ph group/f mb-3 break-inside-avoid overflow-hidden bg-mist"
        data-id="1" data-y="2024" data-w="900" data-h="600"
        data-cat="projets"            <!-- projets|partenariats|evenements|institution|terrain -->
        data-th="educ" data-lieu="sk" data-ori="l"
        data-par="beting"             <!-- '|' séparés si plusieurs -->
        data-col="phares" data-use="projets accueil"   <!-- plusieurs = espaces -->
        data-tf="Titre FR" data-te="Title EN"
        data-pfr="Lieu FR" data-pen="Place EN"
        data-src="projets.show?..." data-sfr="Voir la fiche du projet" data-sen="View the project sheet"
        data-tags="école~school"      <!-- '|' séparés, paires slug~slug -->
        data-dl="OAAT-0001.jpg"
        data-q="mots normalisés minuscules sans accents toutes langues oaat-0001">
  <button type="button" class="open relative block h-full w-full ..." aria-label="Ouvrir : {titre FR}">
    <img src="{card}" alt="" ...>
    …surface de légende (année, titre)…
  </button>
  <button type="button" class="sel absolute right-2 top-2 ..." aria-pressed="false" aria-label="Sélectionner">
    <svg …/>
  </button>
  <div class="meta hidden min-w-0 grow group-data-[view=list]/g:block">
    …catégorie · année, titre (h3), lieu, OAAT-xxxx, tags…
  </div>
</figure>
```

- `data-w`/`data-h` **obligatoires** (ratio mosaïque). `data-y` optionnel (sinon « — »).
- Miniature filmstrip, dans `#vfl` : `<button type="button" class="fs hidden shrink-0 opacity-60 …" data-id="1"><img src="{thumb}"></button>` (une par média).

### 3.2 IDs / classes requis

| Groupe | Éléments |
|---|---|
| En-tête / défilement | `#pg`, `#hd`, `#hi`, `#up`, `#rg` (déjà dans le layout) |
| Hero | `#sl0` (lancer le diaporama), `#hlp` (aide) |
| Barre d'outils | `#fbtn` (+ badge `#fn`), `#q` (recherche), `#dens` (taille), `#so` (tri), `.vb[data-v]` (vues), `#rc` (compteur visibles) |
| Facettes (aside `#fb`) | `input[data-f]` + `label>span.grow` + `.cnt` ; `#fclr` ; années `#y0`, `#y1`, `#y0t`, `#y1t`, `#yr`, `.yb[data-y]` ; couleurs `.cc[data-col]` ; décennies `.dec[data-y0][data-y1]` |
| Tiroir mobile | `#fbtn`, `#fb`, `#fbk`, `#fclose` |
| Onglets | `#tabs` + `.tb[data-c]` (+ `.cnt`) |
| Chips | `#chips` + `.chip[data-f][data-v]`, `#chy`, `#chq`, `#cclr` |
| Grille | `#gr` + `figure.ph` (§3.1) ; `#em` (état vide, `.emc`), `#more`, `#snt`, `#pg2` |
| Sélection | `#tray` + `#tn`, `#tl`, `#tall`, `#tinv`, `#tcp`, `#tclr`, `#treq` (mailto) ; `#cpi` (input caché) |
| Aide | `#help` + `#hlp`, `#hx` (modal raccourcis) |
| Visionneuse `#vw` | `#vst` (scène), `#vi` (img), `#vpr` (progression diapo), `#vbar`, `#vcn`, `#vx`, `#vp`, `#vn`, `#vzi`, `#vzo`, `#vz0`, `#vsl`, `#vfs`, `#vii`, `#vs` (`.v-off`/`.v-on`), `#vc`, `#vsh`, `#vsim`, `.vtg[data-tg]`, `#vd`, `#vsrc`, `#vsrt`, `#vq` ; `#vin` (panneau infos : `#vcat`, `#vt`, `#vm1`…`#vm10`) ; `#vfl` + `.fs[data-id]` (filmstrip) |
| Toast | `#ts` (déjà dans le layout) |

## 4. Notes de migration & pièges

- **Ne pas redéclarer** les noms existants de `oaat.js` (en-tête + const § `ph sc pick cf bf sq rc em fb …`) → §23 renomme : `gq` (#q), `fb2` (#fb), `lbl` (≠ §20 `lb`), `rcEl`/`emEl` (≠ §15 `rc`/`em`), `trayList` (≠ §12 `tl`), `facetPick` (≠ §8 `pick`), `soEl`, `densEl`, `vnEl`, `vsEl`.
- **§15 adapté** : le filtre en direct des cartes projets est **gardé** par `if(sq && $('#gr article'))` car la galerie réutilise `#q`, `#rc`, `#em`. Sans cette garde, la galerie serait traitée comme « 0 projet ».
- **§5 non modifié** : `#top` existe sur `projets-show` ET sur la galerie (héro) ; des `<h2>` vivent dans `#gr` sur `projets-index` (ex. « En attente de financement »). La maquette exclut `#gr`/`#top` de ses barres mais la galerie ne contient aucun `h2` dans ces zones — le comportement par défaut de §5 convient.
- **`#tabs`** : l'onglet timeline (organisation, §7) = `#tabs [data-tab]` ; la galerie = `#tabs .tb[data-c]`. Sélecteurs distincts, même id sur des pages différentes — OK.
- **Langue** : pas de bascule client (routes + `$t()`). Le tri `az` et la recherche `nz` suivent `<html lang>`.
- **`data-id` stable exigé** : sert (a) de référence `OAAT-xxxx`, (b) de cible du lien direct `#gN`, (c) de clé du panier localStorage `oaat-sel`. Ne pas réordonner entre deux rendus.
- **localStorage** : clés `oaat-sel`, `oaat-view`, `oaat-dens` (préfixe `oaat-`).
- **`data-en`** de la maquette : ignoré (rendu serveur localisé). Le JS le lit quand présent (compatible fichier statique).
- **Isolation** : le module §23 ne s'active que si `#gr` + `figure.ph` existent — aucun effet sur les autres pages.
- **Interdépendance layout** : `#pg`, `#up`, `#rg`, `#ts` existent déjà dans `layouts/app.blade.php` ; la page galerie n'a qu'à les laisser en place.
---

## 5. Étape 3 — Logique métier : analyse du terrain

> Analyse produite **avant** l'implémentation du contrôleur. Elle fixe le
> périmètre, les dettes et l'ordre de travail de l'étape 3.

### 5.1 Périmètre et état des lieux

**Ce qui existe déjà (fonctionnel)**

| Pièce | État | Remarque |
|---|---|---|
| Modèle `Album` | ✅ | `title` (traduisible), `slug`, `project_id`, collection `photos` |
| Modèle `Media` (vendor) | ✅ | `custom_properties` JSON, `getUrl($conversion)`, conversions `thumb`/`card` |
| `GalerieController@index()` | ⚠️ | Ne passe que `albums` + `locale` → **à réécrire** |
| Route `GET /galerie` | ✅ | Pointe sur `GalerieController@index` |
| `pages/galerie-index.blade.php` | ✅ | Attend `$medias`, `$categories`, `$facettes`, `$annees`, `$collections`, `$qs`, `$tags`, `$stats`, `$settings`, `$locale` |
| 10 partials `_galerie_*` | ✅ | Rendu validé : 81/81 hooks DOM, 11/11 compilation |
| `oaat.js` §23 | ✅ | Lit les `data-*`, filtre, trie, visionneuse |
| `AlbumSeeder` | ⚠️ | 1 album vide, sans photo → **à enrichir** |
| `SettingResource` (Filament) | ✅ | Identité, contacts, réseaux, chiffres — aucune section galerie |
| `AlbumResource` (Filament) | ✅ | Création d'album + upload photos, **sans métadonnées par média** |

### 5.2 Dettes et trous identifiés

#### D1 — Aucune métadonnée par photo

`AlbumForm` téléverse des photos mais ne collecte **aucune** métadonnée par
fichier : ni année, ni titre, ni lieu, ni catégorie, ni thème, ni tags. Or
`_galerie_media.blade.php` attend **20+ `data-*`** par `<figure>` :

`data-y`, `data-tf`, `data-te`, `data-pfr`, `data-pen`, `data-cat`, `data-th`,
`data-lieu`, `data-ori`, `data-par`, `data-col`, `data-use`, `data-tags`,
`data-src`, `data-sfr`, `data-sen`, `data-q`, `data-thumb`, `data-card`,
`data-dl`, `data-catLabel`, `data-catLabelEn`, `data-lieuLabel`.

**Conséquence** : les `<figure>` sortent vides, le JS ne peut ni filtrer ni
trier → page inutilisable.

**Emplacement retenu** : la colonne `custom_properties` (JSON) de la table
`media` — déjà présente, déjà castée en `array` par le modèle `Media`, mais
actuellement vide (`{}`).

#### D2 — Aucune UI pour remplir `custom_properties`

Enrichir `SpatieMediaLibraryFileUpload` avec des champs par fichier
(`withCustomProperties([...])`) n'est pas trivial : la librairie n'offre pas
nativement de formulaire par média.

**Solution MVP** : métadonnées posées par `AlbumSeeder` (démo) et, côté
contrôleur, dérivation de ce qui est déductible du contexte
(Album → Projet → domaine pour `cat` / `th`). L'admin des métadonnées sera une
étape ultérieure dédiée.

#### D3 — `GalerieController@index()` quasi vide

Le contrôleur actuel se limite à `Album::query()->latest('created_at')->get()`
et ne transmet que `albums`. Il doit :

1. charger tous les albums **avec** leurs médias ;
2. transformer chaque média en hash `$m` conforme au contrat §3.1 ;
3. calculer `$categories`, `$facettes`, `$collections`, `$annees`, `$stats`,
   `$tags`, `$qs`.

#### D4 — `$settings` non transmis

La vue et le layout lisent `$settings ?? null` ; les autres contrôleurs
(`Accueil`, `Projet`, `Organisation`) passent `Setting::current()`.
`GalerieController` doit faire de même (topbar, footer, e-mail du dock).

#### D5 — `AlbumSeeder` sans photo

Un seul album vide : impossible de démontrer facettes, tris et visionneuse.
Minimum : 2-3 albums et quelques photos factices portant leurs
`custom_properties`.

### 5.3 Plan d'implémentation (4 phases)

#### Phase A — Réécrire `GalerieController@index`

Fichier : `app/Http/Controllers/Public/GalerieController.php`

```php
class GalerieController
{
    public function index(string $locale): mixed
    {
        $settings = Setting::current();

        // 1. Albums + médias (eager) : évite le N+1 sur getMedia('photos')
        $albums = Album::query()
            ->with('media')
            ->with('project')
            ->latest('created_at')
            ->get();

        // 2. Catalogue plat : un hash par média
        $medias = collect();
        foreach ($albums as $album) {
            foreach ($album->getMedia('photos') as $media) {
                $medias->push($this->buildMediaHash($media, $album, $locale));
            }
        }

        // 3. Catégories (onglets .tb)
        $categories = $medias->groupBy('cat')->map(...)->values();

        // 4. Facettes (thème, lieu, partenaires, collections, orientation…)
        $facettes = [...];

        // 5. Collections (bandeau #cs)
        $collections = [...];

        // 6. Années (min / max / histogramme #yr)
        $annees = [...];

        // 7. Statistiques du héro
        $stats = [...];

        // 8. Vocabulaire des tags (panneau infos)
        $tags = [...];

        // 9. Recherches fréquentes
        $qs = config('oaat.galerie.quick_searches', []);

        return view('pages.galerie-index', compact(
            'medias', 'categories', 'facettes', 'collections',
            'annees', 'stats', 'tags', 'qs', 'settings', 'locale'
        ));
    }
}
```

#### Phase A1 — Helper `buildMediaHash()`

Méthode privée du contrôleur (une fois stabilisée, elle pourra migrer vers un
service) :

- lit `$media->custom_properties` ;
- remplit les 20+ clés du contrat §3.1 ;
- replis : `tf`/`te` ← titre de l'album, `cat`/`th`/`y` ← projet rattaché ;
- construit `id`, `ref` = `OAAT-` + `str_pad(id, 4, '0')`, `q` normalisé,
  `thumb`/`card`/`dl` via les conversions.

#### Phase B — Enrichir `AlbumSeeder`

- 2-3 albums, titres FR/EN et slugs distincts ;
- 2-3 photos par album, chacune avec ses `custom_properties` (année, titres,
  lieu, catégorie, thème, tags) ;
- visuels de démo (mêmes SVG placeholders que la maquette) : aucune dépendance
  à un fichier binaire.

#### Phase C — Test `tests/Feature/GalerieIndexTest.php`

Calqué sur `ProjetIndexTest` (§14 des PATTERNS) :

- mock de `ViewFactory` pour capturer les données passées au rendu ;
- vérifie la présence et le typage des 10 variables ;
- vérifie les compteurs (`$stats`), la structure d'un hash média (clés
  obligatoires) et la cohérence des facettes avec le catalogue.

#### Phase D — Dettes à consigner

Reporter D1 → D5 dans `PATTERNS.md` §16 (dettes & pièges connus) à la clôture
de l'étape.

### 5.4 Décisions d'architecture

| Question | Décision |
|---|---|
| Stockage des métadonnées par photo | `custom_properties` (JSON sur `media`) — **pas de nouvelle table** |
| UI admin de saisie des métadonnées | **Hors périmètre** de l'étape 3 (seeder + dérivation) |
| Calcul des facettes | En PHP, dans le contrôleur, à partir du catalogue |
| Source des catégories | Champ `cat` de `custom_properties` (libellé via config) |
| Source des collections | Champ `col[]` de `custom_properties` |
| Source des années | `custom_properties.year`, repli `album.project.start_date` |
| Source des tags | `custom_properties.tags[]` |
| Recherches fréquentes | `config('oaat.galerie.quick_searches')` |
| URL des images | `Media::getUrl('thumb')`, `Media::getUrl('card')`, `Media::getUrl()` |
| Référence affichée | `OAAT-` + `media.id` sur 4 chiffres |
| Chaîne de recherche `q` | `tf` + `te` + référence, normalisée |
| Transmission de `$settings` | Oui, comme tous les contrôleurs publics |

### 5.5 Points ouverts

**1. `data-mailto` du dock de sélection (`#treq`)**

La maquette pose `data-mailto` sur `<body>` ; notre JS §23.5 le lit pour
construire le lien de demande. Deux options :

- poser `data-mailto="{{ $settings?->email ?? 'contact@oaat.cd' }}"` sur le
  conteneur de la page galerie ;
- laisser le JS construire le `mailto:` depuis `$settings->email`.

**Reco** : première option (un attribut, aucune logique en JS), valeur fournie
par le contrôleur.

**2. Métadonnées photo dans le formulaire Album, maintenant ?**

Non pour cette étape : chantier Filament conséquent (champs par fichier
téléversé). Le contrôleur fonctionne avec `custom_properties` et le seeder
fournit la démo. L'admin des métadonnées fera l'objet d'une étape dédiée.

### 5.6 Ordre d'exécution de l'étape 3

1. **`GalerieController@index`** — réécriture complète + helper
   `buildMediaHash()` (catalogue, catégories, facettes, collections, années,
   stats, tags).
2. **`AlbumSeeder`** — photos de démonstration + `custom_properties`.
3. **`GalerieIndexTest`** — capture du contrat de données et validation des
   compteurs.
4. **`PATTERNS.md` §16** — consignation des dettes D1 → D5.
