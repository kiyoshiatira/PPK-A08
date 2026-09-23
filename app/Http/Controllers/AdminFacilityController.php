<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminFacilityController extends Controller
{
    public function index(Request $request)
    {
        $facilities = Facility::orderBy('created_at', 'desc')->get();
        
        $editFacility = null;
        if ($request->has('edit')) {
            $editFacility = Facility::findOrFail($request->query('edit'));
        }

        return view('admin.facilities.index', compact('facilities', 'editFacility'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'capacity'    => 'required|integer|min:1',
            'description' => 'nullable|string',
            'photo'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'      => 'required|in:Aktif,Dalam Perbaikan,Nonaktif'
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('facilities', 'public');
        }

        Facility::create($validated);

        return redirect()->route('admin.facilities.index')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'capacity'    => 'required|integer|min:1',
            'description' => 'nullable|string',
            'photo'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'      => 'required|in:Aktif,Dalam Perbaikan,Nonaktif'
        ]);

        if ($request->hasFile('photo')) {
            if ($facility->photo) {
                Storage::disk('public')->delete($facility->photo);
            }
            $validated['photo'] = $request->file('photo')->store('facilities', 'public');
        }

        $facility->update($validated);

        return redirect()->route('admin.facilities.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Facility $facility)
    {
        if ($facility->photo) {
            Storage::disk('public')->delete($facility->photo);
        }
        
        $facility->delete();

        return redirect()->route('admin.facilities.index')->with('success', 'Fasilitas berhasil dihapus.');
    }
}