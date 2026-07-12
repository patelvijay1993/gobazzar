<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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

    public bool   $email_verification_required = true;
    public bool   $business_enabled            = true;
    public bool   $ai_assistant_enabled        = true;

    // SEO settings
    public string $seo_site_title         = '';
    public string $seo_tagline            = '';
    public string $seo_meta_description   = '';
    public string $seo_og_image           = '';
    public string $seo_google_verification = '';
    public string $seo_google_analytics   = '';
    public string $seo_facebook_pixel     = '';

    public string $app_name              = '';
    public string $app_url               = '';
    public string $openai_api_key        = '';
    public string $google_vision_api_key = '';
    public string $google_places_api_key = '';
    public string $gemini_api_key        = '';
    public string $groq_api_key          = '';
    public string $groq_api_key1         = '';
    public string $stripe_key            = '';
    public string $stripe_secret         = '';
    public string $stripe_webhook_secret = '';
    public string $filesystem_disk       = 'public';
    public string $aws_access_key_id     = '';
    public string $aws_secret_access_key = '';
    public string $aws_default_region    = '';
    public string $aws_bucket            = '';
    public string $aws_use_path_style    = 'false';
    public string $mail_mailer           = 'smtp';
    public string $mail_scheme           = 'smtps';
    public string $mail_host             = '';
    public string $mail_port             = '465';
    public string $mail_username         = '';
    public string $mail_password         = '';
    public string $mail_from_address     = '';
    public string $mail_from_name        = '';

    public function mount(): void
    {
        $this->email_verification_required = Setting::bool('email_verification_required', true);
        $this->business_enabled            = Setting::bool('business_enabled', true);
        $this->ai_assistant_enabled        = Setting::bool('ai_assistant_enabled', true);

        // SEO
        $this->seo_site_title          = Setting::get('seo_site_title', 'GoBazaar');
        $this->seo_tagline             = Setting::get('seo_tagline', "Canada's #1 Community Marketplace");
        $this->seo_meta_description    = Setting::get('seo_meta_description', '');
        $this->seo_og_image            = Setting::get('seo_og_image', '');
        $this->seo_google_verification = Setting::get('seo_google_verification', '');
        $this->seo_google_analytics    = Setting::get('seo_google_analytics', '');
        $this->seo_facebook_pixel      = Setting::get('seo_facebook_pixel', '');

        $this->app_name              = $this->readEnvValue('APP_NAME') ?: 'GoBazaar';
        $this->app_url               = $this->readEnvValue('APP_URL');
        $this->openai_api_key        = $this->readEnvValue('OPENAI_API_KEY');
        $this->google_vision_api_key = $this->readEnvValue('GOOGLE_VISION_API_KEY');
        $this->google_places_api_key = $this->readEnvValue('GOOGLE_PLACES_API_KEY');
        $this->gemini_api_key        = $this->readEnvValue('GEMINI_API_KEY');
        $this->groq_api_key          = $this->readEnvValue('GROQ_API_KEY');
        $this->groq_api_key1         = $this->readEnvValue('GROQ_API_KEY1');
        $this->stripe_key            = $this->readEnvValue('STRIPE_KEY');
        $this->stripe_secret         = $this->readEnvValue('STRIPE_SECRET');
        $this->stripe_webhook_secret = $this->readEnvValue('STRIPE_WEBHOOK_SECRET');
        $this->filesystem_disk       = $this->readEnvValue('FILESYSTEM_DISK') ?: 'public';
        $this->aws_access_key_id     = $this->readEnvValue('AWS_ACCESS_KEY_ID');
        $this->aws_secret_access_key = $this->readEnvValue('AWS_SECRET_ACCESS_KEY');
        $this->aws_default_region    = $this->readEnvValue('AWS_DEFAULT_REGION') ?: 'us-east-1';
        $this->aws_bucket            = $this->readEnvValue('AWS_BUCKET');
        $this->aws_use_path_style    = $this->readEnvValue('AWS_USE_PATH_STYLE_ENDPOINT') ?: 'false';
        $this->mail_mailer           = $this->readEnvValue('MAIL_MAILER') ?: 'smtp';
        $this->mail_scheme           = $this->readEnvValue('MAIL_SCHEME') ?: 'smtps';
        $this->mail_host             = $this->readEnvValue('MAIL_HOST');
        $this->mail_port             = $this->readEnvValue('MAIL_PORT') ?: '465';
        $this->mail_username         = $this->readEnvValue('MAIL_USERNAME');
        $this->mail_password         = $this->readEnvValue('MAIL_PASSWORD');
        $this->mail_from_address     = trim($this->readEnvValue('MAIL_FROM_ADDRESS'), '"');
        $raw_from_name               = trim($this->readEnvValue('MAIL_FROM_NAME'), '"');
        $this->mail_from_name        = ($raw_from_name === '${APP_NAME}') ? config('app.name') : $raw_from_name;
    }

    private function readEnvValue(string $key): string
    {
        $content = file_get_contents(base_path('.env'));
        if (preg_match("/^{$key}=(.*)$/m", $content, $m)) {
            return trim($m[1]);
        }
        return '';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')
                    ->description('Basic application settings.')
                    ->icon('heroicon-o-globe-alt')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('app_name')
                                ->label('Site Name')
                                ->placeholder('GoBazaar')
                                ->helperText('Application name shown in emails and browser tabs.')
                                ->maxLength(100),

                            TextInput::make('app_url')
                                ->label('App URL')
                                ->placeholder('https://gobazaar.ca')
                                ->helperText('Full URL of the site including https://')
                                ->maxLength(200),
                        ]),
                    ]),

                Section::make('Features')
                    ->description('Enable or disable site features. Disabled features show a "Coming Soon" popup to users.')
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        Toggle::make('business_enabled')
                            ->label('My Business / Business Directory')
                            ->helperText('When OFF — users see a "Coming Soon" popup when they click My Business.')
                            ->onColor('success')
                            ->offColor('danger'),

                        Toggle::make('ai_assistant_enabled')
                            ->label('AI Assistant Chatbot')
                            ->helperText('When ON — floating 🤖 button appears on all pages. Users can search listings, jobs, events by natural language.')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),

                Section::make('SEO & Google')
                    ->description('Search engine optimization, Google Search Console verification, and analytics tracking.')
                    ->icon('heroicon-o-magnifying-glass-circle')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('seo_site_title')
                                ->label('Site Name')
                                ->placeholder('GoBazaar')
                                ->helperText('Appears in browser tab and search results.')
                                ->maxLength(100),

                            TextInput::make('seo_tagline')
                                ->label('Tagline')
                                ->placeholder("Canada's #1 Community Marketplace")
                                ->helperText('Short description shown in search results title.')
                                ->maxLength(100),
                        ]),

                        Textarea::make('seo_meta_description')
                            ->label('Default Meta Description')
                            ->placeholder('GoBazaar — Find classifieds, jobs, events, and businesses across Canada.')
                            ->helperText('Shown in Google search results. Keep it 120–160 characters.')
                            ->rows(2)
                            ->maxLength(200),

                        TextInput::make('seo_og_image')
                            ->label('Default OG Image URL')
                            ->placeholder('https://your-bucket.s3.amazonaws.com/og-image.jpg')
                            ->helperText('Image shown when pages are shared on Facebook/WhatsApp. Recommended: 1200×630px.')
                            ->url()
                            ->maxLength(500),

                        TextInput::make('seo_google_verification')
                            ->label('Google Search Console Verification Code')
                            ->placeholder('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx')
                            ->helperText('Only the content="..." value from the Google meta tag. Example: abc123def456...')
                            ->maxLength(200),

                        TextInput::make('seo_google_analytics')
                            ->label('Google Analytics 4 — Measurement ID')
                            ->placeholder('G-XXXXXXXXXX')
                            ->helperText('Get from analytics.google.com → Admin → Data Streams → Measurement ID')
                            ->maxLength(50),

                        TextInput::make('seo_facebook_pixel')
                            ->label('Facebook Pixel ID')
                            ->placeholder('123456789012345')
                            ->helperText('From Facebook Business Manager → Events Manager → Pixel ID')
                            ->maxLength(50),
                    ]),

                Section::make('Authentication')
                    ->description('Control how users authenticate on GoBazaar.')
                    ->collapsible()
                    ->schema([
                        Toggle::make('email_verification_required')
                            ->label('Email Verification Required')
                            ->helperText('When ON — users must verify their email before logging in. When OFF — users can log in immediately after registering.')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),

                Section::make('AI API Keys')
                    ->description('Keys are stored securely in the .env file. Leave blank to disable that AI feature.')
                    ->icon('heroicon-o-cpu-chip')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('openai_api_key')
                            ->label('OpenAI API Key')
                            ->helperText('Used for content moderation (text flagging). Get from platform.openai.com')
                            ->placeholder('sk-proj-...')
                            ->password()
                            ->revealable()
                            ->maxLength(200),

                        TextInput::make('google_vision_api_key')
                            ->label('Google Vision API Key')
                            ->helperText('Used for image moderation. Get from console.cloud.google.com')
                            ->placeholder('AIzaSy...')
                            ->password()
                            ->revealable()
                            ->maxLength(200),

                        TextInput::make('gemini_api_key')
                            ->label('Gemini API Key')
                            ->helperText('Used for AI content generation (business descriptions). Get from aistudio.google.com')
                            ->placeholder('AIzaSy...')
                            ->password()
                            ->revealable()
                            ->maxLength(200),

                        TextInput::make('groq_api_key')
                            ->label('Groq API Key')
                            ->helperText('Used for fast AI content generation (business descriptions fallback). Get from console.groq.com')
                            ->placeholder('gsk_...')
                            ->password()
                            ->revealable()
                            ->maxLength(200),

                        TextInput::make('groq_api_key1')
                            ->label('Groq API Key (Backup)')
                            ->helperText('Backup Groq key used when primary key hits rate limit.')
                            ->placeholder('gsk_...')
                            ->password()
                            ->revealable()
                            ->maxLength(200),

                        TextInput::make('google_places_api_key')
                            ->label('Google Places API Key')
                            ->helperText('Used for Lead Finder — search businesses on Google Maps. Enable "Places API" in console.cloud.google.com')
                            ->placeholder('AIzaSy...')
                            ->password()
                            ->revealable()
                            ->maxLength(200),
                    ]),

                Section::make('Stripe Payments')
                    ->description('Stripe keys for subscription and payment processing. Get from dashboard.stripe.com')
                    ->icon('heroicon-o-credit-card')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('stripe_key')
                            ->label('Publishable Key')
                            ->helperText('Frontend key (pk_test_... or pk_live_...)')
                            ->placeholder('pk_test_...')
                            ->password()
                            ->revealable()
                            ->maxLength(300),

                        TextInput::make('stripe_secret')
                            ->label('Secret Key')
                            ->helperText('Backend secret key (sk_test_... or sk_live_...)')
                            ->placeholder('sk_test_...')
                            ->password()
                            ->revealable()
                            ->maxLength(300),

                        TextInput::make('stripe_webhook_secret')
                            ->label('Webhook Secret')
                            ->helperText('Webhook signing secret (whsec_...). Set in Stripe Dashboard → Webhooks')
                            ->placeholder('whsec_...')
                            ->password()
                            ->revealable()
                            ->maxLength(300),
                    ]),

                Section::make('AWS / S3 Storage')
                    ->description('File storage configuration. Switch between local storage and Amazon S3.')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Select::make('filesystem_disk')
                            ->label('Storage Driver')
                            ->helperText('Where uploaded files (images, logos) are stored.')
                            ->options([
                                'public' => '💾 Local (public disk)',
                                's3'     => '☁️ Amazon S3',
                            ])
                            ->required(),

                        TextInput::make('aws_access_key_id')
                            ->label('AWS Access Key ID')
                            ->placeholder('AKIA...')
                            ->password()
                            ->revealable()
                            ->maxLength(200),

                        TextInput::make('aws_secret_access_key')
                            ->label('AWS Secret Access Key')
                            ->placeholder('your-secret-key')
                            ->password()
                            ->revealable()
                            ->maxLength(200),

                        TextInput::make('aws_default_region')
                            ->label('AWS Region')
                            ->placeholder('us-east-1')
                            ->maxLength(50),

                        TextInput::make('aws_bucket')
                            ->label('S3 Bucket Name')
                            ->placeholder('my-bucket')
                            ->maxLength(100),

                        Select::make('aws_use_path_style')
                            ->label('Use Path Style Endpoint')
                            ->helperText('Enable for local S3-compatible storage (MinIO etc). Keep false for real AWS.')
                            ->options([
                                'false' => 'false (standard — use for AWS)',
                                'true'  => 'true (path-style — use for MinIO/local)',
                            ]),
                    ]),

                Section::make('Mail / SMTP')
                    ->description('Outgoing email configuration for verification emails, notifications, and password resets.')
                    ->collapsible()
                    ->collapsed()
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('mail_mailer')
                                ->label('Mail Driver')
                                ->options([
                                    'smtp'     => 'SMTP',
                                    'sendmail' => 'Sendmail',
                                    'mailgun'  => 'Mailgun',
                                    'ses'      => 'Amazon SES',
                                    'log'      => 'Log (testing only)',
                                    'array'    => 'Array (testing only)',
                                ])
                                ->required(),

                            Select::make('mail_scheme')
                                ->label('Encryption')
                                ->options([
                                    'smtps' => 'SMTPS (SSL — port 465)',
                                    'tls'   => 'TLS (STARTTLS — port 587)',
                                    ''      => 'None (port 25)',
                                ]),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('mail_host')
                                ->label('SMTP Host')
                                ->placeholder('smtp.resend.com')
                                ->maxLength(200),

                            TextInput::make('mail_port')
                                ->label('SMTP Port')
                                ->placeholder('465')
                                ->maxLength(10),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('mail_username')
                                ->label('Username')
                                ->placeholder('resend / apikey / your@email.com')
                                ->maxLength(200),

                            TextInput::make('mail_password')
                                ->label('Password / API Key')
                                ->placeholder('re_... / your-api-key')
                                ->password()
                                ->revealable()
                                ->maxLength(300),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('mail_from_address')
                                ->label('From Address')
                                ->placeholder('noreply@yourdomain.com')
                                ->email()
                                ->maxLength(200),

                            TextInput::make('mail_from_name')
                                ->label('From Name')
                                ->placeholder('GoBazaar')
                                ->maxLength(100),
                        ]),
                    ]),
            ])
            ->statePath(null);
    }

    public function save(): void
    {
        // Save DB settings
        Setting::set('email_verification_required', $this->email_verification_required ? '1' : '0');
        Setting::set('business_enabled', $this->business_enabled ? '1' : '0');
        Setting::set('ai_assistant_enabled', $this->ai_assistant_enabled ? '1' : '0');

        // SEO settings
        Setting::set('seo_site_title',          $this->seo_site_title);
        Setting::set('seo_tagline',             $this->seo_tagline);
        Setting::set('seo_meta_description',    $this->seo_meta_description);
        Setting::set('seo_og_image',            $this->seo_og_image);
        Setting::set('seo_google_verification', $this->seo_google_verification);
        Setting::set('seo_google_analytics',    $this->seo_google_analytics);
        Setting::set('seo_facebook_pixel',      $this->seo_facebook_pixel);

        // Update .env file for AI keys
        $this->updateEnv([
            'APP_NAME'              => $this->app_name,
            'APP_URL'               => $this->app_url,
            'OPENAI_API_KEY'        => $this->openai_api_key,
            'GOOGLE_VISION_API_KEY' => $this->google_vision_api_key,
            'GOOGLE_PLACES_API_KEY' => $this->google_places_api_key,
            'GEMINI_API_KEY'        => $this->gemini_api_key,
            'GROQ_API_KEY'          => $this->groq_api_key,
            'GROQ_API_KEY1'         => $this->groq_api_key1,
            'STRIPE_KEY'                  => $this->stripe_key,
            'STRIPE_SECRET'               => $this->stripe_secret,
            'STRIPE_WEBHOOK_SECRET'       => $this->stripe_webhook_secret,
            'FILESYSTEM_DISK'             => $this->filesystem_disk,
            'AWS_ACCESS_KEY_ID'           => $this->aws_access_key_id,
            'AWS_SECRET_ACCESS_KEY'       => $this->aws_secret_access_key,
            'AWS_DEFAULT_REGION'          => $this->aws_default_region,
            'AWS_BUCKET'                  => $this->aws_bucket,
            'AWS_USE_PATH_STYLE_ENDPOINT' => $this->aws_use_path_style,
            'MAIL_MAILER'                 => $this->mail_mailer,
            'MAIL_SCHEME'                 => $this->mail_scheme,
            'MAIL_HOST'                   => $this->mail_host,
            'MAIL_PORT'                   => $this->mail_port,
            'MAIL_USERNAME'               => $this->mail_username,
            'MAIL_PASSWORD'               => $this->mail_password,
            'MAIL_FROM_ADDRESS'           => $this->mail_from_address,
            'MAIL_FROM_NAME'              => $this->mail_from_name,
        ]);

        // Clear config cache so new keys take effect immediately
        \Artisan::call('config:clear');

        Notification::make()
            ->title('Settings saved!')
            ->success()
            ->send();
    }

    private function updateEnv(array $values): void
    {
        $envPath = base_path('.env');

        if (!is_writable($envPath)) {
            Notification::make()
                ->title('Cannot write .env file')
                ->body('The .env file is not writable. Please update mail credentials manually in the .env file on your server.')
                ->warning()
                ->persistent()
                ->send();
            return;
        }

        $content = file_get_contents($envPath);

        foreach ($values as $key => $value) {
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $content
                );
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $content);
    }
}
