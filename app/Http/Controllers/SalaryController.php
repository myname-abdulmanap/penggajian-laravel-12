<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Allowance;
use App\Models\Deduction;
use Barryvdh\DomPDF\Facade\Pdf;

class SalaryController extends Controller
{
      public function index() {

        $user = auth()->user();

        if ($user->role === 'karyawan') {
            // Hanya tampilkan gaji milik karyawan ini
            $salaries = Salary::where('users_id', $user->users_id)->get();
        } else {
            // Admin/HR bisa melihat semua data
            $salaries = Salary::with('user')->get();
        }

        return view('salary.index', compact('salaries'));
    }

    public function create()
    {
        $users = User::all();
        $allowances = Allowance::all();
        $deductions = Deduction::all();

        // Hitung total absen tiap user, misal dalam bulan ini
        $absences = Attendance::selectRaw('users_id, COUNT(*) as total_absen')
            ->where('status', 'hadir')
            ->whereMonth('date', now()->month)
            ->groupBy('users_id')
            ->pluck('total_absen', 'users_id');

        return view('salary.create', compact('users', 'allowances', 'deductions', 'absences'));
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

        // Hitung total absen tiap user, misal dalam bulan ini
        $absences = Attendance::selectRaw('users_id, COUNT(*) as total_absen')
            ->where('status', 'hadir')
            ->whereMonth('date', now()->month)
            ->groupBy('users_id')
            ->pluck('total_absen', 'users_id');

        return view('salary.edit', compact('salary', 'users', 'allowances', 'deductions', 'absences'));
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

        if ($request->has('allowance_ids')) {
            $salary->allowances()->sync(array_unique($request->allowance_ids));
        } else {
            $salary->allowances()->detach();
        }

        if ($request->has('deduction_ids')) {
            $salary->deductions()->sync(array_unique($request->deduction_ids));
        } else {
            $salary->deductions()->detach();
        }

        return redirect()->route('salaries.index')->with('success', 'Salary successfully updated');
    }
    public function show($id)
    {
        $salary = Salary::with(['user', 'allowances', 'deductions'])->findOrFail($id);
        return view('salary.show', compact('salary'));
    }

    public function destroy($id) {
        Salary::findOrFail($id)->delete();
        return back();
    }


    public function getAbsensi(Request $request)
    {
        $userId = $request->query('user_id');
        $period = $request->query('period'); // format: YYYY-MM-DD

        if (!$userId || !$period) {
            return response()->json(['total_absensi' => 0]);
        }

        try {
            $date = \Carbon\Carbon::parse($period);
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

        $pdf = PDF::loadView('salary.pdf', compact('salary'));

        // Set paper size dan orientation
        $pdf->setPaper('A4', 'portrait');

        // Set options untuk better rendering
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

        $pdf = PDF::loadView('salary.pdf', compact('salary'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('slip_gaji.pdf');
    }
}
