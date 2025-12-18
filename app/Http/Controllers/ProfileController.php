<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $favorites = $user->favorites()->with('menu')->get();
        $recentDashboards = $user->recentDashboards()
            ->with('menu')
            ->orderBy('accessed_at', 'desc')
            ->limit(5)
            ->get();

        return view('profile.show', compact('user', 'favorites', 'recentDashboards'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('user')->ignore($user->id_user, 'id_user')],
            'telp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'telp' => $request->telp,
        ];

        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($user->foto && file_exists(public_path($user->foto))) {
                unlink(public_path($user->foto));
            }

            $file = $request->file('foto');
            $filename = 'avatar_' . $user->id_user . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $data['foto'] = 'uploads/avatars/' . $filename;
        }

        $user->update($data);

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')->with('success', 'Password berhasil diubah.');
    }
}
