<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminPetugasController extends Controller
{
    public function index()
    {
        $petugasList = User::where('role', 'petugas')
                           ->orderBy('created_at', 'desc')
                           ->get();

        return view('admin.petugas.index', compact('petugasList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => 'petugas',
            'is_verified' => true,
        ]);

        return redirect()->route('admin.petugas.index')->with('success', 'Akun petugas berhasil ditambahkan.');
    }
}