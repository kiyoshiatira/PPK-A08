<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - RUANG KAMPUS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; display: flex; min-height: 100vh; flex-direction: column; }
        
        /* Layout Utama */
        .main-layout { display: flex; flex: 1; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: #ffffff; border-right: 1px solid #e2e8f0; display: flex; flex-direction: column; }
        .sidebar-brand { padding: 24px; font-weight: bold; font-size: 18px; color: #1d4ed8; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f1f5f9; }
        .sidebar-menu { list-style: none; padding: 16px; display: flex; flex-direction: column; gap: 4px; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #64748b; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 14px; transition: 0.2s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #eff6ff; color: #1d4ed8; }

        /* Konten */
        .content-container { flex: 1; display: flex; flex-direction: column; }
        .top-navbar { height: 70px; background: #ffffff; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; padding: 0 32px; }
        .top-navbar .brand-title { font-weight: 600; font-size: 15px; color: #0f172a; }
        .user-profile { display: flex; align-items: center; gap: 12px; }
        .avatar { width: 36px; height: 36px; background: #2563eb; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; }
        .logout-btn { background: none; border: none; color: #ef4444; font-weight: 600; font-size: 14px; cursor: pointer; margin-left: 12px; }

        .dashboard-content { padding: 32px; flex: 1; }
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
        .page-title { font-size: 24px; font-weight: bold; color: #0f172a; margin-bottom: 4px; }
        .page-subtitle { color: #64748b; font-size: 14px; }
        
        .status-badge { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px; }
        .status-dot { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; }

        /* Grid Statistik (4 Kolom) */
        .stat-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
        .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .stat-label { color: #64748b; font-size: 13px; font-weight: 500; margin-bottom: 8px; }
        .stat-value { color: #1d4ed8; font-size: 32px; font-weight: bold; margin-bottom: 6px; line-height: 1; }
        .stat-desc { color: #64748b; font-size: 12px; }

        /* Grid Bagian Bawah (Aktivitas & Perhatian) */
        .bottom-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
        .card-ui { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .card-title { font-size: 16px; font-weight: bold; color: #0f172a; margin-bottom: 16px; }

        /* Tabel Aktivitas */
        .activity-table { width: 100%; border-collapse: collapse; }
        .activity-table th { text-align: left; padding: 10px 0; color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
        .activity-table td { padding: 14px 0; font-size: 14px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .activity-table tr:last-child td { border-bottom: none; }

        /* List Perlu Perhatian */
        .attention-list { list-style: none; display: flex; flex-direction: column; gap: 14px; }
        .attention-item { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #334155; }
        .dot-yellow { width: 8px; height: 8px; background: #eab308; border-radius: 50%; }
        .dot-red { width: 8px; height: 8px; background: #ef4444; border-radius: 50%; }

        /* Footer */
        footer { background: #0f172a; color: #94a3b8; padding: 32px; font-size: 13px; border-top: 1px solid #1e293b; }
        .footer-content { display: flex; justify-content: space-between; max-width: 1200px; margin: 0 auto; width: 100%; }
        .footer-links { display: flex; gap: 40px; }
        .footer-links div { display: flex; flex-direction: column; gap: 8px; }
        .footer-links span { color: #f8fafc; font-weight: bold; font-size: 14px; margin-bottom: 4px; }
        .footer-links a { color: #94a3b8; text-decoration: none; }
        .footer-links a:hover { color: #ffffff; }
        .footer-bottom { text-align: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid #1e293b; font-size: 12px; }
    </style>
</head>
<body>

    <div class="main-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <span>RUANG KAMPUS</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a></li>
                <li><a href="{{ route('admin.users.create') ?? '#' }}">Akun Petugas</a></li>
                <li><a href="{{ route('admin.facilities.index') }}">Fasilitas</a></li>
                <li><a href="{{ route('admin.rekap.index') }}">Rekap & Export</a></li>
            </ul>
        </div>

        <!-- Konten Utama -->
        <div class="content-container">
            <!-- Navbar Atas -->
            <div class="top-navbar">
                <div class="brand-title">RUANG KAMPUS &bull; Dashboard Admin</div>
                <div class="user-profile">
                    <span>Administrator</span>
                    <div class="avatar">AD</div>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            </div>

            <!-- Isi Halaman Dashboard -->
            <div class="dashboard-content">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Dashboard admin</h1>
                        <p class="page-subtitle">Overview umum aktivitas dan operasional RUANG KAMPUS.</p>
                    </div>
                </div>

                <!-- 4 Kotak Statistik Atas -->
                <div class="stat-grid-4">
                    <div class="stat-card">
                        <div class="stat-label">Jumlah user</div>
                        <div class="stat-value">{{ number_format($totalUsers, 0, ',', '.') }}</div>
                        <div class="stat-desc">+{{ $usersBulanIni }} bulan ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Fasilitas</div>
                        <div class="stat-value">{{ $totalFasilitas }}</div>
                        <div class="stat-desc">{{ $fasilitasAktif }} aktif</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Reservasi</div>
                        <div class="stat-value">{{ number_format($totalReservasi, 0, ',', '.') }}</div>
                        <div class="stat-desc">{{ $reservasiMingguIni }} minggu ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Laporan</div>
                        <div class="stat-value">{{ number_format($totalLaporan, 0, ',', '.') }}</div>
                        <div class="stat-desc">{{ $laporanPending }} belum ditangani</div>
                    </div>
                </div>

                <!-- Grid Bagian Bawah -->
                <div class="bottom-grid">
                    <!-- Aktivitas Terbaru -->
                    <div class="card-ui">
                        <div class="card-title">Aktivitas terbaru</div>
                        <table class="activity-table">
                            <thead>
                                <tr>
                                    <th>Aktivitas</th>
                                    <th style="text-align: right;">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $activity)
                                    <tr>
                                        <td>{{ is_array($activity) ? $activity['aktivitas'] : $activity->deskripsi }}</td>
                                        <td style="text-align: right; color: #64748b;">{{ is_array($activity) ? $activity['waktu'] : $activity->waktu }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" style="text-align: center; color: #64748b; padding: 20px 0;">Belum ada aktivitas terbaru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Perlu Perhatian -->
                    <div class="card-ui">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <div class="card-title" style="margin-bottom: 0;">Perlu perhatian</div>
                            <span style="background: #fee2e2; color: #ef4444; padding: 2px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;">Tindakan</span>
                        </div>
                        <ul class="attention-list">
                            <li class="attention-item">
                                <div class="dot-yellow"></div>
                                <span>{{ $usersBulanIni ?? 0 }} akun menunggu verifikasi</span>
                            </li>
                            <li class="attention-item">
                                <div class="dot-red"></div>
                                <span>{{ $fasilitasPerbaikan }} fasilitas dalam perbaikan</span>
                            </li>
                            <li class="attention-item">
                                <div class="dot-yellow"></div>
                                <span>{{ $laporanPending }} laporan belum ditangani</span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <footer>
                <div class="footer-content">
                    <div>
                        <strong style="color: #ffffff; font-size: 15px; display: block; margin-bottom: 6px;">RUANG KAMPUS</strong>
                        <p style="color: #94a3b8; max-width: 300px; line-height: 1.5;">Sistem Informasi Manajemen Reservasi Ruang & Fasilitas Universitas secara Terpusat.</p>
                    </div>
                    <div class="footer-links">
                        <div>
                            <span>Kontrol Utama</span>
                            <a href="{{ route('admin.facilities.index') }}">Kelola Fasilitas</a>
                            <a href="{{ route('admin.rekap.index') }}">Persetujuan Akun</a>
                        </div>
                        <div>
                            <span>Laporan & Data</span>
                            <a href="{{ route('admin.rekap.index') }}">Rekapitulasi Bulanan</a>
                            <a href="{{ route('admin.dashboard') }}">Log Audit Sistem</a>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    &copy; 2026 RUANG KAMPUS. Portal Administrator Utama. Seluruh hak dilindungi.
                </div>
            </footer>
        </div>
    </div>

</body>
</html>