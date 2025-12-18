<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Services\TableauEmbedService;

class EmbedController extends Controller
{
    /**
     * Get default viewer username from config
     */
    protected function getDefaultUsername(): string
    {
        return config('tableau.viewer_username') ?: config('tableau.admin_username', 'korlantas');
    }

    /**
     * Get default view path from config
     */
    protected function getDefaultViewPath(): string
    {
        return config('tableau.default_view_path', '');
    }

    /**
     * Return error view when no valid menu/path
     */
    protected function noMenuResponse(string $view, $menus, string $message = null)
    {
        return view($view, [
            'failed' => true,
            'ticket' => '-1',
            'embed_url' => '',
            'error_message' => $message ?? 'Belum ada menu dashboard yang aktif. Silakan tambahkan menu melalui panel admin.',
            'server' => config('tableau.server'),
            'menus' => $menus,
            'activeMenu' => null
        ]);
    }

    /**
     * Get first menu with valid tableau_view_path
     */
    protected function getFirstValidMenu($menus)
    {
        return $menus->first(function ($menu) {
            return !empty($menu->tableau_view_path);
        });
    }

    /**
     * Halaman embed Tableau (full page)
     */
    public function show(TableauEmbedService $tableau)
    {
        $menus = Menu::active()->get();
        $firstMenu = $this->getFirstValidMenu($menus);

        if (!$firstMenu) {
            return $this->noMenuResponse('embed', $menus);
        }

        $username = $firstMenu->tableau_username ?: $this->getDefaultUsername();
        $viewPath = $firstMenu->tableau_view_path;

        if (empty($viewPath)) {
            return $this->noMenuResponse('embed', $menus, 'Menu tidak memiliki Tableau view path yang valid.');
        }

        $data = $tableau->getTrustedUrl($username, $viewPath);

        return view('embed', [
            'failed' => $data['failed'],
            'ticket' => $data['ticket'],
            'embed_url' => $data['embed_url'],
            'error_message' => $data['error_message'],
            'server' => config('tableau.server'),
            'menus' => $menus,
            'activeMenu' => $firstMenu
        ]);
    }

    /**
     * Halaman dashboard home dengan embed Tableau
     */
    public function dashboard(TableauEmbedService $tableau)
    {
        $menus = Menu::active()->get();
        $firstMenu = $this->getFirstValidMenu($menus);

        if (!$firstMenu) {
            return $this->noMenuResponse('dashboard-home', $menus);
        }

        $username = $firstMenu->tableau_username ?: $this->getDefaultUsername();
        $viewPath = $firstMenu->tableau_view_path;

        if (empty($viewPath)) {
            return $this->noMenuResponse('dashboard-home', $menus, 'Menu tidak memiliki Tableau view path yang valid.');
        }

        $data = $tableau->getTrustedUrl($username, $viewPath);

        return view('dashboard-home', [
            'failed' => $data['failed'],
            'ticket' => $data['ticket'],
            'embed_url' => $data['embed_url'],
            'error_message' => $data['error_message'],
            'server' => config('tableau.server'),
            'menus' => $menus,
            'activeMenu' => $firstMenu
        ]);
    }

    /**
     * View specific menu/dashboard berdasarkan menu yang dipilih di sidebar
     */
    public function viewMenu(Menu $menu, TableauEmbedService $tableau)
    {
        if (!$menu->is_active) {
            abort(404);
        }

        $menus = Menu::active()->get();

        // Check if menu has valid view path
        if (empty($menu->tableau_view_path)) {
            return $this->noMenuResponse('dashboard-home', $menus, 'Menu ini tidak memiliki Tableau view path yang valid.');
        }

        $username = $menu->tableau_username ?: $this->getDefaultUsername();
        $viewPath = $menu->tableau_view_path;

        $data = $tableau->getTrustedUrl($username, $viewPath);

        return view('dashboard-home', [
            'failed' => $data['failed'],
            'ticket' => $data['ticket'],
            'embed_url' => $data['embed_url'],
            'error_message' => $data['error_message'],
            'server' => config('tableau.server'),
            'menus' => $menus,
            'activeMenu' => $menu
        ]);
    }
}
