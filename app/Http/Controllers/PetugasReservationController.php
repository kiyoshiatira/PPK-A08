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
     * SRS-10: Halaman Khusus Antrean Reservasi / Pembatalan Darurat
     */
    public function emergencyCancelIndex(Request $request)
    {
        $search = $request->query('search');

        $query = Reservation::with(['user', 'facility', 'processor'])
            ->where('status', 'Approved')
            ->orderBy('reservation_date', 'asc')
            ->orderBy('start_time', 'asc');

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

        $approvedReservations = $query->get();

        return view('petugas.reservations.emergency_cancel', compact('approvedReservations', 'search'));
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
