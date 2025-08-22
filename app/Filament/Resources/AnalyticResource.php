<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AnalyticResource\Pages\ListAnalytics;
use App\Filament\Resources\AnalyticResource\Pages\CreateAnalytic;
use App\Filament\Resources\AnalyticResource\Pages\ViewAnalytic;
use App\Filament\Resources\AnalyticResource\Pages\EditAnalytic;
use App\Filament\Resources\AnalyticResource\Pages;
use App\Filament\Resources\AnalyticResource\RelationManagers;
use App\Models\Analytic;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnalyticResource extends Resource
{
    protected static ?string $model = Analytic::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('service')
                    ->required()
                    ->maxLength(255),
                TextInput::make('tracking_id')
                    ->required()
                    ->maxLength(255),
                Toggle::make('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service')
                    ->searchable(),
                TextColumn::make('tracking_id')
                    ->searchable(),
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
            'index' => ListAnalytics::route('/'),
            'create' => CreateAnalytic::route('/create'),
            'view' => ViewAnalytic::route('/{record}'),
            'edit' => EditAnalytic::route('/{record}/edit'),
        ];
    }
}
