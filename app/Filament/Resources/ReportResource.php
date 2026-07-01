<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Moderation';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Reports';

    public static function getNavigationBadge(): ?string
    {
        $count = Report::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->options([
                    'pending'   => 'Pending',
                    'reviewed'  => 'Reviewed',
                    'actioned'  => 'Actioned',
                    'dismissed' => 'Dismissed',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable()->width('60px'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'reviewed',
                        'success' => 'actioned',
                        'gray'    => 'dismissed',
                    ]),

                Tables\Columns\TextColumn::make('reason')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'pornography' => 'danger',
                        'harmful'     => 'danger',
                        'misleading'  => 'warning',
                        'spam'        => 'gray',
                        'fake'        => 'warning',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('reportable_type')
                    ->label('Content Type')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->badge()->color('primary'),

                Tables\Columns\TextColumn::make('reportable_id')->label('Content ID'),

                Tables\Columns\TextColumn::make('details')
                    ->limit(50)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Reporter')
                    ->placeholder('Guest'),

                Tables\Columns\TextColumn::make('reporter_ip')->label('IP'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'reviewed'  => 'Reviewed',
                        'actioned'  => 'Actioned',
                        'dismissed' => 'Dismissed',
                    ]),
                Tables\Filters\SelectFilter::make('reason')
                    ->options([
                        'pornography' => 'Pornography',
                        'harmful'     => 'Harmful',
                        'misleading'  => 'Misleading',
                        'spam'        => 'Spam',
                        'fake'        => 'Fake',
                        'other'       => 'Other',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view_content')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Report $record) => self::getContentUrl($record))
                    ->openUrlInNewTab()
                    ->color('gray'),

                Tables\Actions\Action::make('approve')
                    ->label('Dismiss')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn(Report $record) => $record->update(['status' => 'dismissed']))
                    ->requiresConfirmation()
                    ->visible(fn(Report $r) => $r->status === 'pending'),

                Tables\Actions\Action::make('action')
                    ->label('Remove Content')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->action(function (Report $record) {
                        $model = $record->reportable;
                        if ($model && isset($model->status)) {
                            $model->update(['status' => 'removed']);
                        }
                        $record->update(['status' => 'actioned']);
                    })
                    ->requiresConfirmation()
                    ->visible(fn(Report $r) => in_array($r->status, ['pending', 'reviewed'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('dismiss_all')
                    ->label('Dismiss Selected')
                    ->icon('heroicon-o-check')
                    ->action(fn($records) => $records->each->update(['status' => 'dismissed']))
                    ->requiresConfirmation(),
            ]);
    }

    protected static function getContentUrl(Report $record): string
    {
        $type = class_basename($record->reportable_type);
        $id   = $record->reportable_id;
        return match($type) {
            'Listing'  => route('classifieds.show', $id),
            'Event'    => route('events.show', $id),
            'Job'      => route('jobs.show', $id),
            'Business' => route('directory.show', $id),
            default    => '#',
        };
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
        ];
    }
}
