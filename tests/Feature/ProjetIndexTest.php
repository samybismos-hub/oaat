<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\Project;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

/**
 * Étape A — Backend de la page « Projets » (ProjetController@index).
 *
 * La vue pages.projets-index n'existe pas encore (créée à l'étape B) :
 * on mock la factory de vue pour capturer les données passées au rendu et
 * vérifier les filtres, les compteurs et le calcul de la durée.
 */
class ProjetIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Vue factice conforme au contrat Illuminate\Contracts\View\View.
     */
    private function fakeView(): View
    {
        return new class implements View
        {
            public function name(): string
            {
                return 'pages.projets-index';
            }

            public function with($key, $value = null): static
            {
                return $this;
            }

            public function getData(): array
            {
                return [];
            }

            public function render(?callable $callback = null): string
            {
                return '';
            }
        };
    }

    /**
     * Joue la requête GET /fr/projets et capture les données de la vue.
     *
     * Le helper view() résout le contrat Illuminate\Contracts\View\Factory :
     * on remplace ce binding par un mock (recherche de la vraie vue à l'étape B).
     *
     * @return array<string, mixed>
     */
    private function getIndex(string $query = ''): array
    {
        $captured = null;

        $this->mock(ViewFactory::class, function ($mock) use (&$captured) {
            $mock->shouldReceive('make')
                ->with('pages.projets-index', Mockery::capture($captured), Mockery::any())
                ->andReturn($this->fakeView());

            // Le middleware SetLocale partage la locale via view()->share(...).
            $mock->shouldReceive('share')->andReturnNull();
        });

        $this->get('/fr/projets' . $query)->assertOk();

        $this->assertIsArray($captured, "La vue n'a pas reçu de données.");

        return $captured;
    }

    private function makeDomain(string $slug = 'sante'): Domain
    {
        return Domain::create([
            'name' => ['fr' => ucfirst($slug), 'en' => ucfirst($slug)],
            'slug' => $slug,
            'position' => 1,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeProject(Domain $domain, array $attributes = []): Project
    {
        return Project::create(array_merge([
            'domain_id' => $domain->id,
            'title' => ['fr' => 'Projet par défaut', 'en' => 'Default project'],
            'slug' => 'projet-' . bin2hex(random_bytes(4)),
            'status' => 'completed',
            'country' => 'RD Congo',
            'city' => 'Bukavu',
            'start_date' => '2023-01-01',
            'end_date' => '2023-12-31',
            'budget_amount' => 100000,
            'budget_currency' => 'USD',
            'beneficiaries_count' => 1000,
            'published_at' => '2023-01-01 00:00:00',
        ], $attributes));
    }

    public function test_index_filtre_par_statut(): void
    {
        $domaine = $this->makeDomain();

        $this->makeProject($domaine, [
            'title' => ['fr' => 'Ponts à Sebele', 'en' => 'Bridges in Sebele'],
            'status' => 'completed',
        ]);

        $this->makeProject($domaine, [
            'title' => ['fr' => 'Drainage du marais', 'en' => 'Marsh drainage'],
            'status' => 'awaiting_funding',
        ]);

        $this->makeProject($domaine, [
            'title' => ['fr' => 'École en construction', 'en' => 'School under construction'],
            'status' => 'ongoing',
        ]);

        // « all » → les 3 projets publiés.
        $data = $this->getIndex();
        $this->assertCount(3, $data['projets']);
        $this->assertSame('all', $data['statut']);
        $this->assertSame(3, $data['compteurs']['total']);

        // « completed » → seul le projet réalisé.
        $data = $this->getIndex('?statut=completed');
        $this->assertCount(1, $data['projets']);
        $this->assertSame('completed', $data['projets']->first()->status->value);
        $this->assertSame('completed', $data['statut']);

        // « awaiting_funding » → le projet en attente de financement.
        $data = $this->getIndex('?statut=awaiting_funding');
        $this->assertCount(1, $data['projets']);
        $this->assertSame('awaiting_funding', $data['projets']->first()->status->value);

        // Valeur inconnue → repli sur « all ».
        $data = $this->getIndex('?statut=en-cours');
        $this->assertCount(3, $data['projets']);
        $this->assertSame('all', $data['statut']);
    }

    public function test_index_filtre_par_domaine(): void
    {
        $sante = $this->makeDomain('sante');
        $education = $this->makeDomain('education');

        $this->makeProject($sante, ['title' => ['fr' => 'Centre de santé', 'en' => 'Health centre']]);
        $this->makeProject($education, ['title' => ['fr' => 'École de Karhale', 'en' => 'Karhale school']]);

        $data = $this->getIndex('?domaine=education');
        $this->assertCount(1, $data['projets']);
        $this->assertSame('education', $data['domaine']);
        $this->assertSame('École de Karhale', $data['projets']->first()->getTranslation('title', 'fr'));

        // Domaine inconnu → aucun résultat.
        $data = $this->getIndex('?domaine=inexistant');
        $this->assertCount(0, $data['projets']);
    }

    public function test_index_recherche_plein_texte(): void
    {
        $domaine = $this->makeDomain();

        $this->makeProject($domaine, [
            'title' => ['fr' => 'Drainage du marais de Nyamubanda', 'en' => 'Nyamubanda marsh drainage'],
        ]);
        $this->makeProject($domaine, [
            'title' => ['fr' => 'Construction des ponts Kasandjala', 'en' => 'Kasandjala bridges construction'],
        ]);

        $data = $this->getIndex('?q=nyamubanda');
        $this->assertCount(1, $data['projets']);
        $this->assertSame('nyamubanda', $data['recherche']);
        $this->assertStringContainsStringIgnoringCase('Nyamubanda', $data['projets']->first()->getTranslation('title', 'en'));

        // Recherche via l'alias « recherche ».
        $data = $this->getIndex('?recherche=Kasandjala');
        $this->assertCount(1, $data['projets']);
    }

    public function test_index_expose_compteurs_domaines_et_periodes(): void
    {
        $sante = $this->makeDomain('sante');
        $education = $this->makeDomain('education');

        $this->makeProject($sante, ['city' => 'Bukavu']);
        $this->makeProject($sante, ['city' => 'Fizi']);
        $this->makeProject($education, ['city' => 'Bukavu']);

        // 1 projet non publié : exclu partout.
        $this->makeProject($sante, [
            'title' => ['fr' => 'Brouillon', 'en' => 'Draft'],
            'published_at' => null,
        ]);

        $data = $this->getIndex();

        $this->assertSame(3, $data['compteurs']['total']);
        $this->assertSame(3, $data['compteurs']['realises']);
        $this->assertSame(0, $data['compteurs']['en_attente']);
        $this->assertSame(2, $data['compteurs']['domaines']);
        $this->assertSame(2, $data['compteurs']['zones']);

        // Statuts de la barre de filtres : 3 entrées avec leur compteur.
        $this->assertCount(3, $data['statuts']);
        $this->assertSame(
            [['cle' => 'all', 'count' => 3], ['cle' => 'completed', 'count' => 3], ['cle' => 'awaiting_funding', 'count' => 0]],
            $data['statuts']
        );

        // Domaines avec le count réel des projets publiés (sans dépendre de
        // l'ordre d'affichage, trié par nom traduit dans la langue active).
        $this->assertCount(2, $data['domaines']);
        $counts = $data['domaines']->pluck('projects_count')->sort()->values()->all();
        $this->assertSame([1, 2], $counts);

        // Périodes fixes de la maquette (4 plages).
        $this->assertCount(4, $data['periodes']);
        $this->assertSame('1994–2000', $data['periodes'][0]['label']);
        $this->assertSame('2021–2024', $data['periodes'][3]['label']);
    }

    public function test_duration_parts_et_labels(): void
    {
        $domaine = $this->makeDomain();

        $projet = $this->makeProject($domaine, [
            'start_date' => '2007-01-01',
            'end_date' => '2007-05-01',
        ]);

        $this->assertSame(['years' => 0, 'months' => 4], $projet->durationParts());
        $this->assertSame('4 mois', $projet->durationLabel('fr'));
        $this->assertSame('4 months', $projet->durationLabel('en'));

        // 1 an 2 mois.
        $projet = $this->makeProject($domaine, [
            'start_date' => '2020-01-01',
            'end_date' => '2021-03-01',
            'slug' => 'projet-' . bin2hex(random_bytes(4)),
        ]);
        $this->assertSame(['years' => 1, 'months' => 2], $projet->durationParts());
        $this->assertSame('1 an 2 mois', $projet->durationLabel('fr'));
        $this->assertSame('1 year 2 months', $projet->durationLabel('en'));

        // Projet en attente sans date de fin → durée « À déterminer / To be determined ».
        $projet = $this->makeProject($domaine, [
            'end_date' => null,
            'slug' => 'projet-' . bin2hex(random_bytes(4)),
        ]);
        $this->assertNull($projet->durationParts());
        $this->assertSame('À déterminer', $projet->durationLabel('fr'));
        $this->assertSame('To be determined', $projet->durationLabel('en'));
    }

    public function test_budget_label_et_statut_court(): void
    {
        $domaine = $this->makeDomain();

        $projet = $this->makeProject($domaine, [
            'budget_amount' => 541591,
            'budget_currency' => 'USD',
        ]);
        $this->assertSame('541 591 USD', $projet->budgetLabel('fr'));
        $this->assertSame('541,591 USD', $projet->budgetLabel('en'));

        $projet = $this->makeProject($domaine, [
            'budget_amount' => 450000000,
            'budget_currency' => 'EUR',
            'slug' => 'projet-' . bin2hex(random_bytes(4)),
        ]);
        $this->assertSame('450 000 000 €', $projet->budgetLabel('fr'));

        // Mapping maquette : completed/ongoing → d ; awaiting_funding/planned → w.
        $this->assertSame('d', $this->makeProject($domaine, ['status' => 'completed', 'slug' => 'projet-' . bin2hex(random_bytes(4))])->statut_court);
        $this->assertSame('d', $this->makeProject($domaine, ['status' => 'ongoing', 'slug' => 'projet-' . bin2hex(random_bytes(4))])->statut_court);
        $this->assertSame('w', $this->makeProject($domaine, ['status' => 'awaiting_funding', 'slug' => 'projet-' . bin2hex(random_bytes(4))])->statut_court);
        $this->assertSame('w', $this->makeProject($domaine, ['status' => 'planned', 'slug' => 'projet-' . bin2hex(random_bytes(4))])->statut_court);
    }

    public function test_index_charge_les_relations_domain_et_partners(): void
    {
        $domaine = $this->makeDomain('sante');
        $projet = $this->makeProject($domaine);

        $data = $this->getIndex();

        $premier = $data['projets']->first();
        $this->assertSame($projet->id, $premier->id);
        $this->assertTrue($premier->relationLoaded('domain'));
        $this->assertTrue($premier->relationLoaded('partners'));
        $this->assertSame('sante', $premier->domain->slug);
    }
}