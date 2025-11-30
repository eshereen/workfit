<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingResource\Pages;
use App\Models\Shipping;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Actions as TablesActions;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use BackedEnum;
use UnitEnum;

class ShippingResource extends Resource
{
    protected static ?string $model = Shipping::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static UnitEnum|string|null $navigationGroup = 'Orders';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->required()
                    ->unique(
                        table: Shipping::class,
                        column: 'country_id',
                        ignorable: fn ($record) => $record,
                    ),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->step('0.01')
                    ->prefix('USD'),
                Toggle::make('is_free_for_order')
                    ->label('Free shipping for this country')
                    ->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('country.name')
                    ->label('Country')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('is_free_for_order')
                    ->label('Free')
                    ->boolean(),
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShippings::route('/'),
            'create' => Pages\CreateShipping::route('/create'),
            'edit' => Pages\EditShipping::route('/{record}/edit'),
        ];
    }
}

