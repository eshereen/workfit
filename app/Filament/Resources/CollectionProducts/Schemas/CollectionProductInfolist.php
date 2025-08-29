<?php

namespace App\Filament\Resources\CollectionProducts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Select;
use Filament\Schemas\Schema;

class CollectionProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('collection.name')
                    ->relatinship('collection','name'),
               Select::make('product.name')
                    ->relationship('product','name'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
