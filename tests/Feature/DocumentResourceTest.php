<?php

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Models\Document;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class DocumentResourceTest extends TestCase
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
    private function makeDocument(array $attributes = []): Document
    {
        return Document::create(array_merge([
            'title' => [
                'fr' => "Présentation de l'OAAT et de ses expériences",
                'en' => 'Presentation of OAAT and its experience',
            ],
            'type' => DocumentType::Report,
            'published_at' => '2026-01-15 09:00:00',
        ], $attributes));
    }

    public function test_les_valeurs_de_lenum_correspondent_aux_types_ecrits_par_le_seeder(): void
    {
        // Le DocumentSeeder écrit exactement ces trois valeurs : les renommer
        // dans l'enum sans toucher au seeder serait une erreur silencieuse.
        $this->assertSame('rapport', DocumentType::Report->value);
        $this->assertSame('attestation', DocumentType::Certificate->value);
        $this->assertSame('etude', DocumentType::Study->value);

        // Les libellés et couleurs viennent des contrats HasLabel / HasColor.
        $this->assertSame('info', DocumentType::Report->getColor());
        $this->assertSame('Étude ou catalogue de projets', DocumentType::Study->getLabel());
    }

    public function test_la_ressource_est_rangee_dans_le_menu_des_medias_et_documents(): void
    {
        $this->assertSame('Documents & publications', DocumentResource::getNavigationLabel());
        $this->assertSame('Médias & documents', DocumentResource::getNavigationGroup());
        $this->assertSame('title', DocumentResource::getRecordTitleAttribute());
    }

    public function test_la_liste_signale_les_documents_sans_fichier_ou_encore_en_brouillon(): void
    {
        Storage::fake('public');

        $complet = $this->makeDocument();

        $complet->addMediaFromString('%PDF-1.4 contenu du rapport')
            ->usingFileName('presentation-oaat.pdf')
            ->toMediaCollection('file');

        $incomplet = $this->makeDocument([
            'title' => [
                'fr' => 'Catalogue des projets en attente de financement',
                'en' => 'Catalogue of projects awaiting funding',
            ],
            'type' => DocumentType::Study,
            'published_at' => null,
        ]);

        Livewire::test(ListDocuments::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$complet, $incomplet])
            ->assertSee("Présentation de l'OAAT et de ses expériences")
            ->assertSee("Rapport d'activité ou d'évaluation")   // libellé FR de l'enum
            ->assertSee('En ligne')                            // le PDF existe
            ->assertSee('Manquant')                            // aucun fichier envoyé
            ->assertSee('Brouillon');                          // pas de date de mise en ligne
    }

    public function test_la_recherche_du_tableau_porte_sur_le_titre_francais_stocke_en_json(): void
    {
        $rapport = $this->makeDocument();

        $catalogue = $this->makeDocument([
            'title' => [
                'fr' => 'Catalogue des projets en attente de financement',
                'en' => 'Catalogue of projects awaiting funding',
            ],
            'type' => DocumentType::Study,
        ]);

        Livewire::test(ListDocuments::class)
            ->searchTable('catalogue')
            ->assertCanSeeTableRecords([$catalogue])
            ->assertCanNotSeeTableRecords([$rapport]);
    }

    public function test_les_filtres_isolent_une_nature_de_document_et_les_brouillons(): void
    {
        $rapport = $this->makeDocument();

        $brouillon = $this->makeDocument([
            'title' => ['fr' => "Statuts de l'association", 'en' => 'Articles of association'],
            'type' => DocumentType::Policy,
            'published_at' => null,
        ]);

        Livewire::test(ListDocuments::class)
            ->filterTable('type', DocumentType::Policy->value)
            ->assertCanSeeTableRecords([$brouillon])
            ->assertCanNotSeeTableRecords([$rapport]);

        Livewire::test(ListDocuments::class)
            ->filterTable('published_at', false)   // false = colonne NULL = brouillon
            ->assertCanSeeTableRecords([$brouillon])
            ->assertCanNotSeeTableRecords([$rapport]);
    }

    public function test_le_formulaire_contient_le_titre_bilingue_le_fichier_la_nature_et_la_date(): void
    {
        $document = $this->makeDocument();

        Livewire::test(EditDocument::class, ['record' => $document->getRouteKey()])
            ->assertOk()
            ->assertFormFieldExists('title.fr')
            ->assertFormFieldExists('title.en')
            ->assertFormFieldExists('file')
            ->assertFormFieldExists('type')
            ->assertFormFieldExists('published_at')
            ->assertSee('Titre du document')
            ->assertSee('Fichier & classement');
    }

    public function test_un_document_est_cree_avec_ses_deux_langues_et_sa_nature(): void
    {
        Livewire::test(CreateDocument::class)
            ->fillForm([
                'title.fr' => 'Rapport annuel 2025',
                'title.en' => 'Annual report 2025',
                'type' => DocumentType::Report->value,
                'published_at' => '2026-02-01 08:30:00',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $document = Document::where('title->fr', 'Rapport annuel 2025')->first();

        $this->assertNotNull($document, 'Le document aurait dû être créé.');
        $this->assertSame('Rapport annuel 2025', $document->getTranslation('title', 'fr'));
        $this->assertSame('Annual report 2025', $document->getTranslation('title', 'en'));
        $this->assertSame(DocumentType::Report, $document->type);
        $this->assertNotNull($document->published_at);
    }

    public function test_le_fichier_est_stocke_dans_la_collection_file_et_le_bouton_de_telechargement_suit(): void
    {
        Storage::fake('public');

        $avecFichier = $this->makeDocument();

        $avecFichier->addMediaFromString('%PDF-1.4 rapport')
            ->usingFileName('rapport-2025.pdf')
            ->toMediaCollection('file');

        $sansFichier = $this->makeDocument([
            'title' => ['fr' => 'Étude à venir', 'en' => 'Upcoming study'],
            'published_at' => null,
        ]);

        $avecFichier = $avecFichier->refresh();

        // « file » est une collection singleFile : un seul fichier à la fois.
        $this->assertCount(1, $avecFichier->getMedia('file'));
        $this->assertSame('rapport-2025.pdf', $avecFichier->getFirstMedia('file')->file_name);
        Storage::disk('public')->assertExists($avecFichier->getFirstMedia('file')->getPathRelativeToRoot());

        // Le bouton n'apparaît que lorsque le fichier existe réellement :
        // proposer de télécharger un document vide serait une fausse promesse.
        Livewire::test(ListDocuments::class)
            ->assertTableActionVisible('telecharger', $avecFichier)
            ->assertTableActionHidden('telecharger', $sansFichier);
    }
}
