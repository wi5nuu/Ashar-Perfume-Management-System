@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Karyawan</h3>
                    <div class="card-tools">
                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                        <a href="{{ route('employees.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width:200px;">Nama</th>
                                <td>{{ $employee->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $employee->email }}</td>
                            </tr>
                            <tr>
                                <th>Telepon</th>
                                <td>{{ $employee->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Posisi</th>
                                <td><span class="badge badge-info">{{ ucfirst($employee->role) }}</span></td>
                            </tr>
                            <tr>
                                <th>Cabang</th>
                                <td>{{ $employee->branch->name ?? 'Pusat' }}</td>
                            </tr>
                            @if($employee->employee_id)
                            <tr>
                                <th>ID Karyawan</th>
                                <td>{{ $employee->employee_id }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
