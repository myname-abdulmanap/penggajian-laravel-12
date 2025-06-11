@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Input Gaji Pegawai</div>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card-body">
                    <form method="POST" action="{{ route('salaries.store') }}">
                        @csrf

                        <!-- Pegawai -->
                        <div class="form-group mb-3">
                            <label for="users_id">Pilih Pegawai</label>
                            <select name="users_id" id="users_id" class="form-control" required>
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->users_id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Periode -->
                        <div class="form-group mb-3">
                            <label for="period">Periode</label>
                            <input type="date" name="period" id="period" class="form-control" required>
                        </div>

                        <!-- Total Attendance (Hidden field untuk dikirim) -->
                        <input type="hidden" name="total_attendance" id="total_attendance" value="0">

                        <!-- Display Total Absen -->
                        <div class="form-group mb-3">
                            <label>Total Absen Bulan Ini</label>
                            <input type="text" id="absensi_display" class="form-control" readonly value="0 hari">
                        </div>

                        <!-- Gaji Pokok -->
                        <div class="form-group mb-3">
                            <label for="base_salary">Gaji Pokok</label>
                            <input type="number" name="base_salary" id="base_salary" class="form-control" required>
                        </div>

                        <!-- Lembur -->
                        <div class="form-group mb-3">
                            <label for="overtime">Lembur (Rp)</label>
                            <input type="number" name="overtime" id="overtime" class="form-control" value="0">
                        </div>

                        <!-- Tunjangan -->
                        <div id="allowance-container" class="mb-3">
                            <label>Tunjangan</label>
                            <div class="row mb-2 allowance-row">
                                <div class="col-md-9">
                                    <select name="allowance_ids[]" class="form-control allowance-select">
                                        <option value="">-- Pilih Tunjangan --</option>
                                        @foreach ($allowances as $item)
                                            <option
                                                value="{{ $item->allowance_id }}"
                                                data-type="{{ $item->type }}"
                                                data-amount="{{ $item->amount ?? 0 }}"
                                                data-percentage="{{ $item->percentage ?? 0 }}"
                                            >
                                                {{ $item->name }} -
                                                @if($item->type === 'percentage')
                                                    {{ rtrim(rtrim(number_format($item->percentage, 2), '0'), '.') }}% dari gaji pokok
                                                @else
                                                    Rp. {{ number_format($item->amount, 0, ',', '.') }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary" id="addAllowance">+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Potongan -->
                        <div id="deduction-container" class="mb-3">
                            <label>Potongan</label>
                            <div class="row mb-2 deduction-row">
                                <div class="col-md-9">
                                    <select name="deduction_ids[]" class="form-control deduction-select">
                                        <option value="">-- Pilih Potongan --</option>
                                        @foreach ($deductions as $item)
                                            <option
                                                value="{{ $item->deduction_id }}"
                                                data-type="{{ $item->type }}"
                                                data-amount="{{ $item->amount ?? 0 }}"
                                                data-percentage="{{ $item->percentage ?? 0 }}"
                                            >
                                                {{ $item->name }} -
                                                @if($item->type === 'percentage')
                                                    {{ rtrim(rtrim(number_format($item->percentage, 2), '0'), '.') }}% dari gaji pokok
                                                @else
                                                    Rp. {{ number_format($item->amount, 0, ',', '.') }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary" id="addDeduction">+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Gaji Bersih -->
                        <div class="form-group mb-3">
                            <label for="net_salary">Total Gaji Bersih</label>
                            <input type="number" name="net_salary" id="net_salary" class="form-control" readonly>
                        </div>

                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="{{ route('salaries.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS Perhitungan -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    function updateNetSalary() {
        const base = parseFloat(document.getElementById('base_salary').value) || 0;
        const overtime = parseFloat(document.getElementById('overtime').value) || 0;

        let totalAllowance = 0;
        document.querySelectorAll('.allowance-select').forEach(select => {
            if (select.value && select.value !== '') {
                const selected = select.selectedOptions[0];
                const type = selected.getAttribute('data-type');

                console.log('Allowance - Type:', type, 'Selected Value:', select.value);

                if (type === 'percentage') {
                    const percentStr = selected.getAttribute('data-percentage');
                    console.log('Raw percentage string:', percentStr);

                    // Parse percentage - handle decimal values like 15.00
                    const percent = parseFloat(percentStr);
                    console.log('Parsed percentage:', percent);

                    if (!isNaN(percent) && percent >= 0) {
                        const calculatedAmount = (base * percent) / 100;
                        totalAllowance += calculatedAmount;
                        console.log('Allowance - Percentage:', percent + '%', 'Base:', base, 'Calculated:', calculatedAmount);
                    } else {
                        console.log('Invalid percentage value for allowance:', percent);
                    }
                } else if (type === 'fixed') {
                    const amountStr = selected.getAttribute('data-amount');
                    const amount = parseFloat(amountStr);
                    if (!isNaN(amount) && amount >= 0) {
                        totalAllowance += amount;
                        console.log('Allowance - Fixed amount:', amount);
                    }
                }
            }
        });

        let totalDeduction = 0;
        document.querySelectorAll('.deduction-select').forEach(select => {
            if (select.value && select.value !== '') {
                const selected = select.selectedOptions[0];
                const type = selected.getAttribute('data-type');

                console.log('Deduction - Type:', type, 'Selected Value:', select.value);

                if (type === 'percentage') {
                    const percentStr = selected.getAttribute('data-percentage');
                    console.log('Raw percentage string:', percentStr);

                    // Parse percentage - handle decimal values like 15.00
                    const percent = parseFloat(percentStr);
                    console.log('Parsed percentage:', percent);

                    if (!isNaN(percent) && percent >= 0) {
                        const calculatedAmount = (base * percent) / 100;
                        totalDeduction += calculatedAmount;
                        console.log('Deduction - Percentage:', percent + '%', 'Base:', base, 'Calculated:', calculatedAmount);
                    } else {
                        console.log('Invalid percentage value for deduction:', percent);
                    }
                } else if (type === 'fixed') {
                    const amountStr = selected.getAttribute('data-amount');
                    const amount = parseFloat(amountStr);
                    if (!isNaN(amount) && amount >= 0) {
                        totalDeduction += amount;
                        console.log('Deduction - Fixed amount:', amount);
                    }
                }
            }
        });

        const netSalary = base + overtime + totalAllowance - totalDeduction;
        document.getElementById('net_salary').value = Math.round(netSalary);

        // Debug log komprehensif
        console.log('=== Perhitungan Gaji ===');
        console.log('Base Salary:', base);
        console.log('Overtime:', overtime);
        console.log('Total Allowance:', totalAllowance);
        console.log('Total Deduction:', totalDeduction);
        console.log('Net Salary:', netSalary);
        console.log('=====================');
    }

    // Event listeners untuk input
    const baseSalaryInput = document.getElementById('base_salary');
    const overtimeInput = document.getElementById('overtime');

    if (baseSalaryInput) {
        baseSalaryInput.addEventListener('input', updateNetSalary);
        baseSalaryInput.addEventListener('keyup', updateNetSalary);
        baseSalaryInput.addEventListener('change', updateNetSalary);
    }

    if (overtimeInput) {
        overtimeInput.addEventListener('input', updateNetSalary);
        overtimeInput.addEventListener('keyup', updateNetSalary);
        overtimeInput.addEventListener('change', updateNetSalary);
    }

    // Event delegation untuk select yang dinamis
    document.getElementById('allowance-container').addEventListener('change', function(e) {
        if (e.target.classList.contains('allowance-select')) {
            console.log('Allowance changed'); // Debug
            updateNetSalary();
        }
    });

    document.getElementById('deduction-container').addEventListener('change', function(e) {
        if (e.target.classList.contains('deduction-select')) {
            console.log('Deduction changed'); // Debug
            updateNetSalary();
        }
    });

    // Tambah tunjangan
    document.getElementById('addAllowance').addEventListener('click', function () {
        const originalRow = document.querySelector('.allowance-row');
        const row = originalRow.cloneNode(true);

        // Reset pilihan
        const select = row.querySelector('select');
        select.value = '';
        select.selectedIndex = 0;

        // Ganti tombol + dengan tombol -
        const button = row.querySelector('button');
        button.className = 'btn btn-danger remove-allowance';
        button.textContent = '-';

        document.getElementById('allowance-container').appendChild(row);
    });

    // Tambah potongan
    document.getElementById('addDeduction').addEventListener('click', function () {
        const originalRow = document.querySelector('.deduction-row');
        const row = originalRow.cloneNode(true);

        // Reset pilihan
        const select = row.querySelector('select');
        select.value = '';
        select.selectedIndex = 0;

        // Ganti tombol + dengan tombol -
        const button = row.querySelector('button');
        button.className = 'btn btn-danger remove-deduction';
        button.textContent = '-';

        document.getElementById('deduction-container').appendChild(row);
    });

    // Hapus tunjangan/potongan
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-allowance')) {
            e.target.closest('.allowance-row').remove();
            updateNetSalary();
        }
        if (e.target.classList.contains('remove-deduction')) {
            e.target.closest('.deduction-row').remove();
            updateNetSalary();
        }
    });

    // Inisialisasi perhitungan
    setTimeout(function() {
        updateNetSalary();
    }, 100);
});
</script>

<!-- JS Fetch Absen -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const userSelect = document.getElementById('users_id');
    const periodInput = document.getElementById('period');
    const absensiDisplay = document.getElementById('absensi_display');
    const totalAttendanceInput = document.getElementById('total_attendance');

    function fetchAbsensi() {
        const userId = userSelect.value;
        const period = periodInput.value;

        if (userId && period) {
            fetch(`/get-absensi?user_id=${userId}&period=${period}`)
                .then(response => response.json())
                .then(data => {
                    // Update display field
                    absensiDisplay.value = `${data.total_absensi} hari`;

                    // Update hidden field untuk dikirim ke server
                    totalAttendanceInput.value = data.total_absensi;
                })
                .catch(error => {
                    console.error('Error fetching absensi:', error);
                    absensiDisplay.value = 'Gagal mengambil data';
                    totalAttendanceInput.value = 0;
                });
        } else {
            absensiDisplay.value = '0 hari';
            totalAttendanceInput.value = 0;
        }
    }

    userSelect.addEventListener('change', fetchAbsensi);
    periodInput.addEventListener('change', fetchAbsensi);
});
</script>
@endsection
