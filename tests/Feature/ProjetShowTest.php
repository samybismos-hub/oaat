<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Page publique « Fiche projet » (ProjetController@show) — pages.projets-show.
 *
 * La vue hérite de layouts.app (topbar, header, footer) et consomme les
 * helpers du modèle Project (budget, durée, bénéficiaires, focus…). On rend
 * la vraie vue : ce test attrape toute erreur de compilation Blade ou de
 * données invalides.
 */
class ProjetShowTest extends TestCase
{
    use RefreshDatabase;

    private function makeDomain(): Domain
    {
        return Domain::create([
            'name' => ['fr' => 'Santé', 'en' => 'Health'],
            'slug' => 'sante',
            'position' => 1,
        ]);
    }

    private function makeProject(Domain $domain, array $attributes = []): Project
    {
        return Project::create(array_merge([
            'domain_id' => $domain->id,
            'title' => ['fr' => 'Projet de démonstration', 'en' => 'Demonstration project'],
            'slug' => 'projet-demonstration',
            'status' => 'awaiting_funding',
            'country' => 'RD Congo',
            'city' => 'Bukavu',
            'start_date' => '2025-01-01',
            'end_date' => '2029-12-31',
            'objectives' => ['fr' => 'Objectif de démonstration.', 'en' => 'Demonstration objective.'],
            'results' => ['fr' => 'Résultat de démonstration.', 'en' => 'Demonstration result.'],
            'budget_amount' => 450000000,
            'budget_currency' => 'EUR',
            'beneficiaries_count' => 6349200,
            'beneficiaries_unit' => 'habitants',
            'is_featured' => true,
            'published_at' => now(),
        ], $attributes));
    }

    public function test_la_fiche_projet_rend_avec_les_donnees_du_modele(): void
    {
        $domaine = $this->makeDomain();
        $projet = $this->makeProject($domaine);

        $response = $this->get("/fr/projets/{$projet->slug}");

        $response->assertOk();
        $response->assertSee('Projet de démonstration');      // titre (hero + breadcrumb)
        $response->assertSee('Santé');                         // domaine (fiche)
        $response->assertSee('Recherche de financement');      // statut (fiche)
        $response->assertSee('450 000 000 €');                 // budget (fiche)
        $response->assertSee('5 ans');                         // durée 2025→2029
        $response->assertSee('Objectif de démonstration.');    // aperçu
    }

    public function test_la_fiche_projet_est_bilingue(): void
    {
        $domaine = $this->makeDomain();
        $projet = $this->makeProject($domaine);

        $this->get("/en/projets/{$projet->slug}")
            ->assertOk()
            ->assertSee('Demonstration project')         // titre anglais
            ->assertSee('Seeking funding')               // statut anglais
            ->assertSee('Demonstration objective.');     // aperçu anglais

        $this->get("/fr/projets/{$projet->slug}")
            ->assertOk()
            ->assertSee('Recherche de financement');
    }

    public function test_les_cartes_appel_a_soutien_pointent_vers_le_contact_avec_objet_pre_rempli(): void
    {
        $domaine = $this->makeDomain();
        $projet = $this->makeProject($domaine);

        $response = $this->get("/fr/projets/{$projet->slug}");

        $response->assertOk()
            ->assertSee('/fr/contact?objet=f')
            ->assertSee('/fr/contact?objet=p')
            ->assertSee('/fr/contact?objet=i');
    }

    public function test_le_bloc_autres_projets_affiche_un_badge_traduit_sans_valeur_brute(): void
    {
        $domaine = $this->makeDomain();
        $projet = $this->makeProject($domaine);
        $autre = $this->makeProject($domaine, [
            'slug' => 'deuxieme-projet',
            'title' => ['fr' => 'Deuxième projet à financer', 'en' => 'Second project to fund'],
        ]);

        $response = $this->get("/fr/projets/{$projet->slug}");

        $response->assertOk()
            ->assertSee('Autres projets disponibles')
            ->assertSee('Deuxième projet à financer')
            ->assertSee('À financer');
        // La valeur brute de l'enum ne doit plus jamais apparaître.
        $this->assertStringNotContainsString('awaiting_funding', $response->getContent());

        $enb = $this->get("/en/projets/{$projet->slug}");

        $enb->assertOk()
            ->assertSee('Other projects available')
            ->assertSee('Second project to fund')
            ->assertSee('To fund');
        $this->assertStringNotContainsString('awaiting_funding', $enb->getContent());
    }

