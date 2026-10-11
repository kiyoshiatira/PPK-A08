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
        'email'    => ['required', 'email', 'max:255'],
        'password' => ['required'],
        ], $this->messages(), $this->attributes());

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi salah.'])
                ->onlyInput('email');
        }

        if (! Auth::user()->is_verified) {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'Akun kamu masih menunggu verifikasi admin. Coba lagi nanti.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard'); 
        }
        if (Auth::user()->role === 'petugas') {
            return redirect()->route('petugas.dashboard');
        }
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
        'name'     => ['required', 'string', 'min:3', 'max:255'],
        'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], $this->messages(), $this->attributes());


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

    private function messages(): array
    {
        return [
            'required'           => ':attribute wajib diisi.',
            'email'              => 'Format email tidak valid. Contoh: nama@kampus.ac.id',
            'min.string'         => ':attribute minimal :min karakter.',
            'max.string'         => ':attribute maksimal :max karakter.',
            'unique'             => ':attribute sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ];
    }

    private function attributes(): array
    {
        return [
            'name'     => 'Nama lengkap',
            'email'    => 'Email kampus',
            'password' => 'Kata sandi',
        ];
    }
}