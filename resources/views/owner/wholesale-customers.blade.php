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

    <ul class="nav nav-tabs mb-3" id="wsTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="customers-tab" data-toggle="tab" href="#customers" role="tab">
                <i class="fas fa-users mr-1"></i> Pelanggan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="requests-tab" data-toggle="tab" href="#requests" role="tab">
                <i class="fas fa-key mr-1"></i> Permintaan Password
                <span class="badge badge-danger badge-pill" id="reqBadge" style="display:none">0</span>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        {{-- TAB 1: Customers --}}
        <div class="tab-pane fade show active" id="customers" role="tabpanel">
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
                                        <span class="text-muted pw-masked" style="font-family:monospace" data-id="{{ $c->id }}">••••••••</span>
                                        <i class="fas fa-eye toggle-pw-icon" data-id="{{ $c->id }}" style="cursor:pointer;margin-left:6px;color:#adb5bd" title="Lihat password"></i>
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
                                    <td><small>{{ $c->last_order?->format('d/m/Y') ?? '-' }}</small></td>
                                    <td>
                                        <span class="badge badge-info" style="cursor:default" title="Nama {{ $c->name }} — hubungi via admin untuk networking">
                                            <i class="fas fa-handshake mr-1"></i> Ingin terhubung?
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" onclick="viewOrders({{ $c->id }}, @js($c->name))" title="Lihat pesanan">
                                            <i class="fas fa-receipt"></i>
                                        </button>
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

        {{-- TAB 2: Password Requests --}}
        <div class="tab-pane fade" id="requests" role="tabpanel">
            <div class="card card-apms border-0 shadow-sm">
                <div class="card-header border-0 bg-transparent">
                    <h5 class="card-title mb-0"><i class="fas fa-key mr-2"></i> Permintaan Reset Password</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="reqTable">
                            <thead>
                                <tr>
                                    <th>Pelanggan</th>
                                    <th>Email</th>
                                    <th>Diminta</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="reqBody">
                                <tr><td colspan="4" class="text-center text-muted py-4">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card card-apms border-0 shadow-sm mt-3">
                <div class="card-header border-0 bg-transparent">
                    <h5 class="card-title mb-0"><i class="fas fa-history mr-2"></i> Riwayat Terselesaikan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Pelanggan</th>
                                    <th>Email</th>
                                    <th>Diselesaikan Oleh</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody id="resolvedBody">
                                <tr><td colspan="4" class="text-center text-muted py-4">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
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
                    <button type="submit" class="btn btn-primary-apms" id="btnSave">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Orders Modal -->
<div class="modal fade" id="ordersModal" tabindex="-1" role="dialog" aria-labelledby="ordersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ordersModalLabel"><i class="fas fa-receipt mr-2"></i> Pesanan <span id="ordersCustomerName"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Tanggal</th>
                                <th>Item</th>
                                <th>Penerima</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="ordersBody">
                            <tr><td colspan="7" class="text-center text-muted py-4">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
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

