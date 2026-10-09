<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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


public function canBeCancelled(): bool
{
    if (! in_array($this->status, ['Pending', 'Approved'])) {
        return false;
    }

    $start = Carbon::parse(
        Carbon::parse($this->reservation_date)->toDateString() . ' ' . $this->start_time
    );

    return $start->isFuture();
}
}

