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
        padding: 20px;
        position: sticky;
        top: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .list-panel-header {
        font-size: 15px;
        font-weight: 700;
        color: #171717;
        margin-bottom: 16px;
    }
    .report-items-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .report-item {
        background: #ffffff;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }
    .report-item:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .report-item.active {
        background: #e2e8f0;
        border-color: #cbd5e1;
    }
    .ri-title { font-weight: 700; font-size: 14px; color: #171717; margin-bottom: 6px; line-height: 1.4; }
    .ri-meta { font-size: 12px; color: #525252; }
    .ri-empty { padding: 36px 20px; text-align: center; color: #737373; font-size: 13.5px; }

    /* Status Badges */
    .sbadge {
        display: inline-flex; align-items: center; padding: 4px 10px;
        border-radius: 9999px; font-size: 11px; font-weight: 600; white-space: nowrap;
    }
    .s-baru     { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .s-diproses { background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; }
    .s-selesai  { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
    .s-ditolak  { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }

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
        background: #f1f5f9;
        border-radius: 12px;
        height: 240px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 13.5px;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .photo-box img { width: 100%; height: 100%; object-fit: cover; border-radius: 12px; }

    .desc-label { font-size: 14px; font-weight: 700; color: #171717; margin-bottom: 6px; }
    .desc-text  { font-size: 14px; color: #404040; line-height: 1.6; margin-bottom: 24px; }

    /* Status action box */
    .status-action-box {
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        padding: 20px;
        background: #ffffff;
    }
    .status-box-header {
        font-size: 13.5px;
        font-weight: 700;
        color: #171717;
        margin-bottom: 12px;
    }
    .status-select-action {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d4d4d4;
        border-radius: 8px;
        font-size: 13.5px;
        background: #ffffff;
        color: #171717;
        outline: none;
        cursor: pointer;
        margin-bottom: 14px;
    }
    .status-select-action:focus {
        border-color: #2563eb;
    }

    .status-pill-list {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .pill-step {
        padding: 5px 14px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #e5e5e5;
        background: #f1f5f9;
        color: #64748b;
        cursor: pointer;
        transition: all 0.15s;
    }
    .pill-step:hover {
        opacity: 0.85;
    }
    .pill-step.active {
        border-color: #d97706;
        color: #d97706;
        background: #ffffff;
        box-shadow: 0 0 0 1px #d97706;
    }
    .pill-step.active-baru {
        border-color: #2563eb;
        color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 1px #2563eb;
    }
    .pill-step.active-selesai {
        border-color: #16a34a;
        color: #16a34a;
        background: #ffffff;
        box-shadow: 0 0 0 1px #16a34a;
    }
    .pill-step.active-ditolak {
        border-color: #dc2626;
        color: #dc2626;
        background: #ffffff;
        box-shadow: 0 0 0 1px #dc2626;
    }
    .pill-step.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .resolution-label { font-size: 13.5px; font-weight: 700; color: #171717; margin-bottom: 8px; }
    .resolution-textarea {
        width: 100%; padding: 12px 14px; border: 1px solid #d4d4d4; border-radius: 8px;
        font-size: 13.5px; font-family: inherit; color: #171717; outline: none; resize: vertical;
        min-height: 90px; transition: border-color 0.15s; margin-bottom: 18px;
    }
    .resolution-textarea:focus { border-color: #2563eb; }

    .action-row {
        display: flex; gap: 10px; justify-content: flex-end;
    }
    .btn-close {
        background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;
        padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 600;
        cursor: pointer; text-decoration: none; transition: all 0.15s;
    }
    .btn-close:hover { background: #e2e8f0; }
    .btn-save {
        background: #3b82f6; color: #ffffff; border: none;
        padding: 9px 24px; border-radius: 8px; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.15s;
    }
    .btn-save:hover { background: #2563eb; }
    .btn-save:disabled { background: #cbd5e1; cursor: not-allowed; }

    .no-selection {
        padding: 60px 30px; text-align: center; color: #737373;
    }
    .no-selection-icon { font-size: 36px; margin-bottom: 12px; }

    /* Custom App Modal */
    .custom-modal-backdrop {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(4px);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    .custom-modal-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        width: 100%;
        max-width: 440px;
        padding: 24px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        animation: modalScaleIn 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        text-align: center;
    }
    @keyframes modalScaleIn {
        from { opacity: 0; transform: scale(0.94); }
        to { opacity: 1; transform: scale(1); }
    }
    .custom-modal-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }
    .custom-modal-icon.warning {
        background: #fef3c7;
        color: #d97706;
    }
    .custom-modal-icon.info {
        background: #eff6ff;
        color: #2563eb;
    }
    .custom-modal-icon.danger {
        background: #fee2e2;
        color: #dc2626;
    }
    .custom-modal-title {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .custom-modal-desc {
        font-size: 13.5px;
        color: #64748b;
        line-height: 1.5;
        margin-bottom: 24px;
    }
    .custom-modal-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    .custom-modal-btn-cancel {
        flex: 1;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s;
    }
    .custom-modal-btn-cancel:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .custom-modal-btn-confirm {
        flex: 1;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        border: none;
        background: #2563eb;
        color: #ffffff;
        cursor: pointer;
        transition: all 0.15s;
    }
    .custom-modal-btn-confirm:hover {
        background: #1d4ed8;
    }
</style>

<div>
    <h1 class="page-title">Kelola laporan</h1>
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
            <input type="text" id="searchInput" class="search-input" placeholder="Cari laporan atau fasilitas..." oninput="filterList()">
        </div>
        <select class="filter-select" name="status" id="statusSelectFilter" onchange="this.form.submit()">
            <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
            <option value="Baru" {{ ($statusFilter ?? '') === 'Baru' ? 'selected' : '' }}>Baru</option>
            <option value="Diproses" {{ ($statusFilter ?? '') === 'Diproses' ? 'selected' : '' }}>Diproses</option>
            <option value="Selesai" {{ ($statusFilter ?? '') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="Ditolak" {{ ($statusFilter ?? '') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <select class="filter-select" name="time" id="timeSelectFilter" onchange="this.form.submit()">
            <option value="all" {{ ($timeFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Prioritas</option>
            <option value="today" {{ ($timeFilter ?? '') === 'today' ? 'selected' : '' }}>Hari Ini</option>
            <option value="this_week" {{ ($timeFilter ?? '') === 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
            <option value="this_month" {{ ($timeFilter ?? '') === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
        </select>
    </form>

    <!-- Split Pane -->
    <div class="split-pane">
        <!-- Left: Report List -->
        <div class="list-panel">
            <div class="list-panel-header">
                Daftar Laporan ({{ $reports->total() }})
            </div>

            <div class="report-items-container" id="reportListItems">
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
    <!-- Custom App Modal Structure -->
    <div id="customModalBackdrop" class="custom-modal-backdrop">
        <div class="custom-modal-card">
            <div id="customModalIcon" class="custom-modal-icon warning">
                <!-- Icon will be injected -->
            </div>
            <div id="customModalTitle" class="custom-modal-title">Konfirmasi Tindakan</div>
            <div id="customModalDesc" class="custom-modal-desc">Apakah Anda yakin ingin melanjutkan tindakan ini?</div>
            <div class="custom-modal-actions">
                <button type="button" id="customModalCancelBtn" class="custom-modal-btn-cancel" onclick="closeCustomModal(false)">Batal</button>
                <button type="button" id="customModalConfirmBtn" class="custom-modal-btn-confirm" onclick="closeCustomModal(true)">Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Global Custom Modal Implementation
    let modalResolver = null;

    function openCustomModal({ title, message, type = 'warning', confirmText = 'Lanjutkan', cancelText = 'Batal', isAlert = false }) {
        const backdrop = document.getElementById('customModalBackdrop');
        const iconContainer = document.getElementById('customModalIcon');
        const titleEl = document.getElementById('customModalTitle');
        const descEl = document.getElementById('customModalDesc');
        const cancelBtn = document.getElementById('customModalCancelBtn');
        const confirmBtn = document.getElementById('customModalConfirmBtn');

        titleEl.textContent = title;
        descEl.textContent = message;
        confirmBtn.textContent = confirmText;
        cancelBtn.textContent = cancelText;

        // Type styles
        iconContainer.className = 'custom-modal-icon ' + type;
        if (type === 'warning') {
            iconContainer.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
            confirmBtn.style.background = '#2563eb';
        } else if (type === 'danger') {
            iconContainer.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
            confirmBtn.style.background = '#dc2626';
        } else {
            iconContainer.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
            confirmBtn.style.background = '#2563eb';
        }

        if (isAlert) {
            cancelBtn.style.display = 'none';
        } else {
            cancelBtn.style.display = 'block';
        }

        backdrop.style.display = 'flex';

        return new Promise(resolve => {
            modalResolver = resolve;
        });
    }

    function closeCustomModal(result) {
        document.getElementById('customModalBackdrop').style.display = 'none';
        if (modalResolver) {
            modalResolver(result);
            modalResolver = null;
        }
    }

    // Window helpers
    window.customConfirm = function(message, title = 'Konfirmasi Tindakan', type = 'warning') {
        return openCustomModal({ title, message, type, confirmText: 'Ya, Lanjutkan', cancelText: 'Batal', isAlert: false });
    };

    window.customAlert = function(message, title = 'Pemberitahuan', type = 'info') {
        return openCustomModal({ title, message, type, confirmText: 'Mengerti', cancelText: '', isAlert: true });
    };

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
                const panel = document.getElementById('detailPanel');
                panel.innerHTML = html;
                // Re-execute script tags from the loaded partial
                panel.querySelectorAll('script').forEach(oldScript => {
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
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
