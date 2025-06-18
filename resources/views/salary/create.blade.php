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
                                    <option value="{{ $user->users_id }}">{{ $user->name }} ({{$user->users_id}})</option>
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

                        <!-- Display Total Cuti -->
                        <div class="form-group mb-3">
                            <label>Total Cuti Bulan Ini</label>
                            <input type="text" id="cuti_display" class="form-control" readonly value="0 hari">
                        </div>

                        <!-- Display Total Terlambat -->
                        <div class="form-group mb-3">
                            <label>Total Terlambat Bulan Ini</label>
                            <input type="text" id="telat_display" class="form-control" readonly value="0 hari">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Global variables
    let totalTelatDays = 0;
    const LATE_PENALTY_PER_DAY = 50000;

    // DOM elements
    const elements = {
        userSelect: document.getElementById('users_id'),
        periodInput: document.getElementById('period'),
        baseSalaryInput: document.getElementById('base_salary'),
        overtimeInput: document.getElementById('overtime'),
        netSalaryInput: document.getElementById('net_salary'),
        absensiDisplay: document.getElementById('absensi_display'),
        cutiDisplay: document.getElementById('cuti_display'),
        telatDisplay: document.getElementById('telat_display'),
        totalAttendanceInput: document.getElementById('total_attendance'),
        allowanceContainer: document.getElementById('allowance-container'),
        deductionContainer: document.getElementById('deduction-container'),
        addAllowanceBtn: document.getElementById('addAllowance'),
        addDeductionBtn: document.getElementById('addDeduction')
    };

    // Calculate total allowances
    function calculateTotalAllowance(baseSalary) {
        let total = 0;

        document.querySelectorAll('.allowance-select').forEach(select => {
            if (select.value) {
                const option = select.selectedOptions[0];
                const type = option.getAttribute('data-type');

                if (type === 'percentage') {
                    const percent = parseFloat(option.getAttribute('data-percentage')) || 0;
                    total += (baseSalary * percent) / 100;
                } else if (type === 'fixed') {
                    const amount = parseFloat(option.getAttribute('data-amount')) || 0;
                    total += amount;
                }
            }
        });

        return total;
    }

    // Calculate total deductions (excluding late penalty)
    function calculateTotalDeduction(baseSalary) {
        let total = 0;

        document.querySelectorAll('.deduction-select').forEach(select => {
            if (select.value) {
                const option = select.selectedOptions[0];
                const type = option.getAttribute('data-type');

                if (type === 'percentage') {
                    const percent = parseFloat(option.getAttribute('data-percentage')) || 0;
                    total += (baseSalary * percent) / 100;
                } else if (type === 'fixed') {
                    const amount = parseFloat(option.getAttribute('data-amount')) || 0;
                    total += amount;
                }
            }
        });

        return total;
    }

    // Update net salary calculation
    function updateNetSalary() {
        const baseSalary = parseFloat(elements.baseSalaryInput.value) || 0;
        const overtime = parseFloat(elements.overtimeInput.value) || 0;

        const totalAllowance = calculateTotalAllowance(baseSalary);
        const totalDeduction = calculateTotalDeduction(baseSalary);
        const latePenalty = totalTelatDays * LATE_PENALTY_PER_DAY;

        // Net salary sudah termasuk pengurangan late penalty
        const netSalary = baseSalary + overtime + totalAllowance - totalDeduction - latePenalty;

        elements.netSalaryInput.value = Math.round(netSalary);

        // Debug log
        console.log('=== Perhitungan Gaji ===');
        console.log('Base Salary:', baseSalary);
        console.log('Overtime:', overtime);
        console.log('Total Allowance:', totalAllowance);
        console.log('Total Deduction (manual):', totalDeduction);
        console.log('Late Days:', totalTelatDays);
        console.log('Late Penalty (client-side only):', latePenalty);
        console.log('Net Salary (final):', netSalary);
        console.log('=====================');
    }

    // Fetch attendance data
    function fetchAttendanceData() {
        const userId = elements.userSelect.value;
        const period = elements.periodInput.value;

        if (!userId || !period) {
            resetAttendanceDisplay();
            return;
        }

        // Fetch attendance
        fetch(`/get-absensi?user_id=${userId}&period=${period}`)
            .then(response => response.json())
            .then(data => {
                elements.absensiDisplay.value = `${data.total_absensi} hari`;
                elements.totalAttendanceInput.value = data.total_absensi;
            })
            .catch(error => {
                console.error('Error fetching absensi:', error);
                elements.absensiDisplay.value = 'Gagal mengambil data';
                elements.totalAttendanceInput.value = 0;
            });

        // Fetch leave data
        fetch(`/get-cuti?user_id=${userId}&period=${period}`)
            .then(response => response.json())
            .then(data => {
                elements.cutiDisplay.value = `${data.total_cuti} hari`;
            })
            .catch(error => {
                console.error('Error fetching cuti:', error);
                elements.cutiDisplay.value = 'Gagal mengambil data';
            });

        // Fetch late data
        fetch(`/get-keterlambatan?user_id=${userId}&period=${period}`)
            .then(response => response.json())
            .then(data => {
                totalTelatDays = data.total_telat;
                elements.telatDisplay.value = `${data.total_telat} hari`;
                updateNetSalary(); // Recalculate after getting late data
            })
            .catch(error => {
                console.error('Error fetching keterlambatan:', error);
                elements.telatDisplay.value = 'Gagal mengambil data';
                totalTelatDays = 0;
                updateNetSalary();
            });
    }

    // Reset attendance display
    function resetAttendanceDisplay() {
        elements.absensiDisplay.value = '0 hari';
        elements.cutiDisplay.value = '0 hari';
        elements.telatDisplay.value = '0 hari';
        elements.totalAttendanceInput.value = 0;
        totalTelatDays = 0;
        updateNetSalary();
    }

    // Add new allowance row
    function addAllowanceRow() {
        const originalRow = document.querySelector('.allowance-row');
        const newRow = originalRow.cloneNode(true);

        const select = newRow.querySelector('select');
        select.value = '';

        const button = newRow.querySelector('button');
        button.className = 'btn btn-danger remove-allowance';
        button.textContent = '-';

        elements.allowanceContainer.appendChild(newRow);
    }

    // Add new deduction row
    function addDeductionRow() {
        const originalRow = document.querySelector('.deduction-row');
        const newRow = originalRow.cloneNode(true);

        const select = newRow.querySelector('select');
        select.value = '';

        const button = newRow.querySelector('button');
        button.className = 'btn btn-danger remove-deduction';
        button.textContent = '-';

        elements.deductionContainer.appendChild(newRow);
    }

    // Event listeners
    elements.userSelect.addEventListener('change', fetchAttendanceData);
    elements.periodInput.addEventListener('change', fetchAttendanceData);

    elements.baseSalaryInput.addEventListener('input', updateNetSalary);
    elements.overtimeInput.addEventListener('input', updateNetSalary);

    elements.allowanceContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('allowance-select')) {
            updateNetSalary();
        }
    });

    elements.deductionContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('deduction-select')) {
            updateNetSalary();
        }
    });

    elements.addAllowanceBtn.addEventListener('click', addAllowanceRow);
    elements.addDeductionBtn.addEventListener('click', addDeductionRow);

    // Remove allowance/deduction rows
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-allowance')) {
            e.target.closest('.allowance-row').remove();
            updateNetSalary();
        }
        if (e.target.classList.contains('remove-deduction')) {
            e.target.closest('.deduction-row').remove();
            updateNetSalary();
        }
    });

    // Initialize
    setTimeout(updateNetSalary, 100);
});
</script>

@endsection
