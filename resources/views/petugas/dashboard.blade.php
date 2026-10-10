@extends('layouts.petugas')

@section('content')
<style>
    .page-title {
        font-size: 26px;
        font-weight: 700;
        color: #171717;
        letter-spacing: -0.5px;
        margin-bottom: 4px;
    }
    .page-subtitle {
        color: #525252;
        font-size: 14px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .realtime-date {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #404040;
        font-weight: 500;
        background: #e5e5e5;
        padding: 5px 12px;
        border-radius: 8px;
        border: 1px solid #d4d4d4;
        font-size: 13px;
        margin-left: auto;
    }

    /* Stat Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 28px;
    }
    .stat-card {
        background: #e5e5e5;
        border: 1px solid #d4d4d4;
        border-radius: 12px;
        padding: 24px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
    .stat-label {
        font-size: 14px;
        color: #171717;
        font-weight: 500;
        margin-bottom: 10px;
    }
    .stat-number {
        font-size: 38px;
        font-weight: 700;
        color: #1d4ed8;
        line-height: 1;
        margin-bottom: 10px;
    }
    .stat-desc {
        font-size: 13.5px;
        color: #525252;
    }

    /* Two Column Grid */
    .two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    /* Section Card */
    .section-card {
        background: #ffffff;
        border: 1px solid #e5e5e5;
        border-radius: 16px;
        overflow: hidden;
    }
    .section-header {
        padding: 20px 24px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f0f0f0;
    }
    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #171717;
    }
    .count-badge {
        padding: 3px 10px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
    }
    .count-pending {
        background: #fef9c3;
        color: #854d0e;
    }
    .count-report {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Queue Item Row */
    .queue-item {
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f5f5f5;
        gap: 12px;
        transition: background 0.1s;
    }
    .queue-item:last-child {
        border-bottom: none;
    }
    .queue-item:hover {
        background: #fafafa;
    }
    .queue-name {
        font-weight: 600;
        font-size: 14px;
        color: #171717;
        margin-bottom: 3px;
    }
    .queue-meta {
        font-size: 13px;
        color: #525252;
    }
    .btn-tinjau {
        background: #ffffff;
        border: 1.5px solid #171717;
        color: #171717;
        padding: 7px 18px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.15s ease;
        flex-shrink: 0;
    }
    .btn-tinjau:hover {
        background: #171717;
        color: #ffffff;
    }
    .empty-state {
        padding: 36px 24px;
        text-align: center;
        color: #737373;
        font-size: 13.5px;
    }
</style>

<div>
    <!-- Header -->
    <h1 class="page-title">Dashboard Petugas</h1>
    <p class="page-subtitle">
        <span>Ringkasan aktivitas dan antrean operasional kampus.</span>
        <span class="realtime-date" id="liveDateContainer">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            <span id="currentLiveDate">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
        </span>
    </p>

    <!-- Stat Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Reservasi pending</div>
            <div class="stat-number">{{ $pendingCount }}</div>
            <div class="stat-desc">Perlu ditinjau segera</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Laporan baru</div>
            <div class="stat-number">{{ $newReportCount }}</div>
            <div class="stat-desc">Kerusakan belum ditangani</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Ditangani hari ini</div>
            <div class="stat-number">{{ $todayHandled }}</div>
            <div class="stat-desc">Laporan &amp; reservasi tuntas</div>
        </div>
    </div>

    <!-- Two Column Quick View -->
    <div class="two-col">
        <!-- Reservasi Pending -->
        <div class="section-card">
            <div class="section-header">
                <span class="section-title">Reservasi pending</span>
                <span class="count-badge count-pending">{{ $pendingCount }} antrean</span>
            </div>
            @forelse($pendingReservations as $res)
                <div class="queue-item">
                    <div>
                        <div class="queue-name">{{ $res->facility->name ?? '-' }}</div>
                        <div class="queue-meta">
                            {{ $res->user->name ?? '-' }} &middot;
                            {{ \Carbon\Carbon::parse($res->reservation_date)->translatedFormat('d M') }} &middot;
                            {{ substr($res->start_time, 0, 5) }}–{{ substr($res->end_time, 0, 5) }}
                        </div>
                    </div>
                    <a href="{{ route('petugas.reservations.index') }}" class="btn-tinjau">Tinjau</a>
                </div>
            @empty
                <div class="empty-state">Tidak ada reservasi pending.</div>
            @endforelse
        </div>

        <!-- Laporan Baru -->
        <div class="section-card">
            <div class="section-header">
                <span class="section-title">Laporan baru</span>
                <span class="count-badge count-report">{{ $newReportCount }} antrean</span>
            </div>
            @forelse($newReports as $report)
                <div class="queue-item">
                    <div>
                        <div class="queue-name">{{ $report->description }}</div>
                        <div class="queue-meta">
                            {{ $report->facility->name ?? '-' }} &middot;
                            {{ $report->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <a href="{{ route('petugas.reports.index') }}" class="btn-tinjau">Buka</a>
                </div>
            @empty
                <div class="empty-state">Tidak ada laporan baru.</div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function updateLiveDate() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric'
        };
        const dateStr = now.toLocaleDateString('id-ID', options);
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
        const el = document.getElementById('currentLiveDate');
        if (el) {
            el.textContent = `${dateStr} • ${timeStr} WIB`;
        }
    }

    updateLiveDate();
    setInterval(updateLiveDate, 1000);
</script>
@endsection
