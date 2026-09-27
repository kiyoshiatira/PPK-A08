<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyNotificationSeeder extends Seeder
{
    /**
     * Seeder untuk menguji coba fitur SRS-12 (Notifikasi Sistem).
     * Jalankan dengan: php artisan db:seed --class=DummyNotificationSeeder
     */
    public function run(): void
    {
        $pengguna = User::where('role', 'pengguna')->first();
        $petugas  = User::where('role', 'petugas')->first();

        if ($pengguna) {
            Notification::create([
                'user_id' => $pengguna->id,
                'title'   => 'Reservasi Disetujui',
                'message' => 'Reservasi Anda untuk fasilitas Aula Utama pada tanggal 2026-09-30 (09:00 - 11:00) telah disetujui oleh petugas.',
                'type'    => 'reservasi',
                'link'    => route('reservations.index'),
                'is_read' => false,
            ]);

            Notification::create([
                'user_id' => $pengguna->id,
                'title'   => 'Status Laporan: Selesai',
                'message' => 'Laporan kerusakan fasilitas Lab Komputer 1 telah selesai ditangani. Catatan: AC sudah diperbaiki oleh teknisi.',
                'type'    => 'laporan',
                'link'    => route('reports.index'),
                'is_read' => false,
            ]);

            Notification::create([
                'user_id' => $pengguna->id,
                'title'   => 'Pembatalan Darurat Reservasi',
                'message' => 'Reservasi Anda untuk fasilitas Lapangan Basket pada tanggal 2026-09-28 dibatalkan secara darurat. Alasan: Pemeliharaan lapangan.',
                'type'    => 'reservasi',
                'link'    => route('reservations.index'),
                'is_read' => true,
            ]);
        }

        if ($petugas) {
            Notification::create([
                'user_id' => $petugas->id,
                'title'   => 'Laporan Kerusakan Baru',
                'message' => 'Laporan kerusakan baru telah diajukan oleh pengguna untuk fasilitas Ruang Rapat A.',
                'type'    => 'laporan',
                'link'    => route('petugas.reports.index'),
                'is_read' => false,
            ]);

            Notification::create([
                'user_id' => $petugas->id,
                'title'   => 'Reservasi Baru Masuk',
                'message' => 'Pengguna mengajukan reservasi baru untuk fasilitas Lab Komputer 2.',
                'type'    => 'reservasi',
                'link'    => route('petugas.reservations.index'),
                'is_read' => false,
            ]);
        }

        $this->command->info('✓ DummyNotificationSeeder berhasil dijalankan.');
    }
}
