@extends('layouts.app')

@section('content')
<div class="col-lg-10 mb-10 mx-auto">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="m-0 font-weight-bold">DATA GAJI</h4>
                @unless(auth()->user()->role === 'karyawan')
                <a href="{{ route('salaries.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah
                </a>
                @endunless
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
            @endif
            <table id="dataTable" class="table table-bordered">
                <thead>
                    <tr align="center">
                        <th>#</th>
                        <th>Nama Pegawai</th>
                        <th>Periode</th>

                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaries as $index => $salary)
                    <tr align="center">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $salary->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($salary->period)->translatedFormat('F Y') }}</td>
                      
                        <td>
                            <div class="btn-group" role="group">
                                <!-- Button Show Slip Detail -->
                                <a href="{{ route('salaries.show', $salary->salary_id) }}"
                                   class="btn btn-sm btn-info"
                                   title="Lihat Slip Gaji">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @unless(auth()->user()->role === 'karyawan')
                                <!-- Button Delete -->
                                <form method="POST" action="{{ route('salaries.destroy', $salary->salary_id) }}" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')"
                                            title="Hapus Data">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if($salaries->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data gaji</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Custom CSS untuk styling button group -->
<style>
.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

/* Responsive untuk mobile */
@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .btn-group .btn {
        margin-right: 0;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}

/* Hover effects */
.btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}
</style>
@endsection
