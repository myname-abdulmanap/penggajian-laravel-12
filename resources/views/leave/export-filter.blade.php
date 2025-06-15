@extends('layouts.app')

@section('content')
<div class="col-lg-8 mb-10 mx-auto">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">Export Data Cuti Karyawan ke PDF</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('leaves.download-pdf') }}" method="GET" id="exportForm">
                <div class="row">
                    <!-- Filter Karyawan -->
                    <div class="col-md-6 mb-3">
                        <label for="user_id" class="form-label">Filter Karyawan</label>
                        <select class="form-control" id="user_id" name="user_id">
                            @if(Auth::user()->role == 'admin')
                                <option value="">-- Semua Karyawan --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->users_id }}" {{ request('user_id') == $user->users_id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="{{ Auth::user()->users_id }}" selected>
                                    {{ Auth::user()->name }}
                                </option>
                            @endif
                        </select>
                    </div>


                    <!-- Filter Jenis Cuti -->
                    <div class="col-md-6 mb-3">
                        <label for="leave_type" class="form-label">Filter Jenis Cuti</label>
                        <select class="form-control" id="leave_type" name="leave_type">
                            <option value="">-- Semua Jenis Cuti --</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type }}" {{ request('leave_type') == $type ? 'selected' : '' }}>
                                    @switch($type)
                                        @case('Sakit')
                                            Cuti Sakit
                                            @break
                                        @case('Tahunan')
                                            Cuti Tahunan
                                            @break
                                        @case('Melahirkan')
                                            Cuti Melahirkan
                                            @break
                                        @case('Menikah')
                                            Cuti Menikah
                                            @break
                                        @default
                                            Cuti Lainnya
                                    @endswitch
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <!-- Filter Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Filter Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">-- Semua Status --</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    @switch($status)
                                        @case('pending')
                                            Menunggu Persetujuan
                                            @break
                                        @case('approved')
                                            Disetujui
                                            @break
                                        @case('rejected')
                                            Ditolak
                                            @break
                                        @default
                                            {{ ucfirst($status) }}
                                    @endswitch
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Pencarian -->
                    <div class="col-md-6 mb-3">
                        <label for="search" class="form-label">Cari Nama/Email/Alasan</label>
                        <input type="text" class="form-control" id="search" name="search"
                               placeholder="Masukkan nama, email, atau alasan cuti..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <hr>
                        <h6 class="font-weight-bold text-info">Filter Tanggal Pengajuan Cuti</h6>
                    </div>

                    <!-- Filter Tanggal Pengajuan Mulai -->
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Tanggal Pengajuan Dari</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>

                    <!-- Filter Tanggal Pengajuan Akhir -->
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">Tanggal Pengajuan Sampai</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <hr>
                        <h6 class="font-weight-bold text-info">Filter Periode Cuti</h6>
                    </div>

                    <!-- Filter Tanggal Cuti Mulai -->
                    <div class="col-md-6 mb-3">
                        <label for="leave_start_date" class="form-label">Periode Cuti Dari</label>
                        <input type="date" class="form-control" id="leave_start_date" name="leave_start_date" value="{{ request('leave_start_date') }}">
                    </div>

                    <!-- Filter Tanggal Cuti Akhir -->
                    <div class="col-md-6 mb-3">
                        <label for="leave_end_date" class="form-label">Periode Cuti Sampai</label>
                        <input type="date" class="form-control" id="leave_end_date" name="leave_end_date" value="{{ request('leave_end_date') }}">
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="row">
                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('leaves.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="button" class="btn btn-warning ml-2" onclick="resetFilter()">
                                    <i class="fas fa-undo"></i> Reset Filter
                                </button>
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
                <li>Jika tidak ada filter yang dipilih, semua data cuti akan diexport</li>
                <li>Filter dapat dikombinasikan untuk hasil yang lebih spesifik</li>
                <li>File PDF akan didownload dengan format: data-cuti-karyawan-YYYY-MM-DD-HH-MM-SS.pdf</li>
                <li>Gunakan tombol "Preview PDF" untuk melihat hasil sebelum download</li>
                <li>Filter "Tanggal Pengajuan" berdasarkan kapan cuti diajukan</li>
                <li>Filter "Periode Cuti" berdasarkan kapan cuti akan/telah diambil</li>
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
    form.action = '{{ route("leaves.preview-pdf") }}';
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
    window.location.href = '{{ route("leaves.export-filter") }}';
}

// Validasi tanggal pengajuan
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

