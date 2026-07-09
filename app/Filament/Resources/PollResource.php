<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PollResource\Pages;
use App\Models\Location;
use App\Models\Poll;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PollResource extends Resource
{
    protected static ?string $model = Poll::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationLabel = 'GoBazaar Poll';
    protected static ?string $modelLabel = 'Poll';
    protected static ?string $pluralModelLabel = 'GoBazaar Polls';
    protected static ?string $navigationGroup = 'Classified';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Poll')->schema([
                Forms\Components\TextInput::make('question')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->placeholder('Do you expect the job market to improve in 2026?'),

                Forms\Components\Repeater::make('options')
                    ->relationship()
                    ->label('Answer Options')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->required()
                            ->placeholder('Yes / No / Maybe')
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('votes')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),
                    ])
                    ->columns(4)
                    ->defaultItems(2)
                    ->minItems(2)
                    ->maxItems(6)
                    ->reorderable('sort_order')
                    ->orderColumn('sort_order')
                    ->addActionLabel('+ Add Option')
                    ->columnSpanFull(),
            ])->columns(1),

            Forms\Components\Section::make('Settings')->schema([
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->helperText('Only active polls show on the homepage.'),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Expires At (optional)')
                    ->helperText('Leave empty to keep it running forever.'),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower number shows first.'),
            ])->columns(3),

            Forms\Components\Section::make('Location Scope')->schema([
                Forms\Components\Select::make('scope')
                    ->options([
                        'canada'   => 'All Canada',
                        'province' => 'Province',
                        'city'     => 'City',
                    ])
                    ->default('canada')
                    ->required()
                    ->live()
                    ->helperText('Where should this poll appear?'),

                Forms\Components\Select::make('province')
                    ->options(Location::activeProvinces()->mapWithKeys(fn ($p) => [$p => $p]))
                    ->searchable()
                    ->live()
                    ->visible(fn (Get $get) => in_array($get('scope'), ['province', 'city']))
                    ->required(fn (Get $get) => in_array($get('scope'), ['province', 'city'])),

                Forms\Components\Select::make('city')
                    ->options(fn (Get $get) => $get('province')
                        ? Location::activeCities($get('province'))->mapWithKeys(fn ($c) => [$c => $c])
                        : [])
                    ->searchable()
                    ->visible(fn (Get $get) => $get('scope') === 'city')
                    ->required(fn (Get $get) => $get('scope') === 'city'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')->searchable()->limit(50)->wrap(),
                Tables\Columns\TextColumn::make('options_count')
                    ->counts('options')
                    ->label('Options')
                    ->badge()->color('info'),
                Tables\Columns\TextColumn::make('total_votes')
                    ->label('Total Votes')
                    ->getStateUsing(fn (Poll $r) => $r->options->sum('votes'))
                    ->badge()->color('success'),
                Tables\Columns\TextColumn::make('scope')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'canada'   => 'success',
                        'province' => 'info',
                        'city'     => 'warning',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state, Poll $r) => match($state) {
                        'canada'   => 'All Canada',
                        'province' => $r->province,
                        'city'     => $r->city.', '.$r->province,
                        default    => $state,
                    }),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('expires_at')->dateTime()->placeholder('Never')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->date()->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                Tables\Actions\Action::make('results')
                    ->icon('heroicon-o-chart-bar')
                    ->color('gray')
                    ->modalHeading(fn (Poll $r) => $r->question)
                    ->modalContent(fn (Poll $r) => view('filament.poll-results', ['poll' => $r]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                Tables\Actions\Action::make('reset_votes')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Reset all vote counts to zero for this poll?')
                    ->action(function (Poll $r) {
                        $r->options()->update(['votes' => 0]);
                        $r->votes()->delete();
                    }),
                Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListPolls::route('/'),
            'create' => Pages\CreatePoll::route('/create'),
            'edit'   => Pages\EditPoll::route('/{record}/edit'),
        ];
    }
}

