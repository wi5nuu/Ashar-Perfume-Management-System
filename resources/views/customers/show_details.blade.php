<div class="row">
    <div class="col-md-12">
        <table class="table table-sm table-bordered">
            <tr>
                <th width="30%" class="bg-light">Kode</th>
                <td><strong>{{ $customer->customer_code }}</strong></td>
            </tr>
            <tr>
                <th class="bg-light">NIK</th>
                <td>{{ $customer->nik ?? '-' }}</td>
            </tr>
            <tr>
                <th class="bg-light">Nama</th>
                <td>{{ $customer->name }}</td>
            </tr>
            <tr>
                <th class="bg-light">Jenis Kelamin</th>
                <td>{{ $customer->gender == 'male' ? 'Laki-laki' : ($customer->gender == 'female' ? 'Perempuan' : ($customer->gender == 'other' ? 'Lainnya' : '-')) }}</td>
            </tr>
            <tr>
                <th class="bg-light">Tgl Lahir</th>
                <td>{{ $customer->birth_date ? \Carbon\Carbon::parse($customer->birth_date)->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <th class="bg-light">Telepon</th>
                <td>{{ $customer->phone ?? '-' }}</td>
            </tr>
            <tr>
                <th class="bg-light">Email</th>
                <td>{{ $customer->email ?? '-' }}</td>
            </tr>
            <tr>
                <th class="bg-light">Tipe</th>
                <td>
                    @if($customer->type == 'wholesale')
                        <span class="badge badge-info">Wholesale</span>
                    @elseif($customer->type == 'vip')
                        <span class="badge badge-success">VIP</span>
                    @else
                        <span class="badge badge-secondary">Retail</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="bg-light">Alamat</th>
                <td>{{ $customer->address ?? '-' }}</td>
            </tr>
            <tr>
                <th class="bg-light">Status</th>
                <td>
                    @if($customer->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-danger">Nonaktif</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="small-box bg-info p-2 text-center">
            <h5 class="mb-0">{{ $customer->transactions->count() }}</h5>
            <p class="mb-0 text-xs">Total Transaksi</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="small-box bg-success p-2 text-center">
            <h5 class="mb-0">Rp {{ number_format($customer->transactions->sum('total_amount'), 0, ',', '.') }}</h5>
            <p class="mb-0 text-xs">Total Belanja</p>
        </div>
    </div>
</div>
