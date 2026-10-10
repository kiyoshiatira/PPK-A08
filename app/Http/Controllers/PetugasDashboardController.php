<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PetugasDashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // Stat cards
        $pendingCount    = Reservation::where('status', 'Pending')->count();
        $newReportCount  = Report::where('status', 'Baru')->count();
        $todayHandled    = Reservation::whereIn('status', ['Approved', 'Rejected', 'Canceled'])
            ->whereDate('updated_at', $now->toDateString())->count()
            + Report::whereIn('status', ['Diproses', 'Selesai', 'Ditolak'])
            ->whereDate('updated_at', $now->toDateString())->count();

        // Recent pending reservations (5 terbaru)
        $pendingReservations = Reservation::with(['user', 'facility'])
            ->where('status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent new reports (5 terbaru)
        $newReports = Report::with(['user', 'facility'])
            ->where('status', 'Baru')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact(
            'pendingCount',
            'newReportCount',
            'todayHandled',
            'pendingReservations',
            'newReports'
        ));
    }
}
