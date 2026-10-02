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
     * Menangani seluruh edge cases:
     * - DB Transaction & row lock
     * - Cegah pembatalan untuk kegiatan yang sudah selesai di masa lalu
     * - Sanitasi alasan pembatalan darurat (tidak boleh hanya spasi)
     * - Safe notification
     */
    public function emergencyCancel(Request $request, Reservation $reservation)
    {
        // Edge Case 1: Sanitasi alasan pembatalan darurat
        $reason = trim($request->input('cancel_reason', ''));
        if (empty($reason)) {
            return back()->withErrors(['cancel_reason' => 'Alasan pembatalan darurat wajib diisi dan tidak boleh hanya berupa spasi.']);
        }

        if (mb_strlen($reason) < 5) {
            return back()->withErrors(['cancel_reason' => 'Alasan pembatalan darurat minimal 5 karakter agar informatif bagi pengguna.']);
        }

        if (mb_strlen($reason) > 500) {
            return back()->withErrors(['cancel_reason' => 'Alasan pembatalan darurat maksimal 500 karakter.']);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($reservation, $reason) {
            $res = Reservation::lockForUpdate()->find($reservation->id);

            if (!$res) {
                return back()->withErrors(['msg' => 'Reservasi tidak ditemukan.']);
            }

            // Edge Case 2: Pastikan status masih Approved
            if ($res->status !== 'Approved') {
                return back()->withErrors(['msg' => "Hanya reservasi yang sudah disetujui (Approved) yang dapat dibatalkan secara darurat. Status saat ini: '{$res->status}'."]);
            }

            // Edge Case 3: Cegah pembatalan darurat jika kegiatan sudah selesai di masa lalu
            $endDateTime = \Carbon\Carbon::parse("{$res->reservation_date} {$res->end_time}");
            if ($endDateTime->isPast()) {
                return back()->withErrors([
                    'msg' => 'Gagal membatalkan! Kegiatan reservasi ini sudah selesai dilaksanakan di masa lalu dan tidak dapat dibatalkan.'
                ]);
            }

            // Update status menjadi Canceled
            $res->update([
                'status'                     => 'Canceled',
                'rejection_or_cancel_reason' => $reason,
                'processed_by'               => Auth::id(),
            ]);

            // Edge Case 4: Safe notification
            if ($res->user) {
                Notification::create([
                    'user_id' => $res->user_id,
                    'title'   => 'Pembatalan Darurat Reservasi',
                    'message' => "Reservasi Anda untuk fasilitas {$res->facility->name} pada tanggal {$res->reservation_date} ({$res->start_time} - {$res->end_time}) dibatalkan secara darurat oleh petugas. Alasan: {$reason}",
                    'type'    => 'reservasi',
                    'link'    => route('reservations.index'),
                ]);
            }

            return back()->with('success', "Reservasi fasilitas '{$res->facility->name}' berhasil dibatalkan secara darurat.");
        });
    }
}
