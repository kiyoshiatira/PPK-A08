@extends('layouts.admin')

@section('content')
<div class="page-header-flex">
    <div>
        <h1 class="page-title" style="font-size: 24px; font-weight: 700; margin-bottom: 6px;">Kelola akun</h1>
        <p class="page-subtitle" style="color: #6b7280; font-size: 14px;">Tambah akun langsung serta verifikasi atau tolak registrasi mandiri.</p>
    </div>
    <a href="#form-tambah" class="btn-primary">Tambah akun</a>
</div>

<!-- 1. Tabel Menunggu Verifikasi (Paling Atas) -->
<div class="card-ui">
    <div class="card-title">
        Menunggu verifikasi 
        <span class="badge badge-yellow" style="font-size: 11px;">{{ $pendingUsers->count() }} permintaan</span>
    </div>
    <table class="table-ui">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Tanggal Daftar</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingUsers as $user)
                <tr>
                    <td style="font-weight: 600; color: #111;">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d M Y') }}</td>
                    <td style="text-align: right;">
                        <!-- Form Verifikasi -->
                        <form action="{{ route('admin.users.verify', $user->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            <button type="submit" class="action-link text-blue" style="background: transparent; border: none; cursor: pointer; padding: 0;">Verifikasi</button>
                        </form>
                        
                        <!-- Form Tolak -->
                        <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-link text-red" style="background: transparent; border: none; cursor: pointer; padding: 0; margin-right: 0;">Tolak</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #6b7280;">Tidak ada permintaan verifikasi saat ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 15px; display: flex; justify-content: flex-end;">
        {{ $pendingUsers->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>

<!-- 2. Tabel Semua Akun (Menggantikan Semua Pengguna) -->
<div class="card-ui">
    <div class="card-title">Semua akun</div>
    <!-- Filter & Search Bar Section -->
    <div style="background: #ffffff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
        <form action="{{ route('admin.users.create') }}" method="GET" style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
            
            <!-- Search Bar Input -->
            <div style="flex: 1; min-width: 240px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px;">Cari Pengguna</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama atau email..." style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
            </div>

            <!-- Filter Berdasarkan Role -->
            <div style="width: 200px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px;">Filter Peran (Role)</label>
                <select name="role" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: #fff;">
                    <option value="all">Semua Peran</option>
                    <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                    <option value="Petugas" {{ request('role') == 'Petugas' ? 'selected' : '' }}>Petugas</option>
                    <option value="Pengguna" {{ request('role') == 'Pengguna' ? 'selected' : '' }}>Pengguna</option>
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div style="display: flex; gap: 8px; align-items: flex-end; margin-top: 22px;">
                <button type="submit" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer;">Cari</button>
                <a href="{{ route('admin.users.create') }}" style="padding: 10px 16px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-block;">Reset</a>
            </div>

        </form>
    </div>
    <table class="table-ui">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Peran</th>
                <th>Status</th>
                <th style="text-align: right;">Aksi</th> 
            </tr>
        </thead>
        <tbody>
            @forelse($allUsers as $user)
                <tr>
                    <td style="font-weight: 600; color: #111;">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span style="font-weight: 500; color: #4b5563;">{{ ucfirst($user->role) }}</span></td>
                    <td>
                        @if($user->is_verified)
                            <span class="badge badge-green">Terverifikasi</span>
                        @else
                            <span class="badge badge-red">Ditolak</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}?');" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-link text-red" style="background: transparent; border: none; cursor: pointer; padding: 0; margin-right: 0;">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #6b7280;">Belum ada akun terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 15px; display: flex; justify-content: flex-end;">
        {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>

<!-- 3. Form Tambah Akun Langsung -->
<div id="form-tambah" class="card-ui">
    <div class="card-title" style="margin-bottom: 5px;">Tambah akun langsung</div>
    <p style="color: #6b7280; font-size: 13px; margin-bottom: 20px;">Daftarkan akun sivitas akademika secara manual ke database sistem.</p>
    
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap..." required>
            </div>
            <div class="form-group">
                <label class="form-label">Email Kampus</label>
                <input type="email" name="email" class="form-control" placeholder="sivitas@student.ac.id" required>
            </div>
            <div class="form-group">
                <label class="form-label">Peran</label>
                <select name="role" class="form-control" required>
                    <option value="pengguna">Pengguna</option>
                    <option value="petugas">Petugas</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>
        
        <div class="form-group" style="margin-top: 10px;">
            <label class="form-label">Password Sementara</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password untuk pengguna baru..." required>
        </div>

        <div style="text-align: right; margin-top: 20px;">
            <button type="submit" class="btn-primary">Tambah akun</button>
        </div>
    </form>
</div>
@endsection