    public function test_la_fiche_projet_ignore_les_sections_sans_contenu(): void
    {
        $domaine = $this->makeDomain();
        $projet = $this->makeProject($domaine, [
            'city' => null,
            'country' => null,
            'beneficiaries_count' => null,
            'beneficiaries_unit' => null,
        ]);

        $response = $this->get("/fr/projets/{$projet->slug}");

        $response->assertOk();
        // Pas de zone, pas de sous-nav vers #zone, pas de galerie vide.
        $this->assertStringNotContainsString('id="zone"', $response->getContent());
        $this->assertStringNotContainsString('id="galerie"', $response->getContent());
    }

    public function test_un_projet_non_publie_renvoie_404(): void
    {
        $domaine = $this->makeDomain();
        $brouillon = $this->makeProject($domaine, [
            'slug' => 'projet-brouillon',
            'published_at' => null,
        ]);

        $this->get("/fr/projets/{$brouillon->slug}")->assertNotFound();

        // Publier le projet le rend immédiatement accessible.
        $brouillon->update(['published_at' => now()]);
        $this->get("/fr/projets/{$brouillon->slug}")->assertOk();
    }

    public function test_les_sections_editoriales_sont_rendues_depuis_la_base(): void
    {
        $domaine = $this->makeDomain();
        $projet = $this->makeProject($domaine, [
            'slug' => 'projet-avec-editorial',
            'contexte' => [
                'fr' => "Paragraphe un.\n\nParagraphe deux.",
                'en' => "Paragraph one.\n\nParagraph two.",
            ],
            'objectif_general' => [
                'fr' => 'Objectif général test.',
                'en' => 'General test objective.',
            ],
            'composantes' => [
                [
                    'nom_fr' => 'Routes', 'nom_en' => 'Roads',
                    'prevoit_fr' => 'Construire.', 'prevoit_en' => 'Build.',
                    'resultat_fr' => 'Trafic.', 'resultat_en' => 'Traffic.',
                ],
            ],
        ]);

        $response = $this->get("/fr/projets/{$projet->slug}");

        $response->assertOk()
            ->assertSee('id="contexte"', false)
            ->assertSee('Paragraphe un.')
            ->assertSee('Paragraphe deux.')
            ->assertSee('Objectif général test.')
            ->assertSee('id="composantes"', false)
            ->assertSee('Routes')
            ->assertSee('Construire.')
            ->assertSee('Trafic.');

        $responseEn = $this->get("/en/projets/{$projet->slug}");

        $responseEn->assertOk()
            ->assertSee('Paragraph one.')
            ->assertSee('Paragraph two.')
            ->assertSee('General test objective.')
            ->assertSee('Roads')
            ->assertSee('Build.')
            ->assertSee('Traffic.');
    }

    /**
     * La mention « Crédit : … » de la fiche (couverture + galerie) vient du champ
     * bilingue credit_photo saisi dans l'administration du projet. Un vrai PNG est
     * utilisé : contrairement à un upload factice, il passe les conversions Spatie
     * sans déclencher d'erreur du pilote GD.
     */
    private function attachCover(Project $projet): void
    {
        Storage::fake('public');

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');

        $projet->addMediaFromString($png)
            ->usingFileName('couverture.png')
            ->toMediaCollection('cover');
    }

    public function test_le_credit_photo_est_rendu_depuis_le_projet(): void
    {
        $domaine = $this->makeDomain();
        $projet = $this->makeProject($domaine, [
            'slug' => 'projet-avec-credit',
            'credit_photo' => ['fr' => 'Photographe de test', 'en' => 'Test photographer'],
        ]);
        $this->attachCover($projet);

        $this->get("/fr/projets/{$projet->slug}")
            ->assertOk()
            ->assertSee('Crédit : Photographe de test');

        $this->get("/en/projets/{$projet->slug}")
            ->assertOk()
            ->assertSee('Credit : Test photographer');
    }

    public function test_sans_credit_photo_la_fiche_replie_sur_oaat(): void
    {
        $domaine = $this->makeDomain();
        $projet = $this->makeProject($domaine, ['slug' => 'projet-sans-credit']);
        $this->attachCover($projet);

        $this->get("/fr/projets/{$projet->slug}")
            ->assertOk()
            ->assertSee('Crédit : OAAT');
    }
}
