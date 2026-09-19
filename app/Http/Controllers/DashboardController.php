<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data unik untuk dropdown filter
        $types = Facility::select('type')->distinct()->pluck('type');
        $locations = Facility::select('location')->distinct()->pluck('location');

        // Query dasar fasilitas
        $query = Facility::query();

        // FR-02: Filter Berdasarkan Nama
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // FR-02: Filter Berdasarkan Tipe
        if ($request->filled('type') && $request->type !== 'Semua tipe') {
            $query->where('type', $request->type);
        }

        // FR-02: Filter Berdasarkan Lokasi
        if ($request->filled('location') && $request->location !== 'Semua lokasi') {
            $query->where('location', $request->location);
        }

        // FR-02: Filter Kapasitas Minimum & Maksimum
        if ($request->filled('min_capacity') && $request->min_capacity > 0) {
            $query->where('capacity', '>=', $request->min_capacity);
        }
        if ($request->filled('max_capacity') && $request->max_capacity > 0) {
            $query->where('capacity', '<=', $request->max_capacity);
        }

        $facilities = $query->paginate(6)->withQueryString();
        
        // Tanggal untuk tampilan UI
        $today = Carbon::now()->translatedFormat('l, d F Y');

        return view('dashboard', compact('facilities', 'types', 'locations', 'today'));
    }
}