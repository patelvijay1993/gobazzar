<?php

namespace App\Filament\Widgets;

use App\Models\PaymentHistory;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Recent Signups & Payments';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->latest()->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('User')
                    ->searchable()
                    ->description(fn (User $r): string => $r->email),

                Tables\Columns\TextColumn::make('plan')
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
                        default     => 'gray',
                    })
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->url(fn (User $record) => \App\Filament\Resources\UserResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-pencil-square')
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}
