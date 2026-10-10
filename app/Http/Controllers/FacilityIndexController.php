<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FacilityIndexController extends Controller
{
    public function index(Request $request)
    {
        $capacityOptions = [
            '1-30'   => 'Kecil (≤ 30 orang)',
            '31-100' => 'Sedang (31–100 orang)',
            '101-'   => 'Besar (> 100 orang)',
        ];

        $sortOptions = [
            'name_asc'      => 'Urut: Nama A–Z',
            'name_desc'     => 'Urut: Nama Z–A',
            'capacity_asc'  => 'Urut: Kapasitas terkecil',
            'capacity_desc' => 'Urut: Kapasitas terbesar',
        ];

        // Data dropdown filter
        $types     = Facility::select('type')->distinct()->pluck('type');
        $locations = Facility::select('location')->distinct()->pluck('location');

        // Slide hero: fasilitas dikelompokkan per lokasi
        $buildings = Facility::selectRaw('location, count(*) as total, max(photo) as photo')
            ->groupBy('location')
            ->orderBy('location')
            ->get();

        $query = Facility::query();

        // FR-02: cari nama, tipe, lokasi, kapasitas
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->filled('type') && $request->input('type') !== 'Semua tipe') {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('location') && $request->input('location') !== 'Semua lokasi') {
            $query->where('location', $request->input('location'));
        }

        $capacity = (string) $request->input('capacity');
        if (array_key_exists($capacity, $capacityOptions)) {
            [$min, $max] = explode('-', $capacity);
            $query->where('capacity', '>=', (int) $min);
            if ($max !== '') {
                $query->where('capacity', '<=', (int) $max);
            }
        }

        // Dipertahankan dari versi lama: min/maks kapasitas lewat query string
        // (?min_capacity=&max_capacity=). Dropdown rentang di UI tetap jalan berdampingan.
        if ($request->filled('min_capacity') && $request->min_capacity > 0) {
            $query->where('capacity', '>=', (int) $request->min_capacity);
        }
        if ($request->filled('max_capacity') && $request->max_capacity > 0) {
            $query->where('capacity', '<=', (int) $request->max_capacity);
        }

        match ($request->input('sort')) {
            'name_desc'     => $query->orderByDesc('name'),
            'capacity_asc'  => $query->orderBy('capacity'),
            'capacity_desc' => $query->orderByDesc('capacity'),
            default         => $query->orderBy('name'),
        };

        $facilities = $query->paginate(9)->withQueryString()->fragment('daftar');

        $today = Carbon::now()->locale('id')->translatedFormat('d F Y');

        return view('facilities.index', compact(
            'facilities', 'types', 'locations', 'buildings',
            'capacityOptions', 'sortOptions', 'today'
        ));
    }
}