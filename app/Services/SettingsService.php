<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_KEY = 'app_settings';

    private const CACHE_TTL = 60;

    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        return $settings[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        AppSetting::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'updated_at' => now(),
            ]
        );

        Cache::forget(self::CACHE_KEY);
    }

    public function isChatboxEnabled(): bool
    {
        return (bool) $this->get('chatbox_enabled', false);
    }

    public function isOpenAiConfigured(): bool
    {
        return filled(config('openai.api_key'));
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return AppSetting::query()
                ->pluck('value', 'key')
                ->all();
        });
    }
}
