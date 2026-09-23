<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasReservationController extends Controller
{
    /**
     * Menampilkan daftar reservasi masuk untuk petugas
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'Pending'); // Default tampilkan Pending

        $query = Reservation::with(['user', 'facility', 'processor'])
            ->orderBy('reservation_date', 'asc')
            ->orderBy('start_time', 'asc');

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $reservations = $query->paginate(10)->withQueryString();

        return view('petugas.reservations.index', compact('reservations', 'statusFilter'));
    }

    /**
     * FR-09: Petugas Menyetujui (Approve) Reservasi
     * Dengan validasi anti-bentrok jadwal di fasilitas yang sama
     */
    public function approve(Reservation $reservation)
    {
        // 1. Pastikan reservasi masih berstatus Pending
        if ($reservation->status !== 'Pending') {
            return back()->withErrors(['msg' => 'Hanya reservasi berstatus Pending yang dapat disetujui.']);
        }

        // 2. Cek apakah fasilitas sedang aktif
        if ($reservation->facility->status !== 'Aktif') {
            return back()->withErrors(['msg' => 'Fasilitas ini sedang berstatus "' . $reservation->facility->status . '", tidak dapat disetujui.']);
        }

        // 3. Logika Pencegahan Bentrok Jadwal (Anti Overlap)
        // Bentrok jika: (start_baru < end_lama) AND (end_baru > start_lama)
        $hasConflict = Reservation::where('facility_id', $reservation->facility_id)
            ->where('reservation_date', $reservation->reservation_date)
            ->where('status', 'Approved')
            ->where('id', '!=', $reservation->id)
            ->where(function ($query) use ($reservation) {
                $query->where('start_time', '<', $reservation->end_time)
                      ->where('end_time', '>', $reservation->start_time);
            })
            ->exists();

        if ($hasConflict) {
            return back()->withErrors([
                'msg' => 'Gagal menyetujui! Sudah ada reservasi lain yang disetujui pada fasilitas dan jam tersebut (Jadwal Bentrok).'
            ]);
        }

        // 4. Update status menjadi Approved & catat ID petugas yang memproses
        $reservation->update([
            'status'       => 'Approved',
            'processed_by' => Auth::id(),
        ]);

        return back()->with('success', 'Reservasi berhasil disetujui.');
    }

    /**
     * FR-09: Petugas Menolak (Reject) Reservasi
     */
    public function reject(Request $request, Reservation $reservation)
    {
        // 1. Pastikan status masih Pending
        if ($reservation->status !== 'Pending') {
            return back()->withErrors(['msg' => 'Hanya reservasi berstatus Pending yang dapat ditolak.']);
        }

        // 2. Validasi alasan penolakan
        $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        // 3. Update status menjadi Rejected & catat alasan serta petugas
        $reservation->update([
            'status'                     => 'Rejected',
            'rejection_or_cancel_reason' => $request->input('reason'),
            'processed_by'               => Auth::id(),
        ]);

        return back()->with('success', 'Reservasi telah ditolak.');
    }
}
