<?php

namespace App\Filament\Resources\OrderItems\Schemas;

use App\Models\Product;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class OrderItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                ->relationship('order', 'order_number')
                ->required(),

                Select::make('product_id')
                ->relationship('product', 'name')
                ->preload()
                ->searchable()
                ->reactive()
                ->required(),

            Select::make('variant_id') // 👈 instead of product.variants.sku
                ->label('Variant')
                ->options(function (callable $get) {
                    $productId = $get('product_id');
                    if (!$productId) {
                        return [];
                    }

                    return Product::find($productId)
                        ?->variants()
                        ->pluck('sku', 'id') // 👈 you can replace sku with color/size
                        ->toArray() ?? [];
                })
                ->searchable()
                ->required()
                ->reactive()
                ->disabled(fn (callable $get) => !$get('product_id')),

            TextInput::make('quantity')
                ->numeric()
                ->required(),

            TextInput::make('price')
                ->numeric()
                ->prefix('$')
                ->required(),
        ]);
    }
}
