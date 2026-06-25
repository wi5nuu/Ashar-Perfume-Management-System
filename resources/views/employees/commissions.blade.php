@extends('layouts.app')
@section('title', 'Komisi Karyawan')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-percentage"></i> Komisi Karyawan</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Karyawan</a></li>
                    <li class="breadcrumb-item active">Komisi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>Rp {{ number_format($totalCommission, 0, ',', '.') }}</h3>
                    <p>Total Komisi ({{ $month }})</p>
                </div>
                <div class="icon"><i class="fas fa-percentage"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Rp {{ number_format($paidCommission, 0, ',', '.') }}</h3>
                    <p>Sudah Dibayar</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>Rp {{ number_format($pendingCommission, 0, ',', '.') }}</h3>
                    <p>Belum Dibayar</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Controls --}}
        <div class="col-lg-4">
            {{-- Filter --}}
            <div class="card card-apms mb-4">
                <div class="card-header"><h3 class="card-title">Filter</h3></div>
                <div class="card-body">
                    <form method="GET">
                        <div class="form-group">
                            <label>Bulan</label>
                            <input type="month" name="month" class="form-control" value="{{ $month }}">
                        </div>
                        <div class="form-group">
                            <label>Karyawan</label>
                            <select name="user_id" class="form-control">
                                <option value="">Semua</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ $userFilter == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary-apms btn-block" onclick="disableBtn(this, 'Memfilter...')">Filter</button>
                    </form>
                </div>
            </div>

            {{-- Calculate --}}
            <div class="card card-apms mb-4">
                <div class="card-header"><h3 class="card-title">Hitung Komisi</h3></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('commissions.calculate') }}">
                        @csrf
                        <div class="form-group">
                            <label>Bulan</label>
                            <input type="month" name="month" class="form-control" value="{{ $month }}" required>
                        </div>
                        <div class="form-group">
                            <label>Rate Komisi (%)</label>
                            <input type="number" name="commission_rate" class="form-control" step="0.1" min="0" max="100" value="2" required>
                        </div>
                        <button type="submit" class="btn btn-primary-apms btn-block" onclick="return confirmHitung(this)">
                            <i class="fas fa-calculator mr-1"></i> Hitung Komisi
                        </button>
                    </form>
                </div>
            </div>

            {{-- Mark Paid --}}
            <div class="card card-apms mb-4">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-check-circle mr-1"></i> Tandai Dibayar</h3></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('commissions.mark-paid') }}">
                        @csrf
                        <div class="form-group">
                            <label>Karyawan</label>
                            <select name="user_id" class="form-control" required>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="month" value="{{ $month }}">
                        <button type="submit" class="btn btn-primary-apms btn-block" onclick="return confirmBayar(this)">
                            <i class="fas fa-check mr-1"></i> Tandai Dibayar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Per-user Summary & Detail Table --}}
        <div class="col-lg-8">
            {{-- Per-user summary --}}
            <div class="card card-apms mb-4">
                <div class="card-header"><h3 class="card-title">Ringkasan per Karyawan</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="thead-light">
                            <tr><th>Karyawan</th><th class="text-right">Total Komisi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($perUser as $pu)
                            <tr>
                                <td>{{ $pu->user->name ?? '-' }}</td>
                                <td class="text-right font-weight-bold">Rp {{ number_format($pu->total, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center text-muted">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Detail --}}
            <div class="card card-apms">
                <div class="card-header"><h3 class="card-title">Detail Komisi</h3></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Karyawan</th>
                                    <th>Invoice</th>
                                    <th class="text-right">Total Trx</th>
                                    <th class="text-center">Rate</th>
                                    <th class="text-right">Komisi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($commissions as $c)
                                <tr>
                                    <td>{{ $c->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $c->user->name ?? '-' }}</td>
                                    <td>{{ $c->transaction->invoice_number ?? '-' }}</td>
                                    <td class="text-right">Rp {{ number_format($c->transaction->total_amount ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $c->commission_rate }}%</td>
                                    <td class="text-right font-weight-bold">Rp {{ number_format($c->commission_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($c->status === 'paid')
                                            <span class="badge badge-success">Dibayar</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted">Belum ada data komisi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-2">{{ $commissions->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function disableBtn(btn, loadingText) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> ' + loadingText;
}
function confirmHitung(btn) {
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Hitung komisi untuk semua transaksi bulan ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#FF6B35',
        confirmButtonText: 'Ya, Hitung',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            disableBtn(btn, 'Menghitung...');
            btn.form.submit();
        }
    });
    return false;
}
function confirmBayar(btn) {
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Tandai semua komisi karyawan ini sebagai dibayar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#FF6B35',
        confirmButtonText: 'Ya, Tandai',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            disableBtn(btn, 'Memproses...');
            btn.form.submit();
        }
    });
    return false;
}
</script>
@endpush
