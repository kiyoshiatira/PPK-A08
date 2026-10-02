@extends('layouts.admin')

@section('content')
<style>
    /* Grid Filters */
    .filter-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
    
    /* Stat Cards */
    .stat-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
    .stat-box-alt { background: #f3f4f6; border-radius: 12px; padding: 24px; display: flex; flex-direction: column; gap: 8px; border: 1px solid #e5e7eb; }
    .stat-box-alt .label { font-size: 13px; color: #4b5563; font-weight: 600; }
    .stat-box-alt .value { font-size: 42px; font-weight: 700; color: #1d4ed8; line-height: 1; letter-spacing: -1px; }

    /* Middle Layout */
    .content-grid { display: grid; gap: 24px; margin-bottom: 24px; }
    .pagination { display: flex; padding-left: 0; list-style: none; gap: 6px; margin: 20px 0 0 0; }
    .pagination li a, .pagination li span { padding: 6px 12px; font-size: 13px; color: #374151; background: #fff; border: 1px solid #e5e7eb; border-radius: 4px; text-decoration: none; }
    .pagination li a:hover { background: #f3f4f6; }
    .pagination li.active span { color: #fff; background: #1d4ed8; border-color: #1d4ed8; }
    .pagination li.disabled span { color: #9ca3af; background: #f9fafb; pointer-events: none; }
    
    /* Progress Bars */
    .progress-item { margin-bottom: 24px; }
    .progress-item:last-child { margin-bottom: 0; }
    .progress-header { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #111; }
    .progress-row { display: flex; align-items: center; }
    .progress-track { background-color: #e5e7eb; border-radius: 99px; height: 16px; width: 100%; overflow: hidden; }
    .progress-fill { background-color: #60a5fa; height: 100%; border-radius: 99px; }
    .progress-value { font-weight: 700; color: #1d4ed8; font-size: 15px; margin-left: 16px; min-width: 40px; text-align: right; }

    /* Table Frekuensi */
    .freq-table { width: 100%; border-collapse: collapse; }
    .freq-table th { text-align: left; padding: 0 0 12px 0; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; }
    .freq-table td { padding: 16px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; color: #111; font-weight: 500; }
    .freq-table th:last-child, .freq-table td:last-child { text-align: right; color: #1d4ed8; font-weight: 600; }
    .freq-table tr:last-child td { border-bottom: none; padding-bottom: 0; }

    /* Export Buttons */
    .btn-outline-dark { background: #fff; border: 1px solid #374151; color: #111; padding: 8px 24px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.2s; text-decoration: none; }
    .btn-outline-dark:hover { background: #f3f4f6; }
    .export-buttons { display: flex; gap: 12px; margin-top: 16px; }
</style>

<div>
    <div class="page-header-flex" style="margin-bottom: 30px;">
        <div>
            <h1 class="page-title" style="font-size: 28px; font-weight: 700; margin-bottom: 6px;">Rekap & export</h1>
            <p class="page-subtitle" style="color: #6b7280; font-size: 14px;">Lihat okupansi fasilitas dan frekuensi kerusakan dalam satu laporan.</p>
        </div>
    </div>

    <!-- Filter Section (Rapi 3 Kolom) -->
    <form action="{{ route('admin.rekap.index') }}" method="GET" id="filterForm">
        <div class="filter-grid">
            <!-- Kolom 1: Periode -->
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Periode</label>
                <select name="periode" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    @foreach($periodeBulan as $value => $label)
                        <option value="{{ $value }}" {{ $selectedPeriode == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Kolom 2: Fasilitas -->
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Fasilitas</label>
                <select name="facility_id" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="all" {{ $selectedFacility == 'all' ? 'selected' : '' }}>Semua fasilitas</option>
                    @foreach($facilities as $fac)
                        <option value="{{ $fac->id }}" {{ $selectedFacility == $fac->id ? 'selected' : '' }}>{{ $fac->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Kolom 3: Tipe Laporan -->
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Tipe laporan</label>
                <select name="tipe_laporan" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="all" {{ $selectedTipe == 'all' ? 'selected' : '' }}>Okupansi & kerusakan</option>
                    <option value="okupansi" {{ $selectedTipe == 'okupansi' ? 'selected' : '' }}>Hanya Okupansi</option>
                    <option value="kerusakan" {{ $selectedTipe == 'kerusakan' ? 'selected' : '' }}>Hanya Kerusakan</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Stats Cards (Dinamis) -->
    <div class="stat-grid-3">
        <div class="stat-box-alt">
            <div class="label">Total peminjaman</div>
            <div class="value">{{ $totalPeminjaman }} <span style="font-size: 16px; color: #6b7280; font-weight: 600;">kali</span></div>
        </div>
        <div class="stat-box-alt">
            <div class="label">Fasilitas terpadat</div>
            <div class="value" style="font-size: {{ strlen($fasilitasTerpadat) > 15 ? '28px' : '38px' }}; line-height: 1.1;">{{ $fasilitasTerpadat }}</div>
        </div>
        <div class="stat-box-alt">
            <div class="label">Total kerusakan</div>
            <div class="value">{{ $totalKerusakan }} <span style="font-size: 16px; color: #6b7280; font-weight: 600;">laporan</span></div>
        </div>
    </div>

    <!-- Middle Content: grid-template-columns berubah menjadi 1fr 1fr (50:50) atau 1fr (Full width) -->
    <div class="content-grid" style="grid-template-columns: {{ $selectedTipe == 'all' ? '1fr 1fr' : '1fr' }};">
        
        <!-- ================= BAGIAN OKUPANSI ================= -->
        @if($selectedTipe == 'all' || $selectedTipe == 'okupansi')
        <div class="card-ui" style="margin-bottom: 0; min-height: 300px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div class="card-title">{{ $selectedFacility == 'all' ? 'Frekuensi peminjaman fasilitas' : 'Detail peminjam ruangan' }}</div>
                
                @if($selectedFacility == 'all')
                    <div class="progress-list" style="margin-top: 24px;">
                        @forelse($occupancies as $occ)
                            @php $width = min(($occ->reservations_count / $maxReservations) * 100, 100); @endphp
                            <div class="progress-item">
                                <div class="progress-header">{{ $occ->name }}</div>
                                <div class="progress-row">
                                    <div class="progress-track"><div class="progress-fill" style="width: {{ $width > 0 ? $width : 1 }}%;"></div></div>
                                    <div class="progress-value" style="font-size: 14px;">{{ $occ->reservations_count }} kali</div>
                                </div>
                            </div>
                        @empty
                            <p style="color: #6b7280; font-size: 14px; text-align: center; padding: 30px 0;">Belum ada data reservasi pada periode ini.</p>
                        @endforelse
                    </div>
                @else
                    <div style="margin-top: 16px;">
                        <table class="freq-table">
                            <thead><tr><th>Peminjam</th><th>Tanggal</th><th style="text-align: right;">Waktu</th></tr></thead>
                            <tbody>
                                @forelse($reservationDetails as $res)
                                    <tr>
                                        <td style="font-weight: 600;">{{ $res->user->name ?? 'User Dihapus' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($res->reservation_date)->format('d M Y') }}</td>
                                        <td style="text-align: right; color: #4b5563;">{{ \Carbon\Carbon::parse($res->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($res->end_time)->format('H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" style="text-align: center; color: #6b7280; padding: 30px 0;">Tidak ada peminjaman.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            
            <!-- Link Pagination Okupansi -->
            <div>{{ $selectedFacility == 'all' ? $occupancies->links('pagination::bootstrap-4') : $reservationDetails->links('pagination::bootstrap-4') }}</div>
        </div>
        @endif

        <!-- ================= BAGIAN KERUSAKAN ================= -->
        @if($selectedTipe == 'all' || $selectedTipe == 'kerusakan')
        <div class="card-ui" style="margin-bottom: 0; min-height: 300px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div class="card-title">{{ $selectedFacility == 'all' ? 'Frekuensi kerusakan' : 'Detail laporan kerusakan' }}</div>
                
                @if($selectedFacility == 'all')
                    <table class="freq-table" style="margin-top: 16px;">
                        <thead><tr><th>Fasilitas</th><th style="text-align: right;">Laporan</th></tr></thead>
                        <tbody>
                            @forelse($kerusakanData as $rusak)
                                <tr><td>{{ $rusak->nama }}</td><td style="text-align: right;">{{ $rusak->jumlah }}</td></tr>
                            @empty
                                <tr><td colspan="2" style="text-align: center; color: #6b7280; padding: 30px 0;">Belum ada laporan kerusakan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <div style="margin-top: 16px;">
                        <table class="freq-table">
                            <thead><tr><th>Deskripsi Kerusakan</th><th style="text-align: right;">Tanggal Lapor</th></tr></thead>
                            <tbody>
                                @forelse($reportDetails as $report)
                                    <tr>
                                        <td style="font-weight: 500;">{{ $report->description ?? 'Kerusakan dilaporkan' }}</td>
                                        <td style="text-align: right; color: #4b5563;">{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" style="text-align: center; color: #6b7280; padding: 30px 0;">Tidak ada laporan kerusakan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Link Pagination Kerusakan -->
            <div>{{ $selectedFacility == 'all' ? $kerusakanData->links('pagination::bootstrap-4') : $reportDetails->links('pagination::bootstrap-4') }}</div>
        </div>
        @endif
        
    </div>

    <!-- Export laporan -->
    <div class="card-ui" style="margin-top: 24px;">
        <div class="card-title">Export laporan</div>

        @if($selectedTipe == 'all')
            <div class="content-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 0;">
                <div>
                    <span style="display: block; margin-bottom: 12px; font-weight: 600;">Data Okupansi Fasilitas</span>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('admin.rekap.export-csv', array_merge(request()->query(), ['tipe_export' => 'okupansi'])) }}" class="btn btn-outline" style="padding: 6px 16px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; text-decoration: none;">CSV</a>
                        <a href="{{ route('admin.rekap.export-excel', array_merge(request()->query(), ['tipe_export' => 'okupansi'])) }}" class="btn btn-outline" style="padding: 6px 16px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; text-decoration: none;">Excel</a>
                        <a href="{{ route('admin.rekap.export-pdf', array_merge(request()->query(), ['tipe_export' => 'okupansi'])) }}" class="btn btn-outline" style="padding: 6px 16px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; text-decoration: none;">PDF</a>
                    </div>
                </div>
                <div>
                    <span style="display: block; margin-bottom: 12px; font-weight: 600;">Data Laporan Kerusakan</span>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('admin.rekap.export-csv', array_merge(request()->query(), ['tipe_export' => 'kerusakan'])) }}" class="btn btn-outline" style="padding: 6px 16px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; text-decoration: none;">CSV</a>
                        <a href="{{ route('admin.rekap.export-excel', array_merge(request()->query(), ['tipe_export' => 'kerusakan'])) }}" class="btn btn-outline" style="padding: 6px 16px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; text-decoration: none;">Excel</a>
                        <a href="{{ route('admin.rekap.export-pdf', array_merge(request()->query(), ['tipe_export' => 'kerusakan'])) }}" class="btn btn-outline" style="padding: 6px 16px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; text-decoration: none;">PDF</a>
                    </div>
                </div>
            </div>
        @else
            @php $jenisExport = $selectedTipe == 'okupansi' ? 'okupansi' : 'kerusakan'; @endphp
            <div>
                <span style="display: block; margin-bottom: 12px; font-weight: 600;">Data {{ $selectedTipe == 'okupansi' ? 'Okupansi Fasilitas' : 'Laporan Kerusakan' }}</span>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('admin.rekap.export-csv', array_merge(request()->query(), ['tipe_export' => $jenisExport])) }}" class="btn btn-outline" style="padding: 6px 16px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; text-decoration: none;">CSV</a>
                    <a href="{{ route('admin.rekap.export-excel', array_merge(request()->query(), ['tipe_export' => $jenisExport])) }}" class="btn btn-outline" style="padding: 6px 16px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; text-decoration: none;">Excel</a>
                    <a href="{{ route('admin.rekap.export-pdf', array_merge(request()->query(), ['tipe_export' => $jenisExport])) }}" class="btn btn-outline" style="padding: 6px 16px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; text-decoration: none;">PDF</a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection