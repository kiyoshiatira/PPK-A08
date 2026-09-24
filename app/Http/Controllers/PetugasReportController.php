<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasReportController extends Controller
{
    /**
     * FR-11: Menampilkan daftar laporan kerusakan fasilitas
     * Bisa difilter berdasarkan status (Baru, Diproses, Selesai, Ditolak)
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'Baru'); // Default tampilkan Baru

        $query = Report::with(['user', 'facility', 'processor'])
            ->orderBy('created_at', 'desc');

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $reports = $query->paginate(10)->withQueryString();

        return view('petugas.reports.index', compact('reports', 'statusFilter'));
    }

    /**
     * FR-11: Petugas mengubah status laporan kerusakan
     * Alur status yang diizinkan:
     * - Baru        → Diproses
     * - Diproses    → Selesai / Ditolak
     * - Selesai / Ditolak → TIDAK BISA diubah (sudah final)
     *
     * Aturan: resolution_notes WAJIB diisi jika status baru = Selesai / Ditolak
     */
    public function updateStatus(Request $request, Report $report)
    {
        // 1. Validasi status yang dikirimkan
        $request->validate([
            'status'           => 'required|in:Diproses,Selesai,Ditolak',
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        $newStatus = $request->input('status');

        // 2. Cek perpindahan status yang diizinkan
        $allowedTransitions = [
            'Baru'     => ['Diproses'],
            'Diproses' => ['Selesai', 'Ditolak'],
        ];

        // Jika status laporan sudah final (Selesai/Ditolak) tidak bisa diubah lagi
        if (!isset($allowedTransitions[$report->status])) {
            return back()->withErrors([
                'msg' => 'Status laporan ini sudah final dan tidak dapat diubah lagi.'
            ]);
        }

        // Jika status baru tidak sesuai alur yang diizinkan
        if (!in_array($newStatus, $allowedTransitions[$report->status])) {
            return back()->withErrors([
                'msg' => "Status tidak dapat diubah dari '{$report->status}' ke '{$newStatus}'."
            ]);
        }

        // 3. Jika status baru adalah Selesai atau Ditolak, catatan resolusi WAJIB diisi
        if (in_array($newStatus, ['Selesai', 'Ditolak'])) {
            $request->validate([
                'resolution_notes' => 'required|string|max:1000',
            ], [
                'resolution_notes.required' => 'Catatan resolusi wajib diisi saat laporan diselesaikan atau ditolak.',
            ]);
        }

        // 4. Update status laporan
        $report->update([
            'status'           => $newStatus,
            'resolution_notes' => $request->input('resolution_notes'),
            'processed_by'     => Auth::id(),
        ]);

        $pesan = match ($newStatus) {
            'Diproses' => 'Laporan ditandai sedang diproses.',
            'Selesai'  => 'Laporan berhasil diselesaikan.',
            'Ditolak'  => 'Laporan telah ditolak.',
            default    => 'Status laporan berhasil diperbarui.',
        };

        return back()->with('success', $pesan);
    }
}
