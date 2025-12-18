<?php

namespace App\Console\Commands;

use App\Services\MenuCacheService;
use Illuminate\Console\Command;

class ClearMenuCache extends Command
{
    protected $signature = 'menu:clear-cache';
    protected $description = 'Clear menu sidebar cache';

    public function handle(MenuCacheService $menuCache): int
    {
        $menuCache->clearCache();
        $this->info('Menu cache cleared successfully.');

        return Command::SUCCESS;
    }
}
