@extends('layouts.app')

@section('title', 'Fasilitas - Ruang Kampus')

@section('content')
@php
    $icon = '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>';
    $selectClass = 'bg-neutral-200/70 rounded-lg px-4 py-2.5 text-sm';
    $pageBtn = 'h-8 min-w-8 px-2 flex items-center justify-center rounded-md border text-xs';

    $steps = [
        [
            'title' => 'Cari fasilitas', 'no' => 1, 'headline' => 'Lihat slot yang kosong',
            'desc' => 'Filter berdasarkan tipe, lokasi, dan kapasitas.',
            'points' => ['Kalender per 30 menit', 'Status fasilitas selalu terbaru', 'Tanpa perlu masuk'],
            'label' => 'Lihat fasilitas', 'href' => '#daftar',
        ],
        [
            'title' => 'Ajukan', 'no' => 2, 'headline' => 'Isi tujuan dan jam',
            'desc' => 'Sistem memeriksa bentrok jadwal secara otomatis.',
            'points' => ['Jam mulai dan selesai', 'Tujuan penggunaan', 'Persetujuan tata tertib'],
            'label' => auth()->check() ? 'Pilih fasilitas' : 'Masuk untuk mengajukan',
            'href' => auth()->check() ? '#daftar' : route('login'),
        ],
        [
            'title' => 'Pantau', 'no' => 3, 'headline' => 'Tunggu persetujuan',
            'desc' => 'Petugas meninjau pengajuan. Anda melihat statusnya di riwayat.',
            'points' => ['Pending, Approved, Rejected', 'Batalkan sebelum mulai', 'Laporkan kerusakan ruang'],
            'label' => 'Lihat riwayat',
            'href' => auth()->check() ? route('reservations.index') : route('login'),
        ],
    ];
@endphp