<script>
    // Preview PDF function
    function previewPdf() {
        // Ubah action form untuk preview
        const form = document.getElementById('exportForm');
        const originalAction = form.action;

        // Ganti action ke route preview
        form.action = '{{ route("leaves.preview-pdf") }}';
        form.target = '_blank'; // Buka di tab baru

        // Submit form
        form.submit();

        // Kembalikan action dan target ke semula
        setTimeout(() => {
            form.action = originalAction;
            form.target = '';
        }, 100);
    }

    // Reset form function
    function resetFilter() {
        document.getElementById('exportForm').reset();
        window.location.href = '{{ route("leaves.export-filter") }}';
    }

    // Validasi tanggal pengajuan
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = this.value;
        const endDateInput = document.getElementById('end_date');

        if (startDate) {
            endDateInput.min = startDate;

            // Jika end_date sudah diisi dan lebih kecil dari start_date, reset end_date
            if (endDateInput.value && endDateInput.value < startDate) {
                endDateInput.value = '';
                showNotification('Tanggal akhir pengajuan direset karena lebih kecil dari tanggal mulai', 'warning');
            }
        } else {
            endDateInput.removeAttribute('min');
        }
    });

    document.getElementById('end_date').addEventListener('change', function() {
        const endDate = this.value;
        const startDateInput = document.getElementById('start_date');

        if (endDate) {
            startDateInput.max = endDate;

            // Jika start_date sudah diisi dan lebih besar dari end_date, reset start_date
            if (startDateInput.value && startDateInput.value > endDate) {
                startDateInput.value = '';
                showNotification('Tanggal mulai pengajuan direset karena lebih besar dari tanggal akhir', 'warning');
            }
        } else {
            startDateInput.removeAttribute('max');
        }
    });

    // Validasi tanggal periode cuti
    document.getElementById('leave_start_date').addEventListener('change', function() {
        const startDate = this.value;
        const endDateInput = document.getElementById('leave_end_date');

        if (startDate) {
            endDateInput.min = startDate;

            // Jika leave_end_date sudah diisi dan lebih kecil dari leave_start_date, reset leave_end_date
            if (endDateInput.value && endDateInput.value < startDate) {
                endDateInput.value = '';
                showNotification('Tanggal akhir periode cuti direset karena lebih kecil dari tanggal mulai', 'warning');
            }
        } else {
            endDateInput.removeAttribute('min');
        }
    });

    document.getElementById('leave_end_date').addEventListener('change', function() {
        const endDate = this.value;
        const startDateInput = document.getElementById('leave_start_date');

        if (endDate) {
            startDateInput.max = endDate;

            // Jika leave_start_date sudah diisi dan lebih besar dari leave_end_date, reset leave_start_date
            if (startDateInput.value && startDateInput.value > endDate) {
                startDateInput.value = '';
                showNotification('Tanggal mulai periode cuti direset karena lebih besar dari tanggal akhir', 'warning');
            }
        } else {
            startDateInput.removeAttribute('max');
        }
    });

    // Fungsi untuk menampilkan notifikasi (opsional - memerlukan library seperti SweetAlert atau Toast)
    function showNotification(message, type = 'info') {
        // Menggunakan alert sederhana, bisa diganti dengan library notifikasi yang lebih baik
        if (type === 'warning') {
            console.warn(message);
            // Uncomment jika menggunakan SweetAlert
            // Swal.fire({
            //     icon: 'warning',
            //     title: 'Perhatian!',
            //     text: message,
            //     timer: 3000,
            //     showConfirmButton: false
            // });
        }
    }

    // Validasi form sebelum submit
    document.getElementById('exportForm').addEventListener('submit', function(e) {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const leaveStartDate = document.getElementById('leave_start_date').value;
        const leaveEndDate = document.getElementById('leave_end_date').value;

        // Validasi tanggal pengajuan
        if (startDate && endDate && startDate > endDate) {
            e.preventDefault();
            alert('Tanggal mulai pengajuan tidak boleh lebih besar dari tanggal akhir pengajuan!');
            return false;
        }

        // Validasi tanggal periode cuti
        if (leaveStartDate && leaveEndDate && leaveStartDate > leaveEndDate) {
            e.preventDefault();
            alert('Tanggal mulai periode cuti tidak boleh lebih besar dari tanggal akhir periode cuti!');
            return false;
        }

        // Tampilkan loading indicator
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        submitBtn.disabled = true;

        // Restore button setelah beberapa detik (fallback)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 10000);
    });

    // Auto-submit form ketika ada perubahan filter (opsional)
    function autoApplyFilter() {
        const filterElements = [
            'user_id', 'leave_type', 'status', 'start_date',
            'end_date', 'leave_start_date', 'leave_end_date'
        ];

        filterElements.forEach(elementId => {
            const element = document.getElementById(elementId);
            if (element) {
                element.addEventListener('change', function() {
                    // Auto-apply filter setelah delay singkat
                    clearTimeout(window.filterTimeout);
                    window.filterTimeout = setTimeout(() => {
                        // Uncomment jika ingin auto-refresh halaman dengan filter
                        // window.location.href = updateQueryString();
                    }, 500);
                });
            }
        });
    }

    // Fungsi untuk update query string (opsional)
    function updateQueryString() {
        const form = document.getElementById('exportForm');
        const formData = new FormData(form);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value.trim() !== '') {
                params.append(key, value);
            }
        }

        const currentPath = window.location.pathname;
        const queryString = params.toString();

        return queryString ? `${currentPath}?${queryString}` : currentPath;
    }

    // Inisialisasi saat dokumen ready
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi auto-apply filter jika diperlukan
        // autoApplyFilter();

        // Set tanggal maksimal untuk input tanggal (tidak boleh lebih dari hari ini)
        const today = new Date().toISOString().split('T')[0];
        const dateInputs = ['start_date', 'end_date', 'leave_start_date', 'leave_end_date'];

        dateInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input && !input.hasAttribute('max')) {
                // Uncomment jika ingin membatasi tanggal maksimal sampai hari ini
                // input.setAttribute('max', today);
            }
        });

        // Fokus ke input pencarian jika kosong
        const searchInput = document.getElementById('search');
        if (searchInput && !searchInput.value) {
            // searchInput.focus();
        }
    });

    // Fungsi untuk clear individual filter
    function clearFilter(filterId) {
        const filterElement = document.getElementById(filterId);
        if (filterElement) {
            filterElement.value = '';

            // Trigger change event
            const event = new Event('change', { bubbles: true });
            filterElement.dispatchEvent(event);
        }
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + Enter untuk submit form
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('exportForm').submit();
        }

        // Escape untuk reset form
        if (e.key === 'Escape') {
            e.preventDefault();
            resetFilter();
        }
    });
    </script>
