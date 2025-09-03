<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
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
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages\ViewOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-gift';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Details')
                ->schema([
                TextInput::make('order_number')
                    ->required()
                    ->maxLength(255),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('customer_id')
                    ->numeric(),
                ])->columns(3)->columnSpanFull(),
                Section::make('Customer Details')
                ->schema([
                TextInput::make('first_name')
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->maxLength(255),
                Select::make('country.name')
                    ->relationship('country', 'name'),
                TextInput::make('state')
                    ->maxLength(255),
                TextInput::make('city')
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('phone_number')
                    ->tel()
                    ->maxLength(255),
                ])->columns(3)->columnSpanFull(),
                Section::make('Order Summary')
                ->schema([
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('shipping_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->maxLength(3)
                    ->default('USD'),
                TextInput::make('billing_address')
                    ->required()
                    ->maxLength(255),
                TextInput::make('billing_building_number')
                    ->required()
                    ->maxLength(255),
                TextInput::make('shipping_address')
                    ->required()
                    ->maxLength(255),
                TextInput::make('shipping_building_number')
                    ->required()
                    ->maxLength(255),
                Toggle::make('use_billing_for_shipping')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                ])->columns(3)->columnSpanFull(),
                Section::make('Order Status')
                ->schema([
                TextInput::make('coupon_id')
                    ->numeric(),
                Toggle::make('is_guest')
                    ->required(),
                TextInput::make('payment_method')
                    ->required()
                    ->maxLength(255),
                Select::make('payment_status')
                    ->options(PaymentStatus::class)
                    ->required(),
                Select::make('status')
                    ->options(OrderStatus::class)
                    ->required(),
                ])->columns(3)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('customer_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('country.name')

                    ->sortable(),
                TextColumn::make('state')
                    ->searchable(),
                TextColumn::make('city')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->searchable(),
                TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('shipping_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('billing_address')
                    ->searchable(),
                TextColumn::make('billing_building_number')
                    ->searchable(),
                TextColumn::make('shipping_address')
                    ->searchable(),
                TextColumn::make('shipping_building_number')
                    ->searchable(),
                IconColumn::make('use_billing_for_shipping')
                    ->boolean(),
                TextColumn::make('coupon_id')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_guest')
                    ->boolean(),
                TextColumn::make('payment_method')
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (PaymentStatus $state): string => match ($state) {
                        PaymentStatus::PENDING => 'warning',
                        PaymentStatus::PROCESSING => 'info',
                        PaymentStatus::PAID => 'success',
                        PaymentStatus::CONFIRMED => 'success',
                        PaymentStatus::COMPLETED => 'success',
                        PaymentStatus::FAILED => 'danger',
                        PaymentStatus::CANCELLED => 'danger',
                        PaymentStatus::REFUNDED => 'info',
                        PaymentStatus::PARTIALLY_REFUNDED => 'warning',
                        PaymentStatus::DECLINED => 'danger',
                        PaymentStatus::EXPIRED => 'danger',
                        PaymentStatus::VOIDED => 'danger',
                    })
                    ->formatStateUsing(fn (PaymentStatus $state): string => $state->label()),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (OrderStatus $state): string => match ($state) {
                        OrderStatus::PENDING => 'warning',
                        OrderStatus::CONFIRMED => 'info',
                        OrderStatus::PROCESSING => 'info',
                        OrderStatus::SHIPPED => 'primary',
                        OrderStatus::DELIVERED => 'success',
                        OrderStatus::CANCELLED => 'danger',
                        OrderStatus::REFUNDED => 'info',
                        OrderStatus::RETURNED => 'warning',
                        OrderStatus::ON_HOLD => 'warning',
                        OrderStatus::BACKORDERED => 'warning',
                        OrderStatus::PARTIALLY_SHIPPED => 'primary',
                        OrderStatus::COMPLETED => 'success',
                    })
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->label()),
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
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options(PaymentStatus::class)
                    ->label('Payment Status'),
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
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'view' => ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
