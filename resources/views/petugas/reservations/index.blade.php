@extends('layouts.petugas')

@section('content')
<style>
    .page-title { font-size: 24px; font-weight: 700; margin-bottom: 5px; color: #111; }
    .page-subtitle { color: #666; font-size: 14px; margin-bottom: 25px; }

    .card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 30px; overflow: hidden; }
    .card-header { padding: 18px 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
    .card-title { font-size: 15px; font-weight: 700; margin: 0; color: #222; }

    .data-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
    .data-table th { background: #f5f5f5; padding: 12px 18px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e0e0; }
    .data-table td { padding: 14px 18px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    .data-table tr:last-child td { border-bottom: none; }
    
    .facility-badge { font-weight: 600; color: #111; display: block; font-size: 13px; }
    .user-info { color: #666; font-size: 12px; }
    
    .status-badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .status-pending { background: #fff8e1; color: #f57f17; border: 1px solid #ffe082; }
    .status-approved { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .status-rejected { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
    .status-canceled { background: #f5f5f5; color: #757575; border: 1px solid #e0e0e0; }

    .btn-action-group { display: flex; gap: 8px; align-items: center; }
    .btn-approve { background: #2e7d32; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-approve:hover { background: #1b5e20; }
    .btn-reject { background: #d32f2f; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-reject:hover { background: #b71c1c; }

    .filter-tabs { display: flex; gap: 8px; margin-bottom: 20px; }
    .filter-tab { padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 500; text-decoration: none; color: #666; background: #fff; border: 1px solid #e0e0e0; }
    .filter-tab.active { background: #1565c0; color: #fff; border-color: #1565c0; font-weight: 600; }

    /* Modal Alasan Tolak */
    .modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; justify-content: center; align-items: center; }
    .modal-box { background: #fff; border-radius: 8px; padding: 24px; max-width: 450px; width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
</style>

<div>
    <h1 class="page-title">Persetujuan Reservasi Fasilitas</h1>
    <p class="page-subtitle">Kelola permohonan reservasi yang masuk. Sistem secara otomatis memvalidasi bentrok jadwal antar reservasi.</p>

    <!-- Notifikasi Sukses / Error -->
    @if(session('success'))
        <div style="background: #e8f5e9; color: #2e7d32; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; border: 1px solid #c8e6c9;">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: #ffebee; color: #c62828; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; border: 1px solid #ffcdd2;">
            <strong>Gagal Memproses:</strong>
            <ul style="margin: 5px 0 0 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tab Filter Status -->
    <div class="filter-tabs">
        <a href="{{ route('petugas.reservations.index', ['status' => 'Pending']) }}" class="filter-tab {{ $statusFilter === 'Pending' ? 'active' : '' }}">Menunggu Persetujuan (Pending)</a>
        <a href="{{ route('petugas.reservations.index', ['status' => 'Approved']) }}" class="filter-tab {{ $statusFilter === 'Approved' ? 'active' : '' }}">Disetujui (Approved)</a>
        <a href="{{ route('petugas.reservations.index', ['status' => 'Rejected']) }}" class="filter-tab {{ $statusFilter === 'Rejected' ? 'active' : '' }}">Ditolak (Rejected)</a>
        <a href="{{ route('petugas.reservations.index', ['status' => 'all']) }}" class="filter-tab {{ $statusFilter === 'all' ? 'active' : '' }}">Semua Status</a>
    </div>

    <!-- Tabel Reservasi -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Permohonan Reservasi</h3>
            <span style="font-size: 12px; color: #666;">Total: {{ $reservations->total() }} Data</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 22%;">Fasilitas</th>
                    <th style="width: 18%;">Pemohon</th>
                    <th style="width: 22%;">Jadwal Penggunaan</th>
                    <th style="width: 18%;">Tujuan</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 10%; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $res)
                    <tr>
                        <td>
                            <span class="facility-badge">{{ $res->facility->name }}</span>
                            <span class="user-info">{{ $res->facility->location }} · Kapasitas {{ $res->facility->capacity }}</span>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #222; display: block;">{{ $res->user->name }}</span>
                            <span class="user-info">{{ $res->user->email }}</span>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #1565c0; display: block;">{{ \Carbon\Carbon::parse($res->reservation_date)->translatedFormat('d M Y') }}</span>
                            <span class="user-info">{{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }} WIB</span>
                        </td>
                        <td>
                            <span style="color: #444; font-size: 13px;">{{ $res->purpose }}</span>
                            @if($res->rejection_or_cancel_reason)
                                <div style="margin-top: 4px; font-size: 11px; color: #c62828;">
                                    <em>Catatan: {{ $res->rejection_or_cancel_reason }}</em>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($res->status === 'Pending')
                                <span class="status-badge status-pending">Pending</span>
                            @elseif($res->status === 'Approved')
                                <span class="status-badge status-approved">Disetujui</span>
                            @elseif($res->status === 'Rejected')
                                <span class="status-badge status-rejected">Ditolak</span>
                            @else
                                <span class="status-badge status-canceled">{{ $res->status }}</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            @if($res->status === 'Pending')
                                <div class="btn-action-group" style="justify-content: flex-end;">
                                    <!-- Tombol Setujui -->
                                    <form action="{{ route('petugas.reservations.approve', $res->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-approve" onclick="return confirm('Setujui reservasi {{ $res->facility->name }} untuk {{ $res->user->name }}?')">Setujui</button>
                                    </form>

                                    <!-- Tombol Buka Modal Tolak -->
                                    <button type="button" class="btn-reject" onclick="openRejectModal({{ $res->id }}, '{{ addslashes($res->facility->name) }}', '{{ addslashes($res->user->name) }}')">Tolak</button>
                                </div>
                            @elseif($res->status === 'Approved')
                                <div class="btn-action-group" style="justify-content: flex-end;">
                                    <button type="button" class="btn-reject" style="background: #e65100;" onclick="openCancelModal({{ $res->id }}, '{{ addslashes($res->facility->name) }}', '{{ addslashes($res->user->name) }}')">
                                        Batalkan (Darurat)
                                    </button>
                                </div>
                            @else
                                <span style="color: #999; font-size: 12px;">Diproses oleh: {{ $res->processor->name ?? 'Sistem' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 35px; color: #888;">
                            Tidak ada data reservasi dengan status <strong>{{ $statusFilter }}</strong>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($reservations->hasPages())
            <div style="padding: 15px 20px; border-top: 1px solid #f0f0f0;">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Form Penolakan (Reject Pending) -->
<div id="rejectModal" class="modal-backdrop">
    <div class="modal-box">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 8px; color: #111;">Tolak Permohonan Reservasi</h3>
        <p id="rejectModalDesc" style="font-size: 13px; color: #666; margin-bottom: 16px;"></p>

        <form id="rejectForm" method="POST">
            @csrf
            @method('PATCH')
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: #333;">Alasan Penolakan <span style="color:red;">*</span></label>
                <textarea name="reason" rows="3" required style="width: 100%; border: 1px solid #ccc; border-radius: 4px; padding: 10px; font-size: 13px; font-family: inherit;" placeholder="Contoh: Jadwal bentrok dengan acara akademik kampus..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeRejectModal()" style="padding: 8px 14px; background: #e0e0e0; border: none; border-radius: 4px; font-size: 13px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 8px 16px; background: #d32f2f; color: #fff; border: none; border-radius: 4px; font-size: 13px; font-weight: 600; cursor: pointer;">Tolak Reservasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Form Pembatalan Darurat (FR-10) -->
<div id="cancelModal" class="modal-backdrop">
    <div class="modal-box">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 8px; color: #e65100;">Pembatalan Darurat Reservasi</h3>
        <p id="cancelModalDesc" style="font-size: 13px; color: #666; margin-bottom: 16px;"></p>

        <form id="cancelForm" method="POST">
            @csrf
            @method('PATCH')
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: #333;">Alasan Pembatalan Darurat <span style="color:red;">*</span></label>
                <textarea name="cancel_reason" rows="3" required style="width: 100%; border: 1px solid #ccc; border-radius: 4px; padding: 10px; font-size: 13px; font-family: inherit;" placeholder="Contoh: Terjadi kebocoran atap lab mendadak / Listrik padam di gedung..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeCancelModal()" style="padding: 8px 14px; background: #e0e0e0; border: none; border-radius: 4px; font-size: 13px; cursor: pointer;">Tutup</button>
                <button type="submit" style="padding: 8px 16px; background: #e65100; color: #fff; border: none; border-radius: 4px; font-size: 13px; font-weight: 600; cursor: pointer;">Batalkan Reservasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(id, facilityName, userName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const desc = document.getElementById('rejectModalDesc');

        form.action = `/petugas/reservations/${id}/reject`;
        desc.innerText = `Menolak reservasi fasilitas ${facilityName} oleh pemohon ${userName}.`;
        modal.style.display = 'flex';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }

    function openCancelModal(id, facilityName, userName) {
        const modal = document.getElementById('cancelModal');
        const form = document.getElementById('cancelForm');
        const desc = document.getElementById('cancelModalDesc');

        form.action = `/petugas/reservations/${id}/cancel`;
        desc.innerText = `Membatalkan reservasi yang SUDAH DISETUJUI untuk fasilitas ${facilityName} oleh pemohon ${userName}.`;
        modal.style.display = 'flex';
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').style.display = 'none';
    }
</script>
@endsection
