<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvertiseRequestResource\Pages;
use App\Models\AdvertiseRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdvertiseRequestResource extends Resource
{
    protected static ?string $model = AdvertiseRequest::class;
    protected static ?string $navigationIcon  = 'heroicon-o-envelope-open';
    protected static ?string $navigationLabel = 'Ad Enquiries';
    protected static ?string $modelLabel      = 'Ad Enquiry';
    protected static ?string $pluralModelLabel = 'Ad Enquiries';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int    $navigationSort  = 10;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contact Info')->schema([
                Forms\Components\TextInput::make('name')->disabled(),
                Forms\Components\TextInput::make('email')->disabled(),
                Forms\Components\TextInput::make('phone')->disabled(),
                Forms\Components\TextInput::make('business_name')->label('Business')->disabled(),
                Forms\Components\TextInput::make('website')->disabled(),
            ])->columns(2),

            Forms\Components\Section::make('Ad Details')->schema([
                Forms\Components\TextInput::make('ad_position')->label('Position')->disabled(),
                Forms\Components\TextInput::make('budget')->disabled(),
                Forms\Components\Textarea::make('message')->disabled()->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Admin')->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'new'       => 'New',
                        'contacted' => 'Contacted',
                        'closed'    => 'Closed',
                    ])
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y, h:i a')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->placeholder('—'),
                Tables\Columns\TextColumn::make('business_name')->label('Business')->placeholder('—'),

                Tables\Columns\TextColumn::make('ad_position')
                    ->label('Position')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'home-banner' => 'success',
                        'sidebar'     => 'info',
                        'inline'      => 'warning',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'home-banner' => 'Home Banner',
                        'sidebar'     => 'Sidebar',
                        'inline'      => 'Inline',
                        default       => $state ?? '—',
                    }),

                Tables\Columns\TextColumn::make('budget')->placeholder('—'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'new'       => 'warning',
                        'contacted' => 'info',
                        'closed'    => 'success',
                        default     => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new'       => 'New',
                        'contacted' => 'Contacted',
                        'closed'    => 'Closed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('View / Update'),
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
            'index'  => Pages\ListAdvertiseRequests::route('/'),
            'edit'   => Pages\EditAdvertiseRequest::route('/{record}/edit'),
        ];
    }
}
