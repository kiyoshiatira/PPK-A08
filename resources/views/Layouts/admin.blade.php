<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Ruang Kampus</title>
    <style>
        /* Reset & Base */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', 'Segoe UI', sans-serif; }
        body { background-color: #f3f4f6; color: #111; display: flex; flex-direction: column; min-height: 100vh; }

        /* Top Header */
        .top-header {
            background-color: #111827; 
            color: #fff;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 50;
        }

        .brand-container { display: flex; align-items: center; gap: 10px; }
        .logo-box { width: 20px; height: 20px; background-color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .logo-inner { width: 10px; height: 10px; background-color: #3b82f6; border-radius: 50%; }
        .brand-text { font-weight: 700; font-size: 16px; letter-spacing: 0.5px; }
        .brand-sub { color: #60a5fa; font-weight: 500; font-size: 14px; }

        /* Profil di Header (Tanpa Logout) */
        .header-right { display: flex; align-items: center; gap: 15px; }
        .admin-profile { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 500; }
        .avatar { width: 32px; height: 32px; background-color: #60a5fa; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; }

        /* Wrapper Container */
        .app-container { display: flex; margin-top: 64px; flex: 1; }

        /* Sidebar - Diubah agar bisa menggunakan space-between */
        .sidebar {
            width: 250px;
            background-color: #ffffff; 
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            justify-content: space-between; 
            position: fixed;
            height: calc(100vh - 64px);
            overflow-y: auto;
        }

        .sidebar-menu-top { padding-top: 20px; }

        .nav-item {
            padding: 12px 30px;
            display: block;
            text-decoration: none;
            color: #4b5563;
            font-size: 14px;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: 0.2s;
        }

        .nav-item:hover { background-color: #f3f4f6; color: #111; }
        .nav-item.active { background-color: #eff6ff; color: #2563eb; border-left-color: #2563eb; }

        /* Area Profil dan Logout di Bawah Sidebar */
        .sidebar-bottom {
            padding: 15px 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .sidebar-user-info { display: flex; flex-direction: column; }
        .sidebar-user-name { font-size: 13px; font-weight: 700; color: #111; }
        .sidebar-user-role { font-size: 11px; font-weight: 500; color: #3b82f6; }
        
        .btn-keluar { 
            background: transparent; 
            border: none; 
            color: #ef4444; 
            font-size: 12px; 
            font-weight: 600;
            cursor: pointer; 
        }
        .btn-keluar:hover { text-decoration: underline; }

        /* Main Content */
        .main-content { flex: 1; padding: 40px; margin-left: 250px; }

        /* ... (CSS Footer, Table, Card) ... */
        .footer { background-color: #111827; color: #d1d5db; padding: 40px 60px 20px; font-size: 13px; margin-left: 250px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .footer-brand { display: flex; align-items: center; gap: 8px; color: #fff; font-weight: 700; font-size: 16px; margin-bottom: 15px; }
        .footer-title { color: #60a5fa; font-weight: 600; margin-bottom: 15px; font-size: 14px; }
        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-links a { color: #9ca3af; text-decoration: none; transition: 0.2s; }
        .footer-links a:hover { color: #fff; }
        .footer-bottom { border-top: 1px solid #374151; padding-top: 20px; text-align: center; font-size: 12px; color: #6b7280; }
        
        .page-header-flex { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
        .btn-primary { background-color: #1d4ed8; color: #fff; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; border: none; cursor: pointer; }
        .btn-primary:hover { background-color: #1e40af; }
        .btn-outline-danger { background-color: transparent; border: 1px solid #ef4444; color: #ef4444; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; }
        .card-ui { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .card-title { font-size: 18px; font-weight: 700; color: #111; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
        .table-ui { width: 100%; border-collapse: collapse; }
        .table-ui th { text-align: left; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; }
        .table-ui td { padding: 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; color: #374151; }
        .table-ui tr:last-child td { border-bottom: none; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-yellow { background: #fef08a; color: #854d0e; }
        .badge-green { background: #dcfce3; color: #166534; }
        .badge-red { background: #fee2e2; color: #b91c1c; }
        .badge-gray { background: #f3f4f6; color: #4b5563; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #111; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .action-link { font-weight: 600; text-decoration: none; margin-right: 12px; font-size: 13px; }
        .text-blue { color: #2563eb; }
        .text-red { color: #ef4444; }

        nav .text-muted { display: none; } 
        .pagination { display: flex; padding-left: 0; list-style: none; gap: 6px; margin: 0; justify-content: center; align-items: center; }
        .page-item .page-link { display: block; padding: 8px 14px; color: #4b5563; background-color: #fff; border: 1px solid #e5e7eb; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s; }
        .page-item.active .page-link { color: #fff; background-color: #1d4ed8; border-color: #1d4ed8; }
        .page-item.disabled .page-link { color: #9ca3af; pointer-events: none; background-color: #f9fafb; }
        .page-item .page-link:hover:not(.disabled) { background-color: #f3f4f6; color: #111; }
    </style>
</head>
<body>

    <!-- Top Header -->
    <header class="top-header">
        <div class="brand-container">
            <div class="logo-box"><div class="logo-inner"></div></div>
            <div class="brand-text">RUANG KAMPUS</div>
        </div>
        
        <div class="header-right">
            <!-- Menampilkan profil dan inisial di pojok kanan atas -->
            <div class="admin-profile">
                {{ Auth::user()->name ?? 'Administrator' }}
                <div class="avatar">{{ substr(Auth::user()->name ?? 'AD', 0, 2) }}</div>
            </div>
        </div>
    </header>

    <div class="app-container">
       <!-- Sidebar Kiri -->
        <aside class="sidebar">
            <div class="sidebar-menu-top">
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                
                <a href="{{ route('admin.users.create') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Akun</a> 
                
                <a href="{{ route('admin.facilities.index') }}" class="nav-item {{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}">Fasilitas</a>
                
                <a href="{{ route('admin.rekap.index') }}" class="nav-item {{ request()->routeIs('admin.rekap.*') ? 'active' : '' }}">Rekap & Export</a>
            </div>

            <!-- BAGIAN BARU: Profil & Tombol Logout di Dasar Sidebar -->
            <div class="sidebar-bottom">
                <div class="sidebar-user-info">
                    <span class="sidebar-user-name">{{ Auth::user()->name ?? 'admin' }}</span>
                    <span class="sidebar-user-role">Administrator Utama</span>
                </div>
                
                <!-- TOMBOL LOGOUT DIPINDAH KE SINI -->
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-keluar">Keluar</button>
                </form>
            </div>
        </aside>

        <!-- Area Konten -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <!-- ... (Isi Footer Tetap Sama) ... -->
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <div class="logo-box" style="width: 16px; height: 16px;"><div class="logo-inner" style="width: 8px; height: 8px;"></div></div>
                    RUANG KAMPUS
                </div>
                <p style="color: #9ca3af; line-height: 1.6; max-width: 300px;">Sistem Informasi Manajemen Reservasi Ruang & Fasilitas Universitas secara Terpusat.</p>
            </div>
            <div>
                <div class="footer-title">Kontrol Utama</div>
                <ul class="footer-links">
                    <li><a href="{{ route('admin.facilities.index') }}">Kelola Fasilitas</a></li>
                    <li><a href="{{ route('admin.users.create') }}">Persetujuan Akun</a></li>
                </ul>
            </div>
            <div>
                <div class="footer-title">Laporan & Data</div>
                <ul class="footer-links">
                    <li><a href="{{ route('admin.rekap.index') }}">Rekapitulasi Bulanan</a></li>
                    <li><a href="#">Log Audit Sistem</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            © 2026 RUANG KAMPUS. Portal Administrator Utama. Seluruh hak dilindungi.
        </div>
    </footer>

</body>
</html>