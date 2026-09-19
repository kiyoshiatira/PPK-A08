<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Kampus</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

    <!-- Navbar -->
    <nav class="flex items-center justify-between px-8 py-4 bg-white border-b">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-gray-900 rounded"></div>
            <span class="font-bold text-lg tracking-wide">RUANG KAMPUS</span>
        </div>
        <div class="space-x-6 text-sm font-medium">
            <a href="#" class="text-black">Beranda</a>
            <a href="#" class="text-gray-500 hover:text-black">Fasilitas</a>
            <a href="#" class="text-gray-500 hover:text-black">Masuk</a>
        </div>
    </nav>

    <!-- Header & Form Filter -->
    <main class="max-w-7xl mx-auto px-8 py-10">
        <h1 class="text-3xl font-bold mb-2">Temukan fasilitas kampus</h1>
        <p class="text-gray-500 mb-8">Cek ketersediaan ruang dan fasilitas per 30 menit, pukul 07.00–20.00.</p>

        <form action="{{ route('dashboard') }}" method="GET" class="bg-white p-6 rounded-xl border shadow-sm mb-10">
            <!-- Search -->
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Cari fasilitas</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama fasilitas..." class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900">
            </div>

            <!-- Dropdowns -->
            <div class="grid grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Tipe</label>
                    <select name="type" class="w-full border rounded-lg p-3 text-sm bg-white">
                        <option value="Semua tipe">Semua tipe</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Lokasi</label>
                    <select name="location" class="w-full border rounded-lg p-3 text-sm bg-white">
                        <option value="Semua lokasi">Semua lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>{{ $location }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Kapasitas minimum</label>
                    <input type="number" name="min_capacity" value="{{ request('min_capacity', 0) }}" class="w-full border rounded-lg p-3 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Kapasitas maksimum</label>
                    <input type="number" name="max_capacity" value="{{ request('max_capacity', 500) }}" class="w-full border rounded-lg p-3 text-sm bg-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2 text-transparent">Aksi</label>
                    <button type="submit" class="w-full bg-gray-900 text-white rounded-lg p-3 text-sm font-semibold hover:bg-gray-800 transition">
                        Terapkan
                    </button>
                </div>
            </div>
        </form>

        <!-- Results Header -->
        <div class="flex justify-between items-end mb-6">
            <h2 class="text-xl font-bold">{{ $facilities->total() }} fasilitas</h2>
            <p class="text-sm text-gray-500">Hari ini, {{ $today }}</p>
        </div>

        <!-- Facility Grid -->
        <div class="grid grid-cols-3 gap-6">
            @foreach($facilities as $facility)
            <div class="bg-white border rounded-xl p-4 hover:shadow-md transition">
                <!-- Image Placeholder -->
                <div class="bg-gray-200 h-48 rounded-lg mb-4 w-full"></div>
                
                <h3 class="font-bold text-lg">{{ $facility->name }}</h3>
                <p class="text-sm text-gray-500 mb-6">{{ $facility->location }} · {{ $facility->capacity }} orang</p>
                
                <div class="flex justify-between items-center text-sm">
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full font-medium">
                        Tersedia {{ $facility->available_slots }} slot
                    </span>
                    <span class="text-gray-500">07.00 — 20.00</span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $facilities->links() }}
        </div>
    </main>

</body>
</html>