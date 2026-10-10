<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Petugas - Ruang Kampus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reset & Base */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        body { background-color: #f5f5f5; color: #171717; min-height: 100vh; display: flex; flex-direction: column; }

        /* Top Navbar */
        .top-navbar {
            background: #0a0a0a;
            color: #ffffff;
            height: 56px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid #262626;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 14.5px;
            letter-spacing: 0.5px;
            color: #ffffff;
            text-decoration: none;
        }

        .logo-circle-outer {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .logo-circle-inner {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #3b82f6;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-user-name {
            font-size: 13.5px;
            color: #d4d4d4;
            font-weight: 500;
        }

        .nav-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #3b82f6;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Main Container */
        .app-wrapper { display: flex; width: 100%; flex: 1; min-height: calc(100vh - 56px); }

        /* Sidebar Kiri */
        .sidebar {
            width: 250px;
            background: #ffffff;
            border-right: 1px solid #e5e5e5;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: sticky;
            top: 56px;
            height: calc(100vh - 56px);
        }

        .sidebar-menu {
            list-style: none;
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: #64748b;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 13.5px;
            transition: all 0.15s ease;
        }

        .sidebar-menu a:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .sidebar-menu a.active {
            background-color: #e2e8f0;
            color: #2563eb;
            font-weight: 600;
        }

        .sidebar-menu .menu-icon {
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Sidebar Footer (User Info & Logout) */
        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fafafa;
        }

        .user-meta {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-badge {
            font-size: 11px;
            color: #2563eb;
            font-weight: 500;
        }

        .btn-logout {
            background: transparent;
            border: none;
            color: #ef4444;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: 0.15s;
        }

        .btn-logout:hover {
            background: #fee2e2;
        }

        /* Content Area */
        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow-y: auto;
        }

        .main-content {
            padding: 32px 48px;
            max-width: 1560px;
            width: 100%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    @php
        $initials = auth()->check()
            ? collect(explode(' ', trim(auth()->user()->name)))
                ->filter()->take(2)
                ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                ->implode('')
            : 'P';
    @endphp

    <!-- Top Navbar -->
    <header class="top-navbar">
        <a href="{{ route('petugas.dashboard') }}" class="nav-brand">
            <span class="logo-circle-outer">
                <span class="logo-circle-inner"></span>
            </span>
            <span>RUANG KAMPUS</span>
        </a>
        <div class="nav-user">
            <span class="nav-user-name">{{ Auth::user()->name }}</span>
            <span class="nav-user-avatar">{{ $initials }}</span>
        </div>
    </header>

    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('petugas.dashboard') }}" class="{{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                        </span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('petugas.reservations.index') }}" class="{{ request()->routeIs('petugas.reservations.*') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        </span>
                        <span>Antrean Reservasi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('petugas.reports.index') }}" class="{{ request()->routeIs('petugas.reports.*') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-4"/><path d="M12 10h.01"/></svg>
                        </span>
                        <span>Laporan Kerusakan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('petugas.facilities.index') }}" class="{{ request()->routeIs('petugas.facilities.*') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </span>
                        <span>Status Fasilitas</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div class="user-meta">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <span class="user-badge">Petugas Operasional</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-logout" title="Keluar dari sistem">Keluar</button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="content-area">
            <main class="main-content">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
