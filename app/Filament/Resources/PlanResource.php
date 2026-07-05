<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;
    protected static ?string $navigationIcon  = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Pricing Plans';
    protected static ?string $modelLabel      = 'Plan';
    protected static ?string $pluralModelLabel = 'Pricing Plans';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int    $navigationSort  = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Plan Details')->schema([
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Lowercase, no spaces. e.g. basic, premium')
                    ->disabled(fn ($record) => $record !== null), // slug fixed after creation

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(50),

                Forms\Components\TextInput::make('icon')
                    ->label('Icon (emoji)')
                    ->default('🆓')
                    ->maxLength(10),

                Forms\Components\TextInput::make('icon_bg')
                    ->label('Icon Background Color')
                    ->default('#f0ede8')
                    ->helperText('Hex color e.g. #f0ede8'),

                Forms\Components\TextInput::make('tagline')
                    ->maxLength(100)
                    ->placeholder('For regular community members')
                    ->columnSpanFull(),
            ])->columns(4),

            Forms\Components\Section::make('Pricing & Stripe')->schema([
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('$')
                    ->default(0)
                    ->required(),

                Forms\Components\Select::make('period')
                    ->options([
                        'forever' => 'Forever (Free)',
                        'month'   => 'Per Month',
                        'year'    => 'Per Year',
                    ])
                    ->default('month')
                    ->required(),

                Forms\Components\TextInput::make('stripe_price_id')
                    ->label('Stripe Price ID')
                    ->placeholder('price_1ABC...')
                    ->helperText('From Stripe Dashboard → Products → Price ID (starts with price_)')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower = shown first (left)'),

                Forms\Components\Toggle::make('is_popular')
                    ->label('Most Popular badge')
                    ->default(false),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])->columns(5),

            Forms\Components\Section::make('Limits & Features')->schema([
                Forms\Components\TextInput::make('post_days')
                    ->label('Post Visibility (days)')
                    ->numeric()
                    ->minValue(0)
                    ->default(7)
                    ->helperText('0 = Permanent / Auto-renew'),

                Forms\Components\TextInput::make('max_listings')
                    ->label('Max Active Listings')
                    ->numeric()
                    ->minValue(1)
                    ->default(3)
                    ->helperText('Classifieds + Jobs per user'),

                Forms\Components\TextInput::make('max_images')
                    ->label('Max Images per Post')
                    ->numeric()
                    ->minValue(1)
                    ->default(3)
                    ->helperText('Photos allowed per listing/post'),

                Forms\Components\TextInput::make('biz_listings')
                    ->label('Business Directory Listings')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->helperText('0 = Not allowed · 999 = Unlimited'),

                Forms\Components\TextInput::make('featured_credits')
                    ->label('Featured Credits / month')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),

                Forms\Components\Toggle::make('verified_badge')
                    ->label('Verified Badge'),

                Forms\Components\Toggle::make('auto_renew')
                    ->label('Auto Renew Listings'),

                Forms\Components\Toggle::make('favorites')
                    ->label('Favorites / Follow'),

                Forms\Components\Toggle::make('bulk_upload')
                    ->label('Bulk Upload'),

                Forms\Components\Toggle::make('unlimited_posts')
                    ->label('Unlimited Posts'),

                Forms\Components\Toggle::make('featured_placement')
                    ->label('Priority Search Placement'),

                Forms\Components\Toggle::make('analytics')
                    ->label('Analytics & Insights'),

                Forms\Components\Toggle::make('priority_support')
                    ->label('Priority Support'),
            ])->columns(4),

            Forms\Components\Section::make('Feature List (shown on pricing page)')->schema([
                Forms\Components\Repeater::make('features')
                    ->label('')
                    ->schema([
                        Forms\Components\TextInput::make('text')
                            ->required()
                            ->placeholder('30-day post visibility')
                            ->columnSpan(3),
                        Forms\Components\Toggle::make('included')
                            ->label('Included')
                            ->default(true)
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('highlight')
                            ->label('Bold/Blue')
                            ->default(false)
                            ->columnSpan(1),
                    ])
                    ->columns(5)
                    ->addActionLabel('+ Add Feature')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('icon')->label('')->width(30),
                Tables\Columns\TextColumn::make('name')->weight('bold')->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('usd')
                    ->formatStateUsing(fn ($state, Plan $r) => '$'.number_format($r->price, 0).' / '.$r->period),
                Tables\Columns\IconColumn::make('is_popular')->boolean()->label('Popular'),
                Tables\Columns\IconColumn::make('featured_placement')->boolean()->label('Featured'),
                Tables\Columns\TextColumn::make('post_days')->label('Post Days')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Permanent' : $state.' days'),
                Tables\Columns\TextColumn::make('max_listings')->label('Listings'),
                Tables\Columns\TextColumn::make('max_images')->label('Images'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit'   => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
