<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ViewProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\RelationManagers\VariantsRelationManager;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $recordTitleAttribute = 'name';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar-square';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Details')
                ->schema([
                    TextInput::make('name')
                    ->required()
                    ->maxLength(255)->columnSpanFull(),
                Select::make('category.name')
                    ->relationship('category', 'name'),
                Select::make('subcategory.name')
                    ->relationship('subcategory', 'name'),

                    Textarea::make('description')
                    ->columnSpanFull(),

                SpatieMediaLibraryFileUpload::make('main_image')
                    ->collection('main_image')
                    ->imageEditor()
                    ->disk('public'),
                SpatieMediaLibraryFileUpload::make('product_images')
                    ->collection('product_images')
                    ->multiple()
                    ->imageEditor()
                    ->disk('public'),

                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('compare_price')
                    ->numeric(),
                ])->columns(2)->columnSpanFull(),
                Section::make('Product Status')
                ->schema([
                Toggle::make('featured')
                    ->required(),
                Toggle::make('active')
                    ->required(),
            ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('main_image')
                ->collection('main_image')
                    ->circular(),
                TextColumn::make('category.name')
                    ->sortable(),
                TextColumn::make('subcategory.name')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('compare_price')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('featured')
                    ->boolean(),
                IconColumn::make('active')
                    ->boolean(),
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
                   VariantsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
