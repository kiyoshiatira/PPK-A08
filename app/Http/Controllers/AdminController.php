<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\Report;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $usersBulanIni = User::whereMonth('created_at', Carbon::now()->month)->count();
        
        $usersPending = 0; 

        $totalFasilitas = Facility::count();
        $fasilitasAktif = Facility::where('status', 'LIKE', '%aktif%')->count();
        
        $fasilitasPerbaikan = Facility::where('status', 'LIKE', '%perbaikan%')
            ->orWhere('status', 'LIKE', '%maintenance%')
            ->orWhere('status', 'LIKE', '%repair%')
            ->count();

        $totalReservasi = Reservation::count();
        $reservasiMingguIni = Reservation::where('created_at', '>=', Carbon::now()->startOfWeek())->count();

        $totalLaporan = Report::count();
        $laporanPending = Report::where('status', 'LIKE', '%pending%')
            ->orWhere('status', 'LIKE', '%menunggu%')
            ->count();

        $recentUsers = User::latest()->take(2)->get()->map(function($user) {
            return [
                'aktivitas' => 'Akun pengguna baru terdaftar (' . $user->name . ')',
                'waktu' => $user->created_at->diffForHumans(),
                'timestamp' => $user->created_at
            ];
        });

        $recentReservations = Reservation::latest()->take(2)->get()->map(function($res) {
            return [
                'aktivitas' => 'Reservasi baru dibuat',
                'waktu' => $res->created_at->diffForHumans(),
                'timestamp' => $res->created_at
            ];
        });

        $recentReports = Report::latest()->take(2)->get()->map(function($rep) {
            return [
                'aktivitas' => 'Laporan kerusakan baru diterima',
                'waktu' => $rep->created_at->diffForHumans(),
                'timestamp' => $rep->created_at
            ];
        });

        $recentActivities = collect()
            ->concat($recentUsers)
            ->concat($recentReservations)
            ->concat($recentReports)
            ->sortByDesc('timestamp')
            ->take(3);

        return view('admin.dashboard', compact(
            'totalUsers', 'usersBulanIni',
            'totalFasilitas', 'fasilitasAktif', 'fasilitasPerbaikan',
            'totalReservasi', 'reservasiMingguIni',
            'totalLaporan', 'laporanPending',
            'recentActivities'
        ));
    }
}