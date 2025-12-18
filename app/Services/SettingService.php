<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    protected const CACHE_KEY = 'app_settings_service';
    protected const CACHE_TTL = 3600;

    /**
     * Get all settings for views
     */
    public function getAll(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return [
                'app_name' => Setting::get('app_name', 'DAKGAR LANTAS'),
                'app_subtitle' => Setting::get('app_subtitle', 'Dashboard Portal'),
                'app_logo' => Setting::get('app_logo'),
                'app_favicon' => Setting::get('app_favicon'),
                'footer_text' => Setting::get('footer_text', 'KORLANTAS POLRI'),
                'dashboard_refresh_interval' => Setting::get('dashboard_refresh_interval', 0),
                'enable_fullscreen' => Setting::get('enable_fullscreen', true),
            ];
        });
    }

    /**
     * Get single setting
     */
    public function get(string $key, $default = null)
    {
        $settings = $this->getAll();
        return $settings[$key] ?? $default;
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Setting::clearCache();
    }
}
