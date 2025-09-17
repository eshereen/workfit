<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\ProductVariant;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductVariantResource\Pages;
use App\Filament\Resources\ProductVariantResource\RelationManagers;
use App\Filament\Resources\ProductVariantResource\Pages\EditProductVariant;
use App\Filament\Resources\ProductVariantResource\Pages\ViewProductVariant;
use App\Filament\Resources\ProductVariantResource\Pages\ListProductVariants;
use App\Filament\Resources\ProductVariantResource\Pages\CreateProductVariant;
use BackedEnum;
use UnitEnum;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-adjustments-vertical';
    protected static string | UnitEnum   | null $navigationGroup = 'Products';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product.name')
                    ->required()
                    ->relationship('product','name'),
                TextInput::make('color')
                    ->maxLength(255),
                TextInput::make('size')
                    ->maxLength(255),

                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('weight')
                    ->numeric(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_id')
                    ->numeric()
                    ->sortable(),
                    TextColumn::make('product.name')

                    ->sortable(),
                TextColumn::make('color')
                    ->searchable(),
                TextColumn::make('size')
                    ->searchable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
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
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductVariants::route('/'),
            'create' => CreateProductVariant::route('/create'),
            'view' => ViewProductVariant::route('/{record}'),
            'edit' => EditProductVariant::route('/{record}/edit'),
        ];
    }
}
