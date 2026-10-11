# Ruang Kampus

Sistem Reservasi & Pelaporan Fasilitas Kampus berbasis web. Pengguna dapat mengecek ketersediaan ruang dan fasilitas per slot 30 menit, mengajukan reservasi, serta melaporkan kerusakan. Petugas dan admin memproses reservasi, laporan, dan data master secara terpusat.

Dibuat dengan **Laravel** untuk Project PPK 2026.

## Fitur

| Aktor | Fitur |
|---|---|
| **Pengunjung** (tanpa login) | Melihat daftar fasilitas, kalender ketersediaan per 30 menit, serta mencari dan memfilter fasilitas (tipe, lokasi, kapasitas) |
| **Pengguna** | Registrasi mandiri, login/logout, mengajukan reservasi, melihat riwayat dan membatalkan reservasi sendiri, membuat dan memantau laporan kerusakan, notifikasi |
| **Petugas** | Antrean reservasi (setujui, tolak, batalkan darurat), mengelola status laporan kerusakan, mengubah status fasilitas |
| **Admin** | Mendaftarkan, memverifikasi, menolak, dan menghapus akun; CRUD data fasilitas; rekap okupansi dan kerusakan dengan ekspor CSV/Excel/PDF |



## Teknologi

- Laravel (PHP)
- MySQL
- Blade + Tailwind CSS (dimuat lewat CDN, tidak perlu build Node.js)
- Validasi dua sisi: server (Laravel) dan client (JavaScript)

## Prasyarat

Pastikan sudah terpasang:

- **PHP 8.3 atau lebih baru** 
- **Composer**
- **MySQL**, 
- **Git**
- **Koneksi internet** saat menjalankan aplikasi, karena Tailwind CSS dimuat dari CDN

## Menjalankan di Lokal

### 1. Clone repository

```bash
git clone https://github.com/kiyoshiatira/PPK-A08.git
cd PPK-A08
```

### 2. Pasang dependensi

```bash
composer install
```

### 3. Siapkan file environment

```bash
copy .env.example .env
```

> Linux/macOS: `cp .env.example .env`

Buka `.env`, lalu atur koneksi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ruangkampus
DB_USERNAME=root
DB_PASSWORD=
```

Opsional, agar nama bulan dan hari tampil dalam Bahasa Indonesia:

```env
APP_LOCALE=id
```

Lalu buat application key:

```bash
php artisan key:generate
```

### 4. Buat database

1. Nyalakan **Apache** dan **MySQL** di XAMPP Control Panel.
2. Buka `http://localhost/phpmyadmin`.
3. Buat database baru bernama **`ruangkampus`** (collation `utf8mb4_unicode_ci`).

### 5. Migrasi dan isi data awal

```bash
php artisan migrate --seed
```

Perintah ini membuat seluruh tabel, 3 akun demo, 2 fasilitas contoh, dan beberapa reservasi contoh. Untuk mengulang dari nol (**menghapus semua data**):

```bash
php artisan migrate:fresh --seed
```

### 6. Hubungkan folder penyimpanan (untuk foto fasilitas)

```bash
php artisan storage:link
```

### 7. Jalankan aplikasi

```bash
php artisan serve
```

Buka **http://127.0.0.1:8000** di browser.

## Akun Demo

Semua akun menggunakan kata sandi **`password123`**.

| Peran | Email | Halaman utama |
|---|---|---|
| Pengguna | `pengguna@kampus.ac.id` | `/` |
| Petugas | `petugas@kampus.ac.id` | `/petugas/reservations` |
| Admin | `admin@kampus.ac.id` | `/admin/dashboard` |

Akun yang dibuat lewat halaman **Daftar** berstatus belum terverifikasi. Login sebagai admin untuk memverifikasinya terlebih dahulu.

## Struktur Folder

| Folder / File | Isi |
|---|---|
| `app/Http/Controllers` | Logika proses (controller) |
| `app/Http/Middleware/CheckRole.php` | Pembatasan akses berdasarkan peran (`checkrole`) |
| `app/Models` | Model Eloquent |
| `config` | Konfigurasi aplikasi |
| `database/migrations`, `database/seeders` | Struktur tabel dan data awal |
| `resources/views` | Tampilan (Blade), layout bersama di `layouts/app.blade.php` |
| `public` | Aset publik, termasuk `js/form-validation.js` |
| `routes/web.php` | Seluruh rute aplikasi |

## Pemecahan Masalah

| Masalah | Solusi |
|---|---|
| `Unknown database 'ruangkampus'` | Buat database `ruangkampus` di phpMyAdmin (langkah 4) |
| `Connection refused` / `[2002]` | Nyalakan MySQL di XAMPP, dan pastikan `DB_PORT` di `.env` sesuai |
| `No application encryption key has been specified` | Jalankan `php artisan key:generate` |
| `Class ... not found` atau folder `vendor` tidak ada | Jalankan `composer install` |
| Perubahan `.env` tidak terbaca | Jalankan `php artisan config:clear` |
| Duplicate entry saat seeding ulang | Gunakan `php artisan migrate:fresh --seed` |
| Halaman error 419 (Page Expired) | Muat ulang halaman, atau jalankan `php artisan optimize:clear` |
| Foto fasilitas tampil sebagai gambar rusak | Jalankan `php artisan storage:link`. Di Windows, buka terminal sebagai **Administrator**, hindari menaruh project di folder OneDrive, dan pastikan drive berformat NTFS. Jika `public/storage` sudah ada tetapi rusak, hapus dengan `rmdir public\storage` lalu jalankan `storage:link` lagi |
| Tampilan tidak berformat (tanpa gaya) | Pastikan terhubung ke internet, karena Tailwind CSS dimuat dari CDN |