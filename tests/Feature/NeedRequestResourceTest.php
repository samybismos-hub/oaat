<?php

namespace Tests\Feature;

use App\Filament\Resources\NeedRequests\NeedRequestResource;
use App\Filament\Resources\NeedRequests\Pages\ListNeedRequests;
use App\Models\NeedRequest;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Livewire\Livewire;
use Tests\TestCase;

class NeedRequestResourceTest extends TestCase
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
    private function makeRequest(array $attributes = []): NeedRequest
    {
        $needRequest = NeedRequest::create(array_merge([
            'organization' => 'Association des jeunes de Fizi',
            'contact_name' => 'Jean BISIMWA',
            'email' => 'jean.bisimwa@example.org',
            'phone' => '+243 970 000 000',
            'need_type' => "Forage d'eau potable",
            'description' => "Notre village manque d'eau potable depuis la saison sèche.",
            'location' => 'Fizi, Sud-Kivu',
        ], Arr::except($attributes, 'created_at')));

        // « created_at » n'est pas fillable : on le fixe explicitement pour
        // tester le filtre de période sur des dates choisies.
        $needRequest->created_at = $attributes['created_at'] ?? '2026-02-10 09:00:00';
        $needRequest->save();

        return $needRequest;
    }

    public function test_la_ressource_est_rangee_dans_le_menu_des_demandes_recues(): void
    {
        $this->assertSame('Demandes de besoin', NeedRequestResource::getNavigationLabel());
        $this->assertSame('Demandes reçues', NeedRequestResource::getNavigationGroup());
        $this->assertSame('contact_name', NeedRequestResource::getRecordTitleAttribute());
    }

    public function test_les_demandes_ne_se_creent_pas_et_ne_se_modifient_pas_dans_l_administration(): void
    {
        $demande = $this->makeRequest();

        $this->assertFalse(NeedRequestResource::canCreate());
        $this->assertFalse(NeedRequestResource::canEdit($demande));

        // La ressource ne déclare donc que la page de liste.
        $this->assertSame(['index'], array_keys(NeedRequestResource::getPages()));
    }

    public function test_la_liste_affiche_le_demandeur_l_organisation_et_le_lieu(): void
    {
        $demande = $this->makeRequest();

        $particulier = $this->makeRequest([
            'organization' => null,
            'contact_name' => 'Espérance KAVIRA',
            'email' => 'esperance.kavira@example.org',
            'need_type' => "Réhabilitation d'une école",
            'location' => null,
            'created_at' => '2026-02-12 11:30:00',
        ]);

        Livewire::test(ListNeedRequests::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$demande, $particulier])
            ->assertSee('Jean BISIMWA')
            ->assertSee('Association des jeunes de Fizi')
            ->assertSee('Fizi, Sud-Kivu')
            ->assertSee('À titre individuel')      // organisation absente
            ->assertSee('Lieu non précisé');       // lieu absent
    }

    public function test_la_recherche_porte_sur_le_nom_du_demandeur_et_sur_l_organisation(): void
    {
        $demande = $this->makeRequest();

        $autre = $this->makeRequest([
            'organization' => 'École primaire de Karhale',
            'contact_name' => 'Espérance KAVIRA',
            'email' => 'esperance.kavira@example.org',
            'need_type' => "Réhabilitation d'une école",
        ]);

        Livewire::test(ListNeedRequests::class)
            ->searchTable('Karhale')
            ->assertCanSeeTableRecords([$autre])
            ->assertCanNotSeeTableRecords([$demande]);
    }

    public function test_le_filtre_des_natures_de_besoin_est_construit_a_partir_des_demandes_recues(): void
    {
        $forage = $this->makeRequest();

        $ecole = $this->makeRequest([
            'organization' => 'École primaire de Karhale',
            'contact_name' => 'Espérance KAVIRA',
            'email' => 'esperance.kavira@example.org',
            'need_type' => "Réhabilitation d'une école",
            'location' => 'Kalehe, Sud-Kivu',
        ]);

        // La liste des natures n'est pas connue d'avance : elle dépend des
        // choix du formulaire public. Le filtre l'extrait donc des demandes
        // réellement reçues, et il fonctionne dès la première.
        Livewire::test(ListNeedRequests::class)
            ->filterTable('need_type', "Réhabilitation d'une école")
            ->assertCanSeeTableRecords([$ecole])
            ->assertCanNotSeeTableRecords([$forage]);
    }

    public function test_le_filtre_de_periode_isole_les_demandes_recues_sur_une_plage_de_dates(): void
    {
        $fevrier = $this->makeRequest();

        $mars = $this->makeRequest([
            'contact_name' => 'Espérance KAVIRA',
            'email' => 'esperance.kavira@example.org',
            'created_at' => '2026-03-05 09:00:00',
        ]);

        Livewire::test(ListNeedRequests::class)
            ->filterTable('recu', ['du' => '2026-03-01', 'au' => '2026-03-31'])
            ->assertCanSeeTableRecords([$mars])
            ->assertCanNotSeeTableRecords([$fevrier]);
    }

    public function test_l_action_de_lecture_affiche_la_demande_complete(): void
    {
        $demande = $this->makeRequest();

        Livewire::test(ListNeedRequests::class)
            ->assertTableActionVisible('view', $demande)
            ->mountTableAction('view', $demande)
            // La fenêtre de lecture est ouverte, et tous ses champs — y compris
            // la description, qui n'apparaît pas dans le tableau — sont remplis
            // avec la demande. (Le HTML d'une fenêtre modale n'est pas rendu
            // dans la réponse Livewire : on vérifie donc l'état de ses champs.)
            ->assertTableActionDataSet([
                'contact_name' => 'Jean BISIMWA',
                'organization' => 'Association des jeunes de Fizi',
                'email' => 'jean.bisimwa@example.org',
                'phone' => '+243 970 000 000',
                'need_type' => "Forage d'eau potable",
                'location' => 'Fizi, Sud-Kivu',
                'description' => "Notre village manque d'eau potable depuis la saison sèche.",
            ]);
    }

    public function test_l_action_de_reponse_prepare_un_courriel_vers_le_demandeur(): void
    {
        $demande = $this->makeRequest();

        Livewire::test(ListNeedRequests::class)
            ->assertTableActionVisible('repondre', $demande)
            ->assertSee('mailto:jean.bisimwa@example.org');
    }

    public function test_une_demande_recue_peut_etre_supprimee(): void
    {
        $demande = $this->makeRequest();

        Livewire::test(ListNeedRequests::class)
            ->callTableAction('delete', $demande);

        $this->assertModelMissing($demande);
    }
}
