<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function show(Request $request, Facility $facility)
    {
        // Minimal H+3: reservasi paling cepat 3 hari dari sekarang, gak ada batas atas.
        $minDate = Carbon::today()->addDays(3);

        $date = Carbon::parse($request->query('date', $minDate->toDateString()));

        // Clamp: gak boleh liat tanggal yang lebih mepet dari H+3
        if ($date->lt($minDate)) {
            $date = $minDate->copy();
        }

        // PENTING: cuma reservasi yang sudah Approved yang bikin slot jadi tidak tersedia.
        // Reservasi Pending TIDAK memblokir, biar petugas bisa milih mana yang di-approve
        // di antara beberapa pengajuan yang bentrok jadwal.
        $reservations = Reservation::where('facility_id', $facility->id)
            ->where('reservation_date', $date->toDateString())
            ->where('status', 'Approved')
            ->get(['start_time', 'end_time']);

        $slots = [];
        $cursor = Carbon::parse($date->toDateString() . ' 07:00');
        $closing = Carbon::parse($date->toDateString() . ' 20:00');

        while ($cursor < $closing) {
            $slotEnd = $cursor->copy()->addMinutes(30);

            $available = $reservations->every(function ($r) use ($cursor, $slotEnd, $date) {
                $resStart = Carbon::parse($date->toDateString() . " {$r->start_time}");
                $resEnd = Carbon::parse($date->toDateString() . " {$r->end_time}");
                return $slotEnd <= $resStart || $cursor >= $resEnd;
            });

            $slots[] = ['time' => $cursor->format('H:i'), 'available' => $available];
            $cursor->addMinutes(30);
        }

        return view('facilities.show', [
            'facility'  => $facility,
            'date'      => $date,
            'slots'     => $slots,
            'canGoPrev' => $date->gt($minDate),
            'canGoNext' => true,
        ]);
    }
}