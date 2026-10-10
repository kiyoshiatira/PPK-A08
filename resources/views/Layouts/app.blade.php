<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ruang Kampus')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-neutral-100 text-neutral-900 font-sans min-h-screen flex flex-col">

    @php
        $navClass = fn ($active) => $active ? 'text-blue-400' : 'text-white hover:text-blue-300';

        $initials = auth()->check()
            ? collect(explode(' ', trim(auth()->user()->name)))
                ->filter()->take(2)
                ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                ->implode('')
            : '';
    @endphp

    <!-- Navbar -->
    <header class="bg-neutral-950 text-white">
        <div class="max-w-6xl mx-auto px-8 h-14 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-bold tracking-wide">
                <span class="w-5 h-5 rounded-full bg-white flex items-center justify-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                </span>
                RUANG KAMPUS
            </a>

            <nav class="flex items-center gap-8 text-sm font-medium">
                @auth
                    <a href="{{ route('dashboard') }}" class="{{ $navClass(request()->routeIs('dashboard', 'reservations.*')) }}">Beranda</a>
                    <a href="{{ route('facilities.index') }}" class="{{ $navClass(request()->routeIs('facilities.*')) }}">Fasilitas</a>
                    <a href="{{ route('reports.create') }}" class="{{ $navClass(request()->routeIs('reports.*')) }}">Laporan</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-white hover:text-red-400">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('facilities.index') }}" class="{{ $navClass(request()->routeIs('facilities.*')) }}">Fasilitas</a>
                @endauth
            </nav>

            <div>
                @auth
                    <span title="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full bg-blue-400 text-white text-xs font-bold flex items-center justify-center">{{ $initials }}</span>
                @else
                    <div class="flex items-center gap-4 text-sm font-medium">
                        <a href="{{ route('login') }}" class="hover:text-blue-300">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-white text-neutral-900 rounded-lg px-4 py-1.5 hover:bg-neutral-200">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1 pb-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-neutral-950 text-neutral-300 text-sm">
        <div class="max-w-6xl mx-auto px-8 py-12 grid grid-cols-4 gap-8">
            <div>
                <div class="flex items-center gap-2 font-bold text-white mb-3">
                    <span class="w-5 h-5 rounded-full bg-white flex items-center justify-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    </span>
                    RUANG KAMPUS
                </div>
                <p class="text-neutral-400 text-xs leading-relaxed">Reservasi ruang dan fasilitas kampus dengan jadwal yang jelas.</p>
            </div>

            <div>
                <h4 class="text-blue-400 font-semibold mb-3">Halaman</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('dashboard') }}" class="hover:text-white">Beranda</a></li>
                    <li><a href="{{ route('facilities.index') }}" class="hover:text-white">Fasilitas</a></li>
                    @guest
                        <li><a href="{{ route('login') }}" class="hover:text-white">Masuk</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white">Daftar</a></li>
                    @endguest
                </ul>
            </div>

            <div>
                <h4 class="text-blue-400 font-semibold mb-3">Tautan cepat</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-white">Panduan</a></li>
                    <li><a href="#" class="hover:text-white">Kebijakan</a></li>
                    <li><a href="#" class="hover:text-white">Tata tertib</a></li>
                    @auth
                        <li><a href="{{ route('reservations.index') }}" class="hover:text-white">Riwayat</a></li>
                    @endauth
                </ul>
            </div>

            <div>
                <h4 class="text-blue-400 font-semibold mb-3">Kontak</h4>
                <ul class="space-y-2">
                    <li>Gedung Rektorat Lantai 1</li>
                    <li>bantuan@kampus.ac.id</li>
                    <li>07.00–20.00 setiap hari</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-neutral-800">
            <p class="max-w-6xl mx-auto px-8 py-5 text-xs text-center text-neutral-400">© 2026 RUANG KAMPUS. Seluruh hak dilindungi.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>