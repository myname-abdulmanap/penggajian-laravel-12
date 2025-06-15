<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::paginate(10);
        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'users_id' => 'required|numeric|unique:users,users_id',
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'role'      => ['nullable', 'string', 'in:admin,manager,karyawan'], // Validasi role
            'status'    => ['nullable', 'string', 'in:aktif,nonaktif'], // Validasi status
            'phone'     => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'], // Validasi format nomor
            'address'   => ['nullable', 'string', 'max:500'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'photo'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Spesifik format gambar
        ]);

        $userData = [
            'users_id'  => $validatedData['users_id'],
            'name'      => $validatedData['name'],
            'email'     => $validatedData['email'],
            'password'  => Hash::make($validatedData['password']),
            'role'      => $validatedData['role'] ?? 'karyawan',
            'status'    => $validatedData['status'] ?? 'aktif',
            'phone'     => $validatedData['phone'] ?? null,
            'address'   => $validatedData['address'] ?? null,
            'job_title' => $validatedData['job_title'] ?? null,
        ];

        // Handle upload photo dengan nama file unik
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('photos', $fileName, 'public');
            $userData['photo'] = $path;
        }

        try {
            User::create($userData);
            return redirect()->route('user.index')->with('success', 'User berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data user.');
        }
    }

    public function show($users_id)
    {
        $user = User::where('users_id', $users_id)->firstOrFail();
        return view('user.show', compact('user'));
    }

    public function edit($users_id)
    {
        $user = User::where('users_id', $users_id)->firstOrFail();
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, $users_id)
    {
        $user = User::where('users_id', $users_id)->firstOrFail();

        $validatedData = $request->validate([

            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->users_id, 'users_id')],
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'      => ['nullable', 'string', 'in:admin,manager,karyawan'],
            'status'    => ['nullable', 'string', 'in:aktif,nonaktif'],
            'phone'     => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'address'   => ['nullable', 'string', 'max:500'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'photo'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Update data user
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->role = $validatedData['role'] ?? $user->role;
        $user->status = $validatedData['status'] ?? $user->status;
        $user->phone = $validatedData['phone'];
        $user->address = $validatedData['address'];
        $user->job_title = $validatedData['job_title'];

        // Update password jika diisi
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        // Handle update photo
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // Upload foto baru
            $file = $request->file('photo');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('photos', $fileName, 'public');
            $user->photo = $path;
        }

        try {
            $user->save();
            return redirect()->route('user.index')->with('success', 'Data user berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data user.');
        }
    }

    public function destroy($users_id)
    {
        $user = User::where('users_id', $users_id)->firstOrFail();

        // Cegah menghapus user yang sedang login
        if ($user->users_id === Auth::id()) {
            return redirect()->route('user.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        try {
            // Hapus foto jika ada
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $user->delete();
            return redirect()->route('user.index')->with('success', 'Data user berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('user.index')->with('error', 'Terjadi kesalahan saat menghapus data user.');
        }
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }


    public function updateProfile(Request $request)
{
    $user = Auth::user();

    $validatedData = $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->users_id, 'users_id')],
        'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        'phone'    => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
        'address'  => ['nullable', 'string', 'max:500'],
        'photo'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
    ]);

    // Update data user dengan cara yang sama seperti method update
    $user->name = $validatedData['name'];
    $user->email = $validatedData['email'];
    $user->phone = $validatedData['phone'] ?? null;
    $user->address = $validatedData['address'] ?? null;

    // Update password jika diisi
    if (!empty($validatedData['password'])) {
        $user->password = Hash::make($validatedData['password']);
    }

    // Handle update photo
    if ($request->hasFile('photo')) {
        // Hapus foto lama jika ada
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        // Upload foto baru dengan nama file unik
        $file = $request->file('photo');
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('photos', $fileName, 'public');
        $user->photo = $path;
    }

    try {
        $user->save();
        return redirect()->route('user.profile')->with('success', 'Profil berhasil diperbarui.');
    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui profil.');
    }
}

    /**
     * Method helper untuk mendapatkan URL foto user
     */
    public function getPhotoUrl($user)
    {
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            return Storage::url($user->photo);
        }
        return asset('images/default-avatar.png'); // Default avatar jika tidak ada foto

    }

    // Method untuk menampilkan halaman filter
public function exportFilter()
{
    $roles = ['admin', 'manager', 'karyawan'];
    $statuses = ['aktif', 'nonaktif'];

    return view('user.export-filter', compact('roles', 'statuses'));
}

// Method untuk download PDF
public function downloadPdf(Request $request)
{
    // Validasi input filter
    $request->validate([
        'role' => 'nullable|in:admin,manager,karyawan',
        'status' => 'nullable|in:aktif,nonaktif',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'search' => 'nullable|string|max:100',
        'job_title' => 'nullable|string|max:100',
    ]);

    // Query builder dengan filter
    $query = User::query();

    // Filter berdasarkan role
    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    // Filter berdasarkan status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter berdasarkan tanggal registrasi
    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    // Filter berdasarkan pencarian nama atau email
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Filter berdasarkan job title
    if ($request->filled('job_title')) {
        $query->where('job_title', 'like', "%{$request->job_title}%");
    }

    // Ambil data dengan order by name
    $users = $query->orderBy('name', 'asc')->get();

    // Data untuk template PDF
    $data = [
        'users' => $users,
        'filters' => $request->all(),
        'generated_at' => Carbon::now()->format('d/m/Y H:i:s'),
        'total_users' => $users->count(),
        'company_name' => config('app.name', 'Company Name'), // Bisa disesuaikan
    ];

    // Generate PDF
    $pdf = Pdf::loadView('user.pdf-template', $data);

    // Set paper size dan orientasi
    $pdf->setPaper('A4', 'landscape'); // atau 'portrait'

    // Generate filename
    $filename = 'data-karyawan-' . Carbon::now()->format('Y-m-d-H-i-s') . '.pdf';

    // Download PDF
    return $pdf->download($filename);
}

// Method untuk preview PDF (opsional)
public function previewPdf(Request $request)
{
    // Logic sama seperti downloadPdf, tapi return stream instead of download
    $request->validate([
        'role' => 'nullable|in:admin,manager,karyawan',
        'status' => 'nullable|in:aktif,nonaktif',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'search' => 'nullable|string|max:100',
        'job_title' => 'nullable|string|max:100',
    ]);

    $query = User::query();

    if ($request->filled('role')) {
        $query->where('role', $request->role);
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

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    if ($request->filled('job_title')) {
        $query->where('job_title', 'like', "%{$request->job_title}%");
    }

    $users = $query->orderBy('name', 'asc')->get();

    $data = [
        'users' => $users,
        'filters' => $request->all(),
        'generated_at' => Carbon::now()->format('d/m/Y H:i:s'),
        'total_users' => $users->count(),
        'company_name' => config('app.company', 'Company Name'),
    ];

    $pdf = Pdf::loadView('user.pdf-template', $data);
    $pdf->setPaper('A4', 'landscape');

    // Stream PDF untuk preview
    return $pdf->stream('preview-data-karyawan.pdf');
}
}



//thanks
