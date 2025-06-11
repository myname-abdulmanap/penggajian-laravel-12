@extends('layouts.app')

@section('content')
    <div class="col-lg-10 mb-10 mx-auto">
        <!-- Project Card Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
               
                <h4 class="m-15 font-weight-bold">PENGATURAN</h4>
            </div>
            <div class="card-body">
                <!-- Menampilkan pesan kesuksesan -->
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-error" role="alert">
                        {{ session('error') }}
                    </div>
                @endif



                <form method="POST" action="{{ route('attendance.updateSettings') }}">
                    @csrf
                    <div class="form-group">
                        <label>Jam Masuk</label>
                        <input type="time" name="jam_masuk" value="{{ substr($setting->jam_masuk, 0, 5) }}" required>

                    </div>
                    <div class="form-group">
                        <label>Jam Pulang</label>

                        <input type="time" name="jam_pulang" value="{{ substr($setting->jam_pulang, 0, 5) }}" required>
                    </div>
                    <button type="submit" class="btn-primary">Simpan</button>
                </form>

            </div>
        </div>
    </div>
@endsection
