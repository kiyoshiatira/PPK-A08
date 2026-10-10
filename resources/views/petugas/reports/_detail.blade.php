<div class="detail-panel">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; gap: 12px;">
        <h2 class="detail-title">{{ $report->description }}</h2>
        <span class="sbadge {{ $report->status === 'Baru' ? 's-baru' : ($report->status === 'Diproses' ? 's-diproses' : ($report->status === 'Selesai' ? 's-selesai' : 's-ditolak')) }}" style="margin-top: 4px;">
            {{ $report->status }}
        </span>
    </div>
    <p class="detail-meta">
        {{ $report->facility->name ?? '-' }} &middot; Dilaporkan {{ $report->created_at->translatedFormat('d M Y') }}
    </p>

    <!-- Photo -->
    <div class="photo-box">
        @if($report->photo_path)
            <img src="{{ asset('storage/' . $report->photo_path) }}" alt="Bukti foto">
        @else
            <div style="text-align: center;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 8px; opacity: 0.5;"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                <div>Tidak ada foto yang dilampirkan</div>
            </div>
        @endif
    </div>

    <!-- Description -->
    <div class="desc-label">Deskripsi Kerusakan:</div>
    <p class="desc-text">{{ $report->description }}</p>

    @if($report->resolution_notes)
        <div class="desc-label">Catatan Resolusi Sebelumnya:</div>
        <div style="background: #f8fafc; border-left: 3px solid #2563eb; border-radius: 0 6px 6px 0; padding: 10px 14px; font-size: 13px; color: #475569; margin-bottom: 20px;">
            {{ $report->resolution_notes }}
        </div>
    @endif

    @if(in_array($report->status, ['Baru', 'Diproses']))
        <form action="{{ route('petugas.reports.updateStatus', $report->id) }}" method="POST" id="statusUpdateForm" class="status-action-box">
            @csrf
            @method('PATCH')

            <div class="status-box-header">Ubah status:</div>

            <!-- Dropdown Status -->
            <select name="status" id="statusSelectInput" class="status-select-action" onchange="syncPillFromSelect(this.value)">
                @if($report->status === 'Baru')
                    <option value="Diproses" selected>Diproses</option>
                @elseif($report->status === 'Diproses')
                    <option value="Diproses" selected>Diproses</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Ditolak">Ditolak</option>
                @endif
            </select>

            <!-- Status Step Pills -->
            <div class="status-pill-list">
                {{-- Baru pill --}}
                <button type="button" class="pill-step {{ $report->status === 'Baru' ? 'active-baru' : 'disabled' }}"
                        @if($report->status !== 'Baru') disabled @endif>
                    Baru
                </button>

                {{-- Diproses pill --}}
                <button type="button" id="pill-diproses"
                        class="pill-step {{ $report->status === 'Diproses' || $report->status === 'Baru' ? 'active' : '' }}"
                        onclick="selectStatusPill('Diproses')"
                        @if($report->status !== 'Baru' && $report->status !== 'Diproses') disabled @endif>
                    Diproses
                </button>

                {{-- Selesai pill --}}
                <button type="button" id="pill-selesai"
                        class="pill-step {{ $report->status === 'Baru' ? 'disabled' : '' }}"
                        onclick="selectStatusPill('Selesai')"
                        @if($report->status === 'Baru') disabled title="Laporan harus Diproses terlebih dahulu" @endif>
                    Selesai
                </button>

                {{-- Ditolak pill --}}
                <button type="button" id="pill-ditolak"
                        class="pill-step {{ $report->status === 'Baru' ? 'disabled' : '' }}"
                        onclick="selectStatusPill('Ditolak')"
                        @if($report->status === 'Baru') disabled title="Laporan harus Diproses terlebih dahulu" @endif>
                    Ditolak
                </button>
            </div>

            <div class="resolution-label">
                Catatan resolusi:
                <span id="notesRequiredStar" style="color: #ef4444; display: none;">*</span>
            </div>
            <textarea name="resolution_notes" id="detailResolutionNotes" class="resolution-textarea" placeholder="Tulis catatan tindakan perbaikan di sini..."></textarea>

            <div class="action-row">
                <button type="button" class="btn-close" onclick="resetDetailForm()">Tutup</button>
                <button type="submit" class="btn-save" id="btnSubmitStatus">Simpan perubahan</button>
            </div>
        </form>
    @else
        <!-- Final status -->
        <div style="padding: 24px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; text-align: center; color: #64748b; font-size: 13.5px;">
            Laporan ini telah diproses dengan status <strong>{{ $report->status }}</strong>
            @if($report->processor)
                oleh <strong>{{ $report->processor->name }}</strong>
            @endif.
            <div style="margin-top: 6px; font-size: 12.5px; color: #94a3b8;">
                Status akhir tidak dapat diubah lagi.
            </div>
        </div>
    @endif
