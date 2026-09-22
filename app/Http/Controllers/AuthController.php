<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi salah.'])
                ->onlyInput('email');
        }

<<<<<<< HEAD
        return back()
            ->withErrors(['email' => 'Email atau kata sandi salah.'])
            ->onlyInput('email');
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi salah.'])
                ->onlyInput('email');
        }

=======
        // Akun hasil registrasi mandiri wajib diverifikasi admin dulu (Story #15)
>>>>>>> 1a3dff9d4a7edb4715cd1deaf5ae3acd0e93902e
        if (! Auth::user()->is_verified) {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'Akun kamu masih menunggu verifikasi admin. Coba lagi nanti.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
<<<<<<< HEAD

        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard'); 
        }

=======
>>>>>>> 1a3dff9d4a7edb4715cd1deaf5ae3acd0e93902e
        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => 'pengguna',
            'is_verified' => false,
        ]);

        return redirect()
            ->route('login')
            ->with('status', 'Registrasi berhasil! Akun kamu menunggu verifikasi admin sebelum bisa dipakai login.');
    }
}