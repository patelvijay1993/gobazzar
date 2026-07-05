<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlaggedPostResource\Pages;
use App\Models\FlaggedPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FlaggedPostResource extends Resource
{
    protected static ?string $model = FlaggedPost::class;
    protected static ?string $navigationIcon  = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = 'Moderation';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $navigationLabel = 'Flagged Attempts';

    public static function getNavigationBadge(): ?string
    {
        $count = FlaggedPost::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->options([
                    'pending'  => 'Pending',
                    'reviewed' => 'Reviewed',
                    'dismissed'=> 'Dismissed',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width('60px'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->description(fn (FlaggedPost $r) => $r->user?->email ?? 'Guest')
                    ->placeholder('Guest')
                    ->searchable(),

                Tables\Columns\TextColumn::make('post_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'classified'    => '🏷️ Classified',
                        'job'           => '💼 Job',
                        'event'         => '🎉 Event',
                        'business'      => '🏢 Business',
                        'business-post' => '📌 Biz Post',
                        default         => ucfirst($state),
                    })
                    ->color(fn ($state) => match ($state) {
                        'classified'    => 'info',
                        'job'           => 'success',
                        'event'         => 'warning',
                        'business'      => 'primary',
                        'business-post' => 'gray',
                        default         => 'gray',
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->limit(40)
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('flag_reason')
                    ->label('Reason')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'banned_word'         => '🤬 Banned Word',
                        'junk_phrase'         => '🗑️ Junk Phrase',
                        'repeated_chars'      => '🔁 Repeated Chars',
                        'gibberish_pattern'   => '🔤 Gibberish',
                        'too_many_links'      => '🔗 Too Many Links',
                        'too_many_phones'     => '📞 Too Many Phones',
                        'duplicate_title_desc'=> '♊ Duplicate',
                        'ai_flagged'          => '🤖 AI Flagged',
                        default               => ucwords(str_replace('_', ' ', $state)),
                    })
                    ->color(fn ($state) => match ($state) {
                        'banned_word'   => 'danger',
                        'ai_flagged'    => 'danger',
                        'junk_phrase'   => 'warning',
                        'gibberish_pattern', 'repeated_chars' => 'warning',
                        default         => 'gray',
                    }),

                Tables\Columns\TextColumn::make('flag_message')
                    ->label('Message')
                    ->limit(50)
                    ->tooltip(fn (FlaggedPost $r) => $r->flag_message)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('ip')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'  => 'warning',
                        'reviewed' => 'info',
                        'dismissed'=> 'gray',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('At')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'reviewed' => 'Reviewed',
                        'dismissed'=> 'Dismissed',
                    ]),

                Tables\Filters\SelectFilter::make('flag_reason')
                    ->label('Reason')
                    ->options([
                        'banned_word'          => 'Banned Word',
                        'junk_phrase'          => 'Junk Phrase',
                        'repeated_chars'       => 'Repeated Chars',
                        'gibberish_pattern'    => 'Gibberish',
                        'too_many_links'       => 'Too Many Links',
                        'too_many_phones'      => 'Too Many Phones',
                        'duplicate_title_desc' => 'Duplicate Title/Desc',
                        'ai_flagged'           => 'AI Flagged',
                        'content_policy'       => 'Content Policy',
                    ]),

                Tables\Filters\SelectFilter::make('post_type')
                    ->label('Post Type')
                    ->options([
                        'classified'    => '🏷️ Classified',
                        'job'           => '💼 Job',
                        'event'         => '🎉 Event',
                        'business'      => '🏢 Business',
                        'business-post' => '📌 Biz Post',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view_data')
                    ->label('Raw Data')
                    ->icon('heroicon-o-code-bracket')
                    ->color('gray')
                    ->modalContent(fn (FlaggedPost $record) => view(
                        'filament.modals.flagged-raw-data',
                        ['record' => $record]
                    ))
                    ->modalWidth('lg')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->visible(fn (FlaggedPost $r) => !empty($r->raw_data)),

                Tables\Actions\Action::make('mark_reviewed')
                    ->label('Mark Reviewed')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->action(fn (FlaggedPost $record) => $record->update(['status' => 'reviewed']))
                    ->requiresConfirmation()
                    ->visible(fn (FlaggedPost $r) => $r->status === 'pending'),

                Tables\Actions\Action::make('dismiss')
                    ->label('Dismiss')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->action(fn (FlaggedPost $record) => $record->update(['status' => 'dismissed']))
                    ->requiresConfirmation()
                    ->visible(fn (FlaggedPost $r) => $r->status !== 'dismissed'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk_reviewed')
                    ->label('Mark Reviewed')
                    ->icon('heroicon-o-eye')
                    ->action(fn ($records) => $records->each->update(['status' => 'reviewed']))
                    ->requiresConfirmation(),

                Tables\Actions\BulkAction::make('bulk_dismiss')
                    ->label('Dismiss Selected')
                    ->icon('heroicon-o-x-mark')
                    ->action(fn ($records) => $records->each->update(['status' => 'dismissed']))
                    ->requiresConfirmation(),

                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlaggedPosts::route('/'),
        ];
    }
}
