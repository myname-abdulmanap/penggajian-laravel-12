<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\QrToken;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Barryvdh\DomPDF\Facade\Pdf;


class AttendanceController extends Controller
{
    public function index()
    {
        return view('admin.attendance.index');
    }
    public function indexAdminAbsen(Request $request)
    {
        $today = Carbon::today();


        $setting = AttendanceSetting::first();
        $jamMasuk = $setting ? Carbon::parse($setting->jam_masuk) : null;

        $query = Attendance::with('user')->whereDate('date', $today);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        }

        $attendances = $query->orderBy('created_at', 'desc')->get();

        // Tambahkan status keterlambatan
        foreach ($attendances as $attendance) {
            // Status keterlambatan
            if ($attendance->check_in && $jamMasuk) {
                $checkInTime = Carbon::parse($attendance->check_in);
                $attendance->status = $checkInTime->gt($jamMasuk) ? 'terlambat' : 'tepat waktu';
            } else {
                $attendance->status = 'belum check-in';
            }


            $attendanceDate = Carbon::parse($attendance->date);
            $days = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
            ];
            $dayName = $days[$attendanceDate->format('l')];
            $attendance->formatted_date = $dayName . ', ' . $attendanceDate->format('d-m-Y');
        }


        return view('admin.attendance.riwayat', compact('attendances'));
    }


    // Admin: Pengaturan jam kerja
    public function settings()
    {
        $setting = AttendanceSetting::getCurrent();
        return view('admin.attendance.settings', compact('setting'));
    }

    public function updateSettings(Request $request)
    {
        // Validasi format HH:MM dari input type="time"
        $request->validate([
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i',
        ]);


        $jamMasuk = $request->input('jam_masuk') . ':00';
        $jamPulang = $request->input('jam_pulang') . ':00';

        $setting = AttendanceSetting::getCurrent();
        $setting->update([
            'jam_masuk' => $jamMasuk,
            'jam_pulang' => $jamPulang,
        ]);

        return back()->with('success', 'Pengaturan jam kerja diperbarui.');
    }


    // Admin: Generate QR Token
    public function generateQrToken()
    {
        $existingToken = QrToken::where('type', 'absensi')
            ->where('expires_at', '>', now())
            ->where('is_used', false)
            ->latest()
            ->first();

        if ($existingToken) {
            $token = $existingToken->token;
        } else {
            $token = Str::random(40);
            QrToken::create([
                'token' => $token,
                'type' => 'absensi',
                'expires_at' => now()->addMinutes(1440),
                'is_used' => false,
            ]);
        }

        $url = route('attendance.scan') . '?token=' . $token;

        $qrCode = new \Endroid\QrCode\QrCode($url);
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);

        $qrImage = $result->getDataUri();

        return view('admin.attendance.qrcode', compact('qrImage', 'token'));
    }

    // Method baru untuk check status token via AJAX
    public function checkTokenStatus(Request $request)
    {
        $token = $request->get('token');

        $tokenData = QrToken::where('token', $token)->first();

        if (!$tokenData) {
            return response()->json(['status' => 'invalid']);
        }

        if ($tokenData->is_used) {
            return response()->json([
                'status' => 'used',
                'used_by' => $tokenData->used_by,
                'used_at' => $tokenData->updated_at->format('H:i:s')
            ]);
        }

        if ($tokenData->expires_at < now()) {
            return response()->json(['status' => 'expired']);
        }

        return response()->json(['status' => 'active']);
    }

    // Karyawan: Form untuk scan QR
    public function scanForm()
    {
        return view('karyawan.attendance.scan');
    }

    // Karyawan: Proses scan QR atau input token
    public function scanSubmit(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'location' => 'nullable|string'
        ]);

        $token = QrToken::where('token', $request->token)->valid()->first();

        if (!$token) {
            return back()->with('error', 'Token tidak valid atau sudah digunakan.');
        }

        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            ['users_id' => $user->users_id, 'date' => $today],
            ['status' => 'hadir']
        );

        if (!$attendance->check_in) {
            $attendance->check_in = now()->format('H:i:s');
        } elseif (!$attendance->check_out) {
            $attendance->check_out = now()->format('H:i:s');
        } else {
            return back()->with('error', 'Anda sudah absen masuk dan keluar hari ini.');
        }

        $attendance->location = $request->location;
        $attendance->save();

        $token->markAsUsed($user->users_id);

        return redirect()->route('attendance.riwayat')->with('success', 'Presensi berhasil disimpan.');

    }

    // Karyawan: Riwayat absen
    public function riwayat(Request $request)
{
    $user = Auth::user();
    $today = Carbon::today();

    $setting = AttendanceSetting::first();
    $jamMasuk = $setting ? Carbon::parse($setting->jam_masuk) : null;

    $query = Attendance::with('user')
        ->where('users_id', $user->users_id);

    if ($request->start_date && $request->end_date) {
        $query->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59',
        ]);
    }

    $attendances = $query->orderBy('created_at', 'desc')->get();

    foreach ($attendances as $attendance) {
        if ($attendance->check_in && $jamMasuk) {
            $checkInTime = Carbon::parse($attendance->check_in);
            $attendance->status = $checkInTime->gt($jamMasuk) ? 'terlambat' : 'tepat waktu';
        } else {
            $attendance->status = 'belum check-in';
        }

        $attendanceDate = Carbon::parse($attendance->date);
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $dayName = $days[$attendanceDate->format('l')];
        $attendance->formatted_date = $dayName . ', ' . $attendanceDate->format('d-m-Y');
    }

    return view('karyawan.attendance.riwayat', compact('attendances'));
}


  public function indexAbsensiKaryawan()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('users_id', $user->users_id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->users_id = $user->users_id;
            $attendance->date = $today;
            $attendance->status = 'hadir';
        }

        return view('karyawan.attendance.index', compact('attendance'));


    }

    // Method untuk menampilkan halaman filter export
