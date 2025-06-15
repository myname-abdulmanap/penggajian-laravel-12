@extends('layouts.app')

@section('content')
    <div class="col-lg-10 mb-10 mx-auto">
        <div class="card shadow mb-4">
            <!-- Menampilkan pesan kesuksesan -->
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-error" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Header Card yang Responsif -->
            <div class="card-header py-3">
                <!-- Title -->
                <div class="row align-items-center mb-3 mb-md-0">
                    <div class="col-12 col-md-auto mb-2 mb-md-0">
                        <h4 class="m-0 font-weight-bold text-center text-md-left">Riwayat Absensi</h4>
                    </div>

                    <!-- Export Button - Tampil di mobile sebagai button terpisah -->
                    <div class="col-12 col-md-auto text-center text-md-right mb-3 mb-md-0 order-md-3">
                        <a href="{{ route('attendance.export-filter') }}" class="btn btn-success">
                            <i class="fas fa-file-pdf"></i>
                            <span class="d-inline d-md-none ml-1">Export PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('attendance.riwayat') }}" method="GET" class="filter-form">
                            <div class="row align-items-end">
                                <!-- Start Date -->
                                <div class="col-12 col-sm-6 col-md-3 mb-2">
                                    <label for="start_date" class="form-label small">Dari</label>
                                    <input type="date" name="start_date" id="start_date"
                                           class="form-control form-control-sm"
                                           value="{{ request('start_date') }}">
                                </div>

                                <!-- End Date -->
                                <div class="col-12 col-sm-6 col-md-3 mb-2">
                                    <label for="end_date" class="form-label small">Sampai</label>
                                    <input type="date" name="end_date" id="end_date"
                                           class="form-control form-control-sm"
                                           value="{{ request('end_date') }}">
                                </div>

                                <!-- Filter Button -->
                                <div class="col-12 col-md-2 mb-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-filter d-md-none"></i>
                                        <span class="d-none d-md-inline">Filter</span>
                                        <span class="d-inline d-md-none ml-1">Filter</span>
                                    </button>
                                </div>

                                <!-- Reset Button (Optional) -->
                                <div class="col-12 col-md-2 mb-2">
                                    <a href="{{ route('attendance.riwayat') }}" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="fas fa-undo d-md-none"></i>
                                        <span class="d-none d-md-inline">Reset</span>
                                        <span class="d-inline d-md-none ml-1">Reset</span>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Table Container dengan scroll horizontal di mobile -->
                <div class="table-responsive">
                    <table id="dataTable" class="table table-bordered table-hover">
                        <thead>
                            <tr align="center">
                                <th class="text-nowrap">Tgl</th>
                                <th class="text-nowrap">Lokasi</th>
                                <th class="text-nowrap">Check In</th>
                                <th class="text-nowrap">Check Out</th>
                                <th class="text-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendances as $attendance)
                                <tr align="center">
                                    <td class="text-nowrap">{{ $attendance->formatted_date }}</td>
                                    <td class="text-nowrap">{{ $attendance->location ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $attendance->check_in ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $attendance->check_out ?? '-' }}</td>
                                    <td>
                                        @if ($attendance->status == 'terlambat')
                                            <span class="badge badge-danger">Terlambat</span>
                                        @elseif($attendance->status == 'tepat waktu')
                                            <span class="badge badge-success">Tepat Waktu</span>
                                        @elseif($attendance->status == 'belum check-in')
                                            <span class="badge badge-secondary">Belum Check-In</span>
                                        @else
                                            <span class="badge badge-light">{{ ucfirst($attendance->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom CSS untuk Mobile Responsiveness -->
    <style>
        /* Mobile First Approach */
        @media (max-width: 767.98px) {
            .card-header {
                padding: 1rem !important;
            }

            .filter-form .row {
                margin: 0;
            }

            .filter-form .row > div {
                padding-left: 5px;
                padding-right: 5px;
            }

            .form-label {
                margin-bottom: 0.25rem;
                font-weight: 600;
            }

            .btn-sm {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }

            /* Table responsiveness */
            .table-responsive {
                border: none;
            }

            .table td, .table th {
                padding: 0.5rem;
                font-size: 0.875rem;
            }

            .badge {
                font-size: 0.75rem;
                padding: 0.25em 0.5em;
            }
        }

        /* Tablet */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .card-header {
                padding: 1.25rem !important;
            }
        }

        /* Desktop */
        @media (min-width: 992px) {
            .filter-form .row {
                justify-content: flex-start;
            }
        }

        /* Utility classes */
        .text-nowrap {
            white-space: nowrap;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
    </style>
@endsection
