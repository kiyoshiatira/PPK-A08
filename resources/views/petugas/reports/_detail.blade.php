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
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 8px; opacity: 0.4;"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
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
        <!-- Status flow indicator -->
        <div class="status-label">Status saat ini:</div>
        <div class="status-flow">
            <span class="sf-pill {{ $report->status === 'Baru' ? 'current' : 'done' }}">Baru</span>
            <span class="sf-pill {{ $report->status === 'Diproses' ? 'current' : '' }}">Diproses</span>
            <span class="sf-pill">Selesai</span>
            <span class="sf-pill">Ditolak</span>
        </div>

        @if($report->status === 'Baru')
            <!-- Baru → Proses -->
            <form action="{{ route('petugas.reports.updateStatus', $report->id) }}" method="POST" style="margin: 0;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="Diproses">
                <div class="resolution-label">Catatan Resolusi (opsional untuk tahap ini):</div>
                <textarea name="resolution_notes" class="resolution-textarea" placeholder="Opsional: tambahkan catatan penanganan awal..."></textarea>
                <div class="action-row">
                    <button type="submit" class="btn-save"
                        onclick="return confirm('Tandai laporan ini sedang diproses oleh petugas?')">
                        Tandai Diproses
                    </button>
                </div>
            </form>

        @elseif($report->status === 'Diproses')
            <!-- Diproses → Selesai/Ditolak -->
            <div id="actionFormArea">
                <div class="resolution-label">Catatan Resolusi: <span style="color:#ef4444;">*</span></div>
                <textarea id="resolutionNotes" class="resolution-textarea" placeholder="Tulis catatan tindakan perbaikan di sini..."></textarea>
                <div class="action-row">
                    <button type="button" class="btn-close" onclick="document.getElementById('resolutionNotes').value = ''">Tutup</button>
                    <button type="button" class="btn-save" onclick="submitStatus('Ditolak')" style="background: #dc2626;">
                        Tolak
                    </button>
                    <button type="button" class="btn-save" onclick="submitStatus('Selesai')">
                        Simpan Perubahan
                    </button>
                </div>
            </div>

            <form id="resolveFormInline" action="{{ route('petugas.reports.updateStatus', $report->id) }}" method="POST" style="display:none;">
                @csrf
                @method('PATCH')
                <input type="hidden" id="hiddenStatus" name="status" value="">
                <input type="hidden" id="hiddenNotes" name="resolution_notes" value="">
            </form>
        @endif

    @else
        <!-- Final status -->
        <div style="padding: 20px; background: #f8fafc; border-radius: 8px; text-align: center; color: #64748b; font-size: 13.5px;">
            Laporan ini telah diproses dengan status <strong>{{ $report->status }}</strong>
            @if($report->processor)
                oleh <strong>{{ $report->processor->name }}</strong>
            @endif.
        </div>
    @endif
</div>

<script>
    function submitStatus(status) {
        const notes = document.getElementById('resolutionNotes')?.value || '';
        if (!notes.trim()) {
            alert('Catatan resolusi wajib diisi sebelum mengubah status.');
            return;
        }
        document.getElementById('hiddenStatus').value = status;
        document.getElementById('hiddenNotes').value  = notes;
        document.getElementById('resolveFormInline').submit();
    }
</script>
