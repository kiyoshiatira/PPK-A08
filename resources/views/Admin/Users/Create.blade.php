@extends('layouts.admin')

@section('content')
<style>
    .page-title { font-size: 24px; font-weight: 700; margin-bottom: 5px; color: #111; }
    .page-subtitle { color: #666; font-size: 14px; margin-bottom: 30px; }

    .card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 30px; overflow: hidden; }
    .card-header { padding: 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-size: 16px; font-weight: 700; margin: 0; }

    .data-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
    .data-table th { background: #f5f5f5; padding: 12px 20px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e0e0; }
    .data-table td { padding: 16px 20px; border-bottom: 1px solid #f0f0f0; vertical-align: top; }
    .data-table tr:last-child td { border-bottom: none; }
    
    .user-name { font-weight: 600; color: #111; margin-bottom: 4px; display: block; font-size: 14px; }
    .user-email { color: #666; }
    .text-gray { color: #666; }
    
    .search-filter-form { display: flex; gap: 10px; margin-bottom: 20px; width: 100%; flex-wrap: wrap; }
    .search-input { flex: 1; min-width: 250px; padding: 10px 12px; border: 1px solid #dcdcdc; border-radius: 4px; font-size: 13px; font-family: inherit; background: #fff; }
    .filter-select { padding: 10px 12px; border: 1px solid #dcdcdc; border-radius: 4px; font-size: 13px; font-family: inherit; background: #fff; }
    .btn-search { background: #222; color: #fff; border: none; padding: 0 20px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600; }
    .btn-search:hover { background: #000; }
    .btn-reset { padding: 10px 15px; background: #e0e0e0; color: #333; border-radius: 4px; text-decoration: none; font-size: 13px; display: flex; align-items: center; }
    
    .role-badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .role-admin { background: #ffebee; color: #c62828; }
    .role-petugas { background: #e3f2fd; color: #1565c0; }
    .role-pengguna { background: #e8f5e9; color: #2e7d32; }

    .form-container { width: 100%; max-width: 450px; padding: 20px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: #111; }
    .form-control { width: 100%; padding: 10px 12px; border: 1px solid #dcdcdc; border-radius: 4px; box-sizing: border-box; font-family: inherit; font-size: 13px; }
    .btn-dark { background: #222; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600; margin-top: 10px; }
    .btn-delete { background: none; border: none; color: #c62828; cursor: pointer; font-size: 13px; padding: 0; }
    .btn-delete:hover { text-decoration: underline; }
</style>

<div>
    <h1 class="page-title">Kelola Akun Pengguna</h1>
    <p class="page-subtitle">Verifikasi pendaftaran mandiri, kelola seluruh data akun (Pengguna, Petugas, Admin), serta tambah akun baru.</p>

    <!-- Notifikasi -->
    @if(session('success'))
        <div style="background: #e6f6e6; color: #2e7d32; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; font-size: 13px; border: 1px solid #c8e6c9;">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: #fdecea; color: #c62828; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; font-size: 13px; border: 1px solid #f8bbd0;">
            @foreach($errors->all() as $error)
                <div>- {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- SEKSI 1: Tabel Menunggu Verifikasi (Selalu Ditampilkan) -->
    <div class="card" style="margin-bottom: 30px;">
        <div class="card-header">
            <h3 class="card-title">
                Menunggu verifikasi 
                <span style="background: #eee; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 5px;">{{ $pendingUsers->count() }} permintaan</span>
            </h3>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Nama</th>
                    <th style="width: 35%;">Email</th>
                    <th style="width: 20%;">Tanggal daftar</th>
                    <th style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingUsers as $user)
                    <tr>
                        <td><span class="user-name">{{ $user->name }}</span></td>
                        <td><span class="user-email">{{ $user->email }}</span></td>
                        <td><span class="text-gray">{{ $user->created_at->format('d M Y') }}</span></td>
                        <td>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <!-- Tombol Verifikasi -->
                                <form action="{{ route('admin.users.verify', $user->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" style="background: none; border: none; color: #2e7d32; cursor: pointer; font-size: 13px; padding: 0;" onclick="return confirm('Verifikasi akun {{ $user->name }}?')">Verifikasi</button>
                                </form>
                                <span style="color: #666;">·</span>
                                <!-- Tombol Tolak -->
                                <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #c62828; cursor: pointer; font-size: 13px; padding: 0;" onclick="return confirm('Tolak pendaftaran {{ $user->name }}?')">Tolak</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px; color: #888;">Tidak ada pendaftaran akun yang menunggu verifikasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- SEKSI 2: Form Pencarian & Filter Role -->
    <form action="{{ route('admin.users.create') }}" method="GET" class="search-filter-form">
        <input type="text" name="search" class="search-input" placeholder="Cari berdasarkan nama atau email..." value="{{ request('search') }}">
        
        <select name="role" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua Peran (Role)</option>
            <option value="pengguna" {{ request('role') == 'pengguna' ? 'selected' : '' }}>Pengguna (Mahasiswa/Dosen/Staf)</option>
            <option value="petugas" {{ request('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>

        <button type="submit" class="btn-search">Filter</button>
        
        @if(request('search') || request('role'))
            <a href="{{ route('admin.users.create') }}" class="btn-reset">Reset</a>
        @endif
    </form>

    <!-- SEKSI 3: Daftar Semua Akun Terverifikasi -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Akun Sistem</h3>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Nama</th>
                    <th style="width: 30%;">Email</th>
                    <th style="width: 20%;">Peran (Role)</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allUsers as $user)
                    <tr>
                        <td><span class="user-name">{{ $user->name }}</span></td>
                        <td><span class="user-email">{{ $user->email }}</span></td>
                        <td>
                            @if($user->role == 'admin')
                                <span class="role-badge role-admin">Admin</span>
                            @elseif($user->role == 'petugas')
                                <span class="role-badge role-petugas">Petugas</span>
                            @else
                                <span class="role-badge role-pengguna">Pengguna</span>
                            @endif
                        </td>
                        <td><span style="color: #2e7d32; font-weight: 500;">Aktif</span></td>
                        <td>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" onclick="return confirm('Yakin ingin menghapus akun {{ $user->name }}?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: #888;">Tidak ada data akun yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- SEKSI 4: Form Tambah Akun Langsung -->
    <div class="card form-container">
        <h3 class="card-title" style="margin-bottom: 20px;">Tambah akun baru</h3>
        
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="name">Nama lengkap</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Nama lengkap" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="email@kampus.ac.id" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="role">Peran (Role)</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="" disabled selected>Pilih peran</option>
                    <option value="pengguna">Pengguna (Mahasiswa/Dosen/Staf)</option>
                    <option value="petugas">Petugas</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password sementara</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="********" required minlength="6">
            </div>

            <button type="submit" class="btn-dark">Buat akun</button>
        </form>
    </div>
</div>
@endsection