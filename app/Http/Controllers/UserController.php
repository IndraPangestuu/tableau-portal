<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id_user', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:user',
            'telp' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:user',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'telp' => $request->telp,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_role_id' => $request->role === 'admin' ? 1 : 3,
            'account_status' => 'Active',
            'password_expire_date' => now()->addMonths(6),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('user')->ignore($user->id_user, 'id_user')],
            'telp' => 'nullable|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('user')->ignore($user->id_user, 'id_user')],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        $data = [
            'nama' => $request->nama,
            'username' => $request->username,
            'telp' => $request->telp,
            'email' => $request->email,
            'user_role_id' => $request->role === 'admin' ? 1 : 3,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        if ($user->id_user === auth()->user()->id_user) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
