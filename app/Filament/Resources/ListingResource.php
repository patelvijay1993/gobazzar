<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListingResource\Pages;
use App\Models\Category;
use App\Models\Listing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Listing Details')->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->columnSpanFull(),
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(['pending'=>'Pending','active'=>'Active','rejected'=>'Rejected','expired'=>'Expired'])
                    ->default('pending')
                    ->required(),
                Forms\Components\Textarea::make('description')->columnSpanFull()->rows(4),
            ])->columns(2),

            Forms\Components\Section::make('Pricing & Location')->schema([
                Forms\Components\TextInput::make('price')->placeholder('$1,200'),
                Forms\Components\TextInput::make('price_unit')->placeholder('/mo'),
                Forms\Components\TextInput::make('location')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Media & Tags')->schema([
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('listings')
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('tags')->columnSpanFull(),
                Forms\Components\CheckboxList::make('badges')
                    ->options(['feat'=>'Featured','ver'=>'Verified','new'=>'New','hot'=>'Hot'])
                    ->columns(4)
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Contact Info')->schema([
                Forms\Components\TextInput::make('contact_name'),
                Forms\Components\TextInput::make('contact_email')->email(),
                Forms\Components\TextInput::make('contact_phone'),
            ])->columns(3),

            Forms\Components\Section::make('Settings')->schema([
                Forms\Components\Toggle::make('is_featured'),
                Forms\Components\Toggle::make('is_verified'),
                Forms\Components\DateTimePicker::make('expires_at'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->circular(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->sortable(),
                Tables\Columns\TextColumn::make('category.name')->badge()->color('info'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\TextColumn::make('location')->limit(25),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'danger'  => 'rejected',
                        'gray'    => 'expired',
                    ]),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\IconColumn::make('is_verified')->boolean(),
                Tables\Columns\TextColumn::make('views')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->date()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending'=>'Pending','active'=>'Active','rejected'=>'Rejected','expired'=>'Expired']),
                Tables\Filters\SelectFilter::make('category')->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Listing $record) => $record->status === 'pending')
                    ->action(fn (Listing $record) => $record->update(['status' => 'active'])),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Listing $record) => $record->status === 'pending')
                    ->action(fn (Listing $record) => $record->update(['status' => 'rejected'])),
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
            'index'  => Pages\ListListings::route('/'),
            'create' => Pages\CreateListing::route('/create'),
            'edit'   => Pages\EditListing::route('/{record}/edit'),
        ];
    }
}
