@extends('layouts.app')

@section('content')
<div class="col-lg-10 mx-auto">
    <h4 class="mb-4 font-weight-bold text-center">Menu Absensi</h4>

    <div class="row">
        <!-- Card Riwayat Absensi -->
        <div class="col-md-6 mb-4">
            <a href="/karyawan/attendance/riwayat" class="text-decoration-none">
                <div class="card shadow border-left-primary h-100 py-2">
                    <div class="card-body">
                        <div class="text-center">
                            <i class="fas fa-history fa-2x text-primary mb-2"></i>
                            <h5 class="font-weight-bold text-dark">Riwayat Absensi</h5>
                            <p class="text-muted">Lihat data kehadiran Anda.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card Scan QR -->
        <div class="col-md-6 mb-4">
            <a href="/karyawan/attendance/scan" class="text-decoration-none">
                <div class="card shadow border-left-success h-100 py-2">
                    <div class="card-body">
                        <div class="text-center">
                            <i class="fas fa-qrcode fa-2x text-success mb-2"></i>
                            <h5 class="font-weight-bold text-dark">Scan QR Absen</h5>
                            <p class="text-muted">Lakukan presensi harian.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
