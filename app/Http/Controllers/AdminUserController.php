<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function create(Request $request)
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');

        // 1. Mengambil akun registrasi mandiri yang BELUM diverifikasi (Tabel Atas)
        $pendingUsers = User::where('is_verified', false)
                            ->when($search, function ($query, $search) {
                                return $query->where(function ($q) use ($search) {
                                    $q->where('name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%");
                                });
                            })
                            ->orderBy('created_at', 'desc')
                            ->get();

        // 2. Mengambil akun yang SUDAH diverifikasi untuk tabel utama dengan filter role & pencarian (Tabel Bawah)
        $allUsers = User::where('is_verified', true)
                        ->when($roleFilter, function ($query, $roleFilter) {
                            return $query->where('role', $roleFilter);
                        })
                        ->when($search, function ($query, $search) {
                            return $query->where(function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                            });
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.users.create', compact('pendingUsers', 'allUsers', 'search', 'roleFilter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:pengguna,petugas,admin',
        ]);

        User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => $validated['role'],
            'is_verified' => true, // Akun yang dibuat langsung oleh admin otomatis terverifikasi
        ]);

        return redirect()->route('admin.users.create')->with('success', 'Akun baru berhasil ditambahkan.');
    }

    public function verify(User $user)
    {
        $user->update(['is_verified' => true]);

        return redirect()->back()->with('success', 'Akun ' . $user->name . ' berhasil diverifikasi.');
    }

    public function reject(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'Pendaftaran akun ' . $user->name . ' telah ditolak.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'Akun berhasil dihapus.');
    }
}