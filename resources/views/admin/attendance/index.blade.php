{{-- resources/views/admin/attendance/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Presensi Hari Ini')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Data Presensi - {{ date('d F Y') }}
                    </h4>
                    <div>
                        <a href="{{ route('admin.attendance.qrcode') }}" class="btn btn-light">
                            <i class="fas fa-qrcode me-2"></i>
                            Generate QR Code
                        </a>
                        <button class="btn btn-success" onclick="exportExcel()">
                            <i class="fas fa-file-excel me-2"></i>
                            Export Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Summary Cards --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <h4>{{ $attendances->where('status', 'hadir')->count() }}</h4>
                                    <p class="mb-0">Hadir</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h4>{{ $attendances->where('status', 'terlambat')->count() }}</h4>
                                    <p class="mb-0">Terlambat</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-clock fa-2x mb-2"></i>
                                    <h4>{{ $attendances->whereNotNull('check_out')->count() }}</h4>
                                    <p class="mb-0">Sudah Pulang</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <h4>{{ $attendances->count() }}</h4>
                                    <p class="mb-0">Total Presensi</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Cari nama karyawan...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="hadir">Hadir</option>
                                <option value="terlambat">Terlambat</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                                <i class="fas fa-undo me-2"></i>Reset Filter
                            </button>
                        </div>
                    </div>

                    {{-- Data Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="attendanceTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Karyawan</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                    <th>Lokasi</th>
                                    <th>Durasi Kerja</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $index => $attendance)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">

                                             

                                            <div>
                                                <strong>{{ $attendance->user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $attendance->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-sign-in-alt me-1"></i>
                                            {{ $attendance->check_in }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($attendance->check_out)
                                            <span class="badge bg-info">
                                                <i class="fas fa-sign-out-alt me-1"></i>
                                                {{ $attendance->check_out }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning">Belum Check Out</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->status == 'hadir')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Hadir
                                            </span>
                                        @elseif($attendance->status == 'terlambat')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Terlambat
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">{{ $attendance->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ Str::limit($attendance->location, 30) }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($attendance->check_out)
                                            @php
                                                $checkIn = \Carbon\Carbon::parse($attendance->check_in);
                                                $checkOut = \Carbon\Carbon::parse($attendance->check_out);
                                                $duration = $checkOut->diff($checkIn);
                                            @endphp
                                            <span class="badge bg-success">
                                                {{ $duration->format('%H:%I:%S') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="viewDetail({{ $attendance->attendance_id }})">
                                                <i class="fas fa-eye"></i>
                               </button>
                                            <button class="btn btn-outline-info" onclick="editAttendance({{ $attendance->attendance_id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Belum ada data presensi hari ini</h5>
                                        <p class="text-muted">Silakan generate QR Code untuk memulai presensi</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 35px;
    height: 35px;
    font-size: 14px;
    font-weight: 600;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
}
</style>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    filterTable();
});

document.getElementById('statusFilter').addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const table = document.getElementById('attendanceTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const nameCell = rows[i].getElementsByTagName('td')[1];
        const statusCell = rows[i].getElementsByTagName('td')[4];

        if (nameCell && statusCell) {
            const nameText = nameCell.textContent.toLowerCase();
            const statusText = statusCell.textContent.toLowerCase();

            const matchesSearch = nameText.includes(searchTerm);
            const matchesStatus = statusFilter === '' || statusText.includes(statusFilter);

            rows[i].style.display = matchesSearch && matchesStatus ? '' : 'none';
        }
    }
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    filterTable();
}

function viewDetail(id) {
    // Implement view detail functionality
    alert('View detail for ID: ' + id);
}

function editAttendance(id) {
    // Implement edit functionality
    alert('Edit attendance for ID: ' + id);
}

function exportExcel() {
    // Implement Excel export
    window.location.href = '/admin/attendance/export';
}

// Auto refresh every 5 minutes
setInterval(function() {
    window.location.reload();
}, 5 * 60 * 1000);
</script>
@endsection
