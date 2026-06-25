@extends('layouts.app')
@section('title', 'Payroll Karyawan')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="font-weight-bold text-dark"><i class="fas fa-file-invoice-dollar mr-2 text-primary"></i>Manajemen Payroll</h3>
        <form action="{{ route('payroll.generate') }}" method="POST">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <button class="btn btn-primary-apms shadow-sm"><i class="fas fa-sync mr-1"></i> Generate Payroll {{ $month }}</button>
        </form>
    </div>

    <div class="card card-apms border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="pl-4">Nama</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Potongan</th>
                        <th>Total Gaji</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $e)
                    @php $p = $e->payrolls->first(); @endphp
                    <tr>
                        <td class="pl-4 font-weight-bold">{{ $e->name }}</td>
                        <td>Rp {{ number_format($e->basic_salary, 0, ',', '.') }}</td>
                        <td class="text-info">Rp {{ number_format($e->payrollSettings->allowance ?? 0, 0, ',', '.') }}</td>
                        <td class="text-danger">Rp {{ number_format($e->payrollSettings->deduction ?? 0, 0, ',', '.') }}</td>
                        <td class="font-weight-bold text-success">Rp {{ number_format($p->total_salary ?? 0, 0, ',', '.') }}</td>
                        <td>
                            @if($p) <span class="badge badge-success-soft">Terhitung</span> @else <span class="badge badge-secondary-soft">Belum Digenerate</span> @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
