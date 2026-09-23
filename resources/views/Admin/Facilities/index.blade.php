@extends('layouts.admin')

@section('content')
<style>
    .page-title { font-size: 24px; font-weight: 700; margin-bottom: 5px; color: #111; }
    .page-subtitle { color: #666; font-size: 14px; margin-bottom: 20px; }
    .btn-dark { background: #222; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-block; }
    .btn-dark:hover { background: #000; }
    .btn-outline { background: #fff; color: #111; border: 1px solid #dcdcdc; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: none; }
    .btn-outline:hover { background: #f9f9f9; }

    /* Table Styles matching the Figma image */
    .facility-list { border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; margin-top: 20px; margin-bottom: 40px; background: #fff; }
    .facility-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
    .facility-table th { background: #f0f0f0; padding: 16px 20px; font-weight: 700; color: #111; border-bottom: 1px solid #e0e0e0; }
    .facility-table td { padding: 16px 20px; border-bottom: 1px solid #e0e0e0; color: #555; vertical-align: middle; cursor: pointer; }
    .facility-table tr:hover td { background-color: #fafafa; }
    .facility-table tr:last-child td { border-bottom: none; }
    .fac-name { font-weight: 600; color: #111; }

    /* Form Styles */
    .form-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 30px; margin-bottom: 50px; }
    .form-title { font-size: 20px; font-weight: 700; margin-bottom: 25px; margin-top: 0; color: #111; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #111; }
    .form-control { width: 100%; padding: 12px 15px; border: 1px solid #dcdcdc; border-radius: 4px; font-size: 14px; font-family: inherit; box-sizing: border-box; }
    textarea.form-control { resize: vertical; min-height: 100px; }
    
    .file-drop-area { border: 1px solid #dcdcdc; background: #f0f0f0; border-radius: 4px; padding: 40px; text-align: center; color: #888; font-size: 13px; }
    .form-actions { display: flex; flex-direction: column; gap: 10px; margin-top: 30px; align-items: flex-start; }
</style>

<div>
    <h1 class="page-title">Kelola data fasilitas</h1>
    <p class="page-subtitle">Tambah, ubah, dan hapus data fasilitas kampus.</p>
    
    <a href="{{ route('admin.facilities.index') }}#form-section" class="btn-dark">Tambah fasilitas</a>

    @if(session('success'))
        <div style="background: #e8f5e9; color: #2e7d32; padding: 12px 15px; border-radius: 4px; margin-top: 20px; font-size: 13px; border: 1px solid #c8e6c9;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fdecea; color: #c62828; padding: 12px 15px; border-radius: 4px; margin-top: 20px; font-size: 13px; border: 1px solid #f8bbd0;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tabel Daftar Fasilitas -->
    <div class="facility-list">
        <table class="facility-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Nama</th>
                    <th style="width: 20%;">Tipe</th>
                    <th style="width: 30%;">Lokasi</th>
                    <th style="width: 10%;">Kapasitas</th>
                    <th style="width: 15%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facilities as $fac)
                    <!-- Baris bisa diklik untuk edit -->
                    <tr onclick="window.location='{{ route('admin.facilities.index', ['edit' => $fac->id]) }}#form-section'">
                        <td><span class="fac-name">{{ $fac->name }}</span></td>
                        <td>{{ $fac->type }}</td>
                        <td>{{ $fac->location }}</td>
                        <td>{{ $fac->capacity }}</td>
                        <td>{{ $fac->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px;">Belum ada data fasilitas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Area Form Tambah / Ubah -->
    <div id="form-section" class="form-card">
        <h2 class="form-title">{{ $editFacility ? 'Ubah fasilitas' : 'Tambah fasilitas' }}</h2>
        
        <form action="{{ $editFacility ? route('admin.facilities.update', $editFacility->id) : route('admin.facilities.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($editFacility) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label" for="name">Nama</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $editFacility->name ?? '') }}" placeholder="Contoh: Ruang Seminar A" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="type">Tipe</label>
                <input type="text" id="type" name="type" class="form-control" value="{{ old('type', $editFacility->type ?? '') }}" placeholder="Contoh: Ruang seminar" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="location">Lokasi</label>
                <input type="text" id="location" name="location" class="form-control" value="{{ old('location', $editFacility->location ?? '') }}" placeholder="Contoh: Gedung Rektorat, Lantai 2" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="capacity">Kapasitas</label>
                <input type="number" id="capacity" name="capacity" class="form-control" value="{{ old('capacity', $editFacility->capacity ?? '') }}" placeholder="80" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-control" placeholder="Ruang presentasi serbaguna...">{{ old('description', $editFacility->description ?? '') }}</textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="Aktif" {{ old('status', $editFacility->status ?? '') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Dalam Perbaikan" {{ old('status', $editFacility->status ?? '') == 'Dalam Perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                    <option value="Nonaktif" {{ old('status', $editFacility->status ?? '') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="photo">Foto</label>
                @if($editFacility && $editFacility->photo)
                    <div style="margin-bottom: 10px;">
                        <img src="{{ asset('storage/' . $editFacility->photo) }}" alt="Foto Fasilitas" style="max-height: 150px; border-radius: 4px;">
                    </div>
                @endif
                <div class="file-drop-area">
                    <input type="file" id="photo" name="photo" style="width: 100%;">
                    <p style="margin-top: 10px; margin-bottom: 0;">Pilih file foto (Maks 2MB, .jpg, .png)</p>
                </div>
            </div>

            <div class="form-actions">
                @if($editFacility)
                    <!-- Tombol hapus dipisah form-nya menggunakan form.submit() agar UI tetap rapi -->
                    <button type="button" class="btn-outline" onclick="if(confirm('Hapus fasilitas ini?')) { document.getElementById('delete-form-{{ $editFacility->id }}').submit(); }">Hapus fasilitas</button>
                @endif
                <button type="submit" class="btn-dark">Simpan perubahan</button>
            </div>
        </form>
        
        <!-- Hidden Delete Form (Hanya muncul saat mode edit) -->
        @if($editFacility)
            <form id="delete-form-{{ $editFacility->id }}" action="{{ route('admin.facilities.destroy', $editFacility->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</div>
@endsection