@extends('layouts.app')

@section('title', 'Pelanggan Grosir')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="font-weight-bold"><i class="fas fa-users mr-2"></i> Pelanggan Grosir</h4>
            <p class="text-muted">Daftar seluruh pelanggan grosir AL'ASHAR PARFUM — Owner dapat mengubah email & password.</p>
        </div>
    </div>

    <div class="card card-apms border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Nama Pelanggan</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Bergabung</th>
                            <th>Total Pesanan</th>
                            <th>Total Belanja</th>
                            <th>Tier</th>
                            <th>Pesanan Terakhir</th>
                            <th>Networking</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $c)
                        <tr>
                            <td class="font-weight-bold">{{ $c->name }}</td>
                            <td><code class="email-display-{{ $c->id }}">{{ $c->email }}</code></td>
                            <td>
                                <span class="text-muted" style="font-family:monospace">••••••••</span>
                            </td>
                            <td><small>{{ $c->created_at->format('d/m/Y') }}</small></td>
                            <td>{{ $c->order_count }}</td>
                            <td>Rp {{ number_format($c->total_spent, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $tc = ['Regular'=>'#999','VIP'=>'#FF6B35','Silver'=>'#6B7280','Gold'=>'#F59E0B','Platinum'=>'#8B5CF6'];
                                    $tic = ['Regular'=>'fa-user','VIP'=>'fa-certificate','Silver'=>'fa-gem','Gold'=>'fa-star','Platinum'=>'fa-crown'];
                                @endphp
                                <span style="color:{{ $tc[$c->tier_label] ?? '#999' }}">
                                    <i class="fas {{ $tic[$c->tier_label] ?? 'fa-user' }} mr-1"></i> {{ $c->tier_label }}
                                </span>
                            </td>
                            <td><small>{{ $c->last_order ? $c->last_order->format('d/m/Y') : '-' }}</small></td>
                            <td>
                                <span class="badge badge-info" style="cursor:default" title="Nama {{ $c->name }} — hubungi via admin untuk networking">
                                    <i class="fas fa-handshake mr-1"></i> Ingin terhubung?
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editAccount({{ $c->id }}, @js($c->name), @js($c->email))" title="Edit akun">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="resetPassword({{ $c->id }}, @js($c->email))" title="Reset password">
                                    <i class="fas fa-key"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">Belum ada pelanggan grosir.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Akun Pelanggan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editForm">
                <input type="hidden" id="editId">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Pelanggan</label>
                        <input type="text" id="editName" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password Baru <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                        <input type="password" id="editPassword" class="form-control" placeholder="Kosongkan jika tidak diubah">
                        <small class="text-muted">Min. 6 karakter</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSave">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editAccount(id, name, email) {
    $('#editId').val(id);
    $('#editName').val(name);
    $('#editEmail').val(email);
    $('#editPassword').val('');
    $('#editModal').modal('show');
}

$('#editForm').submit(function(e) {
    e.preventDefault();
    const id = $('#editId').val();
    const email = $('#editEmail').val();
    const password = $('#editPassword').val();

    if (!email) {
        Swal.fire({icon:'error', title:'Email wajib diisi'});
        return;
    }

    $('#btnSave').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

    $.ajax({
        url: '/owner/wholesale-customers/' + id + '/update',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            email: email,
            password: password,
        },
        success: function(res) {
            $('.email-display-' + id).text(email);
            $('#editModal').modal('hide');
            Swal.fire({icon:'success', title:'Berhasil!', text:'Akun pelanggan diperbarui.', timer:1500, showConfirmButton:false});
        },
        error: function(xhr) {
            const msg = xhr.responseJSON?.message || 'Gagal menyimpan.';
            Swal.fire({icon:'error', title:'Gagal!', text:msg});
        },
        complete: function() {
            $('#btnSave').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan');
        }
    });
});

function resetPassword(id, email) {
    Swal.fire({
        title: 'Reset Password?',
        html: 'Reset password <strong>' + email + '</strong>?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Reset!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/owner/wholesale-customers/' + id + '/reset-password',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    const newPw = res.password || '(lihat log)';
                    Swal.fire({icon:'success', title:'Berhasil!', html:'Password baru: <code>' + newPw + '</code><br><small class="text-muted">Simpan password ini sekarang. Hanya ditampilkan sekali.</small>', timer:60000, showConfirmButton:true, confirmButtonText:'Saya sudah menyimpannya'});
                },
                error: function() {
                    Swal.fire({icon:'error', title:'Gagal!', text:'Gagal mereset password.'});
                }
            });
        }
    });
}
</script>
@endpush