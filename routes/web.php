<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminFacilityController;
use App\Http\Controllers\PetugasReservationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserReservationController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ReservationController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/reservations', [UserReservationController::class, 'index'])
    ->name('reservations.index');

    // FR-06 & FR-07 - Laporan Kerusakan
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // FR-08 - Antrean Laporan Petugas
    Route::get('/petugas/reports', [ReportController::class, 'petugasQueue'])
        ->middleware('checkrole:petugas,admin')
        ->name('petugas.reports.index');


    // Group Route untuk Petugas & Admin
    Route::middleware(['checkrole:petugas,admin'])->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/reservations', [PetugasReservationController::class, 'index'])->name('reservations.index');
        Route::patch('/reservations/{reservation}/approve', [PetugasReservationController::class, 'approve'])->name('reservations.approve');
        Route::patch('/reservations/{reservation}/reject', [PetugasReservationController::class, 'reject'])->name('reservations.reject');
        Route::patch('/reservations/{reservation}/cancel', [PetugasReservationController::class, 'emergencyCancel'])->name('reservations.cancel');
    });

    Route::middleware(['checkrole:admin'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');

        Route::patch('/users/{user}/verify', [AdminUserController::class, 'verify'])->name('users.verify');
        Route::delete('/users/{user}/reject', [AdminUserController::class, 'reject'])->name('users.reject');

        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('/facilities', [AdminFacilityController::class, 'index'])->name('facilities.index');
        Route::post('/facilities', [AdminFacilityController::class, 'store'])->name('facilities.store');
        Route::put('/facilities/{facility}', [AdminFacilityController::class, 'update'])->name('facilities.update');
        Route::delete('/facilities/{facility}', [AdminFacilityController::class, 'destroy'])->name('facilities.destroy');

    });

});
Route::get('/fasilitas/{facility}', [FacilityController::class, 'show'])->name('facilities.show');

Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');