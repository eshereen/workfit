<?php

namespace App\Filament\Resources\OrderItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

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
                ->required(),

            Select::make('variant_id')
                ->relationship('variant', 'sku')
                ->required(),

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
