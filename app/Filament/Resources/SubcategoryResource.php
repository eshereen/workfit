<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\SubCategory;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubCategoryResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\SubCategoryResource\RelationManagers;
use App\Filament\Resources\SubCategoryResource\Pages\EditSubCategory;
use App\Filament\Resources\SubCategoryResource\Pages\ViewSubCategory;
use App\Filament\Resources\SubCategoryResource\Pages\CreateSubCategory;
use App\Filament\Resources\SubCategoryResource\Pages\ListSubCategories;

class SubCategoryResource extends Resource
{
    protected static ?string $model = SubCategory::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-inbox-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SpatieMediaLibraryFileUpload::make('main_image')
                ->collection('main_image')
                ->imageEditor()
                ->disk('public'),
                Select::make('category.name')
                    ->required()
                   ->relationship('category', 'name'),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->columnSpanFull(),
                Toggle::make('featured')
                    ->required(),
                Toggle::make('active')
                    ->required(),
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
                TextColumn::make('slug')
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
            'index' => ListSubCategories::route('/'),
            'create' => CreateSubCategory::route('/create'),
            'view' => ViewSubCategory::route('/{record}'),
            'edit' => EditSubCategory::route('/{record}/edit'),
        ];
    }
}
