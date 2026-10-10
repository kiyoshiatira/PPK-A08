@extends('layouts.petugas')

@section('content')
<style>
    .page-title { font-size: 26px; font-weight: 700; color: #171717; letter-spacing: -0.5px; margin-bottom: 4px; }
    .page-subtitle { color: #525252; font-size: 14px; margin-bottom: 24px; }

    /* Filter Bar */
    .filter-bar {
        display: flex; align-items: center; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;
    }
    .search-wrapper { flex: 1; min-width: 240px; position: relative; }
    .search-input {
        width: 100%; padding: 10px 14px 10px 38px; border: 1px solid #d4d4d4;
        border-radius: 8px; font-size: 13.5px; background: #ffffff; color: #171717; outline: none; transition: all 0.15s;
    }
    .search-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    .search-icon {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        color: #737373; pointer-events: none; display: flex; align-items: center;
    }
    .filter-select {
        padding: 10px 14px; border: 1px solid #d4d4d4; border-radius: 8px;
        font-size: 13.5px; background: #ffffff; color: #171717; outline: none; cursor: pointer;
        min-width: 150px;
    }
    .filter-select:focus { border-color: #2563eb; }

    /* Main Table Card */
    .card-wrap {
        background: #ffffff; border: 1px solid #e5e5e5; border-radius: 16px;
        overflow: hidden; margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.02);
    }
    .data-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
    .data-table th {
        background: #fafafa; padding: 14px 22px; font-weight: 600;
        font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #525252;
        border-bottom: 1px solid #e5e5e5;
    }
    .data-table td { padding: 16px 22px; border-bottom: 1px solid #f5f5f5; color: #171717; }
    .data-table tbody tr { cursor: pointer; transition: background 0.1s; }
    .data-table tbody tr:hover { background: #fafafa; }
    .data-table tbody tr.selected { background: #eff6ff; }
    .data-table tr:last-child td { border-bottom: none; }

    .facility-name { font-weight: 600; color: #171717; }

    /* Status Badges */
    .sbadge {
        display: inline-flex; align-items: center; padding: 4px 10px;
        border-radius: 9999px; font-size: 11.5px; font-weight: 600; white-space: nowrap;
    }
    .s-aktif         { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .s-perbaikan     { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .s-nonaktif      { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    /* Selected Facility Detail Panel */
    .detail-panel {
        background: #ffffff; border: 1px solid #e5e5e5; border-radius: 16px; padding: 32px;
        box-shadow: 0 1px 3px rgba(0,0,0,.02);
    }
    .detail-section-label {
        font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        color: #525252; margin-bottom: 16px;
    }
    .detail-facility-name { font-size: 22px; font-weight: 700; color: #171717; margin-bottom: 4px; }
    .detail-report-note { font-size: 13.5px; color: #525252; margin-bottom: 6px; }
    .detail-warning { font-size: 13px; color: #525252; background: #f5f5f5; border: 1px solid #e5e5e5; border-radius: 8px; padding: 14px 16px; margin-bottom: 24px; }

    /* Status toggle row */
    .status-toggle-label {
        font-size: 14px; font-weight: 600; color: #171717; margin-bottom: 10px;
    }
    .toggle-row {
        display: flex; align-items: center; border: 1px solid #d4d4d4;
        border-radius: 8px; overflow: hidden; margin-bottom: 24px; width: fit-content;
    }
    .toggle-btn {
        padding: 9px 24px; font-size: 13.5px; font-weight: 600; cursor: pointer;
        border: none; background: #ffffff; color: #525252; transition: all 0.15s;
    }
    .toggle-btn.active-btn { background: #171717; color: #ffffff; }
    .toggle-btn:first-child { border-right: 1px solid #d4d4d4; }

    .action-row {
        display: flex; gap: 10px; justify-content: flex-end;
    }
    .btn-cancel-change {
        background: #f5f5f5; color: #404040; border: 1px solid #d4d4d4;
        padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.15s;
    }
    .btn-cancel-change:hover { background: #e5e5e5; }
    .btn-save-change {
        background: #2563eb; color: #ffffff; border: none;
        padding: 9px 24px; border-radius: 8px; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.15s;
    }
    .btn-save-change:hover { background: #1d4ed8; }

    .no-selection {
        padding: 60px 20px; text-align: center; color: #737373; font-size: 13.5px;
    }
</style>

<div>
    <h1 class="page-title">Status Fasilitas</h1>
    <p class="page-subtitle">Tandai fasilitas aktif atau dalam perbaikan agar informasi selalu akurat.</p>

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
    @if(session('error'))
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13.5px;">
            ✕ {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13.5px;">
            <strong>Gagal:</strong> {{ $errors->first() }}
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="search-wrapper">
            <span class="search-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari nama fasilitas atau lokasi..." oninput="filterTable()">
        </div>
        <select class="filter-select" id="statusFilter" onchange="filterTable()">
            <option value="">Semua Status</option>
            <option value="Aktif" {{ $statusFilter === 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Dalam Perbaikan" {{ $statusFilter === 'Dalam Perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
        </select>
    </div>

    <!-- Facilities Table -->
    <div class="card-wrap">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Fasilitas</th>
                        <th style="width: 22%;">Lokasi</th>
                        <th style="width: 18%;">Status</th>
                        <th style="width: 22%;">Pembaruan Terakhir</th>
                        <th style="width: 8%; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="facilityTableBody">
                    @forelse($facilities as $facility)
                        <tr id="row-{{ $facility->id }}"
                            data-search="{{ strtolower($facility->name . ' ' . $facility->location . ' ' . $facility->type) }}"
                            data-status="{{ $facility->status }}"
                            onclick="selectFacility({{ $facility->id }})">
                            <td>
                                <span class="facility-name">{{ $facility->name }}</span>
                            </td>
                            <td style="color: #475569;">{{ $facility->location }}</td>
                            <td>
                                @if($facility->status === 'Aktif')
                                    <span class="sbadge s-aktif">Aktif</span>
                                @elseif($facility->status === 'Dalam Perbaikan')
                                    <span class="sbadge s-perbaikan">Dalam Perbaikan</span>
                                @else
                                    <span class="sbadge s-nonaktif">Nonaktif</span>
                                @endif
                            </td>
                            <td style="color: #64748b; font-size: 13px;">
                                {{ $facility->updated_at->format('j M, H:i') }}
                            </td>
                            <td style="text-align: right; color: #94a3b8;">—</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">
                                Tidak ada fasilitas ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($facilities->hasPages())
            <div style="padding: 14px 20px; border-top: 1px solid #f1f5f9; background: #fafafa;">
                {{ $facilities->links() }}
            </div>
        @endif
    </div>

    <!-- Selected Facility Detail Panel -->
    <div id="selectedPanel" style="display: none;">
        <div class="detail-section-label">Fasilitas yang Dipilih</div>
        <div class="detail-panel">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                <h3 id="sel-name" class="detail-facility-name"></h3>
                <span id="sel-badge" class="sbadge"></span>
            </div>
            <p id="sel-report-note" class="detail-report-note" style="display: none;"></p>
            <p id="sel-warning" class="detail-warning" style="display: none;"></p>

            <form id="updateStatusForm" method="POST" action="">
                @csrf
                @method('PATCH')

                <div class="status-toggle-label">Ubah Status Operasional Fasilitas</div>
                <div class="toggle-row">
                    <button type="button" class="toggle-btn" id="btnAktif" onclick="setStatus('Aktif')">Aktif</button>
                    <button type="button" class="toggle-btn" id="btnPerbaikan" onclick="setStatus('Dalam Perbaikan')">Dalam Perbaikan</button>
                </div>
                <input type="hidden" name="status" id="hiddenStatusInput">

                <div class="action-row">
                    <button type="button" class="btn-cancel-change" onclick="closePanel()">Batalkan</button>
                    <button type="submit" class="btn-save-change" id="saveBtn">Tandai Aktif</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Facilities data from server
    const facilitiesData = {!! $facilitiesJson !!};

    let currentStatus = '';

    function selectFacility(id) {
        const f = facilitiesData.find(x => x.id === id);
        if (!f) return;

        // Highlight row
        document.querySelectorAll('#facilityTableBody tr').forEach(r => r.classList.remove('selected'));
        const row = document.getElementById('row-' + id);
        if (row) row.classList.add('selected');

        // Fill panel
        document.getElementById('sel-name').innerText = f.name;
        setStatus(f.status);

        // Badge
        const badge = document.getElementById('sel-badge');
        badge.className = 'sbadge ' + statusClass(f.status);
        badge.innerText = f.status;

        // Form action
        document.getElementById('updateStatusForm').action = `/petugas/facilities/${id}/status`;

        // Warning for non-active
        const warning = document.getElementById('sel-warning');
        if (f.status !== 'Aktif') {
            warning.style.display = '';
            warning.innerText = `Pembaruan status dari "${f.status}" ke "Aktif" hanya dilakukan setelah teknisi tuntas menangani keluhan.`;
        } else {
            warning.style.display = 'none';
        }

        document.getElementById('selectedPanel').style.display = '';

        // Scroll to panel
        document.getElementById('selectedPanel').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function setStatus(status) {
        currentStatus = status;
        document.getElementById('hiddenStatusInput').value = status;

        // Toggle buttons
        ['Aktif', 'Dalam Perbaikan'].forEach(s => {
            const btnMap = { 'Aktif': 'btnAktif', 'Dalam Perbaikan': 'btnPerbaikan' };
            const btn = document.getElementById(btnMap[s]);
            if (btn) btn.classList.toggle('active-btn', s === status);
        });

        // Save button label
        const labels = { 'Aktif': 'Tandai Aktif', 'Dalam Perbaikan': 'Tandai Dalam Perbaikan' };
        document.getElementById('saveBtn').innerText = labels[status] || 'Simpan Perubahan';
    }

    function statusClass(status) {
        if (status === 'Aktif') return 's-aktif';
        if (status === 'Dalam Perbaikan') return 's-perbaikan';
        return 's-nonaktif';
    }

    function closePanel() {
        document.getElementById('selectedPanel').style.display = 'none';
        document.querySelectorAll('#facilityTableBody tr').forEach(r => r.classList.remove('selected'));
    }

    function filterTable() {
        const query  = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('statusFilter').value;
        document.querySelectorAll('#facilityTableBody tr[id]').forEach(row => {
            const text     = (row.dataset.search || '').toLowerCase();
            const rowStat  = row.dataset.status || '';
            const matchQ   = !query  || text.includes(query);
            const matchS   = !status || rowStat === status;
            row.style.display = (matchQ && matchS) ? '' : 'none';
        });
    }
</script>
@endsection
