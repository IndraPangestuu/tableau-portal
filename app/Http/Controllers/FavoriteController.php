<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Menu $menu)
    {
        $user = auth()->user();
        $isFavorite = $user->toggleFavorite($menu->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite,
                'message' => $isFavorite ? 'Ditambahkan ke favorit' : 'Dihapus dari favorit'
            ]);
        }

        return back()->with('success', $isFavorite ? 'Ditambahkan ke favorit' : 'Dihapus dari favorit');
    }

    public function index()
    {
        $favorites = auth()->user()->favorites()->with('menu')->get();
        return view('favorites.index', compact('favorites'));
    }
}
