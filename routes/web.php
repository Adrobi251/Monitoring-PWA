<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MahasiswaDashboardController;
use App\Http\Controllers\DosenDashboardController;
use App\Http\Controllers\JadwalKonsultasiController;
use App\Http\Controllers\PengajuanKonsultasiController;
use App\Http\Controllers\CatatanKonsultasiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user && $user->role === 'dosen') {
        return redirect()->route('dosen.dashboard');
    }
    return redirect()->route('mahasiswa.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('mahasiswa')->name('mahasiswa.')->middleware(['auth', 'verified', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/jadwal', [MahasiswaDashboardController::class, 'jadwal'])->name('jadwal');
    Route::get('/dashboard/riwayat', [MahasiswaDashboardController::class, 'riwayat'])->name('riwayat');
    Route::post('/pengajuan', [PengajuanKonsultasiController::class, 'store'])->name('pengajuan.store');
    Route::get('/pengajuan/{pengajuan}/catatan', [CatatanKonsultasiController::class, 'show'])->name('pengajuan.catatan.show');
});

Route::prefix('dosen')->name('dosen.')->middleware(['auth', 'verified', 'role:dosen'])->group(function () {
    Route::get('/dashboard', [DosenDashboardController::class, 'index'])->name('dashboard');
    Route::post('/mahasiswa/assign', [DosenDashboardController::class, 'assignMahasiswa'])->middleware('throttle:5,1')->name('mahasiswa.assign');
    Route::get('/dashboard/jadwal', [DosenDashboardController::class, 'jadwal'])->name('jadwal');
    Route::get('/dashboard/riwayat', [DosenDashboardController::class, 'riwayat'])->name('riwayat');
    Route::get('/jadwal/create', [JadwalKonsultasiController::class, 'create'])->name('jadwal.create');
    Route::post('/jadwal', [JadwalKonsultasiController::class, 'store'])->name('jadwal.store');
    Route::get('/jadwal/{jadwal}/edit', [JadwalKonsultasiController::class, 'edit'])->name('jadwal.edit');
    Route::put('/jadwal/{jadwal}', [JadwalKonsultasiController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{jadwal}', [JadwalKonsultasiController::class, 'destroy'])->name('jadwal.destroy');
    Route::match(['put', 'post'], '/pengajuan/{pengajuan}/status', [PengajuanKonsultasiController::class, 'updateStatus'])->name('pengajuan.status');
    Route::get('/pengajuan/{pengajuan}/catatan/create', [CatatanKonsultasiController::class, 'create'])->name('pengajuan.catatan.create');
    Route::post('/pengajuan/{pengajuan}/catatan', [CatatanKonsultasiController::class, 'store'])->name('pengajuan.catatan.store');
    Route::get('/pengajuan/{pengajuan}/catatan/{catatan}/edit', [CatatanKonsultasiController::class, 'edit'])->name('pengajuan.catatan.edit');
    Route::put('/pengajuan/{pengajuan}/catatan/{catatan}', [CatatanKonsultasiController::class, 'update'])->name('pengajuan.catatan.update');
    Route::get('/pengajuan/{pengajuan}', [DosenDashboardController::class, 'riwayatShow'])->name('riwayat.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
