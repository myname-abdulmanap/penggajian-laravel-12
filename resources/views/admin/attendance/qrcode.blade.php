{{-- resources/views/admin/attendance/qrcode.blade.php --}}
@extends('layouts.app')

@section('title', 'Generate QR Code Presensi')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-qrcode me-2"></i>
                        QR Code Presensi
                    </h4>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <h5 class="text-muted">Scan QR Code untuk Presensi</h5>
                        <p class="text-secondary">Karyawan dapat memindai QR code ini untuk melakukan presensi</p>
                    </div>

                    {{-- QR Code Display --}}
                    <div class="qr-container mb-4">
                        <div class="bg-white p-4 rounded shadow-sm d-inline-block">
                            <img src="{{ $qrImage }}" alt="QR Code Presensi" class="img-fluid" style="max-width: 300px;">
                        </div>
                    </div>

                    {{-- Token Info --}}
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Token:</strong> {{ $token }}
                    </div>

                    {{-- Action Buttons --}}
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" onclick="refreshQR()">
                            <i class="fas fa-sync-alt me-2"></i>
                            Generate Ulang
                        </button>
                        <button type="button" class="btn btn-primary" onclick="printQR()">
                            <i class="fas fa-print me-2"></i>
                            Print QR Code
                        </button>
                        <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali
                        </a>
                    </div>

                    {{-- Instructions --}}
                    <div class="mt-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                                    <h6>Buka Kamera</h6>
                                    <p class="text-muted small">Karyawan membuka aplikasi kamera atau scanner QR</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-qrcode fa-3x text-success mb-3"></i>
                                    <h6>Scan QR Code</h6>
                                    <p class="text-muted small">Arahkan kamera ke QR code untuk memindai</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-check-circle fa-3x text-warning mb-3"></i>
                                    <h6>Presensi Tercatat</h6>
                                    <p class="text-muted small">Sistem akan mencatat waktu presensi otomatis</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Print Styles --}}
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .qr-container, .qr-container * {
        visibility: visible;
    }
    .qr-container {
        position: absolute;
        left: 50%;
        top: 30%;
        transform: translate(-50%, -50%);
    }
    .btn-group {
        display: none !important;
    }
}

.qr-container {
    transition: transform 0.3s ease;
}

.qr-container:hover {
    transform: scale(1.05);
}
</style>

<script>
function refreshQR() {
    if(confirm('Generate QR Code baru? QR Code lama akan tidak berlaku.')) {
        window.location.reload();
    }
}

function printQR() {
    window.print();
}

// Auto refresh every 30 minutes for security
setTimeout(function() {
    if(confirm('QR Code telah aktif selama 30 menit. Generate QR Code baru untuk keamanan?')) {
        window.location.reload();
    }
}, 30 * 60 * 1000);
</script>
@endsection
