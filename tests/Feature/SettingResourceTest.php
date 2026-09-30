<?php

namespace Tests\Feature;

use App\Filament\Resources\Settings\Pages\CreateSetting;
use App\Filament\Resources\Settings\Pages\EditSetting;
use App\Filament\Resources\Settings\Pages\ListSettings;
use App\Filament\Resources\Settings\SettingResource;
use App\Models\Setting;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SettingResourceTest extends TestCase
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

    private function makeSetting(): Setting
    {
        return Setting::create([
            'organisation_name' => [
                'fr' => "Organisation Africaine pour l'Aménagement des Territoires",
                'en' => 'African Organisation for Land Planning',
            ],
            'email' => 'oaatrdc2000@gmail.com',
            'phones' => ['+243 993 537 325', '+243 896 127 195'],
            'addresses' => [
                'fr' => ['Bureau de représentation : Bukavu, Sud-Kivu'],
                'en' => ['Representation office: Bukavu, South Kivu'],
            ],
            'socials' => null,
            'stat_zones' => 13,
        ]);
    }

    public function test_la_ressource_est_rangee_dans_le_menu_de_l_organisation(): void
    {
        $this->assertSame('Paramètres du site', SettingResource::getNavigationLabel());
        $this->assertSame('Organisation', SettingResource::getNavigationGroup());
        $this->assertSame('paramètres', SettingResource::getPluralModelLabel());
    }

    public function test_la_page_des_parametres_ouvre_directement_le_formulaire_unique(): void
    {
        $setting = $this->makeSetting();

        // La liste n'a pas de raison d'être affichée : il n'existe qu'une fiche.
        Livewire::test(ListSettings::class)
            ->assertRedirect(EditSetting::getUrl(['record' => $setting]));
    }

    public function test_on_ne_peut_pas_creer_une_seconde_fiche_de_parametres(): void
    {
        // Table vide (installation neuve) : la création est possible…
        $this->assertTrue(SettingResource::canCreate());

        $this->makeSetting();

        // …et dès qu'une fiche existe, le bouton « Créer » disparaît.
        $this->assertFalse(SettingResource::canCreate());
    }

    public function test_la_suppression_des_parametres_est_refusee(): void
    {
        $setting = $this->makeSetting();

        $this->assertFalse(SettingResource::canDelete($setting));

        // Le message explique POURQUOI, plutôt que de laisser un bouton
        // silencieusement inerte.
        $this->assertSame(
            "Les paramètres du site ne peuvent pas être supprimés : ils contiennent les coordonnées officielles de l'OAAT.",
            SettingResource::getDeleteAuthorizationResponse($setting)->message(),
        );
    }

    public function test_le_formulaire_contient_l_identite_les_telephones_les_adresses_les_reseaux_et_les_chiffres(): void
    {
        $setting = $this->makeSetting();

        Livewire::test(EditSetting::class, ['record' => $setting->getRouteKey()])
            ->assertOk()
            ->assertFormFieldExists('organisation_name.fr')
            ->assertFormFieldExists('organisation_name.en')
            ->assertFormFieldExists('email')
            ->assertFormFieldExists('logo')
            ->assertFormFieldExists('phones')
            ->assertFormFieldExists('addresses.fr')
            ->assertFormFieldExists('addresses.en')
            ->assertFormFieldExists('socials')
            ->assertFormFieldExists('stat_projects')
            ->assertFormFieldExists('stat_beneficiaries')
            ->assertFormFieldExists('stat_zones')
            ->assertFormFieldExists('stat_years')
            ->assertSee('Identité de l\'organisation')
            ->assertSee('Réseaux sociaux')
            ->assertSee('Chiffres clés');
    }

    public function test_les_parametres_sont_enregistres_avec_leurs_traductions_et_leurs_tableaux_json(): void
    {
        $setting = $this->makeSetting();

        Livewire::test(EditSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm([
                'organisation_name.fr' => "Organisation Africaine pour l'Aménagement des Territoires",
                'organisation_name.en' => 'African Organisation for Land Planning',
                'email' => 'contact@oaat.cd',
                // Un Repeater « simple » attend la forme [['champ' => valeur], …] :
                // c'est la forme que produit l'interface, et Filament la réduit
                // ensuite au tableau de chaînes stocké en base.
                'phones' => [
                    ['phone' => '+243 993 537 325'],
                    ['phone' => '+243 896 127 195'],
                ],
                'addresses.fr' => [
                    ['address' => 'Siège social : Nyangezi, Walungu'],
                    ['address' => 'Bureau : Bukavu, Ibanda'],
                ],
                'addresses.en' => [
                    ['address' => 'Head office: Nyangezi, Walungu'],
                ],
                'socials' => [
                    ['platform' => 'Facebook', 'url' => 'https://www.facebook.com/oaat'],
                ],
                'stat_projects' => 15,
                'stat_beneficiaries' => 12000,
                'stat_zones' => 13,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $setting = $setting->refresh();

        // 1. Texte traduisible : chaque langue part dans sa case du JSON.
        $this->assertSame(
            'African Organisation for Land Planning',
            $setting->getTranslation('organisation_name', 'en'),
        );

        // 2. Colonne texte simple.
        $this->assertSame('contact@oaat.cd', $setting->email);

        // 3. Tableau JSON de chaînes : c'est ce que produit le Repeater « simple ».
        //    La forme est celle attendue par le seeder et par le site public.
        $this->assertSame(['+243 993 537 325', '+243 896 127 195'], $setting->phones);

        // 4. Tableau JSON traduisible : une liste de chaînes par langue.
        $this->assertSame(
            ['Siège social : Nyangezi, Walungu', 'Bureau : Bukavu, Ibanda'],
            $setting->getTranslation('addresses', 'fr'),
        );
        $this->assertSame(
            ['Head office: Nyangezi, Walungu'],
            $setting->getTranslation('addresses', 'en'),
        );

        // 5. Tableau JSON d'objets : la forme produite par les deux champs
        //    « réseau » et « adresse » du Repeater.
        $this->assertSame(
            [['platform' => 'Facebook', 'url' => 'https://www.facebook.com/oaat']],
            $setting->socials,
        );

        // 6. Chiffres : les colonnes entières, y compris celles non modifiées.
        $this->assertSame(15, $setting->stat_projects);
        $this->assertSame(12000, $setting->stat_beneficiaries);
        $this->assertSame(13, $setting->stat_zones);
    }

    public function test_les_parametres_peuvent_etre_crees_quand_la_table_est_vide(): void
    {
        Livewire::test(CreateSetting::class)
            ->fillForm([
                'organisation_name.fr' => "Organisation Africaine pour l'Aménagement des Territoires",
                'email' => 'oaatrdc2000@gmail.com',
                'stat_zones' => 13,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $setting = Setting::current();

        $this->assertNotNull($setting, 'Les paramètres auraient dû être créés.');
        $this->assertSame("Organisation Africaine pour l'Aménagement des Territoires", $setting->getTranslation('organisation_name', 'fr'));
        $this->assertSame('oaatrdc2000@gmail.com', $setting->email);
    }

    public function test_le_logo_est_stocke_dans_sa_collection(): void
    {
        Storage::fake('public');

        $setting = $this->makeSetting();

        $setting->addMediaFromString('contenu-du-logo')
            ->usingFileName('logo-oaat.png')
            ->toMediaCollection('logo');

        $setting = $setting->refresh();

        $this->assertCount(1, $setting->getMedia('logo'));
        $this->assertSame('logo-oaat.png', $setting->getFirstMedia('logo')->file_name);
    }
}
