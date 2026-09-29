<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Projects\RelationManagers\PartnersRelationManager;
use App\Models\Domain;
use App\Models\Project;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Hors requête HTTP, Filament ne sait pas quel panneau est actif :
        // il faut le lui dire, sinon les URL des ressources sont vides.
        Filament::setCurrentPanel('admin');

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    private function makeDomain(): Domain
    {
        return Domain::create([
            'name' => ['fr' => 'Logistique & Infrastructures', 'en' => 'Logistics & Infrastructure'],
            'slug' => 'logistique',
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
            'title' => [
                'fr' => 'Construction des ponts KASANDJALA I et II',
                'en' => 'Construction of the KASANDJALA I and II bridges',
            ],
            'slug' => 'ponts-kasandjala',
            'status' => ProjectStatus::Completed,
            'country' => 'RD Congo',
            'city' => 'Sebele, Fizi',
            'start_date' => '2007-01-01',
            'end_date' => '2007-05-01',
            'budget_amount' => 104918,
            'budget_currency' => 'USD',
            'beneficiaries_count' => 15800,
            'is_featured' => true,
            'published_at' => '2007-05-01 00:00:00',
        ], $attributes));
    }

    public function test_les_valeurs_de_lenum_correspondent_a_la_colonne_enum_de_la_base(): void
    {
        // Si ces valeurs changent, la base refuse l'enregistrement :
        // ce test sert de sonnette d'alarme.
        $this->assertSame('planned', ProjectStatus::Planned->value);
        $this->assertSame('ongoing', ProjectStatus::Ongoing->value);
        $this->assertSame('completed', ProjectStatus::Completed->value);
        $this->assertSame('awaiting_funding', ProjectStatus::AwaitingFunding->value);

        // Les libellés et couleurs viennent des contrats HasLabel / HasColor.
        $this->assertSame('Réalisé', ProjectStatus::Completed->getLabel());
        $this->assertSame('En attente de financement', ProjectStatus::AwaitingFunding->getLabel());
        $this->assertSame('success', ProjectStatus::Completed->getColor());
    }

    public function test_la_ressource_est_rangee_dans_le_menu_des_programmes(): void
    {
        $this->assertSame('Projets & réalisations', ProjectResource::getNavigationLabel());
        $this->assertSame('Structure & Programmes', ProjectResource::getNavigationGroup());
        $this->assertSame('title', ProjectResource::getRecordTitleAttribute());
    }

    public function test_la_liste_affiche_le_titre_francais_le_domaine_et_le_statut_traduit(): void
    {
        $domain = $this->makeDomain();
        $project = $this->makeProject($domain);

        Livewire::test(ListProjects::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$project])
            ->assertSee('Construction des ponts KASANDJALA I et II')   // traduction FR explicite
            ->assertSee('Logistique & Infrastructures')                 // nom FR du domaine
            ->assertSee('Réalisé')                                      // libellé FR du statut
            ->assertSee('104 918 USD');                                 // budget mis en forme
    }

    public function test_la_recherche_du_tableau_porte_sur_le_titre_francais_stocke_en_json(): void
    {
        $domain = $this->makeDomain();
        $ponts = $this->makeProject($domain);

        $eau = $this->makeProject($domain, [
            'slug' => 'eau-potable-sebele',
            'title' => ['fr' => 'Approvisionnement en eau potable à Sebele', 'en' => 'Drinking water supply in Sebele'],
        ]);

        // La recherche utilise un chemin JSON : where('title->fr', 'like', …).
        // C'est ce test qui prouve que la syntaxe fonctionne vraiment.
        Livewire::test(ListProjects::class)
            ->searchTable('eau potable')
            ->assertCanSeeTableRecords([$eau])
            ->assertCanNotSeeTableRecords([$ponts]);
    }

    public function test_le_filtre_par_domaine_isole_les_projets(): void
    {
        $logistique = $this->makeDomain();

        $wash = Domain::create([
            'name' => ['fr' => 'Eau, Hygiène & Assainissement', 'en' => 'Water, Hygiene & Sanitation'],
            'slug' => 'wash',
            'position' => 2,
        ]);

        $ponts = $this->makeProject($logistique);

        $eau = $this->makeProject($wash, [
            'slug' => 'eau-potable-sebele',
            'title' => ['fr' => 'Approvisionnement en eau potable à Sebele', 'en' => 'Drinking water supply in Sebele'],
        ]);

        Livewire::test(ListProjects::class)
            ->filterTable('domain_id', $wash->id)
            ->assertCanSeeTableRecords([$eau])
            ->assertCanNotSeeTableRecords([$ponts]);
    }

    public function test_les_filtres_ternaires_isolent_les_projets_a_la_une_et_les_brouillons(): void
    {
        $domain = $this->makeDomain();

        $publie = $this->makeProject($domain);

        $brouillon = $this->makeProject($domain, [
            'slug' => 'projet-en-preparation',
            'title' => ['fr' => 'Projet en préparation', 'en' => 'Project being prepared'],
            'status' => ProjectStatus::AwaitingFunding,
            'is_featured' => false,
            'published_at' => null,
        ]);

        Livewire::test(ListProjects::class)
            ->filterTable('is_featured', true)
            ->assertCanSeeTableRecords([$publie])
            ->assertCanNotSeeTableRecords([$brouillon]);

        Livewire::test(ListProjects::class)
            ->filterTable('published_at', false)   // false = colonne NULL = brouillon
            ->assertCanSeeTableRecords([$brouillon])
            ->assertCanNotSeeTableRecords([$publie]);
    }

    public function test_le_formulaire_contient_tous_les_blocs_et_champs_attendus(): void
    {
        $domain = $this->makeDomain();
        $project = $this->makeProject($domain);

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->assertOk()
            ->assertFormFieldExists('title.fr')
            ->assertFormFieldExists('title.en')
            ->assertFormFieldExists('objectives.fr')
            ->assertFormFieldExists('beneficiaries.en')
            ->assertFormFieldExists('results.fr')
            ->assertFormFieldExists('domain_id')
            ->assertFormFieldExists('slug')
            ->assertFormFieldExists('country')
            ->assertFormFieldExists('city')
            ->assertFormFieldExists('start_date')
            ->assertFormFieldExists('end_date')
            ->assertFormFieldExists('budget_amount')
            ->assertFormFieldExists('budget_currency')
            ->assertFormFieldExists('beneficiaries_count')
            ->assertFormFieldExists('beneficiaries_unit')
            ->assertFormFieldExists('status')
            ->assertFormFieldExists('is_featured')
            ->assertFormFieldExists('published_at')
            ->assertFormFieldExists('cover')
            ->assertFormFieldExists('photos')
            ->assertSee('Français')
            ->assertSee('English')
            ->assertSee('Domaine d\'intervention')
            ->assertSee('Photos & illustrations')
            ->assertSee('Galerie photos');
    }

    public function test_le_menu_deroulant_des_domaines_affiche_le_nom_francais_et_non_le_json(): void
    {
        $domain = $this->makeDomain();
        $project = $this->makeProject($domain);

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->assertOk()
            ->assertSee('Logistique & Infrastructures')   // le libellé lisible
            ->assertDontSee('&quot;fr&quot;');             // et surtout pas le JSON brut
    }

    public function test_un_projet_est_cree_avec_ses_deux_langues_et_son_statut(): void
    {
        $domain = $this->makeDomain();

        Livewire::test(CreateProject::class)
            ->fillForm([
                'title.fr' => 'Nouveau projet de contrôle',
                'title.en' => 'New control project',
                'domain_id' => $domain->id,
                'slug' => 'nouveau-projet-de-controle',
                'status' => ProjectStatus::Ongoing->value,
                'budget_amount' => 12500.5,
                'budget_currency' => 'USD',
                'beneficiaries_count' => 300,
                'beneficiaries_unit' => 'ménages',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $project = Project::where('slug', 'nouveau-projet-de-controle')->first();

        $this->assertNotNull($project, 'Le projet aurait dû être créé.');
        $this->assertSame('Nouveau projet de contrôle', $project->getTranslation('title', 'fr'));
        $this->assertSame('New control project', $project->getTranslation('title', 'en'));
        $this->assertSame(ProjectStatus::Ongoing, $project->status);
        $this->assertSame('12500.50', $project->budget_amount);
    }

    public function test_une_photo_de_couverture_et_des_photos_de_galerie_peuvent_etre_attachees(): void
    {
        // Storage::fake : les fichiers partent dans un dossier temporaire,
        // rien n'est écrit dans storage/app/public pendant les tests.
        Storage::fake('public');

        $domain = $this->makeDomain();
        $project = $this->makeProject($domain);

        $project->addMediaFromString('contenu-de-la-couverture')
            ->usingFileName('pont-kasandjala.jpg')
            ->toMediaCollection('cover');

        $project->addMediaFromString('contenu-photo-1')
            ->usingFileName('chantier-1.jpg')
            ->toMediaCollection('photos');

        $project->addMediaFromString('contenu-photo-2')
            ->usingFileName('chantier-2.jpg')
            ->toMediaCollection('photos');

        $project = $project->refresh();

        // « cover » est une collection singleFile : une seule image à la fois.
        $this->assertCount(1, $project->getMedia('cover'));
        $this->assertCount(2, $project->getMedia('photos'));

        $cover = $project->getFirstMedia('cover');

        $this->assertNotNull($cover);
        $this->assertSame('pont-kasandjala.jpg', $cover->file_name);
        $this->assertNotNull($cover->getUrl());
        Storage::disk('public')->assertExists($cover->getPathRelativeToRoot());
    }

    /**
     * Les partenaires d'un projet ne se gèrent plus dans le formulaire principal,
     * mais dans l'onglet « Partenaires & rôles » (gestionnaire de relations) :
     * seul ce dernier permet de saisir le rôle de chacun, et il évite qu'un
     * enregistrement du formulaire, resté sur une liste périmée, ne détache un
     * partenaire ajouté entre-temps depuis l'onglet.
     */
    public function test_les_partenaires_du_projet_se_gerent_dans_l_onglet_dedie_et_non_dans_le_formulaire(): void
    {
        $domain = $this->makeDomain();
        $project = $this->makeProject($domain);

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->assertOk()
            ->assertFormFieldDoesNotExist('partners');

        $this->assertContains(PartnersRelationManager::class, ProjectResource::getRelations());
    }
}
