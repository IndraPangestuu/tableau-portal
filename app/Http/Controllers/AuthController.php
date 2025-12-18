<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required',
            'password' => 'required',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        // Cek apakah login pakai username atau NRP
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 
                     (is_numeric($login) ? 'nrp' : 'username');

        $credential = [
            $fieldType => $login,
            'password' => $password
        ];

        if (Auth::attempt($credential)) {
            $request->session()->regenerate();
            
            // Debug logging
            \Log::info('Login successful', [
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'is_authenticated' => Auth::check()
            ]);
            
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'loginError' => 'Username/NRP atau password salah.',
        ])->withInput($request->only('login'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
