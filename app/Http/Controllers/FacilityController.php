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
        $date = $request->query('date', now()->toDateString());
        $date = Carbon::parse($date)->toDateString();

        $reservations = Reservation::where('facility_id', $facility->id)
            ->where('reservation_date', $date)
            ->whereIn('status', ['Pending', 'Approved'])
            ->get(['start_time', 'end_time']);

        $slots = [];
        $cursor = Carbon::parse("$date 07:00");
        $closing = Carbon::parse("$date 20:00");

        while ($cursor < $closing) {
            $slotEnd = $cursor->copy()->addMinutes(30);

            $available = $reservations->every(function ($r) use ($cursor, $slotEnd, $date) {
                $resStart = Carbon::parse("$date {$r->start_time}");
                $resEnd = Carbon::parse("$date {$r->end_time}");
                return $slotEnd <= $resStart || $cursor >= $resEnd;
            });

            $slots[] = ['time' => $cursor->format('H:i'), 'available' => $available];
            $cursor->addMinutes(30);
        }

        return view('facilities.show', [
            'facility' => $facility,
            'date' => Carbon::parse($date),
            'slots' => $slots,
        ]);
    }
}