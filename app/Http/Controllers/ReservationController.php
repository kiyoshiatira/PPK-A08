<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id'      => ['required', 'exists:facilities,id'],
            'reservation_date' => ['required', 'date'],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i', 'after:start_time'],
            'purpose'          => ['required', 'string', 'max:500'],
        ]);

        $open  = Carbon::parse('07:00');
        $close = Carbon::parse('20:00');
        $start = Carbon::parse($validated['start_time']);
        $end   = Carbon::parse($validated['end_time']);

        if ($start->lt($open) || $end->gt($close)) {
            return back()->withErrors(['start_time' => 'Waktu reservasi harus dalam jam operasional 07.00–20.00.'])->withInput();
        }

        if ($start->minute % 30 !== 0 || $end->minute % 30 !== 0) {
            return back()->withErrors(['start_time' => 'Waktu harus kelipatan slot 30 menit.'])->withInput();
        }

        $conflict = Reservation::where('facility_id', $validated['facility_id'])
            ->where('reservation_date', $validated['reservation_date'])
            ->whereIn('status', ['Pending', 'Approved'])
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

        if ($conflict) {
            return back()->withErrors(['start_time' => 'Slot ini sudah dipesan atau masih diproses. Pilih slot lain.'])->withInput();
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