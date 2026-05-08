<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatrimonialResource\Pages;
use App\Models\Matrimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MatrimonialResource extends Resource
{
    protected static ?string $model = Matrimonial::class;
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 7;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Personal Details')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state.'-'.rand(100,999))))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->columnSpanFull(),
                Forms\Components\Select::make('profile_for')
                    ->options(['self' => 'Self', 'son' => 'Son', 'daughter' => 'Daughter', 'brother' => 'Brother', 'sister' => 'Sister', 'friend' => 'Friend'])
                    ->required(),
                Forms\Components\Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female'])
                    ->required(),
                Forms\Components\TextInput::make('age')->numeric()->required()->minValue(18)->maxValue(80),
                Forms\Components\TextInput::make('height')->placeholder("5'7\""),
                Forms\Components\Select::make('marital_status')
                    ->options(['never_married' => 'Never Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed'])
                    ->default('never_married'),
                Forms\Components\TextInput::make('religion')->placeholder('Hindu, Muslim, Sikh…'),
                Forms\Components\TextInput::make('caste')->placeholder('Optional'),
                Forms\Components\TextInput::make('mother_tongue')->placeholder('Hindi, Gujarati, Punjabi…'),
                Forms\Components\Select::make('diet')
                    ->options(['veg' => 'Vegetarian', 'non-veg' => 'Non-Vegetarian', 'eggetarian' => 'Eggetarian']),
            ])->columns(3),

            Forms\Components\Section::make('Professional')->schema([
                Forms\Components\TextInput::make('education')->placeholder('B.Tech, MBA…'),
                Forms\Components\TextInput::make('occupation')->placeholder('Software Engineer…'),
                Forms\Components\TextInput::make('income')->placeholder('$60K–$80K/yr'),
            ])->columns(3),

            Forms\Components\Section::make('Location')->schema([
                Forms\Components\TextInput::make('city')->required(),
                Forms\Components\TextInput::make('province'),
                Forms\Components\TextInput::make('country')->default('Canada'),
            ])->columns(3),

            Forms\Components\Section::make('About')->schema([
                Forms\Components\Textarea::make('about')->rows(4)->columnSpanFull(),
                Forms\Components\Textarea::make('partner_preference')->rows(3)->label('Partner Preference')->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Photos & Contact')->schema([
                Forms\Components\FileUpload::make('photo')->image()->directory('matrimonials')->label('Profile Photo'),
                Forms\Components\TextInput::make('contact_name'),
                Forms\Components\TextInput::make('contact_phone'),
                Forms\Components\TextInput::make('contact_email')->email(),
                Forms\Components\Toggle::make('hide_contact')->label('Hide contact from public'),
                Forms\Components\Select::make('status')
                    ->options(['pending' => 'Pending', 'active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('pending')->required(),
                Forms\Components\Toggle::make('is_featured'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')->circular()->label('Photo'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('gender')->badge()
                    ->color(fn ($state) => $state === 'male' ? 'info' : 'danger'),
                Tables\Columns\TextColumn::make('age')->sortable(),
                Tables\Columns\TextColumn::make('religion'),
                Tables\Columns\TextColumn::make('city'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning' => 'pending', 'success' => 'active', 'gray' => 'inactive']),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\TextColumn::make('views')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->date()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'active' => 'Active', 'inactive' => 'Inactive']),
                Tables\Filters\SelectFilter::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female']),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn (Matrimonial $r) => $r->status === 'pending')
                    ->action(fn (Matrimonial $r) => $r->update(['status' => 'active'])),
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
            'index'  => Pages\ListMatrimonials::route('/'),
            'create' => Pages\CreateMatrimonial::route('/create'),
            'edit'   => Pages\EditMatrimonial::route('/{record}/edit'),
        ];
    }
}
