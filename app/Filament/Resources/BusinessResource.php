<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\Pages;
use App\Models\Business;
use App\Models\Category;
use App\Models\Location;
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
    protected static ?string $navigationGroup = 'Directory';
    protected static ?int $navigationSort = 2;

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
                    ->options(Category::where('type', 'directory')->whereNull('parent_id')->where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('subcategory_id', null)),
                Forms\Components\Select::make('subcategory_id')
                    ->label('Sub-Category')
                    ->options(fn (Forms\Get $get) => Category::where('parent_id', $get('category_id'))->where('is_active', true)->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->placeholder('— Select Sub-Category —'),
                Forms\Components\Select::make('status')
                    ->options(['pending' => 'Pending', 'active' => 'Active', 'inactive' => 'Inactive', 'flagged' => 'Flagged'])
                    ->default('pending')->required(),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull()
                    ->toolbarButtons(['attachFiles','bold','italic','underline','strike','bulletList','orderedList','h2','h3','link','blockquote','undo','redo']),
            ])->columns(2),

            Forms\Components\Section::make('Location & Contact')->schema([
                Forms\Components\TextInput::make('address'),
                Forms\Components\Select::make('province')
                    ->options(fn () => Location::distinct()->orderBy('province')->pluck('province', 'province')->filter()->toArray())
                    ->searchable()
                    ->live()
                    ->placeholder('— Select Province —')
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('city', null)),
                Forms\Components\Select::make('city')
                    ->options(fn (Forms\Get $get) => Location::where('province', $get('province'))->orderBy('city')->pluck('city', 'city')->filter()->toArray())
                    ->searchable()
                    ->placeholder('— Select City —')
                    ->live(),
                Forms\Components\TextInput::make('phone'),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\TextInput::make('website')->url(),
                Forms\Components\Grid::make(7)
                    ->schema(collect(['monday'=>'Mon','tuesday'=>'Tue','wednesday'=>'Wed','thursday'=>'Thu','friday'=>'Fri','saturday'=>'Sat','sunday'=>'Sun'])
                        ->map(fn ($label, $key) => Forms\Components\Group::make([
                            Forms\Components\Placeholder::make("_lbl_{$key}")->label('')->content($label),
                            Forms\Components\TextInput::make("biz_hours_{$key}_open")
                                ->label('Open')->placeholder('09:00')->maxLength(5)->extraInputAttributes(['style'=>'font-size:12px;padding:4px 6px']),
                            Forms\Components\TextInput::make("biz_hours_{$key}_close")
                                ->label('Close')->placeholder('18:00')->maxLength(5)->extraInputAttributes(['style'=>'font-size:12px;padding:4px 6px']),
                            Forms\Components\Toggle::make("biz_hours_{$key}_closed")
                                ->label('Closed')->inline(false),
                        ]))->values()->toArray()
                    )
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('map_url')->label('Map Embed URL')->url()->columnSpanFull()->placeholder('https://maps.google.com/embed?...'),
            ])->columns(3),

            Forms\Components\Section::make('Media & Tags')->schema([
                Forms\Components\FileUpload::make('image')->image()->disk('s3')->directory('businesses')->columnSpanFull(),
                Forms\Components\FileUpload::make('logo')->image()->disk('s3')->directory('businesses/logos'),
                Forms\Components\FileUpload::make('images')
                    ->label('Gallery Images')
                    ->image()
                    ->disk('s3')
                    ->directory('businesses')
                    ->multiple()
                    ->reorderable()
                    ->helperText('Additional photos for the business gallery.'),
                Forms\Components\TagsInput::make('tags')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Social Links')->schema([
                Forms\Components\TextInput::make('social.facebook')->label('Facebook')->url()->placeholder('https://facebook.com/...'),
                Forms\Components\TextInput::make('social.instagram')->label('Instagram')->url()->placeholder('https://instagram.com/...'),
                Forms\Components\TextInput::make('social.twitter')->label('Twitter / X')->url()->placeholder('https://x.com/...'),
                Forms\Components\TextInput::make('social.youtube')->label('YouTube')->url()->placeholder('https://youtube.com/...'),
                Forms\Components\TextInput::make('social.linkedin')->label('LinkedIn')->url()->placeholder('https://linkedin.com/...'),
                Forms\Components\TextInput::make('social.whatsapp')->label('WhatsApp')->placeholder('+1 416 555 0000'),
            ])->columns(3),

            Forms\Components\Section::make('Ratings & Settings')->schema([
                Forms\Components\TextInput::make('rating')->numeric()->step(0.1)->minValue(0)->maxValue(5)->default(0)
                    ->helperText('Auto-calculated from reviews. Edit only to override.'),
                Forms\Components\TextInput::make('review_count')->numeric()->default(0)
                    ->helperText('Auto-calculated. Edit only to override.'),
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
                    ->options(['pending' => 'Pending', 'active' => 'Active', 'inactive' => 'Inactive', 'flagged' => 'Flagged']),
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

