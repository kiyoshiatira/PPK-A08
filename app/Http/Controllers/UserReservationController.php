<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserReservationController extends Controller
{
public function index(Request $request)
{
    $query = Reservation::with(['facility', 'processor'])
        ->where('user_id', auth()->id());

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->whereHas('facility', fn ($q) => $q->where('name', 'like', "%{$search}%"));
    }

    if (in_array($request->input('status'), ['Pending', 'Approved', 'Rejected', 'Cancelled'], true)) {
        $query->where('status', $request->input('status'));
    }

    match ($request->input('period')) {
        'upcoming'   => $query->whereDate('reservation_date', '>=', today()),
        'this_month' => $query->whereMonth('reservation_date', now()->month)
                               ->whereYear('reservation_date', now()->year),
        'past'       => $query->whereDate('reservation_date', '<', today()),
        default      => null,
    };

    $reservations = $query
        ->orderByDesc('reservation_date')
        ->orderByDesc('start_time')
        ->paginate(10)
        ->withQueryString();

    return view('reservations.index', compact('reservations'));
}
}
