<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Cities & Provinces';
    protected static ?string $modelLabel = 'Location';
    protected static ?string $pluralModelLabel = 'Cities & Provinces';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('province')
                    ->options([
                        'Ontario'                       => 'Ontario',
                        'British Columbia'              => 'British Columbia',
                        'Alberta'                       => 'Alberta',
                        'Quebec'                        => 'Quebec',
                        'Manitoba'                      => 'Manitoba',
                        'Saskatchewan'                  => 'Saskatchewan',
                        'Nova Scotia'                   => 'Nova Scotia',
                        'New Brunswick'                 => 'New Brunswick',
                        'Newfoundland and Labrador'     => 'Newfoundland and Labrador',
                        'Prince Edward Island'          => 'Prince Edward Island',
                        'Northwest Territories'         => 'Northwest Territories',
                        'Nunavut'                       => 'Nunavut',
                        'Yukon'                         => 'Yukon',
                    ])
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('city')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('e.g. Brampton'),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first in dropdowns'),

                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->helperText('Inactive cities are hidden from all dropdowns'),

                Forms\Components\FileUpload::make('city_image')
                    ->label('City Image')
                    ->image()
                    ->directory('locations/cities')
                    ->imagePreviewHeight('80')
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('province')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\ImageColumn::make('city_image')
                    ->label('City Img')
                    ->circular()
                    ->defaultImageUrl(null)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('province')
            ->filters([
                Tables\Filters\SelectFilter::make('province')
                    ->options([
                        'Ontario'                   => 'Ontario',
                        'British Columbia'          => 'British Columbia',
                        'Alberta'                   => 'Alberta',
                        'Quebec'                    => 'Quebec',
                        'Manitoba'                  => 'Manitoba',
                        'Saskatchewan'              => 'Saskatchewan',
                        'Nova Scotia'               => 'Nova Scotia',
                        'New Brunswick'             => 'New Brunswick',
                        'Newfoundland and Labrador' => 'Newfoundland and Labrador',
                        'Prince Edward Island'      => 'Prince Edward Island',
                        'Northwest Territories'     => 'Northwest Territories',
                        'Nunavut'                   => 'Nunavut',
                        'Yukon'                     => 'Yukon',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Set Active')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Set Inactive')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit'   => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
