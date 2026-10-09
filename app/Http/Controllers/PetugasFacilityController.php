<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class PetugasFacilityController extends Controller
{
    /**
     * SRS-12: Menampilkan daftar fasilitas kampus untuk petugas
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status');
        $search       = $request->query('search');

        $query = Facility::query()->orderBy('name', 'asc');

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $facilities = $query->paginate(10)->withQueryString();

        return view('petugas.facilities.index', compact('facilities', 'statusFilter', 'search'));
    }

    /**
     * SRS-12: Petugas mengubah status fasilitas (Aktif, Dalam Perbaikan, Nonaktif)
     */
    public function updateStatus(Request $request, Facility $facility)
    {
        $request->validate([
            'status' => 'required|in:Aktif,Dalam Perbaikan,Nonaktif',
        ], [
            'status.required' => 'Status fasilitas wajib dipilih.',
            'status.in'       => 'Pilihan status fasilitas tidak valid.',
        ]);

        $oldStatus = $facility->status;
        $newStatus = $request->input('status');

        // Edge Case 1: Idempotency - jika status tidak berubah
        if ($oldStatus === $newStatus) {
            return back()->with('info', "Status fasilitas '{$facility->name}' sudah '{$newStatus}', tidak ada perubahan.");
        }

        // Edge Case 2: Petugas mencoba mengubah ke 'Aktif' padahal masih ada laporan kerusakan berstatus 'Diproses'
        if ($newStatus === 'Aktif') {
            $laporanDiproses = \App\Models\Report::where('facility_id', $facility->id)
                ->where('status', 'Diproses')
                ->count();

            if ($laporanDiproses > 0) {
                return back()->with('error', "Fasilitas '{$facility->name}' tidak dapat diubah ke 'Aktif' karena masih ada {$laporanDiproses} laporan kerusakan yang berstatus 'Diproses'. Harap selesaikan laporan terlebih dahulu.");
            }
        }

        // Simpan perubahan status
        $facility->update([
            'status' => $newStatus,
        ]);

        // Edge Case 3: Jika fasilitas diubah menjadi 'Dalam Perbaikan' atau 'Nonaktif',
        // cek apakah ada reservasi mendatang yang sudah disetujui (Approved) agar petugas aware
        $pesan = "Status fasilitas '{$facility->name}' berhasil diubah dari '{$oldStatus}' menjadi '{$newStatus}'.";

        if (in_array($newStatus, ['Dalam Perbaikan', 'Nonaktif'])) {
            $reservasiTerdampak = \App\Models\Reservation::where('facility_id', $facility->id)
                ->whereIn('status', ['Pending', 'Approved'])
                ->where('reservation_date', '>=', now()->toDateString())
                ->count();

            if ($reservasiTerdampak > 0) {
                $pesan .= " PERINGATAN: Terdapat {$reservasiTerdampak} reservasi mendatang (Pending/Approved) pada fasilitas ini. Anda dapat meninjau antrean reservasi untuk melakukan Pembatalan Darurat (SRS-10) jika diperlukan.";
            }
        }

        return back()->with('success', $pesan);
    }
}
