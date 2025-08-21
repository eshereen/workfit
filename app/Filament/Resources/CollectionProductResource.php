<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectionProductResource\Pages;
use App\Filament\Resources\CollectionProductResource\RelationManagers;
use App\Models\CollectionProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CollectionProductResource extends Resource
{
    protected static ?string $model = CollectionProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCollectionProducts::route('/'),
            'create' => Pages\CreateCollectionProduct::route('/create'),
            'view' => Pages\ViewCollectionProduct::route('/{record}'),
            'edit' => Pages\EditCollectionProduct::route('/{record}/edit'),
        ];
    }
}
