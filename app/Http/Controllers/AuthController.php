<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function doLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // login berbasis username (bukan email)
        $remember = $request->boolean('remember');
        if (Auth::attempt($request->only('username','password'), $remember)) {
            $request->session()->regenerate(); // penting supaya sesi baru
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['login' => 'Username atau password salah.'])
                     ->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();   // hapus sesi lama
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showPassword()
    {
        return view('auth.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();
        if (!$user || !Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak cocok.']);
        }

        // Tanpa memanggil ->save(), ini selalu aman:
        User::whereKey(Auth::id())->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('status', 'Password berhasil diperbarui.');
    }
}
