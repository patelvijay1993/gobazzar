<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Category;
use App\Models\Event;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Event Details')->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->columnSpanFull(),
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', 'events')->where('is_active', true)->pluck('name', 'id'))
                    ->searchable(),
                Forms\Components\Select::make('status')
                    ->options(['draft' => 'Draft', 'active' => 'Active', 'cancelled' => 'Cancelled', 'completed' => 'Completed', 'flagged' => 'Flagged'])
                    ->default('draft')->required(),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'strike',
                        'bulletList', 'orderedList',
                        'h2', 'h3',
                        'link', 'blockquote',
                        'undo', 'redo',
                    ]),
            ])->columns(2),

            Forms\Components\Section::make('Date & Location')->schema([
                Forms\Components\DateTimePicker::make('start_date')->required(),
                Forms\Components\DateTimePicker::make('end_date'),
                Forms\Components\TextInput::make('venue')->columnSpanFull(),
                Forms\Components\Select::make('province')
                    ->options(fn () => Location::distinct()->orderBy('province')->pluck('province', 'province')->filter()->toArray())
                    ->searchable()
                    ->live()
                    ->placeholder('— Select Province —')
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('city', null)),
                Forms\Components\Select::make('city')
                    ->options(fn (Forms\Get $get) => Location::where('province', $get('province'))->orderBy('city')->pluck('city', 'city')->filter()->toArray())
                    ->searchable()
                    ->placeholder('— Select City —')
                    ->live(),
                Forms\Components\TextInput::make('price')->default('Free'),
            ])->columns(2),

            Forms\Components\Section::make('Organizer')->schema([
                Forms\Components\TextInput::make('organizer'),
                Forms\Components\TextInput::make('organizer_phone'),
                Forms\Components\TextInput::make('organizer_email')->email(),
                Forms\Components\TextInput::make('website')->url(),
            ])->columns(2),

            Forms\Components\Section::make('Media & Tags')->schema([
                Forms\Components\FileUpload::make('image')->image()->disk(config('filesystems.default'))->directory('events')->columnSpanFull(),
                Forms\Components\TagsInput::make('tags')->columnSpanFull(),
                Forms\Components\Toggle::make('is_featured'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->circular(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(35)->sortable(),
                Tables\Columns\TextColumn::make('category.name')->badge()->color('warning'),
                Tables\Columns\TextColumn::make('start_date')->dateTime('d M Y, h:i A')->sortable(),
                Tables\Columns\TextColumn::make('city'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['gray' => 'draft', 'success' => 'active', 'danger' => 'cancelled', 'info' => 'completed']),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
            ])
            ->defaultSort('start_date')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'active' => 'Active', 'cancelled' => 'Cancelled', 'completed' => 'Completed', 'flagged' => 'Flagged']),
                Tables\Filters\SelectFilter::make('category')->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('publish')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn (Event $r) => $r->status === 'draft')
                    ->action(fn (Event $r) => $r->update(['status' => 'active'])),
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
            'index'  => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit'   => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}

