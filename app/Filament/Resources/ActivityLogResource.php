<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Activity Log';
    protected static ?string $navigationGroup = 'System';
    protected static ?int    $navigationSort  = 20;

    public static function canCreate(): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->width('160px'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('Unknown (Guest)')
                    ->description(fn (ActivityLog $r) => $r->user?->email ?? $r->ip)
                    ->searchable(query: fn ($query, string $search) => $query
                        ->whereHas('user', fn ($q) => $q
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                        )
                        ->orWhere('ip', 'like', "%{$search}%")
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('action')
                    ->label('Action')
                    ->formatStateUsing(fn (ActivityLog $r) => $r->actionLabel())
                    ->badge()
                    ->color(fn (string $state) => match(true) {
                        in_array($state, ['viewed_listing','viewed_job','viewed_event','viewed_business']) => 'info',
                        $state === 'searched'                        => 'warning',
                        in_array($state, ['login','registered'])     => 'success',
                        $state === 'logout'                          => 'gray',
                        default                                      => 'primary',
                    }),

                Tables\Columns\TextColumn::make('subject_label')
                    ->label('Content')
                    ->limit(40)
                    ->placeholder('—')
                    ->getStateUsing(fn (ActivityLog $r) => $r->action === 'searched'
                        ? '🔍 ' . ($r->meta['keyword'] ?? '—') . (isset($r->meta['section']) ? ' · ' . $r->meta['section'] : '')
                        : ($r->subject_label ?? '—')
                    ),

                Tables\Columns\TextColumn::make('device')
                    ->label('Device')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'mobile'  => '📱 Mobile',
                        'tablet'  => '📋 Tablet',
                        default   => '🖥 Desktop',
                    })
                    ->color('gray'),

                Tables\Columns\TextColumn::make('ip')
                    ->label('IP')
                    ->fontFamily('mono'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'viewed_listing'  => 'Viewed Classified',
                        'viewed_job'      => 'Viewed Job',
                        'viewed_event'    => 'Viewed Event',
                        'viewed_business' => 'Viewed Business',
                        'searched'        => 'Searched',
                        'login'           => 'Logged In',
                        'logout'          => 'Logged Out',
                        'registered'      => 'Registered',
                        'post_created'    => 'Created Post',
                        'chat_started'    => 'Started Chat',
                    ]),

                Tables\Filters\Filter::make('guests_only')
                    ->label('Guests Only (Unknown)')
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereNull('user_id')),

                Tables\Filters\Filter::make('logged_in_only')
                    ->label('Logged-in Users Only')
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereNotNull('user_id')),

                Tables\Filters\Filter::make('today')
                    ->label('Today')
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereDate('created_at', today())),
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkAction::make('delete')
                    ->label('Delete Selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->action(fn ($records) => $records->each->delete())
                    ->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