</div>

<script>
    const currentReportStatus = "{{ $report->status }}";

    function syncPillFromSelect(val) {
        const pDiproses = document.getElementById('pill-diproses');
        const pSelesai = document.getElementById('pill-selesai');
        const pDitolak = document.getElementById('pill-ditolak');
        const star = document.getElementById('notesRequiredStar');

        if (pDiproses) pDiproses.className = 'pill-step' + (val === 'Diproses' ? ' active' : '');
        if (pSelesai) pSelesai.className = 'pill-step' + (val === 'Selesai' ? ' active-selesai' : (currentReportStatus === 'Baru' ? ' disabled' : ''));
        if (pDitolak) pDitolak.className = 'pill-step' + (val === 'Ditolak' ? ' active-ditolak' : (currentReportStatus === 'Baru' ? ' disabled' : ''));

        if (star) {
            star.style.display = (val === 'Selesai' || val === 'Ditolak') ? 'inline' : 'none';
        }
    }

    function selectStatusPill(status) {
        const select = document.getElementById('statusSelectInput');
        if (!select) return;

        // Cek jika opsi ada di select
        let optionExists = false;
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value === status) {
                optionExists = true;
                break;
            }
        }

        if (optionExists) {
            select.value = status;
            syncPillFromSelect(status);
        } else if (currentReportStatus === 'Baru' && (status === 'Selesai' || status === 'Ditolak')) {
            if (window.customAlert) {
                window.customAlert('Laporan berstatus Baru harus diproses terlebih dahulu sebelum dapat diselesaikan atau ditolak.', 'Tahap Belum Sesuai', 'warning');
            } else {
                alert('Laporan berstatus Baru harus diproses terlebih dahulu sebelum dapat diselesaikan atau ditolak.');
            }
        }
    }

    function resetDetailForm() {
        const textarea = document.getElementById('detailResolutionNotes');
        if (textarea) textarea.value = '';
        const select = document.getElementById('statusSelectInput');
        if (select) {
            select.selectedIndex = 0;
            syncPillFromSelect(select.value);
        }
    }

    // Validasi form saat submit dengan Custom Modal
    let isSubmittingStatus = false;
    document.getElementById('statusUpdateForm')?.addEventListener('submit', async function(e) {
        if (isSubmittingStatus) return;

        const select = document.getElementById('statusSelectInput');
        const notes = document.getElementById('detailResolutionNotes')?.value.trim() || '';
        const targetStatus = select ? select.value : '';

        if ((targetStatus === 'Selesai' || targetStatus === 'Ditolak') && notes.length < 5) {
            e.preventDefault();
            if (window.customAlert) {
                await window.customAlert('Catatan resolusi wajib diisi minimal 5 karakter untuk menyelesaikan atau menolak laporan.', 'Catatan Resolusi Kosong', 'warning');
            } else {
                alert('Catatan resolusi wajib diisi minimal 5 karakter untuk menyelesaikan atau menolak laporan.');
            }
            document.getElementById('detailResolutionNotes')?.focus();
            return false;
        }

        if (targetStatus === 'Diproses' && currentReportStatus === 'Baru') {
            e.preventDefault();
            let confirmed = true;
            if (window.customConfirm) {
                confirmed = await window.customConfirm('Tandai laporan ini sedang diproses oleh petugas operasional?', 'Mulai Penanganan', 'info');
            } else {
                confirmed = confirm('Tandai laporan ini sedang diproses oleh petugas?');
            }

            if (confirmed) {
                isSubmittingStatus = true;
                this.submit();
            }
            return false;
        }
    });

    // Jalankan sync saat render
    (function() {
        const sel = document.getElementById('statusSelectInput');
        if (sel) syncPillFromSelect(sel.value);
    })();
</script>
