<?php

namespace Tests\Feature;

use App\Filament\Resources\TeamMembers\Pages\CreateTeamMember;
use App\Filament\Resources\TeamMembers\Pages\EditTeamMember;
use App\Filament\Resources\TeamMembers\Pages\ListTeamMembers;
use App\Filament\Resources\TeamMembers\TeamMemberResource;
use App\Models\TeamMember;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class TeamMemberResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeMember(array $attributes = []): TeamMember
    {
        return TeamMember::create(array_merge([
            'name' => 'Ir MANEMA CIRHAHINGIRWA Roger',
            'role' => [
                'fr' => 'Représentant national',
                'en' => 'National Representative',
            ],
            'bio' => [
                'fr' => 'Ingénieur et fondateur de l\'organisation.',
                'en' => 'Engineer and founder of the organisation.',
            ],
            'quote' => [
                'fr' => 'Une citation du fondateur.',
                'en' => 'A quote from the founder.',
            ],
            'is_founder' => true,
            'position' => 1,
        ], $attributes));
    }

    public function test_la_ressource_est_rangee_dans_le_menu_de_l_organisation(): void
    {
        $this->assertSame('Équipe & gouvernance', TeamMemberResource::getNavigationLabel());
        $this->assertSame('Organisation', TeamMemberResource::getNavigationGroup());
        $this->assertSame('name', TeamMemberResource::getRecordTitleAttribute());
        $this->assertSame('membre', TeamMemberResource::getModelLabel());
    }

    public function test_la_liste_affiche_la_fonction_traduite_et_le_poste_pour_le_representant_national(): void
    {
        $membre = $this->makeMember();

        Livewire::test(ListTeamMembers::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$membre])
            ->assertSee('Ir MANEMA CIRHAHINGIRWA Roger')
            ->assertSee('Représentant national');   // traduction FR lue explicitement
    }

    public function test_les_membres_sont_tries_par_ordre_d_afficher_et_non_par_nom(): void
    {
        $deuxieme = $this->makeMember([
            'name' => 'ZAMUKA Jean',
            'position' => 2,
        ]);

        $premier = $this->makeMember([
            'name' => 'AKILIMALI Marie',
            'position' => 1,
        ]);

        // inOrder: true vérifie l'ORDRE des lignes : c'est la colonne « position »
        // qui commande (1 en tête), et non l'ordre alphabétique des noms.
        Livewire::test(ListTeamMembers::class)
            ->assertCanSeeTableRecords([$premier, $deuxieme], inOrder: true);
    }

    public function test_la_recherche_du_tableau_porte_sur_le_nom(): void
    {
        $membre = $this->makeMember();

        $autre = $this->makeMember([
            'name' => 'KAVIRA Espérance',
            'role' => ['fr' => 'Chargée de programmes', 'en' => 'Programme officer'],
            'position' => 3,
        ]);

        Livewire::test(ListTeamMembers::class)
            ->searchTable('KAVIRA')
            ->assertCanSeeTableRecords([$autre])
            ->assertCanNotSeeTableRecords([$membre]);
    }

    public function test_le_formulaire_contient_le_nom_la_fonction_bilingue_l_ordre_et_le_portrait(): void
    {
        $membre = $this->makeMember();

        Livewire::test(EditTeamMember::class, ['record' => $membre->getRouteKey()])
            ->assertOk()
            ->assertFormFieldExists('name')
            ->assertFormFieldExists('position')
            ->assertFormFieldExists('is_founder')
            ->assertFormFieldExists('role.fr')
            ->assertFormFieldExists('role.en')
            ->assertFormFieldExists('bio.fr')
            ->assertFormFieldExists('bio.en')
            ->assertFormFieldExists('quote.fr')
            ->assertFormFieldExists('quote.en')
            ->assertFormFieldExists('photo')
            ->assertSee('Français')
            ->assertSee('English')
            ->assertSee('Portrait')
            ->assertSee('Biographie (FR)')
            ->assertSee('Biography (EN)');
    }

    public function test_un_membre_non_fondateur_est_cree_avec_sa_fonction_mais_sans_biographie_ni_citation(): void
    {
        Livewire::test(CreateTeamMember::class)
            ->fillForm([
                'name' => 'BISIMWA Patrick',
                'role.fr' => 'Chargé des finances',
                'role.en' => 'Finance officer',
                // On poste pourtant bio/quote : ces champs étant pilotés par le
                // toggle « Fondateur » (dehydrated), ils ne doivent PAS être
                // persistés pour un membre non fondateur.
                'bio.fr' => 'Ne doit pas être enregistrée.',
                'bio.en' => 'Should not be saved.',
                'quote.fr' => 'Ni cette citation.',
                'quote.en' => 'Neither this quote.',
                'is_founder' => false,
                'position' => 4,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $membre = TeamMember::where('name', 'BISIMWA Patrick')->first();

        $this->assertNotNull($membre, 'Le membre aurait dû être créé.');
        $this->assertSame('Chargé des finances', $membre->getTranslation('role', 'fr'));
        $this->assertSame('Finance officer', $membre->getTranslation('role', 'en'));
        $this->assertNull($membre->getTranslation('bio', 'fr'));
        $this->assertNull($membre->getTranslation('bio', 'en'));
        $this->assertNull($membre->getTranslation('quote', 'fr'));
        $this->assertSame(4, $membre->position);
    }

    public function test_le_fondateur_seul_peut_renseigner_sa_biographie_et_sa_citation(): void
    {
        Livewire::test(CreateTeamMember::class)
            ->fillForm([
                'name' => 'Ir MANEMA CIRHAHINGIRWA Roger',
                'role.fr' => 'Représentant national',
                'role.en' => 'National Representative',
                'bio.fr' => 'Fondateur de l\'organisation le 10 mai 1995.',
                'bio.en' => 'Founder of the organisation on 10 May 1995.',
                'quote.fr' => 'Ce n\'est pas normal qu\'il y ait toujours des gens pour demander et d\'autres pour donner.',
                'quote.en' => 'It is not normal that there are always people asking and others giving.',
                'is_founder' => true,
                'position' => 1,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $membre = TeamMember::where('is_founder', true)->first();

        $this->assertNotNull($membre, 'Le fondateur aurait dû être créé.');
        $this->assertTrue($membre->is_founder);
        $this->assertSame('Fondateur de l\'organisation le 10 mai 1995.', $membre->getTranslation('bio', 'fr'));
        $this->assertSame('Founder of the organisation on 10 May 1995.', $membre->getTranslation('bio', 'en'));
        $this->assertSame('Ce n\'est pas normal qu\'il y ait toujours des gens pour demander et d\'autres pour donner.', $membre->getTranslation('quote', 'fr'));
        $this->assertSame('It is not normal that there are always people asking and others giving.', $membre->getTranslation('quote', 'en'));
    }

    public function test_il_ne_peut_y_avoir_qu_un_seul_fondateur(): void
    {
        $this->makeMember(); // Premier fondateur existant en base.

        Livewire::test(CreateTeamMember::class)
            ->fillForm([
                'name' => 'Deuxième fondateur',
                'is_founder' => true,
                'position' => 2,
            ])
            ->call('create')
            ->assertHasFormErrors(['is_founder']);

        $this->assertNull(
            TeamMember::where('name', 'Deuxième fondateur')->first(),
            'Le second fondateur ne doit pas être créé.'
        );
    }

    public function test_le_portrait_est_stocke_dans_sa_collection_et_remplace_le_precedent(): void
    {
        Storage::fake('public');

        $membre = $this->makeMember();

        $membre->addMedia(UploadedFile::fake()->image('manema-2025.jpg')->size(32))
            ->toMediaCollection('photo');

        $membre->addMedia(UploadedFile::fake()->image('manema-2026.jpg')->size(32))
            ->toMediaCollection('photo');

        $membre = $membre->refresh();

        // « photo » est une collection singleFile : un nouvel envoi remplace
        // le précédent, il n'y a donc jamais deux portraits en ligne.
        $this->assertCount(1, $membre->getMedia('photo'));
        $this->assertSame('manema-2026.jpg', $membre->getFirstMedia('photo')->file_name);
    }
}
