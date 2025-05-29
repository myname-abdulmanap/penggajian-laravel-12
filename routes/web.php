<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AttendanceController;
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
    Route::put('/user/edit/{users_id}', [UserController::class, 'update'])->name('user.update'); // Perbaiki konsistensi parameter
    Route::delete('/user/hapus/{users_id}', [UserController::class, 'destroy'])->name('user.delete'); // Perbaiki konsistensi parameter

    // Optional: Route untuk detail user
    Route::get('/user/detail/{users_id}', [UserController::class, 'show'])->name('user.show');

});

// User management routes
    // Route::resource('user', UserController::class, [
    //     'parameters' => ['user' => 'users_id']
    // ]);

// Routes untuk Profile (bisa diakses semua authenticated user)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
});
// Updated routes for admin attendance management
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('admin/attendance')->name('admin.attendance.')->group(function () {
        // Dashboard presensi
        Route::get('/', [AttendanceController::class, 'index'])->name('index');

        // Generate QR Code - Updated route name to match view
        Route::get('/generate-qr', [AttendanceController::class, 'generateQrCode'])->name('generate-qr');

        // Alternative route for backward compatibility
        Route::get('/qrcode', [AttendanceController::class, 'generateQrCode'])->name('qrcode');

        // Test Token API - New route untuk JavaScript di view
        Route::get('/test-token', [AttendanceController::class, 'testToken'])->name('test-token');

        // Debug Tokens API - New route untuk debugging
        Route::get('/debug-tokens', [AttendanceController::class, 'debugTokens'])->name('debug-tokens');

        // Laporan harian
        Route::get('/report', [AttendanceController::class, 'dailyReport'])->name('report');

        // Export data
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');

        // API endpoints
        Route::get('/today', function () {
            $today = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d');
            $attendances = \App\Models\Attendance::whereDate('date', $today)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($attendances);
        })->name('today');

        Route::get('/valid-tokens', [AttendanceController::class, 'getValidTokens'])->name('valid-tokens');
    });
});

// Routes untuk karyawan (employee attendance routes)
Route::middleware(['auth'])->group(function () {
    // Form scan QR untuk presensi
    Route::get('/presensi/scan', [AttendanceController::class, 'scanForm'])->name('presensi.scan');

    // Process scan dan simpan presensi
    Route::post('/presensi/store', [AttendanceController::class, 'store'])->name('presensi.store');

    // Riwayat presensi karyawan
    Route::get('/presensi/riwayat', [AttendanceController::class, 'riwayat'])->name('presensi.riwayat');
});

// API Routes (jika diperlukan untuk mobile app)
Route::middleware(['auth:sanctum'])->prefix('api/v1')->group(function () {
    Route::post('/attendance/checkin', [AttendanceController::class, 'store']);
    Route::get('/attendance/history', [AttendanceController::class, 'riwayat']);
    Route::get('/attendance/today', function () {
        $user = auth()->user();
        $attendance = \App\Models\Attendance::getTodayAttendance($user->users_id);

        return response()->json([
            'has_attended' => $attendance ? true : false,
            'attendance' => $attendance,
            'can_checkout' => $attendance && !$attendance->check_out
        ]);
    });
});
