<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; font-size: 12px; }
        th { background-color: #f3f4f6; text-align: left; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; font-size: 14px; margin-top: 0; color: #666; }
    </style>
</head>
<body>
    <h2>{{ $tipe === 'kerusakan' ? 'Rekap Laporan Kerusakan' : 'Rekap Okupansi Fasilitas' }}</h2>
    <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>

    <table>
        @if($tipe === 'kerusakan')
            <!-- TABEL KHUSUS KERUSAKAN -->
            <thead>
                <tr>
                    <th>Tanggal Lapor</th>
                    <th>Fasilitas</th>
                    <th>Deskripsi Kerusakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y H:i') }}</td>
                        <td>{{ $row->facility->name ?? 'Fasilitas Terhapus' }}</td>
                        <td>{{ $row->description ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align: center;">Tidak ada data laporan kerusakan</td></tr>
                @endforelse
            </tbody>
        @else
            <!-- TABEL KHUSUS OKUPANSI (PEMINJAMAN) -->
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Peminjam</th>
                    <th>Fasilitas</th>
                    <th>Waktu</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->reservation_date)->format('d M Y') }}</td>
                        <td>{{ $row->user->name ?? 'User Dihapus' }}</td>
                        <td>{{ $row->facility->name ?? 'Fasilitas Terhapus' }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($row->end_time)->format('H:i') }}</td>
                        <td>{{ $row->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align: center;">Tidak ada data peminjaman</td></tr>
                @endforelse
            </tbody>
        @endif
    </table>
</body>
</html>