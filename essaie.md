Merci pour la relance — voici l'audit complet, refait méthodiquement sur l'état actuel du code (fiche + contrôleur + modèle + JS), avec des numéros de lignes exacts et des propositions pour chaque point.

---

# 📋 Rapport d'audit — Page `projets-show`

**Périmètre vérifié** : `resources/views/pages/projets-show.blade.php` (716 lignes), `app/Http/Controllers/Public/ProjetController.php` (`show` + `index`), `app/Models/Project.php`, `public/js/oaat.js`, layout `layouts/app.blade.php`, partials liées, modèles liés (`Partner`, `Album`, `Setting`, `ProjectStatus`).

---

## 1. Logique — `ProjetController@show` (l. 175-209)

| # | Verdict | Constat | Proposition |
|---|---|---|---|
| 1.1 | 🟥 **BUG (P1)** | Le projet affiché est chargé avec `where('slug')…->firstOrFail()` **sans `published()`** (contrairement à l'index). **Tout brouillon est publiquement accessible** par son slug. | Ajouter `->published()` dans `show()` (les non publiés → 404). Si l'équipe a besoin de prévisualiser, prévoir une route `?preview=` gardée par le panneau Filament. |
| 1.2 | 🟩 OK | Eager loading `domain`, `partners`, `albums.media` — 3 requêtes max, **aucun N+1** (helpers sans requête). | — |
| 1.3 | 🟩 OK | `$experiences` : 4 réalisés récents, courant exclu, `partners` chargés (réellement utilisés par `funderLabel`). | — |
| 1.4 | 🟨 | `$connexes_awaiting` trié par `latest('start_date')` : si une date de début est nulle, l'ordre devient instable. | Soit `orderByDesc('start_date')` protégé par `->whereNotNull('start_date')`, soit un vrai champ « priorité ». |
| 1.5 | 🟨 | `compteurs['en_cours']` existe dans `index()` mais la barre de filtres n'expose jamais `ongoing`/`planned` — compteur calculé pour rien. | Hors périmètre fiche ; à trancher côté index (afficher « En cours » dans les filtres ou supprimer le compteur). |

## 2. Logique — `Project` (modèle)

| # | Verdict | Constat | Proposition |
|---|---|---|---|
| 2.1 | 🟩 OK | `statusLabel()`, `getStatutCourtAttribute()`, `budgetLabel()`, `durationLabel()`, `beneficiariesLabel()`, `funderLabel()`, `implementationPartnersLabel()`, `partnersLabel()`, `year()` — tous utilisés correctement. | — |
| 2.2 | 🟨 | `beneficiariesLabel()` formate le nombre avec `number_format($c, 0, ',', ' ')` **dans les deux langues** → « 6 349 200 » en anglais aussi. | Formater selon la locale (`,` comme séparateur en anglais). |
| 2.3 | 🟨 | `durationLabel()` : si `start_date == end_date` (durée 0) → renvoie **« En cours »/« In progress »** (ambigu). | Distinguer « — » (inconnue) et « En cours » (projet sans date de fin assumé). |
| 2.4 | 🟨 | Logo des bailleurs : `$partenaire->getFirstMediaUrl('logo')` renvoie l'original ; la conversion `thumb` (200×200) existe mais n'est pas utilisée. | Utiliser `->getFirstMediaUrl('logo','thumb')` pour alléger la page. |
| 2.5 | 🟩 OK | Disques média tous `public` (cover, photos, logo) — le problème 403 de `TeamMember` n'affecte pas cette page. | — |

## 3. Logique — Vue & JS

| # | Verdict | Constat |
|---|---|---|
| 3.1 | 🟩 OK | Zéro `data-en` / `data-href-fr` restant dans la fiche ; toutes les chaînes passent par `$t()`. |
| 3.2 | 🟩 OK | Sous-nav / scrollspy (`oaat.js` §11), barre mobile (§17), copie (§18), impression (§19), galerie/lightbox (§20, `#ln2` utilisé), partage `.sh` (§22) — **tous les ID référencés par le JS existent dans le DOM**. |
| 3.3 | 🟨 | `meta_description` = `Str::limit($objectifs, 155)` **sans fallback** (description vide si pas d'objectifs) et **aucune balise canonical/Open Graph**. |
| 3.4 | 🟨 | Phrases de la section zone : templates « Le projet est mené à {zone} » — casse pour certaines formes de lieu (« mené à Oicha », féminin/pluriel). |
| 3.5 | 🟨 | `oaat.js` §22 gère encore `[data-href-fr]` alors qu'**aucune vue** n'en possède plus → code mort à nettoyer (garder seulement `.sh`). |
| 3.6 | 🟨 | `data-en` **toujours présents dans `projets-index` + `_carte_projet`** alors que le JS de traduction a été retiré → **les textes FR de l'index ne sont pas traduits en EN** (même défaut que la fiche avait). |

## 4. 🗂️ Inventaire du contenu codé en dur (hardcodé)

| Ligne(s) | Contenu figé | Conséquence | Proposition |
|---|---|---|---|
| **94** | `$editorial = $projet->slug === 'amenagement-des-8-territoires'` | La logique d'affichage dépend d'un **slug magique**. | Remplacer par `$projet->hasEditorialContent()` (vraies données). |
| **316-323** | 2 paragraphes « Contexte et enjeux » (les « huit territoires ») | Visibles **uniquement** sur le projet emblématique ; contenu métier figé dans le template. | Colonnes DB traduisibles `contexte` (JSON) ou modèle `ProjectSection`. |
| **331-364** | `$composantes` : 4 lignes × (nom, travaux, résultats) × FR/EN | Idem — le contenu métier est écrit en dur dans la vue. | Table `project_composantes` (ou JSON `composantes` bilingue dans `projects`) + interface Filament. |
| **366-367** | « Objectif général : faciliter la mobilité… de **huit territoires** » | Idem. | Champ `objectif_general` traduisible. |
| **138** | `Fiche projet n° {{ $projet->id }}` | Expose l'**ID interne de la base** au public. | Champ métier « n° de fiche » propre, ou retirer la mention. |
| **169 / ≈577** | « Crédit : OAAT » (hero + chaque légende de galerie) | Crédit figé. | Libellé crédit dans `Setting` (ou champ média). |
| **630** | « Interlocuteur : Ir Roger Manema Cirhahingirwa, Représentant national » | Personne **figée**, affichée sur **toutes** les fiches. | Passer nom + titre dans `Setting` (modifiable, affiché si renseigné). |
| **503-504** | Intro « Depuis 1995, l'OAAT réalise des routes, des ponts… » | Texte marketing figé dans le template. | `Setting` (texte d'introduction) ou assumé comme copie statique. |
| **437** | « Détail budgétaire → **Communiqué aux partenaires sur demande** » | Réponse stéréotypée codée en dur. | `Setting` (texte) ou suppression. |
| **82** | `?? 'oaatrdc2000@gmail.com'` | E-mail de secours en dur. | Constante de config `config/oaat.php` (cohérente avec `oaat.founded_at`). |
| **466-469** | Logo OAAT en **SVG inline** | **Déjà dupliqué** dans `_header` (l.20-21) et `_footer` (l.44-45). | Partial commun `_svg_oaat` réutilisé partout. |
| **144, 654** | `text-[#7A5200]` | Hexadécimal brut répété. | Token Tailwind `ochre-dark` (comme `lake`, `moss`…). |
| **479-485** | `mailto:` « Rejoindre le partenariat » | CTA e-mail hors du modèle « contact ?objet= ». | Aligner sur `route('contact','objet=p')` pour cohérence avec les 3 cartes. |
| — | Wording statut | Index : « **En attente de financement** » (`_carte_projet`) vs Fiche : « **Recherche de financement** » (`statusLabel`). | Harmoniser en un seul libellé via `statusLabel()` partout. |
| — | Labels d'UI (fil d'ariane, sous-nav, aside, outils, tableaux) | Traduits via `$t()` — **légitimes**, pas du hardcodé. | — |

