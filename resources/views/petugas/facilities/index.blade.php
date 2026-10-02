@extends('layouts.petugas')

@section('content')
<style>
    .page-title { font-size: 24px; font-weight: 700; margin-bottom: 5px; color: #111; }
    .page-subtitle { color: #666; font-size: 14px; margin-bottom: 25px; }

    .card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 30px; overflow: hidden; }
    .card-header { padding: 18px 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: #fafafa; flex-wrap: wrap; gap: 10px; }
    .card-title { font-size: 15px; font-weight: 700; margin: 0; color: #222; }

    .data-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
    .data-table th { background: #f5f5f5; padding: 12px 18px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e0e0; }
    .data-table td { padding: 14px 18px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    .data-table tr:last-child td { border-bottom: none; }

    .status-badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .status-aktif          { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .status-dalamperbaikan { background: #fff3e0; color: #e65100; border: 1px solid #ffe0b2; }
    .status-nonaktif       { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }

    .filter-tabs { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
    .filter-tab { padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 500; text-decoration: none; color: #666; background: #fff; border: 1px solid #e0e0e0; }
    .filter-tab.active { background: #1565c0; color: #fff; border-color: #1565c0; font-weight: 600; }

    .select-status { padding: 5px 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 12px; font-family: inherit; background: #fff; }
    .btn-update { background: #1565c0; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-update:hover { background: #0d47a1; }

    .search-box { display: flex; gap: 8px; }
    .search-input { padding: 6px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; width: 220px; }
</style>

<div>
    <h1 class="page-title">Ubah Status Fasilitas</h1>
    <p class="page-subtitle">Kelola status operasional fasilitas kampus (Aktif, Dalam Perbaikan, Nonaktif). Fasilitas "Dalam Perbaikan" atau "Nonaktif" tidak dapat dipesan pengguna.</p>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div style="background:#e8f5e9; color:#2e7d32; padding:12px 16px; border-radius:6px; margin-bottom:20px; font-size:13px; border:1px solid #c8e6c9;">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div style="background:#e3f2fd; color:#1565c0; padding:12px 16px; border-radius:6px; margin-bottom:20px; font-size:13px; border:1px solid #bbdefb;">
            ℹ {{ session('info') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#ffebee; color:#c62828; padding:12px 16px; border-radius:6px; margin-bottom:20px; font-size:13px; border:1px solid #ffcdd2;">
            ✕ {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#ffebee; color:#c62828; padding:12px 16px; border-radius:6px; margin-bottom:20px; font-size:13px; border:1px solid #ffcdd2;">
            <strong>Gagal:</strong>
            <ul style="margin:5px 0 0 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filter & Search --}}
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom:20px;">
        <div class="filter-tabs" style="margin-bottom:0;">
            <a href="{{ route('petugas.facilities.index') }}" class="filter-tab {{ !$statusFilter ? 'active' : '' }}">Semua</a>
            <a href="{{ route('petugas.facilities.index', ['status' => 'Aktif']) }}" class="filter-tab {{ $statusFilter === 'Aktif' ? 'active' : '' }}">Aktif</a>
            <a href="{{ route('petugas.facilities.index', ['status' => 'Dalam Perbaikan']) }}" class="filter-tab {{ $statusFilter === 'Dalam Perbaikan' ? 'active' : '' }}">Dalam Perbaikan</a>
            <a href="{{ route('petugas.facilities.index', ['status' => 'Nonaktif']) }}" class="filter-tab {{ $statusFilter === 'Nonaktif' ? 'active' : '' }}">Nonaktif</a>
        </div>

        <form action="{{ route('petugas.facilities.index') }}" method="GET" class="search-box">
            @if($statusFilter)
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif
            <input type="text" name="search" class="search-input" placeholder="Cari nama / lokasi..." value="{{ $search }}">
            <button type="submit" class="btn-update" style="background:#424242;">Cari</button>
        </form>
    </div>

    {{-- Tabel Fasilitas --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Fasilitas Kampus</h3>
            <span style="font-size:12px; color:#666;">Total: {{ $facilities->total() }} Fasilitas</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:25%;">Fasilitas</th>
                    <th style="width:15%;">Tipe</th>
                    <th style="width:20%;">Lokasi</th>
                    <th style="width:10%;">Kapasitas</th>
                    <th style="width:12%;">Status Saat Ini</th>
                    <th style="width:18%; text-align:right;">Ubah Status (SRS-12)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facilities as $facility)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:12px;">
                                @if($facility->photo)
                                    <img src="{{ asset('storage/' . $facility->photo) }}" alt="{{ $facility->name }}"
                                         style="width:40px; height:40px; border-radius:6px; object-fit:cover; border:1px solid #e0e0e0;">
                                @else
                                    <div style="width:40px; height:40px; border-radius:6px; background:#e0e0e0; display:flex; align-items:center; justify-content:center; color:#777; font-size:16px;">
                                        🏛️
                                    </div>
                                @endif
                                <div>
                                    <span style="font-weight:600; color:#111; display:block;">{{ $facility->name }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $facility->type }}</td>
                        <td>{{ $facility->location }}</td>
                        <td>{{ $facility->capacity }} Orang</td>
                        <td>
                            @if($facility->status === 'Aktif')
                                <span class="status-badge status-aktif">✓ Aktif</span>
                            @elseif($facility->status === 'Dalam Perbaikan')
                                <span class="status-badge status-dalamperbaikan">🛠️ Dalam Perbaikan</span>
                            @else
                                <span class="status-badge status-nonaktif">🚫 Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <form action="{{ route('petugas.facilities.updateStatus', $facility->id) }}" method="POST" style="margin:0; display:inline-flex; gap:6px; align-items:center;">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="select-status">
                                    <option value="Aktif" {{ $facility->status === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Dalam Perbaikan" {{ $facility->status === 'Dalam Perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                                    <option value="Nonaktif" {{ $facility->status === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                <button type="submit" class="btn-update" onclick="return confirm('Ubah status fasilitas {{ addslashes($facility->name) }}?')">
                                    Simpan
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:40px; color:#888;">
                            Tidak ada fasilitas ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($facilities->hasPages())
            <div style="padding:15px 20px; border-top:1px solid #f0f0f0;">
                {{ $facilities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
