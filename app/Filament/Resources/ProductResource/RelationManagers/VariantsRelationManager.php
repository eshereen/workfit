<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Filament\Resources\ProductResource;
use Filament\Resources\RelationManagers\RelationManager;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $relatedResource = ProductResource::class;
    protected static ?string $recordTitleAttribute = 'sku';

    public function form(Schema $schema): Schema    {
        return $schema->schema([
            Forms\Components\TextInput::make('sku')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(64),

            Forms\Components\Select::make('size')
                ->options([
                    'XS' => 'XS',
                    'S' => 'S',
                    'M' => 'M',
                    'L' => 'L',
                    'XL' => 'XL',
                ])
                ->required(),

                Forms\Components\Select::make('color')
                ->label('Color')
                ->options(array_keys(config('colors'))) // show only the names as options
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('stock')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required(),

            Forms\Components\TextInput::make('price')
                ->numeric()
                ->step('0.01')
                ->prefix('USD'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->sortable()->searchable(),
                TextColumn::make('size')->badge(),
                TextColumn::make('color')
                ->label('Color')
                ->formatStateUsing(fn ($state) => $state) // show the color name
                ->badge()
                ->extraAttributes(fn ($state) => [
                    'style' => 'background-color: ' . (config('colors')[$state] ?? '#ccc') . '; color: white; padding: 2px 2px; border-radius: 30px; width:30px;height:30px;',
                ]),

                TextColumn::make('stock')->sortable(),
                TextColumn::make('price')->money('USD'),
                TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('deleted_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
