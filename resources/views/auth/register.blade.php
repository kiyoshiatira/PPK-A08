<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Ruang Kampus</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

    <!-- Navbar -->
    <nav class="flex items-center justify-between px-8 py-4 bg-white border-b">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-gray-900 rounded"></div>
            <span class="font-bold text-lg tracking-wide">RUANG KAMPUS</span>
        </div>
        <div class="space-x-6 text-sm font-medium">
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-black">Beranda</a>
            <a href="#" class="text-gray-500 hover:text-black">Fasilitas</a>
            <a href="{{ route('login') }}" class="text-gray-500 hover:text-black">Masuk</a>
        </div>
    </nav>

    <!-- Register Card -->
    <main class="flex justify-center items-center px-8 py-20">
        <div class="bg-white p-8 rounded-xl border shadow-sm w-full max-w-md">
            <h1 class="text-2xl font-bold mb-1">Buat akun</h1>
            <p class="text-gray-500 text-sm mb-6">Daftar dengan identitas kampus Anda.</p>

            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register.attempt') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Nama lengkap</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Nama lengkap"
                        required
                        autofocus
                        class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 @error('name') border-red-400 @enderror"
                    >
                    @error('name')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Email kampus</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@kampus.ac.id"
                        required
                        class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 @error('email') border-red-400 @enderror"
                    >
                    @error('email')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Kata sandi</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Minimal 8 karakter"
                        required
                        class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 @error('password') border-red-400 @enderror"
                    >
                    @error('password')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">Konfirmasi kata sandi</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        placeholder="Ulangi kata sandi"
                        required
                        class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900"
                    >
                </div>

                <p class="text-xs text-gray-400 mb-4">
                    Akun kamu akan diverifikasi oleh admin terlebih dahulu sebelum bisa digunakan untuk login.
                </p>

                <button
                    type="submit"
                    class="w-full bg-gray-900 text-white rounded-lg p-3 text-sm font-semibold hover:bg-gray-800 transition"
                >
                    Daftar
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-gray-900 font-semibold hover:underline">Masuk</a>
            </p>
        </div>
    </main>

</body>
</html>