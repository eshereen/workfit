<?php

namespace App\Filament\Resources\Banners\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Str;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('preview')
                    ->label('Preview')
                    ->disk('public')
                    ->getStateUsing(function ($record) {
                        // Show poster_image for videos if available, otherwise show image
                        if ($record->media_type === 'video') {
                            return $record->poster_image ?? null;
                        }
                        return $record->image ?? null;
                    })
                    ->defaultImageUrl(url('/images/video-placeholder.png'))
                    ->square()
                    ->size(80),

                TextColumn::make('media_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->icon(fn (string $state): string => match ($state) {
                        'image' => 'heroicon-o-photo',
                        'video' => 'heroicon-o-video-camera',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'image' => 'success',
                        'video' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('section')
                    ->label('Section')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('link_type')
                    ->label('Link Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'category' => 'success',
                        'subcategory' => 'warning',
                        'url' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Str::title($state)),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->default('—')
                    ->limit(20),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('section')
                    ->options(function () {
                        return \App\Models\Banner::distinct('section')
                            ->pluck('section', 'section')
                            ->toArray();
                    }),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All banners')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
           
            ->defaultSort('sort_order', 'asc');
    }
}
