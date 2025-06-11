
@extends('layouts.app')

@section('content')
<div class="col-lg-10 mb-10 mx-auto">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="m-0 font-weight-bold">DATA GAJI</h4>
                <a href="{{ route('salaries.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
            @endif
            <table class="table table-bordered">
                <thead>
                    <tr align="center">
                        <th>#</th>
                        <th>Nama</th>
                        <th>Periode</th>
                        <th>Gaji Bersih</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaries as $index => $salary)
                    <tr align="center">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $salary->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($salary->period)->format('F Y') }}</td>
                        <td>Rp{{ number_format($salary->net_salary) }}</td>
                        <td>
                            <form method="POST" action="{{ route('salaries.destroy', $salary->salary_id) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin hapus?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
