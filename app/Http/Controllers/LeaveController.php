<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LeaveController extends Controller
{
    /**
     * Menampilkan daftar cuti (semua untuk admin, milik sendiri untuk karyawan).
     */
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            $leaves = Leave::with('user')->latest()->get();
        } else {
            $leaves = Leave::where('user_id', Auth::id())->latest()->get();
        }

        return view('leave.index', compact('leaves'));
    }

    /**
     * Menyimpan pengajuan cuti baru oleh karyawan.
     */

     public function store(Request $request)
{
    $request->validate([
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after_or_equal:start_date',
        'leave_type' => 'required|string',
        'reason' => 'required|string',
        'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    $data = $request->only('start_date', 'end_date', 'leave_type', 'reason');
    $data['user_id'] = Auth::id();
    $data['status'] = 'pending';

    if ($request->hasFile('attachment')) {
        // ⬇️ Simpan ke: storage/app/public/photos/
        $data['attachment'] = $request->file('attachment')->store('photos', 'public');
    }

    Leave::create($data);

    return back()->with('success', 'Pengajuan cuti berhasil dikirim.');
}


    /**
     * Admin memperbarui status pengajuan.
     */
    public function update(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);

        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);

        $leave->status = $request->status;
        $leave->save();

        return back()->with('success', 'Status cuti diperbarui.');
    }

    /**
     * Karyawan bisa menghapus pengajuan mereka jika status masih pending.
     */
    public function destroy($id)
    {
        $leave = Leave::findOrFail($id);



        if ($leave->attachment) {
            Storage::delete($leave->attachment);
        }

        $leave->delete();

        return back()->with('success', 'Pengajuan cuti berhasil dihapus.');
    }




// Method untuk halaman filter export
public function exportFilter()
{
    // Ambil data untuk dropdown filter
    $leaveTypes = ['Sakit', 'Menikah', 'Tahunan', 'Melahirkan', 'Lainnya']; // Sesuaikan dengan jenis cuti yang ada
    $statuses = ['pending', 'approved', 'rejected'];

    // Ambil daftar user untuk filter (jika diperlukan)
    $users = User::orderBy('name')->get(['users_id', 'name']);

    return view('leave.export-filter', compact('leaveTypes', 'statuses', 'users'));
}

// Method untuk download PDF
public function downloadPdf(Request $request)
{
    // Validasi input filter
    $request->validate([
        'user_id' => 'nullable|exists:users,users_id',
        'leave_type' => 'nullable|in:sakit,menikah,tahunan,melahirkan,lainnya',
        'status' => 'nullable|in:pending,approved,rejected',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'leave_start_date' => 'nullable|date',
        'leave_end_date' => 'nullable|date|after_or_equal:leave_start_date',
        'search' => 'nullable|string|max:100',
    ]);

    // Query builder dengan filter
    $query = Leave::with('user'); // Assuming you have a relationship with User model

    // Filter berdasarkan user
    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    // Filter berdasarkan leave type
    if ($request->filled('leave_type')) {
        $query->where('leave_type', $request->leave_type);
    }

    // Filter berdasarkan status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter berdasarkan tanggal pengajuan
    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    // Filter berdasarkan tanggal cuti
    if ($request->filled('leave_start_date')) {
        $query->whereDate('start_date', '>=', $request->leave_start_date);
    }

    if ($request->filled('leave_end_date')) {
        $query->whereDate('end_date', '<=', $request->leave_end_date);
    }

    // Filter berdasarkan pencarian nama karyawan atau alasan
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('user', function($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('reason', 'like', "%{$search}%");
        });
    }

    // Ambil data dengan order by created_at
    $leaves = $query->orderBy('created_at', 'desc')->get();

    // Data untuk template PDF
    $data = [
        'leaves' => $leaves,
        'filters' => $request->all(),
        'generated_at' => Carbon::now()->format('d/m/Y H:i:s'),
        'total_leaves' => $leaves->count(),
        'company_name' => config('app.name', 'Company Name'),
    ];

    // Generate PDF
    $pdf = Pdf::loadView('leave.pdf-template', $data);

    // Set paper size dan orientasi
    $pdf->setPaper('A4', 'landscape');

    // Generate filename
    $filename = 'data-cuti-karyawan-' . Carbon::now()->format('Y-m-d-H-i-s') . '.pdf';

    // Download PDF
    return $pdf->download($filename);
}

// Method untuk preview PDF (opsional)
public function previewPdf(Request $request)
{
    // Logic sama seperti downloadPdf, tapi return stream instead of download
    $request->validate([
        'user_id' => 'nullable|exists:users,users_id',
        'leave_type' => 'nullable|in:sakit,menikah,tahunan,melahirkan,lainnya',
        'status' => 'nullable|in:pending,approved,rejected',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'leave_start_date' => 'nullable|date',
        'leave_end_date' => 'nullable|date|after_or_equal:leave_start_date',
        'search' => 'nullable|string|max:100',
    ]);

    $query = Leave::with('user');

    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    if ($request->filled('leave_type')) {
        $query->where('leave_type', $request->leave_type);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    if ($request->filled('leave_start_date')) {
        $query->whereDate('start_date', '>=', $request->leave_start_date);
    }

    if ($request->filled('leave_end_date')) {
        $query->whereDate('end_date', '<=', $request->leave_end_date);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('user', function($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('reason', 'like', "%{$search}%");
        });
    }

    $leaves = $query->orderBy('created_at', 'desc')->get();

    $data = [
        'leaves' => $leaves,
        'filters' => $request->all(),
        'generated_at' => Carbon::now()->format('d/m/Y H:i:s'),
        'total_leaves' => $leaves->count(),
        'company_name' => config('app.name', 'Company Name'),
    ];

    $pdf = Pdf::loadView('leave.pdf-template', $data);
    $pdf->setPaper('A4', 'landscape');

    // Stream PDF untuk preview
    return $pdf->stream('preview-data-cuti-karyawan.pdf');
}


}
