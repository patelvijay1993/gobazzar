<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvertisementResource\Pages;
use App\Models\Advertisement;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdvertisementResource extends Resource
{
    protected static ?string $model = Advertisement::class;
    protected static ?string $navigationIcon  = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Paid Ads';
    protected static ?string $modelLabel      = 'Advertisement';
    protected static ?string $pluralModelLabel = 'Advertisements';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int    $navigationSort  = 9;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Ad Details')->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(150)
                    ->placeholder('e.g. Anne Insurance — Summer Special')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('click_url')
                    ->label('Click URL')
                    ->required()
                    ->url()
                    ->placeholder('https://example.com')
                    ->columnSpanFull(),
            ])->columns(1),

            Forms\Components\Section::make('Position & Image')->schema([
                Forms\Components\Select::make('position')
                    ->options([
                        'home-banner' => 'Home Top Banner (1200×120 px)',
                        'sidebar'     => 'Sidebar (300×250 px)',
                        'inline'      => 'Inline Between Listings (800×120 px)',
                    ])
                    ->required()
                    ->default('sidebar')
                    ->live()
                    ->helperText('Upload image matching the recommended size for sharpest display.'),

                Forms\Components\FileUpload::make('image')
                    ->label(fn (Get $get) => match($get('position')) {
                        'home-banner' => 'Banner Image (1200×120 px)',
                        'inline'      => 'Inline Image (800×120 px)',
                        default       => 'Sidebar Image (300×250 px)',
                    })
                    ->image()
                    ->required()
                    ->directory('ads')
                    ->imagePreviewHeight('100')
                    ->maxSize(2048)
                    ->helperText(fn (Get $get) => match($get('position')) {
                        'home-banner' => 'Recommended: 1200×120 px — wide horizontal banner',
                        'inline'      => 'Recommended: 800×120 px — horizontal strip',
                        default       => 'Recommended: 300×250 px — standard rectangle',
                    })
                    ->columnSpanFull(),
            ])->columns(1),

            Forms\Components\Section::make('Location Scope')->schema([
                Forms\Components\Select::make('scope')
                    ->options([
                        'canada'   => 'All Canada',
                        'province' => 'Province',
                        'city'     => 'City',
                    ])
                    ->default('canada')
                    ->required()
                    ->live()
                    ->helperText('Where should this ad appear?'),

                Forms\Components\Select::make('province')
                    ->options(Location::activeProvinces()->mapWithKeys(fn ($p) => [$p => $p]))
                    ->searchable()
                    ->live()
                    ->visible(fn (Get $get) => in_array($get('scope'), ['province', 'city']))
                    ->required(fn (Get $get) => in_array($get('scope'), ['province', 'city'])),

                Forms\Components\Select::make('city')
                    ->options(fn (Get $get) => $get('province')
                        ? Location::activeCities($get('province'))->mapWithKeys(fn ($c) => [$c => $c])
                        : [])
                    ->searchable()
                    ->visible(fn (Get $get) => $get('scope') === 'city')
                    ->required(fn (Get $get) => $get('scope') === 'city'),
            ])->columns(3),

            Forms\Components\Section::make('Schedule & Settings')->schema([
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),

                Forms\Components\DatePicker::make('starts_at')
                    ->label('Start Date')
                    ->nullable(),

                Forms\Components\DatePicker::make('ends_at')
                    ->label('End Date')
                    ->nullable()
                    ->helperText('Leave empty to run indefinitely.'),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower = shown first.'),

                Forms\Components\Select::make('category_type')
                    ->label('Show On Page')
                    ->options(fn (Get $get) => $get('position') === 'home-banner'
                        ? ['all' => 'Home Page Only']
                        : [
                            'all'         => 'All Inner Pages',
                            'classifieds' => 'Classifieds',
                            'jobs'        => 'Jobs',
                            'events'      => 'Events',
                            'directory'   => 'Directory',
                        ])
                    ->required()
                    ->default('all')
                    ->live()
                    ->visible(fn (Get $get) => $get('position') !== null)
                    ->helperText(fn (Get $get) => $get('position') === 'home-banner'
                        ? 'Home Top Banner only shows on the home page.'
                        : 'Which inner page section should this ad appear in?'),

                Forms\Components\TextInput::make('slide_duration')
                    ->label('Slide Duration (sec)')
                    ->numeric()
                    ->default(3)
                    ->minValue(1)
                    ->maxValue(30)
                    ->suffix('sec')
                    ->helperText('Seconds before next ad shows (applies to all ads in same position).'),
            ])->columns(5),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('s3')
                    ->width(80)
                    ->height(40),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category_type')
                    ->label('Page')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'all'         => 'All Pages',
                        'classifieds' => 'Classifieds',
                        'jobs'        => 'Jobs',
                        'events'      => 'Events',
                        'directory'   => 'Directory',
                        default       => $state,
                    }),

                Tables\Columns\TextColumn::make('position')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'home-banner' => 'success',
                        'sidebar'     => 'info',
                        'inline'      => 'warning',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'home-banner' => 'Home Banner',
                        'sidebar'     => 'Sidebar',
                        'inline'      => 'Inline',
                        default       => $state,
                    }),

                Tables\Columns\TextColumn::make('scope')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'canada'   => 'success',
                        'province' => 'info',
                        'city'     => 'warning',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state, Advertisement $r) => match($state) {
                        'canada'   => 'All Canada',
                        'province' => $r->province,
                        'city'     => $r->city.', '.$r->province,
                        default    => $state,
                    }),

                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),

                Tables\Columns\TextColumn::make('starts_at')->date()->placeholder('—'),
                Tables\Columns\TextColumn::make('ends_at')->date()->placeholder('Forever'),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('position')
                    ->options([
                        'home-banner' => 'Home Banner',
                        'sidebar'     => 'Sidebar',
                        'inline'      => 'Inline',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAdvertisements::route('/'),
            'create' => Pages\CreateAdvertisement::route('/create'),
            'edit'   => Pages\EditAdvertisement::route('/{record}/edit'),
        ];
    }
}
