@extends('layouts.app')

@section('title', 'Daftar - Ruang Kampus')

@section('content')
<div class="flex justify-center px-8 py-16">
    <div class="bg-white border rounded-2xl p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold">Buat akun</h1>
        <p class="text-neutral-600 text-sm mt-1 mb-6">Daftar dengan identitas kampus Anda.</p>

        <form action="{{ route('register.attempt') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold mb-2">Nama lengkap</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Nama lengkap"
                    required
                    autofocus
                    class="w-full bg-neutral-100 border rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-neutral-900 @error('name') border-red-400 @enderror"
                >
                @error('name')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold mb-2">Email kampus</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="nama@kampus.ac.id"
                    required
                    class="w-full bg-neutral-100 border rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-neutral-900 @error('email') border-red-400 @enderror"
                >
                @error('email')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-semibold mb-2">Kata sandi</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimal 8 karakter"
                    required
                    class="w-full bg-neutral-100 border rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-neutral-900 @error('password') border-red-400 @enderror"
                >
                @error('password')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-semibold mb-2">Konfirmasi kata sandi</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Ulangi kata sandi"
                    required
                    class="w-full bg-neutral-100 border rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-neutral-900"
                >
            </div>

            <p class="text-xs text-neutral-400 mb-5">
                Akun akan diverifikasi oleh admin terlebih dahulu sebelum bisa dipakai untuk masuk.
            </p>

            <button
                type="submit"
                class="w-full bg-blue-400 hover:bg-blue-500 text-neutral-900 border border-blue-500 rounded-lg py-3 text-sm font-semibold transition"
            >
                Daftar
            </button>
        </form>

        <p class="text-center text-sm mt-5">
            <a href="{{ route('login') }}" class="text-blue-700 hover:underline">Sudah punya akun? Masuk</a>
        </p>
    </div>
</div>
@endsection