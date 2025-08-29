<?php

namespace App\Filament\Resources\CollectionProducts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CollectionProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('collection.name')
                    
                    ->sortable(),
                TextColumn::make('product.name')
                    ->sortable(),
              
                SpatieMediaLibraryImageColumn::make('product.main_image')
                ->collection('main_image') // ðŸ‘ˆ the collection name you used
                ->circular()
                ->label('Product'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
}
