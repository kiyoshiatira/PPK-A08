<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Notification;
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
        $statusFilter = $request->query('status', '');
        $timeFilter   = $request->query('time', 'all');

        $query = Report::with(['user', 'facility', 'processor'])
            ->orderBy('created_at', 'desc');

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($timeFilter === 'today') {
            $query->whereDate('created_at', \Carbon\Carbon::today());
        } elseif ($timeFilter === 'this_week') {
            $query->whereBetween('created_at', [
                \Carbon\Carbon::now()->startOfWeek(),
                \Carbon\Carbon::now()->endOfWeek(),
            ]);
        } elseif ($timeFilter === 'this_month') {
            $query->whereMonth('created_at', \Carbon\Carbon::now()->month)
                  ->whereYear('created_at', \Carbon\Carbon::now()->year);
        }

        $reports = $query->paginate(30)->withQueryString();

        return view('petugas.reports.index', compact('reports', 'statusFilter', 'timeFilter'));
    }

    /**
     * Detail panel partial for AJAX sidebar click
     */
    public function detail(Report $report)
    {
        $report->load(['user', 'facility', 'processor']);
        return view('petugas.reports._detail', compact('report'));
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
        $notes = trim($request->input('resolution_notes', ''));

        // Edge Case 1: Jika status baru adalah Selesai atau Ditolak, catatan resolusi WAJIB diisi dan tidak boleh hanya spasi
        if (in_array($newStatus, ['Selesai', 'Ditolak'])) {
            if (empty($notes)) {
                return back()->withErrors([
                    'resolution_notes' => 'Catatan resolusi wajib diisi dan tidak boleh hanya berupa spasi saat laporan diselesaikan atau ditolak.'
                ]);
            }

            if (mb_strlen($notes) < 5) {
                return back()->withErrors([
                    'resolution_notes' => 'Catatan resolusi minimal 5 karakter agar memberikan kejelasan bagi pelapor.'
                ]);
            }
        }

        // 2. Transaksi Database & Pesimistic Lock untuk Concurrency Safety
        return \Illuminate\Support\Facades\DB::transaction(function () use ($report, $newStatus, $notes) {
            $rep = Report::lockForUpdate()->find($report->id);

            if (!$rep) {
                return back()->withErrors(['msg' => 'Data laporan tidak ditemukan.']);
            }

            // Edge Case 2: Idempotency (jika status sudah sama)
            if ($rep->status === $newStatus) {
                return back()->with('info', "Laporan ini sudah berstatus '{$newStatus}' sebelumnya.");
            }

            // 3. Cek perpindahan status yang diizinkan
            $allowedTransitions = [
                'Baru'     => ['Diproses'],
                'Diproses' => ['Selesai', 'Ditolak'],
            ];

            // Jika status laporan sudah final (Selesai/Ditolak) tidak bisa diubah lagi
            if (!isset($allowedTransitions[$rep->status])) {
                return back()->withErrors([
                    'msg' => "Status laporan ini sudah final ('{$rep->status}') dan tidak dapat diubah lagi."
                ]);
            }

            // Jika status baru tidak sesuai alur yang diizinkan
            if (!in_array($newStatus, $allowedTransitions[$rep->status])) {
                return back()->withErrors([
                    'msg' => "Status tidak dapat diubah dari '{$rep->status}' ke '{$newStatus}'."
                ]);
            }

            // 4. Update status laporan
            $rep->update([
                'status'           => $newStatus,
                'resolution_notes' => in_array($newStatus, ['Selesai', 'Ditolak']) ? $notes : null,
                'processed_by'     => Auth::id(),
            ]);

            // SRS-12: Konsekuensi otomatis perubahan status fasilitas (dengan null-safety)
            if ($rep->facility) {
                if ($newStatus === 'Diproses') {
                    $rep->facility->update(['status' => 'Dalam Perbaikan']);
                } elseif (in_array($newStatus, ['Selesai', 'Ditolak'])) {
                    $masihAdaYangDiproses = \App\Models\Report::where('facility_id', $rep->facility_id)
                        ->where('status', 'Diproses')
                        ->where('id', '!=', $rep->id) // Kecualikan laporan yang baru saja diselesaikan
                        ->exists();

                    if (!$masihAdaYangDiproses) {
                        // Tidak ada laporan lain yang masih Diproses → fasilitas boleh kembali Aktif
                        $rep->facility->update(['status' => 'Aktif']);
                    }
                    // Jika masih ada laporan lain yang Diproses → fasilitas tetap "Dalam Perbaikan"
                }
            }

            // FR-12: Buat notifikasi untuk Pengguna pelapor (dengan null-safety)
            if ($rep->user) {
                $facilityName = $rep->facility ? $rep->facility->name : 'Fasilitas Terkait';
                $pesanNotif = match ($newStatus) {
                    'Diproses' => "Laporan kerusakan fasilitas {$facilityName} sedang ditangani oleh petugas.",
                    'Selesai'  => "Laporan kerusakan fasilitas {$facilityName} telah selesai ditangani. Catatan: {$notes}",
                    'Ditolak'  => "Laporan kerusakan fasilitas {$facilityName} ditolak. Alasan: {$notes}",
                    default    => "Status laporan kerusakan Anda diperbarui menjadi {$newStatus}.",
                };

                Notification::create([
                    'user_id' => $rep->user_id,
                    'title'   => "Status Laporan: {$newStatus}",
                    'message' => $pesanNotif,
                    'type'    => 'laporan',
                    'link'    => route('reports.index'),
                ]);
            }

            $pesan = match ($newStatus) {
                'Diproses' => 'Laporan ditandai sedang diproses.',
                'Selesai'  => 'Laporan berhasil diselesaikan.',
                'Ditolak'  => 'Laporan telah ditolak.',
                default    => 'Status laporan berhasil diperbarui.',
            };

            return back()->with('success', $pesan);
        });
    }
}
