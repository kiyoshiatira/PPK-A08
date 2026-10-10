@extends('layouts.app')

@section('title', 'Riwayat Laporan Saya - Ruang Kampus')

@section('content')
<div class="max-w-6xl mx-auto px-8 pt-8 pb-16">

    <!-- Breadcrumb -->
    <div class="text-sm text-neutral-500 mb-4">
        Beranda / Riwayat / <span class="text-blue-500 font-medium">Riwayat Laporan</span>
    </div>

    <!-- Header -->
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 mb-2">Riwayat laporan saya</h1>
            <p class="text-neutral-500 text-sm">Pantau status dan catatan resolusi untuk seluruh laporan kerusakan Anda.</p>
        </div>
        
        <a href="{{ route('reports.create') }}" class="px-5 py-2 text-sm font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2 shadow-sm">
            + Buat Laporan Baru
        </a>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Container Utama -->
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
        
        <!-- Bagian Filter & Pencarian -->
        <div class="p-4 border-b border-neutral-200 bg-white flex flex-col md:flex-row gap-4">
            <form action="{{ route('reports.index') }}" method="GET" class="flex w-full gap-4">
                <!-- Input Pencarian -->
                <div class="relative flex-1">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" placeholder="Cari laporan atau fasilitas..." class="w-full pl-10 pr-4 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-colors" value="{{ request('search') }}">
                </div>
                
                <!-- Filter Status -->
                <select name="status" class="w-48 bg-white border border-neutral-200 rounded-lg text-sm px-4 py-2 focus:outline-none focus:border-blue-500 cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="Baru" {{ request('status') == 'Baru' ? 'selected' : '' }}>Baru</option>
                    <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>

                <!-- Filter Periode (Opsional) -->
                <select name="periode" class="w-48 bg-white border border-neutral-200 rounded-lg text-sm px-4 py-2 focus:outline-none focus:border-blue-500 cursor-pointer">
                    <option value="">Semua Periode</option>
                </select>

                <button type="submit" class="hidden">Cari</button>
            </form>
        </div>

        <!-- Tabel Riwayat -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-neutral-100 text-neutral-600 text-xs font-bold uppercase tracking-wider border-b border-neutral-200">
                        <th class="px-6 py-4">Laporan</th>
                        <th class="px-6 py-4">Fasilitas & Tanggal</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Catatan Resolusi Petugas</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-neutral-800 divide-y divide-neutral-200">
                    @forelse($reports as $report)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-4">
                                <!-- Kategori dan Deskripsi digabung seperti pada desain -->
                                <div class="font-bold text-neutral-900">{{ $report->category }}</div>
                                <div class="text-neutral-500 text-xs mt-1 max-w-xs truncate" title="{{ $report->description }}">{{ $report->description }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-neutral-700">{{ $report->facility->name ?? '-' }}</div>
                                <div class="text-neutral-500 text-xs mt-1">
                                    {{ \Carbon\Carbon::parse($report->created_at)->translatedFormat('j M Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    // Logika warna badge dinamis
                                    $badgeClass = 'bg-neutral-100 text-neutral-700';
                                    if($report->status == 'Baru') $badgeClass = 'bg-blue-100 text-blue-700';
                                    elseif($report->status == 'Diproses') $badgeClass = 'bg-yellow-100 text-yellow-700';
                                    elseif($report->status == 'Selesai') $badgeClass = 'bg-green-100 text-green-700';
                                    elseif($report->status == 'Ditolak') $badgeClass = 'bg-red-100 text-red-700';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badgeClass }}">
                                    {{ $report->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($report->resolution_notes)
                                    <span class="text-neutral-700">{{ $report->resolution_notes }}</span>
                                @else
                                    <span class="text-neutral-400 italic">Belum ada catatan petugas.</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-neutral-500">
                                Belum ada laporan kerusakan yang Anda buat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginasi -->
        @if($reports->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200 bg-white">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection