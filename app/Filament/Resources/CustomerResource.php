<?php

namespace App\Filament\Resources;

use UnitEnum;
use BackedEnum;
use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use App\Traits\HasExports;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CustomerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\Pages\EditCustomer;
use App\Filament\Resources\CustomerResource\Pages\ViewCustomer;
use App\Filament\Resources\CustomerResource\Pages\ListCustomers;
use App\Filament\Resources\CustomerResource\Pages\CreateCustomer;

class CustomerResource extends Resource
{
    use HasExports;
    protected static ?string $model = Customer::class;

    protected static string | UnitEnum   | null $navigationGroup = 'Orders Details';

    public static function getDefaultExportColumns(): array
    {
        return [
            'email' => 'Email',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone_number' => 'Phone Number',
            'billingCountry.name' => 'Billing Country',
            'billing_state' => 'Billing State',
            'billing_city' => 'Billing City',
            'billing_building_number' => 'Billing Building Number',
            'shippingCountry.name' => 'Shipping Country',
            'shipping_state' => 'Shipping State',
            'created_at' => 'Created At',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Customer Details')
                ->schema([
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Select::make('user.name')
                    ->relationship('user', 'name'),
                Select::make('country.name')
                    ->relationship('country', 'name'),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone_number')
                    ->maxLength(255),
                ])->columns(2)->columnSpanFull(),

                Fieldset::make('Billing Address')
                ->schema([
                Select::make('billingCountry.name')
                    ->relationship('billingCountry','name'),
                TextInput::make('billing_state')
                    ->maxLength(255),
                TextInput::make('billing_city')
                    ->maxLength(255),
                TextInput::make('billing_building_number')
                    ->maxLength(255),
                Textarea::make('billing_address')
                    ->columnSpanFull(),

                ])->columns(2)->columnSpanFull(),
                Fieldset::make('Shipping Address')
                ->schema([
                Select::make('shippingCountry.name')
                    ->relationship('shippingCountry','name'),

                TextInput::make('shipping_state')
                    ->maxLength(255),
                TextInput::make('shipping_city')
                    ->maxLength(255),
                    TextInput::make('shipping_building_number')
                    ->maxLength(255),
                Textarea::make('shipping_address')
                    ->columnSpanFull(),
                Toggle::make('use_billing_for_shipping')
                    ->required(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')

                    ->sortable(),
                    TextColumn::make('country.name')

                    ->sortable(),

                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->searchable(),
                TextColumn::make('loyalty_points')
                    ->label('Loyalty Points')
                    ->getStateUsing(function ($record) {
                        if ($record->user) {
                            return $record->user->loyaltyBalance();
                        }
                        return 0;
                    })
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0))
                    ->sortable(false),
                    TextColumn::make('billingCountry.name')
                        ->searchable()
                        ->sortable(),
                TextColumn::make('billing_state')
                    ->searchable(),
                TextColumn::make('billing_city')
                    ->searchable(),
                TextColumn::make('billing_building_number')
                    ->searchable(),
                TextColumn::make('shippingCountry.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('shipping_state')
                    ->searchable(),
                TextColumn::make('shipping_city')
                    ->searchable(),
                TextColumn::make('shipping_building_number')
                    ->searchable(),
                IconColumn::make('use_billing_for_shipping')
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
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ...static::getExportBulkActions(),
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
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'view' => ViewCustomer::route('/{record}'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }
}
