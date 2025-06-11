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
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h4 class="m-0 font-weight-bold">Riwayat Absensi</h4>
                <form action="{{ route('attendance.riwayat') }}" method="GET" class="form-inline">
                    <div class="form-group mx-2">
                        <label for="start_date">Dari</label>
                        <input type="date" name="start_date" id="start_date" class="form-control ml-2"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="form-group mx-2">
                        <label for="end_date">Sampai</label>
                        <input type="date" name="end_date" id="end_date" class="form-control ml-2"
                            value="{{ request('end_date') }}">
                    </div>
                    <button type="submit" class="btn btn-primary ml-2">Filter</button>
                </form>

                <!-- Export Button -->
                <a href="{{ route('attendance.export-filter') }}" class="btn btn-success ml-2">
                    <i class="fas fa-file-pdf"></i>
                </a>
            </div>

            <div class="card-body">
                <table id="dataTable" class="table table-bordered table-hover">
                    <thead>
                        <tr align="center">
                            <th>Tgl</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr align="center">
                                <td>{{ $attendance->formatted_date }}</td>
                                <td>{{ $attendance->check_in ?? '-' }}</td>
                                <td>{{ $attendance->check_out ?? '-' }}</td>
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
@endsection
