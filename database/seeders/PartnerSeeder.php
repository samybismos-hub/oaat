<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Bailleurs et partenaires de mise en œuvre cités dans le PDF de présentation OAAT.
     *
     * Les descriptions marquées "(à préciser)" ne sont pas documentées dans le PDF :
     * elles doivent être validées avec le client avant publication.
     */
    public function run(): void
    {
        $partners = [
            ['name' => 'PNUD', 'description' => ['fr' => 'Programme des Nations Unies pour le Développement.', 'en' => 'United Nations Development Programme.']],
            ['name' => 'FAO', 'description' => ['fr' => "Organisation des Nations Unies pour l'alimentation et l'agriculture.", 'en' => 'Food and Agriculture Organization of the United Nations.']],
            ['name' => 'UNICEF', 'description' => ['fr' => "Fonds des Nations Unies pour l'enfance.", 'en' => "United Nations Children's Fund."]],
            ['name' => 'HCR', 'description' => ['fr' => 'Haut-Commissariat des Nations Unies pour les réfugiés.', 'en' => 'United Nations High Commissioner for Refugees.']],
            ['name' => 'PAM', 'description' => ['fr' => 'Programme alimentaire mondial.', 'en' => 'World Food Programme.']],
            ['name' => 'ECHO', 'description' => ['fr' => "Aide humanitaire de l'Union européenne.", 'en' => 'European Union humanitarian aid.']],
            ['name' => 'NAMA Facility', 'description' => ['fr' => "Fonds de financement d'actions d'atténuation du changement climatique. (à préciser)", 'en' => 'Funding facility for climate change mitigation actions. (to be confirmed)']],
            ['name' => 'Pooled Fund', 'description' => ['fr' => 'Fonds commun humanitaire. (à préciser)', 'en' => 'Common humanitarian pooled fund. (to be confirmed)']],
            ['name' => 'AVSI', 'description' => ['fr' => 'ONG internationale de coopération et de développement.', 'en' => 'International cooperation and development NGO.']],
            ['name' => 'CECI', 'description' => ['fr' => "Centre d'étude et de coopération internationale (ONG canadienne).", 'en' => 'Centre for International Studies and Cooperation (Canadian NGO).']],
            ['name' => 'Tearfund', 'description' => ['fr' => 'ONG internationale de développement.', 'en' => 'International development NGO.']],
            ['name' => 'BDOM', 'description' => ['fr' => 'Bureau diocésain des œuvres médicales.', 'en' => 'Diocesan Office of Medical Works.']],
            ['name' => 'Louvain Développement', 'description' => ['fr' => 'ONG belge de développement.', 'en' => 'Belgian development NGO.']],
            ['name' => 'ATUNGA FIZI', 'description' => ['fr' => 'Organisation locale partenaire dans le territoire de Fizi. (à préciser)', 'en' => 'Local partner organisation in Fizi territory. (to be confirmed)']],
            ['name' => 'BETING', 'description' => ['fr' => "Partenaire intervenant notamment dans l'éducation. (à préciser)", 'en' => 'Partner involved notably in education. (to be confirmed)']],
            ['name' => 'IPBS', 'description' => ['fr' => 'Partenaire cité pour le financement climat. (à préciser)', 'en' => 'Partner cited for climate financing. (to be confirmed)']],
        ];

        foreach ($partners as $partner) {
            Partner::updateOrCreate(['name' => $partner['name']], $partner);
        }
    }
}
