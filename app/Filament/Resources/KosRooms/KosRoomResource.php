<?php

namespace App\Filament\Resources\KosRooms;

use App\Filament\Resources\KosRooms\Pages\CreateKosRoom;
use App\Filament\Resources\KosRooms\Pages\EditKosRoom;
use App\Filament\Resources\KosRooms\Pages\ListKosRooms;
use App\Filament\Resources\KosRooms\Schemas\KosRoomForm;
use App\Filament\Resources\KosRooms\Tables\KosRoomsTable;
use App\Models\KosRoom;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KosRoomResource extends Resource
{
    protected static ?string $model = KosRoom::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengelolaan Kos';

    protected static ?string $navigationLabel = 'Kamar';

    protected static ?string $modelLabel = 'Kamar';

    protected static ?string $pluralModelLabel = 'Daftar Kamar';

    public static function form(Schema $schema): Schema
    {
        return KosRoomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KosRoomsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKosRooms::route('/'),
            'create' => CreateKosRoom::route('/create'),
            'edit' => EditKosRoom::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->role === 'owner') {
            $query->whereHas('kosListing', fn ($q) => $q->where('owner_id', auth()->id()));
        }

        return $query;
    }
}
