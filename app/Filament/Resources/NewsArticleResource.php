<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsArticleResource\Pages;
use App\Models\NewsArticle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsArticleResource extends Resource
{
    protected static ?string $model = NewsArticle::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Classified';
    protected static ?string $navigationLabel = 'News';
    protected static ?string $modelLabel = 'News Article';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Article')->schema([
                Forms\Components\TextInput::make('title')->required()->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->columnSpanFull(),
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options(['published' => 'Published', 'hidden' => 'Hidden'])
                    ->default('published')
                    ->required(),
                Forms\Components\Toggle::make('is_featured'),
                Forms\Components\TextInput::make('source_name')->label('Source'),
                Forms\Components\TextInput::make('link')->label('Original Link')->url()->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')->label('Image'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(45)->sortable(),
                Tables\Columns\TextColumn::make('source_name')->label('Source')->badge()->color('info'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) { 'published' => 'success', default => 'gray' }),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\TextColumn::make('views')->sortable(),
                Tables\Columns\TextColumn::make('pub_date')->dateTime()->sortable(),
            ])
            ->defaultSort('pub_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['published' => 'Published', 'hidden' => 'Hidden']),
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
            'index' => Pages\ListNewsArticles::route('/'),
            'edit'  => Pages\EditNewsArticle::route('/{record}/edit'),
        ];
    }
}
