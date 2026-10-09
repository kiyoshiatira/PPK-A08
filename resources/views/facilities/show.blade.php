@extends('layouts.app')

@section('title', $facility->name . ' - Ruang Kampus')

@section('content')
<div class="max-w-6xl mx-auto px-8 py-8">

    <!-- Breadcrumb -->
    <p class="text-xs text-neutral-600 mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-black">Beranda</a> /
        <a href="{{ route('facilities.index') }}" class="hover:text-black">Fasilitas</a> /
        <span class="text-blue-700 font-semibold">{{ $facility->name }}</span>
    </p>

    @if (session('status'))
        <div class="mb-6 p-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <!-- Header fasilitas -->
    <div class="grid grid-cols-2 gap-8 mb-8 items-start">
        @if($facility->photo)
            <img src="{{ asset('storage/' . $facility->photo) }}" alt="{{ $facility->name }}" class="h-72 w-full object-cover rounded-2xl">
        @else
            <div class="h-72 w-full rounded-2xl bg-slate-200 flex items-center justify-center gap-2 text-sm text-neutral-800">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                Foto fasilitas
            </div>
        @endif

        <div>
            <h1 class="text-3xl font-bold mb-2">{{ $facility->name }}</h1>

            <div class="flex flex-wrap gap-2 mb-4">
                <span class="bg-neutral-200 px-3 py-1 rounded-full text-xs font-semibold">{{ $facility->location }}</span>
                <span class="bg-neutral-200 px-3 py-1 rounded-full text-xs font-semibold">Kapasitas {{ $facility->capacity }}</span>
                <span class="bg-neutral-200 px-3 py-1 rounded-full text-xs font-semibold">{{ $facility->type }}</span>
            </div>

            <p class="text-sm text-neutral-700 leading-relaxed mb-6">{{ $facility->description }}</p>

            @guest
                <a
                    href="{{ route('login') }}"
                    class="inline-block bg-blue-400 hover:bg-blue-500 text-neutral-900 border border-blue-500 rounded-lg px-5 py-2.5 text-sm font-semibold transition"
                >
                    Masuk untuk reservasi
                </a>
            @else
                <a
                    href="#reservation-form"
                    class="inline-block bg-blue-400 hover:bg-blue-500 text-neutral-900 border border-blue-500 rounded-lg px-5 py-2.5 text-sm font-semibold transition"
                >
                    Ajukan reservasi
                </a>
            @endguest
        </div>
    </div>

    <!-- Kalender ketersediaan -->
    <div class="bg-white border rounded-2xl p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Kalender ketersediaan</h2>

            <div class="flex items-center gap-4 text-sm font-semibold">
                @if($canGoPrev)
                    <a href="{{ route('facilities.show', ['facility' => $facility->id, 'date' => $date->copy()->subDay()->toDateString()]) }}" class="text-blue-700 px-1">&lsaquo;</a>
                @else
                    <span class="text-neutral-300 px-1 cursor-not-allowed">&lsaquo;</span>
                @endif

                <span>{{ $date->translatedFormat('d F Y') }}</span>

                @if($canGoNext)
                    <a href="{{ route('facilities.show', ['facility' => $facility->id, 'date' => $date->copy()->addDay()->toDateString()]) }}" class="text-blue-700 px-1">&rsaquo;</a>
                @else
                    <span class="text-neutral-300 px-1 cursor-not-allowed">&rsaquo;</span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-5 text-xs font-medium mb-1">
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500"></span>Tersedia</span>
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-neutral-400"></span>Tidak tersedia</span>
        </div>
        <p class="text-xs text-neutral-400 mb-5">Reservasi diajukan minimal 3 hari sebelum tanggal pemakaian.</p>

        <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-12 gap-3">
            @foreach($slots as $slot)
                @php $label = str_replace(':', '.', $slot['time']); @endphp

                @if($slot['available'] && auth()->check())
                    <button
                        type="button"
                        data-time="{{ $slot['time'] }}"
                        class="slot-btn border rounded-lg py-3 text-sm text-center bg-white hover:border-blue-400 hover:bg-blue-50 transition"
                    >{{ $label }}</button>
                @elseif($slot['available'])
                    <div class="border rounded-lg py-3 text-sm text-center bg-white">{{ $label }}</div>
                @else
                    <div class="rounded-lg py-3 text-sm text-center bg-neutral-200 text-neutral-500">{{ $label }}</div>
                @endif
            @endforeach
        </div>

        @guest
            <p class="text-xs text-neutral-400 mt-4">Masuk untuk memilih slot dan mengajukan reservasi.</p>
        @endguest
    </div>

    <!-- Form reservasi -->
    @auth
        <div id="reservation-form" class="bg-white border rounded-2xl p-6 max-w-lg scroll-mt-6">
            <h2 class="text-xl font-bold mb-1">Ajukan reservasi</h2>
            <p class="text-sm text-neutral-500 mb-5">Pilih slot di kalender, lalu lengkapi form ini.</p>

            @if ($errors->any())
                <div class="mb-5 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

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
                        class="w-full border rounded-lg p-3 text-sm bg-neutral-100 text-neutral-500"
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
                            class="w-full border rounded-lg p-3 text-sm bg-neutral-100 @error('start_time') border-red-400 @enderror"
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

                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">Tujuan penggunaan</label>
                    <textarea
                        name="purpose"
                        rows="3"
                        required
                        placeholder="Contoh: Rapat organisasi, presentasi tugas akhir, dll."
                        class="w-full border rounded-lg p-3 text-sm @error('purpose') border-red-400 @enderror"
                    >{{ old('purpose') }}</textarea>
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-400 hover:bg-blue-500 text-neutral-900 border border-blue-500 rounded-lg p-3 text-sm font-semibold transition"
                >
                    Ajukan reservasi
                </button>
            </form>
        </div>
    @endauth
</div>
@endsection

@push('scripts')
<script>
    function highlightSlot(time) {
        document.querySelectorAll('.slot-btn').forEach(function (btn) {
            btn.classList.remove('ring-2', 'ring-blue-400', 'bg-blue-50');
        });
        const active = document.querySelector('.slot-btn[data-time="' + time + '"]');
        if (active) active.classList.add('ring-2', 'ring-blue-400', 'bg-blue-50');
    }

    function fillEnd(time, selected) {
        const endSelect = document.getElementById('end_time');
        endSelect.innerHTML = '';

        const parts = time.split(':').map(Number);
        let cursor = parts[0] * 60 + parts[1] + 30;

        while (cursor <= 20 * 60) {
            const label = String(Math.floor(cursor / 60)).padStart(2, '0') + ':' + String(cursor % 60).padStart(2, '0');
            const opt = document.createElement('option');
            opt.value = label;
            opt.textContent = label.replace(':', '.');
            if (label === selected) opt.selected = true;
            endSelect.appendChild(opt);
            cursor += 30;
        }
    }

    function selectSlot(time) {
        document.getElementById('start_time').value = time;
        fillEnd(time, null);
        highlightSlot(time);
        document.getElementById('reservation-form').scrollIntoView({ behavior: 'smooth' });
    }

    document.querySelectorAll('.slot-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            selectSlot(btn.dataset.time);
        });
    });

    // Setelah validasi gagal: pulihkan pilihan jam & arahkan ke form
    const startInput = document.getElementById('start_time');
    if (startInput && startInput.value) {
        fillEnd(startInput.value, @json(old('end_time')));
        highlightSlot(startInput.value);
    }
    @if($errors->any())
        document.getElementById('reservation-form')?.scrollIntoView();
    @endif
</script>
@endpush