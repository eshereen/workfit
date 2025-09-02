<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Filament\Resources\SubcategoryResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class SubcategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'subcategories';

    protected static ?string $relatedResource = SubcategoryResource::class;

    public function table(Table $table): Table
    {
        return $table
        ->headerActions([
            CreateAction::make(),
        ])
        ->recordActions([
            ViewAction::make(),
            EditAction::make(),
            DeleteAction::make(),
        ])
        ->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
                ForceDeleteBulkAction::make(),
                RestoreBulkAction::make(),

            ]),
        ]);
}
}
