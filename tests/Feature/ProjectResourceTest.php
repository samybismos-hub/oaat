<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Projects\RelationManagers\AlbumsRelationManager;
use App\Filament\Resources\Projects\RelationManagers\PartnersRelationManager;
use App\Models\Album;
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
            // La galerie ne se téléverse plus ici : elle se gère dans l'onglet
            // « Albums photo » de la fiche (gestionnaire de relations).
            ->assertFormFieldDoesNotExist('photos')
            ->assertSee('Français')
            ->assertSee('English')
            ->assertSee('Domaine d\'intervention')
            ->assertSee('Photos & illustrations')
            ->assertDontSee('Galerie photos');
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

    /**
     * Depuis l'option A, la galerie d'un projet vit dans ses albums (collection
     * « photos » des albums liés), et plus dans une collection « photos » du
     * projet lui-même : un seul canal pour toutes les images.
     *
     * Un vrai PNG est utilisé : contrairement à un upload factice, il passe les
     * conversions Spatie (thumb/card, non-queued) sans erreur du pilote GD.
     */
    public function test_la_galerie_dun_projet_est_alimentee_par_ses_albums(): void
    {
        // Storage::fake : les fichiers partent dans un dossier temporaire,
        // rien n'est écrit dans storage/app/public pendant les tests.
        Storage::fake('public');

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');

        $domain = $this->makeDomain();
        $project = $this->makeProject($domain);

        $project->addMediaFromString($png)
            ->usingFileName('pont-kasandjala.jpg')
            ->toMediaCollection('cover');

        $album = Album::create([
            'title' => ['fr' => 'Chantier des ponts', 'en' => 'Bridges building site'],
            'slug' => 'chantier-des-ponts',
            'project_id' => $project->id,
        ]);

        $album->addMediaFromString($png)
            ->usingFileName('chantier-1.jpg')
            ->toMediaCollection('photos');
        $album->addMediaFromString($png)
            ->usingFileName('chantier-2.jpg')
            ->toMediaCollection('photos');

        $project = $project->refresh();

        // La couverture reste une collection singleFile sur le projet…
        $cover = $project->getFirstMedia('cover');
        $this->assertCount(1, $project->getMedia('cover'));
        $this->assertNotNull($cover);
        $this->assertSame('pont-kasandjala.jpg', $cover->file_name);
        $this->assertNotNull($cover->getUrl());
        Storage::disk('public')->assertExists($cover->getPathRelativeToRoot());

        // …et la galerie vit dans les albums rattachés, plus dans une
        // collection « photos » du projet (canal unique pour les images).
        $this->assertCount(1, $project->albums);
        $this->assertCount(2, $album->getMedia('photos'));
        $this->assertSame($project->id, $album->project_id);
        $this->assertCount(0, $project->getMedia('photos'));
    }

    /**
     * L'onglet « Albums photo » de la fiche projet remplace l'ancien champ
     * « Galerie photos » du formulaire : on peut créer un album lié au projet
     * sans quitter la fiche, et il est immédiatement attaché au projet.
     */
    public function test_l_onglet_albums_est_branche_sur_la_fiche_et_permet_de_creer_un_album(): void
    {
        $domain = $this->makeDomain();
        $project = $this->makeProject($domain);

        $this->assertContains(AlbumsRelationManager::class, ProjectResource::getRelations());

        Livewire::test(AlbumsRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => EditProject::class,
        ])
            ->assertOk()
            ->assertTableActionExists('create')
            ->callTableAction('create', data: [
                'title.fr' => 'Album de chantier',
                'title.en' => 'Worksite album',
                'slug' => 'album-de-chantier',
            ])
            ->assertHasNoTableActionErrors();

        $album = $project->albums()->first();

        $this->assertNotNull($album, 'L\'album aurait dû être créé depuis l\'onglet.');
        $this->assertSame('Album de chantier', $album->getTranslation('title', 'fr'));
        $this->assertSame('Worksite album', $album->getTranslation('title', 'en'));
        $this->assertSame('album-de-chantier', $album->slug);
        $this->assertSame($project->id, $album->project_id);
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
