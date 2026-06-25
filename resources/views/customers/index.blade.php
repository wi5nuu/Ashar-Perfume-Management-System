@extends('layouts.app')

@section('title', 'Manajemen Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">Daftar Pelanggan</h3>
                    <div class="ml-auto">
                        <a href="{{ route('customers.create') }}" class="btn btn-primary-apms">
                            <i class="fas fa-user-plus"></i> Pelanggan Baru
                        </a>
                        <button class="btn btn-success ml-2" onclick="exportCustomers()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <input type="text" id="searchCustomers" class="form-control" 
                                   placeholder="Cari pelanggan...">
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <select id="customerTypeFilter" class="form-control">
                                <option value="">Semua Tipe</option>
                                <option value="retail">Retail</option>
                                <option value="wholesale">Wholesale</option>
                                <option value="vip">VIP</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <select id="customerStatusFilter" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <button class="btn btn-secondary btn-block" onclick="resetFilters()">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pelanggan</th>
                                    <th>Kontak</th>
                                    <th>Tipe</th>
                                    <th>Total Transaksi</th>
                                    <th>Total Belanja</th>
                                    <th>Poin</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $customer)
                                @php
                                    $totalTransactions = $customer->transactions->count();
                                    $totalSpent = $customer->transactions->sum('total_amount');
                                    $points = floor($totalSpent / 10000); // 1 point per 10k
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge badge-light">{{ $customer->customer_code }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="img-circle img-size-32 bg-primary d-flex align-items-center justify-content-center mr-2">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $customer->name }}</strong><br>
                                                <small class="text-muted">Bergabung: {{ $customer->created_at->format('d/m/Y') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <small><i class="fas fa-phone text-muted mr-1"></i> {{ $customer->phone ?? '-' }}</small><br>
                                            <small><i class="fas fa-envelope text-muted mr-1"></i> {{ $customer->email ?? '-' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($customer->type == 'wholesale')
                                            <span class="badge badge-info">Wholesale</span>
                                        @elseif($customer->type == 'vip')
                                            <span class="badge badge-success">VIP</span>
                                        @else
                                            <span class="badge badge-secondary">Retail</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <div class="h5 mb-0">{{ $totalTransactions }}</div>
                                            <small class="text-muted">transaksi</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-success">
                                            <strong>Rp {{ number_format($totalSpent, 0, ',', '.') }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <div class="h5 mb-0">{{ $points }}</div>
                                            <small class="text-muted">poin</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($customer->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-primary" onclick="viewTransactions(@json($customer->id))">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <button class="btn btn-danger" onclick="deleteCustomer(@json($customer->id))">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $customers->links('pagination::bootstrap-4') }}
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="small">
                                Total Pelanggan: <strong>{{ $customers->total() }}</strong> |
                                Aktif: <strong>{{ $activeCustomers }}</strong> |
                                Wholesale: <strong>{{ $wholesaleCustomers }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="small">
                                Rata-rata Belanja: <strong>Rp {{ number_format($averageSpent, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Customer Modal -->
<div class="modal fade" id="newCustomerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pelanggan Baru</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="customerForm" action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Lengkap *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Lahir</label>
                                <input type="date" class="form-control" name="birth_date">
                            </div>
                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <select class="form-control" name="gender">
                                    <option value="">Pilih</option>
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nomor Telepon *</label>
                                <input type="text" class="form-control" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea class="form-control" name="address" rows="3"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tipe Pelanggan</label>
                                        <select class="form-control" name="type">
                                            <option value="retail">Retail</option>
                                            <option value="wholesale">Wholesale</option>
                                            <option value="vip">VIP</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name="is_active">
                                            <option value="1">Aktif</option>
                                            <option value="0">Nonaktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Catatan</label>
                                <textarea class="form-control" name="notes" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-apms">Simpan Pelanggan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Customer Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pelanggan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="customerDetailContent">
                <!-- Customer details will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewCustomer(id) {
    $.ajax({
        url: `/api/customers/${id}`,
        method: 'GET',
        success: function(response) {
            $('#customerDetailContent').html(response.html);
            $('#viewCustomerModal').modal('show');
        }
    });
}

function editCustomer(id) {
    window.location.href = `/customers/${id}/edit`;
}

function viewTransactions(id) {
    window.location.href = `/transactions?customer_id=${id}`;
}

function deleteCustomer(id) {
    Swal.fire({
        title: 'Hapus Pelanggan?',
        text: "Data pelanggan akan dihapus permanen",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/customers/${id}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    Swal.fire('Terhapus!', 'Pelanggan berhasil dihapus.', 'success')
                        .then(() => location.reload());
                }
            });
        }
    });
}

function exportCustomers() {
    Swal.fire({title:'Mengexport...', text:'Memproses export pelanggan', allowOutsideClick:false, didOpen:()=>Swal.showLoading()});
    window.open('{{ route('customers.export') }}', '_blank');
    setTimeout(() => Swal.close(), 3000);
}

function resetFilters() {
    $('#searchCustomers').val('');
    $('#customerTypeFilter').val('');
    $('#customerStatusFilter').val('');
    
    // Reset table display
    $('tbody tr').show();
}

$(function() {
    // Search filter
    $('#searchCustomers').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    
    // Type filter
    $('#customerTypeFilter').change(function() {
        const value = $(this).val();
        if (!value) {
            $('tbody tr').show();
            return;
        }
        
        $('tbody tr').each(function() {
            const type = $(this).find('td:eq(3) .badge').text().toLowerCase();
            $(this).toggle(type.indexOf(value) > -1);
        });
    });
    
    // Status filter
    $('#customerStatusFilter').change(function() {
        const value = $(this).val();
        if (!value) {
            $('tbody tr').show();
            return;
        }
        
        $('tbody tr').each(function() {
            const status = $(this).find('td:eq(7) .badge').hasClass('badge-success') ? 'active' : 'inactive';
            $(this).toggle(status === value);
        });
    });
    
    // Customer form submission
    $('#customerForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Berhasil', 'Pelanggan berhasil ditambahkan', 'success')
                    .then(() => {
                        $('#newCustomerModal').modal('hide');
                        $('#customerForm')[0].reset();
                        location.reload();
                    });
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
            }
        });
    });
});
</script>
@endpush