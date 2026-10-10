@extends('layouts.app')

@section('title', 'Riwayat Reservasi - Ruang Kampus')

@section('content')
@php
    $periods = [
        'upcoming'   => 'Akan datang',
        'this_month' => 'Bulan ini',
        'past'       => 'Sudah lewat',
    ];
@endphp

<div class="max-w-6xl mx-auto px-8 py-10">
    <h1 class="text-3xl font-bold">Riwayat reservasi saya</h1>
    <p class="text-neutral-500 text-sm mt-1 mb-6">Pantau status dan kelola seluruh pengajuan reservasi Anda.</p>

    @if(session('success'))
        <div class="mb-6 p-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Filter -->
    <form method="GET" action="{{ route('reservations.index') }}" class="bg-white rounded-2xl border p-4 mb-6 flex gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari fasilitas..."
                class="w-full bg-neutral-100 rounded-lg py-2.5 pl-9 pr-3 text-sm focus:outline-none focus:ring-1 focus:ring-neutral-900"
            >
        </div>

        <select name="status" onchange="this.form.submit()" class="bg-neutral-100 rounded-lg px-3 py-2.5 text-sm">
            <option value="">Semua Status</option>
            @foreach(['Pending', 'Approved', 'Rejected', 'Cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
            @endforeach
        </select>

        <select name="period" onchange="this.form.submit()" class="bg-neutral-100 rounded-lg px-3 py-2.5 text-sm">
            <option value="">Semua Periode</option>
            @foreach($periods as $value => $label)
                <option value="{{ $value }}" @selected(request('period') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    <!-- Tabel -->
    <div class="bg-white rounded-2xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 border-b">
                <tr class="text-left text-xs font-bold uppercase tracking-wide">
                    <th class="px-5 py-3">Fasilitas</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Waktu</th>
                    <th class="px-5 py-3">Tujuan penggunaan</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $reservation)
                    @php
                        $date  = \Carbon\Carbon::parse($reservation->reservation_date);
                        $start = str_replace(':', '.', substr($reservation->start_time, 0, 5));
                        $end   = str_replace(':', '.', substr($reservation->end_time, 0, 5));
                        $badge = match($reservation->status) {
                            'Approved'  => 'bg-green-100 text-green-700',
                            'Pending'   => 'bg-yellow-100 text-yellow-700',
                            'Rejected'  => 'bg-red-100 text-red-700',
                            default     => 'bg-neutral-200 text-neutral-700',
                        };
                    @endphp
                    <tr class="border-b last:border-b-0">
                        <td class="px-5 py-4 font-semibold">{{ $reservation->facility->name ?? '-' }}</td>
                        <td class="px-5 py-4 whitespace-nowrap">{{ $date->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-4 whitespace-nowrap">{{ $start }} - {{ $end }}</td>
                        <td class="px-5 py-4 text-neutral-600">
                            <div class="max-w-[14rem] truncate" title="{{ $reservation->purpose }}">{{ $reservation->purpose }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $badge }}">{{ $reservation->status }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if($reservation->canBeCancelled())
                                <form
                                    action="{{ route('reservations.cancel', $reservation) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Yakin mau membatalkan reservasi ini?')"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-red-600 font-semibold hover:underline">Batalkan</button>
                                </form>
                            @else
                                <span class="text-neutral-300">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-neutral-400">
                            {{ request()->hasAny(['search', 'status', 'period']) ? 'Tidak ada reservasi yang cocok dengan filter.' : 'Belum ada riwayat reservasi.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($reservations->total() > 0)
            @php
                $btn = 'w-8 h-8 flex items-center justify-center rounded-md border text-xs';
                $current = $reservations->currentPage();
                $range = $reservations->getUrlRange(max(1, $current - 2), min($reservations->lastPage(), $current + 2));
            @endphp
            <div class="flex items-center justify-between px-5 py-4 border-t text-sm text-neutral-500">
                <span>Menampilkan {{ $reservations->firstItem() }}-{{ $reservations->lastItem() }} dari {{ $reservations->total() }} data</span>

                <div class="flex gap-2">
                    @if($reservations->onFirstPage())
                        <span class="{{ $btn }} text-neutral-300">&lt;</span>
                    @else
                        <a href="{{ $reservations->previousPageUrl() }}" class="{{ $btn }} hover:bg-neutral-50">&lt;</a>
                    @endif

                    @foreach($range as $page => $url)
                        <a href="{{ $url }}" class="{{ $btn }} {{ $page === $current ? 'bg-blue-500 border-blue-500 text-white' : 'hover:bg-neutral-50' }}">{{ $page }}</a>
                    @endforeach

                    @if($reservations->hasMorePages())
                        <a href="{{ $reservations->nextPageUrl() }}" class="{{ $btn }} hover:bg-neutral-50">&gt;</a>
                    @else
                        <span class="{{ $btn }} text-neutral-300">&gt;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@endsection