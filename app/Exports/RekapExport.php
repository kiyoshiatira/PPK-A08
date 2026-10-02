<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RekapExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $reservations;

    public function __construct($reservations)
    {
        $this->reservations = $reservations;
    }

    public function collection(): Collection
    {
        return $this->reservations;
    }

    public function headings(): array
    {
        return ['Tanggal', 'Peminjam', 'Email', 'Fasilitas', 'Tujuan', 'Status'];
    }

    public function map($reservation): array
    {
        return [
            Carbon::parse($reservation->created_at)->format('Y-m-d H:i'),
            $reservation->user->name ?? 'User Dihapus',
            $reservation->user->email ?? '-',
            $reservation->facility->name ?? 'Fasilitas Dihapus',
            $reservation->purpose ?? '-',
            $reservation->status
        ];
    }

    // Penambahan : ?array untuk menyesuaikan aturan versi terbaru
    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}