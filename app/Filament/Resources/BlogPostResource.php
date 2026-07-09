<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Classified';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Post Details')->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Author')
                    ->options(User::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->placeholder('â€” Select Author â€”')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('category')->placeholder('Technology, Culture, Foodâ€¦'),
                Forms\Components\Select::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published'])
                    ->default('draft')
                    ->required(),
                Forms\Components\Textarea::make('excerpt')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('body')
                    ->required()
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Media & Meta')->schema([
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->disk(config('filesystems.default'))
                    ->directory('blog')
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('tags'),
                Forms\Components\Toggle::make('is_featured'),
                Forms\Components\DateTimePicker::make('published_at'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Cover'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(35)->sortable(),
                Tables\Columns\TextColumn::make('category')->badge()->color('info'),
                Tables\Columns\TextColumn::make('author.name')->label('Author'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['gray' => 'draft', 'success' => 'published']),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\TextColumn::make('views')->sortable(),
                Tables\Columns\TextColumn::make('published_at')->date()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published']),
            ])
            ->actions([
                Tables\Actions\Action::make('publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (BlogPost $r) => $r->status === 'draft')
                    ->action(fn (BlogPost $r) => $r->update([
                        'status'       => 'published',
                        'published_at' => now(),
                    ])),
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
            'index'  => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit'   => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}


