<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_redirige_la_racine_vers_la_locale(): void
    {
        // La racine du site n'affiche pas une page : elle redirige vers la
        // locale courante (accueil FR ou EN), posée par le middleware SetLocale.
        $this->get('/')->assertRedirect();
    }
}
