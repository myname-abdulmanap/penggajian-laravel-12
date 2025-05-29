<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $attendances = Attendance::where('date', $today)->with('user')->get();
        return view('admin.attendance.index', compact('attendances'));
    }

    public function scanForm()
    {
        return view('karyawan.presensi.scan');
    }


   public function store(Request $request)
{
    $user = Auth::user();
    $today = Carbon::today();

    $existing = Attendance::where('users_id', $user->users_id)
        ->where('date', $today)
        ->first();

    if (!$existing) {
        Attendance::create([
            'users_id' => $user->users_id,
            'date' => $today,
            'check_in' => now()->format('H:i:s'), // created_at jamnya
            'location' => $request->location,
            'status' => 'hadir',
        ]);
    } else {
        $existing->update([
            'check_out' => now()->format('H:i:s'), // updated_at jamnya
        ]);
    }

    return redirect()->back()->with('success', 'Presensi berhasil disimpan!');
}




    public function riwayat()
    {
        $user = Auth::user();
        $attendances = Attendance::where('users_id', $user->users_id)
            ->orderBy('date', 'desc')
            ->get();

        return view('karyawan.presensi.riwayat', compact('attendances'));
    }

    public function generateQrCode()
    {
        $token = Str::random(40);
        $url = route('presensi.scan') . '?token=' . $token;

        // Create QR code using new API
        $qrCode = new QrCode($url);
        $writer = new PngWriter();

        // Generate the result object
        $result = $writer->write($qrCode);

        // Get data URI for display
        $qrImage = $result->getDataUri();

        return view('admin.attendance.qrcode', compact('qrImage', 'token'));
    }
}
