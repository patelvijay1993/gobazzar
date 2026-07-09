<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Classified';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('icon')->placeholder('🏠'),
                Forms\Components\Select::make('type')
                    ->options([
                        'classifieds' => 'Classifieds',
                        'jobs'        => 'Jobs',
                        'events'      => 'Events',
                        'directory'   => 'Business Directory',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Category (leave empty for top-level)')
                    ->options(fn (Forms\Get $get) => Category::query()
                        ->whereNull('parent_id')
                        ->when($get('type'), fn ($q, $type) => $q->where('type', $type))
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('— None (this is a top-level category) —')
                    ->helperText('Pick a parent to make this a sub-category.'),
                Forms\Components\Toggle::make('is_active')->default(true),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(2),

            Forms\Components\Section::make('Custom Post Fields')
                ->description('Extra fields shown on the post form when a user posts under this category. Sub-categories also inherit their parent\'s fields. (Values are currently captured on Business posts.)')
                ->schema([
                    Forms\Components\Repeater::make('fields')
                        ->relationship()
                        ->label('')
                        ->schema([
                            Forms\Components\TextInput::make('label')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('key', Str::slug($state, '_')))
                                ->placeholder('Cuisine Type')
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('key')
                                ->required()
                                ->helperText('auto-filled')
                                ->columnSpan(2),
                            Forms\Components\Select::make('type')
                                ->options([
                                    'text'     => 'Text',
                                    'number'   => 'Number',
                                    'textarea' => 'Textarea',
                                    'select'   => 'Dropdown',
                                    'checkbox' => 'Checkbox (Yes/No)',
                                ])
                                ->default('text')
                                ->live()
                                ->required()
                                ->columnSpan(2),
                            Forms\Components\TagsInput::make('options')
                                ->label('Dropdown Options')
                                ->placeholder('Add option + Enter')
                                ->visible(fn (Forms\Get $get) => $get('type') === 'select')
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('placeholder')
                                ->visible(fn (Forms\Get $get) => in_array($get('type'), ['text', 'number', 'textarea']))
                                ->columnSpan(2),
                            Forms\Components\Toggle::make('is_required')
                                ->label('Required')
                                ->inline(false)
                                ->columnSpan(1),
                        ])
                        ->columns(6)
                        ->orderColumn('sort_order')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'New field')
                        ->addActionLabel('+ Add Custom Field')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icon')
                    ->label('')
                    ->width('40px'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, Category $r) => ($r->parent_id ? '↳ ' : '') . $state),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'classifieds' => 'info',
                        'jobs'        => 'success',
                        'events'      => 'warning',
                        'directory'   => 'danger',
                        default       => 'gray',
                    }),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sort')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'classifieds' => '🏷️ Classifieds',
                        'jobs'        => '💼 Jobs',
                        'events'      => '🎉 Events',
                        'directory'   => '🏢 Business Directory',
                    ])
                    ->placeholder('All Types'),
            ])
            ->actions([
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
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
