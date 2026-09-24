<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminFacilityController;
use App\Http\Controllers\PetugasReservationController;
use App\Http\Controllers\PetugasReportController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Group Route untuk Petugas & Admin
    Route::middleware(['checkrole:petugas,admin'])->prefix('petugas')->name('petugas.')->group(function () {
        // FR-09: Approve / Reject Reservasi
        Route::get('/reservations', [PetugasReservationController::class, 'index'])->name('reservations.index');
        Route::patch('/reservations/{reservation}/approve', [PetugasReservationController::class, 'approve'])->name('reservations.approve');
        Route::patch('/reservations/{reservation}/reject', [PetugasReservationController::class, 'reject'])->name('reservations.reject');
        // FR-10: Pembatalan Darurat
        Route::patch('/reservations/{reservation}/cancel', [PetugasReservationController::class, 'emergencyCancel'])->name('reservations.cancel');

        // FR-11: Kelola Status Laporan Kerusakan
        Route::get('/reports', [PetugasReportController::class, 'index'])->name('reports.index');
        Route::patch('/reports/{report}/status', [PetugasReportController::class, 'updateStatus'])->name('reports.updateStatus');
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
