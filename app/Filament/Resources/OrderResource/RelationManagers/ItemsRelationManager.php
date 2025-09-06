<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\OrderItems\OrderItemResource;
use Filament\Resources\RelationManagers\RelationManager;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $recordTitleAttribute = 'id';


    protected static ?string $relatedResource = OrderItemResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['product', 'variant']))
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('variant.sku')
                    ->label('SKU')
                    ->placeholder('No variant')
                    ->searchable(),
                TextColumn::make('variant.color')
                    ->label('Color')
                    ->placeholder('No variant'),
                TextColumn::make('variant.size')
                    ->label('Size')
                    ->placeholder('No variant'),
                TextColumn::make('quantity')
                    ->sortable(),
                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
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
