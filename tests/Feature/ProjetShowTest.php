<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\Project;
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
}
