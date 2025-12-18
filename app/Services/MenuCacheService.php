<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\Cache;

class MenuCacheService
{
    protected const CACHE_KEY = 'sidebar_menus';
    protected const CACHE_TTL = 300; // 5 minutes

    /**
     * Get cached menus
     */
    public function getMenus()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Menu::activeParentMenus()->get();
        });
    }

    /**
     * Clear menu cache
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Refresh menu cache
     */
    public function refreshCache()
    {
        $this->clearCache();
        return $this->getMenus();
    }
}
