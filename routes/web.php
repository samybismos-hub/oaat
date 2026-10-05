<?php

use App\Http\Controllers\Public\AccueilController;
use App\Http\Controllers\Public\ActualiteController;
use App\Http\Controllers\Public\BesoinController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\DocumentController;
use App\Http\Controllers\Public\DomaineController;
use App\Http\Controllers\Public\GalerieController;
use App\Http\Controllers\Public\OrganisationController;
use App\Http\Controllers\Public\PartenaireController;
use App\Http\Controllers\Public\ProjetController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes du site public — bilingue (fr / en)
|--------------------------------------------------------------------------
|
| Toutes les routes sont préfixées par {locale?} et passent par le
| middleware SetLocale qui active la bonne langue.
|
| Exemples :
|   /fr/projets            → Projets en français
|   /en/projets            → Projects in English
|   /fr/projets/ecole-kindu → Détail du projet en français
|
| La racine "/" redirige vers "/fr/" via le middleware.
|
*/

Route::group(
    [
        'prefix' => '{locale?}',
        'where' => ['locale' => '(fr|en)'],
        'middleware' => [SetLocale::class],
    ],
    function () {

        // ─── Accueil ───────────────────────────────────────────
        Route::get('/', [AccueilController::class, 'index'])->name('accueil');

        // ─── Organisation ──────────────────────────────────────
        Route::get('/organisation', [OrganisationController::class, 'index'])
            ->name('organisation');

        // ─── Domaines d'intervention ────────────────────────────
        Route::get('/domaines', [DomaineController::class, 'index'])
            ->name('domaines.index');
        Route::get('/domaines/{slug}', [DomaineController::class, 'show'])
            ->name('domaines.show');

        // ─── Projets ────────────────────────────────────────────
        Route::get('/projets', [ProjetController::class, 'index'])
            ->name('projets.index');
        Route::get('/projets/{slug}', [ProjetController::class, 'show'])
            ->name('projets.show');

        // ─── Actualités ─────────────────────────────────────────
        Route::get('/actualites', [ActualiteController::class, 'index'])
            ->name('actualites.index');
        Route::get('/actualites/{slug}', [ActualiteController::class, 'show'])
            ->name('actualites.show');

        // ─── Documents ──────────────────────────────────────────
        Route::get('/documents', [DocumentController::class, 'index'])
            ->name('documents.index');

        // ─── Galerie (albums photos) ────────────────────────────
        Route::get('/galerie', [GalerieController::class, 'index'])
            ->name('galerie.index');
        Route::get('/galerie/{slug}', [GalerieController::class, 'show'])
            ->name('galerie.show');

        // ─── Partenaires ────────────────────────────────────────
        Route::get('/partenaires', [PartenaireController::class, 'index'])
            ->name('partenaires');

        // ─── Contact ────────────────────────────────────────────
        Route::get('/contact', [ContactController::class, 'index'])
            ->name('contact');
        Route::post('/contact/send', [ContactController::class, 'send'])
            ->name('contact.send');

        // ─── Besoin ─────────────────────────────────────────────
        Route::get('/besoin', [BesoinController::class, 'index'])
            ->name('besoin');
        Route::post('/besoin/send', [BesoinController::class, 'send'])
            ->name('besoin.send');
    },
);
