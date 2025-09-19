<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\WishlistResource\Pages\ListWishlists;
use App\Filament\Resources\WishlistResource\Pages\CreateWishlist;
use App\Filament\Resources\WishlistResource\Pages\ViewWishlist;
use App\Filament\Resources\WishlistResource\Pages\EditWishlist;
use App\Filament\Resources\WishlistResource\Pages;
use App\Filament\Resources\WishlistResource\RelationManagers;
use App\Models\Wishlist;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;
use Filament\Actions\DeleteAction;

class WishlistResource extends Resource
{
    protected static ?string $model = Wishlist::class;

    protected static string | UnitEnum   | null $navigationGroup = 'Orders Details';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('product.name')
                    ->required()
                    ->relationship('product', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                    TextColumn::make('user.name')

                    ->sortable(),
                TextColumn::make('product_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('product.name')

                    ->sortable(),
                TextColumn::make('product.variants.sku')

                    ->sortable(),
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
            'index' => ListWishlists::route('/'),
            'create' => CreateWishlist::route('/create'),
            'view' => ViewWishlist::route('/{record}'),
            'edit' => EditWishlist::route('/{record}/edit'),
        ];
    }
}
