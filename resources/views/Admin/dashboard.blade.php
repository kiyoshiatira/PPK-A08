@extends('layouts.admin')

@section('content')
<style>
    .page-title { font-size: 26px; font-weight: 700; margin-bottom: 4px; color: #111; }
    .page-subtitle { color: #666; font-size: 14px; margin-bottom: 30px; }

    /* Kartu Statistik Menumpuk ke Bawah */
    .stat-list { display: flex; flex-direction: column; gap: 16px; margin-bottom: 35px; }
    .stat-card {
        background: #fff;
        border: 1px solid #e4e4e7;
        border-radius: 8px;
        padding: 24px;
    }
    .stat-label { font-size: 13px; color: #71717a; margin-bottom: 8px; }
    .stat-value { font-size: 30px; font-weight: 700; color: #111; margin-bottom: 4px; line-height: 1.1; }
    .stat-sub { font-size: 13px; color: #a1a1aa; }

    /* Kotak Seksi (Aktivitas & Perhatian) */
    .section-card {
        background: #fff;
        border: 1px solid #e4e4e7;
        border-radius: 8px;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .section-header {
        padding: 18px 24px;
        font-size: 16px;
        font-weight: 700;
        border-bottom: 1px solid #e4e4e7;
        color: #111;
    }

    /* Tabel Aktivitas */
    .activity-table { width: 100%; border-collapse: collapse; }
    .activity-table th {
        background-color: #f4f4f5;
        text-align: left;
        padding: 12px 24px;
        font-size: 13px;
        font-weight: 600;
        color: #3f3f46;
        border-bottom: 1px solid #e4e4e7;
    }
    .activity-table td {
        padding: 16px 24px;
        border-bottom: 1px solid #e4e4e7;
        font-size: 14px;
        color: #27272a;
    }
    .activity-table tr:last-child td { border-bottom: none; }
    .time-col { color: #71717a; font-size: 13px; }

    /* List Perlu Perhatian */
    .attention-box {
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        font-size: 14px;
        color: #27272a;
        font-weight: 500;
    }
</style>

<div>
    <h1 class="page-title">Dashboard admin</h1>
    <p class="page-subtitle">Overview umum aktivitas dan operasional RUANG KAMPUS.</p>

    <!-- Empat Kartu Statistik Utama -->
    <div class="stat-list">
        <div class="stat-card">
            <div class="stat-label">Jumlah user</div>
            <div class="stat-value">{{ number_format($totalUsers, 0, ',', '.') }}</div>
            <div class="stat-sub">+{{ $newUsersThisMonth }} bulan ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Fasilitas</div>
            <div class="stat-value">{{ $totalFacilities }}</div>
            <div class="stat-sub">{{ $activeFacilities }} aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Reservasi</div>
            <div class="stat-value">{{ $totalReservations }}</div>
            <div class="stat-sub">{{ $weeklyReservations }} minggu ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Laporan</div>
            <div class="stat-value">{{ $totalReports }}</div>
            <div class="stat-sub">{{ $unhandledReports }} belum ditangani</div>
        </div>
    </div>

    <!-- Tabel Aktivitas Terbaru -->
    <div class="section-card">
        <div class="section-header">Aktivitas terbaru</div>
        <table class="activity-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Aktivitas</th>
                    <th style="width: 30%;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentActivities as $activity)
                    <tr>
                        <td>{{ $activity['description'] }}</td>
                        <td class="time-col">{{ $activity['time']->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #888;">Belum ada aktivitas tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Bagian Perlu Perhatian -->
    <div class="section-card">
        <div class="section-header">Perlu perhatian</div>
        <div class="attention-box">
            <div>{{ $pendingUsersCount }} akun menunggu verifikasi</div>
            <div>{{ $facilitiesInRepair }} fasilitas dalam perbaikan</div>
            <div>{{ $unhandledReports }} laporan belum ditangani</div>
        </div>
    </div>
</div>
@endsection