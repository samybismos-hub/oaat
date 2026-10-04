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
     * Compteur du menu : le nombre de messages non lus.
     *
     * La colonne read_at (timestamp nullable) indique si un message a été
     * ouvert. Si elle est NULL, le message est considéré comme non lu.
     * Le badge passe en orange tant qu'il y a du courrier en attente.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = ContactMessage::query()->whereNull('read_at')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return ContactMessage::query()->whereNull('read_at')->exists() ? 'warning' : 'gray';
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
