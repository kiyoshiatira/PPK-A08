<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Ruang Kampus</title>
    <style>
        /* Reset & Base */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', 'Segoe UI', sans-serif; }
        body { background-color: #f4f4f5; color: #111; display: flex; flex-direction: column; min-height: 100vh; }

        /* Top Header */
        .top-header {
            background-color: #fff;
            border-bottom: 1px solid #e4e4e7;
            padding: 0 40px;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            height: 70px;
            width: 100%;
        }

        .brand-container { 
            display: flex !important; 
            flex-direction: row !important;
            align-items: center !important; 
            gap: 12px; 
        }
    
        .logo-box { width: 22px; height: 22px; background-color: #111; border-radius: 4px; }
        .brand-text { font-weight: 700; font-size: 15px; letter-spacing: 0.5px; }

        /* Menu Navigasi Horizontal Sejajar ke Kanan */
        .nav-menu { 
            display: flex !important; 
            flex-direction: row !important;
            align-items: center !important;
            gap: 24px !important; 
            height: 100% !important;
            list-style: none;
        }

        .nav-menu a {
            text-decoration: none !important;
            color: #52525b;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            height: 100%;
            border: none !important;
            border-bottom: 2px solid transparent !important;
            outline: none !important;
            background: transparent !important;
            padding: 0 2px;
            white-space: nowrap;
        }

        .nav-menu a:hover, .nav-menu a.active { 
            color: #111 !important; 
            border-bottom: 2px solid #111 !important; 
        }

        /* Konten Utama */
        .main-content {
            flex: 1;
            padding: 40px;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }

        /* Footer */
        .footer {
            background-color: #27272a;
            color: #a1a1aa;
            padding: 25px 40px;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .footer a { color: #a1a1aa; text-decoration: none; margin: 0 5px; }
        .footer a:hover { color: #fff; }
    </style>
</head>
<body>

    <!-- Header & Navigasi -->
    <header class="top-header">
        <div class="brand-container">
            <div class="logo-box"></div>
            <div class="brand-text">RUANG KAMPUS</div>
        </div>
        <nav class="nav-menu">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard admin</a>
            <a href="#">Akun Petugas</a>
            <a href="{{ route('admin.users.create') }}">Akun Pengguna</a> 
            <a href="#">Fasilitas</a>
            <a href="#">Reservasi</a>
            <a href="#">Laporan</a>
            <a href="#">Rekap</a>
        </nav>
    </header>

    <!-- Area Konten Dinamis -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div>RUANG KAMPUS</div>
        <div>
            <a href="#">Panduan</a> · <a href="#">Kebijakan</a> · <a href="#">Bantuan</a> · © 2026
        </div>
    </footer>

</body>
</html>