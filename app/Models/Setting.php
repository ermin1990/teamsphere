<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
    }

    /**
     * Where app notification emails (bug reports, new registrations, plan
     * upgrade requests) should be sent - the configured notification_email
     * if set, otherwise every admin user's email.
     *
     * @return array<int, string>
     */
    public static function notificationRecipients(): array
    {
        $configured = static::get('notification_email');
        if ($configured) {
            return [$configured];
        }

        return User::where('is_admin', true)->pluck('email')->all();
    }
}
