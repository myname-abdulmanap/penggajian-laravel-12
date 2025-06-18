<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Allowance;
use App\Models\Deduction;
use App\Models\Leave;
use App\Models\AttendanceSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'karyawan') {
            $salaries = Salary::where('users_id', $user->users_id)->get();
        } else {
            $salaries = Salary::with('user')->get();
        }

        return view('salary.index', compact('salaries'));
    }

    public function create()
    {
        $users = User::all();
        $allowances = Allowance::all();
        $deductions = Deduction::all();
        $month = now()->month;
        $year = now()->year;

        // Total hadir
        $absences = Attendance::selectRaw('users_id, COUNT(*) as total_absen')
            ->where('status', 'hadir')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('users_id')
            ->pluck('total_absen', 'users_id');

        // Total cuti
        $cutis = Leave::selectRaw('user_id, COUNT(*) as total_cuti')
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->groupBy('user_id')
            ->pluck('total_cuti', 'user_id');

        // Keterlambatan
        $settings = AttendanceSetting::first();
        $lateCounts = Attendance::selectRaw('users_id, COUNT(*) as total_telat')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('check_in', '>', $settings->jam_masuk)
            ->groupBy('users_id')
            ->pluck('total_telat', 'users_id');

        return view('salary.create', compact('users', 'allowances', 'deductions', 'absences', 'cutis', 'lateCounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'users_id' => 'required|exists:users,users_id',
            'period' => 'required|date',
            'base_salary' => 'required|numeric',
            'overtime' => 'required|numeric',
            'net_salary' => 'required|numeric',
            'total_attendance' => 'nullable|integer',
            'allowance_ids.*' => 'nullable|exists:allowances,allowance_id',
            'deduction_ids.*' => 'nullable|exists:deductions,deduction_id',
        ]);

        $salary = Salary::create($request->only([
            'users_id',
            'period',
            'base_salary',
            'overtime',
            'total_attendance',
            'net_salary'
        ]));

        if ($request->has('allowance_ids')) {
            $salary->allowances()->attach(array_unique($request->allowance_ids));
        }

        if ($request->has('deduction_ids')) {
            $salary->deductions()->attach(array_unique($request->deduction_ids));
        }

        return redirect()->route('salaries.index')->with('success', 'Salary successfully created');
    }

    public function edit($id)
    {
        $salary = Salary::with(['user', 'allowances', 'deductions'])->findOrFail($id);
        $users = User::all();
        $allowances = Allowance::all();
        $deductions = Deduction::all();

        $month = now()->month;
        $year = now()->year;

        $absences = Attendance::selectRaw('users_id, COUNT(*) as total_absen')
            ->where('status', 'hadir')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('users_id')
            ->pluck('total_absen', 'users_id');

        $cutis = Leave::selectRaw('user_id, COUNT(*) as total_cuti')
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->groupBy('users_id')
            ->pluck('total_cuti', 'users_id');

        $settings = AttendanceSetting::first();
        $lateCounts = Attendance::selectRaw('users_id, COUNT(*) as total_telat')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('check_in', '>', $settings->jam_masuk)
            ->groupBy('users_id')
            ->pluck('total_telat', 'users_id');

        return view('salary.edit', compact('salary', 'users', 'allowances', 'deductions', 'absences', 'cutis', 'lateCounts'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'users_id' => 'required|exists:users,users_id',
            'period' => 'required|date',
            'base_salary' => 'required|numeric',
            'overtime' => 'required|numeric',
            'net_salary' => 'required|numeric',
            'total_attendance' => 'nullable|integer',
            'allowance_ids.*' => 'nullable|exists:allowances,allowance_id',
            'deduction_ids.*' => 'nullable|exists:deductions,deduction_id',
        ]);

        $salary = Salary::findOrFail($id);
        $salary->update($request->only([
            'users_id',
            'period',
            'base_salary',
            'overtime',
            'total_attendance',
            'net_salary'
        ]));

        $salary->allowances()->sync($request->allowance_ids ?? []);
        $salary->deductions()->sync($request->deduction_ids ?? []);

        return redirect()->route('salaries.index')->with('success', 'Salary successfully updated');
    }

    public function show($id)
    {
        $salary = Salary::with(['user', 'allowances', 'deductions'])->findOrFail($id);
        $user = $salary->user;

        $month = Carbon::parse($salary->period)->month;
        $year = Carbon::parse($salary->period)->year;

        $totalCuti = Leave::where('user_id', $user->users_id)
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->count();

        $settings = AttendanceSetting::first();

        $lateCount = Attendance::where('users_id', $user->users_id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('check_in', '>', $settings->jam_masuk)
            ->count();

        return view('salary.show', compact('salary', 'totalCuti', 'lateCount'));
    }

    public function destroy($id)
    {
        Salary::findOrFail($id)->delete();
        return back();
    }

    public function getAbsensi(Request $request)
    {
        $userId = $request->query('user_id');
        $period = $request->query('period');

        if (!$userId || !$period) {
            return response()->json(['total_absensi' => 0]);
        }

        try {
            $date = Carbon::parse($period);
            $month = $date->month;
            $year = $date->year;

            $totalAbsensi = Attendance::where('users_id', $userId)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->count();

            return response()->json([
                'total_absensi' => $totalAbsensi
            ]);
        } catch (\Exception $e) {
            return response()->json(['total_absensi' => 0]);
        }
    }

    public function downloadPdf($id)
    {
        $salary = Salary::with(['user', 'allowances', 'deductions'])->findOrFail($id);
        $user = $salary->user;

        $month = Carbon::parse($salary->period)->month;
        $year = Carbon::parse($salary->period)->year;

        $totalCuti = Leave::where('user_id', $user->users_id)
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->count();

        $settings = AttendanceSetting::first();

        $lateCount = Attendance::where('users_id', $user->users_id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('check_in', '>', $settings->jam_masuk)
            ->count();

        $pdf = PDF::loadView('salary.pdf', compact('salary', 'totalCuti', 'lateCount'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'sans-serif'
        ]);

        $filename = 'Slip_Gaji_' . str_replace(' ', '_', $salary->user->name) . '_' . date('Y-m', strtotime($salary->period)) . '.pdf';

        return $pdf->download($filename);
    }

    public function viewPdf($id)
    {
        $salary = Salary::with(['user', 'allowances', 'deductions'])->findOrFail($id);
        $user = $salary->user;

        $month = Carbon::parse($salary->period)->month;
        $year = Carbon::parse($salary->period)->year;

        $totalCuti = Leave::where('user_id', $user->users_id)
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->count();

        $settings = AttendanceSetting::first();

        $lateCount = Attendance::where('users_id', $user->users_id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('check_in', '>', $settings->jam_masuk)
            ->count();

        $pdf = PDF::loadView('salary.pdf', compact('salary', 'totalCuti', 'lateCount'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('slip_gaji.pdf');
    }

    // Tambahkan method ini ke dalam SalaryController

    public function getCuti(Request $request)
    {
        $userId = $request->query('user_id');
        $period = $request->query('period');

        if (!$userId || !$period) {
            return response()->json(['total_cuti' => 0]);
        }

        try {
            $date = Carbon::parse($period);
            $month = $date->month;
            $year = $date->year;

            $totalCuti = Leave::where('user_id', $userId)
                ->whereMonth('start_date', $month)
                ->whereYear('start_date', $year)
                ->count();

            return response()->json([
                'total_cuti' => $totalCuti
            ]);
        } catch (\Exception $e) {
            return response()->json(['total_cuti' => 0]);
        }
    }

    public function getKeterlambatan(Request $request)
    {
        $userId = $request->query('user_id');
        $period = $request->query('period');

        if (!$userId || !$period) {
            return response()->json(['total_telat' => 0]);
        }

        try {
            $date = Carbon::parse($period);
            $month = $date->month;
            $year = $date->year;

            $settings = AttendanceSetting::first();

            $totalTelat = Attendance::where('users_id', $userId)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('check_in', '>', $settings->jam_masuk)
                ->count();

            return response()->json([
                'total_telat' => $totalTelat
            ]);
        } catch (\Exception $e) {
            return response()->json(['total_telat' => 0]);
        }
    }
}
