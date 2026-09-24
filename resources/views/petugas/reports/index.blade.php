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
    .data-table td { padding: 14px 18px; border-bottom: 1px solid #f0f0f0; vertical-align: top; }
    .data-table tr:last-child td { border-bottom: none; }

    .status-badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .status-baru      { background: #e3f2fd; color: #1565c0; border: 1px solid #90caf9; }
    .status-diproses  { background: #fff8e1; color: #f57f17; border: 1px solid #ffe082; }
    .status-selesai   { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .status-ditolak   { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }

    .filter-tabs { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
    .filter-tab { padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 500; text-decoration: none; color: #666; background: #fff; border: 1px solid #e0e0e0; }
    .filter-tab.active { background: #1565c0; color: #fff; border-color: #1565c0; font-weight: 600; }

    .btn-proses   { background: #1565c0; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-proses:hover { background: #0d47a1; }
    .btn-selesai  { background: #2e7d32; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-selesai:hover { background: #1b5e20; }
    .btn-tolak    { background: #c62828; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-tolak:hover { background: #b71c1c; }

    /* Modal */
    .modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; justify-content: center; align-items: center; }
    .modal-box { background: #fff; border-radius: 8px; padding: 24px; max-width: 480px; width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
</style>

<div>
    <h1 class="page-title">Kelola Laporan Kerusakan Fasilitas</h1>
    <p class="page-subtitle">Tinjau dan proses laporan kerusakan dari pengguna. Catatan resolusi wajib diisi saat laporan diselesaikan atau ditolak.</p>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div style="background:#e8f5e9; color:#2e7d32; padding:12px 16px; border-radius:6px; margin-bottom:20px; font-size:13px; border:1px solid #c8e6c9;">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#ffebee; color:#c62828; padding:12px 16px; border-radius:6px; margin-bottom:20px; font-size:13px; border:1px solid #ffcdd2;">
            <strong>Gagal:</strong>
            <ul style="margin:5px 0 0 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tab Filter Status --}}
    <div class="filter-tabs">
        <a href="{{ route('petugas.reports.index', ['status' => 'Baru']) }}"      class="filter-tab {{ $statusFilter === 'Baru'     ? 'active' : '' }}">Baru</a>
        <a href="{{ route('petugas.reports.index', ['status' => 'Diproses']) }}"  class="filter-tab {{ $statusFilter === 'Diproses'  ? 'active' : '' }}">Sedang Diproses</a>
        <a href="{{ route('petugas.reports.index', ['status' => 'Selesai']) }}"   class="filter-tab {{ $statusFilter === 'Selesai'   ? 'active' : '' }}">Selesai</a>
        <a href="{{ route('petugas.reports.index', ['status' => 'Ditolak']) }}"   class="filter-tab {{ $statusFilter === 'Ditolak'   ? 'active' : '' }}">Ditolak</a>
        <a href="{{ route('petugas.reports.index', ['status' => 'all']) }}"       class="filter-tab {{ $statusFilter === 'all'       ? 'active' : '' }}">Semua</a>
    </div>

    {{-- Tabel Laporan --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Laporan Kerusakan</h3>
            <span style="font-size:12px; color:#666;">Total: {{ $reports->total() }} Laporan</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:20%;">Fasilitas</th>
                    <th style="width:15%;">Pelapor</th>
                    <th style="width:12%;">Kategori</th>
                    <th style="width:22%;">Deskripsi & Resolusi</th>
                    <th style="width:8%;">Bukti</th>
                    <th style="width:10%;">Status</th>
                    <th style="width:13%; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>
                            <span style="font-weight:600; color:#111; display:block;">{{ $report->facility->name }}</span>
                            <span style="color:#666; font-size:12px;">{{ $report->facility->location }}</span>
                        </td>
                        <td>
                            <span style="font-weight:600; color:#222; display:block;">{{ $report->user->name }}</span>
                            <span style="color:#666; font-size:12px;">{{ $report->created_at->format('d M Y') }}</span>
                        </td>
                        <td>
                            <span style="background:#f5f5f5; padding:3px 8px; border-radius:4px; font-size:12px; font-weight:500;">
                                {{ $report->category }}
                            </span>
                        </td>
                        <td>
                            <span style="color:#444; font-size:13px; display:block;">{{ $report->description }}</span>
                            @if($report->resolution_notes)
                                <div style="margin-top:6px; padding:6px 8px; background:#f9f9f9; border-left:3px solid #1565c0; border-radius:0 4px 4px 0; font-size:12px; color:#555;">
                                    <em>Resolusi: {{ $report->resolution_notes }}</em>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($report->photo_path)
                                <a href="{{ asset('storage/' . $report->photo_path) }}" target="_blank"
                                   style="color:#1565c0; font-size:12px; text-decoration:none; font-weight:600;">
                                    Lihat Foto ↗
                                </a>
                            @else
                                <span style="color:#bbb; font-size:12px;">Tidak ada</span>
                            @endif
                        </td>
                        <td>
                            @if($report->status === 'Baru')
                                <span class="status-badge status-baru">Baru</span>
                            @elseif($report->status === 'Diproses')
                                <span class="status-badge status-diproses">Diproses</span>
                            @elseif($report->status === 'Selesai')
                                <span class="status-badge status-selesai">Selesai</span>
                            @else
                                <span class="status-badge status-ditolak">Ditolak</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            @if($report->status === 'Baru')
                                {{-- Hanya tombol Proses (Baru → Diproses) --}}
                                <form action="{{ route('petugas.reports.updateStatus', $report->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Diproses">
                                    <button type="submit" class="btn-proses"
                                        onclick="return confirm('Tandai laporan ini sedang diproses?')">
                                        Proses
                                    </button>
                                </form>

                            @elseif($report->status === 'Diproses')
                                {{-- Tombol Selesai & Tolak (keduanya butuh catatan resolusi via modal) --}}
                                <div style="display:flex; gap:6px; justify-content:flex-end;">
                                    <button class="btn-selesai"
                                        onclick="openResolveModal({{ $report->id }}, 'Selesai', '{{ addslashes($report->facility->name) }}')">
                                        Selesai
                                    </button>
                                    <button class="btn-tolak"
                                        onclick="openResolveModal({{ $report->id }}, 'Ditolak', '{{ addslashes($report->facility->name) }}')">
                                        Tolak
                                    </button>
                                </div>

                            @else
                                {{-- Status sudah final --}}
                                <span style="color:#999; font-size:12px;">
                                    Oleh: {{ $report->processor->name ?? '-' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:40px; color:#888;">
                            Tidak ada laporan dengan status <strong>{{ $statusFilter }}</strong>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($reports->hasPages())
            <div style="padding:15px 20px; border-top:1px solid #f0f0f0;">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Catatan Resolusi (untuk Selesai / Ditolak) --}}
<div id="resolveModal" class="modal-backdrop">
    <div class="modal-box">
        <h3 id="resolveModalTitle" style="font-size:16px; font-weight:700; margin-bottom:8px; color:#111;"></h3>
        <p id="resolveModalDesc" style="font-size:13px; color:#666; margin-bottom:16px;"></p>

        <form id="resolveForm" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" id="resolveStatusInput" name="status">

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#333;">
                    Catatan Resolusi / Alasan <span style="color:red;">*</span>
                </label>
                <textarea name="resolution_notes" id="resolutionNotesInput" rows="4" required
                    style="width:100%; border:1px solid #ccc; border-radius:4px; padding:10px; font-size:13px; font-family:inherit;"
                    placeholder="Contoh: AC sudah diperbaiki oleh teknisi, kini berfungsi normal..."></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeResolveModal()"
                    style="padding:8px 14px; background:#e0e0e0; border:none; border-radius:4px; font-size:13px; cursor:pointer;">
                    Batal
                </button>
                <button type="submit" id="resolveSubmitBtn"
                    style="padding:8px 16px; color:#fff; border:none; border-radius:4px; font-size:13px; font-weight:600; cursor:pointer;">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openResolveModal(id, status, facilityName) {
        const modal       = document.getElementById('resolveModal');
        const form        = document.getElementById('resolveForm');
        const title       = document.getElementById('resolveModalTitle');
        const desc        = document.getElementById('resolveModalDesc');
        const statusInput = document.getElementById('resolveStatusInput');
        const submitBtn   = document.getElementById('resolveSubmitBtn');
        const notes       = document.getElementById('resolutionNotesInput');

        form.action   = `/petugas/reports/${id}/status`;
        statusInput.value = status;
        notes.value   = '';

        if (status === 'Selesai') {
            title.innerText      = 'Selesaikan Laporan';
            title.style.color    = '#2e7d32';
            desc.innerText       = `Laporan kerusakan fasilitas "${facilityName}" akan ditandai sebagai Selesai.`;
            submitBtn.style.background = '#2e7d32';
            submitBtn.innerText  = 'Tandai Selesai';
            notes.placeholder    = 'Contoh: Kerusakan sudah diperbaiki oleh teknisi. AC kini berfungsi normal...';
        } else {
            title.innerText      = 'Tolak Laporan';
            title.style.color    = '#c62828';
            desc.innerText       = `Laporan kerusakan fasilitas "${facilityName}" akan ditolak.`;
            submitBtn.style.background = '#c62828';
            submitBtn.innerText  = 'Tolak Laporan';
            notes.placeholder    = 'Contoh: Laporan tidak valid, tidak ditemukan kerusakan saat pengecekan...';
        }

        modal.style.display = 'flex';
    }

    function closeResolveModal() {
        document.getElementById('resolveModal').style.display = 'none';
    }
</script>
@endsection
