# Refonte des fiches projet & galeries — Plan de conformité (v2)

> **Ce plan remplace la v1.** Il ne reflète pas un cahier des charges à exécuter :
> il documente l'**état réellement livré** (référentiel) et les seuls points
> résiduels ouverts. Conformément aux échanges, **ce qui est déjà implémenté
> et testé prime sur toute ancienne mention de phase « à faire »** ; les phases
> 3/4 de la v1 s'y rapportent désormais comme « résidu optionnel ».

---

## 1. Livré et testé (référentiel — ne pas refaire)

| Domaine | Implémentation | Validation |
|---|---|---|
| **Fiche projet publique** | `projets-show` hérite de `layouts.app` ; sections à affichage conditionnel (zone, editorial, expérience, connexes, galerie) ; SEO dynamique ; plus de contenu hardcodé | `ProjetShowTest` 9/9 |
| **Contenu éditorial** | `contexte`, `objectif_general`, `composantes` (JSON bilingue sur `projects`), saisis via `ProjectForm`, rendus dans les sections « Contexte » / « Composantes », masquées si vides (`hasEditorialContent()`) | `ProjetShowTest` 9/9 |
| **Crédit photo** | **Par projet** (champ bilingue `credit_photo`), appliqué à la couverture + galerie, fallback « OAAT » | `ProjetShowTest` (2 tests) |
| **Appels à l'action « Soutenir »** | 3 cartes + CTA du bloc soutien → `/{locale}/contact?objet=f|p|i` ; le formulaire de contact pré-sélectionne l'objet et surligne le bouton correspondant | `ContactPageTest` 3/3 |
| **Publication** | Brouillons/non publiés → 404 (`published()` sur `show`) | `ProjetShowTest` |
| **Expérience comparable / connexes** | Réalisés **+ En cours**, hors fiche courante ; compteurs `take(4)` / `take(3)` issus de `config('oaat.projects.*')` | `ProjetShowTest` + `config/oaat.php` |
| **Helpers modèle** | `statusLabel()`, `durationLabel()` (cas zéro), `beneficiariesLabel()` (loc), `hasEditorialContent()`, `funderLabel()`… — source unique | `ProjetIndexTest` 7/7 |
| **Indicateurs index** | Compteurs publics (dont `zones`) | `ProjetIndexTest` 7/7 |
| **Options A — Albums = canal unique** | La collection `photos` du projet (code mort) est supprimée ; galerie gérée **uniquement** par les albums liés ; onglet « Albums photo » (`AlbumsRelationManager`) sur la fiche projet ; slug d'album (migration + formulaire) corrigeant `GalerieController@show` | `ProjectResourceTest` 12/12, `AlbumResourceTest` 8/8 |

## 2. Décisions déjà actées (à ne pas rouvrir)

- **D1 — N° de fiche** : retiré de la fiche publique.
- **D2 — Stockage éditorial** : colonnes JSON sur `projects` (pas de table `project_sections`).
- **D3 — « Expérience comparable »** : inclut Réalisés **+ En cours**.
- **D4 — Appels à l'action** : les cartes et CTAs « Soutenir » passent par le **formulaire de contact avec objet pré-rempli** (traçable, cohérent), plus de `mailto:` pour ces blocs.
- **D5 — Crédit photo** : **par projet** (et non `photo_credit` global dans les Settings comme le prévoyait la v1) — la v1 (T3.1/T3.2) est caduc sur ce point.
- **D6 — Galeries** : **albums = canal unique** (option A) ; pas de double système de photos projet/album.
- **D7 — Publication** : les fiches non publiées ne sont jamais exposées.

## 3. Points résiduels — traités (section 3 « décisions »)

| # | Décision | Traitement appliqué |
|---|---|---|
| **O1** | Pas de `mailto:` | ✅ Le lien « Rejoindre ce partenariat » (bloc Financement) et le lien « Interlocuteur » passent par `route('contact')` (`objet=p` / `objet=i`) ; le helper mort `$mailto`/`$u` et le bloc « E-mails automatiques » sont supprimés. Aucun `mailto:` dans la fiche. |
| **O2** | Wording « Recherche de financement » | ✅ `_carte_projet` utilise désormais `statusLabel()` (source unique du modèle) ; le doublon local `$statutLabel` (« En attente de financement ») est supprimé. |
| **O3** | Nettoyer | ✅ **O3a** – §22 `data-href-fr`/`apply` de `oaat.js` retiré (`node --check` OK). **O3b** – `data-en` retirés de `_carte_projet` + `projets-index` (0 restant ; les `$t()` rendent déjà l’EN). **O3c** – SEO : `rel=canonical` + OG de base dans `layouts/app.blade.php`, fallback `meta_description` (objectifs → résultats → titre) + `og:image` sur la fiche. **O3d** – token `ochreDark` (#7A5200), plus aucune classe arbitraire. **O3e** – logo SVG factorisé dans `partials/_logo_oaat` (3 occurrences), SVG restant unique. Vues compilées ✓ |
| **O4** | Corriger les derniers PNG | ✅ `PartnerResourceTest::test_le_logo_est_stocke…` réparé avec un vrai PNG 1×1 `base64_decode` (fini le `imagecreatefromstring` en échec). `ExampleTest` réécrit : la racine redirige vers la locale (302). |

## 4. Critère d'acceptation (reformulé)

- **Suite complète « tout vert »** : 105/105 avant O4 → cible après O4 : 107/107 (voir `storage/logs/_suite2.log`).
- Les 5 classes ciblées (`ProjetShowTest`, `ContactPageTest`, `ProjetIndexTest`, `ProjectResourceTest`, `AlbumResourceTest`) : ✓ vertes tout au long.
- Rendu manuel FR/EN conforme sur « 8 territoires » et fiches sans sections vides : ✓.

