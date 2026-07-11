<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\PaymentHistory;
use App\Models\Plan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int    $navigationSort  = 2;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Account Information')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('city')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('province')
                        ->maxLength(255),
                    Forms\Components\DateTimePicker::make('email_verified_at')
                        ->label('Email Verified At'),
                    Forms\Components\Toggle::make('is_admin')
                        ->label('Admin Access')
                        ->helperText('Grants access to the admin panel'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active Account')
                        ->default(true),
                    Forms\Components\Textarea::make('bio')
                        ->columnSpanFull()
                        ->rows(3),
                    Forms\Components\FileUpload::make('avatar')
                        ->label('Avatar')
                        ->image()
                        ->disk('s3')
                        ->directory('avatars')
                        ->helperText('Upload or replace the user\'s profile photo.')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->label('New Password')
                        ->helperText('Leave empty to keep current password')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Subscription & Plan')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('plan')
                        ->label('Plan')
                        ->options(fn () => Plan::orderBy('sort_order')->pluck('name', 'slug')->toArray())
                        ->default('free')
                        ->required(),
                    Forms\Components\Select::make('subscription_status')
                        ->label('Subscription Status')
                        ->options([
                            'active'    => 'Active',
                            'canceling' => 'Canceling (end of period)',
                            'past_due'  => 'Past Due',
                            'canceled'  => 'Canceled',
                        ])
                        ->placeholder('— None —'),
                    Forms\Components\DateTimePicker::make('plan_expires_at')
                        ->label('Plan Expires At'),
                ]),

            Forms\Components\Section::make('Featured Credits')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('featured_credits_used')
                        ->label('Credits Used This Cycle')
                        ->numeric()
                        ->default(0)
                        ->helperText('Reset to 0 to restore credits manually.'),
                    Forms\Components\DateTimePicker::make('featured_credits_reset_at')
                        ->label('Credits Reset At')
                        ->helperText('Next auto-reset date. Set to past to force immediate reset.'),
                ]),

            Forms\Components\Section::make('Stripe IDs')
                ->columns(1)
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('stripe_customer_id')
                        ->label('Stripe Customer ID')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('stripe_subscription_id')
                        ->label('Stripe Subscription ID')
                        ->maxLength(255),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->description(fn (User $r): string => $r->email),

                Tables\Columns\TextColumn::make('plan')
                    ->label('Plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'power_seller' => 'warning',
                        'verified'     => 'info',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('subscription_status')
                    ->label('Sub Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'active'    => 'success',
                        'canceling' => 'warning',
                        'past_due'  => 'danger',
                        'canceled'  => 'gray',
                        default     => 'gray',
                    })
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('plan_expires_at')
                    ->label('Expires')
                    ->date('M d, Y')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('featured_credits_used')
                    ->label('⭐ Credits Used')
                    ->numeric()
                    ->sortable()
                    ->placeholder('0')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Verified')
                    ->date('M d, Y')
                    ->sortable()
                    ->placeholder('Not verified')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('stripe_customer_id')
                    ->label('Stripe Customer')
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('plan')
                    ->options(fn () => Plan::orderBy('sort_order')->pluck('name', 'slug')->toArray()),

                SelectFilter::make('subscription_status')
                    ->label('Subscription Status')
                    ->options([
                        'active'    => 'Active',
                        'canceling' => 'Canceling',
                        'past_due'  => 'Past Due',
                        'canceled'  => 'Canceled',
                    ]),

                TernaryFilter::make('is_admin')
                    ->label('Admin'),

                TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('view_payments')
                    ->label('Payments')
                    ->icon('heroicon-o-credit-card')
                    ->color('gray')
                    ->url(fn (User $record) => PaymentHistoryResource::getUrl('index'))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('grant_plan')
                    ->label('Grant Plan')
                    ->icon('heroicon-o-gift')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('plan')
                            ->label('Plan')
                            ->options(fn () => Plan::orderBy('sort_order')->pluck('name', 'slug')->toArray())
                            ->required(),
                        Forms\Components\DateTimePicker::make('plan_expires_at')
                            ->label('Expires At')
                            ->default(now()->addDays(30)),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update([
                            'plan'                => $data['plan'],
                            'plan_expires_at'     => $data['plan_expires_at'],
                            'subscription_status' => 'active',
                        ]);
                        Notification::make()
                            ->title('Plan granted successfully')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('cancel_subscription')
                    ->label('Cancel Sub')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Subscription')
                    ->modalDescription(fn (User $record) => "Cancel {$record->name}'s subscription and downgrade to free plan?")
                    ->visible(fn (User $record) => $record->isSubscribed())
                    ->action(function (User $record): void {
                        if ($record->stripe_subscription_id) {
                            try {
                                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                                \Stripe\Subscription::update($record->stripe_subscription_id, ['cancel_at_period_end' => false]);
                                \Stripe\Subscription::cancel($record->stripe_subscription_id);
                            } catch (\Throwable) {}
                        }
                        $record->update([
                            'plan'                   => 'free',
                            'plan_expires_at'        => null,
                            'stripe_subscription_id' => null,
                            'subscription_status'    => 'canceled',
                        ]);
                        Notification::make()
                            ->title('Subscription canceled — downgraded to free')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (User $record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn (User $record) => $record->is_active ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                    ->color(fn (User $record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update(['is_active' => !$record->is_active]);
                        Notification::make()
                            ->title($record->fresh()->is_active ? 'User activated' : 'User deactivated')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

