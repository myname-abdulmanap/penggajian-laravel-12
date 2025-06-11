<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Karyawan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .filter-info {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
        }

        .filter-info h3 {
            font-size: 13px;
            margin-bottom: 8px;
            color: #333;
        }

        .filter-row {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
        }

        .filter-label {
            font-weight: bold;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        .text-left {
            text-align: left;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }

        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-info { background-color: #17a2b8; }
        .badge-danger { background-color: #dc3545; }
        .badge-secondary { background-color: #6c757d; }

        .late-badge {
            padding: 2px 4px;
            border-radius: 2px;
            font-size: 9px;
            font-weight: bold;
        }

        .late-ontime { background-color: #d4edda; color: #155724; }
        .late-late { background-color: #f8d7da; color: #721c24; }
        .late-nocheck { background-color: #e2e3e5; color: #383d41; }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }

        .summary {
            background-color: #e9ecef;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
        }

        .summary h3 {
            font-size: 13px;
            margin-bottom: 8px;
        }

        .summary-item {
            display: inline-block;
            margin-right: 15px;
            font-size: 11px;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        /* Page break */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN ABSENSI KARYAWAN</h1>
        <p>Digenerate pada: {{ $generated_at }}</p>
    </div>

    <!-- Filter Information -->
    <div class="filter-info">
        <h3>Filter yang Diterapkan:</h3>
        <div class="filter-row">
            <span class="filter-label">Periode:</span>
            {{ $filters['start_date'] ? \Carbon\Carbon::parse($filters['start_date'])->format('d-m-Y') : 'Semua' }}
            s/d
            {{ $filters['end_date'] ? \Carbon\Carbon::parse($filters['end_date'])->format('d-m-Y') : 'Semua' }}
        </div>

        @if($filters['user_id'])
            <div class="filter-row">
                <span class="filter-label">Karyawan:</span>
                {{ $attendances->first() ? $attendances->first()->user->name : '-' }}
            </div>
        @endif

        @if($filters['status'])
            <div class="filter-row">
                <span class="filter-label">Status:</span> {{ ucfirst($filters['status']) }}
            </div>
        @endif

        @if($filters['late_status'])
            <div class="filter-row">
                <span class="filter-label">Keterlambatan:</span> {{ $filters['late_status'] }}
            </div>
        @endif

        @if($setting)
            <div class="filter-row">
                <span class="filter-label">Jam Kerja:</span> {{ $setting->jam_masuk }} - {{ $setting->jam_pulang }}
            </div>
        @endif
    </div>

    <!-- Summary -->
    @if($attendances->count() > 0)
        <div class="summary">
            <h3>Ringkasan Data:</h3>
            <div class="summary-item">
                <strong>Total Record:</strong> {{ $attendances->count() }}
            </div>
            <div class="summary-item">
                <strong>Hadir:</strong> {{ $attendances->where('status', 'hadir')->count() }}
            </div>
            <div class="summary-item">
                <strong>Terlambat:</strong> {{ $attendances->where('late_status', 'Terlambat')->count() }}
            </div>
            <div class="summary-item">
                <strong>Tepat Waktu:</strong> {{ $attendances->where('late_status', 'Tepat Waktu')->count() }}
            </div>
        </div>
    @endif

    <!-- Data Table -->
    @if($attendances->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 20%;">Nama Karyawan</th>
                    <th style="width: 10%;">Check In</th>
                    <th style="width: 10%;">Check Out</th>
                    <th style="width: 12%;">Status Absensi</th>
                    <th style="width: 12%;">Keterlambatan</th>
                    <th style="width: 19%;">Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $index => $attendance)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $attendance->formatted_date }}</td>
                        <td class="text-left">{{ $attendance->user->name }}</td>
                        <td>{{ $attendance->check_in ?? '-' }}</td>
                        <td>{{ $attendance->check_out ?? '-' }}</td>
                        <td>
                            @if($attendance->status == 'hadir')
                                <span class="badge badge-success">Hadir</span>
                            @elseif($attendance->status == 'izin')
                                <span class="badge badge-warning">Izin</span>
                            @elseif($attendance->status == 'sakit')
                                <span class="badge badge-info">Sakit</span>
                            @elseif($attendance->status == 'alpha')
                                <span class="badge badge-danger">Alpha</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($attendance->status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($attendance->late_status == 'Tepat Waktu')
                                <span class="late-badge late-ontime">Tepat Waktu</span>
                            @elseif($attendance->late_status == 'Terlambat')
                                <span class="late-badge late-late">Terlambat</span>
                            @else
                                <span class="late-badge late-nocheck">{{ $attendance->late_status }}</span>
                            @endif
                        </td>
                        <td class="text-left">{{ $attendance->location ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data absensi yang sesuai dengan filter yang diterapkan.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini digenerate secara otomatis oleh sistem pada {{ $generated_at }}</p>
    </div>
</body>
</html>
