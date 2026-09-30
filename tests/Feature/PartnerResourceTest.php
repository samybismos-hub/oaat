<?php

namespace Tests\Feature;

use App\Enums\PartnerRole;
use App\Enums\ProjectStatus;
use App\Filament\Resources\Partners\Pages\CreatePartner;
use App\Filament\Resources\Partners\Pages\EditPartner;
use App\Filament\Resources\Partners\Pages\ListPartners;
use App\Filament\Resources\Partners\PartnerResource;
use App\Filament\Resources\Partners\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\RelationManagers\PartnersRelationManager;
use App\Models\Domain;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectPartner;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PartnerResourceTest extends TestCase
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

    private function makePartner(string $name = 'PNUD'): Partner
    {
        return Partner::create([
            'name' => $name,
            'description' => [
                'fr' => 'Programme des Nations Unies pour le Développement.',
                'en' => 'United Nations Development Programme.',
            ],
        ]);
    }

    private function makeProject(string $slug = 'ponts-kasandjala'): Project
    {
        $domain = Domain::create([
            'name' => ['fr' => 'Logistique & Infrastructures', 'en' => 'Logistics & Infrastructure'],
            'slug' => 'logistique-'.$slug,
            'position' => 1,
        ]);

        return Project::create([
            'domain_id' => $domain->id,
            'title' => [
                'fr' => 'Construction des ponts KASANDJALA I et II',
                'en' => 'Construction of the KASANDJALA I and II bridges',
            ],
            'slug' => $slug,
            'status' => ProjectStatus::Completed,
            'published_at' => '2007-05-01 00:00:00',
        ]);
    }

    /**
     * Lit le rôle tel qu'il est réellement stocké dans la table pivot.
     */
    private function pivotRole(Project $project, Partner $partner): ?string
    {
        return DB::table('project_partner')
            ->where('project_id', $project->id)
            ->where('partner_id', $partner->id)
            ->value('role');
    }

    public function test_les_valeurs_de_lenum_des_roles_correspondent_a_la_colonne_enum_de_la_base(): void
    {
        // Si ces valeurs changent, la base refuse l'enregistrement :
        // ce test sert de sonnette d'alarme.
        $this->assertSame('funder', PartnerRole::Funder->value);
        $this->assertSame('implementer', PartnerRole::Implementer->value);

        $this->assertSame('Bailleur / Financeur', PartnerRole::Funder->getLabel());
        $this->assertSame('Partenaire de mise en œuvre', PartnerRole::Implementer->getLabel());
        $this->assertSame('success', PartnerRole::Funder->getColor());
        $this->assertSame('info', PartnerRole::Implementer->getColor());
    }

    public function test_le_role_est_lu_depuis_la_table_pivot_sous_forme_denum(): void
    {
        $project = $this->makeProject();
        $partner = $this->makePartner();

        $project->partners()->attach($partner->id, ['role' => PartnerRole::Funder->value]);

        // La classe de pivot ProjectPartner (déclarée avec using()) est bien utilisée,
        // et son cast transforme « funder » en valeur d'enum.
        $pivot = $project->fresh()->partners->first()->pivot;

        $this->assertInstanceOf(ProjectPartner::class, $pivot);
        $this->assertSame(PartnerRole::Funder, $pivot->role);
        $this->assertSame('Bailleur / Financeur', $pivot->role->getLabel());
    }

    public function test_la_ressource_est_rangee_dans_le_menu_des_partenaires(): void
    {
        $this->assertSame('Partenaires & bailleurs', PartnerResource::getNavigationLabel());
        $this->assertSame('Partenaires', PartnerResource::getNavigationGroup());
        $this->assertSame('partenaires', PartnerResource::getPluralModelLabel());
        $this->assertSame('name', PartnerResource::getRecordTitleAttribute());

        // L'onglet des projets (et des rôles) est bien branché sur la fiche partenaire.
        $this->assertSame([ProjectsRelationManager::class], PartnerResource::getRelations());
    }

    public function test_la_liste_affiche_le_partenaire_et_compte_ses_projets(): void
    {
        $unicef = $this->makePartner('UNICEF');
        $fao = $this->makePartner('FAO');

        $project = $this->makeProject();

        $project->partners()->attach($unicef->id, ['role' => PartnerRole::Implementer->value]);

        Livewire::test(ListPartners::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$unicef, $fao])
            ->assertSee('UNICEF')
            ->assertTableColumnStateSet('projects_count', 1, $unicef)
            ->assertTableColumnStateSet('projects_count', 0, $fao);
    }

    public function test_la_recherche_du_tableau_porte_sur_le_nom_du_partenaire(): void
    {
        $unicef = $this->makePartner('UNICEF');
        $pnud = $this->makePartner('PNUD');

        // Ici, pas de chemin JSON : « name » est une vraie colonne texte.
        Livewire::test(ListPartners::class)
            ->searchTable('UNICEF')
            ->assertCanSeeTableRecords([$unicef])
            ->assertCanNotSeeTableRecords([$pnud]);
    }

    public function test_le_filtre_par_role_isole_les_bailleurs(): void
    {
        $bailleur = $this->makePartner('PNUD');
        $executant = $this->makePartner('AVSI');

        $project = $this->makeProject();

        $project->partners()->attach($bailleur->id, ['role' => PartnerRole::Funder->value]);
        $project->partners()->attach($executant->id, ['role' => PartnerRole::Implementer->value]);

        // Ce filtre traverse la table pivot (via whereHas) : c'est ce test qui le prouve.
        Livewire::test(ListPartners::class)
            ->filterTable('role', PartnerRole::Funder->value)
            ->assertCanSeeTableRecords([$bailleur])
            ->assertCanNotSeeTableRecords([$executant]);
    }

    public function test_le_formulaire_contient_les_champs_attendus(): void
    {
        $partner = $this->makePartner();

        Livewire::test(EditPartner::class, ['record' => $partner->getKey()])
            ->assertOk()
            ->assertFormFieldExists('name')
            ->assertFormFieldExists('logo')
            ->assertFormFieldExists('description.fr')
            ->assertFormFieldExists('description.en')
            ->assertSee('Identité du partenaire')
            ->assertSee('Français')
            ->assertSee('English');
    }

    public function test_un_partenaire_est_cree_avec_ses_deux_langues(): void
    {
        Livewire::test(CreatePartner::class)
            ->fillForm([
                'name' => 'Louvain Développement',
                'description.fr' => 'ONG belge de développement.',
                'description.en' => 'Belgian development NGO.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $partner = Partner::where('name', 'Louvain Développement')->first();

        $this->assertNotNull($partner, 'Le partenaire aurait dû être créé.');
        $this->assertSame('ONG belge de développement.', $partner->getTranslation('description', 'fr'));
        $this->assertSame('Belgian development NGO.', $partner->getTranslation('description', 'en'));
    }

    public function test_le_nom_du_partenaire_est_unique(): void
    {
        $this->makePartner('PNUD');

        Livewire::test(CreatePartner::class)
            ->fillForm(['name' => 'PNUD'])
            ->call('create')
            ->assertHasFormErrors(['name']);
    }

    public function test_le_logo_est_stocke_dans_sa_collection_et_remplace_le_precedent(): void
    {
        // Storage::fake : les fichiers partent dans un dossier temporaire,
        // rien n'est écrit dans storage/app/public pendant les tests.
        Storage::fake('public');

        $partner = $this->makePartner();

        $partner->addMediaFromString('contenu-du-logo')
            ->usingFileName('pnud.png')
            ->toMediaCollection('logo');

        $partner = $partner->refresh();

        $this->assertCount(1, $partner->getMedia('logo'));
        $this->assertSame('pnud.png', $partner->getFirstMedia('logo')->file_name);
        Storage::disk('public')->assertExists($partner->getFirstMedia('logo')->getPathRelativeToRoot());

        // La collection « logo » est singleFile : un deuxième envoi remplace le premier.
        $partner->addMediaFromString('contenu-du-nouveau-logo')
            ->usingFileName('pnud-2026.png')
            ->toMediaCollection('logo');

        $partner = $partner->refresh();

        $this->assertCount(1, $partner->getMedia('logo'));
        $this->assertSame('pnud-2026.png', $partner->getFirstMedia('logo')->file_name);
    }

    public function test_le_role_est_modifiable_en_ligne_depuis_la_fiche_du_partenaire(): void
    {
        $partner = $this->makePartner('PNUD');
        $project = $this->makeProject();

        $project->partners()->attach($partner->id, ['role' => PartnerRole::Implementer->value]);

        Livewire::test(ProjectsRelationManager::class, [
            'ownerRecord' => $partner,
            'pageClass' => EditPartner::class,
        ])
            ->assertOk()
            ->assertCanSeeTableRecords([$project])
            ->assertTableColumnStateSet('role', PartnerRole::Implementer->value, $project)
            // C'est cet appel qui prouve que la colonne éditable écrit bien dans la
            // table pivot (et non dans une colonne du projet).
            ->call('updateTableColumnState', 'role', (string) $project->getKey(), PartnerRole::Funder->value);

        $this->assertSame(PartnerRole::Funder->value, $this->pivotRole($project, $partner));
    }

    public function test_le_role_est_modifiable_en_ligne_depuis_la_fiche_du_projet_sans_toucher_aux_autres_partenaires(): void
    {
        $project = $this->makeProject();
        $pnud = $this->makePartner('PNUD');
        $avsi = $this->makePartner('AVSI');

        $project->partners()->attach($pnud->id, ['role' => PartnerRole::Implementer->value]);
        $project->partners()->attach($avsi->id, ['role' => PartnerRole::Implementer->value]);

        Livewire::test(PartnersRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => EditProject::class,
        ])
            ->assertOk()
            ->assertCanSeeTableRecords([$pnud, $avsi])
            ->call('updateTableColumnState', 'role', (string) $pnud->getKey(), PartnerRole::Funder->value);

        $this->assertSame(PartnerRole::Funder->value, $this->pivotRole($project, $pnud));

        // Point capital : la ligne voisine ne doit pas avoir bougé. Laravel construit
        // la mise à jour sur le couple (project_id, partner_id) et jamais sur le seul
        // project_id, ce qui aurait modifié le rôle de tous les partenaires du projet.
        $this->assertSame(PartnerRole::Implementer->value, $this->pivotRole($project, $avsi));
    }

    public function test_un_partenaire_est_associe_au_projet_avec_son_role(): void
    {
        $project = $this->makeProject();
        $partner = $this->makePartner('FAO');

        Livewire::test(PartnersRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => EditProject::class,
        ])
            ->assertOk()
            // Pour viser une action de l'en-tête du tableau (et non une action de
            // page), il faut le dire explicitement avec TestAction::…->table().
            ->assertActionVisible(TestAction::make('attach')->table())
            // Le champ « role » du formulaire d'association porte le nom d'une colonne
            // pivot : Filament le transmet donc à attach() comme donnée de pivot.
            ->callAction(TestAction::make('attach')->table(), [
                'recordId' => $partner->getKey(),
                'role' => PartnerRole::Funder->value,
            ]);

        $this->assertDatabaseHas('project_partner', [
            'project_id' => $project->id,
            'partner_id' => $partner->id,
            'role' => PartnerRole::Funder->value,
        ]);
    }
}
