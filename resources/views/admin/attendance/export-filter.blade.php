@extends('layouts.app')

@section('content')
<div class="col-lg-8 mb-10 mx-auto">
    <div class="card shadow">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">Export Laporan Absensi</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('attendance.download-pdf') }}" method="GET" id="exportForm">
                <div class="row">
                    <!-- Filter Tanggal -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Filter Karyawan - Hanya tampil untuk Admin -->
                    @if(auth()->user()->role === 'admin')
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_id">Karyawan</label>
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="">-- Semua Karyawan --</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->users_id }}" {{ request('user_id') == $user->users_id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                    <!-- Hidden input untuk user biasa agar hanya data mereka yang muncul -->
                    <input type="hidden" name="user_id" value="{{ auth()->user()->users_id }}">
                    @endif

                    <!-- Filter Status Absensi -->
                    <div class="{{ auth()->user()->role === 'admin' ? 'col-md-6' : 'col-md-12' }}">
                        <div class="form-group">
                            <label for="status">Status Absensi</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">-- Semua Status --</option>
                                <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                                <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Filter Status Keterlambatan -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="late_status">Status Keterlambatan</label>
                            <select name="late_status" id="late_status" class="form-control">
                                <option value="">-- Semua Status --</option>
                                <option value="Tepat Waktu" {{ request('late_status') == 'Tepat Waktu' ? 'selected' : '' }}>Tepat Waktu</option>
                                <option value="Terlambat" {{ request('late_status') == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                                <option value="Belum Check-in" {{ request('late_status') == 'Belum Check-in' ? 'selected' : '' }}>Belum Check-in</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Info untuk user biasa -->
                @if(auth()->user()->role !== 'admin')
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Anda akan mengexport laporan absensi untuk: <strong>{{ auth()->user()->name }}</strong>
                </div>
                @endif

                <div class="form-group">
                    <div class="d-flex justify-content-between">
                        @if(auth()->user()->role !== 'admin')
                        <a href="{{ route('attendance.riwayat') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        @else
                        <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        @endif

                        <div>
                            <button type="button" class="btn btn-info mr-2" onclick="previewPdf()">
                                <i class="fas fa-eye"></i> Preview PDF
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-download"></i> Download PDF
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <br />
    <div class="card shadow">
        <div class="card-body">
            <h6 class="font-weight-bold text-info">Informasi:</h6>
            <ul class="mb-0">
                @if(auth()->user()->role === 'admin')
                <li>Jika tidak ada filter yang dipilih, semua data karyawan akan diexport</li>
                <li>Filter dapat dikombinasikan untuk hasil yang lebih spesifik</li>
                @else
                <li>Laporan akan menampilkan data absensi Anda saja</li>
                <li>Filter status dan keterlambatan dapat digunakan untuk hasil yang lebih spesifik</li>
                @endif
                <li>File PDF akan didownload dengan format: data-karyawan-YYYY-MM-DD-HH-MM-SS.pdf</li>
                <li>Gunakan tombol "Preview PDF" untuk melihat hasil sebelum download</li>
            </ul>
        </div>
    </div>
</div>


<script>
function previewPdf() {
    const form = document.getElementById('exportForm');
    const originalAction = form.action;
    form.action = '{{ route("attendance.preview-pdf") }}';
    form.target = '_blank';
    form.submit();
    form.action = originalAction;
    form.target = '';
}

// Set default dates if not provided
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    if (!startDate.value) {
        const firstDayOfMonth = new Date();
        firstDayOfMonth.setDate(1);
        startDate.value = firstDayOfMonth.toISOString().split('T')[0];
    }

    if (!endDate.value) {
        const today = new Date();
        endDate.value = today.toISOString().split('T')[0];
    }
});
</script>
@endsection
