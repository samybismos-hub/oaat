<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use Filament\Resources\Pages\ListRecords;

/**
 * Pas de bouton « Créer » ici : les messages arrivent depuis le site public
 * (voir ContactMessageResource::canCreate()).
 */
class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;
}
