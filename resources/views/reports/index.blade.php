<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Saya</title>
</head>
<body>
    <h2>Laporan Saya</h2>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if($reports->count())
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Fasilitas</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>Catatan Penyelesaian</th>
                </tr>
            </thead>

            <tbody>
                @foreach($reports as $report)
                    <tr>
                        <td>{{ $report->facility->name ?? '-' }}</td>
                        <td>{{ $report->category }}</td>
                        <td>{{ $report->description }}</td>
                        <td>{{ $report->status }}</td>
                        <td>{{ $report->resolution_notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $reports->links() }}
    @else
        <p>Belum ada laporan kerusakan.</p>
    @endif
</body>
</html>
