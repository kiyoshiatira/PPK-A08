<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrean Petugas</title>
</head>
<body>
    <h2>Antrean Petugas</h2>

    <h3>Reservasi Pending</h3>

    @if($reservations->count())
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Fasilitas</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Keperluan</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->user->name ?? '-' }}</td>
                        <td>{{ $reservation->facility->name ?? '-' }}</td>
                        <td>{{ $reservation->reservation_date }}</td>
                        <td>
                            {{ $reservation->start_time }} -
                            {{ $reservation->end_time }}
                        </td>
                        <td>{{ $reservation->purpose }}</td>
                        <td>{{ $reservation->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada reservasi pending.</p>
    @endif

    <h3>Laporan Kerusakan Baru</h3>

    @if($reports->count())
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Fasilitas</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach($reports as $report)
                    <tr>
                        <td>{{ $report->user->name ?? '-' }}</td>
                        <td>{{ $report->facility->name ?? '-' }}</td>
                        <td>{{ $report->category }}</td>
                        <td>{{ $report->description }}</td>
                        <td>{{ $report->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada laporan baru.</p>
    @endif
</body>
</html>
