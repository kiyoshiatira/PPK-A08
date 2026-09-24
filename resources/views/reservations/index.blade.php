<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Reservasi</title>
</head>
<body>
    <h2>Riwayat Reservasi</h2>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if($reservations->count())
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Fasilitas</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Keperluan</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>

            <tbody>
                @foreach($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->facility->name ?? '-' }}</td>
                        <td>{{ $reservation->reservation_date }}</td>
                        <td>
                            {{ $reservation->start_time }} -
                            {{ $reservation->end_time }}
                        </td>
                        <td>{{ $reservation->purpose }}</td>
                        <td>{{ $reservation->status }}</td>
                        <td>{{ $reservation->rejection_or_cancel_reason ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $reservations->links() }}
    @else
        <p>Belum ada riwayat reservasi.</p>
    @endif
</body>
</html>