<div class="max-w-6xl mx-auto px-8 pt-8">

    <!-- Hero: fasilitas per lokasi -->
    @if($buildings->count())
        <div id="hero" class="relative h-56 rounded-2xl overflow-hidden text-white bg-gradient-to-r from-neutral-800 via-neutral-500 to-slate-300">
            @foreach($buildings as $b)
                <div class="hero-slide absolute inset-0 {{ $loop->first ? '' : 'hidden' }}">
                    @if($b->photo)
                        <img src="{{ asset('storage/' . $b->photo) }}" alt="{{ $b->location }}" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/10"></div>
                    @else
                        <div class="absolute right-1/4 top-1/2 -translate-y-1/2 text-neutral-600 flex flex-col items-center gap-1 text-sm">
                            <svg class="w-14 h-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                            Foto gedung
                        </div>
                    @endif
                    <div class="relative h-full px-14 flex flex-col justify-center items-start">
                        <span class="bg-white text-neutral-900 text-[11px] font-semibold px-3 py-1 rounded-full mb-3">Fasilitas kampus</span>
                        <h2 class="text-2xl font-bold">{{ $b->location }}</h2>
                        <p class="text-xs text-neutral-200 mb-4">{{ $b->total }} fasilitas</p>
                        <a href="{{ route('facilities.index', ['location' => $b->location]) }}#daftar" class="bg-blue-400 hover:bg-blue-500 text-neutral-900 border border-blue-500 rounded-lg px-4 py-1.5 text-xs font-semibold transition">Lihat fasilitas</a>
                    </div>
                </div>
            @endforeach

            @if($buildings->count() > 1)
                <button type="button" id="hero-prev" class="absolute left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-white text-neutral-900 text-sm leading-none" aria-label="Sebelumnya">&lsaquo;</button>
                <button type="button" id="hero-next" class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-white text-neutral-900 text-sm leading-none" aria-label="Berikutnya">&rsaquo;</button>

                <div class="absolute bottom-3 right-4 flex items-center gap-2">
                    <button type="button" id="hero-toggle" class="w-5 h-5 rounded-full bg-neutral-900/60 text-[9px]" aria-label="Putar/jeda">❚❚</button>
                    @foreach($buildings as $b)
                        <button type="button" class="hero-dot h-1.5 rounded-full" aria-label="Slide {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- Daftar fasilitas -->
    <section id="daftar" class="pt-12 scroll-mt-4">
        <div class="text-center mb-8">
            <p class="text-xs font-semibold text-blue-600 mb-2">Fasilitas kami</p>
            <h1 class="text-3xl font-bold leading-tight">Temukan ruang yang tepat untuk<br>kegiatan Anda</h1>
            <p class="text-xs text-neutral-500 mt-3">Cek ketersediaan per 30 menit, pukul 07.00–20.00.@guest Masuk untuk mengajukan reservasi.@endguest</p>
        </div>

        <form method="GET" action="{{ route('facilities.index') }}#daftar" class="flex gap-3 mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama fasilitas" class="flex-1 {{ $selectClass }} focus:outline-none focus:ring-1 focus:ring-neutral-900">

            <select name="type" onchange="this.form.submit()" class="{{ $selectClass }}">
                <option value="">Semua tipe</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                @endforeach
            </select>

            <select name="location" onchange="this.form.submit()" class="{{ $selectClass }}">
                <option value="">Semua lokasi</option>
                @foreach($locations as $location)
                    <option value="{{ $location }}" @selected(request('location') === $location)>{{ $location }}</option>
                @endforeach
            </select>

            <select name="capacity" onchange="this.form.submit()" class="{{ $selectClass }}">
                <option value="">Semua kapasitas</option>
                @foreach($capacityOptions as $value => $label)
                    <option value="{{ $value }}" @selected(request('capacity') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="sort" onchange="this.form.submit()" class="{{ $selectClass }}">
                @foreach($sortOptions as $value => $label)
                    <option value="{{ $value }}" @selected(request('sort', 'name_asc') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </form>

        <p class="text-center text-xs text-neutral-500 mb-6">
            @if($facilities->total() > 0)
                Menampilkan {{ $facilities->firstItem() }}–{{ $facilities->lastItem() }} dari {{ $facilities->total() }} fasilitas · Hari ini, {{ $today }}
            @else
                Tidak ada fasilitas yang cocok dengan filter.
            @endif
        </p>

        <div class="grid grid-cols-3 gap-6">
            @foreach($facilities as $facility)
                <a href="{{ route('facilities.show', $facility) }}" class="block bg-neutral-200/70 border border-neutral-200 rounded-2xl p-3 hover:shadow-md transition">
                    @if($facility->photo)
                        <img src="{{ asset('storage/' . $facility->photo) }}" alt="{{ $facility->name }}" class="h-40 w-full object-cover rounded-xl mb-4">
                    @else
                        <div class="h-40 w-full rounded-xl bg-slate-200 flex flex-col items-center justify-center gap-1 text-xs text-neutral-700 mb-4">
                            {!! $icon !!}
                            Foto fasilitas
                        </div>
                    @endif

                    <h3 class="font-bold px-1">{{ $facility->name }}</h3>
                    <p class="text-xs text-neutral-600 px-1 mb-4">{{ $facility->location }}, kapasitas {{ $facility->capacity }} orang</p>

                    <div class="flex justify-between items-center px-1 pb-1">
                        <span class="bg-blue-400 text-neutral-900 px-3 py-1 rounded-full text-xs font-semibold">Tersedia {{ $facility->available_slots }} slot</span>
                        <span class="text-xs text-neutral-700">07.00–20.00</span>
                    </div>
                </a>
            @endforeach
        </div>

        @if($facilities->hasPages())
            @php
                $current = $facilities->currentPage();
                $range = $facilities->getUrlRange(max(1, $current - 2), min($facilities->lastPage(), $current + 2));
            @endphp
            <div class="flex justify-center items-center gap-2 mt-8">
                @if($facilities->onFirstPage())
                    <span class="{{ $pageBtn }} text-neutral-300">‹ Sebelumnya</span>
                @else
                    <a href="{{ $facilities->previousPageUrl() }}" class="{{ $pageBtn }} bg-white hover:bg-neutral-50">‹ Sebelumnya</a>
                @endif

                @foreach($range as $page => $url)
                    <a href="{{ $url }}" class="{{ $pageBtn }} {{ $page === $current ? 'bg-blue-400 border-blue-500 font-semibold' : 'bg-white hover:bg-neutral-50' }}">{{ $page }}</a>
                @endforeach

                @if($facilities->hasMorePages())
                    <a href="{{ $facilities->nextPageUrl() }}" class="{{ $pageBtn }} bg-white hover:bg-neutral-50">Berikutnya ›</a>
                @else
                    <span class="{{ $pageBtn }} text-neutral-300">Berikutnya ›</span>
                @endif
            </div>
        @endif
    </section>

    <!-- Cara reservasi -->
    <section class="pt-16">
        <div class="text-center mb-8">
            <p class="text-xs font-semibold text-blue-600 mb-2">Cara reservasi</p>
            <h2 class="text-3xl font-bold leading-tight">Reservasi ruang jadi sederhana dan<br>cepat</h2>
        </div>

        <div class="grid grid-cols-3 gap-6">
            @foreach($steps as $step)
                <div class="bg-white border rounded-2xl p-6 flex flex-col">
                    <p class="font-bold mb-1">{{ $step['title'] }}</p>
                    <p class="text-4xl font-bold text-blue-700 mb-2">{{ $step['no'] }}</p>
                    <p class="font-semibold text-sm mb-1">{{ $step['headline'] }}</p>
                    <p class="text-xs text-neutral-600 mb-4">{{ $step['desc'] }}</p>
                    <ul class="space-y-1.5 text-xs text-neutral-700 mb-5">
                        @foreach($step['points'] as $point)
                            <li>✓ {{ $point }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ $step['href'] }}" class="mt-auto self-start bg-blue-400 hover:bg-blue-500 text-neutral-900 border border-blue-500 rounded-lg px-4 py-2 text-xs font-semibold transition">{{ $step['label'] }}</a>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    let current = 0;
    let timer = null;

    function show(i) {
        current = (i + slides.length) % slides.length;
        slides.forEach(function (s, n) { s.classList.toggle('hidden', n !== current); });
        dots.forEach(function (d, n) {
            d.className = 'hero-dot h-1.5 rounded-full ' + (n === current ? 'w-5 bg-white' : 'w-1.5 bg-white/50');
        });
    }

    function play() {
        timer = setInterval(function () { show(current + 1); }, 5000);
        document.getElementById('hero-toggle').textContent = '❚❚';
    }

    function pause() {
        clearInterval(timer);
        timer = null;
        document.getElementById('hero-toggle').textContent = '▶';
    }

    if (slides.length > 1) {
        show(0);
        play();
        document.getElementById('hero-prev').addEventListener('click', function () { show(current - 1); });
        document.getElementById('hero-next').addEventListener('click', function () { show(current + 1); });
        document.getElementById('hero-toggle').addEventListener('click', function () { timer ? pause() : play(); });
        dots.forEach(function (d, n) { d.addEventListener('click', function () { show(n); }); });
    }
</script>
@endpush