<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Cuti Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }

        .info-section {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            color: #333;
        }

        .filters {
            margin-bottom: 20px;
        }

        .filters h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
        }

        .filter-item {
            display: inline-block;
            background-color: #e9ecef;
            padding: 5px 10px;
            margin: 2px;
            border-radius: 3px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }

        .badge-primary {
            background-color: #007bff;
            color: white;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        .summary {
            margin-top: 20px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }

        .reason-text {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $company_name }}</h1>
        <h2>LAPORAN DATA CUTI KARYAWAN</h2>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Tanggal Generate:</span>
            <span>{{ $generated_at }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Data Cuti:</span>
            <span>{{ $total_leaves }} data</span>
        </div>
    </div>

    <!-- Filters Applied -->
    @if(array_filter($filters))
    <div class="filters">
        <h3>Filter yang Diterapkan:</h3>
        @if(!empty($filters['user_id']))
            @php
                $selectedUser = \App\Models\User::find($filters['user_id']);
            @endphp
            <span class="filter-item">Karyawan: {{ $selectedUser ? $selectedUser->name : 'N/A' }}</span>
        @endif
        @if(!empty($filters['leave_type']))
            <span class="filter-item">Jenis Cuti: {{ ucfirst($filters['leave_type']) }}</span>
        @endif
        @if(!empty($filters['status']))
            <span class="filter-item">Status: {{ ucfirst($filters['status']) }}</span>
        @endif
        @if(!empty($filters['start_date']))
            <span class="filter-item">Pengajuan Dari: {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }}</span>
        @endif
        @if(!empty($filters['end_date']))
            <span class="filter-item">Pengajuan Sampai: {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}</span>
        @endif
        @if(!empty($filters['leave_start_date']))
            <span class="filter-item">Cuti Dari: {{ \Carbon\Carbon::parse($filters['leave_start_date'])->format('d/m/Y') }}</span>
        @endif
        @if(!empty($filters['leave_end_date']))
            <span class="filter-item">Cuti Sampai: {{ \Carbon\Carbon::parse($filters['leave_end_date'])->format('d/m/Y') }}</span>
        @endif
        @if(!empty($filters['search']))
            <span class="filter-item">Pencarian: "{{ $filters['search'] }}"</span>
        @endif
    </div>
    @endif

    <!-- Data Table -->
    @if($leaves->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 15%;">Nama Karyawan</th>
                <th style="width: 10%;">Jenis Cuti</th>
                <th style="width: 10%;">Tanggal Mulai</th>
                <th style="width: 10%;">Tanggal Selesai</th>
                <th style="width: 8%;">Durasi</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 20%;">Alasan</th>
                <th style="width: 8%;">Lampiran</th>
                <th style="width: 10%;">Tgl. Pengajuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaves as $index => $leave)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $leave->user->name ?? 'N/A' }}</td>
                <td class="text-center">
                    @if($leave->leave_type == 'Sakit')
                        <span class="badge badge-danger">Sakit</span>
                    @elseif($leave->leave_type == 'Tahunan')
                        <span class="badge badge-success">Tahunan</span>
                    @elseif($leave->leave_type == 'Melahirkan')
                        <span class="badge badge-info">Melahirkan</span>
                    @elseif($leave->leave_type == 'Menikah')
                        <span class="badge badge-warning">Menikah</span>
                    @else
                        <span class="badge badge-secondary">Lainnya</span>
                    @endif
                </td>
                <td class="text-center">{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</td>
                <td class="text-center">
                    @php
                        $start = \Carbon\Carbon::parse($leave->start_date);
                        $end = \Carbon\Carbon::parse($leave->end_date);
                        $duration = $start->diffInDays($end) + 1;
                    @endphp
                    {{ $duration }} hari
                </td>
                <td class="text-center">
                    @if($leave->status == 'approved')
                        <span class="badge badge-success">Disetujui</span>
                    @elseif($leave->status == 'rejected')
                        <span class="badge badge-danger">Ditolak</span>
                    @else
                        <span class="badge badge-warning">Menunggu</span>
                    @endif
                </td>
                <td>
                    <div class="reason-text" title="{{ $leave->reason }}">
                        {{ $leave->reason ?? '-' }}
                    </div>
                </td>
                <td class="text-center">
                    @if($leave->attachment)
                        <span class="badge badge-primary">Ada</span>
                    @else
                        <span class="badge badge-secondary">Tidak Ada</span>
                    @endif
                </td>
                <td class="text-center">{{ $leave->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <div class="summary">
        <strong>Ringkasan:</strong><br>
        @php
            $typeCount = $leaves->groupBy('leave_type')->map->count();
            $statusCount = $leaves->groupBy('status')->map->count();
            $totalDays = $leaves->sum(function($leave) {
                $start = \Carbon\Carbon::parse($leave->start_date);
                $end = \Carbon\Carbon::parse($leave->end_date);
                return $start->diffInDays($end) + 1;
            });
        @endphp

        <strong>Berdasarkan Jenis Cuti:</strong>
        @foreach($typeCount as $type => $count)
            @php
                $typeLabel = match($type) {
                    'Sakit' => 'Sakit',
                    'Menikah' => 'Menikah',
                    'Tahunan' => 'Tahunan',
                    'Melahirkan' => 'Melahirkan',

                    default => 'Lainnya'
                };
            @endphp
            {{ $typeLabel }}: {{ $count }} data{{ !$loop->last ? ', ' : '' }}
        @endforeach
        <br>

        <strong>Berdasarkan Status:</strong>
        @foreach($statusCount as $status => $count)
            @php
                $statusLabel = match($status) {
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    default => 'Menunggu'
                };
            @endphp
            {{ $statusLabel }}: {{ $count }} data{{ !$loop->last ? ', ' : '' }}
        @endforeach
        <br>

        <strong>Total Hari Cuti:</strong> {{ $totalDays }} hari
    </div>
    @else
    <div class="no-data">
        <p>Tidak ada data cuti yang sesuai dengan filter yang diterapkan.</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem pada {{ $generated_at }}</p>
        <p>{{ $company_name }} - Sistem Manajemen Cuti Karyawan</p>
    </div>
</body>
</html>
