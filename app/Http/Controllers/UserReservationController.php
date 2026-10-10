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

public function cancel(Request $request, Reservation $reservation)
{
    // Cuma pemilik reservasi yang boleh batalin
    abort_unless($reservation->user_id === auth()->id(), 403);

    if (! $reservation->canBeCancelled()) {
        return back()->withErrors([
            'cancel' => 'Reservasi ini tidak bisa dibatalkan (sudah dimulai, selesai, atau statusnya sudah berubah).',
        ]);
    }

    $reservation->update([
        'status' => 'Cancelled',
        'rejection_or_cancel_reason' => $request->input('reason') ?: 'Dibatalkan oleh pemohon',
    ]);

    return redirect()
        ->route('reservations.index')
        ->with('success', 'Reservasi berhasil dibatalkan.');
}
}
