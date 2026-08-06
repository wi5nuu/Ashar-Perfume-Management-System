@extends('layouts.app')
@section('title', 'Persetujuan Biaya')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-file-invoice-dollar mr-2"></i>Persetujuan Biaya Operasional</h1>
                    <p class="mb-0 mt-1" style="color:rgba(255,255,255,.65);font-size:.82rem;">
                        Review dan setujui pengajuan biaya dari tim
                    </p>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Persetujuan Biaya</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <x-alert />

    {{-- Table --}}
    <div class="card card-apms">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Deskripsi Biaya</th>
                            <th>Diajukan Oleh</th>
                            <th class="d-none d-md-table-cell">Jumlah</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvals as $a)
                        @php
                            $colors = ['#FF6B35','#10b981','#3b82f6','#8b5cf6','#f59e0b'];
                            $ci = abs(crc32($a->requester->name ?? 'X')) % count($colors);
                            $st = strtolower($a->status ?? 'pending');
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:.88rem">{{ $a->expense->description ?? '-' }}</div>
                                <div style="font-size:.75rem;color:#8a8fa8">{{ $a->expense->category ?? '' }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="background:{{ $colors[$ci] }}">
                                        {{ strtoupper(substr($a->requester->name ?? 'X', 0, 2)) }}
                                    </div>
                                    <span style="font-size:.86rem;font-weight:500">{{ $a->requester->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell" style="font-weight:600;color:var(--secondary)">
                                Rp {{ number_format($a->expense->amount ?? 0, 0, ',', '.') }}
                            </td>
                            <td style="font-size:.84rem;color:#6b7280">
                                {{ $a->created_at->format('d M Y') }}
                            </td>
                            <td>
                                @if($st === 'approved')
                                    <span class="badge-modern badge-approved"><i class="fas fa-check-circle"></i> Disetujui</span>
                                @elseif($st === 'rejected')
                                    <span class="badge-modern badge-rejected"><i class="fas fa-times-circle"></i> Ditolak</span>
                                @else
                                    <span class="badge-modern badge-pending"><i class="fas fa-clock"></i> Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($st === 'pending')
                                <div class="d-flex gap-1">
                                    <form method="POST" action="{{ route('expense-approvals.approve', $a->id) }}" class="d-inline" id="approve-form-{{ $a->id }}">
                                        @csrf
                                        <button type="button" class="btn btn-sm btn-success px-2 py-1"
                                                onclick="confirmApprove({{ $a->id }})" title="Setujui">
                                            <i class="fas fa-check" style="font-size:.75rem"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1"
                                            onclick="confirmReject({{ $a->id }})" title="Tolak">
                                        <i class="fas fa-times" style="font-size:.75rem"></i>
                                    </button>
                                </div>
                                @else
                                <span class="text-muted" style="font-size:.8rem">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="text-center py-5">
                                    <i class="fas fa-check-double" style="font-size:2.5rem;color:#10b981;opacity:.5"></i>
                                    <p class="mt-3 mb-0 font-weight-600" style="color:#3d4268">Semua bersih!</p>
                                    <small class="text-muted">Tidak ada pengajuan biaya yang menunggu persetujuan</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($approvals->hasPages())
            <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-top:1px solid #f2f3f8">
                <small class="text-muted">{{ $approvals->firstItem() }}–{{ $approvals->lastItem() }} dari {{ $approvals->total() }}</small>
                {{ $approvals->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script>
function confirmApprove(id) {
    Swal.fire({
        title: 'Setujui Pengajuan?',
        text: 'Biaya ini akan disetujui dan diproses.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1a7a45',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Setujui',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('approve-form-' + id).submit();
        }
    });
}

function confirmReject(id) {
    Swal.fire({
        title: 'Tolak Pengajuan',
        html: '<label style="font-size:.88rem;font-weight:600;color:#5a5f7d;text-align:left;display:block;margin-bottom:.4rem">Alasan Penolakan</label>' +
              '<textarea id="rejectNotes" class="swal2-textarea" placeholder="Tuliskan alasan penolakan..." rows="3"></textarea>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E55A2B',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-times mr-1"></i> Tolak',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        preConfirm: function() {
            var notes = document.getElementById('rejectNotes').value;
            if (!notes) {
                Swal.showValidationMessage('Alasan penolakan wajib diisi');
                return false;
            }
            return notes;
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '/expense-approvals/' + id + '/reject';
            var csrfInput = document.createElement('input');
            csrfInput.type  = 'hidden';
            csrfInput.name  = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var notesInput = document.createElement('input');
            notesInput.type  = 'hidden';
            notesInput.name  = 'notes';
            notesInput.value = result.value;
            form.appendChild(csrfInput);
            form.appendChild(notesInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
