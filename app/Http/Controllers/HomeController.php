<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Report;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Guest gak punya data personal buat ditampilin, arahkan ke daftar fasilitas
        if (! auth()->check()) {
            return redirect()->route('facilities.index');
        }

        $userId = auth()->id();

        // Status yang dianggap "aktif" untuk reservasi & laporan
        $activeReservationStatuses = ['Pending', 'Approved'];
        $activeReportStatuses = ['Baru', 'Diproses'];

        // ---- Statistik kartu atas ----
        $activeReservationsCount = Reservation::where('user_id', $userId)
            ->whereIn('status', $activeReservationStatuses)
            ->count();

        $pendingReservationsCount = Reservation::where('user_id', $userId)
            ->where('status', 'Pending')
            ->count();

        $thisMonthReservationsCount = Reservation::where('user_id', $userId)
            ->whereMonth('reservation_date', Carbon::now()->month)
            ->whereYear('reservation_date', Carbon::now()->year)
            ->count();

        $thisMonthApprovedCount = Reservation::where('user_id', $userId)
            ->whereMonth('reservation_date', Carbon::now()->month)
            ->whereYear('reservation_date', Carbon::now()->year)
            ->where('status', 'Approved')
            ->count();

        $activeReportsCount = Report::where('user_id', $userId)
            ->whereIn('status', $activeReportStatuses)
            ->count();

        // ---- List untuk 2 panel bawah ----
        $activeReservations = Reservation::with('facility')
            ->where('user_id', $userId)
            ->whereIn('status', $activeReservationStatuses)
            ->orderBy('reservation_date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        $activeReports = Report::with('facility')
            ->where('user_id', $userId)
            ->whereIn('status', $activeReportStatuses)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'activeReservationsCount'     => $activeReservationsCount,
            'pendingReservationsCount'    => $pendingReservationsCount,
            'thisMonthReservationsCount'  => $thisMonthReservationsCount,
            'thisMonthApprovedCount'      => $thisMonthApprovedCount,
            'activeReportsCount'          => $activeReportsCount,
            'activeReservations'          => $activeReservations,
            'activeReports'               => $activeReports,
        ]);
    }
}