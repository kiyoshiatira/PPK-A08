<!-- Sesuaikan layout ini dengan desain Figma kelompok Anda -->
@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Daftar Akun Pengguna Baru</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        
        <!-- Validasi Client-side: required -->
        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap">
        </div>

        <!-- Validasi Client-side: type="email" dan required -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required placeholder="email@institusi.ac.id">
        </div>

        <!-- Validasi Client-side: minlength -->
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required minlength="6" placeholder="Minimal 6 karakter">
        </div>

        <div class="form-group">
            <label for="role">Role Pengguna</label>
            <select name="role" id="role" class="form-control" required>
                <option value="">-- Pilih Role --</option>
                <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                <option value="staf" {{ old('role') == 'staf' ? 'selected' : '' }}>Staf</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Daftarkan Pengguna</button>
    </form>
</div>
@endsection