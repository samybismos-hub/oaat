<?php

namespace Tests\Feature;

use App\Filament\Resources\Actualities\ActualityResource;
use App\Filament\Resources\Actualities\Pages\CreateActuality;
use App\Filament\Resources\Actualities\Pages\EditActuality;
use App\Filament\Resources\Actualities\Pages\ListActualities;
use App\Models\Actuality;
use App\Models\User;
use Database\Seeders\ActualitySeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ActualityResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    /**
     * La colonne slug est unique en base : chaque fiche construite par ce test
     * reçoit donc son propre identifiant, sans avoir à le répéter partout.
     */
    private int $compteur = 0;

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
    private function makeActuality(array $attributes = []): Actuality
    {
        return Actuality::create(array_merge([
            'slug' => 'actualite-de-test-'.(++$this->compteur),
            'title' => [
                'fr' => "L'OAAT au service des communautés de l'Est",
                'en' => 'OAAT serving communities in eastern DR Congo',
            ],
            'body' => [
                'fr' => '<p>Depuis 1995, l\'OAAT intervient aux côtés des communautés.</p>',
                'en' => '<p>Since 1995, OAAT has been working alongside communities.</p>',
            ],
            'published_at' => now()->subDay(),
        ], $attributes));
    }

    public function test_le_scope_published_ne_retient_que_les_actualites_deja_datees(): void
    {
        $brouillon = $this->makeActuality(['published_at' => null]);
        $programmee = $this->makeActuality(['published_at' => now()->addWeek()]);
        $enLigne = $this->makeActuality(['published_at' => now()->subDay()]);

        $publiees = Actuality::query()->published()->pluck('id');

        $this->assertCount(1, $publiees, 'Une seule des trois actualités est publiée.');
        $this->assertTrue($publiees->contains($enLigne->id));
        $this->assertFalse($publiees->contains($brouillon->id));
        $this->assertFalse(
            $publiees->contains($programmee->id),
            'Une actualité programmée ne doit pas être visible avant sa date.'
        );
    }

    public function test_la_ressource_est_rangee_dans_le_menu_de_communication(): void
    {
        $this->assertSame('Actualités', ActualityResource::getNavigationLabel());
        $this->assertSame('Communication', ActualityResource::getNavigationGroup());
        $this->assertSame('title', ActualityResource::getRecordTitleAttribute());
    }

    public function test_la_liste_distingue_le_brouillon_la_programmation_et_la_mise_en_ligne(): void
    {
        $brouillon = $this->makeActuality(['published_at' => null]);
        $programmee = $this->makeActuality(['published_at' => now()->addWeek()]);
        $enLigne = $this->makeActuality(['published_at' => now()->subDay()]);

        Livewire::test(ListActualities::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$brouillon, $programmee, $enLigne])
            ->assertTableColumnStateSet('etat', 'Brouillon', $brouillon)
            ->assertTableColumnStateSet('etat', 'Programmée', $programmee)
            ->assertTableColumnStateSet('etat', 'En ligne', $enLigne);
    }

    public function test_la_recherche_couvre_le_titre_francais_et_le_titre_anglais(): void
    {
        $kindu = $this->makeActuality([
            'slug' => 'nos-actions-a-kindu',
            'title' => ['fr' => 'Nos actions à Kindu', 'en' => 'Our Kindu programme'],
        ]);

        $bunia = $this->makeActuality([
            'slug' => 'rapport-de-terrain-a-bunia',
            'title' => ['fr' => 'Rapport de terrain à Bunia', 'en' => 'Bunia field report'],
        ]);

        // « Rapport de terrain » n'existe que dans le titre français : sans la
        // recherche sur la colonne JSON title->fr, ce test échouerait.
        Livewire::test(ListActualities::class)
            ->searchTable('Rapport de terrain')
            ->assertCanSeeTableRecords([$bunia])
            ->assertCanNotSeeTableRecords([$kindu]);

        // « field report » n'existe que dans le titre anglais : même démonstration
        // pour la colonne title->en.
        Livewire::test(ListActualities::class)
            ->searchTable('field report')
            ->assertCanSeeTableRecords([$bunia])
            ->assertCanNotSeeTableRecords([$kindu]);
    }

    public function test_le_filtre_etat_separe_les_trois_situations(): void
    {
        $brouillon = $this->makeActuality(['published_at' => null]);
        $programmee = $this->makeActuality(['published_at' => now()->addWeek()]);
        $enLigne = $this->makeActuality(['published_at' => now()->subDay()]);

        Livewire::test(ListActualities::class)
            ->filterTable('etat', 'brouillon')
            ->assertCanSeeTableRecords([$brouillon])
            ->assertCanNotSeeTableRecords([$programmee, $enLigne]);

        Livewire::test(ListActualities::class)
            ->filterTable('etat', 'programmee')
            ->assertCanSeeTableRecords([$programmee])
            ->assertCanNotSeeTableRecords([$brouillon, $enLigne]);

        Livewire::test(ListActualities::class)
            ->filterTable('etat', 'enligne')
            ->assertCanSeeTableRecords([$enLigne])
            ->assertCanNotSeeTableRecords([$brouillon, $programmee]);
    }

    public function test_le_formulaire_contient_le_titre_bilingue_le_texte_la_couverture_la_date_et_le_slug(): void
    {
        $actuality = $this->makeActuality();

        Livewire::test(EditActuality::class, ['record' => $actuality->getRouteKey()])
            ->assertOk()
            ->assertFormFieldExists('title.fr')
            ->assertFormFieldExists('title.en')
            ->assertFormFieldExists('body.fr')
            ->assertFormFieldExists('body.en')
            ->assertFormFieldExists('cover')
            ->assertFormFieldExists('published_at')
            ->assertFormFieldExists('slug')
            ->assertSee('Image de couverture')
            ->assertSee('Date de publication');
    }

    public function test_le_slug_est_propose_a_la_creation_mais_jamais_ecrase_sur_une_fiche_existante(): void
    {
        Livewire::test(CreateActuality::class)
            ->fillForm(['title.fr' => 'Nos actions à Kindu'])
            ->assertFormSet(['slug' => 'nos-actions-a-kindu']);

        // Sur une fiche qui vit déjà, corriger le titre ne doit pas déplacer
        // l'adresse de l'article : les liens partagés doivent continuer à marcher.
        $actuality = $this->makeActuality(['slug' => 'ancien-slug-stable']);

        Livewire::test(EditActuality::class, ['record' => $actuality->getRouteKey()])
            ->fillForm(['title.fr' => 'Titre corrigé après publication'])
            ->assertFormSet(['slug' => 'ancien-slug-stable']);
    }

    public function test_une_actualite_est_creee_avec_ses_deux_langues_et_redirigee_vers_sa_fiche(): void
    {
        Livewire::test(CreateActuality::class)
            ->fillForm([
                'title.fr' => 'Nos actions à Kindu',
                'title.en' => 'Our actions in Kindu',
                'body.fr' => '<p>Un programme de sécurité alimentaire.</p>',
                'published_at' => '2026-03-01 08:30:00',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(ActualityResource::getUrl('edit', ['record' => 'nos-actions-a-kindu']));

        $actuality = Actuality::query()->where('slug', 'nos-actions-a-kindu')->first();

        $this->assertNotNull($actuality, "L'actualité aurait dû être créée.");
        $this->assertSame('Nos actions à Kindu', $actuality->getTranslation('title', 'fr'));
        $this->assertSame('Our actions in Kindu', $actuality->getTranslation('title', 'en'));
        $this->assertNotNull($actuality->published_at);
    }

    public function test_la_couverture_est_stockee_dans_la_collection_cover_et_remplacee(): void
    {
        Storage::fake('public');

        $actuality = $this->makeActuality();

        $actuality->addMediaFromString('premiere-image')
            ->usingFileName('couverture-1.jpg')
            ->toMediaCollection('cover');

        $actuality->addMediaFromString('seconde-image')
            ->usingFileName('couverture-2.jpg')
            ->toMediaCollection('cover');

        $actuality = $actuality->refresh();

        // « cover » est une collection singleFile : jamais deux images à la fois.
        $this->assertCount(1, $actuality->getMedia('cover'));
        $this->assertSame('couverture-2.jpg', $actuality->getFirstMedia('cover')->file_name);

        Livewire::test(ListActualities::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$actuality]);
    }

    public function test_le_badge_du_menu_ne_compte_que_les_actualites_en_ligne(): void
    {
        // Aucune actualité publiée : pas de badge du tout, plutôt qu'un « 0 ».
        $this->assertNull(ActualityResource::getNavigationBadge());

        $this->makeActuality(['published_at' => null]);
        $this->makeActuality(['published_at' => now()->addWeek()]);

        $this->assertNull(ActualityResource::getNavigationBadge());

        $this->makeActuality(['published_at' => now()->subDay()]);

        $this->assertSame('1', ActualityResource::getNavigationBadge());
        $this->assertSame('success', ActualityResource::getNavigationBadgeColor());
    }

    public function test_le_seeder_est_idempotent_et_laisse_les_actualites_en_brouillon(): void
    {
        // updateOrCreate : relancer le seeder ne doit jamais créer de doublon.
        $this->seed(ActualitySeeder::class);
        $this->seed(ActualitySeeder::class);

        $this->assertSame(2, Actuality::count());

        // Les deux actualités du seeder restent invisibles sur le site public
        // tant que le client ne les a pas datées.
        $this->assertSame(0, Actuality::query()->published()->count());
        $this->assertDatabaseHas('actualities', [
            'slug' => 'oaat-au-service-des-communautes-depuis-1995',
            'published_at' => null,
        ]);
    }
}
