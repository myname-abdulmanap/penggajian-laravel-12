@extends('layouts.app')

@section('content')
<div class="col-lg-8 mb-10 mx-auto">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">Export Data Karyawan ke PDF</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('user.download-pdf') }}" method="GET" id="exportForm">
                <div class="row">
                    <!-- Filter Role -->
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label">Filter Role</label>
                        <select class="form-control" id="role" name="role">
                            <option value="">-- Semua Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Filter Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">-- Semua Status --</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <!-- Filter Tanggal Mulai -->
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Tanggal Registrasi Dari</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>

                    <!-- Filter Tanggal Akhir -->
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">Tanggal Registrasi Sampai</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                </div>

                <div class="row">
                    <!-- Filter Pencarian -->
                    <div class="col-md-6 mb-3">
                        <label for="search" class="form-label">Cari Nama/Email</label>
                        <input type="text" class="form-control" id="search" name="search"
                               placeholder="Masukkan nama atau email..." value="{{ request('search') }}">
                    </div>

                    <!-- Filter Job Title -->
                    <div class="col-md-6 mb-3">
                        <label for="job_title" class="form-label">Jabatan</label>
                        <input type="text" class="form-control" id="job_title" name="job_title"
                               placeholder="Masukkan jabatan..." value="{{ request('job_title') }}">
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="row">
                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('user.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
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
                </div>
            </form>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card shadow">
        <div class="card-body">
            <h6 class="font-weight-bold text-info">Informasi:</h6>
            <ul class="mb-0">
                <li>Jika tidak ada filter yang dipilih, semua data karyawan akan diexport</li>
                <li>Filter dapat dikombinasikan untuk hasil yang lebih spesifik</li>
                <li>File PDF akan didownload dengan format: data-karyawan-YYYY-MM-DD-HH-MM-SS.pdf</li>
                <li>Gunakan tombol "Preview PDF" untuk melihat hasil sebelum download</li>
            </ul>
        </div>
    </div>
</div>

<script>
function previewPdf() {
    // Ubah action form untuk preview
    const form = document.getElementById('exportForm');
    const originalAction = form.action;

    // Ganti action ke route preview
    form.action = '{{ route("user.preview-pdf") }}';
    form.target = '_blank'; // Buka di tab baru

    // Submit form
    form.submit();

    // Kembalikan action dan target ke semula
    setTimeout(() => {
        form.action = originalAction;
        form.target = '';
    }, 100);
}

// Reset form
function resetFilter() {
    document.getElementById('exportForm').reset();
}

// Validasi tanggal
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = this.value;
    const endDateInput = document.getElementById('end_date');

    if (startDate) {
        endDateInput.min = startDate;
    }
});

document.getElementById('end_date').addEventListener('change', function() {
    const endDate = this.value;
    const startDateInput = document.getElementById('start_date');

    if (endDate) {
        startDateInput.max = endDate;
    }
});
</script>
@endsection
