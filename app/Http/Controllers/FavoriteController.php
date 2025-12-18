<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Menu $menu)
    {
        try {
            $user = auth()->user();
            $isFavorite = $user->toggleFavorite($menu->id);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'is_favorite' => $isFavorite,
                    'message' => $isFavorite ? 'Ditambahkan ke favorit' : 'Dihapus dari favorit'
                ]);
            }

            return back()->with('success', $isFavorite ? 'Ditambahkan ke favorit' : 'Dihapus dari favorit');
        } catch (\Exception $e) {
            Log::error('Toggle favorite error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah favorit: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal mengubah favorit');
        }
    }

    public function index()
    {
        $favorites = auth()->user()->favorites()->with('menu')->get();
        return view('favorites.index', compact('favorites'));
    }
}
