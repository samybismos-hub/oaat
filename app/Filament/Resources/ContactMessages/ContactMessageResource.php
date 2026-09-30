<?php

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Tables\ContactMessagesTable;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Les messages envoyés depuis le formulaire de contact du site public.
 *
 * Boîte de réception en LECTURE SEULE : un message reçu ne se réécrit pas.
 * On peut le lire, y répondre par e-mail, ou le supprimer.
 */
class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Messages de contact';

    protected static ?string $modelLabel = 'message';

    protected static ?string $pluralModelLabel = 'messages';

    protected static string|\UnitEnum|null $navigationGroup = 'Demandes reçues';

    protected static ?int $navigationSort = 1;

    // Titre d'une fiche : « Message de <nom de l'expéditeur> », par exemple.
    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Un message reçu ne se crée pas depuis l'administration : il arrive par
     * le formulaire de contact du site public.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Un message reçu ne se modifie pas : le réécrire effacerait ce que le
     * visiteur a réellement écrit. Seule la suppression est possible.
     */
    public static function canEdit(Model $record): bool
    {
        return false;
    }

    /**
     * Compteur du menu : le nombre de messages conservés.
     *
     * La table ne comporte pas de colonne « lu / non lu » : le badge ne peut
     * donc pas signaler du courrier en attente, seulement le volume reçu.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = ContactMessage::query()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'gray';
    }

    public static function table(Table $table): Table
    {
        return ContactMessagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
        ];
    }
}