## 5. Priorisation des propositions (feuille de route)

**P1 — Sécurité/intégrité**
1. `show()` : appliquer `->published()` (+ garde de prévisualisation).
2. Supprimer l'ID de la base de la phrase « Fiche projet n° » (ou vrai champ métier).

**P2 — Contenu éditorial pilotable**
3. Migration `projects` : `contexte` (JSON), `objectif_general` (JSON), `composantes` (JSON bilingue) — ou table `project_sections` ; champs Filament (onglet « Éditorial ») ; suppression du bloc `$editorial` + des tableaux figés.
   - *Note* : une colonne JSON suffit pour les composantes (structure simple) ; une table est plus évolutive (réordonnancement par un admin).

**P3 — « Deco-sanitation » du hardcodé**
4. `Setting` : interlocuteur national + crédit photo → suppression des valeurs figées.
5. Factorization du logo SVG OAAT + token couleur `ochre-dark`.
6. Aligner « Rejoindre le partenariat » sur `contact?objet=p`.
7. Fallback email → `config/oaat.php`.

**P4 — Conseil / cohérence metier**
8. Harmoniser libellés de statut (index = fiche) via `statusLabel()` + badge court.
9. `beneficiariesLabel` formaté par locale ; `durationLabel` 0/0 → « — ».
10. Logo bailleur : utiliser la conversion `thumb`.
11. SEO : canonical + OG minimales + fallback de `meta_description` (reprendre `$resultats` puis le titre).
12. Web Share API pour « Partager » à la place du mailto forcé.
13. Nettoyer `oaat.js` §22 (supprimer la branche `[data-href-fr]`) et traiter les `data-en` résiduels de `projets-index`/`_carte_projet` (traduction EN cassée).

## 6. État de validation actuel
- `ProjetShowTest` **5/5** · `ContactPageTest` **3/3** · suite complète 92 passés / 5 échecs (4× GD images + redirect racine — environnement, hors de cause).
- Rendu réel FR/EN vérifié sur Laragon : liens `?objet=f/p/i`, badge « À financer »/« To fund », 0 `data-en`, 0 valeur brute enum.

---

**Recommandation d'enchaînement** : je conseille de commencer par **P1 (filtre `published`)**, puis **P2 (migration éditorial)** qui déverrouille réellement la fiche (elle cessera d'être liée au slug « 8 territoires »), puis le lot **P3** de dés-hardcodage. Souhaitez-vous que je décline l'un de ces chantiers en tâches implémentables (j'aurai besoin par exemple de l'exposition des champs éditoriaux dans le formulaire Filament pour P2) ?
