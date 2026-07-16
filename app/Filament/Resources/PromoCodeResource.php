<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\Plan;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;
    protected static ?string $navigationIcon  = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Promo Codes';
    protected static ?string $modelLabel      = 'Promo Code';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int    $navigationSort  = 4;

    public static function form(Form $form): Form
    {
        $planOptions = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name', 'slug')
            ->toArray();

        return $form->schema([
            Forms\Components\Section::make('Promo Code')->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(32)
                    ->extraInputAttributes(['style' => 'text-transform:uppercase'])
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                    ->helperText('Case-insensitive. Alphanumeric recommended.')
                    ->columnSpan(1),
                Forms\Components\Select::make('plan_slug')
                    ->label('Plan to Grant')
                    ->options($planOptions)
                    ->required()
                    ->searchable()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('duration_months')
                    ->label('Duration (months)')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(120),
                Forms\Components\TextInput::make('max_uses')
                    ->label('Max Uses (0 = unlimited)')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Code Expires At')
                    ->nullable()
                    ->helperText('Leave blank for no expiry.'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Forms\Components\TextInput::make('description')
                    ->label('Internal Note')
                    ->nullable()
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('plan_slug')
                    ->label('Plan')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('duration_months')
                    ->label('Duration')
                    ->formatStateUsing(fn ($state) => $state . ' mo'),
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used')
                    ->sortable()
                    ->formatStateUsing(fn ($state, PromoCode $r) =>
                        $r->max_uses > 0 ? "{$state} / {$r->max_uses}" : "{$state} / ∞"
                    ),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('M j, Y')
                    ->placeholder('Never')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Note')
                    ->limit(40)
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
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
            'index'  => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit'   => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
