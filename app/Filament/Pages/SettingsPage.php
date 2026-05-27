<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class SettingsPage extends Page
{
    protected string $view = 'filament.pages.settings-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Settings';

    public string $app_name      = '';
    public string $invite_secret = '';

    public function mount(): void
    {
        $this->app_name      = Setting::get('app_name', config('app.name'));
        $this->invite_secret = Setting::get('invite_secret') ?? $this->generateSecret();
    }

    public function save(): void
    {
        Setting::set('app_name',      $this->app_name);
        Setting::set('invite_secret', $this->invite_secret);

        Notification::make()->title('Settings saved!')->success()->send();
    }

    public function regenerateSecret(): void
    {
        $this->invite_secret = $this->generateSecret();
        Setting::set('invite_secret', $this->invite_secret);

        Notification::make()->title('New secret generated. Save to keep it.')->warning()->send();
    }

    public function getInviteUrl(): string
    {
        $phone   = ltrim(config('services.whatsapp.phone_number_id', ''), '+');
        $message = urlencode('START_' . $this->invite_secret);
        return "https://wa.me/{$phone}?text={$message}";
    }

    public function getWebhookUrl(): string
    {
        return url('/webhook/whatsapp');
    }

    public function getVerifyToken(): string
    {
        return config('services.whatsapp.verify_token', 'not-set');
    }

    private function generateSecret(): string
    {
        return strtoupper(Str::random(6)) . '-' . strtoupper(Str::random(6));
    }
}
