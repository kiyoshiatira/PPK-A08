<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Data User
        $totalUsers = User::count();
        $pendingUsersCount = User::where('is_verified', false)->count();
        $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
                                 ->whereYear('created_at', Carbon::now()->year)
                                 ->count();

        // 2. Data Fasilitas
        $totalFacilities = Facility::count();
        $activeFacilities = Facility::where('status', 'Aktif')->count(); 
        $facilitiesInRepair = Facility::where('status', 'Dalam Perbaikan')->count(); // Data dinamis untuk "Perlu perhatian"

        // 3. Data Reservasi 
        $totalReservations = Reservation::count();
        $weeklyReservations = Reservation::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])->count();

        // 4. Data Laporan
        $totalReports = Report::count();
        $unhandledReports = Report::where('status', 'Baru')->count(); // Sesuaikan jika status default Anda berbeda

        // 5. Data Aktivitas Terbaru (Menggabungkan data terbaru dari berbagai tabel)
        $recentActivities = collect();

        // Cek user terbaru yang diverifikasi
        $latestUser = User::where('is_verified', true)->latest('updated_at')->first();
        if ($latestUser) {
            $recentActivities->push([
                'description' => "Akun pengguna ({$latestUser->name}) diverifikasi",
                'time' => $latestUser->updated_at
            ]);
        }

        // Cek fasilitas yang paling baru ditambahkan
        $latestFacility = Facility::latest('created_at')->first();
        if ($latestFacility) {
            $recentActivities->push([
                'description' => "Fasilitas baru ({$latestFacility->name}) ditambahkan",
                'time' => $latestFacility->created_at
            ]);
        }

        // Cek reservasi terbaru yang disetujui (jika ada kolom status)
        // Jika tabel reservasi Anda belum ada kolom status, Anda bisa gunakan ->latest('created_at')
        $latestReservation = Reservation::latest('updated_at')->first(); 
        if ($latestReservation) {
            $recentActivities->push([
                'description' => 'Reservasi diperbarui',
                'time' => $latestReservation->updated_at
            ]);
        }

        // Urutkan aktivitas dari yang paling baru ke lama, dan ambil 3 teratas
        $recentActivities = $recentActivities->sortByDesc('time')->take(3);

        return view('admin.dashboard', compact(
            'totalUsers', 
            'pendingUsersCount', 
            'newUsersThisMonth',
            'totalFacilities', 
            'activeFacilities', 
            'facilitiesInRepair',
            'totalReservations', 
            'weeklyReservations', 
            'totalReports', 
            'unhandledReports',
            'recentActivities'
        ));
    }
}