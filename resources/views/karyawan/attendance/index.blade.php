@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <div class="col-12 col-lg-10 mx-auto">
        <!-- Responsive Heading -->
        <div class="text-center mb-4 mb-md-5">
            <h2 class="h3 h-md-2 mb-2 font-weight-bold text-primary">
                <i class="fas fa-clock mr-2 d-none d-sm-inline"></i>
                Menu Absensi
            </h2>
            <p class="text-muted mb-0 d-none d-md-block">Kelola sistem presensi karyawan</p>
        </div>

        <!-- Responsive Cards Grid -->
        <div class="row justify-content-center">
            <!-- Card Riwayat Absensi -->
            <div class="col-12 col-sm-6 col-lg-5 mb-3 mb-md-4">
                <a href="/karyawan/attendance/riwayat" class="text-decoration-none">
                    <div class="card shadow border-left-primary h-100 py-2 py-md-3 card-hover">
                        <div class="card-body p-3 p-md-4">
                            <div class="text-center">
                                <!-- Responsive Icon -->
                                <div class="icon-container mb-2 mb-md-3">
                                    <i class="fas fa-history fa-2x fa-md-3x text-primary"></i>
                                </div>

                                <!-- Responsive Title -->
                                <h5 class="h6 h-md-5 font-weight-bold text-dark mb-2">
                                    Riwayat Absensi
                                </h5>

                                <!-- Responsive Description -->
                                <p class="text-muted mb-0 small">
                                    Lihat data kehadiran karyawan
                                </p>

                                <!-- Mobile Action Indicator -->
                                <div class="mt-2 d-md-none">
                                    <i class="fas fa-chevron-right text-primary small"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Card Scan QR -->
            <div class="col-12 col-sm-6 col-lg-5 mb-3 mb-md-4">
                <a href="/karyawan/attendance/scan" class="text-decoration-none">
                    <div class="card shadow border-left-success h-100 py-2 py-md-3 card-hover">
                        <div class="card-body p-3 p-md-4">
                            <div class="text-center">
                                <!-- Responsive Icon -->
                                <div class="icon-container mb-2 mb-md-3">
                                    <i class="fas fa-qrcode fa-2x fa-md-3x text-success"></i>
                                </div>

                                <!-- Responsive Title -->
                                <h5 class="h6 h-md-5 font-weight-bold text-dark mb-2">
                                    Scan QR Absen
                                </h5>

                                <!-- Responsive Description -->
                                <p class="text-muted mb-0 small">
                                    Scan kode QR untuk presensi harian
                                </p>

                                <!-- Mobile Action Indicator -->
                                <div class="mt-2 d-md-none">
                                    <i class="fas fa-chevron-right text-success small"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Mobile-specific Navigation Hint -->
        <div class="text-center mt-4 d-md-none">
            <small class="text-muted">
                <i class="fas fa-touch mr-1"></i>
                Ketuk kartu untuk mengakses fitur
            </small>
        </div>
    </div>
</div>

<style>
/* Responsive Menu Styles */
@media (max-width: 576px) {
    /* Mobile-first adjustments */
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }

    /* Compact heading for mobile */
    .text-center h2 {
        font-size: 1.5rem !important;
        margin-bottom: 1.5rem !important;
    }

    /* Mobile card adjustments */
    .card {
        border-radius: 0.75rem;
        transition: all 0.2s ease;
    }

    .card-body {
        padding: 1.25rem !important;
    }

    /* Icon sizing for mobile */
    .icon-container i {
        font-size: 2.5rem !important;
    }

    /* Text sizing adjustments */
    h5 {
        font-size: 1.1rem !important;
        line-height: 1.3;
    }

    p.text-muted {
        font-size: 0.875rem !important;
        line-height: 1.4;
    }
}

@media (min-width: 577px) and (max-width: 768px) {
    /* Tablet adjustments */
    .card-body {
        padding: 1.5rem !important;
    }

    .icon-container i {
        font-size: 2.75rem !important;
    }

    h5 {
        font-size: 1.15rem !important;
    }
}

@media (min-width: 769px) {
    /* Desktop enhancements */
    .card-body {
        padding: 2rem !important;
    }

    .icon-container i {
        font-size: 3rem !important;
    }

    /* Hover effects for desktop */
    .card-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .card-hover:hover .icon-container i {
        transform: scale(1.1);
        transition: transform 0.2s ease;
    }
}

/* Universal improvements */
.card {
    border: none;
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1) !important;
}

.icon-container {
    transition: all 0.3s ease;
}

/* Touch-friendly targets for mobile */
@media (max-width: 768px) {
    .card {
        min-height: 120px;
    }

    a {
        display: block;
        -webkit-tap-highlight-color: transparent;
    }
}

/* Responsive spacing */
.row {
    margin-left: -0.75rem;
    margin-right: -0.75rem;
}

.col-12, .col-sm-6, .col-lg-5 {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
}

/* Loading animation for touch feedback */
@media (max-width: 768px) {
    .card:active {
        transform: scale(0.98);
        transition: transform 0.1s ease;
    }
}
</style>
@endsection
