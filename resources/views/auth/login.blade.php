@extends('layouts.app')

@section('title', 'Masuk - Ruang Kampus')

@section('content')
<div class="flex justify-center px-8 py-16">
    <div class="bg-white border rounded-2xl p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold">Selamat datang</h1>
        <p class="text-neutral-600 text-sm mt-1 mb-6">Masuk untuk mengelola reservasi.</p>

        @if (session('status'))
            <div class="mb-5 p-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('login.attempt') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold mb-2">Email kampus</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="nama@kampus.ac.id"
                    required
                    autofocus
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
                    placeholder="••••••••"
                    required
                    class="w-full bg-neutral-100 border rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-neutral-900 @error('password') border-red-400 @enderror"
                >
                @error('password')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center text-sm text-neutral-600 mb-6">
                <input type="checkbox" name="remember" class="mr-2 rounded border-neutral-300">
                Ingat saya
            </label>

            <button
                type="submit"
                class="w-full bg-blue-400 hover:bg-blue-500 text-neutral-900 border border-blue-500 rounded-lg py-3 text-sm font-semibold transition"
            >
                Masuk
            </button>
        </form>
    </div>
</div>
@endsection