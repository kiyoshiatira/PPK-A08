@extends('layouts.app')

@section('title', 'Beranda - Ruang Kampus')

@section('content')
<div class="max-w-6xl mx-auto px-8 py-10">

    <!-- Header -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-3xl font-bold">Halo, {{ \Illuminate\Support\Str::of(auth()->user()->name)->before(' ') }}</h1>
            <p class="text-neutral-600 text-sm mt-1">Ringkasan aktivitas reservasi dan laporan Anda.</p>
        </div>
        <a
            href="{{ route('facilities.index') }}"
            class="bg-blue-400 hover:bg-blue-500 text-neutral-900 border border-blue-500 rounded-lg px-5 py-2.5 text-sm font-semibold transition"
        >
            Ajukan reservasi
        </a>
    </div>

    <!-- Kartu statistik -->
    <div class="grid grid-cols-3 gap-6 mb-8">
        <div class="bg-neutral-200 border rounded-xl p-6">
            <p class="text-sm mb-3">Reservasi aktif</p>
            <p class="text-4xl font-bold text-blue-700 mb-3">{{ $activeReservationsCount }}</p>
            <p class="text-sm text-neutral-700">{{ $pendingReservationsCount }} menunggu persetujuan</p>
        </div>
        <div class="bg-neutral-200 border rounded-xl p-6">
            <p class="text-sm mb-3">Reservasi bulan ini</p>
            <p class="text-4xl font-bold text-blue-700 mb-3">{{ $thisMonthReservationsCount }}</p>
            <p class="text-sm text-neutral-700">{{ $thisMonthApprovedCount }} telah disetujui</p>
        </div>
        <div class="bg-neutral-200 border rounded-xl p-6">
            <p class="text-sm mb-3">Laporan aktif</p>
            <p class="text-4xl font-bold text-blue-700 mb-3">{{ $activeReportsCount }}</p>
            <p class="text-sm text-neutral-700">Sedang ditinjau pengelola</p>
        </div>
    </div>

    <!-- Dua panel -->
    <div class="grid grid-cols-2 gap-6 items-start">

        <!-- Reservasi aktif -->
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Reservasi aktif</h2>
                <a href="{{ route('reservations.index') }}" class="text-sm text-neutral-500 hover:text-black font-medium">Lihat semua</a>
            </div>

            <div class="space-y-3">
                @forelse($activeReservations as $reservation)
                    @php
                        $date  = \Carbon\Carbon::parse($reservation->reservation_date);
                        $start = str_replace(':', '.', substr($reservation->start_time, 0, 5));
                        $end   = str_replace(':', '.', substr($reservation->end_time, 0, 5));
                        $badge = match($reservation->status) {
                            'Approved' => 'bg-green-100 text-green-700',
                            'Pending'  => 'bg-yellow-100 text-yellow-700',
                            default    => 'bg-neutral-200 text-neutral-700',
                        };
                    @endphp
                    <div class="border rounded-md px-4 py-3 flex justify-between items-center">
                        <div>
                            <p class="font-semibold">{{ $reservation->facility->name ?? '-' }}</p>
                            <p class="text-sm text-neutral-600">{{ $date->translatedFormat('d M') }} · {{ $start }}–{{ $end }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $reservation->status }}</span>
                    </div>
                @empty
                    <p class="text-sm text-neutral-400">Belum ada reservasi aktif.</p>
                @endforelse
            </div>
        </div>

        <!-- Laporan aktif -->
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Laporan aktif</h2>
                <a href="{{ route('reports.index') }}" class="text-sm text-neutral-500 hover:text-black font-medium">Lihat semua</a>
            </div>

            @forelse($activeReports as $report)
                <div class="{{ $loop->last ? '' : 'mb-5 pb-5 border-b' }}">
                    <p class="font-semibold">{{ $report->category }}</p>
                    <p class="text-sm text-neutral-600 mb-4">
                        {{ $report->facility->name ?? '-' }} · Dikirim {{ $report->created_at->translatedFormat('d M') }}
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="bg-neutral-200 text-neutral-800 px-3 py-1 rounded-full text-xs font-semibold">
                            {{ $report->status === 'Diproses' ? 'Dalam peninjauan' : $report->status }}
                        </span>
                        <a
                            href="{{ route('reports.index') }}"
                            class="border border-neutral-900 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-neutral-50 transition"
                        >
                            Lihat laporan
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-sm text-neutral-400">Belum ada laporan aktif.</p>
            @endforelse
        </div>

    </div>
</div>
@endsection