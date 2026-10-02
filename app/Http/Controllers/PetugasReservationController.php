<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasReservationController extends Controller
{
    /**
     * Menampilkan daftar reservasi masuk untuk petugas
     */
    public function index(Request $request)
    {
        $statusFilter   = $request->query('status', 'all');
        $search         = $request->query('search');
        $facilityFilter = $request->query('facility_id');
        $dateFilter     = $request->query('date_filter', 'all');

        $query = Reservation::with(['user', 'facility', 'processor'])
            ->orderBy('reservation_date', 'desc')
            ->orderBy('start_time', 'asc');

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($facilityFilter && $facilityFilter !== 'all') {
            $query->where('facility_id', $facilityFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('facility', function ($fq) use ($search) {
                      $fq->where('name', 'like', "%{$search}%")
                         ->orWhere('location', 'like', "%{$search}%");
                  });
            });
        }

        if ($dateFilter === 'today') {
            $query->whereDate('reservation_date', now()->toDateString());
        } elseif ($dateFilter === 'this_week') {
            $query->whereBetween('reservation_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]);
        }

        $reservations = $query->paginate(12)->withQueryString();

        // Hitung konflik bentrok jadwal untuk masing-masing reservasi Pending
        foreach ($reservations as $res) {
            if ($res->status === 'Pending') {
                $res->has_conflict = Reservation::where('facility_id', $res->facility_id)
                    ->where('reservation_date', $res->reservation_date)
                    ->where('status', 'Approved')
                    ->where('id', '!=', $res->id)
                    ->where(function ($q) use ($res) {
                        $q->where('start_time', '<', $res->end_time)
                          ->where('end_time', '>', $res->start_time);
                    })
                    ->exists();
            } else {
                $res->has_conflict = false;
            }
        }

        $facilities = \App\Models\Facility::orderBy('name')->get();

        return view('petugas.reservations.index', compact(
            'reservations',
            'statusFilter',
            'search',
            'facilityFilter',
            'dateFilter',
            'facilities'
        ));
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

        // FR-12: Buat notifikasi untuk Pengguna
        Notification::create([
            'user_id' => $reservation->user_id,
            'title'   => 'Reservasi Disetujui',
            'message' => "Reservasi Anda untuk fasilitas {$reservation->facility->name} pada tanggal {$reservation->reservation_date} ({$reservation->start_time} - {$reservation->end_time}) telah disetujui oleh petugas.",
            'type'    => 'reservasi',
            'link'    => route('reservations.index'),
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

        // FR-12: Buat notifikasi untuk Pengguna
        Notification::create([
            'user_id' => $reservation->user_id,
            'title'   => 'Reservasi Ditolak',
            'message' => "Reservasi Anda untuk fasilitas {$reservation->facility->name} ditolak. Alasan: {$request->input('reason')}",
            'type'    => 'reservasi',
            'link'    => route('reservations.index'),
        ]);

        return back()->with('success', 'Reservasi telah ditolak.');
    }

    /**
     * FR-10: Pembatalan Darurat oleh Petugas
     * Membatalkan reservasi yang sudah Approved dengan alasan wajib
     */
    public function emergencyCancel(Request $request, Reservation $reservation)
    {
        // 1. Validasi status: hanya yang Approved yang bisa dibatalkan darurat
        if ($reservation->status !== 'Approved') {
            return back()->withErrors(['msg' => 'Hanya reservasi yang sudah disetujui (Approved) yang dapat dibatalkan secara darurat.']);
        }

        // 2. Validasi input: alasan pembatalan wajib diisi
        $request->validate([
            'cancel_reason' => 'required|string|max:500',
        ], [
            'cancel_reason.required' => 'Alasan pembatalan darurat wajib diisi.',
        ]);

        // 3. Update status menjadi Canceled
        $reservation->update([
            'status'                     => 'Canceled',
            'rejection_or_cancel_reason' => $request->input('cancel_reason'),
            'processed_by'               => Auth::id(),
        ]);

        // FR-12: Buat notifikasi untuk Pengguna
        Notification::create([
            'user_id' => $reservation->user_id,
            'title'   => 'Pembatalan Darurat Reservasi',
            'message' => "Reservasi Anda untuk fasilitas {$reservation->facility->name} pada tanggal {$reservation->reservation_date} dibatalkan secara darurat oleh petugas. Alasan: {$request->input('cancel_reason')}",
            'type'    => 'reservasi',
            'link'    => route('reservations.index'),
        ]);

        return back()->with('success', 'Reservasi berhasil dibatalkan secara darurat.');
    }
}
