<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentHistoryResource\Pages;
use App\Models\PaymentHistory;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class PaymentHistoryResource extends Resource
{
    protected static ?string $model = PaymentHistory::class;

    protected static ?string $navigationIcon  = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Payment History';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int    $navigationSort  = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Payment Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'paid'     => 'Paid',
                            'failed'   => 'Failed',
                            'refunded' => 'Refunded',
                            'pending'  => 'Pending',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('plan_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('plan_slug')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('amount')
                        ->required()
                        ->numeric()
                        ->prefix('$'),
                    Forms\Components\TextInput::make('currency')
                        ->required()
                        ->maxLength(3)
                        ->default('usd'),
                    Forms\Components\DateTimePicker::make('paid_at')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('description')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Stripe IDs')
                ->columns(1)
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('stripe_invoice_id')->maxLength(255),
                    Forms\Components\TextInput::make('stripe_subscription_id')->maxLength(255),
                    Forms\Components\TextInput::make('stripe_payment_intent_id')->maxLength(255),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('paid_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->description(fn (PaymentHistory $r): string => $r->user?->email ?? ''),

                Tables\Columns\TextColumn::make('plan_name')
                    ->label('Plan')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'power seller' => 'warning',
                        'verified'     => 'info',
                        default        => 'gray',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('usd')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'     => 'success',
                        'failed'   => 'danger',
                        'refunded' => 'warning',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('stripe_invoice_id')
                    ->label('Invoice ID')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'paid'     => 'Paid',
                        'failed'   => 'Failed',
                        'refunded' => 'Refunded',
                        'pending'  => 'Pending',
                    ]),

                SelectFilter::make('plan_slug')
                    ->label('Plan')
                    ->options(fn () => Plan::pluck('name', 'slug')->toArray()),

                Filter::make('date_range')
                    ->label('Date Range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'],  fn ($q, $d) => $q->whereDate('paid_at', '>=', $d))
                            ->when($data['until'], fn ($q, $d) => $q->whereDate('paid_at', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getWidgets(): array
    {
        return [
            PaymentHistoryResource\Widgets\PaymentStatsOverview::class,
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentHistories::route('/'),
            'view'  => Pages\ViewPaymentHistory::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
