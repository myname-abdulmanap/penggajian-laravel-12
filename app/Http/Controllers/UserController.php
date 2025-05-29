<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::paginate(10); // Menggunakan pagination dan nama variabel plural
        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
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
        return view('user.edit', compact('user')); // Ganti 'update' menjadi 'edit'
    }

    public function update(Request $request, $users_id)
    {
        $user = User::where('users_id', $users_id)->firstOrFail();

        $validatedData = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->users_id, 'users_id')], // Menggunakan primary key users_id
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
}
