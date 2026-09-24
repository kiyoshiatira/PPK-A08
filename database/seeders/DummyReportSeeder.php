<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyReportSeeder extends Seeder
{
    /**
     * Seeder ini hanya untuk keperluan testing fitur SRS-11 (Kelola Status Laporan).
     * Jalankan dengan: php artisan db:seed --class=DummyReportSeeder
     *
     * Prasyarat: Pastikan sudah ada minimal 1 fasilitas dan 1 user pengguna di database.
     */
    public function run(): void
    {
        // Ambil user pengguna dan petugas yang sudah ada
        $pengguna = User::where('role', 'pengguna')->first();
        $petugas  = User::where('role', 'petugas')->first();

        // Ambil beberapa fasilitas yang ada
        $facilities = Facility::take(3)->get();

        if (!$pengguna || $facilities->isEmpty()) {
            $this->command->warn('Seeder dibatalkan: Pastikan ada user pengguna dan fasilitas di database.');
            return;
        }

        $facilityIds = $facilities->pluck('id')->toArray();

        // --- LAPORAN STATUS: BARU (Belum ditangani) ---
        Report::create([
            'user_id'          => $pengguna->id,
            'facility_id'      => $facilityIds[0],
            'category'         => 'Kerusakan Fisik',
            'description'      => 'Kursi di baris ke-3 kaki depannya patah dan berbahaya untuk digunakan. Perlu segera diganti.',
            'photo_path'       => null,
            'status'           => 'Baru',
            'resolution_notes' => null,
            'processed_by'     => null,
        ]);

        Report::create([
            'user_id'          => $pengguna->id,
            'facility_id'      => $facilityIds[array_key_exists(1, $facilityIds) ? 1 : 0],
            'category'         => 'Kebersihan',
            'description'      => 'Toilet di lantai 2 kondisinya sangat kotor dan berbau. Sudah beberapa hari tidak dibersihkan.',
            'photo_path'       => null,
            'status'           => 'Baru',
            'resolution_notes' => null,
            'processed_by'     => null,
        ]);

        // --- LAPORAN STATUS: DIPROSES (Sedang ditangani petugas) ---
        Report::create([
            'user_id'          => $pengguna->id,
            'facility_id'      => $facilityIds[array_key_exists(2, $facilityIds) ? 2 : 0],
            'category'         => 'Peralatan',
            'description'      => 'Proyektor tidak bisa menyala sama sekali. Sudah dicoba restart tapi tetap tidak ada gambar.',
            'photo_path'       => null,
            'status'           => 'Diproses',
            'resolution_notes' => null,
            'processed_by'     => $petugas?->id,
        ]);

        Report::create([
            'user_id'          => $pengguna->id,
            'facility_id'      => $facilityIds[0],
            'category'         => 'Kerusakan Fisik',
            'description'      => 'Pintu ruangan tidak bisa dikunci dari dalam. Kunci terasa longgar dan tidak berfungsi.',
            'photo_path'       => null,
            'status'           => 'Diproses',
            'resolution_notes' => null,
            'processed_by'     => $petugas?->id,
        ]);

        // --- LAPORAN STATUS: SELESAI (Sudah diselesaikan) ---
        Report::create([
            'user_id'          => $pengguna->id,
            'facility_id'      => $facilityIds[array_key_exists(1, $facilityIds) ? 1 : 0],
            'category'         => 'Peralatan',
            'description'      => 'AC di ruangan tidak dingin, hanya mengeluarkan udara biasa tanpa pendinginan.',
            'photo_path'       => null,
            'status'           => 'Selesai',
            'resolution_notes' => 'AC sudah diperbaiki oleh teknisi eksternal. Freon diisi ulang dan kini berfungsi normal.',
            'processed_by'     => $petugas?->id,
        ]);

        // --- LAPORAN STATUS: DITOLAK (Tidak valid) ---
        Report::create([
            'user_id'          => $pengguna->id,
            'facility_id'      => $facilityIds[array_key_exists(2, $facilityIds) ? 2 : 0],
            'category'         => 'Kerusakan Fisik',
            'description'      => 'Lampu ruangan mati.',
            'photo_path'       => null,
            'status'           => 'Ditolak',
            'resolution_notes' => 'Setelah dicek, lampu tidak mati. Kemungkinan saklar dalam kondisi off saat pelaporan. Laporan tidak valid.',
            'processed_by'     => $petugas?->id,
        ]);

        $this->command->info('✓ DummyReportSeeder berhasil: 6 laporan dummy dibuat (Baru: 2, Diproses: 2, Selesai: 1, Ditolak: 1).');
    }
}
