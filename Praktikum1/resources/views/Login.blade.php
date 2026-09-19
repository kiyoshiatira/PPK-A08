<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Ruang Kampus</title>
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
            <a href="{{ route('login') }}" class="text-black">Masuk</a>
        </div>
    </nav>

    <!-- Login Card -->
    <main class="flex justify-center items-center px-8 py-20">
        <div class="bg-white p-8 rounded-xl border shadow-sm w-full max-w-md">
            <h1 class="text-2xl font-bold mb-1">Selamat datang</h1>
            <p class="text-gray-500 text-sm mb-6">Masuk untuk mengelola reservasi.</p>

            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('login.attempt') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Email kampus</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@kampus.ac.id"
                        required
                        autofocus
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
                        placeholder="••••••••"
                        required
                        class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 @error('password') border-red-400 @enderror"
                    >
                    @error('password')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300">
                        Ingat saya
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-gray-500 hover:text-black">
                        Lupa kata sandi?
                    </a>
                </div>

                <button
                    type="submit"
                    class="w-full bg-gray-900 text-white rounded-lg p-3 text-sm font-semibold hover:bg-gray-800 transition"
                >
                    Masuk
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-gray-900 font-semibold hover:underline">Daftar</a>
            </p>
        </div>
    </main>

</body>
</html>