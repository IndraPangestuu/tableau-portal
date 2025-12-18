<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function menus(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $user = auth()->user();

        $menus = Menu::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->whereNotNull('tableau_view_path')
            ->limit(10)
            ->get()
            ->filter(function ($menu) use ($user) {
                return $user->canAccessMenu($menu->id);
            })
            ->map(function ($menu) use ($user) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'icon' => $menu->icon,
                    'url' => route('view.menu', $menu),
                    'is_favorite' => $user->hasFavorite($menu->id),
                ];
            })
            ->values();

        return response()->json(['results' => $menus]);
    }
}
