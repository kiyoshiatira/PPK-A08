@extends('layouts.petugas')

@section('content')
<style>
    .page-title { font-size: 26px; font-weight: 700; color: #171717; letter-spacing: -0.5px; margin-bottom: 4px; }
    .page-subtitle { color: #525252; font-size: 14px; margin-bottom: 24px; }

    /* Filter Bar */
    .filter-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .search-wrapper {
        flex: 1;
        min-width: 240px;
        position: relative;
    }
    .search-input {
        width: 100%;
        padding: 10px 14px 10px 38px;
        border: 1px solid #d4d4d4;
        border-radius: 8px;
        font-size: 13.5px;
        background: #ffffff;
        color: #171717;
        outline: none;
        transition: all 0.15s;
    }
    .search-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    .search-icon {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%); color: #737373;
        pointer-events: none; display: flex; align-items: center;
    }
    .filter-select {
        padding: 10px 14px; border: 1px solid #d4d4d4; border-radius: 8px;
        font-size: 13.5px; background: #ffffff; color: #171717; outline: none; cursor: pointer;
        min-width: 140px;
    }
    .filter-select:focus { border-color: #2563eb; }

    /* Split-Pane Layout */
    .split-pane {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 24px;
        align-items: start;
    }

    /* Left List Panel */
    .list-panel {
        background: #ffffff;
        border: 1px solid #e5e5e5;
        border-radius: 16px;
        overflow: hidden;
        position: sticky;
        top: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .list-panel-header {
        padding: 18px 22px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 15px;
        font-weight: 700;
        color: #171717;
    }
    .report-item {
        padding: 16px 20px;
        border-bottom: 1px solid #f5f5f5;
        cursor: pointer;
        transition: background 0.12s;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }
    .report-item:hover { background: #fafafa; }
    .report-item.active { background: #eff6ff; border-left: 3px solid #2563eb; }
    .report-item:last-child { border-bottom: none; }
    .ri-title { font-weight: 600; font-size: 14px; color: #171717; margin-bottom: 4px; line-height: 1.4; }
    .ri-meta { font-size: 12.5px; color: #525252; }
    .ri-empty { padding: 36px 20px; text-align: center; color: #737373; font-size: 13.5px; }

    /* Status Badges */
    .sbadge {
        display: inline-flex; align-items: center; padding: 3px 8px;
        border-radius: 9999px; font-size: 11px; font-weight: 600; white-space: nowrap;
    }
    .s-baru     { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .s-diproses { background: #fefce8; color: #a16207; border: 1px solid #fef08a; }
    .s-selesai  { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .s-ditolak  { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    /* Right Detail Panel */
    .detail-panel {
        background: #ffffff;
        border: 1px solid #e5e5e5;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .detail-title { font-size: 22px; font-weight: 700; color: #171717; margin-bottom: 6px; }
    .detail-meta  { font-size: 13.5px; color: #525252; margin-bottom: 24px; }

    .photo-box {
        background: #f5f5f5;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #737373;
        font-size: 13.5px;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .photo-box img { width: 100%; height: 100%; object-fit: cover; border-radius: 12px; }

    .desc-label { font-size: 14px; font-weight: 700; color: #171717; margin-bottom: 6px; }
    .desc-text  { font-size: 14px; color: #404040; line-height: 1.6; margin-bottom: 24px; }

    .status-label { font-size: 14px; font-weight: 700; color: #171717; margin-bottom: 8px; }
    .status-flow {
        display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;
    }
    .sf-pill {
        padding: 6px 16px; border-radius: 9999px; font-size: 12.5px; font-weight: 600;
        border: 1px solid #e5e5e5; background: #f5f5f5; color: #525252;
    }
    .sf-pill.current { background: #fefce8; color: #a16207; border-color: #fef08a; }
    .sf-pill.done    { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }

    .resolution-label { font-size: 14px; font-weight: 700; color: #171717; margin-bottom: 8px; }
    .resolution-textarea {
        width: 100%; padding: 12px 14px; border: 1px solid #d4d4d4; border-radius: 8px;
        font-size: 13.5px; font-family: inherit; color: #171717; outline: none; resize: vertical;
        min-height: 100px; transition: border-color 0.15s; margin-bottom: 18px;
    }
    .resolution-textarea:focus { border-color: #2563eb; }

    .action-row {
        display: flex; gap: 10px; justify-content: flex-end;
    }
    .btn-close {
        background: #f5f5f5; color: #404040; border: 1px solid #d4d4d4;
        padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 600;
        cursor: pointer; text-decoration: none; transition: all 0.15s;
    }
    .btn-close:hover { background: #e5e5e5; }
    .btn-save {
        background: #2563eb; color: #ffffff; border: none;
        padding: 9px 24px; border-radius: 8px; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.15s;
    }
    .btn-save:hover { background: #1d4ed8; }
    .btn-save:disabled { background: #cbd5e1; cursor: not-allowed; }

    .no-selection {
        padding: 60px 30px; text-align: center; color: #737373;
    }
    .no-selection-icon { font-size: 36px; margin-bottom: 12px; }
</style>

<div>
    <h1 class="page-title">Laporan Kerusakan</h1>
    <p class="page-subtitle">Pantau proses penanganan kerusakan dan tulis catatan resolusi untuk pelapor.</p>

    {{-- Flash --}}
    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13.5px;">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13.5px;">
            ℹ {{ session('info') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13.5px;">
            <strong>Gagal:</strong> {{ $errors->first() }}
        </div>
    @endif

    <!-- Filter Bar Form -->
    <form id="filterForm" action="{{ route('petugas.reports.index') }}" method="GET" class="filter-bar">
        <div class="search-wrapper">
            <span class="search-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari fasilitas, pelapor, atau deskripsi..." oninput="filterList()">
        </div>
        <select class="filter-select" name="status" id="statusSelectFilter" onchange="this.form.submit()">
            <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
            <option value="Baru" {{ ($statusFilter ?? '') === 'Baru' ? 'selected' : '' }}>Status: Baru</option>
            <option value="Diproses" {{ ($statusFilter ?? '') === 'Diproses' ? 'selected' : '' }}>Status: Diproses</option>
            <option value="Selesai" {{ ($statusFilter ?? '') === 'Selesai' ? 'selected' : '' }}>Status: Selesai</option>
            <option value="Ditolak" {{ ($statusFilter ?? '') === 'Ditolak' ? 'selected' : '' }}>Status: Ditolak</option>
        </select>
        <select class="filter-select" name="time" id="timeSelectFilter" onchange="this.form.submit()">
            <option value="all" {{ ($timeFilter ?? 'all') === 'all' ? 'selected' : '' }}>Waktu: Semua</option>
            <option value="today" {{ ($timeFilter ?? '') === 'today' ? 'selected' : '' }}>Waktu: Hari Ini</option>
            <option value="this_week" {{ ($timeFilter ?? '') === 'this_week' ? 'selected' : '' }}>Waktu: Minggu Ini</option>
            <option value="this_month" {{ ($timeFilter ?? '') === 'this_month' ? 'selected' : '' }}>Waktu: Bulan Ini</option>
        </select>
    </form>

    <!-- Split Pane -->
    <div class="split-pane">
        <!-- Left: Report List -->
        <div class="list-panel">
            <div class="list-panel-header">
                Daftar Laporan Kerusakan ({{ $reports->total() }})
            </div>

            <div id="reportListItems">
                @forelse($reports as $report)
                    <div class="report-item {{ $loop->first ? 'active' : '' }}"
                         id="item-{{ $report->id }}"
                         data-search="{{ strtolower(($report->facility->name ?? '') . ' ' . ($report->user->name ?? '') . ' ' . $report->description . ' ' . $report->status) }}"
                         data-status="{{ $report->status }}"
                         onclick="showDetail({{ $report->id }})">
                        <div>
                            <div class="ri-title">{{ $report->description }}</div>
                            <div class="ri-meta">
                                {{ $report->facility->name ?? '-' }} &middot;
                                {{ $report->user->name ?? '-' }} &middot;
                                {{ $report->created_at->format('d M Y') }}
                            </div>
                        </div>
                        <span class="sbadge
                            {{ $report->status === 'Baru' ? 's-baru' : '' }}
                            {{ $report->status === 'Diproses' ? 's-diproses' : '' }}
                            {{ $report->status === 'Selesai' ? 's-selesai' : '' }}
                            {{ $report->status === 'Ditolak' ? 's-ditolak' : '' }}">
                            {{ $report->status }}
                        </span>
                    </div>
                @empty
                    <div class="ri-empty">Tidak ada laporan.</div>
                @endforelse
            </div>
        </div>

        <!-- Right: Detail Panel -->
        <div id="detailPanel">
            @if($reports->count() > 0)
                @php $first = $reports->first(); @endphp
                @include('petugas.reports._detail', ['report' => $first])
            @else
                <div class="detail-panel">
                    <div class="no-selection">
                        <div class="no-selection-icon">📋</div>
                        <p>Pilih laporan di sebelah kiri untuk melihat detail dan melakukan tindakan.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Data semua reports untuk JS rendering
    const reportsData = @json($reports->items());

    function showDetail(id) {
        // Highlight aktif
        document.querySelectorAll('.report-item').forEach(el => el.classList.remove('active'));
        const item = document.getElementById('item-' + id);
        if (item) item.classList.add('active');

        // Fetch detail via AJAX (simple approach: reload panel via server partial)
        fetch(`/petugas/reports/${id}/detail`)
            .then(r => r.text())
            .then(html => {
                document.getElementById('detailPanel').innerHTML = html;
            })
            .catch(() => {
                // fallback: just scroll to top
            });
    }

    function filterList() {
        const query  = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('statusSelectFilter').value;

        document.querySelectorAll('.report-item').forEach(item => {
            const text   = (item.dataset.search || '').toLowerCase();
            const isStat = (item.dataset.status || '');
            const matchQ = !query  || text.includes(query);
            const matchS = !status || isStat === status;
            item.style.display = (matchQ && matchS) ? '' : 'none';
        });
    }
</script>
@endsection
