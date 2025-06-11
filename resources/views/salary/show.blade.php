@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">SLIP GAJI PEGAWAI</h4>
                    <div>

                        <a href="{{ route('salaries.pdf', $salary->salary_id) }}" class="btn btn-success btn-sm" target="_blank">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                        <a href="{{ route('salaries.pdf.view', $salary->salary_id) }}" class="btn btn-info btn-sm" target="_blank">
                            <i class="fas fa-eye"></i> Lihat PDF
                        </a>
                        <a href="{{ route('salaries.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body" id="slip-content">
                    <!-- Header Perusahaan -->
                    <div class="text-center mb-4">
                        <h3 class="company-name">{{ config('app.company') }}</h3>
                        <p class="company-address mb-1">{{ config('app.alamat', 'Karawang, Jawa Barat') }}</p>
                        <p class="company-contact">{{ config('app.contact', 'Telp: (021) 123456 | email: cvpurwaputera@gmail.com') }}</p>
                        <hr class="my-3">
                    </div>

                    <!-- Informasi Pegawai -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="120">Nama Pegawai</td>
                                    <td width="10">:</td>
                                    <td>{{ $salary->user->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">NIP/ID</td>
                                    <td>:</td>
                                    <td>{{ $salary->user->users_id }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Jabatan</td>
                                    <td>:</td>
                                    <td>{{ $salary->user->job_title ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="120">Periode</td>
                                    <td width="10">:</td>
                                    <td>{{ \Carbon\Carbon::parse($salary->period)->format('F Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Cetak</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::now()->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Absensi</td>
                                    <td>:</td>
                                    <td>{{ $salary->total_attendance ?? 0 }} hari</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Detail Gaji -->
                    <div class="row">
                        <!-- Kolom Kiri - Pendapatan -->
                        <div class="col-md-6">
                            <h5 class="text-success mb-3">
                                <i class="fas fa-plus-circle"></i> PENDAPATAN
                            </h5>

                            <table class="table table-striped">
                                <tr>
                                    <td>Gaji Pokok</td>
                                    <td class="text-end">Rp {{ number_format($salary->base_salary, 0, ',', '.') }}</td>
                                </tr>

                                @if($salary->overtime > 0)
                                <tr>
                                    <td>Lembur</td>
                                    <td class="text-end">Rp {{ number_format($salary->overtime, 0, ',', '.') }}</td>
                                </tr>
                                @endif

                                @if($salary->allowances && $salary->allowances->count() > 0)
                                    @foreach($salary->allowances as $allowance)
                                    <tr>
                                        <td>{{ $allowance->name }}</td>
                                        <td class="text-end">
                                            @if($allowance->type === 'percentage')
                                                @php
                                                    $calculatedAmount = ($salary->base_salary * $allowance->percentage) / 100;
                                                @endphp
                                                Rp {{ number_format($calculatedAmount, 0, ',', '.') }}
                                                <small class="text-muted">({{ rtrim(rtrim(number_format($allowance->percentage, 2), '0'), '.') }}% dari gaji pokok)</small>
                                            @else
                                                Rp {{ number_format($allowance->amount, 0, ',', '.') }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif

                                <tr class="table-success fw-bold">
                                    <td>TOTAL PENDAPATAN</td>
                                    <td class="text-end">
                                        @php
                                            $totalAllowances = 0;
                                            if($salary->allowances && $salary->allowances->count() > 0) {
                                                foreach($salary->allowances as $allowance) {
                                                    if($allowance->type === 'percentage') {
                                                        $totalAllowances += ($salary->base_salary * $allowance->percentage) / 100;
                                                    } else {
                                                        $totalAllowances += $allowance->amount;
                                                    }
                                                }
                                            }
                                            $totalPendapatan = $salary->base_salary + $salary->overtime + $totalAllowances;
                                        @endphp
                                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Kolom Kanan - Potongan -->
                        <div class="col-md-6">
                            <h5 class="text-danger mb-3">
                                <i class="fas fa-minus-circle"></i> POTONGAN
                            </h5>

                            <table class="table table-striped">
                                @if($salary->deductions && $salary->deductions->count() > 0)
                                    @foreach($salary->deductions as $deduction)
                                    <tr>
                                        <td>{{ $deduction->name }}</td>
                                        <td class="text-end">
                                            @if($deduction->type === 'percentage')
                                                @php
                                                    $calculatedAmount = ($salary->base_salary * $deduction->percentage) / 100;
                                                @endphp
                                                Rp {{ number_format($calculatedAmount, 0, ',', '.') }}
                                                <small class="text-muted">({{ rtrim(rtrim(number_format($deduction->percentage, 2), '0'), '.') }}% dari gaji pokok)</small>
                                            @else
                                                Rp {{ number_format($deduction->amount, 0, ',', '.') }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">Tidak ada potongan</td>
                                    </tr>
                                @endif

                                <tr class="table-danger fw-bold">
                                    <td>TOTAL POTONGAN</td>
                                    <td class="text-end">
                                        @php
                                            $totalDeductions = 0;
                                            if($salary->deductions && $salary->deductions->count() > 0) {
                                                foreach($salary->deductions as $deduction) {
                                                    if($deduction->type === 'percentage') {
                                                        $totalDeductions += ($salary->base_salary * $deduction->percentage) / 100;
                                                    } else {
                                                        $totalDeductions += $deduction->amount;
                                                    }
                                                }
                                            }
                                        @endphp
                                        Rp {{ number_format($totalDeductions, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Gaji Bersih -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h4 class="mb-0">
                                            <i class="fas fa-money-bill-wave"></i> GAJI BERSIH
                                        </h4>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h3 class="mb-0 text-primary fw-bold">
                                            Rp {{ number_format($salary->net_salary, 0, ',', '.') }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terbilang -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <p class="text-muted">
                                <strong>Terbilang:</strong> {{ ucwords(terbilang($salary->net_salary)) }} Rupiah
                            </p>
                        </div>
                    </div>

                    <!-- Tanda Tangan -->
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="text-center">
                                <p class="mb-5">Diterima Oleh,</p>
                                <div style="height: 60px;"></div>
                                <p class="fw-bold">{{ $salary->user->name }}</p>
                                <p class="text-muted">Pegawai</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <p class="mb-5">Mengetahui,</p>
                                <div style="height: 60px;"></div>
                                <p class="fw-bold">____________________</p>
                                <p class="text-muted">HRD Manager</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .card-header .btn,
    .btn {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .container {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    body {
        font-size: 12px;
    }

    .company-name {
        font-size: 18px;
        font-weight: bold;
    }

    .table {
        font-size: 11px;
    }

    .alert {
        border: 2px solid #007bff !important;
        background-color: #f8f9fa !important;
    }

    /* Pastikan tidak ada page break di tengah tabel */
    .table tr {
        page-break-inside: avoid;
    }
}

/* Custom styles for better appearance */
.company-name {
    color: #2c3e50;
    font-weight: bold;
}

.company-address, .company-contact {
    color: #7f8c8d;
    font-size: 0.9em;
}

.table-borderless td {
    padding: 0.25rem 0.5rem;
    border: none;
}

.fw-bold {
    font-weight: bold;
}

.text-end {
    text-align: right;
}
</style>

<!-- Helper function untuk terbilang (tambahkan di helper atau langsung di blade) -->
@php
function terbilang($angka) {
    $angka = abs($angka);
    $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $terbilang = "";

    if ($angka < 12) {
        $terbilang = " " . $baca[$angka];
    } else if ($angka < 20) {
        $terbilang = terbilang($angka - 10) . " belas";
    } else if ($angka < 100) {
        $terbilang = terbilang($angka / 10) . " puluh" . terbilang($angka % 10);
    } else if ($angka < 200) {
        $terbilang = " seratus" . terbilang($angka - 100);
    } else if ($angka < 1000) {
        $terbilang = terbilang($angka / 100) . " ratus" . terbilang($angka % 100);
    } else if ($angka < 2000) {
        $terbilang = " seribu" . terbilang($angka - 1000);
    } else if ($angka < 1000000) {
        $terbilang = terbilang($angka / 1000) . " ribu" . terbilang($angka % 1000);
    } else if ($angka < 1000000000) {
        $terbilang = terbilang($angka / 1000000) . " juta" . terbilang($angka % 1000000);
    } else if ($angka < 1000000000000) {
        $terbilang = terbilang($angka / 1000000000) . " milyar" . terbilang(fmod($angka, 1000000000));
    } else if ($angka < 1000000000000000) {
        $terbilang = terbilang($angka / 1000000000000) . " trilyun" . terbilang(fmod($angka, 1000000000000));
    }

    return $terbilang;
}
@endphp
@endsection
