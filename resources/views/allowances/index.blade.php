@extends('layouts.app')

@section('content')
<div class="col-lg-10 mb-10 mx-auto">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="m-0 font-weight-bold">DATA TUNJANGAN</h4>
                <button class="btn btn-success" onclick="openCreateModal()">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr align="center">
                        <th>#</th>
                        <th>Nama Tunjangan</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allowances as $index => $item)
                    <tr align="center">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ ucfirst($item->type) }}</td>
                        <td>
                            @if($item->type === 'fixed')
                                Rp{{ number_format($item->amount, 0, ',', '.') }}
                            @elseif($item->type === 'percentage')
                                {{ number_format($item->percentage, 2) }}%
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->description }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick='openEditModal(@json($item))'>
                                <i class="fas fa-edit"></i> Edit
                            </button>

                            <form method="POST" action="{{ route('allowances.destroy', $item->allowance_id) }}" style="display:inline-block;">
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

<!-- Modal Create/Edit -->
<div class="modal fade" id="allowanceModal" tabindex="-1" role="dialog" aria-labelledby="allowanceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="allowanceForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allowanceModalLabel">Tambah Tunjangan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name">Nama Tunjangan</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="type">Tipe Tunjangan</label>
                        <select class="form-control" id="type" name="type" required onchange="toggleValueInput()">
                            <option value="">-- Pilih Tipe --</option>
                            <option value="fixed">Fixed</option>
                            <option value="percentage">Percentage</option>
                        </select>
                    </div>

                    <div class="mb-3" id="fixedAmountGroup" style="display:none;">
                        <label for="amount">Jumlah (Rp)</label>
                        <input type="number" class="form-control" id="amount" name="amount" min="0" step="1000">
                    </div>

                    <div class="mb-3" id="percentageGroup" style="display:none;">
                        <label for="percentage">Persentase (%)</label>
                        <input type="number" class="form-control" id="percentage" name="percentage" min="0" max="100" step="0.01">
                    </div>

                    <div class="mb-3">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleValueInput() {
        const type = $('#type').val();
        if(type === 'fixed') {
            $('#fixedAmountGroup').show();
            $('#amount').attr('required', true);
            $('#percentageGroup').hide();
            $('#percentage').removeAttr('required').val('');
        } else if(type === 'percentage') {
            $('#percentageGroup').show();
            $('#percentage').attr('required', true);
            $('#fixedAmountGroup').hide();
            $('#amount').removeAttr('required').val('');
        } else {
            $('#fixedAmountGroup').hide();
            $('#percentageGroup').hide();
            $('#amount').removeAttr('required').val('');
            $('#percentage').removeAttr('required').val('');
        }
    }

    function openCreateModal() {
        $('#allowanceModalLabel').text('Tambah Tunjangan');
        $('#allowanceForm').attr('action', "{{ route('allowances.store') }}");
        $('#formMethod').val('POST');

        $('#name').val('');
        $('#type').val('');
        $('#amount').val('');
        $('#percentage').val('');
        $('#description').val('');

        toggleValueInput();

        $('#allowanceModal').modal('show');
    }

    function openEditModal(data) {
        $('#allowanceModalLabel').text('Edit Tunjangan');
        $('#allowanceForm').attr('action', "/allowances/" + data.allowance_id);
        $('#formMethod').val('PUT');

        $('#name').val(data.name);
        $('#type').val(data.type);
        $('#description').val(data.description ?? '');

        if(data.type === 'fixed') {
            $('#amount').val(data.amount);
            $('#percentage').val('');
        } else if(data.type === 'percentage') {
            $('#percentage').val(data.percentage);
            $('#amount').val('');
        } else {
            $('#amount').val('');
            $('#percentage').val('');
        }

        toggleValueInput();

        $('#allowanceModal').modal('show');
    }
</script>
@endsection
