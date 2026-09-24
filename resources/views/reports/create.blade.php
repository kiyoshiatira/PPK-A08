<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor Kerusakan</title>
</head>
<body>
    <h2>Lapor Kerusakan Fasilitas</h2>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="facility_id">Fasilitas</label>
            <select name="facility_id" id="facility_id" required>
                <option value="">Pilih fasilitas</option>

                @foreach($facilities as $facility)
                    <option value="{{ $facility->id }}">
                        {{ $facility->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <br>

        <div>
            <label for="category">Kategori Kerusakan</label>
            <input type="text" name="category" id="category" required>
        </div>

        <br>

        <div>
            <label for="description">Deskripsi Kerusakan</label>
            <textarea name="description" id="description" rows="5" required></textarea>
        </div>

        <br>

        <div>
            <label for="photo">Foto Kerusakan</label>
            <input type="file" name="photo" id="photo" accept=".jpg,.jpeg,.png">
        </div>

        <br>

        <button type="submit">Kirim Laporan</button>
    </form>
</body>
</html>
