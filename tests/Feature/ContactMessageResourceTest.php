<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Models\ContactMessage;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Livewire\Livewire;
use Tests\TestCase;

class ContactMessageResourceTest extends TestCase
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
    private function makeMessage(array $attributes = []): ContactMessage
    {
        $message = ContactMessage::create(array_merge([
            'name' => 'Paul KAVIRA',
            'email' => 'paul.kavira@example.org',
            'subject' => 'Accès au terrain de Sebele',
            'message' => 'Bonjour, le terrain est-il accessible en saison des pluies ?',
        ], Arr::except($attributes, 'created_at')));

        // « created_at » n'est pas fillable : il n'est jamais saisi par un
        // utilisateur. On le fixe donc explicitement, afin que le filtre de
        // période se teste sur des dates choisies et non sur la date du jour.
        $message->created_at = $attributes['created_at'] ?? '2026-02-10 10:15:00';
        $message->save();

        return $message;
    }

    public function test_la_ressource_est_rangee_dans_le_menu_des_demandes_recues(): void
    {
        $this->assertSame('Messages de contact', ContactMessageResource::getNavigationLabel());
        $this->assertSame('Demandes reçues', ContactMessageResource::getNavigationGroup());
        $this->assertSame('name', ContactMessageResource::getRecordTitleAttribute());
    }

    public function test_les_messages_ne_se_creent_pas_et_ne_se_modifient_pas_dans_l_administration(): void
    {
        $message = $this->makeMessage();

        // Un message arrive par le formulaire public : le créer ici n'aurait
        // aucun sens, et le modifier effacerait ce que le visiteur a écrit.
        $this->assertFalse(ContactMessageResource::canCreate());
        $this->assertFalse(ContactMessageResource::canEdit($message));

        // Conséquence concrète : la ressource ne déclare aucune page
        // « création » ni « modification », seulement la liste.
        $this->assertSame(['index'], array_keys(ContactMessageResource::getPages()));
    }

    public function test_la_liste_affiche_l_expediteur_l_objet_et_le_placeholder_sans_objet(): void
    {
        $avecObjet = $this->makeMessage();

        $sansObjet = $this->makeMessage([
            'name' => 'Marie BAHATI',
            'email' => 'marie.bahati@example.org',
            'subject' => null,
            'message' => 'Je souhaite proposer un partenariat.',
            'created_at' => '2026-02-11 08:00:00',
        ]);

        Livewire::test(ListContactMessages::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$avecObjet, $sansObjet])
            ->assertSee('Paul KAVIRA')
            ->assertSee('Accès au terrain de Sebele')
            ->assertSee('Sans objet')   // la colonne « Objet » reste lisible même vide
            ->assertSee('10/02/2026 10:15');
    }

    public function test_la_recherche_porte_sur_le_nom_l_email_et_l_objet(): void
    {
        $message = $this->makeMessage();

        $autre = $this->makeMessage([
            'name' => 'Marie BAHATI',
            'email' => 'marie.bahati@example.org',
            'subject' => 'Proposition de partenariat',
            'message' => 'Bonjour, nous aimerions collaborer.',
        ]);

        Livewire::test(ListContactMessages::class)
            ->searchTable('bahati')
            ->assertCanSeeTableRecords([$autre])
            ->assertCanNotSeeTableRecords([$message]);

        Livewire::test(ListContactMessages::class)
            ->searchTable('partenariat')
            ->assertCanSeeTableRecords([$autre])
            ->assertCanNotSeeTableRecords([$message]);
    }

    public function test_le_filtre_de_periode_isole_les_messages_recus_sur_une_plage_de_dates(): void
    {
        $fevrier = $this->makeMessage();

        $mars = $this->makeMessage([
            'name' => 'Marie BAHATI',
            'email' => 'marie.bahati@example.org',
            'subject' => 'Proposition de partenariat',
            'message' => 'Bonjour, nous aimerions collaborer.',
            'created_at' => '2026-03-05 09:00:00',
        ]);

        // Le filtre sur mesure reçoit les deux dates saisies puis construit
        // la condition whereDate() : c'est ce que ce test vérifie.
        Livewire::test(ListContactMessages::class)
            ->filterTable('recu', ['du' => '2026-03-01', 'au' => '2026-03-31'])
            ->assertCanSeeTableRecords([$mars])
            ->assertCanNotSeeTableRecords([$fevrier]);
    }

    public function test_l_action_de_lecture_affiche_le_message_complet(): void
    {
        $message = $this->makeMessage();

        Livewire::test(ListContactMessages::class)
            ->assertTableActionVisible('view', $message)
            ->mountTableAction('view', $message)
            // La fenêtre de lecture est bien ouverte, et ses champs sont remplis
            // avec la fiche. (On vérifie l'état de ses champs plutôt que le HTML :
            // le contenu d'une fenêtre modale n'est pas rendu dans la réponse
            // renvoyée par Livewire, contrairement aux colonnes du tableau.)
            ->assertTableActionDataSet([
                'name' => 'Paul KAVIRA',
                'email' => 'paul.kavira@example.org',
                'subject' => 'Accès au terrain de Sebele',
                'message' => 'Bonjour, le terrain est-il accessible en saison des pluies ?',
            ]);
    }

    public function test_l_action_de_reponse_prepare_un_courriel_vers_l_expediteur(): void
    {
        $message = $this->makeMessage();

        Livewire::test(ListContactMessages::class)
            ->assertTableActionVisible('repondre', $message)
            // L'action ouvre le logiciel de messagerie de l'utilisateur avec
            // l'adresse du visiteur déjà remplie.
            ->assertSee('mailto:paul.kavira@example.org');
    }

    public function test_un_message_recu_peut_etre_supprime(): void
    {
        $message = $this->makeMessage();

        Livewire::test(ListContactMessages::class)
            ->callTableAction('delete', $message);

        $this->assertModelMissing($message);
    }
}
