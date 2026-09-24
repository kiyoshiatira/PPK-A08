<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Petugas - Ruang Kampus</title>
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
    
        .logo-box { width: 22px; height: 22px; background-color: #1565c0; border-radius: 4px; }
        .brand-text { font-weight: 700; font-size: 15px; letter-spacing: 0.5px; }
        .badge-role { background: #e3f2fd; color: #1565c0; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase; }

        /* Menu Navigasi */
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
            border-bottom: 2px solid #1565c0 !important; 
        }

        .user-nav-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-logout {
            background: none;
            border: 1px solid #d4d4d8;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            color: #52525b;
            font-weight: 500;
        }
        .btn-logout:hover { background: #f4f4f5; color: #111; }

        /* Konten Utama */
        .main-content {
            flex: 1;
            padding: 40px;
            max-width: 1100px;
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
            <span class="badge-role">Petugas</span>
        </div>
        <nav class="nav-menu">
            <a href="{{ route('petugas.reservations.index') }}" class="{{ request()->routeIs('petugas.reservations.*') ? 'active' : '' }}">Antrean Reservasi</a>
            <a href="{{ route('petugas.reports.index') }}" class="{{ request()->routeIs('petugas.reports.*') ? 'active' : '' }}">Laporan Kerusakan</a>
        </nav>
        <div class="user-nav-area">
            <span style="font-size: 13px; font-weight: 600;">{{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="btn-logout">Keluar</button>
            </form>
        </div>
    </header>

    <!-- Area Konten Dinamis -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div>RUANG KAMPUS · Panel Petugas Operasional</div>
        <div>
            <a href="#">Panduan</a> · <a href="#">Bantuan</a> · © 2026
        </div>
    </footer>

</body>
</html>
