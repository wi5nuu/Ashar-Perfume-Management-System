@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Persetujuan Biaya</h1>
  <div class="card shadow mb-4">
    <div class="card-body">
      <table class="table table-sm table-bordered">
        <thead><tr><th>Biaya</th><th>Diminta Oleh</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse($approvals as $a)
          <tr><td>{{ $a->expense->description ?? '-' }}</td><td>{{ $a->requester->name }}</td>
            <td>{{ $a->created_at->format('d/m/Y') }}</td>
            <td><span class="badge badge-warning">{{ ucfirst($a->status) }}</span></td>
            <td>
              <form method="POST" action="{{ route('expense-approvals.approve',$a->id) }}" class="d-inline">
                @csrf <button class="btn btn-sm btn-success" onclick="return confirm('Setujui?')"><i class="fas fa-check"></i></button>
              </form>
              <button class="btn btn-sm btn-danger" onclick="reject({{ $a->id }})"><i class="fas fa-times"></i></button>
            </td></tr>
          @empty <tr><td colspan="5" class="text-center">Tidak ada pengajuan pending</td></tr> @endforelse
        </tbody>
      </table>
      {{ $approvals->links() }}
    </div>
  </div>
</div>
<script>
function reject(id) {
  const notes = prompt('Alasan penolakan:');
  if (notes) {
    const form = document.createElement('form');
    form.method = 'POST'; form.action = `/expense-approvals/${id}/reject`;
    form.innerHTML = `@csrf<input name="notes" value="${notes}">`;
    document.body.appendChild(form); form.submit();
  }
}
</script>
@endsection
