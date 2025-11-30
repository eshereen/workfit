<?php

namespace App\Filament\Resources;

use UnitEnum;
use Filament\Forms;
use Filament\Tables;
use App\Models\Newsletter;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Traits\HasExports;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Toggle;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\NewsletterResource\Pages;
use App\Filament\Resources\NewsletterResource\RelationManagers;
use App\Filament\Resources\NewsletterResource\Pages\EditNewsletter;
use App\Filament\Resources\NewsletterResource\Pages\ViewNewsletter;
use App\Filament\Resources\NewsletterResource\Pages\ListNewsletters;
use App\Filament\Resources\NewsletterResource\Pages\CreateNewsletter;

class NewsletterResource extends Resource
{
    use HasExports;

    protected static ?string $model = Newsletter::class;

    protected static string | UnitEnum   | null $navigationGroup = 'Settings';

    /**
     * Get default export columns for Newsletter
     *
     * @return array
     */
    public static function getDefaultExportColumns(): array
    {
        return [
            'email' => 'Email',
            'verified' => 'Verified',
            'created_at' => 'Created At',
        ];
    }
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('token')
                    ->required()
                    ->maxLength(255),
                Toggle::make('verified')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('token')
                    ->searchable(),
                IconColumn::make('verified')
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
            'index' => ListNewsletters::route('/'),
            'create' => CreateNewsletter::route('/create'),
            'view' => ViewNewsletter::route('/{record}'),
            'edit' => EditNewsletter::route('/{record}/edit'),
        ];
    }
}
