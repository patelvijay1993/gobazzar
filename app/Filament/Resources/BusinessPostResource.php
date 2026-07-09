<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessPostResource\Pages;
use App\Models\Business;
use App\Models\BusinessPost;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BusinessPostResource extends Resource
{
    protected static ?string $model = BusinessPost::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Classified';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Business Posts';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Post Details')->schema([
                Forms\Components\Select::make('business_id')
                    ->label('Business')
                    ->options(Business::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('Owner')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', 'directory')->whereNull('parent_id')->where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('subcategory_id', null)),
                Forms\Components\Select::make('subcategory_id')
                    ->label('Sub-Category')
                    ->options(fn (Forms\Get $get) => Category::where('parent_id', $get('category_id'))->where('is_active', true)->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable()
                    ->placeholder('â€” Select Sub-Category â€”'),
                Forms\Components\Select::make('status')
                    ->options(['pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected', 'expired' => 'Expired', 'flagged' => 'Flagged'])
                    ->default('active')
                    ->required(),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull()
                    ->toolbarButtons(['bold','italic','underline','strike','bulletList','orderedList','h2','h3','link','blockquote','undo','redo']),
            ])->columns(2),

            Forms\Components\Section::make('Custom Fields')->schema([
                Forms\Components\Placeholder::make('custom_fields_view')
                    ->label('Category Custom Fields (stored data)')
                    ->content(function ($record) {
                        if (!$record || empty($record->custom_fields)) {
                            return new \Illuminate\Support\HtmlString('<span style="color:#9ca3af;font-size:13px">No custom fields stored for this post.</span>');
                        }
                        $html = '<table style="width:100%;font-size:13px;border-collapse:collapse">';
                        foreach ($record->custom_fields as $key => $value) {
                            $html .= '<tr><td style="padding:4px 8px;font-weight:600;color:#374151;border-bottom:1px solid #f3f4f6;width:40%">'.e($key).'</td>'
                                   . '<td style="padding:4px 8px;color:#6b7280;border-bottom:1px solid #f3f4f6">'.e($value).'</td></tr>';
                        }
                        $html .= '</table>';
                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Pricing')->schema([
                Forms\Components\TextInput::make('price')
                    ->placeholder('e.g. 25.00 or 25')
                    ->nullable(),
                Forms\Components\TextInput::make('price_unit')
                    ->placeholder('per item, /kg, /hrâ€¦')
                    ->nullable(),
                Forms\Components\Toggle::make('is_featured')->label('Featured'),
                Forms\Components\DateTimePicker::make('expires_at')->label('Expires At')->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Images')->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Main Image')
                    ->image()
                    ->disk(config('filesystems.default'))
                    ->directory('business-posts')
                    ->nullable(),
                Forms\Components\FileUpload::make('images')
                    ->label('Additional Images')
                    ->image()
                    ->disk(config('filesystems.default'))
                    ->directory('business-posts')
                    ->multiple()
                    ->nullable(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Photo')->disk(config('filesystems.default')),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(30)->sortable(),
                Tables\Columns\TextColumn::make('business.name')->label('Business')->searchable()->limit(25),
                Tables\Columns\TextColumn::make('user.name')->label('Owner')->searchable()->limit(20),
                Tables\Columns\TextColumn::make('price')->money('CAD')->sortable()->placeholder('â€”'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'active', 'gray' => 'inactive', 'danger' => 'expired']),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\TextColumn::make('views')->sortable(),
                Tables\Columns\TextColumn::make('expires_at')->date()->sortable()->placeholder('Never'),
                Tables\Columns\TextColumn::make('created_at')->date()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected', 'expired' => 'Expired', 'flagged' => 'Flagged']),
            ])
            ->actions([
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
            'index'  => Pages\ListBusinessPosts::route('/'),
            'create' => Pages\CreateBusinessPost::route('/create'),
            'edit'   => Pages\EditBusinessPost::route('/{record}/edit'),
        ];
    }
}


