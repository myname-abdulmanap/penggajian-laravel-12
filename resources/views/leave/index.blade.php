@extends('layouts.app')

@section('content')
<div class="col-lg-10 mb-10 mx-auto">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold">Data Pengajuan Cuti</h4>
            @if(auth()->user()->role === 'karyawan')
            <button class="btn btn-success" onclick="openCreateModal()">
                <i class="fas fa-plus"></i> Ajukan Cuti
            </button>
            @endif
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered" id="dataTable">
                <thead>
                    <tr align="center">
                        <th>#</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Nama Karyawan</th>
                        @endif
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaves as $index => $leave)
                    <tr align="center">
                        <td>{{ $leave->leave_id }}</td>
                        @if(auth()->user()->role === 'admin')
                            <td>{{ $leave->user->name ?? '-' }}</td>
                        @endif
                        <td>{{ $leave->start_date }} s.d {{ $leave->end_date }}</td>
                        <td>{{ ucfirst($leave->leave_type) }}</td>
                        <td>
                            <span class="badge badge-{{
                                $leave->status === 'approved' ? 'success' :
                                ($leave->status === 'rejected' ? 'danger' : 'warning')
                            }}">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick='showDetailModal(@json($leave))'>
                                <i class="fas fa-eye"></i>
                            </button>

                            @if(auth()->user()->role === 'admin')
                                <button class="btn btn-sm btn-primary" onclick='openStatusModal(@json($leave))'>
                                    <i class="fas fa-edit"></i>
                                </button>
                            @endif

                            @if(auth()->user()->role === 'admin' || auth()->user()->users_id === $leave->users_id)
                                <form method="POST" action="{{ route('leaves.destroy', $leave->leave_id) }}" style="display:inline-block;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin hapus?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

<!-- Modal Ajukan Cuti -->
<div class="modal fade" id="createLeaveModal" tabindex="-1" role="dialog" aria-labelledby="createLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajukan Cuti</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <select name="leave_type" class="form-control" required>
                            <option value="">-- Pilih Jenis Cuti --</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Tahunan">Tahunan</option>
                            <option value="Melahirkan">Melahirkan</option>
                            <option value="Menikah">Menikah</option>
                          
                            <option value="Lainnya">Lainnya</option>
                        </select>

                    </div>
                    <div class="form-group">
                        <label>Alasan</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Lampiran (opsional)</label>
                        <input type="file" name="attachment" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Ajukan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah Status (Admin) -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="statusForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Status Cuti</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select name="status" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                        <option value="pending">Menunggu</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Cuti</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <p><strong>Nama Karyawan:</strong> {{ $leave->user->name ?? '-' }}</p>

                <p><strong>Tanggal Mulai:</strong> <span id="detail-start"></span></p>
                <p><strong>Tanggal Selesai:</strong> <span id="detail-end"></span></p>
                <p><strong>Jenis Cuti:</strong> <span id="detail-type"></span></p>
                <p><strong>Alasan:</strong> <span id="detail-reason"></span></p>
                <p><strong>Status:</strong> <span id="detail-status"></span></p>
                <p><strong>Lampiran:</strong> <span id="detail-attachment"></span></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Script -->
<script>
    function openCreateModal() {
        $('#createLeaveModal').modal('show');
    }

    function openStatusModal(data) {
        const form = $('#statusForm');
        form.attr('action', `/leaves/${data.leave_id}`);
        form.find('select[name="status"]').val(data.status);
        $('#statusModal').modal('show');
    }
</script>

<script>
    function showDetailModal(data) {
        $('#detail-start').text(data.start_date);
        $('#detail-end').text(data.end_date);
        $('#detail-type').text(data.leave_type);
        $('#detail-reason').text(data.reason);
        $('#detail-status').text(data.status);

        if (data.attachment) {
            const attachmentUrl = `/storage/${data.attachment}`;
            $('#detail-attachment').html(`<a href="${attachmentUrl}" target="_blank"><i class="fas fa-file"></i> Lihat Lampiran</a>`);
        } else {
            $('#detail-attachment').text('-');
        }

        $('#detailModal').modal('show');
    }
</script>

@endsection
