<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function create()
    {
        $pendingUsers = User::whereIn('role', ['pengguna', 'petugas', 'admin'])
                            ->where('is_verified', false)
                            ->orderBy('created_at', 'desc')
                            ->get();

        $allUsers = User::whereIn('role', ['pengguna', 'petugas', 'admin'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.users.create', compact('pendingUsers', 'allUsers'));
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
            'password'    => $validated['password'], 
            'role'        => $validated['role'],
            'is_verified' => true,
        ]);

        return redirect()->route('admin.users.create')->with('success', 'Akun pengguna berhasil didaftarkan.');
    }

    public function verify(User $user)
    {
        $user->update(['is_verified' => true]);

        return redirect()->back()->with('success', 'Akun pengguna ' . $user->name . ' berhasil diverifikasi.');
    }

    public function reject(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'Pendaftaran akun ' . $user->name . ' telah ditolak dan dihapus.');
    }
}