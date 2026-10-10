@extends('layouts.admin')

@section('content')
<!-- CSS Khusus untuk halaman Dashboard -->
<style>
    .stat-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
    .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
    .stat-label { color: #64748b; font-size: 13px; font-weight: 500; margin-bottom: 8px; }
    .stat-value { color: #1d4ed8; font-size: 32px; font-weight: bold; margin-bottom: 6px; line-height: 1; }
    .stat-desc { color: #64748b; font-size: 12px; }
    .bottom-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
    .attention-list { list-style: none; display: flex; flex-direction: column; gap: 14px; }
    .attention-item { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #374151; }
    .dot-yellow { width: 8px; height: 8px; background: #eab308; border-radius: 50%; }
    .dot-red { width: 8px; height: 8px; background: #ef4444; border-radius: 50%; }
</style>

<div class="page-header-flex">
    <div>
        <h1 class="page-title" style="font-size: 24px; font-weight: 700; margin-bottom: 6px;">Dashboard admin</h1>
        <p class="page-subtitle" style="color: #6b7280; font-size: 14px;">Overview umum aktivitas dan operasional RUANG KAMPUS.</p>
    </div>
</div>

<!-- 4 Kotak Statistik Atas -->
<div class="stat-grid-4">
    <div class="stat-card">
        <div class="stat-label">Jumlah user</div>
        <div class="stat-value">{{ number_format($totalUsers ?? 0, 0, ',', '.') }}</div>
        <div class="stat-desc">+{{ $usersBulanIni ?? 0 }} bulan ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Fasilitas</div>
        <div class="stat-value">{{ $totalFasilitas ?? 0 }}</div>
        <div class="stat-desc">{{ $fasilitasAktif ?? 0 }} aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Reservasi</div>
        <div class="stat-value">{{ number_format($totalReservasi ?? 0, 0, ',', '.') }}</div>
        <div class="stat-desc">{{ $reservasiMingguIni ?? 0 }} minggu ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Laporan</div>
        <div class="stat-value">{{ number_format($totalLaporan ?? 0, 0, ',', '.') }}</div>
        <div class="stat-desc">{{ $laporanPending ?? 0 }} belum ditangani</div>
    </div>
</div>

<!-- Grid Bagian Bawah -->
<div class="bottom-grid">
    <!-- Aktivitas Terbaru -->
    <div class="card-ui">
        <div class="card-title">Aktivitas terbaru</div>
        <table class="table-ui">
            <thead>
                <tr>
                    <th>Aktivitas</th>
                    <th style="text-align: right;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentActivities as $activity)
                    <tr>
                        <td style="font-weight: 500;">{{ is_array($activity) ? $activity['aktivitas'] : $activity->deskripsi }}</td>
                        <td style="text-align: right; color: #6b7280;">{{ is_array($activity) ? $activity['waktu'] : $activity->waktu }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #6b7280; padding: 20px 0;">Belum ada aktivitas terbaru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Perlu Perhatian -->
    <div class="card-ui">
        @php
            $jumlahAkunPending = \App\Models\User::where('is_verified', false)->count(); 
                                    
            $jumlahFasilitasRusak = \App\Models\Facility::where('status', 'Dalam Perbaikan')->count();
            
            $jumlahLaporan = \App\Models\Report::where('status', 'Baru')->count(); 
            
            $totalPerhatian = $jumlahAkunPending + $jumlahFasilitasRusak + $jumlahLaporan;
        @endphp

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div class="card-title" style="margin-bottom: 0;">Perlu perhatian</div>
            
            <!-- Badge Dinamis -->
            @if($totalPerhatian > 0)
                <span class="badge badge-red">Tindakan ({{ $totalPerhatian }})</span>
            @else
                <span class="badge badge-green">Semua Aman</span>
            @endif
        </div>
        
        <ul class="attention-list">
            @if($totalPerhatian > 0)
                @if($jumlahAkunPending > 0)
                    <li class="attention-item">
                        <div class="dot-yellow"></div>
                        <span><strong>{{ $jumlahAkunPending }}</strong> akun menunggu verifikasi</span>
                    </li>
                @endif
                
                @if($jumlahFasilitasRusak > 0)
                    <li class="attention-item">
                        <div class="dot-red"></div>
                        <span><strong>{{ $jumlahFasilitasRusak }}</strong> fasilitas dalam perbaikan</span>
                    </li>
                @endif
                
                @if($jumlahLaporan > 0)
                    <li class="attention-item">
                        <div class="dot-yellow"></div>
                        <span><strong>{{ $jumlahLaporan }}</strong> laporan belum ditangani</span>
                    </li>
                @endif
            @else
                <li class="attention-item" style="color: #16a34a; font-weight: 500; font-size: 13px;">
                    ✅ Tidak ada masalah yang memerlukan perhatian saat ini. Operasional berjalan lancar.
                </li>
            @endif
        </ul>
    </div>
</div>
@endsection