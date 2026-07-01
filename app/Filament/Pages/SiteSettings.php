<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Site Settings';
    protected static ?string $navigationGroup = 'System';
    protected static ?int    $navigationSort  = 99;
    protected static string  $view            = 'filament.pages.site-settings';

    public bool $email_verification_required = true;

    public function mount(): void
    {
        $this->email_verification_required = Setting::bool('email_verification_required', true);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Authentication')
                    ->description('Control how users authenticate on GoBazaar.')
                    ->schema([
                        Toggle::make('email_verification_required')
                            ->label('Email Verification Required')
                            ->helperText('When ON — users must verify their email before logging in. When OFF — users can log in immediately after registering.')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
            ])
            ->statePath(null);
    }

    public function save(): void
    {
        Setting::set('email_verification_required', $this->email_verification_required ? '1' : '0');

        Notification::make()
            ->title('Settings saved!')
            ->success()
            ->send();
    }
}
