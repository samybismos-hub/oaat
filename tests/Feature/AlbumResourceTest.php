<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Filament\Resources\Albums\AlbumResource;
use App\Filament\Resources\Albums\Pages\CreateAlbum;
use App\Filament\Resources\Albums\Pages\EditAlbum;
use App\Filament\Resources\Albums\Pages\ListAlbums;
use App\Models\Album;
use App\Models\Domain;
use App\Models\Project;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AlbumResourceTest extends TestCase
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

    private function makeProject(): Project
    {
        $domain = Domain::create([
            'name' => ['fr' => 'Éducation & Formation', 'en' => 'Education & Training'],
            'slug' => 'education',
            'position' => 1,
        ]);

        return Project::create([
            'domain_id' => $domain->id,
            'title' => [
                'fr' => "Construction d'une école à Karhale",
                'en' => 'Construction of a school in Karhale',
            ],
            'slug' => 'ecole-karhale',
            'status' => ProjectStatus::Completed,
            'published_at' => '2021-09-01 00:00:00',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeAlbum(array $attributes = []): Album
    {
        return Album::create(array_merge([
            'title' => [
                'fr' => "Chantier de l'école de Karhale",
                'en' => 'Karhale school building site',
            ],
            'project_id' => null,
        ], $attributes));
    }

    public function test_la_ressource_est_rangee_dans_le_menu_des_medias_et_documents(): void
    {
        $this->assertSame('Albums photo', AlbumResource::getNavigationLabel());
        $this->assertSame('Médias & documents', AlbumResource::getNavigationGroup());
    }

    public function test_la_liste_affiche_l_album_le_projet_rattache_et_le_nombre_de_photos(): void
    {
        Storage::fake('public');

        $projet = $this->makeProject();

        $album = $this->makeAlbum(['project_id' => $projet->id]);

        $album->addMediaFromString('photo-1')->usingFileName('chantier-1.jpg')->toMediaCollection('photos');
        $album->addMediaFromString('photo-2')->usingFileName('chantier-2.jpg')->toMediaCollection('photos');

        $general = $this->makeAlbum([
            'title' => ['fr' => "Vie de l'organisation", 'en' => 'Life of the organisation'],
        ]);

        Livewire::test(ListAlbums::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$album, $general])
            ->assertSee("Chantier de l'école de Karhale")
            ->assertSee("Construction d'une école à Karhale")   // titre FR du projet rattaché
            ->assertSee('Album général')                        // aucun projet rattaché
            // Le compteur de photos vient d'une sous-requête COUNT : 2 ici, 0 là.
            ->assertTableColumnStateSet('media_count', 2, $album)
            ->assertTableColumnStateSet('media_count', 0, $general);
    }

    public function test_la_recherche_du_tableau_porte_sur_le_nom_francais_de_l_album(): void
    {
        $album = $this->makeAlbum();

        $autre = $this->makeAlbum([
            'title' => ['fr' => 'Réhabilitation de la route de Numbi', 'en' => 'Numbi road rehabilitation'],
        ]);

        Livewire::test(ListAlbums::class)
            ->searchTable('route de Numbi')
            ->assertCanSeeTableRecords([$autre])
            ->assertCanNotSeeTableRecords([$album]);
    }

    public function test_le_filtre_isole_les_albums_generaux_de_ceux_rattaches_a_un_projet(): void
    {
        $album = $this->makeAlbum(['project_id' => $this->makeProject()->id]);

        $general = $this->makeAlbum([
            'title' => ['fr' => "Vie de l'organisation", 'en' => 'Life of the organisation'],
        ]);

        Livewire::test(ListAlbums::class)
            ->filterTable('project_id', false)   // false = colonne NULL = album général
            ->assertCanSeeTableRecords([$general])
            ->assertCanNotSeeTableRecords([$album]);
    }

    public function test_le_formulaire_contient_le_nom_bilingue_le_projet_et_les_photos(): void
    {
        $album = $this->makeAlbum();

        Livewire::test(EditAlbum::class, ['record' => $album->getRouteKey()])
            ->assertOk()
            ->assertFormFieldExists('title.fr')
            ->assertFormFieldExists('title.en')
            ->assertFormFieldExists('project_id')
            ->assertFormFieldExists('photos')
            ->assertSee('Photos de l\'album');
    }

    public function test_le_menu_deroulant_des_projets_affiche_le_titre_francais_et_non_le_json(): void
    {
        $projet = $this->makeProject();

        // L'album est bien rattaché : c'est le libellé de l'option sélectionnée
        // qui doit s'afficher en français, et non le JSON de la colonne.
        $album = $this->makeAlbum(['project_id' => $projet->id]);

        Livewire::test(EditAlbum::class, ['record' => $album->getRouteKey()])
            ->assertOk()
            // Le libellé est rendu dans les données du menu déroulant, où
            // l'apostrophe est encodée (d\u0027une) : on cherche donc un extrait
            // sans apostrophe, mais qui n'existe que dans le titre français
            // (le titre anglais dit « Construction of a school in Karhale »).
            ->assertSee('école à Karhale')
            // Et surtout : aucun composant ne doit afficher le JSON brut de la
            // colonne « title » (les données internes du composant, elles, sont
            // écartées automatiquement par Livewire lors de ces vérifications).
            ->assertDontSee('&quot;fr&quot;');
    }

    public function test_un_album_est_cree_et_rattache_a_un_projet(): void
    {
        $projet = $this->makeProject();

        Livewire::test(CreateAlbum::class)
            ->fillForm([
                'title.fr' => 'Forages de Fizi',
                'title.en' => 'Fizi boreholes',
                'project_id' => $projet->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $album = Album::where('title->fr', 'Forages de Fizi')->first();

        $this->assertNotNull($album, 'L\'album aurait dû être créé.');
        $this->assertSame('Fizi boreholes', $album->getTranslation('title', 'en'));
        $this->assertSame($projet->id, $album->project_id);
    }

    public function test_les_photos_sont_stockees_dans_la_collection_photos(): void
    {
        Storage::fake('public');

        $album = $this->makeAlbum();

        $album->addMediaFromString('premiere-photo')
            ->usingFileName('chantier-1.jpg')
            ->toMediaCollection('photos');

        $album->addMediaFromString('seconde-photo')
            ->usingFileName('chantier-2.jpg')
            ->toMediaCollection('photos');

        $album = $album->refresh();

        // Contrairement au logo d'un partenaire, un album accepte plusieurs
        // photos : la collection n'est pas déclarée singleFile.
        $this->assertCount(2, $album->getMedia('photos'));
        Storage::disk('public')->assertExists($album->getFirstMedia('photos')->getPathRelativeToRoot());
    }
}
