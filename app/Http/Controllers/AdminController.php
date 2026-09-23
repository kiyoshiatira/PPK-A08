<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Facility;    // Sesuaikan dengan nama model fasilitas Anda
use App\Models\Reservation; // Sesuaikan dengan nama model reservasi Anda
use App\Models\Report;      // Sesuaikan dengan nama model laporan Anda
use Carbon\Carbon;          // Digunakan untuk filter waktu (minggu ini)
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Data User
        $totalUsers = User::count();
        $pendingUsersCount = User::where('is_verified', false)->count();

        // Data Fasilitas
        $totalFacilities = Facility::count();
        $activeFacilities = Facility::where('status', 'Aktif')->count(); 

        // Data Reservasi 
        $totalReservations = Reservation::count();
        $weeklyReservations = Reservation::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])->count();

        $totalReports = Report::count();
        $unhandledReports = Report::where('status', 'Baru')->count();

        return view('admin.dashboard', compact(
            'totalUsers', 
            'pendingUsersCount', 
            'totalFacilities', 
            'activeFacilities', 
            'totalReservations', 
            'weeklyReservations', 
            'totalReports', 
            'unhandledReports'
        ));
    }
}