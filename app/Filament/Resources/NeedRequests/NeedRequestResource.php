<?php

namespace App\Filament\Resources\NeedRequests;

use App\Filament\Resources\NeedRequests\Pages\ListNeedRequests;
use App\Filament\Resources\NeedRequests\Tables\NeedRequestsTable;
use App\Models\NeedRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Les demandes de besoin envoyées depuis le site public : une organisation
 * (ou un particulier) décrit un besoin à couvrir.
 *
 * Boîte de réception en LECTURE SEULE, comme les messages de contact.
 */
class NeedRequestResource extends Resource
{
    protected static ?string $model = NeedRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static ?string $navigationLabel = 'Demandes de besoin';

    protected static ?string $modelLabel = 'demande';

    protected static ?string $pluralModelLabel = 'demandes';

    protected static string|\UnitEnum|null $navigationGroup = 'Demandes reçues';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'contact_name';

    /**
     * Une demande arrive par le formulaire public : elle ne se crée pas ici.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Une demande reçue ne se modifie pas : le réécrire effacerait ce que le
     * demandeur a réellement décrit. Seule la suppression est possible.
     */
    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = NeedRequest::query()->whereNull('read_at')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return NeedRequest::query()->whereNull('read_at')->exists() ? 'warning' : 'gray';
    }

    public static function table(Table $table): Table
    {
        return NeedRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNeedRequests::route('/'),
        ];
    }
}
