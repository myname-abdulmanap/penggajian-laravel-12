<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SalaryController;

// Route halaman welcome
Route::get('/', function () {
    return view('welcome');
});

// Auth route default Laravel
Auth::routes();

// Route home setelah login
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Routes untuk User Management dengan AdminMiddleware
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    // Daftar user
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/tambah', [UserController::class, 'create'])->name('user.create');
    Route::post('/user/tambah', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/edit/{users_id}', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/edit/{users_id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/hapus/{users_id}', [UserController::class, 'destroy'])->name('user.delete');
    Route::get('/user/detail/{users_id}', [UserController::class, 'show'])->name('user.show');
});

// Routes untuk Profile (bisa diakses semua authenticated user)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
});

// Route untuk check status token via AJAX (PENTING: di luar admin prefix!)
Route::get('/attendance/check-token-status', [AttendanceController::class, 'checkTokenStatus'])
    ->name('attendance.check-token-status')
    ->middleware('auth');

// ---------------- ADMIN: Attendance ----------------
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::get('/attendance/riwayat', [AttendanceController::class, 'indexAdminAbsen'])->name('admin.attendance.riwayat');
    Route::get('/attendance/settings', [AttendanceController::class, 'settings'])->name('attendance.settings');
    Route::post('/attendance/settings', [AttendanceController::class, 'updateSettings'])->name('attendance.updateSettings');
    Route::get('/attendance/qrcode', [AttendanceController::class, 'generateQrToken'])->name('attendance.qrcode');
});

// ---------------- KARYAWAN: Attendance ----------------
Route::middleware('auth')->prefix('karyawan')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'indexAbsensiKaryawan'])->name('attendance.index');
    Route::get('/attendance/scan', [AttendanceController::class, 'scanForm'])->name('attendance.scan');
    Route::post('/attendance/scan', [AttendanceController::class, 'scanSubmit'])->name('attendance.scanSubmit');
    Route::get('/attendance/riwayat', [AttendanceController::class, 'riwayat'])->name('attendance.riwayat');
});

// Tambahkan routes berikut ke dalam file routes/web.php

// Routes untuk export PDF
Route::middleware(['auth'])->group(function () {

    // Route untuk halaman filter export
    Route::get('/user/export-filter', [UserController::class, 'exportFilter'])->name('user.export-filter');

    // Route untuk download PDF
    Route::get('/user/download-pdf', [UserController::class, 'downloadPdf'])->name('user.download-pdf');

    // Route untuk preview PDF (opsional)
    Route::get('/user/preview-pdf', [UserController::class, 'previewPdf'])->name('user.preview-pdf');
     // Route untuk menampilkan halaman filter export
    Route::get('/attendance/export-filter', [AttendanceController::class, 'exportFilter'])->name('attendance.export-filter');

    // Route untuk mendownload laporan PDF
    Route::get('/attendance/download-pdf', [AttendanceController::class, 'downloadPdf'])->name('attendance.download-pdf');

    // Route untuk preview PDF (menampilkan dalam browser, bukan download)
    Route::get('/attendance/preview-pdf', [AttendanceController::class, 'previewPdf'])->name('attendance.preview-pdf');

});

Route::resource('salaries', SalaryController::class);