$(document).on('click', '.toggle-pw-icon', function() {
    const id = $(this).data('id');
    Swal.fire({
        title: 'Lihat Password?',
        text: 'Password akan di-reset dan ditampilkan. Simpan password baru ini.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Ya, Reset & Tampilkan!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/owner/wholesale-customers/' + id + '/reset-password',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    const newPw = res.password || '(lihat log)';
                    Swal.fire({icon:'success', title:'Password Baru', html:'Password: <code style="font-size:1.2em">' + newPw + '</code><br><small class="text-muted">Simpan password ini sekarang. Hanya ditampilkan sekali.</small>', timer:60000, showConfirmButton:true, confirmButtonText:'Saya sudah menyimpannya'});
                },
                error: function() {
                    Swal.fire({icon:'error', title:'Gagal!', text:'Gagal mereset password.'});
                }
            });
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

function viewOrders(id, name) {
    $('#ordersCustomerName').text(name);
    $('#ordersBody').html('<tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...</td></tr>');
    $('#ordersModal').modal('show');

    $.ajax({
        url: '/owner/wholesale-customers/' + id + '/orders',
        method: 'GET',
        success: function(res) {
            if (!res.orders || res.orders.length === 0) {
                $('#ordersBody').html('<tr><td colspan="7" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>');
                return;
            }
            let html = '';
            res.orders.forEach(function(o) {
                const statusBadge = {
                    'pending': 'badge-warning', 'reviewed': 'badge-info',
                    'on_progress': 'badge-primary', 'packed': 'badge-secondary',
                    'shipped': 'badge-info', 'delivered': 'badge-success',
                    'completed': 'badge-success', 'cancelled': 'badge-danger',
                }[o.status] || 'badge-secondary';

                const statusLabel = {
                    'pending': 'Baru', 'reviewed': 'Dikonfirmasi',
                    'on_progress': 'Diproses', 'packed': 'Dikemas',
                    'shipped': 'Dikirim', 'delivered': 'Diterima',
                    'completed': 'Selesai', 'cancelled': 'Dibatalkan',
                }[o.status] || o.status;

                const isDeleted = o.deleted_at ? ' <span class="badge badge-danger">Dihapus</span>' : '';

                html += '<tr>' +
                    '<td><code>' + o.invoice_number + '</code>' + isDeleted + '</td>' +
                    '<td><span class="badge ' + statusBadge + '">' + statusLabel + '</span></td>' +
                    '<td>Rp ' + new Intl.NumberFormat('id-ID').format(o.total_amount) + '</td>' +
                    '<td><small>' + o.created_at + '</small></td>' +
                    '<td>' + o.items_count + '</td>' +
                    '<td><small>' + o.recipient_name + '</small></td>' +
                    '<td><a href="/wholesale/' + o.id + '" class="btn btn-sm btn-outline-info" target="_blank" title="Lihat detail"><i class="fas fa-external-link-alt"></i></a></td>' +
                    '</tr>';
            });
            $('#ordersBody').html(html);
        },
        error: function() {
            $('#ordersBody').html('<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat data pesanan.</td></tr>');
        }
    });
}

function loadPasswordRequests() {
    $('#reqBody').html('<tr><td colspan="4" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...</td></tr>');
    $('#resolvedBody').html('<tr><td colspan="4" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...</td></tr>');

    $.ajax({
        url: '/owner/wholesale-password-requests',
        method: 'GET',
        success: function(res) {
            if (res.pending && res.pending.length > 0) {
                let html = '';
                res.pending.forEach(function(r) {
                    html += '<tr>' +
                        '<td class="font-weight-bold">' + r.name + '</td>' +
                        '<td><code>' + r.email + '</code></td>' +
                        '<td><small>' + r.created_at + '</small></td>' +
                        '<td>' +
                            '<button class="btn btn-sm btn-success" onclick="resolveRequest(' + r.id + ', \'' + r.name + '\', \'' + r.email + '\')">' +
                                '<i class="fas fa-check mr-1"></i> Setujui & Tampilkan Password' +
                            '</button>' +
                        '</td>' +
                        '</tr>';
                });
                $('#reqBody').html(html);
                $('#reqBadge').text(res.pending.length).show();
            } else {
                $('#reqBody').html('<tr><td colspan="4" class="text-center text-muted py-4">Tidak ada permintaan pending.</td></tr>');
                $('#reqBadge').hide();
            }

            if (res.resolved && res.resolved.length > 0) {
                let html = '';
                res.resolved.forEach(function(r) {
                    html += '<tr>' +
                        '<td>' + r.name + '</td>' +
                        '<td><code>' + r.email + '</code></td>' +
                        '<td>' + r.resolved_by + '</td>' +
                        '<td><small>' + r.resolved_at + '</small></td>' +
                        '</tr>';
                });
                $('#resolvedBody').html(html);
            } else {
                $('#resolvedBody').html('<tr><td colspan="4" class="text-center text-muted py-4">Belum ada riwayat.</td></tr>');
            }
        },
        error: function() {
            $('#reqBody').html('<tr><td colspan="4" class="text-center text-danger py-4">Gagal memuat data.</td></tr>');
            $('#resolvedBody').html('<tr><td colspan="4" class="text-center text-danger py-4">Gagal memuat data.</td></tr>');
        }
    });
}

function resolveRequest(id, name, email) {
    Swal.fire({
        title: 'Setujui Permintaan?',
        html: 'Reset password <strong>' + email + '</strong>?<br><small class="text-muted">Password baru akan ditampilkan dan bisa Anda kirimkan ke pelanggan.</small>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Setujui!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/owner/wholesale-password-requests/' + id + '/resolve',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    const newPw = res.password || '(lihat log)';
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        html: 'Password baru untuk <strong>' + res.name + '</strong>:<br><code style="font-size:1.3em">' + newPw + '</code><br><br>' +
                            '<small class="text-muted">Kirimkan password ini ke ' + res.email + ' via WhatsApp atau Email.<br>Hanya ditampilkan sekali.</small>',
                        timer: 120000,
                        showConfirmButton: true,
                        confirmButtonText: 'Saya sudah mengirimkan',
                    });
                    loadPasswordRequests();
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Gagal memproses.';
                    Swal.fire({icon:'error', title:'Gagal!', text:msg});
                }
            });
        }
    });
}

// Load password requests when the requests tab is shown
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    if ($(e.target).attr('href') === '#requests') {
        loadPasswordRequests();
    }
});
</script>
@endpush