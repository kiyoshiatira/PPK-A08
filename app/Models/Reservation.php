<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    // Mengizinkan semua kolom diisi (mass assignment)
    protected $guarded = [];

    // Relasi ke tabel pengguna (pemesan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke tabel fasilitas
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    // Relasi ke petugas yang memproses
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}