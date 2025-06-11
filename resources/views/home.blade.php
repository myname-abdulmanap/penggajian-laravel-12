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

        <!-- User Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-lg">
                                    <i class="fas fa-user-circle fa-3x text-primary"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                                <p class="text-muted mb-0">{{ ucfirst(Auth::user()->role) }}</p>
                                <small class="text-muted">Login terakhir: {{ now()->format('d M Y, H:i') }}</small>
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
                        <p class="card-text text-muted mb-3">Kelola absensi dan kehadiran</p>
                        @if ($role === 'admin')
                            <a href="/admin/attendance" class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-arrow-right me-1"></i> Akses
                            </a>
                        @endif
                        @if ($role === 'karyawan')
                            <a href="/karyawan/attendance" class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-arrow-right me-1"></i> Akses
                            </a>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i> Update terakhir: Hari ini
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
                        <p class="card-text text-muted mb-3">Kelola gaji dan tunjangan</p>
                        <a href="/salaries" class="btn btn-success btn-sm px-4">
                            <i class="fas fa-arrow-right me-1"></i> Akses
                        </a>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small class="text-muted">
                            <i class="fas fa-calculator me-1"></i> Periode: {{ now()->format('M Y') }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Cuti Menu -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 menu-card">
                    <div class="card-body text-center">
                        <div class="menu-icon mb-3">
                            <i class="fas fa-umbrella-beach fa-3x text-info"></i>
                        </div>
                        <h5 class="card-title mb-2">Cuti</h5>
                        <p class="card-text text-muted mb-3">Ajukan dan kelola cuti</p>
                        <a href="/leave" class="btn btn-info btn-sm px-4">
                            <i class="fas fa-arrow-right me-1"></i> Akses
                        </a>
                    </div>
                    {{-- <div class="card-footer bg-transparent border-0">
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i> Sisa cuti: 12 hari
                        </small>
                    </div> --}}
                </div>
            </div>

            {{-- <!-- Laporan Menu (Admin Only) -->
            @if ($role === 'admin')
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 menu-card">
                    <div class="card-body text-center">
                        <div class="menu-icon mb-3">
                            <i class="fas fa-chart-bar fa-3x text-warning"></i>
                        </div>
                        <h5 class="card-title mb-2">Laporan</h5>
                        <p class="card-text text-muted mb-3">Lihat laporan dan analisis</p>
                        <a href="/admin/reports" class="btn btn-warning btn-sm px-4">
                            <i class="fas fa-arrow-right me-1"></i> Akses
                        </a>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small class="text-muted">
                            <i class="fas fa-file-alt me-1"></i> Data terbaru
                        </small>
                    </div>
                </div>
            </div>
            @endif --}}

            <!-- Pengaturan Menu -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 menu-card">
                    <div class="card-body text-center">
                        <div class="menu-icon mb-3">
                            <i class="fas fa-cog fa-3x text-secondary"></i>
                        </div>
                        <h5 class="card-title mb-2">Pengaturan</h5>
                        <p class="card-text text-muted mb-3">Kelola profil dan preferensi</p>
                        <a href="/profile" class="btn btn-secondary btn-sm px-4">
                            <i class="fas fa-arrow-right me-1"></i> Akses
                        </a>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small class="text-muted">
                            <i class="fas fa-user-edit me-1"></i> Profil lengkap
                        </small>
                    </div>
                </div>
            </div>

            <!-- Notifikasi Menu -->
            {{-- <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 menu-card">
                    <div class="card-body text-center">
                        <div class="menu-icon mb-3 position-relative">
                            <i class="fas fa-bell fa-3x text-danger"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </div>
                        <h5 class="card-title mb-2">Notifikasi</h5>
                        <p class="card-text text-muted mb-3">Lihat pemberitahuan terbaru</p>
                        <a href="/notifications" class="btn btn-danger btn-sm px-4">
                            <i class="fas fa-arrow-right me-1"></i> Akses
                        </a>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small class="text-muted">
                            <i class="fas fa-exclamation-circle me-1"></i> 3 notifikasi baru
                        </small>
                    </div>
                </div>
            </div>

            <!-- Bantuan Menu -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 menu-card">
                    <div class="card-body text-center">
                        <div class="menu-icon mb-3">
                            <i class="fas fa-question-circle fa-3x text-dark"></i>
                        </div>
                        <h5 class="card-title mb-2">Bantuan</h5>
                        <p class="card-text text-muted mb-3">Panduan dan dukungan</p>
                        <a href="/help" class="btn btn-dark btn-sm px-4">
                            <i class="fas fa-arrow-right me-1"></i> Akses
                        </a>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <small class="text-muted">
                            <i class="fas fa-life-ring me-1"></i> Dukungan 24/7
                        </small>
                    </div>
                </div>
            </div> --}}
        </div>

        <!-- Quick Stats (Admin Only) -->
        @if ($role === 'admin')
        <div class="row mt-4">
            <div class="col-12 mb-3">
                <h4 class="text-gray-800">Statistik Cepat</h4>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Total Karyawan</div>
                                <div class="h5 mb-0 font-weight-bold">125</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-success text-white shadow">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Hadir Hari Ini</div>
                                <div class="h5 mb-0 font-weight-bold">98</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-warning text-white shadow">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Cuti Pending</div>
                                <div class="h5 mb-0 font-weight-bold">7</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-danger text-white shadow">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Terlambat</div>
                                <div class="h5 mb-0 font-weight-bold">5</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <style>
        .menu-card {
            transition: all 0.3s ease;
            border-radius: 10px;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }

        .menu-icon {
            transition: all 0.3s ease;
        }

        .menu-card:hover .menu-icon {
            transform: scale(1.1);
        }

        .avatar-lg {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border-radius: 10px;
        }

        .btn {
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
@endsection
