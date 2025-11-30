<?php

namespace App\Filament\Resources\CollectionProducts;

use App\Filament\Resources\CollectionProducts\Pages\CreateCollectionProduct;
use App\Filament\Resources\CollectionProducts\Pages\EditCollectionProduct;
use App\Filament\Resources\CollectionProducts\Pages\ListCollectionProducts;
use App\Filament\Resources\CollectionProducts\Pages\ViewCollectionProduct;
use App\Filament\Resources\CollectionProducts\Schemas\CollectionProductForm;
use App\Filament\Resources\CollectionProducts\Schemas\CollectionProductInfolist;
use App\Filament\Resources\CollectionProducts\Tables\CollectionProductsTable;
use App\Models\CollectionProduct;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CollectionProductResource extends Resource
{
    protected static ?string $model = CollectionProduct::class;

    protected static string | UnitEnum   | null $navigationGroup = 'Collections Details';
    public static function form(Schema $schema): Schema
    {
        return CollectionProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CollectionProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CollectionProductsTable::configure($table);
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
            'index' => ListCollectionProducts::route('/'),
            'create' => CreateCollectionProduct::route('/create'),
            'view' => ViewCollectionProduct::route('/{record}'),
            'edit' => EditCollectionProduct::route('/{record}/edit'),
        ];
    }
}
