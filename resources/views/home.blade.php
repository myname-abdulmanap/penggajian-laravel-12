@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                <p class="mb-0 text-muted">Selamat datang, {{ Auth::user()->name }}</p>
            </div>
            <div class="d-none d-sm-inline-block">
                <span class="badge badge-primary px-3 py-2">{{ ucfirst(Auth::user()->role) }}</span>
            </div>
        </div>

        @php
            $role = auth()->user()->role;
        @endphp

        <!-- Quick Stats - Tampil di atas untuk semua role -->
        <div class="row mb-4">
            @if ($role === 'admin')
                <!-- Admin Statistics -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-primary text-white shadow-sm h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Total Karyawan</div>
                                    <div class="h5 mb-0 font-weight-bold">
                                        {{ \App\Models\User::where('role', 'karyawan')->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-success text-white shadow-sm h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Hadir Hari Ini</div>
                                    <div class="h5 mb-0 font-weight-bold">
                                        {{ \App\Models\Attendance::whereDate('date', today())->where('status', 'hadir')->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-warning text-white shadow-sm h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Belum Absen</div>
                                    <div class="h5 mb-0 font-weight-bold">
                                        {{ \App\Models\User::where('role', 'karyawan')->count() -
                                           \App\Models\Attendance::whereDate('date', today())->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-info text-white shadow-sm h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Total Gaji Bulan Ini</div>
                                    <div class="h5 mb-0 font-weight-bold">
                                        Rp {{ number_format(\App\Models\Salary::whereMonth('created_at', now()->month)->sum('net_salary'), 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Employee Statistics -->
                @php
                    $userId = Auth::id();
                    $todayAttendance = \App\Models\Attendance::where('users_id', $userId)->whereDate('date', today())->first();
                    $monthlyAttendance = \App\Models\Attendance::where('users_id', $userId)->whereMonth('date', now()->month)->count();
                    $latestSalary = \App\Models\Salary::where('users_id', $userId)->latest()->first();
                @endphp

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-{{ $todayAttendance ? 'success' : 'warning' }} text-white shadow-sm h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Status Hari Ini</div>
                                    <div class="h5 mb-0 font-weight-bold">
                                        {{ $todayAttendance ? ucfirst($todayAttendance->status) : 'Belum Absen' }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-{{ $todayAttendance ? 'check-circle' : 'clock' }} fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-primary text-white shadow-sm h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Kehadiran Bulan Ini</div>
                                    <div class="h5 mb-0 font-weight-bold">{{ $monthlyAttendance }} Hari</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-check fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-success text-white shadow-sm h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Gaji Terakhir</div>
                                    <div class="h5 mb-0 font-weight-bold">
                                        Rp {{ $latestSalary ? number_format($latestSalary->net_salary, 0, ',', '.') : '0' }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-info text-white shadow-sm h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Jam Masuk Hari Ini</div>
                                    <div class="h5 mb-0 font-weight-bold">
                                        {{ $todayAttendance && $todayAttendance->check_in ?
                                           Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') : '--:--' }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- User Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-lg">
                                    @if(Auth::user()->photo)
                                        <img src="{{ Storage::url(Auth::user()->photo) }}"
                                             class="rounded-circle"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <i class="fas fa-user-circle fa-3x text-primary"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                                <p class="text-muted mb-0">{{ ucfirst(Auth::user()->role) }}</p>
                                <small class="text-muted">
                                    {{ Auth::user()->email }} |
                                    Login terakhir: {{ Auth::user()->updated_at->format('d M Y, H:i') }}
                                </small>
                            </div>
                            <div class="col-auto">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <small class="text-success">Online</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Cards -->
        <div class="row">
            <!-- Presensi Menu -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 menu-card">
                    <div class="card-body text-center">
                        <div class="menu-icon mb-3">
                            <i class="fas fa-calendar-check fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title mb-2">Presensi</h5>
                        <p class="card-text text-muted mb-3">
                            {{ $role === 'admin' ? 'Kelola presensi karyawan' : 'Absensi harian' }}
                        </p>
                        @if ($role === 'admin')
                            <a href="/admin/attendance" class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-arrow-right me-1"></i> Kelola
                            </a>
                        @else
                            <a href="/karyawan/attendance" class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-arrow-right me-1"></i> Absen
                            </a>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            {{ $role === 'admin' ? 'Pantau kehadiran' : 'Jangan lupa absen' }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Penggajian Menu -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 menu-card">
                    <div class="card-body text-center">
                        <div class="menu-icon mb-3">
                            <i class="fas fa-money-bill-wave fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title mb-2">Penggajian</h5>
                        <p class="card-text text-muted mb-3">
                            {{ $role === 'admin' ? 'Kelola gaji karyawan' : 'Lihat slip gaji' }}
                        </p>
                        <a href="/salaries" class="btn btn-success btn-sm px-4">
                            <i class="fas fa-arrow-right me-1"></i>
                            {{ $role === 'admin' ? 'Kelola' : 'Lihat' }}
                        </a>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i> Periode: {{ now()->format('M Y') }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Data Master Menu (Admin Only) -->
            @if ($role === 'admin')
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 menu-card">
                    <div class="card-body text-center">
                        <div class="menu-icon mb-3">
                            <i class="fas fa-database fa-3x text-info"></i>
                        </div>
                        <h5 class="card-title mb-2">Data Master</h5>
                        <p class="card-text text-muted mb-3">Kelola data karyawan & tunjangan</p>
                        <a href="/user" class="btn btn-info btn-sm px-4">
                            <i class="fas fa-arrow-right me-1"></i> Kelola
                        </a>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small class="text-muted">
                            <i class="fas fa-users me-1"></i> {{ \App\Models\User::count() }} pengguna
                        </small>
                    </div>
                </div>
            </div>
            @endif

             <!-- Laporan Menu -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 menu-card">
                    <div class="card-body text-center">
                        <div class="menu-icon mb-3">
                            <i class="fas fa-chart-area fa-3x text-warning"></i>
                        </div>
                        <h5 class="card-title mb-2">Laporan Absensi</h5>
                        <p class="card-text text-muted mb-3">
                            {{ $role === 'admin' ? 'Laporan sistem' : 'Riwayat pribadi' }}
                        </p>
                        <a href="/attendance/export-filter" class="btn btn-warning btn-sm px-4">
                            <i class="fas fa-arrow-right me-1"></i> Lihat
                        </a>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small class="text-muted">
                            <i class="fas fa-file-alt me-1"></i> Data terkini
                        </small>
                    </div>
                </div>
            </div>

            <!-- Profile Menu -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 menu-card">
                    <div class="card-body text-center">
                        <div class="menu-icon mb-3">
                            <i class="fas fa-user-cog fa-3x text-secondary"></i>
                        </div>
                        <h5 class="card-title mb-2">Profil</h5>
                        <p class="card-text text-muted mb-3">Kelola profil dan pengaturan</p>
                        <a href="/profile" class="btn btn-secondary btn-sm px-4">
                            <i class="fas fa-arrow-right me-1"></i> Edit
                        </a>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small class="text-muted">
                            <i class="fas fa-user-edit me-1"></i> Update profil
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities (Admin Only) -->
        @if ($role === 'admin')
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                        <a href="/admin/attendance" class="btn btn-primary btn-sm">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        @php
                            $recentAttendances = \App\Models\Attendance::with('user')
                                ->whereDate('date', today())
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp

                        @if($recentAttendances->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Karyawan</th>
                                            <th>Status</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentAttendances as $attendance)
                                        <tr>
                                            <td>{{ $attendance->user->name }}</td>
                                            <td>
                                                <span class="badge badge-{{ $attendance->status == 'hadir' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($attendance->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $attendance->check_in ? Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}</td>
                                            <td>{{ $attendance->check_out ? Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center">Belum ada aktivitas hari ini</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <style>
        .menu-card {
            transition: all 0.3s ease;
            border-radius: 15px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .menu-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        }

        .menu-icon {
            transition: all 0.3s ease;
        }

        .menu-card:hover .menu-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .avatar-lg {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border-radius: 15px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .btn {
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .badge {
            font-size: 0.75em;
            padding: 0.5em 0.75em;
        }

        .table td, .table th {
            vertical-align: middle;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
    </style>
@endsection
