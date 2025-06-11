<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Overtime;

class SalaryController extends Controller
{
      public function index() { 
        $salaries = Salary::with('user')->latest()->get();
        return view('salary.index', compact('salaries'));
    }

    public function create() {
        $users = User::all();
        return view('salary.create', compact('users'));
    }

    public function store(Request $request) {
        $userId = $request->user_id;
        $month = $request->month;
        $year = $request->year;

        $base = $request->base_salary;
        $allowance = $request->allowance;

        $overtime = Overtime::where('user_id', $userId)->whereMonth('date', $month)->whereYear('date', $year)->sum('total');

        $attendances = Attendance::where('users_id', $userId)->whereMonth('date', $month)->whereYear('date', $year)->get();

        $deduction = 0;
        foreach ($attendances as $att) {
            if ($att->status == 'alpha') $deduction += 100000;
            elseif ($att->status == 'terlambat') $deduction += 20000;
        }

        $net = $base + $allowance + $overtime - $deduction;

        Salary::create([
            'user_id' => $userId,
            'period' => "$year-$month-01",
            'base_salary' => $base,
            'allowance' => $allowance,
            'overtime' => $overtime,
            'deduction' => $deduction,
            'net_salary' => $net,
        ]);

        return redirect()->route('salaries.index');
    }

    public function destroy($id) {
        Salary::findOrFail($id)->delete();
        return back();
    }
}
