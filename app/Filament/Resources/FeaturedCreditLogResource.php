<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeaturedCreditLogResource\Pages;
use App\Models\FeaturedCreditLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class FeaturedCreditLogResource extends Resource
{
    protected static ?string $model = FeaturedCreditLog::class;

    protected static ?string $navigationIcon  = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Featured Logs';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int    $navigationSort  = 6;
    protected static ?string $modelLabel      = 'Featured Credit Log';
    protected static ?string $pluralModelLabel = 'Featured Credit Logs';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::whereNull('unfeatured_at')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('featured_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->description(fn (FeaturedCreditLog $r): string => $r->user?->email ?? ''),

                Tables\Columns\TextColumn::make('listing.title')
                    ->label('Listing')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->url(fn (FeaturedCreditLog $r) => $r->listing
                        ? route('classifieds.show', $r->listing->slug)
                        : null)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('featured_at')
                    ->label('Featured At')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unfeatured_at')
                    ->label('Unfeatured At')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->placeholder('Still featured'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn (FeaturedCreditLog $r) => $r->unfeatured_at ? 'Ended' : 'Active')
                    ->colors([
                        'success' => 'Active',
                        'gray'    => 'Ended',
                    ]),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->getStateUsing(function (FeaturedCreditLog $r): string {
                        $end = $r->unfeatured_at ?? now();
                        $diff = $r->featured_at->diff($end);
                        if ($diff->days > 0) return $diff->days . 'd ' . $diff->h . 'h';
                        if ($diff->h > 0) return $diff->h . 'h ' . $diff->i . 'm';
                        return $diff->i . ' min';
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(['active' => 'Active', 'ended' => 'Ended'])
                    ->query(fn ($query, array $data) => match ($data['value'] ?? null) {
                        'active' => $query->whereNull('unfeatured_at'),
                        'ended'  => $query->whereNotNull('unfeatured_at'),
                        default  => $query,
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListFeaturedCreditLogs::route('/'),
        ];
    }
}
