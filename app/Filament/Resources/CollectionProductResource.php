<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\CollectionProductResource\Pages\ListCollectionProducts;
use App\Filament\Resources\CollectionProductResource\Pages\CreateCollectionProduct;
use App\Filament\Resources\CollectionProductResource\Pages\ViewCollectionProduct;
use App\Filament\Resources\CollectionProductResource\Pages\EditCollectionProduct;
use App\Filament\Resources\CollectionProductResource\Pages;
use App\Filament\Resources\CollectionProductResource\RelationManagers;
use App\Models\CollectionProduct;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CollectionProductResource extends Resource
{
    protected static ?string $model = CollectionProduct::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
