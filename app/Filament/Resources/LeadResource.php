<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model          = Lead::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel= 'All Leads';
    protected static ?string $navigationGroup= 'Advertising';
    protected static ?int    $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('category')->maxLength(100),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('city')->maxLength(100),
                    Forms\Components\TextInput::make('province')->maxLength(10),
                ]),
                Forms\Components\TextInput::make('address')->maxLength(255),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('phone')->maxLength(30),
                    Forms\Components\TextInput::make('email')->email()->maxLength(255),
                ]),
                Forms\Components\TextInput::make('website')->url()->maxLength(255),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('rating')->numeric()->maxValue(5),
                    Forms\Components\TextInput::make('review_count')->numeric(),
                ]),
                Forms\Components\Select::make('status')
                    ->options([
                        'new'            => '🆕 New',
                        'contacted'      => '📤 Contacted',
                        'interested'     => '✅ Interested',
                        'not_interested' => '❌ Not Interested',
                        'converted'      => '🎉 Converted',
                    ])->required(),
                Forms\Components\Select::make('contact_method')
                    ->options([
                        'none'      => 'None',
                        'email'     => 'Email',
                        'whatsapp'  => 'WhatsApp',
                        'both'      => 'Both',
                    ]),
                Forms\Components\Textarea::make('notes')->rows(3),
                Forms\Components\TextInput::make('google_maps_url')->url()->maxLength(500),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->sortable()->weight('semibold'),

                Tables\Columns\TextColumn::make('category')
                    ->searchable()->badge()->color('info'),

                Tables\Columns\TextColumn::make('city')
                    ->searchable()->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->formatStateUsing(fn($state) => $state ?: '—')
                    ->color(fn($state) => $state ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->formatStateUsing(fn($state) => $state ?: '—')
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->limit(25),

                Tables\Columns\TextColumn::make('rating')
                    ->formatStateUsing(fn($state) => $state ? "★ $state" : '—')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray'    => 'new',
                        'info'    => 'contacted',
                        'success' => 'interested',
                        'danger'  => 'not_interested',
                        'warning' => 'converted',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'new'            => '🆕 New',
                        'contacted'      => '📤 Contacted',
                        'interested'     => '✅ Interested',
                        'not_interested' => '❌ Not Interested',
                        'converted'      => '🎉 Converted',
                        default          => $state,
                    }),

                Tables\Columns\TextColumn::make('last_contacted_at')
                    ->label('Last Contact')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('Never'),

                Tables\Columns\TextColumn::make('source')
                    ->badge()->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new'            => '🆕 New',
                        'contacted'      => '📤 Contacted',
                        'interested'     => '✅ Interested',
                        'not_interested' => '❌ Not Interested',
                        'converted'      => '🎉 Converted',
                    ]),
                Tables\Filters\SelectFilter::make('city')
                    ->options(fn() => Lead::whereNotNull('city')->distinct()->pluck('city','city')->toArray()),
                Tables\Filters\SelectFilter::make('category')
                    ->options(fn() => Lead::whereNotNull('category')->distinct()->pluck('category','category')->toArray()),
            ])
            ->actions([
                Tables\Actions\Action::make('maps')
                    ->label('Maps')
                    ->icon('heroicon-o-map-pin')
                    ->color('danger')
                    ->url(fn(Lead $r) => $r->google_maps_url)
                    ->openUrlInNewTab()
                    ->visible(fn(Lead $r) => (bool)$r->google_maps_url),

                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->color('success')
                    ->url(fn(Lead $r) => $r->phone
                        ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $r->phone)
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn(Lead $r) => (bool)$r->phone),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_contacted')
                        ->label('Mark as Contacted')
                        ->icon('heroicon-o-paper-airplane')
                        ->action(fn($records) => $records->each->update(['status' => 'contacted', 'last_contacted_at' => now()]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('mark_interested')
                        ->label('Mark as Interested')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn($records) => $records->each->update(['status' => 'interested']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit'   => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
