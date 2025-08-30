<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;

use App\Filament\Resources\ProductResource;

use Filament\Resources\RelationManagers\RelationManager;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

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

    public function isReadOnly(): bool
{
    return false;
}

}
