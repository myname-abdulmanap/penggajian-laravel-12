{{-- resources/views/karyawan/presensi/riwayat.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Presensi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Riwayat Presensi Saya
                    </h4>
                    <div>
                        <a href="{{ route('presensi.scan') }}" class="btn btn-light">
                            <i class="fas fa-qrcode me-2"></i>
                            Scan QR Code
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- User Info --}}
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><i class="fas fa-user me-2"></i>{{ Auth::user()->name }}</h6>
                                            <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <h6><i class="fas fa-calendar me-2"></i>{{ date('F Y') }}</h6>
                                            <p class="text-muted mb-0">Total: {{ $attendances->count() }} hari</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-percentage fa-2x mb-2"></i>
                                    <h4>{{ $attendances->count() > 0 ? number_format(($attendances->where('status', 'hadir')->count() / $attendances->count()) * 100, 1) : 0 }}%</h4>
                                    <p class="mb-0">Tingkat Kehadiran</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Options --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Filter Bulan</label>
                            <select class="form-select" id="monthFilter">
                                <option value="">Semua Bulan</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Filter Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="hadir">Hadir</option>
                                <option value="terlambat">Terlambat</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-outline-secondary d-block w-100" onclick="resetFilters()">
                                <i class="fas fa-undo me-2"></i>Reset Filter
                            </button>
                        </div>
                    </div>

                    {{-- Statistics Cards --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <h4>{{ $attendances->where('status', 'hadir')->count() }}</h4>
                                    <p class="mb-0">Hari Hadir</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h4>{{ $attendances->where('status', 'terlambat')->count() }}</h4>
                                    <p class="mb-0">Hari Terlambat</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    @php
                                        $totalHours = 0;
                                        foreach($attendances->whereNotNull('check_out') as $att) {
                                            $checkIn = \Carbon\Carbon::parse($att->check_in);
                                            $checkOut = \Carbon\Carbon::parse($att->check_out);
                                            $totalHours += $checkOut->diffInHours($checkIn);
                                        }
                                        $avgHours = $attendances->whereNotNull('check_out')->count() > 0 ?
                                                   round($totalHours / $attendances->whereNotNull('check_out')->count(), 1) : 0;
                                    @endphp
                                    <h4>{{ $avgHours }}</h4>
                                    <p class="mb-0">Rata-rata Jam Kerja</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                    <h4>{{ $attendances->count() }}</h4>
                                    <p class="mb-0">Total Hari Kerja</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Data Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="historyTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                    <th>Durasi Kerja</th>
                                    <th>Lokasi</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $index => $attendance)
                                <tr data-month="{{ date('n', strtotime($attendance->date)) }}" data-status="{{ $attendance->status }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ date('d/m/Y', strtotime($attendance->date)) }}</strong>
                                            <br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-sign-in-alt me-1"></i>
                                            {{ $attendance->check_in }}
                                        </span>
                                        <br>
                                        @php
                                            $checkInTime = \Carbon\Carbon::parse($attendance->check_in);
                                            $standardTime = \Carbon\Carbon::parse('08:00');
                                            $isLate = $checkInTime->gt($standardTime);
                                        @endphp
                                        @if($isLate)
                                            <small class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Terlambat {{ $checkInTime->diff($standardTime)->format('%H:%I') }}
                                            </small>
                                        @else
                                            <small class="text-success">
                                                <i class="fas fa-check"></i>
                                                Tepat waktu
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->check_out)
                                            <span class="badge bg-info">
                                                <i class="fas fa-sign-out-alt me-1"></i>
                                                {{ $attendance->check_out }}
                                            </span>
                                            <br>
                                            @php
                                                $checkOutTime = \Carbon\Carbon::parse($attendance->check_out);
                                                $standardEndTime = \Carbon\Carbon::parse('17:00');
                                                $isEarly = $checkOutTime->lt($standardEndTime);
                                            @endphp
                                            @if($isEarly)
                                                <small class="text-warning">
                                                    <i class="fas fa-clock"></i>
                                                    Pulang cepat
                                                </small>
                                            @else
                                                <small class="text-success">
                                                    <i class="fas fa-check"></i>
                                                    Sesuai jadwal
                                                </small>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-minus-circle me-1"></i>
                                                Belum Check Out
                                            </span>
                                            <br>
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->status == 'hadir')
                                            <span class="badge bg-success px-3">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Hadir
                                            </span>
                                        @elseif($attendance->status == 'terlambat')
                                            <span class="badge bg-warning px-3">
                                                <i class="fas fa-clock me-1"></i>
                                                Terlambat
                                            </span>
                                        @else
                                            <span class="badge bg-secondary px-3">
                                                {{ $attendance->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->check_out)
                                            @php
                                                $checkIn = \Carbon\Carbon::parse($attendance->check_in);
                                                $checkOut = \Carbon\Carbon::parse($attendance->check_out);
                                                $duration = $checkOut->diff($checkIn);
                                                $totalMinutes = $checkOut->diffInMinutes($checkIn);
                                                $hours = floor($totalMinutes / 60);
                                                $minutes = $totalMinutes % 60;
                                            @endphp
                                            <div>
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-hourglass-half me-1"></i>
                                                    {{ $hours }}j {{ $minutes }}m
                                                </span>
                                                <br>
                                                @if($hours >= 8)
                                                    <small class="text-success">
                                                        <i class="fas fa-thumbs-up"></i>
                                                        Sesuai target
                                                    </small>
                                                @else
                                                    <small class="text-warning">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        Kurang {{ 8 - $hours }}j
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-minus"></i>
                                                Belum selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="location-info">
                                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                            <small class="text-muted">
                                                {{ Str::limit($attendance->location, 25) }}
                                            </small>
                                            @if(strlen($attendance->location) > 25)
                                                <button class="btn btn-link btn-sm p-0 ms-1"
                                                        onclick="showFullLocation('{{ addslashes($attendance->location) }}')"
                                                        title="Lihat lokasi lengkap">
                                                    <i class="fas fa-expand"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($attendance->check_out)
                                            @if($checkIn->lt(\Carbon\Carbon::parse('08:00')) && $checkOut->gt(\Carbon\Carbon::parse('17:00')))
                                                <span class="badge bg-success">Perfect</span>
                                            @elseif($checkIn->gt(\Carbon\Carbon::parse('08:00')))
                                                <span class="badge bg-warning">Terlambat Masuk</span>
                                            @elseif($checkOut->lt(\Carbon\Carbon::parse('17:00')))
                                                <span class="badge bg-info">Pulang Cepat</span>
                                            @else
                                                <span class="badge bg-success">Normal</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Dalam Kerja</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Belum ada riwayat presensi</h5>
                                        <p class="text-muted">Mulai presensi dengan scan QR Code</p>
                                        <a href="{{ route('presensi.scan') }}" class="btn btn-primary">
                                            <i class="fas fa-qrcode me-2"></i>
                                            Mulai Presensi
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Export Options --}}
                    @if($attendances->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <div class="btn-group">
                                <button class="btn btn-success" onclick="exportPDF()">
                                    <i class="fas fa-file-pdf me-2"></i>
                                    Export PDF
                                </button>
                                <button class="btn btn-primary" onclick="exportExcel()">
                                    <i class="fas fa-file-excel me-2"></i>
                                    Export Excel
                                </button>
                                <button class="btn btn-info" onclick="printReport()">
                                    <i class="fas fa-print me-2"></i>
                                    Print Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Location Modal --}}
<div class="modal fade" id="locationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    Lokasi Presensi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="fullLocation"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
.location-info {
    max-width: 200px;
}

.badge {
    font-size: 0.75rem;
}

.table td {
    vertical-align: middle;
}

@media print {
    .btn, .card-header .btn {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<script>
// Filter functionality
document.getElementById('monthFilter').addEventListener('change', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
    const monthFilter = document.getElementById('monthFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('#historyTable tbody tr[data-month]');

    rows.forEach(row => {
        const monthMatch = !monthFilter || row.dataset.month === monthFilter;
        const statusMatch = !statusFilter || row.dataset.status === statusFilter;

        row.style.display = monthMatch && statusMatch ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('monthFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filterTable();
}

function showFullLocation(location) {
    document.getElementById('fullLocation').textContent = location;
    new bootstrap.Modal(document.getElementById('locationModal')).show();
}

function exportPDF() {
    window.location.href = '/karyawan/presensi/export/pdf';
}

function exportExcel() {
    window.location.href = '/karyawan/presensi/export/excel';
}

function printReport() {
    window.print();
}
</script>
@endsection
