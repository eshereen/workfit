<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use App\Models\Category;
use App\Models\Subcategory;

class BannersForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Banner Information')
                    ->schema([
                        TextInput::make('section')
                            ->required()
                            ->label('Section Identifier')
                            ->helperText('e.g., hero, featured-1, featured-2, promo-banner')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('title')
                            ->label('Title')
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3),

                        Select::make('media_type')
                            ->label('Media Type')
                            ->options([
                                'image' => 'Image',
                                'video' => 'Video',
                            ])
                            ->default('image')
                            ->live(),

                        FileUpload::make('image')
                            ->label('Banner Image')
                            ->image()
                            ->required(fn ($get) => $get('media_type') === 'image')
                            ->disk('public')
                            ->directory('banners/images')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(5120) // 5MB
                            ->visible(fn ($get) => $get('media_type') === 'image'),

                        FileUpload::make('video')
                            ->label('Banner Video')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                            ->required(fn ($get) => $get('media_type') === 'video')
                            ->disk('public')
                            ->directory('banners/videos')
                            ->visibility('public')
                            ->maxSize(51200) // 50MB
                            ->helperText('Accepted formats: MP4, WebM, OGG. Max size: 50MB')
                            ->visible(fn ($get) => $get('media_type') === 'video'),

                        FileUpload::make('poster_image')
                            ->label('Video Poster Image (Optional)')
                            ->image()
                            ->disk('public')
                            ->directory('banners/posters')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(5120) // 5MB
                            ->helperText('Optional thumbnail/preview image for the video')
                            ->visible(fn ($get) => $get('media_type') === 'video'),
                    ])->columns(1),

                Section::make('Button & Link Configuration')
                    ->schema([
                        TextInput::make('button_text')
                            ->label('Button Text')
                            ->placeholder('Shop Now, Explore, Learn More, etc.')
                            ->maxLength(255),

                        Select::make('link_type')
                            ->label('Link Type')
                            ->options([
                                'none' => 'No Link',
                                'category' => 'Link to Category',
                                'subcategory' => 'Link to Subcategory',
                                'url' => 'Custom URL',
                            ])
                            ->default('none')
                            ->live()
                            ->required(),

                        Select::make('category_id')
                            ->label('Category')
                            ->options(Category::where('active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->visible(fn ($get) => in_array($get('link_type'), ['category', 'subcategory']))
                            ->live()
                            ->afterStateUpdated(function ($set) {
                                $set('subcategory_id', null);
                            }),

                        Select::make('subcategory_id')
                            ->label('Subcategory')
                            ->options(function ($get) {
                                $categoryId = $get('category_id');
                                if (!$categoryId) {
                                    return [];
                                }
                                return Subcategory::where('category_id', $categoryId)
                                    ->where('active', true)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->visible(fn ($get) => $get('link_type') === 'subcategory'),

                        TextInput::make('custom_url')
                            ->label('Custom URL')
                            ->url()
                            ->placeholder('https://example.com')
                            ->visible(fn ($get) => $get('link_type') === 'url'),
                    ])->columns(1),

                Section::make('Button 2 & Link Configuration (Optional)')
                    ->schema([
                        TextInput::make('button_text_2')
                            ->label('Button 2 Text')
                            ->placeholder('Shop Now, Explore, Learn More, etc.')
                            ->maxLength(255),

                        Select::make('link_type_2')
                            ->label('Link Type')
                            ->options([
                                'none' => 'No Link',
                                'category' => 'Link to Category',
                                'subcategory' => 'Link to Subcategory',
                                'url' => 'Custom URL',
                            ])
                            ->default('none')
                            ->live(),

                        Select::make('category_id_2')
                            ->label('Category')
                            ->options(Category::where('active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->visible(fn ($get) => in_array($get('link_type_2'), ['category', 'subcategory']))
                            ->live()
                            ->afterStateUpdated(function ($set) {
                                $set('subcategory_id_2', null);
                            }),

                        Select::make('subcategory_id_2')
                            ->label('Subcategory')
                            ->options(function ($get) {
                                $categoryId = $get('category_id_2');
                                if (!$categoryId) {
                                    return [];
                                }
                                return Subcategory::where('category_id', $categoryId)
                                    ->where('active', true)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->visible(fn ($get) => $get('link_type_2') === 'subcategory'),

                        TextInput::make('custom_url_2')
                            ->label('Custom URL')
                            ->url()
                            ->placeholder('https://example.com')
                            ->visible(fn ($get) => $get('link_type_2') === 'url'),
                    ])->columns(1)
                    ->collapsed(),

                Section::make('Display Settings')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                    ])->columns(1),
            ])
            ->columns(3);
    }
}
