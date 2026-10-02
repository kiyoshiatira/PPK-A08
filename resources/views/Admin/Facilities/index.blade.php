@extends('layouts.admin')

@section('content')
<div class="page-header-flex">
    <div>
        <h1 class="page-title" style="font-size: 24px; font-weight: 700; margin-bottom: 6px;">Kelola data fasilitas</h1>
        <p class="page-subtitle" style="color: #6b7280; font-size: 14px;">Tambah, ubah, dan hapus data fasilitas kampus.</p>
    </div>
    <a href="{{ route('admin.facilities.index') }}#form-fasilitas" class="btn-primary">Tambah fasilitas</a>
</div>

<!-- Filter & Search Bar Section -->
    <div style="background: #ffffff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
        <form action="{{ route('admin.facilities.index') }}" method="GET" style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
            
            <!-- Search Bar -->
            <div style="flex: 1; min-width: 240px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px;">Cari Fasilitas</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama atau lokasi..." style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
            </div>

            <!-- Filter Status -->
            <div style="width: 200px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px;">Filter Status</label>
                <select name="status" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: #fff;">
                    <option value="all">Semua Status</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Dalam Perbaikan" {{ request('status') == 'Dalam Perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                    <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div style="display: flex; gap: 8px; align-items: flex-end; margin-top: 22px;">
                <button type="submit" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer;">Cari</button>
                <a href="{{ route('admin.facilities.index') }}" style="padding: 10px 16px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-block;">Reset</a>
            </div>

        </form>
    </div>

<!-- Tabel Data Fasilitas -->
<div class="card-ui">
    <table class="table-ui">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Tipe</th>
                <th>Lokasi</th>
                <th>Kapasitas</th>
                <th>Status</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($facilities as $facility)
                <tr>
                    <td style="font-weight: 600; color: #111;">{{ $facility->name }}</td>
                    <td>{{ $facility->type }}</td>
                    <td>{{ $facility->location }}</td>
                    <td>{{ $facility->capacity }}</td>
                    <td>
                        @if($facility->status == 'Aktif')
                            <span class="badge badge-green">Aktif</span>
                        @elseif($facility->status == 'Dalam Perbaikan')
                            <span class="badge badge-yellow">Dalam Perbaikan</span>
                        @else
                            <span class="badge badge-gray">{{ $facility->status }}</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <!-- Tautan Ubah disesuaikan dengan logika controller Anda -->
                        <a href="{{ route('admin.facilities.index', ['edit' => $facility->id]) }}#form-fasilitas" class="action-link text-blue">Ubah</a>
                        
                        <form action="{{ route('admin.facilities.destroy', $facility->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus fasilitas {{ $facility->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-link text-red" style="background: transparent; border: none; cursor: pointer; padding: 0; margin-right: 0;">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #6b7280;">Belum ada data fasilitas terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Pagination Links -->
    <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
        {{ $facilities->links('pagination::bootstrap-4') }}
    </div>
</div>

<!-- Form Dinamis: Tambah / Ubah Fasilitas -->
<div id="form-fasilitas" class="card-ui">
    <div class="card-title" style="margin-bottom: 5px;">
        {{ $editFacility ? 'Ubah fasilitas' : 'Tambah fasilitas baru' }}
    </div>
    <p style="color: #6b7280; font-size: 13px; margin-bottom: 24px;">
        {{ $editFacility ? 'Formulir edit data fasilitas terpilih.' : 'Formulir penambahan data fasilitas.' }} Data akan langsung diaplikasikan pada kalender publik.
    </p>
    
    <!-- Action dinamis mengarah ke store (jika tambah) atau update (jika ubah) -->
    <form action="{{ $editFacility ? route('admin.facilities.update', $editFacility->id) : route('admin.facilities.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($editFacility)
            @method('PUT')
        @endif
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nama Fasilitas</label>
                <input type="text" name="name" class="form-control" value="{{ $editFacility->name ?? old('name') }}" placeholder="Contoh: Ruang Seminar A" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tipe Fasilitas</label>
                <input type="text" name="type" class="form-control" value="{{ $editFacility->type ?? old('type') }}" placeholder="Contoh: Ruang seminar" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Lokasi</label>
                <input type="text" name="location" class="form-control" value="{{ $editFacility->location ?? old('location') }}" placeholder="Contoh: Gedung Rektorat, Lantai 2" required>
            </div>
            <div class="form-group">
                <label class="form-label">Kapasitas</label>
                <input type="number" name="capacity" class="form-control" value="{{ $editFacility->capacity ?? old('capacity') }}" placeholder="Masukkan angka kapasitas" required>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Status Operasional</label>
            <select name="status" class="form-control" required>
                <option value="Aktif" {{ ($editFacility && $editFacility->status == 'Aktif') ? 'selected' : '' }}>Aktif</option>
                <option value="Dalam Perbaikan" {{ ($editFacility && $editFacility->status == 'Dalam Perbaikan') ? 'selected' : '' }}>Dalam Perbaikan</option>
                <option value="Nonaktif" {{ ($editFacility && $editFacility->status == 'Nonaktif') ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Deskripsi Lengkap</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Masukkan deskripsi fasilitas secara detail..." required>{{ $editFacility->description ?? old('description') }}</textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Unggah Foto Fasilitas (Bila ada)</label>
            @if($editFacility && $editFacility->photo)
                <div style="margin-bottom: 10px; font-size: 13px; color: #4b5563;">
                    <i>Foto saat ini sudah tersimpan. Unggah file baru untuk menggantinya.</i>
                </div>
            @endif
            <input type="file" name="photo" class="form-control" accept="image/*" style="padding: 8px; border: 1px dashed #d1d5db; background: #f9fafb;">
            <small style="color: #6b7280; font-size: 12px; margin-top: 4px; display: block;">Format gambar: JPG, PNG (Maks. 2MB)</small>
        </div>
        
        <div style="display: flex; justify-content: flex-end; margin-top: 30px; gap: 10px;">
            @if($editFacility)
                <!-- Tombol batal akan mengembalikan ke halaman bersih tanpa parameter edit -->
                <a href="{{ route('admin.facilities.index') }}" class="btn-outline-danger" style="text-decoration: none;">Batal</a>
            @endif
            <button type="submit" class="btn-primary">{{ $editFacility ? 'Simpan perubahan' : 'Tambah fasilitas' }}</button>
        </div>
    </form>
</div>
@endsection