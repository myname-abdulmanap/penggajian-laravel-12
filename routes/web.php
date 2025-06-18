<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\LeaveController;
use App\Models\Leave;
use Illuminate\Support\Facades\Artisan;



// Route halaman welcome
Route::get('/', function () {
    return view('welcome');
});

Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return '✅ Cache cleared!';
});
Route::get('/link-storage', function () {
    Artisan::call('storage:link');
    return 'Storage link berhasil dibuat!';
});

Route::get('/cek', function () {
    return '✅ Routing OK!';
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

    // Route untuk halaman filter export
    Route::get('/leaves/export-filter', [LeaveController::class, 'exportFilter'])->name('leaves.export-filter');

    // Route untuk download PDF
    Route::get('/leaves/download-pdf', [LeaveController::class, 'downloadPdf'])->name('leaves.download-pdf');

    // Route untuk preview PDF (opsional)
    Route::get('/leaves/preview-pdf', [LeaveController::class, 'previewPdf'])->name('leaves.preview-pdf');


    // Route untuk menampilkan halaman filter export
    Route::get('/attendance/export-filter', [AttendanceController::class, 'exportFilter'])->name('attendance.export-filter');

    // Route untuk mendownload laporan PDF
    Route::get('/attendance/download-pdf', [AttendanceController::class, 'downloadPdf'])->name('attendance.download-pdf');

    // Route untuk preview PDF (menampilkan dalam browser, bukan download)
    Route::get('/attendance/preview-pdf', [AttendanceController::class, 'previewPdf'])->name('attendance.preview-pdf');
    Route::resource('salaries', SalaryController::class);
    Route::resource('allowances', AllowanceController::class);
    Route::resource('deductions', DeductionController::class);
    Route::get('/get-absensi', [App\Http\Controllers\SalaryController::class, 'getAbsensi']);
    Route::get('salaries/{id}/pdf', [SalaryController::class, 'downloadPdf'])->name('salaries.pdf');
    Route::get('salaries/{id}/pdf-view', [SalaryController::class, 'viewPdf'])->name('salaries.pdf.view');
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::put('/leaves/{id}', [LeaveController::class, 'update'])->name('leaves.update');
    Route::delete('/leaves/{id}', [LeaveController::class, 'destroy'])->name('leaves.destroy');
});


// Route baru yang perlu ditambahkan:
Route::get('/get-cuti', [SalaryController::class, 'getCuti'])->name('get.cuti');
Route::get('/get-keterlambatan', [SalaryController::class, 'getKeterlambatan'])->name('get.keterlambatan');
