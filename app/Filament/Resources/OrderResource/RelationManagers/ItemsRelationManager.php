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
use Illuminate\Support\Facades\Log;

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
                \Filament\Tables\Columns\ImageColumn::make('product.main_image')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => $record->product?->getFirstMediaUrl('main_image', 'thumb_webp') 
                        ?: $record->product?->getFirstMediaUrl('main_image'))
                    ->defaultImageUrl(url('https://via.placeholder.com/150x150?text=No+Image'))
                    ->size(50)
                    ->circular(false)
                    ->extraImgAttributes(['class' => 'object-cover rounded']),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('variant.sku')
                    ->label('SKU')
                    ->placeholder('No variant')
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        // Debug: Log the variant data
                        \Log::info('OrderItem Debug', [
                            'order_item_id' => $record->id,
                            'product_variant_id' => $record->product_variant_id,
                            'variant_loaded' => $record->relationLoaded('variant'),
                            'variant_data' => $record->variant ? $record->variant->toArray() : 'null'
                        ]);
                        
                        return $record->variant?->sku ?? 'No variant';
                    }),
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
