<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Page publique « Contact » — pré-sélection de l'objet du formulaire.
 *
 * Les boutons « Appel à soutien » des fiches projets renvoient vers
 * /{locale}/contact?objet=<code> : le formulaire doit présélectionner
 * la valeur correspondante (select « Objet » + bouton de collaboration).
 */
class ContactPageTest extends TestCase
{
    public function test_l_objet_est_preselectionne_depuis_le_parametre_d_url(): void
    {
        // assertStringContainsString : pas d'échappement HTML sur l'aiguille
        // (le HTML rendu contient un littéral value="f" selected).
        $fr = $this->get('/fr/contact?objet=f')
            ->assertOk()
            ->getContent();
        $this->assertStringContainsString('value="f" selected', $fr);

        $en = $this->get('/en/contact?objet=p')
            ->assertOk()
            ->getContent();
        $this->assertStringContainsString('value="p" selected', $en);
    }

    public function test_sans_parametre_aucun_objet_n_est_preselectionne(): void
    {
        $content = $this->get('/fr/contact')->assertOk()->getContent();

        $this->assertStringNotContainsString(' selected', $content);
    }

    public function test_le_bouton_de_collaboration_correspondant_est_surligne(): void
    {
        $content = $this->get('/fr/contact?objet=f')->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '#<button[^>]*data-v="f"[^>]{0,300}border-lake bg-mist#',
            $content
        );
    }
}
