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
     * Dengan penanganan seluruh edge cases:
     * - DB Transaction & row lock (anti race condition)
     * - Cegah approve reservasi masa lalu / kadaluwarsa
     * - Cek status operasional fasilitas & laporan kerusakan berstatus Diproses
     * - Cek bentrok jadwal (anti overlap)
     * - Safe notification (user null safety)
     */
    public function approve(Reservation $reservation)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($reservation) {
            // Lock row untuk mencegah race condition antar petugas
            $res = Reservation::lockForUpdate()->find($reservation->id);

            if (!$res) {
                return back()->withErrors(['msg' => 'Reservasi tidak ditemukan.']);
            }

            // Edge Case 1: Status sudah bukan Pending (misal sudah diproses petugas lain)
            if ($res->status !== 'Pending') {
                return back()->withErrors(['msg' => "Reservasi ini sudah diproses sebelumnya dengan status '{$res->status}'."]);
            }

            // Edge Case 2: Waktu reservasi sudah lewat / kadaluwarsa (Expired)
            $startDateTime = \Carbon\Carbon::parse("{$res->reservation_date} {$res->start_time}");
            if ($startDateTime->isPast()) {
                return back()->withErrors([
                    'msg' => 'Gagal menyetujui! Waktu pelaksanaan reservasi sudah terlewat di masa lalu. Harap tolak reservasi ini.'
                ]);
            }

            // Edge Case 3: Fasilitas tidak aktif
            if ($res->facility->status !== 'Aktif') {
                return back()->withErrors([
                    'msg' => "Fasilitas '{$res->facility->name}' saat ini berstatus '{$res->facility->status}' dan tidak dapat disetujui."
                ]);
            }

            // Edge Case 4: Ada laporan kerusakan yang sedang diproses pada fasilitas ini
            $adaLaporanDiproses = \App\Models\Report::where('facility_id', $res->facility_id)
                ->where('status', 'Diproses')
                ->exists();
            if ($adaLaporanDiproses) {
                return back()->withErrors([
                    'msg' => "Fasilitas '{$res->facility->name}' memiliki laporan kerusakan yang sedang 'Diproses'. Selesaikan perbaikan terlebih dahulu sebelum menyetujui reservasi."
                ]);
            }

            // Edge Case 5: Pencegahan Bentrok Jadwal (Anti Overlap)
            $hasConflict = Reservation::where('facility_id', $res->facility_id)
                ->where('reservation_date', $res->reservation_date)
                ->where('status', 'Approved')
                ->where('id', '!=', $res->id)
                ->where(function ($query) use ($res) {
                    $query->where('start_time', '<', $res->end_time)
                          ->where('end_time', '>', $res->start_time);
                })
                ->exists();

            if ($hasConflict) {
                return back()->withErrors([
                    'msg' => 'Gagal menyetujui! Sudah ada reservasi lain yang disetujui pada fasilitas dan jam tersebut (Jadwal Bentrok).'
                ]);
            }

            // Update status menjadi Approved & catat ID petugas
            $res->update([
                'status'       => 'Approved',
                'processed_by' => Auth::id(),
            ]);

            // Edge Case 6: Safe notification (pastikan user masih ada di database)
            if ($res->user) {
                Notification::create([
                    'user_id' => $res->user_id,
                    'title'   => 'Reservasi Disetujui',
                    'message' => "Reservasi Anda untuk fasilitas {$res->facility->name} pada tanggal {$res->reservation_date} ({$res->start_time} - {$res->end_time}) telah disetujui oleh petugas.",
                    'type'    => 'reservasi',
                    'link'    => route('reservations.index'),
                ]);
            }

            return back()->with('success', "Reservasi untuk fasilitas '{$res->facility->name}' berhasil disetujui.");
        });
    }

    /**
     * FR-09: Petugas Menolak (Reject) Reservasi
     * Dengan penanganan seluruh edge cases:
     * - DB Transaction & row lock
     * - Sanitasi alasan penolakan (tidak boleh spasi kosong)
     * - Safe notification
     */
    public function reject(Request $request, Reservation $reservation)
    {
        // Edge Case 7: Sanitasi input alasan penolakan (trim whitespace)
        $reason = trim($request->input('reason', ''));
        if (empty($reason)) {
            return back()->withErrors(['reason' => 'Alasan penolakan wajib diisi dan tidak boleh hanya berupa spasi.']);
        }

        if (mb_strlen($reason) > 500) {
            return back()->withErrors(['reason' => 'Alasan penolakan maksimal 500 karakter.']);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($reservation, $reason) {
            $res = Reservation::lockForUpdate()->find($reservation->id);

            if (!$res) {
                return back()->withErrors(['msg' => 'Reservasi tidak ditemukan.']);
            }

            // Pastikan status masih Pending
            if ($res->status !== 'Pending') {
                return back()->withErrors(['msg' => "Hanya reservasi berstatus Pending yang dapat ditolak. Saat ini berstatus '{$res->status}'."]);
            }

            // Update status menjadi Rejected
            $res->update([
                'status'                     => 'Rejected',
                'rejection_or_cancel_reason' => $reason,
                'processed_by'               => Auth::id(),
            ]);

            // Safe notification
            if ($res->user) {
                Notification::create([
                    'user_id' => $res->user_id,
                    'title'   => 'Reservasi Ditolak',
                    'message' => "Reservasi Anda untuk fasilitas {$res->facility->name} ditolak. Alasan: {$reason}",
                    'type'    => 'reservasi',
                    'link'    => route('reservations.index'),
                ]);
            }

            return back()->with('success', "Reservasi untuk fasilitas '{$res->facility->name}' telah ditolak.");
        });
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
