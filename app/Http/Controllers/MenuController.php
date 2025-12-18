<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Services\TableauEmbedService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        // Get parent menus with their children
        $menus = Menu::with('children')->parentMenus()->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        // Get parent menus for dropdown
        $parentMenus = Menu::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.menus.create', compact('parentMenus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'tableau_view_path' => 'nullable|string|max:500',
            'tableau_username' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable',
            'parent_id' => 'nullable|exists:menus,id',
        ]);

        Menu::create([
            'name' => $request->name,
            'icon' => $request->icon,
            'tableau_view_path' => $request->tableau_view_path,
            'tableau_username' => $request->tableau_username ?? 'korlantas_viewer_2',
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        // Get parent menus for dropdown (exclude current menu and its children)
        $parentMenus = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->orderBy('name')
            ->get();
        return view('admin.menus.edit', compact('menu', 'parentMenus'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'tableau_view_path' => 'nullable|string|max:500',
            'tableau_username' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable',
            'parent_id' => 'nullable|exists:menus,id',
        ]);

        // Prevent setting parent to itself or its children
        if ($request->parent_id == $menu->id) {
            return back()->withErrors(['parent_id' => 'Menu tidak bisa menjadi parent dari dirinya sendiri.']);
        }

        $menu->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'tableau_view_path' => $request->tableau_view_path,
            'tableau_username' => $request->tableau_username ?? 'korlantas_viewer_2',
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('menus.index')->with('success', 'Menu berhasil diupdate.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus.');
    }

    public function reorder(Request $request)
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $id => $order) {
            Menu::where('id', $id)->update(['order' => $order]);
        }
        return response()->json(['success' => true]);
    }

    /**
     * Ambil daftar views dari Tableau Server
     */
    public function fetchTableauViews(Request $request, TableauEmbedService $tableau)
    {
        $siteId = $request->input('site_id');
        $result = $tableau->getAllViews($siteId);
        return response()->json($result);
    }

    /**
     * Ambil daftar sites dari Tableau Server
     */
    public function fetchTableauSites(TableauEmbedService $tableau)
    {
        $result = $tableau->getSites();
        return response()->json($result);
    }
}
