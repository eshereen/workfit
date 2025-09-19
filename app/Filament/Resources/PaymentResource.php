<?php

namespace App\Filament\Resources;

use UnitEnum;
use BackedEnum;
use Filament\Forms;
use Filament\Tables;
use App\Models\Payment;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PaymentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Filament\Resources\PaymentResource\Pages\EditPayment;
use App\Filament\Resources\PaymentResource\Pages\ViewPayment;
use App\Filament\Resources\PaymentResource\Pages\ListPayments;
use App\Filament\Resources\PaymentResource\Pages\CreatePayment;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string | UnitEnum   | null $navigationGroup = 'Orders Details';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required(),
                TextInput::make('provider')
                    ->required()
                    ->maxLength(255),
                TextInput::make('provider_reference')
                    ->maxLength(255),
                TextInput::make('status')
                   
                    ->maxLength(255)
                    ->default('initiated'),
                TextInput::make('currency')
                    ->required()
                    ->maxLength(3),
                TextInput::make('amount_minor')
                    ->required()
                    ->numeric(),
                TextInput::make('return_url')
                    ->maxLength(255),
                TextInput::make('cancel_url')
                    ->maxLength(255),
                TextInput::make('webhook_signature')
                    ->maxLength(255),
                TextInput::make('meta'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('provider')
                    ->searchable(),
                TextColumn::make('provider_reference')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('amount_minor')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('return_url')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cancel_url')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('webhook_signature')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                DeleteAction::make(), 
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
            'index' => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'view' => ViewPayment::route('/{record}'),
            'edit' => EditPayment::route('/{record}/edit'),
        ];
    }
}
