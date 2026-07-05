<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvertisementResource\Pages;
use App\Models\AdStat;
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
    protected static ?string $navigationGroup = 'Advertising';
    protected static ?int    $navigationSort  = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([

            // ── Left column (2/3 width) ───────────────────────────────────
            Forms\Components\Group::make()->schema([

                // Section 1: Basic Info
                Forms\Components\Section::make('Basic Information')
                    ->description('Ad title and destination URL.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(150)
                            ->placeholder('e.g. Anne Insurance — Summer Special')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('click_url')
                            ->label('Destination URL')
                            ->required()
                            ->url()
                            ->prefix('🔗')
                            ->placeholder('https://example.com/landing-page')
                            ->helperText('Users will be taken here when they click the ad.')
                            ->columnSpanFull(),
                    ])->columns(1),

                // Section 2: Position & Creative
                Forms\Components\Section::make('Ad Position & Creative')
                    ->description('Choose where the ad appears and upload your image.')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\Select::make('position')
                            ->options([
                                'home-banner' => '🏠 Home Top Banner — 1200 × 120 px',
                                'sidebar'     => '📌 Sidebar — 300 × 250 px',
                                'inline'      => '📄 Inline (between listings) — 800 × 120 px',
                            ])
                            ->required()
                            ->default('sidebar')
                            ->live()
                            ->native(false)
                            ->helperText('Select a slot — then upload an image matching that size.'),

                        Forms\Components\Select::make('category_type')
                            ->label('Show On Section')
                            ->options(fn (Get $get) => $get('position') === 'home-banner'
                                ? ['all' => '🏠 Home Page Only']
                                : [
                                    'all'         => '📋 All Sections',
                                    'classifieds' => '🛒 Classifieds',
                                    'jobs'        => '💼 Jobs',
                                    'events'      => '🎉 Events',
                                    'directory'   => '🏢 Business Directory',
                                ])
                            ->required()
                            ->default('all')
                            ->native(false)
                            ->live()
                            ->visible(fn (Get $get) => $get('position') !== null)
                            ->helperText(fn (Get $get) => $get('position') === 'home-banner'
                                ? 'Home Banner only appears on the homepage.'
                                : 'Restrict this ad to a specific section, or show everywhere.'),

                        Forms\Components\FileUpload::make('image')
                            ->label(fn (Get $get) => match($get('position')) {
                                'home-banner' => 'Banner Image (recommended: 1200 × 120 px)',
                                'inline'      => 'Inline Image (recommended: 800 × 120 px)',
                                default       => 'Sidebar Image (recommended: 300 × 250 px)',
                            })
                            ->image()
                            ->required()
                            ->directory('ads')
                            ->imagePreviewHeight('120')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->helperText('Max 2 MB — JPG, PNG, WebP or GIF.')
                            ->columnSpanFull(),
                    ])->columns(2),

            ])->columnSpan(2),

            // ── Right column (1/3 width) ──────────────────────────────────
            Forms\Components\Group::make()->schema([

                // Section 3: Status
                Forms\Components\Section::make('Status')
                    ->description('Control visibility of this ad.')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ad is Active')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->helperText('Inactive ads will not be displayed.'),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->suffix('↑ lower first')
                            ->helperText('Lower number = shown before others in same slot.'),

                        Forms\Components\TextInput::make('slide_duration')
                            ->label('Slide Duration')
                            ->numeric()
                            ->default(3)
                            ->minValue(1)
                            ->maxValue(30)
                            ->suffix('sec')
                            ->helperText('How long this ad shows before the next one slides in.'),
                    ]),

                // Section 4: Schedule
                Forms\Components\Section::make('Schedule')
                    ->description('Optionally set a date range.')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Forms\Components\DatePicker::make('starts_at')
                            ->label('Start Date')
                            ->nullable()
                            ->native(false)
                            ->placeholder('Run immediately')
                            ->helperText('Leave empty to start right away.'),

                        Forms\Components\DatePicker::make('ends_at')
                            ->label('End Date')
                            ->nullable()
                            ->native(false)
                            ->placeholder('No end date')
                            ->helperText('Leave empty to run indefinitely.')
                            ->after('starts_at'),
                    ]),

                // Section 5: Location Scope
                Forms\Components\Section::make('Location Targeting')
                    ->description('Where in Canada should this ad show?')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\Select::make('scope')
                            ->options([
                                'canada'   => '🍁 All Canada',
                                'province' => '📍 Province only',
                                'city'     => '🏙 Specific City',
                            ])
                            ->default('canada')
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\Select::make('province')
                            ->options(Location::activeProvinces()->mapWithKeys(fn ($p) => [$p => $p]))
                            ->searchable()
                            ->native(false)
                            ->live()
                            ->visible(fn (Get $get) => in_array($get('scope'), ['province', 'city']))
                            ->required(fn (Get $get) => in_array($get('scope'), ['province', 'city'])),

                        Forms\Components\Select::make('city')
                            ->options(fn (Get $get) => $get('province')
                                ? Location::activeCities($get('province'))->mapWithKeys(fn ($c) => [$c => $c])
                                : [])
                            ->searchable()
                            ->native(false)
                            ->visible(fn (Get $get) => $get('scope') === 'city')
                            ->required(fn (Get $get) => $get('scope') === 'city')
                            ->helperText('Select province first.'),
                    ]),

            ])->columnSpan(1),

        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk(config('filesystems.default'))
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

                Tables\Columns\TextColumn::make('total_impressions')
                    ->label('Impressions')
                    ->getStateUsing(fn (Advertisement $r) => $r->stats()->sum('impressions'))
                    ->numeric()
                    ->sortable(false)
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_clicks')
                    ->label('Clicks')
                    ->getStateUsing(fn (Advertisement $r) => $r->stats()->sum('clicks'))
                    ->numeric()
                    ->sortable(false)
                    ->color('success'),

                Tables\Columns\TextColumn::make('ctr')
                    ->label('CTR')
                    ->getStateUsing(function (Advertisement $r) {
                        $impressions = $r->stats()->sum('impressions');
                        $clicks      = $r->stats()->sum('clicks');
                        if (!$impressions) return '—';
                        return round(($clicks / $impressions) * 100, 2) . '%';
                    })
                    ->sortable(false)
                    ->badge()
                    ->color('warning'),
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
                Tables\Actions\Action::make('analytics')
                    ->label('Analytics')
                    ->icon('heroicon-o-chart-bar-square')
                    ->color('info')
                    ->url(fn (Advertisement $r) => \App\Filament\Pages\AdAnalytics::getUrl() . '?ad=' . $r->id),
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

