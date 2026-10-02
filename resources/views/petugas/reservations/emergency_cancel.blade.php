@extends('layouts.petugas')

@section('content')
<style>
    /* Header Section */
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

    /* Red Danger Warning Banner */
    .banner-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #991b1b;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 28px;
    }
    .danger-icon-circle {
        width: 22px;
        height: 22px;
        background: #991b1b;
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        flex-shrink: 0;
    }

    /* Card Container Form */
    .card-emergency {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 32px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }

    .step-tag {
        color: #2563eb;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        margin-bottom: 16px;
        display: block;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
    }

    .search-wrapper {
        position: relative;
        margin-bottom: 16px;
    }
    .search-input {
        width: 100%;
        padding: 11px 14px 11px 40px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13.5px;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: all 0.15s;
    }
    .search-input:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
        display: flex;
        align-items: center;
    }

    /* Selected / Available Reservations List */
    .reservation-list-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-height: 280px;
        overflow-y: auto;
        padding: 4px;
        margin-bottom: 24px;
    }

    .reservation-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .reservation-item:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .reservation-item.selected {
        background: #ffffff;
        border: 2px solid #ef4444;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.08);
    }

    .item-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .item-sub {
        font-size: 13px;
        color: #64748b;
    }

    .badge-approved {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
        padding: 3px 10px;
        border-radius: 9999px;
        font-size: 11.5px;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Textarea */
    .textarea-reason {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13.5px;
        font-family: inherit;
        color: #0f172a;
        outline: none;
        resize: vertical;
        min-height: 110px;
        transition: all 0.15s;
    }
    .textarea-reason:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    /* Checkbox terms */
    .checkbox-container {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
        margin-bottom: 28px;
        cursor: pointer;
    }
    .checkbox-custom {
        width: 18px;
        height: 18px;
        accent-color: #2563eb;
        cursor: pointer;
    }
    .checkbox-label {
        font-size: 13.5px;
        color: #334155;
        user-select: none;
        cursor: pointer;
    }

    /* Action Buttons Row */
    .form-btn-row {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
    .btn-batal {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 10px 22px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-batal:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .btn-submit-emergency {
        background: #881337;
        color: #ffffff;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-submit-emergency:hover:not(:disabled) {
        background: #4c0519;
    }
    .btn-submit-emergency:disabled {
        background: #cbd5e1;
        cursor: not-allowed;
    }
</style>

<div>
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Antrean Reservasi / Pembatalan Darurat</h1>
        <p class="page-subtitle">Batalkan reservasi yang sudah approved karena kondisi mendadak. Pemohon akan menerima notifikasi.</p>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 13.5px; font-weight: 500;">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 13.5px;">
            <strong style="display: block; margin-bottom: 4px;">Gagal Memproses:</strong>
            <ul style="margin-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Banner Danger Sesuai Mockup -->
    <div class="banner-danger">
        <div class="danger-icon-circle">!</div>
        <span>Gunakan fitur ini hanya untuk keadaan darurat atau fasilitas yang tidak dapat digunakan.</span>
    </div>

    <!-- Card Form Sesuai Mockup -->
    <div class="card-emergency">
        <span class="step-tag">Pilih Reservasi yang Akan Dibatalkan</span>

        <!-- Search Input -->
        <div class="form-group">
            <label class="form-label">Cari reservasi:</label>
            <div class="search-wrapper">
                <span class="search-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" id="filterInput" class="search-input" placeholder="Cari nama pemohon atau fasilitas..." oninput="filterReservations()">
            </div>
        </div>

        <!-- Form Submit Form -->
        <form id="emergencyCancelForm" method="POST" action="">
            @csrf
            @method('PATCH')

            <!-- List Pilihan Reservasi -->
            <div class="reservation-list-container" id="reservationList">
                @forelse($approvedReservations as $index => $res)
                    <div class="reservation-item {{ $index === 0 ? 'selected' : '' }}" 
                         data-id="{{ $res->id }}"
                         data-name="{{ strtolower($res->user->name . ' ' . $res->facility->name . ' ' . $res->purpose) }}"
                         onclick="selectReservation(this, {{ $res->id }})">
                        <div>
                            <div class="item-title">{{ $res->facility->name }}</div>
                            <div class="item-sub">
                                Pemohon: <strong>{{ $res->user->name }}</strong> &bull; {{ \Carbon\Carbon::parse($res->reservation_date)->translatedFormat('d M Y') }} &bull; {{ substr($res->start_time, 0, 5) }}&ndash;{{ substr($res->end_time, 0, 5) }}
                            </div>
                            <div class="item-sub" style="margin-top: 2px;">
                                Tujuan: {{ $res->purpose }}
                            </div>
                        </div>
                        <span class="badge-approved">Approved</span>
                    </div>
                @empty
                    <div style="padding: 30px; text-align: center; color: #64748b; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">
                        Tidak ada reservasi berstatus <strong>Approved</strong> yang sedang aktif saat ini.
                    </div>
                @endforelse
            </div>

            <!-- Input Alasan -->
            <div class="form-group">
                <label class="form-label">Alasan pembatalan darurat <span style="color: #ef4444;">*</span></label>
                <textarea name="cancel_reason" required class="textarea-reason" placeholder="Jelaskan alasan pembatalan secara jelas..."></textarea>
            </div>

            <!-- Checkbox Pemahaman -->
            <label class="checkbox-container">
                <input type="checkbox" id="agreementCheckbox" class="checkbox-custom" required onchange="toggleSubmitButton()">
                <span class="checkbox-label">Saya memahami bahwa pembatalan akan langsung diberitahukan kepada pemohon.</span>
            </label>

            <!-- Buttons -->
            <div class="form-btn-row">
                <a href="{{ route('petugas.reservations.index') }}" class="btn-batal">Batal</a>
                <button type="submit" id="submitBtn" class="btn-submit-emergency" disabled onclick="return confirm('Apakah Anda yakin ingin membatalkan reservasi ini secara darurat?')">
                    Batalkan reservasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let selectedReservationId = null;

    document.addEventListener('DOMContentLoaded', function() {
        const firstItem = document.querySelector('.reservation-item');
        if (firstItem) {
            selectedReservationId = firstItem.dataset.id;
            updateFormAction(selectedReservationId);
        }
    });

    function selectReservation(element, id) {
        document.querySelectorAll('.reservation-item').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        selectedReservationId = id;
        updateFormAction(id);
        toggleSubmitButton();
    }

    function updateFormAction(id) {
        const form = document.getElementById('emergencyCancelForm');
        form.action = `/petugas/reservations/${id}/cancel`;
    }

    function toggleSubmitButton() {
        const checkbox = document.getElementById('agreementCheckbox');
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = !checkbox.checked || !selectedReservationId;
    }

    function filterReservations() {
        const query = document.getElementById('filterInput').value.toLowerCase();
        const items = document.querySelectorAll('.reservation-item');
        
        items.forEach(item => {
            const text = item.dataset.name;
            if (text.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>
@endsection
