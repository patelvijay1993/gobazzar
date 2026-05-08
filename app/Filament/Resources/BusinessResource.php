<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\Pages;
use App\Models\Business;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Business Info')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->columnSpanFull(),
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', 'directory')->where('is_active', true)->pluck('name', 'id'))
                    ->searchable(),
                Forms\Components\Select::make('status')
                    ->options(['pending' => 'Pending', 'active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('pending')->required(),
                Forms\Components\Textarea::make('description')->rows(4)->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Location & Contact')->schema([
                Forms\Components\TextInput::make('address'),
                Forms\Components\TextInput::make('city'),
                Forms\Components\TextInput::make('province'),
                Forms\Components\TextInput::make('phone'),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\TextInput::make('website')->url(),
                Forms\Components\TextInput::make('hours')->placeholder('Mon-Fri 9am-6pm')->columnSpanFull(),
            ])->columns(3),

            Forms\Components\Section::make('Media & Tags')->schema([
                Forms\Components\FileUpload::make('image')->image()->directory('businesses')->columnSpanFull(),
                Forms\Components\FileUpload::make('logo')->image()->directory('businesses/logos'),
                Forms\Components\TagsInput::make('tags'),
            ])->columns(2),

            Forms\Components\Section::make('Ratings & Settings')->schema([
                Forms\Components\TextInput::make('rating')->numeric()->step(0.1)->minValue(0)->maxValue(5)->default(0),
                Forms\Components\TextInput::make('review_count')->numeric()->default(0),
                Forms\Components\Toggle::make('is_verified'),
                Forms\Components\Toggle::make('is_featured'),
            ])->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->circular(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->badge()->color('info'),
                Tables\Columns\TextColumn::make('city'),
                Tables\Columns\TextColumn::make('rating')->sortable()
                    ->formatStateUsing(fn ($state) => '⭐ '.$state),
                Tables\Columns\TextColumn::make('review_count')->label('Reviews')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning' => 'pending', 'success' => 'active', 'gray' => 'inactive']),
                Tables\Columns\IconColumn::make('is_verified')->boolean(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'active' => 'Active', 'inactive' => 'Inactive']),
                Tables\Filters\SelectFilter::make('category')->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
                Tables\Filters\TernaryFilter::make('is_verified')->label('Verified'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn (Business $r) => $r->status === 'pending')
                    ->action(fn (Business $r) => $r->update(['status' => 'active'])),
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
            'index'  => Pages\ListBusinesses::route('/'),
            'create' => Pages\CreateBusiness::route('/create'),
            'edit'   => Pages\EditBusiness::route('/{record}/edit'),
        ];
    }
}
