@extends('layouts.app')

@section('title', 'Detail Pengeluaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Pengeluaran</h3>
                    <div class="card-tools">
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                        <a href="{{ route('expenses.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:200px;">Tanggal</th>
                            <td>{{ $expense->date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>{{ $expense->category->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $expense->description }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Vendor</th>
                            <td>{{ $expense->vendor ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dicatat oleh</th>
                            <td>{{ $expense->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Cabang</th>
                            <td>{{ $expense->branch->name ?? '-' }}</td>
                        </tr>
                        @if($expense->proof_image)
                        <tr>
                            <th>Bukti</th>
                            <td>
                                <a href="{{ asset('storage/' . $expense->proof_image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $expense->proof_image) }}" class="img-thumbnail" style="max-height:200px;">
                                </a>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