public function exportFilter()
{
    return view('admin.attendance.export-filter');
}

// Method untuk download PDF
public function downloadPdf(Request $request)
{
    $query = Attendance::with('user');

    // Filter berdasarkan tanggal
    if ($request->start_date && $request->end_date) {
        $query->whereBetween('date', [
            $request->start_date,
            $request->end_date
        ]);
    }

    // Filter berdasarkan user (karyawan)
    if ($request->user_id) {
        $query->where('users_id', $request->user_id);
    }

    // Filter berdasarkan status
    if ($request->status) {
        $query->where('status', $request->status);
    }

    // Filter berdasarkan status keterlambatan
    $setting = AttendanceSetting::first();
    $jamMasuk = $setting ? Carbon::parse($setting->jam_masuk) : null;

    $attendances = $query->orderBy('date', 'desc')->get();

    // Tambahkan status keterlambatan dan format tanggal
    foreach ($attendances as $attendance) {
        if ($attendance->check_in && $jamMasuk) {
            $checkInTime = Carbon::parse($attendance->check_in);
            $attendance->late_status = $checkInTime->gt($jamMasuk) ? 'Terlambat' : 'Tepat Waktu';
        } else {
            $attendance->late_status = 'Belum Check-in';
        }

        $attendanceDate = Carbon::parse($attendance->date);
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $dayName = $days[$attendanceDate->format('l')];
        $attendance->formatted_date = $dayName . ', ' . $attendanceDate->format('d-m-Y');
    }

    // Filter berdasarkan status keterlambatan jika diminta
    if ($request->late_status) {
        $attendances = $attendances->filter(function($attendance) use ($request) {
            return $attendance->late_status == $request->late_status;
        });
    }

    $data = [
        'attendances' => $attendances,
        'filters' => [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'user_id' => $request->user_id,
            'status' => $request->status,
            'late_status' => $request->late_status,
        ],
        'generated_at' => now()->format('d-m-Y H:i:s'),
        'setting' => $setting
    ];

    $pdf = Pdf::loadView('admin.attendance.pdf-template', $data);
    $pdf->setPaper('A4', 'landscape');

    $filename = 'laporan-absensi-' . now()->format('Y-m-d-His') . '.pdf';

    return $pdf->download($filename);
}

// Method untuk preview PDF (opsional)
public function previewPdf(Request $request)
{
    $query = Attendance::with('user');

    // Filter berdasarkan tanggal
    if ($request->start_date && $request->end_date) {
        $query->whereBetween('date', [
            $request->start_date,
            $request->end_date
        ]);
    }

    // Filter berdasarkan user (karyawan)
    if ($request->user_id) {
        $query->where('users_id', $request->user_id);
    }

    // Filter berdasarkan status
    if ($request->status) {
        $query->where('status', $request->status);
    }

    $setting = AttendanceSetting::first();
    $jamMasuk = $setting ? Carbon::parse($setting->jam_masuk) : null;

    $attendances = $query->orderBy('date', 'desc')->get();

    // Tambahkan status keterlambatan dan format tanggal
    foreach ($attendances as $attendance) {
        if ($attendance->check_in && $jamMasuk) {
            $checkInTime = Carbon::parse($attendance->check_in);
            $attendance->late_status = $checkInTime->gt($jamMasuk) ? 'Terlambat' : 'Tepat Waktu';
        } else {
            $attendance->late_status = 'Belum Check-in';
        }

        $attendanceDate = Carbon::parse($attendance->date);
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $dayName = $days[$attendanceDate->format('l')];
        $attendance->formatted_date = $dayName . ', ' . $attendanceDate->format('d-m-Y');
    }

    // Filter berdasarkan status keterlambatan jika diminta
    if ($request->late_status) {
        $attendances = $attendances->filter(function($attendance) use ($request) {
            return $attendance->late_status == $request->late_status;
        });
    }

    $data = [
        'attendances' => $attendances,
        'filters' => [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'user_id' => $request->user_id,
            'status' => $request->status,
            'late_status' => $request->late_status,
        ],
        'generated_at' => now()->format('d-m-Y H:i:s'),
        'setting' => $setting
    ];

    $pdf = Pdf::loadView('admin.attendance.pdf-template', $data);
    $pdf->setPaper('A4', 'landscape');

    return $pdf->stream('preview-laporan-absensi.pdf');
}
}
