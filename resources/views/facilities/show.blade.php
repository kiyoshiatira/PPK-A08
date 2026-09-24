<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $facility->name }} - Ruang Kampus</title>
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
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-black">Beranda</a>
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-black">Fasilitas</a>
            @auth
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-black">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-black">Masuk</a>
            @endauth
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-8 py-10">

        <!-- Breadcrumb -->
        <p class="text-sm text-gray-500 mb-6">
            <a href="{{ route('dashboard') }}" class="hover:text-black">Beranda</a> /
            <a href="{{ route('dashboard') }}" class="hover:text-black">Fasilitas</a> /
            {{ $facility->name }}
        </p>

        @if (session('status'))
            <div class="mb-6 p-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Header -->
        <div class="grid grid-cols-2 gap-10 mb-10">
            @if($facility->photo)
                <img src="{{ asset('storage/' . $facility->photo) }}" alt="{{ $facility->name }}" class="h-72 w-full object-cover rounded-lg">
            @else
                <div class="bg-gray-200 h-72 rounded-lg w-full"></div>
            @endif

            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $facility->name }}</h1>
                <p class="text-gray-500 mb-4">{{ $facility->description }}</p>

                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="bg-gray-100 px-3 py-1 rounded-full text-sm font-medium">{{ $facility->location }}</span>
                    <span class="bg-gray-100 px-3 py-1 rounded-full text-sm font-medium">Kapasitas {{ $facility->capacity }}</span>
                    <span class="bg-gray-100 px-3 py-1 rounded-full text-sm font-medium">{{ $facility->type }}</span>
                </div>

                @guest
                    <a
                        href="{{ route('login') }}"
                        class="inline-block bg-gray-900 text-white rounded-lg px-5 py-3 text-sm font-semibold hover:bg-gray-800 transition"
                    >
                        Masuk untuk reservasi
                    </a>
                @else
                    <button
                        type="button"
                        onclick="document.getElementById('reservation-form').scrollIntoView({behavior: 'smooth'})"
                        class="inline-block bg-gray-900 text-white rounded-lg px-5 py-3 text-sm font-semibold hover:bg-gray-800 transition"
                    >
                        Ajukan reservasi
                    </button>
                @endguest
            </div>
        </div>

        <!-- Calendar -->
        <div class="bg-white border rounded-xl shadow-sm p-6 mb-10">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Kalender ketersediaan</h2>
                <div class="flex items-center gap-3 text-sm">
                    <a
                        href="{{ route('facilities.show', ['facility' => $facility->id, 'date' => $date->copy()->subDay()->toDateString()]) }}"
                        class="px-2 py-1 rounded hover:bg-gray-100"
                    >&lsaquo;</a>
                    <span class="font-medium">{{ $date->translatedFormat('d F Y') }}</span>
                    <a
                        href="{{ route('facilities.show', ['facility' => $facility->id, 'date' => $date->copy()->addDay()->toDateString()]) }}"
                        class="px-2 py-1 rounded hover:bg-gray-100"
                    >&rsaquo;</a>
                </div>
            </div>

            <div class="flex gap-3 mb-5">
                <span class="bg-white border px-3 py-1 rounded-full text-xs font-medium">Tersedia</span>
                <span class="bg-gray-100 px-3 py-1 rounded-full text-xs font-medium">Tidak tersedia</span>
            </div>

            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3">
                @foreach ($slots as $slot)
                    @if ($slot['available'] && auth()->check())
                        <button
                            type="button"
                            onclick="selectSlot('{{ $slot['time'] }}')"
                            class="border rounded-lg p-3 text-sm text-center bg-white hover:border-gray-900 hover:bg-gray-50 transition slot-btn"
                            data-time="{{ $slot['time'] }}"
                        >
                            {{ $slot['time'] }}
                        </button>
                    @else
                        <div
                            class="rounded-lg p-3 text-sm text-center {{ $slot['available'] ? 'bg-white border text-gray-800' : 'bg-gray-100 text-gray-400' }}"
                        >
                            {{ $slot['time'] }}
                        </div>
                    @endif
                @endforeach
            </div>

            @guest
                <p class="text-xs text-gray-400 mt-4">Masuk untuk memilih slot dan mengajukan reservasi.</p>
            @endguest
        </div>

        <!-- Reservation Form -->
        @auth
            <div id="reservation-form" class="bg-white border rounded-xl shadow-sm p-6 max-w-lg">
                <h2 class="text-xl font-bold mb-1">Ajukan reservasi</h2>
                <p class="text-sm text-gray-500 mb-6">Pilih slot di kalender di atas, lalu lengkapi form ini.</p>

                <form action="{{ route('reservations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="facility_id" value="{{ $facility->id }}">
                    <input type="hidden" name="reservation_date" value="{{ $date->toDateString() }}">

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Tanggal</label>
                        <input
                            type="text"
                            value="{{ $date->translatedFormat('d F Y') }}"
                            disabled
                            class="w-full border rounded-lg p-3 text-sm bg-gray-50 text-gray-500"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Jam mulai</label>
                            <input
                                type="text"
                                id="start_time"
                                name="start_time"
                                value="{{ old('start_time') }}"
                                placeholder="Pilih slot di atas"
                                readonly
                                required
                                class="w-full border rounded-lg p-3 text-sm bg-gray-50 @error('start_time') border-red-400 @enderror"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Jam selesai</label>
                            <select
                                id="end_time"
                                name="end_time"
                                required
                                class="w-full border rounded-lg p-3 text-sm bg-white @error('end_time') border-red-400 @enderror"
                            >
                                <option value="">Pilih jam mulai dulu</option>
                            </select>
                        </div>
                    </div>
                    @error('start_time')
                        <p class="text-xs text-red-600 -mt-3 mb-4">{{ $message }}</p>
                    @enderror

                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2">Tujuan penggunaan</label>
                        <textarea
                            name="purpose"
                            rows="3"
                            required
                            placeholder="Contoh: Rapat organisasi, presentasi tugas akhir, dll."
                            class="w-full border rounded-lg p-3 text-sm @error('purpose') border-red-400 @enderror"
                        >{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-gray-900 text-white rounded-lg p-3 text-sm font-semibold hover:bg-gray-800 transition"
                    >
                        Ajukan reservasi
                    </button>
                </form>
            </div>
        @endauth

    </main>

    <script>
        function selectSlot(time) {
            document.getElementById('start_time').value = time;

            const endSelect = document.getElementById('end_time');
            endSelect.innerHTML = '';

            const [h, m] = time.split(':').map(Number);
            let cursorMinutes = h * 60 + m + 30;
            const closingMinutes = 20 * 60;

            while (cursorMinutes <= closingMinutes) {
                const hh = String(Math.floor(cursorMinutes / 60)).padStart(2, '0');
                const mm = String(cursorMinutes % 60).padStart(2, '0');
                const label = `${hh}:${mm}`;
                const opt = document.createElement('option');
                opt.value = label;
                opt.textContent = label;
                endSelect.appendChild(opt);
                cursorMinutes += 30;
            }

            document.querySelectorAll('.slot-btn').forEach(btn => {
                btn.classList.remove('border-gray-900', 'bg-gray-50');
            });
            document.querySelector(`.slot-btn[data-time="${time}"]`)?.classList.add('border-gray-900', 'bg-gray-50');

            document.getElementById('reservation-form').scrollIntoView({ behavior: 'smooth' });
        }
    </script>

</body>
</html>