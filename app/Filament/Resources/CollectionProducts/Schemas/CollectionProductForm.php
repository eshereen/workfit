<?php

namespace App\Filament\Resources\CollectionProducts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class CollectionProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
               Select::make('collection.name')
                    ->relationship('collection','name')
                    ->required(),
               Select::make('product.name')
                    ->relationship('product','name')
                    ->required()
            ]);
    }
}
