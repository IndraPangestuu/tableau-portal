<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

    public function store(StoreUserRequest $request)
    {
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

    public function update(UpdateUserRequest $request, User $user)
    {
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
