@extends('layouts.admin')

@section('content')
<style>
    .page-title { font-size: 24px; font-weight: 700; margin-bottom: 5px; color: #111; }
    .page-subtitle { color: #666; font-size: 14px; margin-bottom: 30px; }

    .card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 30px; overflow: hidden; }
    .card-header { padding: 20px; border-bottom: 1px solid #f0f0f0; }
    .card-title { font-size: 16px; font-weight: 700; margin: 0; }

    .data-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
    .data-table th { background: #f5f5f5; padding: 12px 20px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e0e0; }
    .data-table td { padding: 16px 20px; border-bottom: 1px solid #f0f0f0; vertical-align: top; }
    .data-table tr:last-child td { border-bottom: none; }
    
    .user-name { font-weight: 600; color: #111; margin-bottom: 4px; display: block; font-size: 14px; }
    .user-email { color: #666; }
    
    .form-container { width: 100%; max-width: 450px; padding: 20px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: #111; }
    .form-control { width: 100%; padding: 10px 12px; border: 1px solid #dcdcdc; border-radius: 4px; box-sizing: border-box; font-family: inherit; font-size: 13px; }
    .form-control:focus { outline: none; border-color: #888; }
    
    .btn-dark { background: #222; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600; margin-top: 10px; }
    .btn-dark:hover { background: #000; }
</style>

<div>
    <h1 class="page-title">Kelola akun petugas</h1>
    <p class="page-subtitle">Tambah akun petugas langsung. Petugas tidak memiliki registrasi mandiri.</p>

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

    <!-- SEKSI 1: Daftar Akun Petugas -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar akun</h3>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Nama</th>
                    <th style="width: 45%;">Email</th>
                    <th style="width: 15%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($petugasList as $petugas)
                    <tr>
                        <td><span class="user-name">{{ $petugas->name }}</span></td>
                        <td><span class="user-email">{{ $petugas->email }}</span></td>
                        <td>
                            <span style="color: #2e7d32; font-weight: 500;">Aktif</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 30px; color: #888;">Belum ada akun petugas yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- SEKSI 2: Form Tambah Akun Petugas -->
    <div class="card form-container">
        <h3 class="card-title" style="margin-bottom: 20px;">Tambah akun petugas</h3>
        
        <form action="{{ route('admin.petugas.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="name">Nama lengkap</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Nama petugas" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Email kampus</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="nama@kampus.ac.id" required>
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