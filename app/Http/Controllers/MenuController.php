<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Menu;
use App\Services\MenuCacheService;
use App\Services\TableauEmbedService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected MenuCacheService $menuCache;

    public function __construct(MenuCacheService $menuCache)
    {
        $this->menuCache = $menuCache;
    }

    public function index()
    {
        $menus = Menu::with('children')->parentMenus()->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $parentMenus = Menu::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.menus.create', compact('parentMenus'));
    }

    public function store(StoreMenuRequest $request)
    {
        Menu::create([
            'name' => $request->name,
            'icon' => $request->icon,
            'tableau_view_path' => $request->tableau_view_path,
            'tableau_username' => $request->tableau_username ?? config('tableau.viewer_username'),
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
            'parent_id' => $request->parent_id,
        ]);

        $this->menuCache->clearCache();

        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        $parentMenus = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->orderBy('name')
            ->get();
        return view('admin.menus.edit', compact('menu', 'parentMenus'));
    }

    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $menu->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'tableau_view_path' => $request->tableau_view_path,
            'tableau_username' => $request->tableau_username ?? config('tableau.viewer_username'),
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
            'parent_id' => $request->parent_id,
        ]);

        $this->menuCache->clearCache();

        return redirect()->route('menus.index')->with('success', 'Menu berhasil diupdate.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        $this->menuCache->clearCache();

        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus.');
    }

    public function reorder(Request $request)
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $id => $order) {
            Menu::where('id', $id)->update(['order' => $order]);
        }

        $this->menuCache->clearCache();

        return response()->json(['success' => true]);
    }

    public function fetchTableauViews(Request $request, TableauEmbedService $tableau)
    {
        $siteId = $request->input('site_id');
        $result = $tableau->getAllViews($siteId);
        return response()->json($result);
    }

    public function fetchTableauSites(TableauEmbedService $tableau)
    {
        $result = $tableau->getSites();
        return response()->json($result);
    }
}
