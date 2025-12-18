<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share menus to all views that use layouts
        View::composer([
            'layouts.user',
            'layouts.admin',
            'dashboard-home',
            'embed'
        ], function ($view) {
            $menus = $this->getCachedMenus();
            $view->with('sidebarMenus', $menus);
        });

        // Share app settings to all views
        View::composer('*', function ($view) {
            $view->with('appSettings', $this->getCachedSettings());
        });
    }

    protected function getCachedMenus()
    {
        return Cache::remember('sidebar_menus', 300, function () {
            return Menu::activeParentMenus()->get();
        });
    }

    protected function getCachedSettings(): array
    {
        return Cache::remember('app_settings_view', 3600, function () {
            try {
                return [
                    'app_name' => Setting::get('app_name', 'DAKGAR LANTAS'),
                    'app_subtitle' => Setting::get('app_subtitle', 'Dashboard Portal'),
                    'app_logo' => Setting::get('app_logo'),
                    'app_favicon' => Setting::get('app_favicon'),
                    'footer_text' => Setting::get('footer_text', 'KORLANTAS POLRI'),
                    'dashboard_refresh_interval' => Setting::get('dashboard_refresh_interval', 0),
                    'enable_fullscreen' => Setting::get('enable_fullscreen', true),
                ];
            } catch (\Exception $e) {
                // Return defaults if settings table doesn't exist yet
                return [
                    'app_name' => 'DAKGAR LANTAS',
                    'app_subtitle' => 'Dashboard Portal',
                    'app_logo' => null,
                    'app_favicon' => null,
                    'footer_text' => 'KORLANTAS POLRI',
                    'dashboard_refresh_interval' => 0,
                    'enable_fullscreen' => true,
                ];
            }
        });
    }
}
