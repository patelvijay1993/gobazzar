<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Models\Category;
use App\Models\Job;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Job Details')->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->columnSpanFull(),
                Forms\Components\TextInput::make('company')->required(),
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', 'jobs')->where('is_active', true)->pluck('name', 'id'))
                    ->searchable(),
                Forms\Components\Select::make('status')
                    ->options(['draft' => 'Draft', 'active' => 'Active', 'closed' => 'Closed'])
                    ->default('draft')->required(),
                Forms\Components\Select::make('job_type')
                    ->options(['full-time' => 'Full Time', 'part-time' => 'Part Time', 'contract' => 'Contract', 'freelance' => 'Freelance', 'internship' => 'Internship'])
                    ->default('full-time'),
                Forms\Components\Select::make('work_mode')
                    ->options(['onsite' => 'On-site', 'remote' => 'Remote', 'hybrid' => 'Hybrid'])
                    ->default('onsite'),
                Forms\Components\TextInput::make('salary')->placeholder('$60K-$80K/yr'),
                Forms\Components\TextInput::make('experience')->placeholder('3+ years'),
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
                Forms\Components\RichEditor::make('requirements')->columnSpanFull(),
            ])->columns(3),

            Forms\Components\Section::make('Location')->schema([
                Forms\Components\TextInput::make('location'),
                Forms\Components\TextInput::make('city'),
                Forms\Components\TextInput::make('province'),
            ])->columns(3),

            Forms\Components\Section::make('Apply & Media')->schema([
                Forms\Components\TextInput::make('apply_email')->email(),
                Forms\Components\TextInput::make('apply_url')->url(),
                Forms\Components\FileUpload::make('company_logo')->image()->directory('jobs'),
                Forms\Components\TagsInput::make('tags'),
                Forms\Components\Toggle::make('is_featured'),
                Forms\Components\DateTimePicker::make('expires_at'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('company_logo')->circular()->label('Logo'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(30)->sortable(),
                Tables\Columns\TextColumn::make('company')->searchable(),
                Tables\Columns\TextColumn::make('category.name')->badge()->color('success'),
                Tables\Columns\TextColumn::make('job_type')->badge()
                    ->color(fn ($state) => match($state) {
                        'full-time'  => 'success',
                        'part-time'  => 'info',
                        'contract'   => 'warning',
                        'freelance'  => 'danger',
                        'internship' => 'gray',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('work_mode')->badge()->color('info'),
                Tables\Columns\TextColumn::make('city'),
                Tables\Columns\TextColumn::make('salary'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['gray' => 'draft', 'success' => 'active', 'danger' => 'closed']),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\TextColumn::make('views')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'active' => 'Active', 'closed' => 'Closed']),
                Tables\Filters\SelectFilter::make('job_type')
                    ->options(['full-time' => 'Full Time', 'part-time' => 'Part Time', 'contract' => 'Contract']),
                Tables\Filters\SelectFilter::make('work_mode')
                    ->options(['onsite' => 'On-site', 'remote' => 'Remote', 'hybrid' => 'Hybrid']),
                Tables\Filters\SelectFilter::make('category')->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('publish')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn (Job $r) => $r->status === 'draft')
                    ->action(fn (Job $r) => $r->update(['status' => 'active'])),
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
            'index'  => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'edit'   => Pages\EditJob::route('/{record}/edit'),
        ];
    }
}
