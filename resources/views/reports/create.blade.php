@extends('layouts.app')

@section('title', 'Laporkan Kerusakan - Ruang Kampus')

@section('content')
<div class="max-w-4xl mx-auto px-8 pt-10 pb-16">
    
    <!-- Header Halaman -->
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 mb-2">Laporkan kerusakan</h1>
            <p class="text-neutral-500 text-sm">Bantu kami menjaga fasilitas kampus dengan mengirim detail kerusakan yang Anda temukan.</p>
        </div>
        
        <a href="{{ route('reports.index') }}" class="px-5 py-2 text-sm font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors flex items-center gap-2">
            Lihat Riwayat Laporan
        </a>
    </div>

    <!-- Alert untuk Pesan Error -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 p-8">
        <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <h3 class="text-xs font-bold text-neutral-500 mb-6 tracking-wider">01 DETAIL KERUSAKAN</h3>

            <!-- Pilih Fasilitas -->
            <div class="mb-6">
                <label for="facility_id" class="block text-sm font-semibold text-neutral-800 mb-2">Pilih fasilitas</label>
                <select name="facility_id" id="facility_id" required class="w-full bg-neutral-50 border border-neutral-200 text-neutral-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-3 appearance-none outline-none transition-colors">
                    <option value="" disabled selected>Pilih fasilitas yang mengalami kendala</option>
                    @foreach($facilities as $facility)
                        <option value="{{ $facility->id }}" {{ old('facility_id') == $facility->id ? 'selected' : '' }}>
                            {{ $facility->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Kategori Kerusakan -->
            <div class="mb-6">
                <label for="category" class="block text-sm font-semibold text-neutral-800 mb-2">Kategori kerusakan</label>
                <!-- Pada desain Anda bentuknya dropdown, jadi kita ubah input text menjadi select -->
                <select name="category" id="category" required class="w-full bg-neutral-50 border border-neutral-200 text-neutral-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-3 appearance-none outline-none transition-colors">
                    <option value="" disabled selected>Pilih kategori kerusakan (misal: Listrik, AC, Proyektor)</option>
                    <option value="Listrik" {{ old('category') == 'Listrik' ? 'selected' : '' }}>Listrik / Penerangan</option>
                    <option value="AC" {{ old('category') == 'AC' ? 'selected' : '' }}>Pendingin Ruangan (AC)</option>
                    <option value="Proyektor" {{ old('category') == 'Proyektor' ? 'selected' : '' }}>Audio Visual / Proyektor</option>
                    <option value="Mebel" {{ old('category') == 'Mebel' ? 'selected' : '' }}>Mebel (Meja, Kursi, Papan Tulis)</option>
                    <option value="Air & Sanitasi" {{ old('category') == 'Air & Sanitasi' ? 'selected' : '' }}>Air & Sanitasi / Toilet</option>
                    <option value="Lainnya" {{ old('category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <!-- Deskripsi Kerusakan -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-semibold text-neutral-800 mb-2">Deskripsi kerusakan</label>
                <textarea name="description" id="description" rows="4" required class="w-full bg-neutral-50 border border-neutral-200 text-neutral-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-3 outline-none transition-colors resize-none" placeholder="Deskripsikan secara detail letak dan kondisi kerusakan...">{{ old('description') }}</textarea>
            </div>

            <!-- Upload Foto -->
            <div class="mb-8">
                <label class="block text-sm font-semibold text-neutral-800 mb-2">Upload foto</label>
                <div class="flex items-center justify-center w-full">
                    <label for="photo" class="flex flex-col items-center justify-center w-full h-32 border-2 border-neutral-200 border-dashed rounded-xl cursor-pointer bg-neutral-50 hover:bg-neutral-100 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <!-- Ikon Upload (Panah Atas) -->
                            <svg class="w-6 h-6 mb-3 text-neutral-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                            </svg>
                            <p class="mb-1 text-sm font-semibold text-neutral-800">Tarik foto ke sini atau pilih file</p>
                            <p class="text-xs text-neutral-500">JPG/PNG, maksimal 5 MB</p>
                        </div>
                        <input id="photo" name="photo" type="file" class="hidden" accept=".jpg,.jpeg,.png" />
                    </label>
                </div>
                <!-- Menampilkan nama file yang dipilih menggunakan sedikit javascript ringan -->
                <p id="file-name" class="mt-2 text-sm text-neutral-500 hidden"></p>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-3 pt-2">
                <!-- Tombol Batal mengarah kembali ke dashboard / halaman laporan index -->
                <a href="{{ route('reports.index') }}" class="px-6 py-2.5 text-sm font-semibold text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">Kirim laporan</button>
            </div>
            
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Script sederhana untuk menampilkan nama file setelah user memilih foto
    document.getElementById('photo').addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : '';
        var fileNameDisplay = document.getElementById('file-name');
        if (fileName) {
            fileNameDisplay.textContent = 'File terpilih: ' + fileName;
            fileNameDisplay.classList.remove('hidden');
        } else {
            fileNameDisplay.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection