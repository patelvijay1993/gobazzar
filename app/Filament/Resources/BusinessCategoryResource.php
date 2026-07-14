<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessCategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BusinessCategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Directory';
    protected static ?string $navigationLabel = 'Categories';
    protected static ?string $modelLabel = 'Business Category';
    protected static ?string $pluralModelLabel = 'Business Categories';
    protected static ?string $slug = 'business-categories';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'directory');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Category Details')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', 'biz-' . Str::slug($state))),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(table: 'categories', column: 'slug', ignoreRecord: true)
                    ->helperText('Auto-generated from name. Used in URLs.'),

                Forms\Components\TextInput::make('icon')
                    ->placeholder('🏢')
                    ->helperText('Paste an emoji for the category icon'),

                Forms\Components\Select::make('parent_id')
                    ->label('Parent Category')
                    ->placeholder('— None (top-level category) —')
                    ->options(fn () => Category::where('type', 'directory')
                        ->whereNull('parent_id')
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->helperText('Select a parent to make this a subcategory'),

                Forms\Components\Hidden::make('type')->default('directory'),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first'),

                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->helperText('Inactive categories are hidden from the directory'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        $isSubpage = request()->routeIs(static::getRouteBaseName() . '.subcategories');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icon')
                    ->label('Icon')
                    ->alignCenter()
                    ->width(60),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent Category')
                    ->badge()
                    ->color('warning')
                    ->placeholder('—')
                    ->visible($isSubpage),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('businesses_count')
                    ->label('Businesses')
                    ->counts('businesses')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->visible(! $isSubpage),

                Tables\Columns\TextColumn::make('children_count')
                    ->label('Subcategories')
                    ->counts('children')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->visible(! $isSubpage),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Category $record) {
                        if ($record->businesses()->count() > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('Cannot delete category with businesses')
                                ->danger()
                                ->send();
                            $record->skipDeletion = true;
                        }
                    })
                    ->using(function (Category $record) {
                        if (!isset($record->skipDeletion)) {
                            $record->delete();
                        }
                    }),
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
            'index'          => Pages\ListBusinessCategories::route('/'),
            'subcategories'  => Pages\ListBusinessSubcategories::route('/subcategories'),
            'create'         => Pages\CreateBusinessCategory::route('/create'),
            'edit'           => Pages\EditBusinessCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            \Filament\Navigation\NavigationItem::make('Categories')
                ->icon('heroicon-o-building-storefront')
                ->group(static::$navigationGroup)
                ->sort(1)
                ->url(static::getUrl('index'))
                ->isActiveWhen(fn () => request()->routeIs(
                    static::getRouteBaseName() . '.index',
                    static::getRouteBaseName() . '.create',
                    static::getRouteBaseName() . '.edit',
                )),
            \Filament\Navigation\NavigationItem::make('Subcategories')
                ->icon('heroicon-o-bars-3-bottom-left')
                ->group(static::$navigationGroup)
                ->sort(2)
                ->url(static::getUrl('subcategories'))
                ->isActiveWhen(fn () => request()->routeIs(
                    static::getRouteBaseName() . '.subcategories',
                )),
        ];
    }
}
