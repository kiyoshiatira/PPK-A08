<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Facility;
use App\Models\Reservation;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DummyReservationSeeder extends Seeder
{
    public function run(): void
    {
        $pengguna = User::where('role', 'pengguna')->first();
        if (!$pengguna) return;

        // Ambil atau buat fasilitas contoh jika belum ada
        $facilityA = Facility::firstOrCreate(
            ['name' => 'Ruang Seminar A'],
            [
                'type'        => 'Ruang Kelas',
                'location'    => 'Gedung A Lantai 2',
                'capacity'    => 50,
                'description' => 'Ruang seminar lengkap proyektor dan AC.',
                'status'      => 'Aktif',
            ]
        );

        $facilityB = Facility::firstOrCreate(
            ['name' => 'Lab Komputer 1'],
            [
                'type'        => 'Laboratorium',
                'location'    => 'Gedung B Lantai 1',
                'capacity'    => 30,
                'description' => 'Lab komputer dengan PC spek tinggi.',
                'status'      => 'Aktif',
            ]
        );

        $tomorrow = Carbon::tomorrow()->toDateString();

        // Reservasi 1: Pending (09:00 - 10:30)
        Reservation::firstOrCreate(
            [
                'user_id'          => $pengguna->id,
                'facility_id'      => $facilityA->id,
                'reservation_date' => $tomorrow,
                'start_time'       => '09:00:00',
                'end_time'         => '10:30:00',
            ],
            [
                'purpose' => 'Kegiatan Workshop Desain Grafis BEM',
                'status'  => 'Pending',
            ]
        );

        // Reservasi 2: Pending yang bentrok dengan Reservasi 1 (10:00 - 11:30) untuk uji coba anti-bentrok
        Reservation::firstOrCreate(
            [
                'user_id'          => $pengguna->id,
                'facility_id'      => $facilityA->id,
                'reservation_date' => $tomorrow,
                'start_time'       => '10:00:00',
                'end_time'         => '11:30:00',
            ],
            [
                'purpose' => 'Rapat Koordinasi Panitia Wisuda',
                'status'  => 'Pending',
            ]
        );

        // Reservasi 3: Di fasilitas lain (Lab Komputer)
        Reservation::firstOrCreate(
            [
                'user_id'          => $pengguna->id,
                'facility_id'      => $facilityB->id,
                'reservation_date' => $tomorrow,
                'start_time'       => '13:00:00',
                'end_time'         => '15:00:00',
            ],
            [
                'purpose' => 'Praktikum Pengganti Pemrograman Web',
                'status'  => 'Pending',
            ]
        );
    }
}
