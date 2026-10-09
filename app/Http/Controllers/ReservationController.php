<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        // Minimal H+3: reservasi wajib diajukan paling lambat 3 hari sebelum tanggal pemakaian.
        $minDate = Carbon::today()->addDays(3);

        $validated = $request->validate([
            'facility_id'      => ['required', 'exists:facilities,id'],
            'reservation_date' => [
                'required',
                'date',
                'after_or_equal:' . $minDate->toDateString(),
            ],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i', 'after:start_time'],
            'purpose'          => ['required', 'string', 'max:500'],
        ], [
            'reservation_date.after_or_equal' => 'Reservasi minimal diajukan 3 hari sebelum tanggal pemakaian.',
        ]);

        $open  = Carbon::parse('07:00');
        $close = Carbon::parse('20:00');
        $start = Carbon::parse($validated['start_time']);
        $end   = Carbon::parse($validated['end_time']);

        // SRS-12: Fasilitas hanya dapat dipesan jika statusnya 'Aktif'
        $facility = \App\Models\Facility::findOrFail($validated['facility_id']);
        if ($facility->status !== 'Aktif') {
            return back()->withErrors([
                'facility_id' => "Fasilitas '{$facility->name}' saat ini berstatus '{$facility->status}' dan tidak dapat dipesan."
            ])->withInput();
        }

        if ($start->lt($open) || $end->gt($close)) {
            return back()->withErrors(['start_time' => 'Waktu reservasi harus dalam jam operasional 07.00–20.00.'])->withInput();
        }

        if ($start->minute % 30 !== 0 || $end->minute % 30 !== 0) {
            return back()->withErrors(['start_time' => 'Waktu harus kelipatan slot 30 menit.'])->withInput();
        }

        // Cuma cek bentrok sama reservasi yang SUDAH Approved.
        // Reservasi Pending lain boleh numpuk di slot yang sama - nanti petugas
        // yang milih salah satu buat di-approve (lihat catatan di approve()).
        $conflict = Reservation::where('facility_id', $validated['facility_id'])
            ->where('reservation_date', $validated['reservation_date'])
            ->where('status', 'Approved')
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

        if ($conflict) {
            return back()->withErrors(['start_time' => 'Slot ini sudah disetujui untuk pemesan lain. Pilih slot lain.'])->withInput();
        }

        Reservation::create([
            'user_id'          => auth()->id(),
            'facility_id'      => $validated['facility_id'],
            'reservation_date' => $validated['reservation_date'],
            'start_time'       => $validated['start_time'],
            'end_time'         => $validated['end_time'],
            'purpose'          => $validated['purpose'],
            'status'           => 'Pending',
        ]);

        return redirect()
            ->route('facilities.show', ['facility' => $validated['facility_id'], 'date' => $validated['reservation_date']])
            ->with('status', 'Reservasi berhasil diajukan. Menunggu persetujuan petugas.');
    }
}