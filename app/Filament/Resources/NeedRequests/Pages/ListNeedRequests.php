<?php

namespace App\Filament\Resources\NeedRequests\Pages;

use App\Filament\Resources\NeedRequests\NeedRequestResource;
use Filament\Resources\Pages\ListRecords;

/**
 * Pas de bouton « Créer » ici : les demandes arrivent depuis le site public
 * (voir NeedRequestResource::canCreate()).
 */
class ListNeedRequests extends ListRecords
{
    protected static string $resource = NeedRequestResource::class;
}
