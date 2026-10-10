@extends('layouts.petugas')

@section('content')
<style>
    /* Header Page */
    .page-header {
        margin-bottom: 24px;
    }
    .page-title {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.5px;
        margin-bottom: 6px;
    }
    .page-subtitle {
        color: #64748b;
        font-size: 14px;
        line-height: 1.5;
    }

    /* Filters Bar */
    .filter-section {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .search-wrapper {
        flex: 1;
        min-width: 260px;
        position: relative;
    }
    .search-input {
        width: 100%;
        padding: 10px 14px 10px 38px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13.5px;
        background: #ffffff;
        outline: none;
        transition: border-color 0.15s;
    }
    .search-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
        display: flex;
        align-items: center;
    }

    .filter-select {
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13.5px;
        background: #ffffff;
        color: #334155;
        outline: none;
        cursor: pointer;
        min-width: 130px;
    }
    .filter-select:focus {
        border-color: #2563eb;
    }

    /* Button Emergency Banner Shortcut */
    .btn-emergency-shortcut {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1px solid #fed7aa;
        color: #c2410c;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 24px;
        text-decoration: none;
        transition: all 0.15s;
    }
    .btn-emergency-shortcut:hover {
        background: #fff7ed;
        border-color: #fb923c;
    }

    /* Grid Cards */
    .reservation-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .reservation-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .reservation-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }
    .reservation-card.conflict-card {
        border-color: #fecaca;
        background: #fffdfd;
    }

    .card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 14px;
        gap: 12px;
    }
    .facility-name {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }

    /* Badges */
    .badge {
        padding: 3px 10px;
        border-radius: 9999px;
        font-size: 11.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }
    .badge-pending {
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fef08a;
    }
    .badge-conflict {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    .badge-approved {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .badge-rejected {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    .badge-canceled {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .card-meta {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .meta-row {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        line-height: 1.4;
    }
    .meta-label {
        color: #64748b;
        min-width: 60px;
    }
    .meta-val {
        color: #1e293b;
        font-weight: 500;
    }
    .meta-purpose {
        margin-top: 4px;
        padding: 8px 10px;
        background: #f8fafc;
        border-radius: 6px;
        font-size: 12.5px;
        color: #334155;
        font-style: italic;
    }

    /* Action Buttons */
    .card-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
    }
    .btn-approve {
        background: #3b82f6;
        color: #ffffff;
        border: 1px solid transparent;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
        text-align: center;
        width: 100%;
    }
    .btn-approve:hover:not(:disabled) {
        background: #2563eb;
    }
    .btn-approve:disabled {
        background: #cbd5e1;
        color: #ffffff;
        cursor: not-allowed;
        opacity: 0.8;
    }

    .btn-reject {
        background: #ffffff;
        color: #334155;
        border: 1px solid #cbd5e1;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        text-align: center;
        width: 100%;
    }
    .btn-reject:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }

    .btn-emergency-cancel {
        grid-column: span 2;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #c2410c;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        text-align: center;
    }
    .btn-emergency-cancel:hover {
        background: #ffedd5;
        border-color: #fb923c;
    }

    /* Modal / Bottom Drawer for Reject */
    .reject-modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(2px);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    .reject-modal-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        width: 100%;
        max-width: 600px;
        padding: 24px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        animation: fadeIn 0.15s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.98); }
        to { opacity: 1; transform: scale(1); }
    }

    .reject-modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .reject-modal-sub {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 18px;
    }
    .selected-reservation-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 18px;
        font-size: 13.5px;
        font-weight: 500;
        color: #1e293b;
    }

    .form-textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13.5px;
        color: #0f172a;
        resize: vertical;
        outline: none;
        margin-bottom: 20px;
        font-family: inherit;
    }
    .form-textarea:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .modal-btn-row {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .btn-modal-cancel {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
        padding: 9px 18px;
        border-radius: 6px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-modal-cancel:hover {
        background: #f1f5f9;
    }
    .btn-modal-submit-reject {
        background: #ef4444;
        border: none;
        color: #ffffff;
        padding: 9px 18px;
        border-radius: 6px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-modal-submit-reject:hover {
        background: #dc2626;
    }
</style>

<div>
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Antrean Reservasi</h1>
        <p class="page-subtitle">Tinjau pengajuan pending, tangani pembatalan darurat, dan pastikan jadwal fasilitas tidak bertabrakan.</p>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 13.5px; font-weight: 500;">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 13.5px;">
            <strong style="display: block; margin-bottom: 4px;">Peringatan Sistem:</strong>
            <ul style="margin-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter Form -->
    <form id="filterForm" action="{{ route('petugas.reservations.index') }}" method="GET">
        <div class="filter-section">
            <div class="search-wrapper">
                <span class="search-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" name="search" class="search-input" placeholder="Cari reservasi, pemohon, atau ruang..." value="{{ $search }}" onkeydown="if(event.key==='Enter'){this.form.submit();}">
            </div>

            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Status: Semua</option>
                <option value="Pending" {{ $statusFilter === 'Pending' ? 'selected' : '' }}>Status: Pending</option>
                <option value="Approved" {{ $statusFilter === 'Approved' ? 'selected' : '' }}>Status: Approved</option>
                <option value="Rejected" {{ $statusFilter === 'Rejected' ? 'selected' : '' }}>Status: Rejected</option>
                <option value="Canceled" {{ $statusFilter === 'Canceled' ? 'selected' : '' }}>Status: Canceled</option>
            </select>

            <select name="facility_id" class="filter-select" onchange="this.form.submit()">
                <option value="all">Ruang: Semua</option>
                @foreach($facilities as $fac)
                    <option value="{{ $fac->id }}" {{ $facilityFilter == $fac->id ? 'selected' : '' }}>Ruang: {{ $fac->name }}</option>
                @endforeach
            </select>

            <select name="date_filter" class="filter-select" onchange="this.form.submit()">
                <option value="all" {{ $dateFilter === 'all' ? 'selected' : '' }}>Tanggal: Semua</option>
                <option value="today" {{ $dateFilter === 'today' ? 'selected' : '' }}>Tanggal: Hari ini</option>
                <option value="this_week" {{ $dateFilter === 'this_week' ? 'selected' : '' }}>Tanggal: Minggu ini</option>
            </select>
        </div>
    </form>

    <!-- Tombol Shortcut Pembatalan Darurat -->
    <a href="{{ route('petugas.reservations.emergency-cancel') }}" class="btn-emergency-shortcut">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <span>Pembatalan darurat &mdash; Batalkan reservasi aktif saat darurat</span>
    </a>

    <!-- Reservation Cards Grid -->
    <div class="reservation-grid">
        @forelse($reservations as $res)
            @php
                $isExpired = \Carbon\Carbon::parse("{$res->reservation_date} {$res->start_time}")->isPast();
                $isEventEnded = \Carbon\Carbon::parse("{$res->reservation_date} {$res->end_time}")->isPast();
            @endphp
            <div class="reservation-card {{ $res->has_conflict ? 'conflict-card' : '' }}">
                <div>
                    <div class="card-top">
                        <div class="facility-name">{{ $res->facility->name }}</div>
                        <div>
                            @if($res->status === 'Pending')
                                @if($res->has_conflict)
                                    <span class="badge badge-conflict">Bentrok jadwal</span>
                                @elseif($isExpired)
                                    <span class="badge badge-conflict" title="Waktu penggunaan sudah terlewat">⚠️ Waktu Terlewat</span>
                                @else
                                    <span class="badge badge-pending">Pending</span>
                                @endif
                            @elseif($res->status === 'Approved')
                                @if($isEventEnded)
                                    <span class="badge badge-approved" style="background:#e8f5e9; color:#2e7d32; border-color:#c8e6c9;">✓ Selesai Terlaksana</span>
                                @else
                                    <span class="badge badge-approved">Approved</span>
                                @endif
                            @elseif($res->status === 'Rejected')
                                <span class="badge badge-rejected">Ditolak</span>
                            @else
                                <span class="badge badge-canceled">{{ $res->status }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-meta">
                        <div class="meta-row">
                            <span class="meta-label">Pemohon:</span>
                            <span class="meta-val">{{ $res->user->name }}</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Jadwal:</span>
                            <span class="meta-val" style="color: {{ $isExpired && $res->status === 'Pending' ? '#c62828' : '#2563eb' }}; font-weight: 600;">
                                {{ \Carbon\Carbon::parse($res->reservation_date)->translatedFormat('d M Y') }} &bull; {{ substr($res->start_time, 0, 5) }} &ndash; {{ substr($res->end_time, 0, 5) }} WIB
                            </span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Tujuan:</span>
                            <span class="meta-val">&ldquo;{{ $res->purpose }}&rdquo;</span>
                        </div>
                        @if($res->rejection_or_cancel_reason)
                            <div class="meta-purpose" style="background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2;">
                                <strong>Alasan:</strong> {{ $res->rejection_or_cancel_reason }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="card-actions">
                    @if($res->status === 'Pending')
                        <!-- Tombol Approve -->
                        <form action="{{ route('petugas.reservations.approve', $res->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-approve" {{ ($res->has_conflict || $isExpired) ? 'disabled' : '' }} onclick="return confirm('Setujui reservasi {{ addslashes($res->facility->name) }} untuk {{ addslashes($res->user->name) }}?')">
                                Approve
                            </button>
                        </form>

                        <!-- Tombol Reject (Buka Form / Modal) -->
                        <button type="button" class="btn-reject" onclick="openRejectModal({{ $res->id }}, '{{ addslashes($res->facility->name) }}', '{{ addslashes($res->user->name) }}')">
                            Reject
                        </button>
                    @elseif($res->status === 'Approved')
                        @if($isEventEnded)
                            <div style="grid-column: span 2; text-align: center; color: #166534; font-size: 12px; padding: 6px 0; font-weight: 600;">
                                Kegiatan telah selesai terlaksana
                            </div>
                        @else
                            <button type="button" class="btn-emergency-cancel" onclick="openCancelModal({{ $res->id }}, '{{ addslashes($res->facility->name) }}', '{{ addslashes($res->user->name) }}')">
                                Batalkan Reservasi (Darurat)
                            </button>
                        @endif
                    @else
                        <div style="grid-column: span 2; text-align: center; color: #94a3b8; font-size: 12px; padding: 4px 0;">
                            Diproses oleh: {{ $res->processor->name ?? 'Sistem' }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 60px 20px; text-align: center; color: #64748b;">
                <p style="font-size: 15px; font-weight: 500; margin-bottom: 4px;">Tidak ada reservasi ditemukan</p>
                <p style="font-size: 13px; color: #94a3b8;">Coba sesuaikan filter status, ruang, atau kata kunci pencarian Anda.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($reservations->hasPages())
        <div style="display: flex; justify-content: center; margin-top: 20px;">
            {{ $reservations->links() }}
        </div>
    @endif
</div>

<!-- Modal Reject Reservasi (Sesuai Desain Bagian Bawah Gambar Mockup) -->
<div id="rejectModal" class="reject-modal-backdrop">
    <div class="reject-modal-card">
        <h2 class="reject-modal-title">Reject reservasi</h2>
        <p class="reject-modal-sub">Alasan wajib diisi dan akan dikirim kepada pemohon.</p>

        <form id="rejectForm" method="POST">
            @csrf
            @method('PATCH')

            <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Reservasi Terpilih</label>
            <div id="selectedReservationBox" class="selected-reservation-box">
                -
            </div>

            <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Alasan Penolakan <span style="color: #ef4444;">*</span></label>
            <textarea name="reason" rows="4" required class="form-textarea" placeholder="Jadwal bertabrakan dengan kegiatan UTS praktikum mata kuliah Pemrograman Web yang telah disetujui sebelumnya."></textarea>

            <div class="modal-btn-row">
                <button type="button" class="btn-modal-cancel" onclick="closeRejectModal()">Batal</button>
                <button type="submit" class="btn-modal-submit-reject">Konfirmasi reject</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Emergency Cancel -->
<div id="cancelModal" class="reject-modal-backdrop">
    <div class="reject-modal-card">
        <h2 class="reject-modal-title" style="color: #c2410c;">Pembatalan Darurat Reservasi</h2>
        <p class="reject-modal-sub">Batalkan reservasi yang sudah approved karena kondisi mendadak.</p>

        <form id="cancelForm" method="POST">
            @csrf
            @method('PATCH')

            <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Reservasi Terpilih</label>
            <div id="selectedCancelBox" class="selected-reservation-box">
                -
            </div>

            <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Alasan Pembatalan Darurat <span style="color: #ef4444;">*</span></label>
            <textarea name="cancel_reason" rows="4" required class="form-textarea" placeholder="Jelaskan alasan pembatalan secara jelas (contoh: ruangan sedang mengalami renovasi atap darurat)..."></textarea>

            <div class="modal-btn-row">
                <button type="button" class="btn-modal-cancel" onclick="closeCancelModal()">Batal</button>
                <button type="submit" class="btn-modal-submit-reject" style="background: #c2410c;">Batalkan reservasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(id, facilityName, userName) {
        const modal = document.getElementById('rejectModal');
        const form  = document.getElementById('rejectForm');
        const box   = document.getElementById('selectedReservationBox');

        form.action = `/petugas/reservations/${id}/reject`;
        box.innerHTML = `<strong>${facilityName}</strong> &bull; ${userName}`;
        modal.style.display = 'flex';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }

    function openCancelModal(id, facilityName, userName) {
        const modal = document.getElementById('cancelModal');
        const form  = document.getElementById('cancelForm');
        const box   = document.getElementById('selectedCancelBox');

        form.action = `/petugas/reservations/${id}/cancel`;
        box.innerHTML = `<strong>${facilityName}</strong> &bull; ${userName}`;
        modal.style.display = 'flex';
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').style.display = 'none';
    }

    // Close modal when clicking outside modal box
    window.addEventListener('click', function(e) {
        const rejectModal = document.getElementById('rejectModal');
        const cancelModal = document.getElementById('cancelModal');
        if (e.target === rejectModal) closeRejectModal();
        if (e.target === cancelModal) closeCancelModal();
    });
</script>
@endsection


