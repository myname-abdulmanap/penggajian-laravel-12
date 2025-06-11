<?php

namespace App\Http\Controllers;

use App\Models\Allowance;
use Illuminate\Http\Request;

class AllowanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $allowances = Allowance::all();
        return view('allowances.index', compact('allowances'));
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

        if ($validatedData['type'] === 'fixed') {
            $validatedData['percentage'] = null;
        } else {
            $validatedData['amount'] = null;
        }

        Allowance::create($validatedData);

        return redirect()->route('allowances.index')->with('success', 'Tunjangan berhasil dibuat.');
    }

    public function edit($id)
    {
        $allowance = Allowance::findOrFail($id);
        return view('allowances.edit', compact('allowance'));
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

        $allowance = Allowance::findOrFail($id);
        $allowance->update($validatedData);

        return redirect()->route('allowances.index')->with('success', 'Tunjangan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $allowance = Allowance::findOrFail($id);
        $allowance->delete();

        return redirect()->route('allowances.index')->with('success', 'Tunjangan berhasil dihapus.');
    }
}
