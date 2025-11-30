<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\ProductVariant;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Illuminate\Support\Facades\Log;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $recordTitleAttribute = 'sku';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('sku')
                ->required()
                ->unique(
                    table: ProductVariant::class,
                    column: 'sku',
                    ignorable: fn ($record) => $record
                ),
            Select::make('size')
                ->options([
                    'XS' => 'XS',
                    'S' => 'S',
                    'M' => 'M',
                    'L' => 'L',
                    'XL' => 'XL',
                    'XXL' => 'XXL',
                    'XXXL' => 'XXXL',
                ])
                ->required(),
            Select::make('color')
                ->label('Color')
                ->options(array_combine(array_keys(config('colors')), array_keys(config('colors')))) // key => value both as color names
                ->searchable()
                ->required(),
            TextInput::make('stock')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required(),

            TextInput::make('price')
                ->numeric()
                ->step('0.01')
                ->prefix('USD'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->sortable()->searchable(),
                TextColumn::make('size')->badge(),
                TextColumn::make('color')
                ->label('Color')
                ->formatStateUsing(function ($state) {
                    // Debug: Log the state value
                    Log::info('Color state value:', ['state' => $state, 'type' => gettype($state)]);

                    // If state is a number, get the color name from config keys
                    if (is_numeric($state)) {
                        $colorKeys = array_keys(config('colors'));
                        $colorName = $colorKeys[$state - 1] ?? 'Unknown';
                    } else {
                        $colorName = $state;
                    }

                    $colorCode = config('colors')[$colorName] ?? '#ccc';
                    $textColor = $this->getContrastColor($colorCode);

                    return "<span style='background-color: {$colorCode}; color: {$textColor}; padding: 4px 8px; border-radius: 4px; display: inline-block;'>{$colorName}</span>";
                })
                ->html(),

            TextColumn::make('stock')->sortable(),
            TextColumn::make('price')->money('USD'),
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
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),

                ]),
            ]);
    }

    public function isReadOnly(): bool
{
    return false;
}

    /**
     * Get contrast color (black or white) for better text readability
     */
    private function getContrastColor($hexColor)
    {
        // Remove # if present
        $hexColor = ltrim($hexColor, '#');

        // Convert to RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));

        // Calculate luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        // Return black for light colors, white for dark colors
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }

}
