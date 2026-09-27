<?php

namespace App\Filament\Resources\KosListings;

use App\Filament\Resources\KosListings\Pages\CreateKosListing;
use App\Filament\Resources\KosListings\Pages\EditKosListing;
use App\Filament\Resources\KosListings\Pages\ListKosListings;
use App\Filament\Resources\KosListings\Schemas\KosListingForm;
use App\Filament\Resources\KosListings\Tables\KosListingsTable;
use App\Models\KosListing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KosListingResource extends Resource
{
    protected static ?string $model = KosListing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Listing Kos';

    protected static ?string $modelLabel = 'Kos';

    protected static ?string $pluralModelLabel = 'Listing Kos';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return KosListingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KosListingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKosListings::route('/'),
            'create' => CreateKosListing::route('/create'),
            'edit' => EditKosListing::route('/{record}/edit'),
        ];
    }
}
