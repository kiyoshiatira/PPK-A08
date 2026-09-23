<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Facility extends Model
{
    protected $fillable = [
        'name',
        'type',
        'location',
        'capacity',
        'description',
        'photo',
        'status'
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Accessor untuk menghitung slot yang tersedia hari ini
    public function getAvailableSlotsAttribute()
    {
        $totalSlots = 26; // Dari 07.00 sampai 20.00 (13 jam * 2 slot/jam)
        
        // Hitung durasi reservasi aktif hari ini dalam satuan 30 menit
        $bookedSlots = $this->reservations()
            ->whereDate('reservation_date', Carbon::today())
            ->whereIn('status', ['Pending', 'Approved'])
            ->get()
            ->sum(function ($reservation) {
                $start = Carbon::parse($reservation->start_time);
                $end = Carbon::parse($reservation->end_time);
                return $start->diffInMinutes($end) / 30;
            });

        return max(0, $totalSlots - $bookedSlots);
    }
}