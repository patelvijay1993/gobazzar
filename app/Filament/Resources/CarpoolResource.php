<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarpoolResource\Pages;
use App\Models\Carpool;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CarpoolResource extends Resource
{
    protected static ?string $model = Carpool::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Classified';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationLabel = 'Carpooling';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Ride Details')->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->columnSpanFull(),
                Forms\Components\Select::make('ride_type')
                    ->options(['offer' => 'Offering a Ride', 'request' => 'Looking for a Ride'])
                    ->default('offer')->required(),
                Forms\Components\Select::make('status')
                    ->options(['draft' => 'Draft', 'active' => 'Active', 'inactive' => 'Inactive', 'completed' => 'Completed', 'flagged' => 'Flagged'])
                    ->helperText('Inactive rides are auto-deleted 7 days after going inactive.')
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

            Forms\Components\Section::make('Route & Schedule')->schema([
                Forms\Components\Select::make('from_province')
                    ->label('From Province')
                    ->options(fn () => Location::distinct()->orderBy('province')->pluck('province', 'province')->filter()->toArray())
                    ->searchable()->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('from_city', null))
                    ->required(),
                Forms\Components\Select::make('from_city')
                    ->label('From City')
                    ->options(fn (Forms\Get $get) => Location::where('province', $get('from_province'))->orderBy('city')->pluck('city', 'city')->filter()->toArray())
                    ->searchable()->required(),
                Forms\Components\Select::make('to_province')
                    ->label('To Province')
                    ->options(fn () => Location::distinct()->orderBy('province')->pluck('province', 'province')->filter()->toArray())
                    ->searchable()->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('to_city', null))
                    ->required(),
                Forms\Components\Select::make('to_city')
                    ->label('To City')
                    ->options(fn (Forms\Get $get) => Location::where('province', $get('to_province'))->orderBy('city')->pluck('city', 'city')->filter()->toArray())
                    ->searchable()->required(),
                Forms\Components\DateTimePicker::make('travel_date')->required(),
                Forms\Components\TextInput::make('seats_available')->numeric()->minValue(1)->maxValue(20)->default(1)->required(),
                Forms\Components\Toggle::make('is_recurring')->label('Recurring Ride')->live(),
                Forms\Components\TextInput::make('recurring_days')
                    ->placeholder('Mon, Wed, Fri')
                    ->visible(fn (Forms\Get $get) => (bool) $get('is_recurring')),
                Forms\Components\TextInput::make('price')->default('Free')->label('Price per Seat'),
                Forms\Components\TextInput::make('vehicle')->placeholder('Toyota Camry, White'),
            ])->columns(2),

            Forms\Components\Section::make('Contact')->schema([
                Forms\Components\TextInput::make('contact_name'),
                Forms\Components\TextInput::make('contact_phone'),
                Forms\Components\TextInput::make('contact_email')->email(),
            ])->columns(3),

            Forms\Components\Section::make('Media & Settings')->schema([
                Forms\Components\FileUpload::make('image')->image()->disk(config('filesystems.default'))->directory('carpooling')->columnSpanFull(),
                Forms\Components\TagsInput::make('tags'),
                Forms\Components\Toggle::make('is_featured'),
                Forms\Components\Toggle::make('chat_enabled')
                    ->label('Chat Enabled')
                    ->helperText('Allow visitors to chat with the poster. Owner can also toggle this.')
                    ->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->rounded()->getStateUsing(fn ($record) => $record->image_url),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(35)->sortable(),
                Tables\Columns\TextColumn::make('ride_type')
                    ->badge()
                    ->color(fn ($state) => $state === 'offer' ? 'success' : 'info')
                    ->formatStateUsing(fn ($state) => $state === 'offer' ? 'Offering' : 'Looking for'),
                Tables\Columns\TextColumn::make('from_city')->label('From')->searchable(),
                Tables\Columns\TextColumn::make('to_city')->label('To')->searchable(),
                Tables\Columns\TextColumn::make('travel_date')->dateTime('d M Y, h:i A')->sortable(),
                Tables\Columns\TextColumn::make('seats_available')->label('Seats'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match($state) { 'active' => 'success', 'completed' => 'info', 'inactive' => 'gray', default => 'gray' }),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\ToggleColumn::make('chat_enabled')->label('Chat'),
            ])
            ->defaultSort('travel_date')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'active' => 'Active', 'inactive' => 'Inactive', 'completed' => 'Completed', 'flagged' => 'Flagged']),
                Tables\Filters\SelectFilter::make('ride_type')
                    ->options(['offer' => 'Offering', 'request' => 'Looking for']),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('publish')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn (Carpool $r) => $r->status === 'draft')
                    ->action(fn (Carpool $r) => $r->update(['status' => 'active'])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCarpools::route('/'),
            'create' => Pages\CreateCarpool::route('/create'),
            'edit'   => Pages\EditCarpool::route('/{record}/edit'),
        ];
    }
}
