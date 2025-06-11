<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Karyawan</title>
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

        .badge-primary {
            background-color: #007bff;
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
        <h2>LAPORAN DATA KARYAWAN</h2>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Tanggal Generate:</span>
            <span>{{ $generated_at }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Karyawan:</span>
            <span>{{ $total_users }} orang</span>
        </div>
    </div>

    <!-- Filters Applied -->
    @if(array_filter($filters))
    <div class="filters">
        <h3>Filter yang Diterapkan:</h3>
        @if(!empty($filters['role']))
            <span class="filter-item">Role: {{ ucfirst($filters['role']) }}</span>
        @endif
        @if(!empty($filters['status']))
            <span class="filter-item">Status: {{ ucfirst($filters['status']) }}</span>
        @endif
        @if(!empty($filters['start_date']))
            <span class="filter-item">Dari Tanggal: {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }}</span>
        @endif
        @if(!empty($filters['end_date']))
            <span class="filter-item">Sampai Tanggal: {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}</span>
        @endif
        @if(!empty($filters['search']))
            <span class="filter-item">Pencarian: "{{ $filters['search'] }}"</span>
        @endif
        @if(!empty($filters['job_title']))
            <span class="filter-item">Jabatan: "{{ $filters['job_title'] }}"</span>
        @endif
    </div>
    @endif

    <!-- Data Table -->
    @if($users->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Nama</th>
                <th style="width: 20%;">Email</th>
                <th style="width: 10%;">Role</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 15%;">Jabatan</th>
                <th style="width: 12%;">No. Telepon</th>
                <th style="width: 10%;">Tgl. Daftar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td class="text-center">
                    @if($user->role == 'admin')
                        <span class="badge badge-danger">{{ ucfirst($user->role) }}</span>
                    @elseif($user->role == 'manager')
                        <span class="badge badge-warning">{{ ucfirst($user->role) }}</span>
                    @else
                        <span class="badge badge-info">{{ ucfirst($user->role) }}</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($user->status == 'aktif')
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-danger">Nonaktif</span>
                    @endif
                </td>
                <td>{{ $user->job_title ?? '-' }}</td>
                <td>{{ $user->phone ?? '-' }}</td>
                <td class="text-center">{{ $user->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <div class="summary">
        <strong>Ringkasan:</strong><br>
        @php
            $roleCount = $users->groupBy('role')->map->count();
            $statusCount = $users->groupBy('status')->map->count();
        @endphp

        <strong>Berdasarkan Role:</strong>
        @foreach($roleCount as $role => $count)
            {{ ucfirst($role) }}: {{ $count }} orang{{ !$loop->last ? ', ' : '' }}
        @endforeach
        <br>

        <strong>Berdasarkan Status:</strong>
        @foreach($statusCount as $status => $count)
            {{ ucfirst($status) }}: {{ $count }} orang{{ !$loop->last ? ', ' : '' }}
        @endforeach
    </div>
    @else
    <div class="no-data">
        <p>Tidak ada data karyawan yang sesuai dengan filter yang diterapkan.</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem pada {{ $generated_at }}</p>
        <p>{{ $company_name }} - Sistem Manajemen Karyawan</p>
    </div>
</body>
</html>
