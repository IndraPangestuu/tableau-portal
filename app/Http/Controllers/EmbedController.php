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
        return config('tableau.viewer_username', 'korlantas');
    }

    /**
     * Halaman embed Tableau (full page)
     */
    public function show(TableauEmbedService $tableau)
    {
        $menus = Menu::active()->get();
        $firstMenu = $menus->first();

        if ($firstMenu) {
            $username = $firstMenu->tableau_username ?: $this->getDefaultUsername();
            $viewPath = $firstMenu->tableau_view_path;
        } else {
            // Tidak ada menu aktif
            return view('embed', [
                'failed' => true,
                'ticket' => '-1',
                'embed_url' => '',
                'error_message' => 'Belum ada menu dashboard yang aktif. Silakan tambahkan menu melalui panel admin.',
                'server' => config('tableau.server'),
                'menus' => $menus,
                'activeMenu' => null
            ]);
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
        $firstMenu = $menus->first();

        if ($firstMenu) {
            // Ambil dari menu pertama yang aktif
            $username = $firstMenu->tableau_username ?: $this->getDefaultUsername();
            $viewPath = $firstMenu->tableau_view_path;
            $activeMenu = $firstMenu;

            $data = $tableau->getTrustedUrl($username, $viewPath);

            return view('dashboard-home', [
                'failed' => $data['failed'],
                'ticket' => $data['ticket'],
                'embed_url' => $data['embed_url'],
                'error_message' => $data['error_message'],
                'server' => config('tableau.server'),
                'menus' => $menus,
                'activeMenu' => $activeMenu
            ]);
        }

        // Tidak ada menu aktif - tampilkan pesan
        return view('dashboard-home', [
            'failed' => true,
            'ticket' => '-1',
            'embed_url' => '',
            'error_message' => 'Belum ada menu dashboard yang aktif. Silakan tambahkan menu melalui panel admin.',
            'server' => config('tableau.server'),
            'menus' => $menus,
            'activeMenu' => null
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

        // Ambil username dan path dari menu yang dipilih
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
