<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Business;
use App\Models\Category;
use App\Models\Lead;
use App\Services\GooglePlacesService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LeadResource extends Resource
{
    protected static ?string $model          = Lead::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel= 'All Leads';
    protected static ?string $navigationGroup= 'Advertising';
    protected static ?int    $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('category')->maxLength(100),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('city')->maxLength(100),
                    Forms\Components\TextInput::make('province')->maxLength(10),
                ]),
                Forms\Components\TextInput::make('address')->maxLength(255),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('phone')->maxLength(30),
                    Forms\Components\TextInput::make('email')->email()->maxLength(255),
                ]),
                Forms\Components\TextInput::make('website')->url()->maxLength(255),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('rating')->numeric()->maxValue(5),
                    Forms\Components\TextInput::make('review_count')->numeric(),
                ]),
                Forms\Components\Select::make('status')
                    ->options([
                        'new'            => '🆕 New',
                        'contacted'      => '📤 Contacted',
                        'interested'     => '✅ Interested',
                        'not_interested' => '❌ Not Interested',
                        'converted'      => '🎉 Converted',
                    ])->required(),
                Forms\Components\Select::make('contact_method')
                    ->options([
                        'none'      => 'None',
                        'email'     => 'Email',
                        'whatsapp'  => 'WhatsApp',
                        'both'      => 'Both',
                    ]),
                Forms\Components\Textarea::make('notes')->rows(3),
                Forms\Components\TextInput::make('google_maps_url')->url()->maxLength(500),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->sortable()->weight('semibold'),

                Tables\Columns\TextColumn::make('category')
                    ->searchable()->badge()->color('info'),

                Tables\Columns\TextColumn::make('city')
                    ->searchable()->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->formatStateUsing(fn($state) => $state ?: '—')
                    ->color(fn($state) => $state ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->formatStateUsing(fn($state) => $state ?: '—')
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->limit(25),

                Tables\Columns\TextColumn::make('rating')
                    ->formatStateUsing(fn($state) => $state ? "★ $state" : '—')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray'    => 'new',
                        'info'    => 'contacted',
                        'success' => 'interested',
                        'danger'  => 'not_interested',
                        'warning' => 'converted',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'new'            => '🆕 New',
                        'contacted'      => '📤 Contacted',
                        'interested'     => '✅ Interested',
                        'not_interested' => '❌ Not Interested',
                        'converted'      => '🎉 Converted',
                        default          => $state,
                    }),

                Tables\Columns\TextColumn::make('last_contacted_at')
                    ->label('Last Contact')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('Never'),

                Tables\Columns\TextColumn::make('source')
                    ->badge()->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new'            => '🆕 New',
                        'contacted'      => '📤 Contacted',
                        'interested'     => '✅ Interested',
                        'not_interested' => '❌ Not Interested',
                        'converted'      => '🎉 Converted',
                    ]),
                Tables\Filters\SelectFilter::make('city')
                    ->options(fn() => Lead::whereNotNull('city')->distinct()->pluck('city','city')->toArray()),
                Tables\Filters\SelectFilter::make('category')
                    ->options(fn() => Lead::whereNotNull('category')->distinct()->pluck('category','category')->toArray()),
            ])
            ->actions([
                Tables\Actions\Action::make('add_to_directory')
                    ->label('→ Directory')
                    ->icon('heroicon-o-building-storefront')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('category_id')
                            ->label('Directory Category')
                            ->options(fn() => Category::where('type', 'directory')
                                ->whereNull('parent_id')
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(fn($c) => [$c->id => ($c->icon ? $c->icon . ' ' : '') . $c->name])
                                ->toArray())
                            ->required()
                            ->searchable(),
                    ])
                    ->modalHeading('Add to Business Directory')
                    ->modalDescription('Select a category. Photos and AI description will be auto-generated.')
                    ->modalSubmitActionLabel('🚀 Import to Directory')
                    ->action(function (Lead $record, array $data) {
                        $result = self::importLeadToDirectory($record, (int) $data['category_id']);
                        if ($result === 'exists') {
                            Notification::make()->title('Already in directory')->warning()->send();
                        } elseif ($result === 'created') {
                            $record->update(['status' => 'converted']);
                            Notification::make()->title('✅ Added to directory!')->success()->send();
                        } else {
                            Notification::make()->title('Import failed: ' . $result)->danger()->send();
                        }
                    }),

                Tables\Actions\Action::make('maps')
                    ->label('Maps')
                    ->icon('heroicon-o-map-pin')
                    ->color('danger')
                    ->url(fn(Lead $r) => $r->google_maps_url)
                    ->openUrlInNewTab()
                    ->visible(fn(Lead $r) => (bool)$r->google_maps_url),

                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->color('success')
                    ->url(fn(Lead $r) => $r->phone
                        ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $r->phone)
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn(Lead $r) => (bool)$r->phone),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_add_to_directory')
                        ->label('Add to Directory')
                        ->icon('heroicon-o-building-storefront')
                        ->color('success')
                        ->form([
                            Forms\Components\Select::make('category_id')
                                ->label('Directory Category')
                                ->options(fn() => Category::where('type', 'directory')
                                    ->whereNull('parent_id')
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(fn($c) => [$c->id => ($c->icon ? $c->icon . ' ' : '') . $c->name])
                                    ->toArray())
                                ->required()
                                ->searchable(),
                        ])
                        ->modalHeading('Bulk Add to Directory')
                        ->modalDescription('All selected leads will be imported. Photos and AI descriptions auto-generated. Already-imported leads are skipped.')
                        ->modalSubmitActionLabel('🚀 Import All Selected')
                        ->action(function (Collection $records, array $data) {
                            $created = 0;
                            $skipped = 0;
                            foreach ($records as $lead) {
                                $result = self::importLeadToDirectory($lead, (int) $data['category_id']);
                                if ($result === 'created') { $created++; $lead->update(['status' => 'converted']); }
                                else { $skipped++; }
                            }
                            Notification::make()
                                ->title("Imported: $created | Skipped/Exists: $skipped")
                                ->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('mark_contacted')
                        ->label('Mark as Contacted')
                        ->icon('heroicon-o-paper-airplane')
                        ->action(fn($records) => $records->each->update(['status' => 'contacted', 'last_contacted_at' => now()]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('mark_interested')
                        ->label('Mark as Interested')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn($records) => $records->each->update(['status' => 'interested']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /** Returns 'created' | 'exists' | error string */
    private static function importLeadToDirectory(Lead $lead, int $categoryId): string
    {
        if (!$lead->google_place_id) return 'No Google Place ID on this lead';

        if (Business::where('google_place_id', $lead->google_place_id)->exists()) {
            return 'exists';
        }

        $service = new GooglePlacesService();
        $disk    = config('filesystems.default', 'public');

        // Fetch full details (phone, website, photos)
        $details = $service->getPlaceFullDetails($lead->google_place_id);

        // Download up to 3 photos
        $imagePaths = [];
        foreach ($details['photo_refs'] ?? [] as $ref) {
            $bytes = $service->downloadPhoto($ref, 1200);
            if ($bytes) {
                $path = 'businesses/' . Str::uuid() . '.jpg';
                Storage::disk($disk)->put($path, $bytes);
                $imagePaths[] = $path;
            }
        }

        // AI description via Groq
        $description = self::generateDescription(
            $lead->name,
            $lead->category ?? '',
            $details['address'] ?? $lead->address ?? '',
            $lead->city ?? ''
        );

        // Unique slug
        $base = Str::slug($lead->name);
        $slug = $base;
        $i    = 1;
        while (Business::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        Business::create([
            'user_id'         => 1,
            'category_id'     => $categoryId,
            'name'            => $lead->name,
            'slug'            => $slug,
            'description'     => $description,
            'address'         => $details['address'] ?? $lead->address,
            'city'            => $lead->city,
            'province'        => $lead->province,
            'phone'           => $details['phone'] ?? $lead->phone,
            'email'           => $lead->email,
            'website'         => $details['website'] ?? $lead->website,
            'map_url'         => $details['google_maps_url'] ?? $lead->google_maps_url,
            'rating'          => $details['rating'] ?? $lead->rating,
            'review_count'    => $details['review_count'] ?? $lead->review_count ?? 0,
            'images'          => !empty($imagePaths) ? $imagePaths : null,
            'image'           => $imagePaths[0] ?? null,
            'status'          => 'active',
            'is_verified'     => false,
            'is_featured'     => false,
            'google_place_id' => $lead->google_place_id,
        ]);

        return 'created';
    }

    private static function generateDescription(string $name, string $category, string $address, string $city): string
    {
        $keys   = config('services.groq.keys', []);
        $apiKey = is_array($keys) ? (array_values(array_filter($keys))[0] ?? null) : $keys;

        if (!$apiKey) {
            return "$name is a $category business located in $city. Contact us for more information.";
        }

        try {
            $prompt = "Write a 2-3 sentence professional business description for a directory listing. "
                . "Business: \"$name\", Type: $category, Location: $address. "
                . "Factual, friendly, suitable for a Canadian business directory. Plain text only, no markdown.";

            $response = Http::withToken($apiKey)
                ->timeout(10)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'      => 'llama-3.1-8b-instant',
                    'messages'   => [['role' => 'user', 'content' => $prompt]],
                    'max_tokens' => 200,
                ]);

            return trim($response->json('choices.0.message.content') ?? '')
                ?: "$name is a $category business located in $city.";
        } catch (\Exception) {
            return "$name is a $category business located in $city.";
        }
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit'   => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
