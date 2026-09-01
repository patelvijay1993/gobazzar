<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Classified';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Announcement Details')->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->options([
                        'info'    => 'Info',
                        'success' => 'Success',
                        'warning' => 'Warning',
                        'urgent'  => 'Urgent',
                    ])
                    ->default('info')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published'])
                    ->default('draft')
                    ->required(),
                Forms\Components\Textarea::make('excerpt')
                    ->label('Banner Text')
                    ->helperText('Short text shown in the site-wide banner. Falls back to the title if left blank.')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('body')
                    ->required()
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Banner & Link')->schema([
                Forms\Components\Toggle::make('is_pinned')
                    ->label('Show in site-wide banner')
                    ->helperText('Only one announcement can be pinned at a time — pinning this will unpin any other.')
                    ->live(),
                Forms\Components\TextInput::make('link_url')
                    ->label('Link URL')
                    ->url()
                    ->placeholder('https://...'),
                Forms\Components\TextInput::make('link_text')
                    ->label('Link Text')
                    ->placeholder('Learn more'),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->label('Active From')
                    ->helperText('Leave blank to start immediately.'),
                Forms\Components\DateTimePicker::make('ends_at')
                    ->label('Active Until')
                    ->helperText('Leave blank to never expire.'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'urgent'  => 'danger',
                        'warning' => 'warning',
                        'success' => 'success',
                        default   => 'info',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match($state) { 'published' => 'success', default => 'gray' }),
                Tables\Columns\IconColumn::make('is_pinned')->label('Pinned')->boolean(),
                Tables\Columns\TextColumn::make('starts_at')->label('From')->dateTime('d M Y, h:i A')->sortable()->placeholder('—'),
                Tables\Columns\TextColumn::make('ends_at')->label('Until')->dateTime('d M Y, h:i A')->sortable()->placeholder('—'),
                Tables\Columns\TextColumn::make('views')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->date()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published']),
                Tables\Filters\SelectFilter::make('type')
                    ->options(['info' => 'Info', 'success' => 'Success', 'warning' => 'Warning', 'urgent' => 'Urgent']),
            ])
            ->actions([
                Tables\Actions\Action::make('publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Announcement $r) => $r->status === 'draft')
                    ->action(fn (Announcement $r) => $r->update(['status' => 'published'])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit'   => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
