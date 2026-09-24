<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function create()
    {
        $facilities = Facility::where('status', 'aktif')
            ->orderBy('name')
            ->get();

        return view('reports.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reports', 'public');
        }

        Report::create([
            'user_id' => Auth::id(),
            'facility_id' => $validated['facility_id'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'photo_path' => $photoPath,
            'status' => 'Baru',
        ]);

        return redirect()
            ->route('reports.index')
            ->with('success', 'Laporan kerusakan berhasil dikirim.');
    }

    public function index()
    {
        $reports = Report::with('facility', 'processor')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('reports.index', compact('reports'));
    }

    public function petugasQueue()
    {
        $reservations = Reservation::with(['user', 'facility'])
            ->where('status', 'Pending')
            ->latest()
            ->get();

        $reports = Report::with(['user', 'facility'])
            ->where('status', 'Baru')
            ->latest()
            ->get();

        return view('petugas.reports.index', compact('reservations', 'reports'));
    }
}
