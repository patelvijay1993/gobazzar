<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserFavoriteResource\Pages;
use App\Models\UserFavorite;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserFavoriteResource extends Resource
{
    protected static ?string $model = UserFavorite::class;
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Users';
    protected static ?string $navigationLabel = 'Saved Favorites';
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool { return false; }

    public static function getNavigationBadge(): ?string
    {
        return (string) UserFavorite::count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->description(fn ($record) => $record->user?->email)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('favoriteable_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => match (class_basename($state)) {
                        'Listing'  => '🏷️ Classified',
                        'Job'      => '💼 Job',
                        'Event'    => '🎉 Event',
                        'Business' => '🏢 Business',
                        default    => class_basename($state),
                    })
                    ->badge()
                    ->color(fn ($state) => match (class_basename($state)) {
                        'Listing'  => 'info',
                        'Job'      => 'success',
                        'Event'    => 'warning',
                        'Business' => 'danger',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('favoriteable_id')
                    ->label('Item')
                    ->getStateUsing(function ($record) {
                        $item = $record->favoriteable;
                        if (!$item) return 'Deleted';
                        return $item->title ?? $item->name ?? "ID #{$item->id}";
                    })
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Saved At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('favoriteable_type')
                    ->label('Type')
                    ->options([
                        'App\\Models\\Listing'  => '🏷️ Classified',
                        'App\\Models\\Job'      => '💼 Job',
                        'App\\Models\\Event'    => '🎉 Event',
                        'App\\Models\\Business' => '🏢 Business',
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserFavorites::route('/'),
        ];
    }
}
