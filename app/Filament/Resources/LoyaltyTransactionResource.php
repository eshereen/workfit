<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\LoyaltyTransactionResource\Pages\ListLoyaltyTransactions;
use App\Filament\Resources\LoyaltyTransactionResource\Pages\CreateLoyaltyTransaction;
use App\Filament\Resources\LoyaltyTransactionResource\Pages\ViewLoyaltyTransaction;
use App\Filament\Resources\LoyaltyTransactionResource\Pages\EditLoyaltyTransaction;
use App\Filament\Resources\LoyaltyTransactionResource\Pages;
use App\Filament\Resources\LoyaltyTransactionResource\RelationManagers;
use App\Models\LoyaltyTransaction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;
use BackedEnum;

class LoyaltyTransactionResource extends Resource
{
    protected static ?string $model = LoyaltyTransaction::class;

    protected static string | UnitEnum   | null $navigationGroup = 'Orders Details';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('points')
                    ->required()
                    ->numeric(),
                TextInput::make('action')
                    ->required()
                    ->maxLength(255),
                TextInput::make('description')
                    ->maxLength(255),
                TextInput::make('source_type')
                    ->maxLength(255),
                TextInput::make('source_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('points')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('action')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('source_type')
                    ->searchable(),
                TextColumn::make('source_id')
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
            'index' => ListLoyaltyTransactions::route('/'),
            'create' => CreateLoyaltyTransaction::route('/create'),
            'view' => ViewLoyaltyTransaction::route('/{record}'),
            'edit' => EditLoyaltyTransaction::route('/{record}/edit'),
        ];
    }
}
