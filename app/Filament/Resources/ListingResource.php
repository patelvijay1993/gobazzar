<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListingResource\Pages;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Classified';
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
                    ->options(
                        Category::where('is_active', true)
                            ->where('type', 'classifieds')
                            ->whereNull('parent_id')
                            ->orderBy('sort_order')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('custom_fields', [])),
                Forms\Components\Select::make('status')
                    ->options(['pending'=>'Pending','active'=>'Active','rejected'=>'Rejected','expired'=>'Expired','flagged'=>'Flagged'])
                    ->default('pending')
                    ->required(),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'strike',
                        'bulletList', 'orderedList',
                        'h2', 'h3',
                        'link', 'blockquote',
                        'undo', 'redo',
                    ]),
            ])->columns(2),

            Forms\Components\Section::make('Additional Details')
                ->schema(function (Forms\Get $get) {
                    $catId = $get('category_id');
                    if (!$catId) return [
                        Forms\Components\Placeholder::make('_no_cat')
                            ->label('')
                            ->content('Select a category above to see additional fields.')
                            ->columnSpanFull(),
                    ];
                    $fields = \App\Models\CategoryField::where('category_id', $catId)
                        ->orderBy('sort_order')
                        ->get();
                    if ($fields->isEmpty()) return [
                        Forms\Components\Placeholder::make('_no_fields')
                            ->label('')
                            ->content('No custom fields defined for this category.')
                            ->columnSpanFull(),
                    ];
                    return $fields->map(function ($f) {
                        $name = 'custom_fields.' . $f->key;
                        return match ($f->type) {
                            'select' => Forms\Components\Select::make($name)
                                ->label($f->label)
                                ->options(collect(json_decode($f->options ?? '[]', true))->mapWithKeys(fn ($v) => [$v => $v]))
                                ->placeholder($f->placeholder)
                                ->required((bool) $f->is_required),
                            'textarea' => Forms\Components\Textarea::make($name)
                                ->label($f->label)
                                ->placeholder($f->placeholder)
                                ->required((bool) $f->is_required),
                            'number' => Forms\Components\TextInput::make($name)
                                ->label($f->label)
                                ->numeric()
                                ->placeholder($f->placeholder)
                                ->required((bool) $f->is_required),
                            'checkbox' => Forms\Components\Toggle::make($name)
                                ->label($f->label)
                                ->required((bool) $f->is_required),
                            default => Forms\Components\TextInput::make($name)
                                ->label($f->label)
                                ->placeholder($f->placeholder)
                                ->required((bool) $f->is_required),
                        };
                    })->toArray();
                })
                ->columns(2)
                ->live()
                ->visible(fn (Forms\Get $get) => (bool) $get('category_id')),

            Forms\Components\Section::make('Pricing & Location')->schema([
                Forms\Components\TextInput::make('price')->placeholder('$1,200'),
                Forms\Components\Select::make('price_unit')
                    ->options(['' => 'One-time', '/mo' => '/month', '/wk' => '/week', '/hr' => '/hour', '/yr' => '/year'])
                    ->placeholder('One-time'),
                Forms\Components\TextInput::make('location')->columnSpanFull(),
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
            ])->columns(2),

            Forms\Components\Section::make('Media & Tags')->schema([
                // Current photos preview — remove via fetch (no nested form)
                Forms\Components\Placeholder::make('current_photos')
                    ->label('Current Photos')
                    ->content(function ($record) {
                        if (!$record) return new \Illuminate\Support\HtmlString('—');
                        $all = array_values(array_filter(array_merge(
                            $record->image ? [$record->image] : [],
                            (array) ($record->images ?? [])
                        )));
                        if (empty($all)) return new \Illuminate\Support\HtmlString(
                            '<span style="color:#9ca3af;font-size:13px">No photos yet.</span>'
                        );
                        $removeUrl = route('admin.listing.remove-image', $record->id);
                        $token = csrf_token();
                        $html = '<div id="current-photos-wrap" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:4px">';
                        foreach ($all as $img) {
                            $url = str_starts_with($img, 'http') ? $img : Storage::disk(config('filesystems.default'))->url($img);
                            $enc = base64_encode($img);
                            $html .= '
                            <div style="position:relative;display:inline-block" id="wrap-'.$enc.'">
                                <img src="'.$url.'" style="width:100px;height:76px;object-fit:cover;border-radius:8px;border:1.5px solid #e5e7eb;display:block">
                                <button type="button"
                                    onclick="removeListingPhoto(\''.addslashes($removeUrl).'\',\''.$enc.'\',\''.$token.'\')"
                                    style="position:absolute;top:-7px;right:-7px;width:22px;height:22px;border-radius:50%;background:#ef4444;border:2px solid #fff;color:#fff;font-size:13px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;font-weight:700">&times;</button>
                            </div>';
                        }
                        $html .= '</div>
                        <p style="font-size:11px;color:#9ca3af;margin-top:6px">Click × to remove. Upload new photos below to add/replace.</p>
                        <script>
                        function removeListingPhoto(url, enc, token) {
                            if (!confirm("Remove this photo?")) return;
                            fetch(url, {
                                method: "DELETE",
                                headers: {"Content-Type":"application/json","X-CSRF-TOKEN":token},
                                body: JSON.stringify({img: enc})
                            }).then(r => {
                                if (r.ok || r.redirected) {
                                    var el = document.getElementById("wrap-" + enc);
                                    if (el) el.remove();
                                } else { alert("Failed to remove photo."); }
                            }).catch(() => alert("Error removing photo."));
                        }
                        </script>';
                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->columnSpanFull(),

                // Upload new photos — stored properly via mutateFormDataBeforeSave
                Forms\Components\FileUpload::make('new_photos')
                    ->label('Upload New Photos (up to 5)')
                    ->image()
                    ->multiple()
                    ->maxFiles(5)
                    ->reorderable()
                    ->disk(config('filesystems.default'))
                    ->directory('listings')
                    ->imagePreviewHeight('100')
                    ->panelLayout('grid')
                    ->helperText('Leave empty to keep current photos.')
                    ->columnSpanFull()
                    ->dehydrated(true),
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
                Tables\Columns\ImageColumn::make('image')->circular()->getStateUsing(fn ($record) => $record->image_url),
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
                    ->options(['pending'=>'Pending','active'=>'Active','rejected'=>'Rejected','expired'=>'Expired','flagged'=>'Flagged']),
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
                Tables\Actions\Action::make('analytics')
                    ->label('Analytics')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->url(fn (Listing $record) => Pages\ViewListingAnalytics::getUrl(['record' => $record])),
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
            'index'     => Pages\ListListings::route('/'),
            'create'    => Pages\CreateListing::route('/create'),
            'edit'      => Pages\EditListing::route('/{record}/edit'),
            'analytics' => Pages\ViewListingAnalytics::route('/{record}/analytics'),
        ];
    }
}


