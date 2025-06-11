<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $deductions = Deduction::all();
        return view('deductions.index', compact('deductions'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:fixed,percentage',
            'amount' => 'nullable|numeric|required_if:type,fixed',
            'percentage' => 'nullable|numeric|required_if:type,percentage|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        // Pastikan kolom yang tidak dipakai diset null
        if ($validatedData['type'] === 'fixed') {
            $validatedData['percentage'] = null;
        } else {
            $validatedData['amount'] = null;
        }

        Deduction::create($validatedData);

        return redirect()->route('deductions.index')->with('success', 'Potongan berhasil dibuat.');
    }

    public function edit($id)
    {
        $deduction = Deduction::findOrFail($id);
        return view('deductions.edit', compact('deduction'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:fixed,percentage',
            'amount' => 'nullable|numeric|required_if:type,fixed',
            'percentage' => 'nullable|numeric|required_if:type,percentage|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        if ($validatedData['type'] === 'fixed') {
            $validatedData['percentage'] = null;
        } else {
            $validatedData['amount'] = null;
        }

        $deduction = Deduction::findOrFail($id);
        $deduction->update($validatedData);

        return redirect()->route('deductions.index')->with('success', 'Potongan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $deduction = Deduction::findOrFail($id);
        $deduction->delete();

        return redirect()->route('deductions.index')->with('success', 'Potongan berhasil dihapus.');
    }
}
