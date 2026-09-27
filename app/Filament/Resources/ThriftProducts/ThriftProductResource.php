<?php

namespace App\Filament\Resources\ThriftProducts;

use App\Filament\Resources\ThriftProducts\Pages\CreateThriftProduct;
use App\Filament\Resources\ThriftProducts\Pages\EditThriftProduct;
use App\Filament\Resources\ThriftProducts\Pages\ListThriftProducts;
use App\Filament\Resources\ThriftProducts\Schemas\ThriftProductForm;
use App\Filament\Resources\ThriftProducts\Tables\ThriftProductsTable;
use App\Models\ThriftProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ThriftProductResource extends Resource
{
    protected static ?string $model = ThriftProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $navigationLabel = 'Produk Thrift';

    protected static ?string $modelLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Produk Thrift';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ThriftProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ThriftProductsTable::configure($table);
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
            'index' => ListThriftProducts::route('/'),
            'create' => CreateThriftProduct::route('/create'),
            'edit' => EditThriftProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->role === 'owner') {
            $query->where('seller_id', auth()->id());
        }

        return $query;
    }
}
