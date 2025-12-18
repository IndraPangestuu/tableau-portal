<?php

namespace App\Providers;

use App\Models\Menu;
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
        // Share menus to all views that use user layout
        View::composer(['layouts.user', 'dashboard-home', 'embed'], function ($view) {
            $menus = $this->getCachedMenus();
            $view->with('sidebarMenus', $menus);
        });
    }

    /**
     * Get menus from cache or database
     */
    protected function getCachedMenus()
    {
        return Cache::remember('sidebar_menus', 300, function () {
            return Menu::activeParentMenus()->get();
        });
    }
}
