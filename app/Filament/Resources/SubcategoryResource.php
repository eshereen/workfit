<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Subcategory;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubcategoryResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\SubcategoryResource\RelationManagers;
use App\Filament\Resources\SubcategoryResource\Pages\EditSubcategory;
use App\Filament\Resources\SubcategoryResource\Pages\ViewSubcategory;
use App\Filament\Resources\SubcategoryResource\Pages\CreateSubcategory;
use App\Filament\Resources\SubcategoryResource\Pages\ListSubcategories;
use UnitEnum;
use BackedEnum;
class SubcategoryResource extends Resource
{
    protected static ?string $model = Subcategory::class;

    protected static string | UnitEnum   | null $navigationGroup = 'Categories & Subcategories Details';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subcategory Details')
                ->schema([
                    Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                    SpatieMediaLibraryFileUpload::make('main_image')
                    ->collection('main_image')
                    ->imageEditor()
                    ->disk('public'),
                Textarea::make('description'),
                ])->columns(2)->columnSpanFull(),
                Section::make('Subcategory Status')
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
                TextColumn::make('name')
                    ->searchable(),

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubcategories::route('/'),
            'create' => CreateSubcategory::route('/create'),
            'view' => ViewSubcategory::route('/{record}'),
            'edit' => EditSubcategory::route('/{record}/edit'),
        ];
    }
}
