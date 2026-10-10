<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $usersQuery = User::query();

        if ($search) {
            $usersQuery->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%');
            });
        }

        if ($role && $role !== 'all') {
            $usersQuery->where('role', $role);
        }

        $users = $usersQuery->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'all_page')
            ->appends($request->query());

        $pendingQuery = User::where('is_verified', false); 

        if ($search) {
            $pendingQuery->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%');
            });
        }

        if ($role && $role !== 'all') {
            $pendingQuery->where('role', $role);
        }

        $pendingUsers = $pendingQuery->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'pending_page')
            ->appends($request->query());

        return view('admin.users.index', compact('users', 'pendingUsers', 'search', 'role'));
    }

    public function create(Request $request)
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');

        $editUser = null;
        if ($request->has('edit')) {
            $editUser = User::find($request->input('edit'));
        }

        $pendingUsers = User::where('is_verified', false)
                            ->when($search, function ($query, $search) {
                                return $query->where(function ($q) use ($search) {
                                    $q->where('name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%");
                                });
                            })
                            ->orderBy('created_at', 'desc')
                            ->paginate(5, ['*'], 'pending_page') 
                            ->withQueryString(); 

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
                        ->paginate(10, ['*'], 'users_page')
                        ->withQueryString();

        return view('admin.users.create', compact('pendingUsers', 'allUsers', 'search', 'roleFilter', 'editUser'));
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
            'is_verified' => true, 
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

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6', 
            'role'     => 'required|in:pengguna,petugas,admin',
        ]);

        $updateData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.create')->with('success', 'Data akun berhasil diperbarui.');
    }
}