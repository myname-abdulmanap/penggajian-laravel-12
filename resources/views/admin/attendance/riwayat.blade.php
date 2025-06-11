@extends('layouts.app')

@section('content')
    <div class="col-lg-10 mb-10 mx-auto">
        <!-- Card Absensi -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h4 class="m-0 font-weight-bold">Data Absensi</h4>
                <!-- Filter Form -->
                <form action="{{ route('admin.attendance.riwayat') }}" method="GET" class="form-inline">
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
                            <th>Nama</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            {{-- <th>Dibuat</th>
                        <th>Diubah</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr align="center">
                                <td>{{ $attendance->formatted_date }}</td>
                                <td>{{ $attendance->user->name }}</td>
                                <td>{{ $attendance->check_in ?? '-' }}</td>
                                <td>{{ $attendance->check_out ?? '-' }}</td>
                                <td>
                                    @if ($attendance->status == 'hadir')
                                        <span class="badge badge-success">Hadir</span>
                                    @elseif($attendance->status == 'izin')
                                        <span class="badge badge-warning">Izin</span>
                                    @elseif($attendance->status == 'sakit')
                                        <span class="badge badge-info">Sakit</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($attendance->status) }}</span>
                                    @endif
                                </td>
                                {{-- <td>{{ $attendance->created_at->format('d-m-Y H:i') }}</td>
                            <td>{{ $attendance->updated_at->format('d-m-Y H:i') }}</td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
