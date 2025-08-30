<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Filament\Resources\OrderItems\OrderItemResource;
use Filament\Actions\CreateAction;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $recordTitleAttribute = 'id';


    protected static ?string $relatedResource = OrderItemResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name'),
                TextColumn::make('quantity'),
                TextColumn::make('price'),
            ])
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
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
