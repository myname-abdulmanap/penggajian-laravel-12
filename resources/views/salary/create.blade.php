@extends('layouts.app')

@section('content')
<div class="col-lg-10 mb-10 mx-auto">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold">TAMBAH GAJI</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('salaries.store') }}">
                @csrf
                <div class="form-group">
                    <label>Pegawai</label>
                    <select name="user_id" class="form-control">
                        @foreach($users as $user)
                        <option value="{{ $user->users_id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Bulan</label>
                        <input type="number" name="month" class="form-control" placeholder="MM">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Tahun</label>
                        <input type="number" name="year" class="form-control" placeholder="YYYY">
                    </div>
                </div>
                <div class="form-group">
                    <label>Gaji Pokok</label>
                    <input type="number" name="base_salary" class="form-control">
                </div>
                <div class="form-group">
                    <label>Tunjangan</label>
                    <input type="number" name="allowance" class="form-control">
                </div>
                <button class="btn btn-primary">Hitung & Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
