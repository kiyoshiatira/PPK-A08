<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class UserReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['facility', 'processor'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('reservations.index', compact('reservations'));
    }
}
